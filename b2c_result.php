<?php
require_once 'config.php';
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['Result']) && $data['Result']['ResultCode'] == 0) {
    $mpesaCode = $data['Result']['TransactionID'];
    $convoId = $data['Result']['ConversationID'];

    // Update history with the REAL M-Pesa code automatically
    $stmt = $pdo->prepare("UPDATE share_out_history SET transaction_reference = ?, payout_status = 'completed' WHERE mpesa_conversation_id = ?");
    $stmt->execute([$mpesaCode, $convoId]);
}
?>