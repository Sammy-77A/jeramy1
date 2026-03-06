<?php
require 'config.php';
header('Content-Type: application/json');

$checkout_id = $_GET['id'] ?? null;

if (!$checkout_id || !isset($_SESSION['member_id'])) {
    echo json_encode(['status' => false, 'result_desc' => 'Invalid request']);
    exit;
}

// Check the pending_payments table for this checkout ID
$stmt = $pdo->prepare("SELECT status, result_desc, mpesa_receipt FROM pending_payments WHERE checkout_request_id = ? AND member_id = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$checkout_id, $_SESSION['member_id']]);
$payment = $stmt->fetch();

if (!$payment) {
    echo json_encode(['status' => 'not_found', 'result_desc' => 'Payment record not found']);
    exit;
}

echo json_encode([
    'status' => $payment['status'],
    'result_desc' => $payment['result_desc'] ?? '',
    'receipt' => $payment['mpesa_receipt'] ?? ''
]);
exit;