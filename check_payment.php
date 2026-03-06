<?php
require 'config.php';
header('Content-Type: application/json');

$token = $_GET['token'] ?? null;
$paid = false;
$pending_exists = false;

if ($token && $token === ($_SESSION['pending_token'] ?? '')) {
    $stmt = $pdo->prepare("SELECT id FROM pending_registrations WHERE token = ?");
    $stmt->execute([$token]);
    if ($stmt->fetch()) {
        $pending_exists = true;
    } else {
        $phone = $_SESSION['pending_phone'] ?? null;
        if ($phone) {
            $stmt = $pdo->prepare("SELECT id, name FROM members WHERE phone = ? AND paid = 1");
            $stmt->execute([$phone]);
            if ($member = $stmt->fetch()) {
                $paid = true;
                // Auto-set session for redirect
                $_SESSION['member_id'] = $member['id'];
                $_SESSION['name'] = $member['name'];
                $_SESSION['paid'] = 1;
                // Clean up pending session vars
                unset($_SESSION['pending_token']);
                unset($_SESSION['pending_phone']);
            }
        }
    }
}

echo json_encode(['paid' => $paid, 'pending_exists' => $pending_exists]);
exit;
