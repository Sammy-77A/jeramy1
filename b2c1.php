<?php
// ===== CONFIG =====
$consumerKey = 'YTOSaAGpMh856x4AvpmGb5wDGrwHL5ArnhPzAAfHoJIGPbMu'; # Fill with your app Consumer Key$consumerSecret = "YOUR_CONSUMER_SECRET";
$consumerSecret = 'rf4HLlmrOqKLdOHGu8TOPjzFknnOyAzIHADQr4GZuDdrQN443ilu5RchEegmaYsQ'; # Fill with your app Secret

$initiatorName = "testapi";
$initiatorPass = "Safaricom123!!";
$shortCode = "600978"; // sandbox shortcode
$partyB = "254708374149"; // sandbox test MSISDN
$amount = 10;

// ===== GET ACCESS TOKEN =====
$credentials = base64_encode($consumerKey . ":" . $consumerSecret);

$ch = curl_init("https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials");
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Basic $credentials"]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$token = json_decode($response)->access_token;

// ===== GENERATE SECURITY CREDENTIAL =====
$publicKey = file_get_contents("sandbox_cert.pem");
if ($publicKey === false) {
    die("Cannot read sandbox_cert.pem");
}

openssl_public_encrypt(
    $initiatorPass,
    $encrypted,
    $publicKey,
    OPENSSL_PKCS1_PADDING
);

$securityCredential = base64_encode($encrypted);

// ===== B2C PAYLOAD =====
$data = [
    "InitiatorName" => $initiatorName,
    "SecurityCredential" => $securityCredential,
    "CommandID" => "BusinessPayment",
    "Amount" => $amount,
    "PartyA" => $shortCode,
    "PartyB" => $partyB,
    "Remarks" => "Sandbox B2C Test",
    "QueueTimeOutURL" => "'https://jeramy1.top/B2CResultURL.php",
    "ResultURL" => "'https://jeramy1.top/B2CResultURL.php",
    "Occasion" => "Test"
];

// ===== SEND REQUEST =====
$ch = curl_init("https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $token",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);
curl_close($ch);

echo $result;
