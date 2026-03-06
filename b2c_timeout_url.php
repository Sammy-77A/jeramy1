<?php
// b2c_timeout_url.php
require_once 'config.php';

// Capture the raw timeout notification from Safaricom
$json = file_get_contents('php://input');

// Log the timeout for debugging and audit
file_put_contents("B2CTimeoutLog.json", $json . PHP_EOL, FILE_APPEND);

$data = json_decode($json, true);

if (isset($data['Result'])) {
    $convo_id = $data['Result']['ConversationID'];
    
    // Update history to mark the transaction as failed/timed out
    // This allows you to reset the 'Confirm' button for the member
    $stmt = $pdo->prepare("UPDATE share_out_history SET payout_status = 'failed', transaction_reference = 'System Timeout' WHERE mpesa_conversation_id = ?");
    $stmt->execute([$convo_id]);
}
?>