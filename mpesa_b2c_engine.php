<?php
// mpesa_b2c_engine.php
require_once '.env.php';

function initiate_mpesa_b2c($phone, $amount, $remarks) {
    // 1. Get Access Token using B2C-specific keys
    $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    // CRITICAL: Uses B2C keys, not MPESA_ keys
    curl_setopt($ch, CURLOPT_USERPWD, getenv('B2C_CONSUMER_KEY').':'.getenv('B2C_CONSUMER_SECRET'));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    
    $response_token = curl_exec($ch);
    $token_data = json_decode($response_token);
    
    if (!$token_data || !isset($token_data->access_token)) {
        return ['ResponseCode' => '999', 'ResponseDescription' => 'Auth Failed: Check B2C Keys'];
    }
    $token = $token_data->access_token;
    curl_close($ch);

    // 2. Prepare B2C Payout Request
    $b2cUrl = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';
    $payload = [
        'InitiatorName'      => getenv('B2C_INITIATOR'),
        'SecurityCredential' => getenv('B2C_SECURITY_CREDENTIAL'),
        'CommandID'          => 'SalaryPayment',
        'Amount'             =>  round($amount),
        'PartyA'             => getenv('B2C_SHORTCODE'), // Should be 600978
        'PartyB'             => $phone,
        'Remarks'            => $remarks,
        'QueueTimeOutURL'    => getenv('B2C_TIMEOUT_URL'),
        'ResultURL'          => getenv('B2C_RESULT_URL'),
        'Occasion'           => 'Jera_Moyie_ShareOut'
    ];

    $ch = curl_init($b2cUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer ' . $token]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $curl_response = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_response === false || empty($curl_response)) {
        return [
            'ResponseCode' => 'CURL_ERR', 
            'ResponseDescription' => 'No response from Safaricom. Error: ' . $curl_error
        ];
    }
    
    return json_decode($curl_response, true);
}
?>