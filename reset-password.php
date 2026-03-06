<?php
require 'config.php';
require 'EmailService.php'; // Added to handle security alert

$error = $success = '';
$token = $_GET['token'] ?? '';
$user_id = $_GET['user'] ?? '';

if (!$token || !$user_id) {
    header('Location: forgot-password.php');
    exit;
}

// Check for valid token and expiry
$stmt = $pdo->prepare("SELECT phone FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
$stmt->execute([$token]);
$resetReq = $stmt->fetch();

if (!$resetReq) {
    die("<div style='text-align:center; padding:50px; font-family:sans-serif;'>
            <h3>Link Invalid or Expired</h3>
            <p>For security, reset links expire after 30 minutes and can only be used once. <a href='forgot-password.php'>Request a new one here</a>.</p>
         </div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    }
    elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    }
    else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // 1. Fetch user details for the security alert notification
        $userStmt = $pdo->prepare("SELECT name, email FROM members WHERE phone = ?");
        $userStmt->execute([$resetReq['phone']]);
        $member = $userStmt->fetch();

        // 2. Update the member's password
        $pdo->prepare("UPDATE members SET password_hash = ? WHERE phone = ?")
            ->execute([$hash, $resetReq['phone']]);

        // 3. SINGLE USE ENFORCEMENT: Delete the token immediately after success
        $pdo->prepare("DELETE FROM password_resets WHERE phone = ?")
            ->execute([$resetReq['phone']]);

        // 4. Send the Security Notification Alert
        if ($member && !empty($member['email'])) {
            $emailService = new EmailService();
            $emailService->sendPasswordChangedAlert($member['email'], $member['name']);
        }

        $success = "Password updated! Redirecting to login...";
        header("refresh:2;url=login.php");
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Password • Jera Moyie</title>

</head>

<body>

</body>

</html>