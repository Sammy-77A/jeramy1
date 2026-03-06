<?php
require 'config.php';

$callbackJSONData = file_get_contents('php://input');
$callback = json_decode($callbackJSONData, true);

if (!isset($callback['Body']['stkCallback'])) exit;

$data = $callback['Body']['stkCallback'];
$checkout_id = $data['CheckoutRequestID'];
$result_code = $data['ResultCode'];
$result_desc = $data['ResultDesc'];

// Find pending transaction and purpose
$stmt = $pdo->prepare("SELECT member_id, amount, purpose FROM pending_payments WHERE checkout_request_id = ? AND status = 'pending'");
$stmt->execute([$checkout_id]);
$payment = $stmt->fetch();

if (!$payment) exit;

if ($result_code == 0) {
    $member_id = $payment['member_id'];
    $amount = $payment['amount'];
    $purpose = $payment['purpose'] ?? 'deposit';

    // Extract Receipt Number
    $receipt = 'N/A';
    if (isset($data['CallbackMetadata']['Item'])) {
        foreach ($data['CallbackMetadata']['Item'] as $item) {
            if ($item['Name'] == 'MpesaReceiptNumber') {
                $receipt = $item['Value'];
                break;
            }
        }
    }

    $pdo->beginTransaction();
    try {
        if ($purpose === 'welfare') {
            // Success: Insert into welfare_contributions table
            $stmt = $pdo->prepare("INSERT INTO welfare_contributions (member_id, amount, date) VALUES (?, ?, CURDATE()) 
                                   ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)");
            $stmt->execute([$member_id, $amount]);
        } else {
            // Success: Insert into savings_transactions table
            $stmt = $pdo->prepare("INSERT INTO savings_transactions (member_id, type, amount, description, date) 
                                   VALUES (?, 'deposit', ?, 'M-PESA Deposit', CURDATE())");
            $stmt->execute([$member_id, $amount]);
        }

        $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'completed', mpesa_receipt = ? WHERE checkout_request_id = ?");
        $stmt->execute([$receipt, $checkout_id]);
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Callback DB Error: " . $e->getMessage());
    }
} else {
    $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'failed', result_desc = ? WHERE checkout_request_id = ?");
    $stmt->execute([$result_desc, $checkout_id]);
}