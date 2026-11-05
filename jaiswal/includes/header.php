<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!function_exists('h')) {
    function h($s) {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

// Safely load database and site settings if available
if (!isset($pdo)) {
    require_once __DIR__ . '/../../includes/db.php';
}

$siteSettings = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query('SELECT `key`, `value` FROM site_settings');
        $siteSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        // Fallback
    }
}

$isAdmin = isset($_SESSION['admin_id']);
if (!$isAdmin) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Control Panel | <?= h($siteSettings['site_title'] ?? 'Vision Point Educational Service') ?></title>
    
    <!-- Dynamic Favicon & Browser Tab Icon -->
    <?php
    $faviconPath = !empty($siteSettings['site_favicon']) ? $siteSettings['site_favicon'] : (!empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo1.png');
    $faviconUrl = (strpos($faviconPath, 'http') === 0) ? $faviconPath : '../' . ltrim($faviconPath, '/');
    $localFaviconFile = __DIR__ . '/../../' . ltrim($faviconPath, '/');
    if (file_exists($localFaviconFile)) {
        $faviconUrl .= '?v=' . filemtime($localFaviconFile);
    }
    ?>
    <link rel="icon" type="image/png" href="<?= h($faviconUrl) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= h($faviconUrl) ?>">
    <link rel="apple-touch-icon" href="<?= h($faviconUrl) ?>">

    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

    <!-- Dynamic Theme CSS Overrides -->
    <style>
        :root {
            <?php if (!empty($siteSettings['primary_color'])): ?>
            --primary-color: <?= $siteSettings['primary_color'] ?> !important;
            <?php endif; ?>
            <?php if (!empty($siteSettings['primary_light'])): ?>
            --primary-light: <?= $siteSettings['primary_light'] ?> !important;
            <?php endif; ?>
            <?php if (!empty($siteSettings['secondary_color'])): ?>
            --secondary-color: <?= $siteSettings['secondary_color'] ?> !important;
            <?php endif; ?>
            <?php if (!empty($siteSettings['secondary_hover'])): ?>
            --secondary-hover: <?= $siteSettings['secondary_hover'] ?> !important;
            <?php endif; ?>
        }
        body {
            background-color: var(--bg-light);
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }
        
        /* Specialized Admin Top Navigation Bar */
        .admin-topbar {
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 15px 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 100;
        }
    </style>
</head>
<body>

<div class="dashboard-wrap">
    <!-- Collapsible Premium Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header text-center">
            <?php
            $logoPath = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo1.png';
            $logoUrl = (strpos($logoPath, 'http') === 0) ? $logoPath : '../' . $logoPath;
            if (file_exists(__DIR__ . '/../../' . $logoPath) || (strpos($logoPath, 'http') === 0)): ?>
                <img src="<?= h($logoUrl) ?>" alt="Vision Point Logo" class="mb-2 rounded shadow-sm" style="max-height: 48px; max-width: 100%; object-fit: contain;">
            <?php else: ?>
                <i class="fa-solid fa-gauge me-2 fs-4 text-white"></i>
            <?php endif; ?>
            <h5 class="text-white fw-bold mb-1 mt-1" style="font-size: 1.1rem; letter-spacing: 0.5px;"><?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?></h5>
            <p class="text-white-50 small mb-0" style="font-size: 0.75rem; word-break: break-all;"><i class="fa-solid fa-user-shield me-1"></i><?= htmlspecialchars($_SESSION['admin_email']) ?></p>
        </div>
        <nav class="sidebar-menu flex-grow-1">
            <a class="active" href="dashboard.php"><i class="fa-solid fa-house"></i>Dashboard</a>
            <a href="dashboard.php?tab=seo-pages"><i class="fa-solid fa-magnifying-glass text-warning"></i>SEO Manager</a>
            <a href="dashboard.php?tab=seo-audit"><i class="fa-solid fa-stethoscope text-success"></i>SEO Audit</a>
            <a href="dashboard.php?tab=seo-redirects"><i class="fa-solid fa-arrow-turn-down text-info"></i>301 Redirects</a>
            <a href="../index.php" target="_blank"><i class="fa-solid fa-globe"></i>View Live Site</a>
            <a href="../sitemap.xml" target="_blank"><i class="fa-solid fa-sitemap text-warning"></i>View Sitemap</a>
            <a href="../robots.txt" target="_blank"><i class="fa-solid fa-robot text-danger"></i>View Robots.txt</a>
            <a href="export_csv.php?type=enrollments" target="_blank"><i class="fa-solid fa-file-csv"></i>Export Enrollments</a>
            <a href="export_csv.php?type=inquiries" target="_blank"><i class="fa-solid fa-file-csv"></i>Export Inquiries</a>
            <a href="export_csv.php?type=subscribers" target="_blank"><i class="fa-solid fa-file-csv"></i>Export Subscribers</a>
        </nav>
        <div class="mt-auto pt-3 border-top border-secondary">
            <a href="../logout.php" class="btn btn-outline-danger w-100 rounded-pill py-2"><i class="fa-solid fa-right-from-bracket me-2"></i>Sign Out</a>
        </div>
    </aside>

    <!-- Main Workspace -->
    <main class="dash-content p-0">
        <!-- Admin Topbar -->
        <div class="admin-topbar d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill"><i class="fa-solid fa-shield-halved me-1"></i> ADMIN CONSOLE</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small"><i class="fa-solid fa-circle-user me-1 text-primary"></i>Logged in as: <strong><?= htmlspecialchars($_SESSION['admin_email']) ?></strong></span>
                <a href="../logout.php" class="btn btn-sm btn-outline-danger rounded-pill px-3"><i class="fa-solid fa-power-off me-1"></i>Logout</a>
            </div>
        </div>
        
        <!-- Dashboard Inner Content -->
        <div class="p-4 p-md-5">
