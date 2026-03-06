<?php
// b2c_callback_url.php
require_once 'config.php';

$json = file_get_contents('php://input');
file_put_contents("B2CResultResponse.json", $json . PHP_EOL, FILE_APPEND);

$data = json_decode($json, true);

if (isset($data['Result'])) {
    $resultCode = $data['Result']['ResultCode'];
    $conversationID = $data['Result']['ConversationID'];
    $trx_id = $data['Result']['TransactionID'] ?? 'FAILED';
    
    if ($resultCode == 0) {
        // SUCCESS: Update history with real M-Pesa code
        $stmt = $pdo->prepare("UPDATE share_out_history SET transaction_reference = ?, payout_status = 'completed' WHERE mpesa_conversation_id = ?");
        $stmt->execute([$trx_id, $conversationID]);
    } else {
        // FAILURE
        $stmt = $pdo->prepare("UPDATE share_out_history SET payout_status = 'failed', transaction_reference = 'Error: API Reject' WHERE mpesa_conversation_id = ?");
        $stmt->execute([$conversationID]);
    }
}
?>