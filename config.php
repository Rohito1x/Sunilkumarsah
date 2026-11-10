<?php
/**
 * Vision Point Educational Service - Configuration File
 *
 * Automatically loads .env configuration if present, or falls back to defined constants.
 */

// Load .env environment variables if available
if (!function_exists('loadEnvFile')) {
    function loadEnvFile(string $filePath): void {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return;
        }
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false) {
            return;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }
            list($key, $val) = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            if ((strpos($val, '"') === 0 && substr($val, -1) === '"') ||
                (strpos($val, "'") === 0 && substr($val, -1) === "'")) {
                $val = substr($val, 1, -1);
            }
            if (getenv($key) === false) {
                putenv("{$key}={$val}");
            }
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $val;
            }
            if (!isset($_SERVER[$key])) {
                $_SERVER[$key] = $val;
            }
        }
    }
}

// Auto-discover and load .env
$envPaths = [
    __DIR__ . '/.env',
    dirname(__DIR__) . '/.env'
];
foreach ($envPaths as $ep) {
    if (file_exists($ep)) {
        loadEnvFile($ep);
        break;
    }
}

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
if ($isLocalhost && !getenv('DB_NAME')) {
    // Local development settings (XAMPP on Windows without .env override)
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: getenv('DB_DATABASE') ?: 'vision_point_db');
    define('DB_USER', getenv('DB_USER') ?: getenv('DB_USERNAME') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : ''));
} else {
    // Live Production settings (cPanel / visionpoint.edu.np) or .env settings
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_PORT', getenv('DB_PORT') ?: '3306');
    define('DB_NAME', getenv('DB_NAME') ?: 'zptvncco_Sunil');
    define('DB_USER', getenv('DB_USER') ?: 'zptvncco_sah');
    define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Rohit98185@#');
}
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// 3. Environment Mode (Set to true to show detailed error messages during troubleshooting)
define('DEV_MODE', getenv('PHP_DEV') === '1' || getenv('APP_DEBUG') === 'true');

// 4. Admin Security & Session settings
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: 'admin@visionpoint.edu.np');
define('SESSION_LIFETIME', (int)(getenv('SESSION_LIFETIME') ?: 86400 * 7));
