<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: signup.php');
    exit;
}

/* ---------- 1. Normalize phone to 254… ---------- */
$rawPhone = preg_replace('/[\s\-]/', '', trim($_POST['phone']));
$rawPhone = ltrim($rawPhone, '+');
// Convert 07xx/01xx → 2547xx/2541xx
if (preg_match('/^0[71][0-9]{8}$/', $rawPhone)) {
    $phone = '254' . substr($rawPhone, 1);
}
elseif (preg_match('/^254[71][0-9]{8}$/', $rawPhone)) {
    $phone = $rawPhone;
}
else {
    $phone = null;
}

$name = trim($_POST['name']);
$national_id = trim($_POST['national_id']);
$password = $_POST['password'];

/* ---------- 2. Validation ---------- */
if (strlen($national_id) < 7) {
    header('Location: error.php?msg=' . urlencode('National ID must be at least 7 digits.'));
    exit;
}
if (!$phone) {
    header('Location: error.php?msg=' . urlencode('Phone must be a valid Kenyan number (07xxxxxxxx or 01xxxxxxxx).'));
    exit;
}

/* ---------- 3. Duplicate check ---------- */
$stmt = $pdo->prepare("SELECT id FROM members WHERE national_id = ? OR phone = ?");
$stmt->execute([$national_id, $phone]);
if ($stmt->fetch()) {
    header('Location: error.php?msg=' . urlencode('National ID or Phone already registered.'));
    exit;
}

$stmt = $pdo->prepare("SELECT token FROM pending_registrations WHERE national_id = ? OR phone = ?");
$stmt->execute([$national_id, $phone]);
if ($old = $stmt->fetch()) {
    $pdo->prepare("DELETE FROM pending_registrations WHERE token = ?")->execute([$old['token']]);
    error_log("Deleted old pending registration for phone $phone");
}

/* ---------- 4. Insert into pending_registrations ---------- */
$token = bin2hex(random_bytes(16));
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare(
    "INSERT INTO pending_registrations (token, name, national_id, phone, password_hash) VALUES (?, ?, ?, ?, ?)"
);
$stmt->execute([$token, $name, $national_id, $phone, $hash]);

/* ---------- 5. Store token for STK ---------- */
file_put_contents('temp_token.txt', $token);

/* ---------- 6. Pass normalized phone to STK ---------- */
$_POST['phone'] = $phone;

ob_start();
require 'stk_initiate.php';
$stkResponse = json_decode(ob_get_clean(), true);

/* ---------- 7. Handle STK result ---------- */
if (
isset($stkResponse['ResponseCode']) && $stkResponse['ResponseCode'] === '0' &&
!empty($stkResponse['CustomerMessage'])
) {
    $_SESSION['pending_token'] = $token;
    $_SESSION['pending_phone'] = $phone;
    header('Location: pending.php');
    exit;
}
else {
    $pdo->prepare("DELETE FROM pending_registrations WHERE token = ?")->execute([$token]);
    @unlink('temp_token.txt');
    $error = $stkResponse['error'] ?? 'STK push failed. Please try again.';
    header('Location: error.php?msg=' . urlencode($error));
    exit;
}
