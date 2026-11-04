<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    $isSecure = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

if (!function_exists('h')) {
    function h($s) {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

// Safely load database and site settings if available
if (!isset($pdo)) {
    $dbPath = __DIR__ . '/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

// Load centralized SEO component & check 301 redirects
if (file_exists(__DIR__ . '/seo.php')) {
    require_once __DIR__ . '/seo.php';
}
if (function_exists('checkAndPerformRedirect')) {
    checkAndPerformRedirect();
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

// Prepare dynamic SEO data for current page
$pageSeo = $pageSeo ?? getSeoData();
$pageSeoType = $pageSeoType ?? ($pageSeo['schema_type'] ?? 'EducationalOrganization');
$pageSeoExtra = $pageSeoExtra ?? [];

$isAdmin = isset($_SESSION['admin_id']);
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
$docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
$basePath = '/';
if ($docRoot !== '' && strpos($projectRoot, $docRoot) === 0) {
    $basePath = substr($projectRoot, strlen($docRoot));
    if ($basePath === false) {
        $basePath = '/';
    } else {
        $basePath = '/' . trim($basePath, '/') . '/';
    }
}
if ($basePath === '//') {
    $basePath = '/';
}

// Build dynamic navigation menu
$topMenu = [];
if (isset($pdo) && $pdo) {
    try {
        // Auto-ensure Reviews page exists in navigation_menu table
        $hasReviewsNav = $pdo->query("SELECT COUNT(*) FROM navigation_menu WHERE url = 'reviews.php'")->fetchColumn();
        if ($hasReviewsNav == 0) {
            $pdo->exec("INSERT INTO navigation_menu (label, url, display_order, is_visible) VALUES ('Reviews', 'reviews.php', 8, 1)");
        }

        $stmt = $pdo->query('SELECT * FROM navigation_menu WHERE is_visible = 1 ORDER BY display_order ASC');
        $menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $childrenMenu = [];
        foreach ($menuItems as $item) {
            if ($item['parent_id'] === null) {
                $topMenu[$item['id']] = $item;
                $topMenu[$item['id']]['children'] = [];
            } else {
                $childrenMenu[] = $item;
            }
        }
        foreach ($childrenMenu as $item) {
            if (isset($topMenu[$item['parent_id']])) {
                $topMenu[$item['parent_id']]['children'][] = $item;
            }
        }
    } catch (PDOException $e) {
        // Fallback
    }
}

// Complete fallback menu items if topMenu is empty
if (empty($topMenu)) {
    $topMenu = [
        1 => ['id' => 1, 'label' => 'Home', 'url' => 'index.php', 'children' => []],
        2 => ['id' => 2, 'label' => 'About', 'url' => 'about.php', 'children' => []],
        3 => ['id' => 3, 'label' => 'Study in India', 'url' => 'study-in-india.php', 'children' => []],
        4 => [
            'id' => 4, 
            'label' => 'Study Abroad', 
            'url' => '#', 
            'children' => [
                ['id' => 41, 'label' => 'Study in Australia', 'url' => 'study-in-australia.php'],
                ['id' => 42, 'label' => 'Study in UK', 'url' => 'study-in-uk.php'],
                ['id' => 43, 'label' => 'Study in USA', 'url' => 'study-in-usa.php'],
                ['id' => 44, 'label' => 'Study in Canada', 'url' => 'study-in-canada.php'],
                ['id' => 45, 'label' => 'All Destinations', 'url' => 'study-abroad.php']
            ]
        ],
        5 => ['id' => 5, 'label' => 'Institutions', 'url' => 'cities.php', 'children' => []],
        6 => ['id' => 6, 'label' => 'Courses', 'url' => 'courses.php', 'children' => []],
        7 => [
            'id' => 7, 
            'label' => 'Test Prep', 
            'url' => '#', 
            'children' => [
                ['id' => 71, 'label' => 'IELTS Preparation', 'url' => 'ielts.php'],
                ['id' => 72, 'label' => 'PTE Preparation', 'url' => 'pte.php'],
                ['id' => 73, 'label' => 'CEE Preparation', 'url' => 'cee-preparation.php'],
                ['id' => 74, 'label' => 'CTEVT Preparation', 'url' => 'ctevt-preparation.php'],
                ['id' => 75, 'label' => 'All Test Preps', 'url' => 'test-preparation.php']
            ]
        ],
        8 => ['id' => 8, 'label' => 'Scholarships', 'url' => 'scholarships.php', 'children' => []],
        9 => ['id' => 9, 'label' => 'Blog', 'url' => 'guides.php', 'children' => []],
        10 => ['id' => 10, 'label' => 'Contact', 'url' => 'contact.php', 'children' => []]
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Dynamic Technical & On-Page SEO Meta Tags -->
    <?= generateMetaTags($pageSeo) ?>
    
    <!-- Dynamic Social Graph Metadata -->
    <?= generateOpenGraphTags($pageSeo) ?>
    <?= generateTwitterCardTags($pageSeo) ?>
    
    <!-- Schema.org JSON-LD Structured Data -->
    <?= generateSchemaMarkup($pageSeo, $pageSeoType, $pageSeoExtra) ?>

    <!-- Dynamic Favicon & Browser Tab Icon -->
    <?php
    $faviconPath = !empty($siteSettings['site_favicon']) ? $siteSettings['site_favicon'] : 'assets/images/img4.png';
    $faviconUrl = (strpos($faviconPath, 'http') === 0) ? $faviconPath : $basePath . ltrim($faviconPath, '/');
    $localFaviconFile = __DIR__ . '/../' . ltrim($faviconPath, '/');
    if (!file_exists($localFaviconFile)) {
        $faviconPath = 'assets/images/img4.png';
        $faviconUrl = $basePath . $faviconPath;
        $localFaviconFile = __DIR__ . '/../' . $faviconPath;
    }
    if (file_exists($localFaviconFile)) {
        $faviconUrl .= '?v=' . filemtime($localFaviconFile);
    }
    ?>
    <link rel="icon" type="image/png" href="<?= h($faviconUrl) ?>">
    <link rel="shortcut icon" type="image/png" href="<?= h($faviconUrl) ?>">
    <link rel="apple-touch-icon" href="<?= h($faviconUrl) ?>">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Custom Style CSS with Cache Busting -->
    <?php
    $cssVersion = '1.0';
    $cssFile = $projectRoot . '/assets/css/style.css';
    if (file_exists($cssFile)) {
        $cssVersion = filemtime($cssFile);
    }
    ?>
    <link rel="stylesheet" href="<?= h($basePath) ?>assets/css/style.css?v=<?= $cssVersion ?>">

    <!-- Google Analytics 4 (GA4) Integration if configured -->
    <?php 
    $gaId = $siteSettings['ga_measurement_id'] ?? getenv('GA_MEASUREMENT_ID');
    if (!empty($gaId)): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= h($gaId) ?>"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());
          gtag('config', '<?= h($gaId) ?>');
        </script>
    <?php endif; ?>

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
    </style>
</head>
<body>

<?php if (isset($siteSettings['notification_enabled']) && $siteSettings['notification_enabled'] === '1' && !empty($siteSettings['notification_message'])): ?>
    <div class="site-notification-banner py-2 px-3 text-white text-center fw-semibold shadow-sm d-flex align-items-center justify-content-between" style="background-color: <?= h($siteSettings['notification_banner_color'] ?? '#D91F23') ?>; font-size: 14px; position: relative; z-index: 1040;">
        <div class="container d-flex align-items-center justify-content-center gap-2 flex-wrap">
            <i class="fa-solid fa-bullhorn me-1"></i>
            <span><strong><?= h($siteSettings['notification_title'] ?? 'Notice:') ?></strong> <?= h($siteSettings['notification_message']) ?></span>
            <?php if (!empty($siteSettings['notification_link'])): ?>
                <a href="<?= h($siteSettings['notification_link']) ?>" class="btn btn-light btn-sm rounded-pill py-0 px-3 fw-bold text-dark ms-2" style="font-size: 12px;">Learn More</a>
            <?php else: ?>
                <button type="button" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" class="btn btn-light btn-sm rounded-pill py-0 px-3 fw-bold text-dark ms-2" style="font-size: 12px;">Apply Now</button>
            <?php endif; ?>
        </div>
        <button type="button" class="btn-close btn-close-white ms-2" onclick="this.parentElement.style.display='none';" aria-label="Close"></button>
    </div>
<?php endif; ?>

<header class="susankya-site-header">

    <nav class="navbar navbar-expand-lg fixed-top border-bottom py-3">
        <div class="container-fluid px-3 px-xl-5">
            <!-- Mobile Menu Toggler (Left, order-1) -->
            <button class="navbar-toggler d-lg-none order-1" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Brand Logo (Center on mobile, Left on desktop, order-2 / order-lg-1) -->
            <a class="navbar-brand d-flex align-items-center order-2 order-lg-1 mx-auto mx-lg-0 me-lg-3 me-xl-4 text-nowrap" href="<?= h($basePath) ?>index.php">
                <?php
                $logoPath = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo1.png';
                $logoUrl = (strpos($logoPath, 'http') === 0) ? $logoPath : $basePath . $logoPath;
                $localLogoFile = __DIR__ . '/../' . ltrim($logoPath, '/');
                if (file_exists($localLogoFile)) {
                    $logoUrl .= '?v=' . filemtime($localLogoFile);
                }
                if (file_exists($localLogoFile) || (strpos($logoPath, 'http') === 0)): ?>
                    <img src="<?= h($logoUrl) ?>" alt="Vision Point Logo" class="me-2 rounded shadow-sm" style="max-height: 44px; width: auto; object-fit: contain;">
                <?php else: ?>
                    <i class="fa-solid fa-graduation-cap me-2 text-primary"></i>
                <?php endif; ?>
                <span class="logo-text fw-bold text-primary fs-6 fs-xxl-5 d-none d-sm-inline-block text-nowrap"><?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?></span>
            </a>
            
            <!-- Mobile Enroll Now Button (Right, order-3) -->
            <button class="btn btn-primary fw-semibold px-3 rounded-pill btn-sm text-white mobile-header-enroll-btn d-lg-none order-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Enroll Now</button>

            <!-- Navbar Collapse Menu (order-4 / order-lg-2) -->
            <div class="collapse navbar-collapse order-4 order-lg-2" id="navbarScroll">
                <ul class="navbar-nav ms-auto me-auto mb-2 mb-lg-0 justify-content-center">
                    <?php foreach ($topMenu as $item): 
                        $isCurrent = false;
                        $hasChildren = !empty($item['children']);
                        
                        $currentFilename = basename($_SERVER['PHP_SELF']);
                        if ($currentFilename === $item['url']) {
                            $isCurrent = true;
                        } elseif ($item['url'] === '#' && $hasChildren) {
                            foreach ($item['children'] as $child) {
                                if (strpos($child['url'], '?') !== false) {
                                    $parts = explode('?', $child['url']);
                                    if ($currentFilename === $parts[0]) {
                                        $isCurrent = true;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if ($hasChildren): ?>
                            <li class="nav-item dropdown px-1 px-xxl-2">
                                <a class="nav-link dropdown-toggle fw-semibold text-nowrap <?= $isCurrent ? 'active text-primary' : 'text-dark' ?>" href="#" id="navbarDropdown_<?= $item['id'] ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 14px;">
                                    <?= h($item['label']) ?>
                                </a>
                                <ul class="dropdown-menu border-0 shadow-sm" aria-labelledby="navbarDropdown_<?= $item['id'] ?>">
                                    <?php foreach ($item['children'] as $child): 
                                        $childUrl = (strpos($child['url'], 'http') === 0 || strpos($child['url'], '#') === 0) ? $child['url'] : $basePath . $child['url'];
                                    ?>
                                        <li><a class="dropdown-item" href="<?= h($childUrl) ?>"><?= h($child['label']) ?></a></li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else: 
                            $itemUrl = (strpos($item['url'], 'http') === 0 || strpos($item['url'], '#') === 0) ? $item['url'] : $basePath . $item['url'];
                        ?>
                            <li class="nav-item px-1 px-xxl-2">
                                <a class="nav-link fw-semibold text-nowrap <?= ($currentFilename === $item['url']) ? 'active text-primary' : 'text-dark' ?>" href="<?= h($itemUrl) ?>" style="font-size: 14px;"><?= h($item['label']) ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <div class="action-group d-flex align-items-center gap-2 flex-shrink-0 ms-lg-2">
                    <button class="btn btn-outline-warning text-dark fw-bold px-3 px-xl-3 rounded-pill btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#submitReviewModal"><i class="fa-solid fa-star text-warning me-1"></i>Write Review</button>
                    <button class="btn btn-outline-secondary fw-semibold px-3 px-xl-4 rounded-pill btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Scholarships</button>
                    <button class="btn btn-primary fw-semibold px-3 px-xl-4 rounded-pill btn-sm text-white text-nowrap" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Enroll Now</button>
                    
                    <?php if ($isAdmin): ?>
                        <a href="<?= h($basePath) ?>jaiswal/dashboard.php" class="btn btn-success fw-semibold px-3 rounded-pill btn-sm text-white text-nowrap"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a>
                        <a href="<?= h($basePath) ?>logout.php" class="btn btn-outline-danger fw-semibold px-3 rounded-pill btn-sm text-nowrap"><i class="fa-solid fa-sign-out me-1"></i>Logout</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<div class="site-header-spacer"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var navbar = document.querySelector('.navbar.fixed-top');
    if (navbar) {
        function checkScroll() {
            if (window.scrollY > 20) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
            }
        }
        window.addEventListener('scroll', checkScroll);
        checkScroll();
    }
});
</script>
