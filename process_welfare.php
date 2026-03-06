<?php
require 'config.php';
require_once '.env.php';

// 1. Security Check
if (!isset($_SESSION['member_id']) || $_SESSION['paid'] !== 1) {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['amount'])) {
    header('Location: welfare.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$amount = (int)$_POST['amount'];

if ($amount < 1) {
    $_SESSION['error'] = "Amount must be at least KSh 1";
    header('Location: welfare.php');
    exit;
}

// 2. Get member's phone
$stmt = $pdo->prepare("SELECT phone FROM members WHERE id = ?");
$stmt->execute([$member_id]);
$phone = $stmt->fetchColumn();

// Format phone (254... - supports 07xx and 01xx)
$phone = preg_replace('/[\s\-]/', '', $phone);
$phone = ltrim($phone, '+');
if (preg_match('/^0[71][0-9]{8}$/', $phone)) {
    $phone = '254' . substr($phone, 1);
}

// 3. Credentials
$consumer_key = getenv('MPESA_CONSUMER_KEY') ?: die('Env Error: Key Missing');
$consumer_secret = getenv('MPESA_CONSUMER_SECRET') ?: die('Env Error: Secret Missing');
$shortcode = getenv('MPESA_SHORTCODE') ?: die('Env Error: Shortcode Missing');
$passkey = getenv('MPESA_PASSKEY') ?: die('Env Error: Passkey Missing');
// Internal welfare payments use callback_handler.php
$callback_url = str_replace('callback_url.php', 'callback_handler.php', getenv('MPESA_CALLBACK_URL')) ?: die('Env Error: Callback Missing');

// 4. Get Access Token
$credentials = base64_encode("$consumer_key:$consumer_secret");
$ch = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);

if ($response === false) {
    $_SESSION['error'] = "Connection Failed: " . curl_error($ch);
    curl_close($ch);
    header('Location: welfare.php');
    exit;
}
curl_close($ch);

$token = json_decode($response)->access_token ?? null;

if (!$token) {
    error_log("M-PESA Token Error: " . $response);
    $_SESSION['error'] = "M-PESA Token Failed. Please try again.";
    header('Location: welfare.php');
    exit;
}

// 5. STK Push
$timestamp = date('YmdHis');
$password = base64_encode($shortcode . $passkey . $timestamp);

$post_data = [
    "BusinessShortCode" => $shortcode,
    "Password" => $password,
    "Timestamp" => $timestamp,
    "TransactionType" => "CustomerPayBillOnline",
    "Amount" => $amount,
    "PartyA" => $phone,
    "PartyB" => $shortcode,
    "PhoneNumber" => $phone,
    "CallBackURL" => $callback_url,
    "AccountReference" => "WELFARE",
    "TransactionDesc" => "Welfare Contribution"
];

$curl = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt_array($curl, [
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $token
    ],
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($post_data),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false
]);

$response = curl_exec($curl);
curl_close($curl);
$result = json_decode($response);

if (isset($result->ResponseCode) && $result->ResponseCode == "0") {
    $checkout_id = $result->CheckoutRequestID;

    // Insert with purpose='welfare'
    $stmt = $pdo->prepare("INSERT INTO pending_payments (member_id, amount, checkout_request_id, status, purpose) VALUES (?, ?, ?, 'pending', 'welfare') ON DUPLICATE KEY UPDATE status='pending', purpose='welfare'");
    $stmt->execute([$member_id, $amount, $checkout_id]);

    // Store in session for the pending page
    $_SESSION['pending_checkout_id'] = $checkout_id;
    $_SESSION['pending_payment_type'] = 'welfare';
    $_SESSION['pending_payment_amount'] = $amount;

    // Redirect to payment waiting page
    header('Location: payment_pending.php');
}
else {
    $error_msg = $result->errorMessage ?? "Unknown Error";
    $_SESSION['error'] = "Payment Failed: " . $error_msg;
    header('Location: welfare.php');
}
exit;
