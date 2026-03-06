<?php

/* Urls */
$access_token_url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$b2c_url = 'https://sandbox.safaricom.co.ke/mpesa/b2c/v1/paymentrequest';


/* Required Variables */
$consumerKey = 'YTOSaAGpMh856x4AvpmGb5wDGrwHL5ArnhPzAAfHoJIGPbMu'; # Fill with your app Consumer Key
$consumerSecret = 'rf4HLlmrOqKLdOHGu8TOPjzFknnOyAzIHADQr4GZuDdrQN443ilu5RchEegmaYsQ'; # Fill with your app Secret
$headers = ['Content-Type:application/json; charset=utf8'];

/* from the test credentials provided on you developers account */
$InitiatorName = 'testapi'; # Initiator
$SecurityCredential = 'Ers2Ccar4/+cdILmF/VYynoJZuxLekP/NxNT6GrqhTwMW/9eLe/PyPKEpT3XMb9dY9j3IIEGpqjNvTNbaXBUD0A/7QMoT70Eg++QLU8f4zUhrESFvb0mQq44VS9chlL5SZnXYB8l8QNxkVaGaEhoDeapJuw/EqnrTQ44WzOxJul2lbotmQ4FNTQZcCPlHzjDC9keFPF2DiJon8LF/zb02xa+ddHn8W+WOvthcQlwEopCoo9Rq3NNLh2aEu/bJArAnzRlkpdaGTSI5/TxBC2/77pMKps1A8baLbGJXAoUfZSJwvbkF6mso43BK1vLaGsPTfW+uWwNMG7rEw5whtIv8w==';
$CommandID = 'SalaryPayment'; # choose between SalaryPayment, BusinessPayment, PromotionPayment 
$Amount = '10';
$PartyA = '600978'; # shortcode 1
$PartyB = '254708374149'; # Phone number you're sending money to
$Remarks = 'Salary'; # Remarks ** can not be empty
$QueueTimeOutURL = 'https://jeramy1.top/B2CResultURL.php'; # your QueueTimeOutURL
$ResultURL = 'https://jeramy1.top/B2CResultURL.php'; # your ResultURL
$Occasion = 'DivPayout'; # Occasion

/* Obtain Access Token */
$curl = curl_init($access_token_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_HEADER, FALSE);
curl_setopt($curl, CURLOPT_USERPWD, $consumerKey . ':' . $consumerSecret);
$result = curl_exec($curl);
$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($result);
$access_token = $result->access_token;
curl_close($curl);

/* Main B2C Request to the API */
$b2cHeader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $b2c_url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $b2cHeader); //setting custom header

$curl_post_data = array(
  //Fill in the request parameters with valid values
  'InitiatorName' => $InitiatorName,
  'SecurityCredential' => $SecurityCredential,
  'CommandID' => $CommandID,
  'Amount' => $Amount,
  'PartyA' => $PartyA,
  'PartyB' => $PartyB,
  'Remarks' => $Remarks,
  'QueueTimeOutURL' => $QueueTimeOutURL,
  'ResultURL' => $ResultURL,
  'Occasion' => $Occasion
);

$data_string = json_encode($curl_post_data);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
$curl_response = curl_exec($curl);
print_r($curl_response);
echo $curl_response;
?>
