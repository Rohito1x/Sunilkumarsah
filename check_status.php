<?php
/**
 * Vision Point Educational Service - Server Diagnostic & Health Check Tool
 * Access via: https://visionpoint.edu.np/check_status.php
 */
if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Security Guard: Restrict diagnostic tools to authenticated administrators or local development
if (!isset($_SESSION['admin_id']) && (!defined('DEV_MODE') || !DEV_MODE) && php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('403 Forbidden: Administrator authentication is required to access server diagnostics.');
}

$checks = [];

// 1. PHP Version
$phpVersion = PHP_VERSION;
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
$checks[] = [
    'title' => 'PHP Version',
    'status' => $phpOk ? 'pass' : 'fail',
    'message' => "Current version: {$phpVersion} " . ($phpOk ? '(Supported)' : '(Requires PHP 7.4 or higher)')
];

// 2. Required PHP Extensions
$reqExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'json', 'session', 'fileinfo'];
$missingExts = [];
foreach ($reqExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExts[] = $ext;
    }
}
$checks[] = [
    'title' => 'Required PHP Extensions',
    'status' => empty($missingExts) ? 'pass' : 'fail',
    'message' => empty($missingExts) ? 'All essential extensions loaded (PDO, MySQL, JSON, Session, Mbstring, Fileinfo)' : 'Missing extensions: ' . implode(', ', $missingExts)
];

// 3. Configuration & Database Connection
$configExists = file_exists(__DIR__ . '/config.php');
$checks[] = [
    'title' => 'Configuration File (config.php)',
    'status' => $configExists ? 'pass' : 'warn',
    'message' => $configExists ? 'config.php detected' : 'config.php not found; using defaults'
];

if ($configExists) {
    require_once __DIR__ . '/config.php';
}

require_once __DIR__ . '/includes/db.php';

$dbStatus = 'fail';
$dbMessage = '';
$tableCounts = [];

if (isset($pdo) && $pdo) {
    $dbStatus = 'pass';
    $dbMessage = "Successfully connected to database `" . (defined('DB_NAME') ? DB_NAME : 'zptvncco_Visionpoint') . "` on `" . (defined('DB_HOST') ? DB_HOST : 'localhost') . "`";
    
    // Check tables
    $expectedTables = ['admin_users', 'institutions', 'course_streams', 'destinations', 'inquiries', 'enrollments', 'reviews', 'site_settings', 'navigation_menu', 'guides', 'pages', 'bank_accounts', 'team_members'];
    foreach ($expectedTables as $tbl) {
        try {
            $cnt = $pdo->query("SELECT COUNT(*) FROM `$tbl`")->fetchColumn();
            $tableCounts[$tbl] = (int)$cnt;
        } catch (\Throwable $e) {
            $tableCounts[$tbl] = 'Missing / Error';
        }
    }
} else {
    $dbStatus = 'fail';
    $dbMessage = "Connection failed: " . ($dbError ?? 'Check database host, name, username, and password in config.php');
}

$checks[] = [
    'title' => 'Database Connection',
    'status' => $dbStatus,
    'message' => $dbMessage
];

// 4. Uploads Directory Write Permission
$uploadsDir = __DIR__ . '/uploads/';
$uploadsWritable = is_dir($uploadsDir) && is_writable($uploadsDir);
if (!is_dir($uploadsDir)) {
    @mkdir($uploadsDir, 0755, true);
    $uploadsWritable = is_dir($uploadsDir) && is_writable($uploadsDir);
}
$checks[] = [
    'title' => 'Uploads Directory Permission',
    'status' => $uploadsWritable ? 'pass' : 'warn',
    'message' => $uploadsWritable ? 'Uploads directory is writable (0755)' : 'Uploads directory is not writable. Please set chmod 755 on uploads/'
];

// 5. HTTPS / SSL
$isHttps = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
$checks[] = [
    'title' => 'SSL / HTTPS Connection',
    'status' => $isHttps ? 'pass' : 'info',
    'message' => $isHttps ? 'Secure HTTPS connection active' : 'Running on HTTP (Recommended to enforce HTTPS in cPanel for production)'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Diagnostics | Vision Point Educational Service</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #1e293b; }
        .diag-card { border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.04); background: #ffffff; }
        .badge-pass { background-color: #10b981; color: #ffffff; }
        .badge-fail { background-color: #ef4444; color: #ffffff; }
        .badge-warn { background-color: #f59e0b; color: #ffffff; }
        .badge-info { background-color: #3b82f6; color: #ffffff; }
    </style>
</head>
<body class="py-5">
    <div class="container" style="max-width: 860px;">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary mb-1">
                <i class="fa-solid fa-heart-pulse text-danger me-2"></i>System Diagnostic & Status
            </h2>
            <p class="text-muted small">Vision Point Educational Service Server Health & Database Verifier</p>
        </div>

        <div class="diag-card p-4 p-md-5 mb-4">
            <h5 class="fw-bold mb-4 pb-2 border-bottom text-dark">
                <i class="fa-solid fa-list-check text-primary me-2"></i>Environment Health Checks
            </h5>

            <div class="d-flex flex-column gap-3 mb-4">
                <?php foreach ($checks as $chk): ?>
                    <div class="d-flex align-items-start justify-content-between p-3 rounded-3 bg-light border border-light-subtle">
                        <div>
                            <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($chk['title']) ?></div>
                            <div class="text-muted small"><?= htmlspecialchars($chk['message']) ?></div>
                        </div>
                        <span class="badge badge-<?= $chk['status'] ?> rounded-pill px-3 py-2 text-uppercase fw-bold" style="font-size: 11px;">
                            <?= strtoupper($chk['status']) ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($tableCounts)): ?>
                <h5 class="fw-bold mt-4 mb-3 pb-2 border-bottom text-dark">
                    <i class="fa-solid fa-database text-primary me-2"></i>Database Tables Summary
                </h5>
                <div class="row g-2">
                    <?php foreach ($tableCounts as $table => $count): ?>
                        <div class="col-6 col-md-4">
                            <div class="p-2 px-3 rounded-3 bg-white border border-light-subtle d-flex justify-content-between align-items-center">
                                <span class="small fw-semibold text-secondary font-monospace"><?= htmlspecialchars($table) ?></span>
                                <span class="badge <?= is_numeric($count) ? 'bg-primary' : 'bg-danger' ?> rounded-pill"><?= htmlspecialchars((string)$count) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Troubleshooting Guide -->
        <div class="diag-card p-4 p-md-5">
            <h5 class="fw-bold mb-3 text-dark">
                <i class="fa-solid fa-wrench text-warning me-2"></i>How to resolve HTTP 500 or Database Errors
            </h5>
            <ol class="small text-muted mb-4 ps-3" style="line-height: 1.8;">
                <li>Open <code>config.php</code> in your file manager or code editor.</li>
                <li>Ensure <strong>DB_HOST</strong>, <strong>DB_NAME</strong>, <strong>DB_USER</strong>, and <strong>DB_PASS</strong> match your cPanel / hosting MySQL credentials.</li>
                <li>Import <code>database.sql</code> into your MySQL database via cPanel phpMyAdmin if tables are missing.</li>
                <li>Ensure the MySQL user has full privileges (ALL PRIVILEGES) granted to the database.</li>
            </ol>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2 border-top">
                <a href="index.php" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-house me-1"></i> Visit Website Homepage
                </a>
                <a href="setup.php" class="btn btn-outline-secondary btn-sm rounded-pill px-4 fw-bold">
                    <i class="fa-solid fa-database me-1"></i> Run Database Setup
                </a>
            </div>
        </div>
    </div>
</body>
</html>
