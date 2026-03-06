<?php
date_default_timezone_set('Africa/Nairobi');

// Load .env
require_once __DIR__ . '/.env.php';

$consumerKey = getenv('MPESA_CONSUMER_KEY');
$consumerSecret = getenv('MPESA_CONSUMER_SECRET');
$BusinessShortCode = getenv('MPESA_SHORTCODE');
$Passkey = getenv('MPESA_PASSKEY');

if (!$consumerKey || !$consumerSecret) {
  error_log("M-Pesa credentials missing in .env");
  header('Location: error.php?msg=' . urlencode('System configuration error.'));
  exit;
}

// ... rest of your STK code (same as before)
$Amount = 1;
$CallBackURL = getenv('MPESA_CALLBACK_URL');

$logFile = __DIR__ . '/stk_debug.log';
$log = function ($msg) use ($logFile) {
  file_put_contents($logFile, date('Y-m-d H:i:s') . " | $msg\n", FILE_APPEND | LOCK_EX);
};

$log("=== STK PUSH START ===");

$token = @file_get_contents(__DIR__ . '/temp_token.txt');
$phone = $_POST['phone'] ?? '';

if (!$token || !preg_match('/^254[71][0-9]{8}$/', $phone)) {
  $log("ERROR: Invalid token or phone");
  header('Location: error.php?msg=' . urlencode('Invalid data. Please try again.'));
  exit;
}

$Timestamp = date('YmdHis');
$Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);

/* --- Get Access Token --- */
$tokenUrl = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$ch = curl_init($tokenUrl);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
  CURLOPT_USERPWD => "$consumerKey:$consumerSecret",
  CURLOPT_TIMEOUT => 30,
]);
$tokenResp = curl_exec($ch);
$tokenCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$tokenErr = curl_error($ch);
curl_close($ch);

$log("Token HTTP: $tokenCode | Err: $tokenErr | Resp: $tokenResp");

if ($tokenErr || $tokenCode !== 200) {
  $log("ERROR: Token failed - HTTP $tokenCode");
  header('Location: error.php?msg=' . urlencode('M-Pesa connection failed. Try again later.'));
  exit;
}

$access_token = json_decode($tokenResp, true)['access_token'] ?? null;
if (!$access_token) {
  $log("ERROR: No access token");
  header('Location: error.php?msg=' . urlencode('M-Pesa authentication failed.'));
  exit;
}

/* --- STK Push --- */
$payload = [
  'BusinessShortCode' => $BusinessShortCode,
  'Password' => $Password,
  'Timestamp' => $Timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $Amount,
  'PartyA' => $phone,
  'PartyB' => $BusinessShortCode,
  'PhoneNumber' => $phone,
  'CallBackURL' => $CallBackURL . '?token=' . $token,
  'AccountReference' => 'JERAMOYIE',
  'TransactionDesc' => 'Jeramoyie Membership'
];

$stkUrl = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$ch = curl_init($stkUrl);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($payload),
  CURLOPT_HTTPHEADER => [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
  ],
  CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

$log("STK HTTP: $httpCode | Err: $curlErr | Resp: $response");

if ($curlErr || $httpCode !== 200) {
  $log("ERROR: STK failed - HTTP $httpCode");
  header('Location: error.php?msg=' . urlencode('Failed to send payment request. Try again.'));
  exit;
}

$resp = json_decode($response, true);
if (!isset($resp['ResponseCode']) || $resp['ResponseCode'] !== '0') {
  $log("ERROR: STK response not success - " . json_encode($resp));
  header('Location: error.php?msg=' . urlencode('Payment request failed. Please try again.'));
  exit;
}

echo $response;
$log("=== STK PUSH END ===");
