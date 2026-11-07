<?php
/**
 * Vision Point Educational Service - Configuration File
 *
 * Configure your database and environment settings here.
 * For live hosting (Hostinger / cPanel / DirectAdmin / VPS), update the DB_* constants below.
 */

// 1. Environment & Host Detection
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isCli = (php_sapi_name() === 'cli');
$isWindowsXampp = (DIRECTORY_SEPARATOR === '\\' || strpos(__DIR__, 'xampp') !== false);

// Determine if we are running in local development environment
$isLocalhost = (
    in_array($httpHost, ['localhost', '127.0.0.1', '::1'])
    || strpos($httpHost, 'localhost:') === 0
    || ($isCli && $isWindowsXampp)
) && !getenv('FORCE_PROD_DB');

// 2. Database Configuration
if ($isLocalhost) {
    // Local development settings (XAMPP on Windows)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: 'vision_point_db');
    define('DB_USER', getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''));
} else {
    // Live Production settings (cPanel / visionpoint.edu.np)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: 'zptvncco_Visionpoint');
    define('DB_USER', getenv('DB_USER') ?: 'zptvncco_visionpoint');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Visionpoint98185@#');
}
define('DB_CHARSET', 'utf8mb4');

// 3. Environment Mode (Set to true to show detailed error messages during troubleshooting)
define('DEV_MODE', getenv('PHP_DEV') === '1' || getenv('APP_DEBUG') === 'true' || $isLocalhost);

// 4. Admin Security & Session settings
define('ADMIN_EMAIL', 'admin@visionpoint.edu.np');
define('SESSION_LIFETIME', 86400); // 24 hours
