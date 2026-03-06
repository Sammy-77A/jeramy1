<?php
/**
 * STK Query Fallback — when M-Pesa callback doesn't arrive (e.g. ngrok/localhost),
 * we poll Safaricom's STK Push Query API. If the payment completed, we apply the
 * same DB updates as callback_handler and return status so the pending page can redirect.
 */
require 'config.php';
require_once __DIR__ . '/.env.php';

header('Content-Type: application/json');

if (!isset($_SESSION['member_id'])) {
    echo json_encode(['status' => 'error', 'result_desc' => 'Not logged in']);
    exit;
}

$checkout_id = $_GET['id'] ?? $_POST['id'] ?? null;
if (!$checkout_id) {
    echo json_encode(['status' => 'error', 'result_desc' => 'Missing checkout ID']);
    exit;
}

// Only allow querying this member's pending payment
$stmt = $pdo->prepare("SELECT * FROM pending_payments WHERE checkout_request_id = ? AND member_id = ? AND status = 'pending'");
$stmt->execute([$checkout_id, $_SESSION['member_id']]);
$pending = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pending) {
    // Already completed/failed — let check_status handle it
    $stmt = $pdo->prepare("SELECT status, result_desc, mpesa_receipt FROM pending_payments WHERE checkout_request_id = ? AND member_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$checkout_id, $_SESSION['member_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode([
            'status' => $row['status'],
            'result_desc' => $row['result_desc'] ?? '',
            'receipt' => $row['mpesa_receipt'] ?? ''
        ]);
    } else {
        echo json_encode(['status' => 'not_found', 'result_desc' => 'Payment not found']);
    }
    exit;
}

// Credentials
$BusinessShortCode = getenv('MPESA_SHORTCODE');
$Passkey = getenv('MPESA_PASSKEY');
$consumerKey = getenv('MPESA_CONSUMER_KEY');
$consumerSecret = getenv('MPESA_CONSUMER_SECRET');
$mpesaEnv = strtolower(getenv('MPESA_ENV') ?: 'sandbox');
$mpesaBase = ($mpesaEnv === 'live') ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

if (empty($BusinessShortCode) || empty($Passkey) || empty($consumerKey) || empty($consumerSecret)) {
    echo json_encode(['status' => 'pending', 'result_desc' => 'Config missing']);
    exit;
}

// Get OAuth token
$ch = curl_init($mpesaBase . '/oauth/v1/generate?grant_type=client_credentials');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_USERPWD => "$consumerKey:$consumerSecret",
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 15
]);
$tokenResp = curl_exec($ch);
$tokenCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($tokenCode !== 200) {
    echo json_encode(['status' => 'pending', 'result_desc' => 'Token failed']);
    exit;
}

$access_token = json_decode($tokenResp, true)['access_token'] ?? null;
if (!$access_token) {
    echo json_encode(['status' => 'pending', 'result_desc' => 'No token']);
    exit;
}

// STK Push Query
$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);
$payload = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'CheckoutRequestID' => $checkout_id
];

$ch = curl_init($mpesaBase . '/mpesa/stkpushquery/v1/query');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 15
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    echo json_encode(['status' => 'pending', 'result_desc' => 'Query API error']);
    exit;
}

$resp = json_decode($response, true);
$resultCode = isset($resp['ResultCode']) ? (int)$resp['ResultCode'] : -1;
$resultDesc = $resp['ResultDesc'] ?? 'Unknown';

if ($resultCode !== 0) {
    // Payment failed or cancelled — update DB so UI stops polling
    $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'failed', result_desc = ? WHERE id = ?");
    $stmt->execute([$resultDesc, $pending['id']]);
    echo json_encode(['status' => 'failed', 'result_desc' => $resultDesc, 'receipt' => '']);
    exit;
}

// Success: apply same ledger logic as callback_handler (DB only updated after confirmed success)
$member_id = (int)$pending['member_id'];
$amount = (float)$pending['amount'];
$type = $pending['payment_type'];
$receipt = 'STK_QUERY'; // Query API doesn't return M-Pesa receipt

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
            $stmt = $pdo->prepare("INSERT INTO loan_repayments (member_id, amount, repayment_date, reference_number, notes) VALUES (?, ?, CURDATE(), ?, 'M-PESA Auto-Payment')");
            $stmt->execute([$member_id, $amount, $receipt]);
            $stmt = $pdo->prepare("SELECT id, balance FROM normal_loans WHERE member_id = ? AND status IN ('active', 'approved') AND balance > 0 ORDER BY request_date ASC LIMIT 1");
            $stmt->execute([$member_id]);
            $loan = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($loan) {
                $new_balance = max(0, (float)$loan['balance'] - $amount);
                $new_status = ($new_balance <= 0) ? 'paid' : 'active';
                $stmt = $pdo->prepare("UPDATE normal_loans SET balance = ?, status = ? WHERE id = ?");
                $stmt->execute([$new_balance, $new_status, $loan['id']]);
            }
            break;
    }

    $stmt = $pdo->prepare("UPDATE pending_payments SET status = 'completed', mpesa_receipt = ?, completed_at = NOW(), result_desc = ? WHERE id = ?");
    $stmt->execute([$receipt, $resultDesc, $pending['id']]);
    $pdo->commit();

    // Notify member + send email (same as callback path); do not affect STK response
    try {
        require_once __DIR__ . '/includes/NotificationService.php';
        $title = 'Payment Received: ' . ucfirst(str_replace('_', ' ', $type));
        $msg = 'A payment of KSh ' . number_format($amount, 2) . ' via M-PESA has been successfully processed for ' . str_replace('_', ' ', $type) . '. Receipt: ' . $receipt . '.';
        NotificationService::notify($member_id, $title, $msg, 'payment');
    } catch (Exception $e) {
        error_log('stk_query_fallback notification error: ' . $e->getMessage());
    }

    echo json_encode(['status' => 'completed', 'result_desc' => $resultDesc, 'receipt' => $receipt]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log('stk_query_fallback DB error: ' . $e->getMessage());
    echo json_encode(['status' => 'pending', 'result_desc' => 'Update failed']);
}
