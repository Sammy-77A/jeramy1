<?php
require 'config.php';

$content = file_get_contents('php://input');
$data = json_decode($content, true);

if (isset($data['Body']['stkCallback'])) {
    $callback = $data['Body']['stkCallback'];
    $checkoutId = $callback['CheckoutRequestID'];
    $resultCode = $callback['ResultCode'];

    // 0 means Success (Correct PIN entered)
    $status = ($resultCode == 0) ? 'success' : 'failed';

    $stmt = $pdo->prepare("UPDATE reset_attempts SET status = ? WHERE checkout_request_id = ?");
    $stmt->execute([$status, $checkoutId]);
}
