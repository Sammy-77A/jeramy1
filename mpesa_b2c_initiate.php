<?php
function send_mpesa_payout($phone, $amount, $member_id)
{
    $accessToken = get_mpesa_access_token(); // Use your existing token logic
    $url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';

    $payload = [
        'InitiatorName' => getenv('MPESA_B2C_INITIATOR'),
        'SecurityCredential' => getenv('MPESA_B2C_SECURITY_CREDENTIAL'),
        'CommandID' => 'BusinessPayment', // Specific for dividends
        'Amount' => round($amount),
        'PartyA' => getenv('MPESA_B2C_SHORTCODE'),
        'PartyB' => $phone,
        'Remarks' => 'Yearly Dividend',
        'QueueTimeOutURL' => 'https://jeramy1.top/b2c_timeout.php',
        'ResultURL' => 'https://jeramy1.top/b2c_result.php',
        'Occasion' => 'Jera Moyie Share-Out'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $accessToken]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = json_decode(curl_exec($ch), true);
    return $response['ConversationID'] ?? null;
}