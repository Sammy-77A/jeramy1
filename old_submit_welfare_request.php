<?php
require 'config.php';

if (!isset($_SESSION['member_id']) || $_SESSION['paid'] !== 1) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: welfare.php');
    exit;
}

$member_id = $_SESSION['member_id'];
$reason    = trim($_POST['reason']);
$amount    = (float)$_POST['amount'];
$desc      = trim($_POST['description']);

if ($amount < 1000 || $amount > 100000) {
    $_SESSION['error'] = "Amount must be between KSh 1,000 and KSh 100,000";
    header('Location: welfare.php');
    exit;
}

if (empty($desc) || strlen($desc) < 20) {
    $_SESSION['error'] = "Please provide a detailed explanation (min 20 characters)";
    header('Location: welfare.php');
    exit;
}

// Prevent spam: max 3 pending requests
$pending = $pdo->prepare("SELECT COUNT(*) FROM welfare_requests WHERE member_id = ? AND status = 'pending'");
$pending->execute([$member_id]);
if ($pending->fetchColumn() >= 3) {
    $_SESSION['error'] = "You already have 3 pending requests. Wait for approval.";
    header('Location: welfare.php');
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO welfare_requests 
        (member_id, reason, amount_requested, description, status, date) 
        VALUES (?, ?, ?, ?, 'pending', NOW())");
    $stmt->execute([$member_id, $reason, $amount, $desc]);

    $_SESSION['success'] = "Welfare request submitted successfully! The officials will review it shortly.";
} catch (Exception $e) {
    $_SESSION['error'] = "Failed to submit request. Try again.";
}

header('Location: welfare.php');
exit;
