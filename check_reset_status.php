<?php
require 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['reset_req_id'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$stmt = $pdo->prepare("SELECT status FROM reset_attempts WHERE checkout_request_id = ?");
$stmt->execute([$_SESSION['reset_req_id']]);
$attempt = $stmt->fetch();

if ($attempt && $attempt['status'] === 'success') {
    // CRITICAL: Authorize the user to reset their password now
    $_SESSION['is_verified_for_reset'] = true;
}

echo json_encode(['status' => $attempt['status'] ?? 'pending']);
