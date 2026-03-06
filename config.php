<?php
// config.php

// --- Session Configuration (cPanel/HTTPS compatible) ---
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_only_cookies', 1);
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        ini_set('session.cookie_secure', 1);
    }
    session_start();
}

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'jeramoyie1');

/*define('DB_HOST', 'localhost'); define('DB_USER', 'grtzpvyr_Samuel'); // change if needed define('DB_PASS', 'c=7!dD%ECaFJdfvI'); // change if needed define('DB_NAME', 'grtzpvyr_jeramoyie');*/


try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
}
catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}

/**
 * REINFORCED: Admin Activity Logger
 */
function log_admin_activity($pdo, $action, $details)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['member_id'])) {
        try {
            $stmt = $pdo->prepare("INSERT INTO admin_logs (member_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['member_id'],
                $action,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
            ]);
        }
        catch (PDOException $e) {
            error_log("Audit Log Failed: " . $e->getMessage());
        }
    }
}