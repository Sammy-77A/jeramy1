<?php
require 'config.php';
require 'EmailService.php';

$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawPhone = trim($_POST['phone']);
    $phone = preg_replace('/^0/', '254', preg_replace('/[^0-9]/', '', $rawPhone)); //

    $stmt = $pdo->prepare("SELECT id, name, email FROM members WHERE phone = ?");
    $stmt->execute([$phone]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Phone number not registered.";
    }
    elseif (empty($user['email'])) {
        $error = "No email address found. Please log in and update your profile first.";
    }
    else {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+30 minutes')); // 30 Minute Expiry

        $pdo->prepare("DELETE FROM password_resets WHERE phone = ?")->execute([$phone]);
        $pdo->prepare("INSERT INTO password_resets (phone, token, expires_at) VALUES (?, ?, ?)")
            ->execute([$phone, $token, $expires]);

        $emailService = new EmailService();
        $result = $emailService->sendPasswordResetEmail($user['email'], $user['name'], $token, $user['id']);

        if ($result['success']) {
            $success = "Check your email! A secure reset link has been sent.";
        }
        else {
            $error = "Mail error. Please contact admin.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password • Jera Moyie</title>

</head>

<body>

    <script>
        function showLoader() {
            document.getElementById('submitBtn').disabled = true;
            document.getElementById('loader').classList.remove('d-none');
            document.getElementById('btnText').innerText = 'Sending...';
        }
    </script>
</body>

</html>