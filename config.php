<?php
/**
 * Krishna Education Foundation - Configuration File
 *
 * Configure your database and environment settings here.
 * For live hosting (Hostinger / cPanel / DirectAdmin / VPS), update the DB_* constants below.
 */

// 1. Environment & Host Detection
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocalhost = in_array($httpHost, ['localhost', '127.0.0.1', '::1']) || strpos($httpHost, 'localhost:') === 0 || php_sapi_name() === 'cli';

// 2. Database Configuration
if ($isLocalhost && !getenv('FORCE_PROD_DB')) {
    // Local development settings (XAMPP)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: 'vision_point_db');
    define('DB_USER', getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''));
} else {
    // Live Production settings (krishnaeducationfoundation.edu.np)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: 'u474709711_jaiswal');
    define('DB_USER', getenv('DB_USER') ?: 'u474709711_krishna');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'rAmsagardon1');
}
define('DB_CHARSET', 'utf8mb4');

// 3. Environment Mode (Set to true to show detailed error messages during troubleshooting)
define('DEV_MODE', getenv('PHP_DEV') === '1' || getenv('APP_DEBUG') === 'true' || $isLocalhost);

// 4. Admin Security & Session settings
define('ADMIN_EMAIL', 'admin@krishnaeducation.info');
define('SESSION_LIFETIME', 86400); // 24 hours
