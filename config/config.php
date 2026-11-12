<?php
/**
 * Application Configuration
 * TKJ Service Business Web App
 */

// Set Default Timezone for Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');


// Load local .env file if present (without overriding existing environment variables)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim(trim($val), '"\'');
            if (!getenv($key) && !isset($_ENV[$key]) && !isset($_SERVER[$key])) {
                putenv("{$key}={$val}");
                $_ENV[$key] = $val;
                $_SERVER[$key] = $val;
            }
        }
    }
}


// Base URL Definition - Dynamic detection to handle subfolders accurately on XAMPP/Localhost
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || ($_SERVER['SERVER_PORT'] ?? 80) == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$baseUrl = rtrim($protocol . $host . $scriptDir, '/');

define('BASE_URL', $baseUrl);

// Application Details
define('APP_NAME', 'TKJ Service Center');
define('APP_TAGLINE', 'Solusi Layanan IT & Jaringan Profesional Sekolah');
define('APP_VERSION', '1.0.0');

// Database Configuration (MySQL / MariaDB XAMPP Defaults)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_tkj_service');
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

// Session Config
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------
// OTP & Notification Service Configuration
// Environment variables are prioritized to prevent committing secrets
// -------------------------------------------------------------
define('OTP_PROVIDER', getenv('OTP_PROVIDER') ?: 'fonnte'); // e.g. fonnte, wablas, twilio, mock
define('OTP_API_TOKEN', getenv('OTP_API_TOKEN') ?: ''); // Set in server environment or .env
define('OTP_API_ENDPOINT', getenv('OTP_API_ENDPOINT') ?: 'https://api.fonnte.com/send');
define('OTP_EXPIRY_MINUTES', (int)(getenv('OTP_EXPIRY_MINUTES') ?: 5)); // 5 minutes validity
define('OTP_MAX_ATTEMPTS', (int)(getenv('OTP_MAX_ATTEMPTS') ?: 3)); // Max 3 wrong attempts
define('OTP_RESEND_COOLDOWN', (int)(getenv('OTP_RESEND_COOLDOWN') ?: 60)); // 60 seconds cooldown
define('OTP_RATE_LIMIT_PER_HOUR', (int)(getenv('OTP_RATE_LIMIT_PER_HOUR') ?: 5)); // Max 5 requests/hr per identifier

