<?php
require 'config.php';
require_once __DIR__ . '/.env.php';

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit;
}

$member_id = $_SESSION['member_id'];
$type = $_POST['type'] ?? '';
$user_amount = floatval($_POST['amount'] ?? 0);
$phone = $_POST['phone'] ?? '';

// Format phone to 254... (supports 07xx and 01xx)
$phone = preg_replace('/[\s\-]/', '', $phone);
$phone = ltrim($phone, '+');
if (preg_match('/^0[71][0-9]{8}$/', $phone)) {
    $phone = '254' . substr($phone, 1);
}
$phone = preg_replace('/^\+/', '', $phone);

// Validate
if ($user_amount < 1) {
    header("Location: profile.php?error=" . urlencode("Amount must be at least KSh 1."));
    exit;
}
if (!preg_match('/^254[71][0-9]{8}$/', $phone)) {
    header("Location: profile.php?error=" . urlencode("Invalid phone number format."));
    exit;
}

// M-PESA Credentials
$BusinessShortCode = getenv('MPESA_SHORTCODE');
$Passkey = getenv('MPESA_PASSKEY');
$consumerKey = getenv('MPESA_CONSUMER_KEY');
$consumerSecret = getenv('MPESA_CONSUMER_SECRET');

// Basic configuration validation before calling Safaricom
if (empty($BusinessShortCode) || empty($Passkey) || empty($consumerKey) || empty($consumerSecret)) {
    error_log('M-Pesa config error: one or more credentials are missing. SHORTCODE=' . ($BusinessShortCode ? 'set' : 'missing') . ', PASSKEY=' . ($Passkey ? 'set' : 'missing') . ', CK=' . ($consumerKey ? 'set' : 'missing') . ', CS=' . ($consumerSecret ? 'set' : 'missing'));
    header("Location: profile.php?error=" . urlencode("M-Pesa is not configured correctly. Please contact support."));
    exit;
}
$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

// 1. Get Access Token
// Allow switching between sandbox and live via MPESA_ENV ('live' or 'sandbox')
$mpesaEnv = strtolower(getenv('MPESA_ENV') ?: 'sandbox');
$mpesaBase = ($mpesaEnv === 'live')
    ? 'https://api.safaricom.co.ke'
    : 'https://sandbox.safaricom.co.ke';

$tokenUrl = $mpesaBase . '/oauth/v1/generate?grant_type=client_credentials';
$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_USERPWD => "$consumerKey:$consumerSecret",
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30
]);
$tokenResponse = curl_exec($ch);
$tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
$access_token = $tokenData['access_token'] ?? null;

if (!$access_token) {
    error_log("M-Pesa Token Failed: HTTP $tokenHttpCode, Response: $tokenResponse");
    header("Location: profile.php?error=" . urlencode("M-Pesa Authentication Failed. Please try again."));
    exit;
}

// 2. Initiate STK Push
$stkUrl = $mpesaBase . '/mpesa/stkpush/v1/processrequest';

// Build callback URL from the current request so Safaricom can reach it (works with ngrok/localhost)
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? '';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
$callbackURL = $scheme . '://' . $host . ($basePath ? $basePath . '/' : '') . 'callback_handler.php';

$payload = [
    'BusinessShortCode' => $BusinessShortCode,
    'Password' => $Password,
    'Timestamp' => $Timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => $user_amount,
    'PartyA' => $phone,
    'PartyB' => $BusinessShortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => $callbackURL,
    'AccountReference' => strtoupper(substr($type, 0, 5)) . $member_id,
    'TransactionDesc' => 'Jera ' . $type
];

$ch = curl_init($stkUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Authorization: Bearer ' . $access_token],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT => 30
]);

$stkRaw = curl_exec($ch);
$stkHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$stkErr = curl_error($ch);
curl_close($ch);

if ($stkErr || $stkHttpCode !== 200) {
    error_log("STK Push HTTP Error: code=$stkHttpCode error=$stkErr response=$stkRaw");
    header("Location: profile.php?error=" . urlencode("Failed to contact M-PESA. Please try again."));
    exit;
}

$stkResponse = json_decode($stkRaw, true) ?? [];

if (isset($stkResponse['ResponseCode']) && $stkResponse['ResponseCode'] == '0') {
    // Record the pending payment
    $stmt = $pdo->prepare("INSERT INTO pending_payments (member_id, amount, payment_type, checkout_request_id, status, phone_number) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->execute([$member_id, $user_amount, $type, $stkResponse['CheckoutRequestID'], $phone]);

    // Store in session for the pending page
    $_SESSION['pending_checkout_id'] = $stkResponse['CheckoutRequestID'];
    $_SESSION['pending_payment_type'] = $type;
    $_SESSION['pending_payment_amount'] = $user_amount;

    // Redirect to the payment waiting page
    header("Location: payment_pending.php");
}
else {
    $errorMsg = $stkResponse['CustomerMessage'] ?? $stkResponse['errorMessage'] ?? "STK Push Failed. Please try again.";
    error_log("STK Push Failed Payload: " . json_encode($stkResponse));
    header("Location: profile.php?error=" . urlencode($errorMsg));
}
exit;