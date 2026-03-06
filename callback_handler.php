<?php
require 'config.php';

header('Content-Type: application/json');

$logFile = __DIR__ . '/mpesa_callback.log';
$log = function ($msg) use ($logFile) {
    file_put_contents($logFile, date('Y-m-d H:i:s') . ' | ' . $msg . PHP_EOL, FILE_APPEND | LOCK_EX);
};

$callbackData = file_get_contents('php://input');
$log('RAW: ' . $callbackData);

$data = json_decode($callbackData, true);

if (!isset($data['Body']['stkCallback'])) {
    $log('No stkCallback key present');
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'No stkCallback payload']);
    exit;
}

$stkData = $data['Body']['stkCallback'];
$checkoutID = $stkData['CheckoutRequestID'] ?? '';
$resultCode = $stkData['ResultCode'] ?? -1;
$resultDesc = $stkData['ResultDesc'] ?? '';

$log("CheckoutID: $checkoutID | ResultCode: $resultCode | ResultDesc: $resultDesc");

if (!$checkoutID) {
    $log('Missing CheckoutRequestID');
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Missing CheckoutRequestID']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM pending_payments WHERE checkout_request_id = ? AND status = 'pending'");
$stmt->execute([$checkoutID]);
$pending = $stmt->fetch();

if (!$pending) {
    $log('No matching pending_payments row');
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'No pending payment found']);
    exit;
}

if ($resultCode == 0) {
    $member_id = $pending['member_id'];
    $amount = $pending['amount'];
    $type = $pending['payment_type'];

    $receipt = 'N/A';
    if (isset($stkData['CallbackMetadata']['Item'])) {
        foreach ($stkData['CallbackMetadata']['Item'] as $item) {
            if ($item['Name'] == 'MpesaReceiptNumber') {
                $receipt = $item['Value'];
                break;
            }
        }
    }

    $pdo->beginTransaction();
    try {
        switch ($type) {
            case 'normal_savings':
                $stmt = $pdo->prepare("SELECT COALESCE(balance_after, 0) FROM normal_savings WHERE member_id = ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$member_id]);
                $new_bal = ($stmt->fetchColumn() ?: 0) + $amount;
                $stmt = $pdo->prepare("INSERT INTO normal_savings (member_id, amount, transaction_type, description, transaction_date, balance_after) VALUES (?, ?, 'deposit', 'M-PESA Savings', CURDATE(), ?)");
                $stmt->execute([$member_id, $amount, $new_bal]);
                break;

            case 'welfare':
                $stmt = $pdo->prepare("INSERT INTO welfare_contributions (member_id, amount, contribution_type, description, contribution_date) VALUES (?, ?, 'normal', 'M-PESA Welfare', CURDATE())");
                $stmt->execute([$member_id, $amount]);
                break;

            case 'table_banking':
                $stmt = $pdo->prepare("SELECT COALESCE(balance_after, 0) FROM table_banking_shares WHERE member_id = ? ORDER BY id DESC LIMIT 1");
                $stmt->execute([$member_id]);
                $new_bal = ($stmt->fetchColumn() ?: 0) + $amount;
                $stmt = $pdo->prepare("INSERT INTO table_banking_shares (member_id, amount, transaction_type, description, transaction_date, balance_after) VALUES (?, ?, 'share', 'M-PESA Share', CURDATE(), ?)");
                $stmt->execute([$member_id, $amount, $new_bal]);
                break;

            case 'weekly':
                $stmt = $pdo->prepare("INSERT INTO weekly_contributions (member_id, amount, week_number, year, contribution_date, status, paid_date) VALUES (?, ?, ?, ?, CURDATE(), 'paid', NOW()) ON DUPLICATE KEY UPDATE status='paid', paid_date=NOW()");
                $stmt->execute([$member_id, $amount, date('W'), date('Y')]);
                break;

            case 'loan_repayment':
                // 1. Record the repayment in the ledger
                $stmt = $pdo->prepare("INSERT INTO loan_repayments (member_id, amount, repayment_date, reference_number, notes) VALUES (?, ?, CURDATE(), ?, 'M-PESA Auto-Payment')");
                $stmt->execute([$member_id, $amount, $receipt]);

                // 2. Automated Balance Reduction Logic
                $stmt = $pdo->prepare("SELECT id, balance FROM normal_loans WHERE member_id = ? AND status IN ('active', 'approved') AND balance > 0 ORDER BY request_date ASC LIMIT 1");
                $stmt->execute([$member_id]);
                $loan = $stmt->fetch();

                if ($loan) {
                    $new_balance = max(0, $loan['balance'] - $amount);
                    $new_status = ($new_balance <= 0) ? 'paid' : 'active';

                    $stmt = $pdo->prepare("UPDATE normal_loans SET balance = ?, status = ? WHERE id = ?");
                    $stmt->execute([$new_balance, $new_status, $loan['id']]);
                }
                break;
        }

        // Finalize the M-PESA transaction log inside the transaction
        $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'completed', mpesa_receipt = ?, completed_at = NOW(), result_desc = ? WHERE id = ?");
        $stmt->execute([$receipt, $resultDesc, $pending['id']]);

        $pdo->commit();
        $log('Payment processed and committed successfully');
    } catch (Exception $e) {
        $pdo->rollBack();
        $log('Callback DB Error (DB ops rolled back): ' . $e->getMessage());

        // Mark payment as failed so the frontend stops polling indefinitely
        try {
            $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'failed', result_desc = ? WHERE id = ?");
            $stmt->execute(['Database error while applying payment', $pending['id']]);
        } catch (Exception $inner) {
            $log('Callback secondary update failed: ' . $inner->getMessage());
        }

        echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Callback processed with internal DB error']);
        exit;
    }

    // Run notifications AFTER the DB transaction so a mail error does not roll back money
    try {
        require_once __DIR__ . '/EmailService.php';
        require_once __DIR__ . '/includes/NotificationService.php';

        $title = "Payment Received: " . ucfirst(str_replace('_', ' ', $type));
        $msg = "A payment of KSh " . number_format($amount, 2) . " via M-PESA has been successfully processed for " . str_replace('_', ' ', $type) . ". Receipt: $receipt.";
        NotificationService::notify($member_id, $title, $msg, 'payment');
    } catch (Exception $e) {
        $log('Notification error (email/notification not sent): ' . $e->getMessage());
    }
} else {
    $log('Payment failed with ResultCode ' . $resultCode);
    $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'failed', result_desc = ? WHERE id = ?");
    $stmt->execute([$resultDesc, $pending['id']]);
}

echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Callback processed']);
exit;