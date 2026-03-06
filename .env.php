<?php
// .env.php - Fixed & Bulletproof
$envFile = __DIR__ . '/.env';

if (!file_exists($envFile)) {
    die('.env file not found! Create it in the project root.');
}

$env = parse_ini_file($envFile, false, INI_SCANNER_RAW);

if ($env === false || $env === null) {
    die('ERROR: Your .env file has invalid syntax. Remove comments, quotes, and special characters like %');
}

foreach ($env as $key => $value) {
    // Remove potential surrounding quotes from the value
    $value = trim($value);
    $value = preg_replace('/^"|"$/', '', $value);
    $value = preg_replace("/^'|'$/", '', $value);
    putenv("$key=" . $value);
}
