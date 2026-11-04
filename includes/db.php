<?php
// Database connection (PDO) & Security Configuration

// Global Security Headers
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), camera=(), microphone=()');
    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === '1')) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Load configuration if available
$configPaths = [
    __DIR__ . '/../config.php',
    __DIR__ . '/../../config.php',
    __DIR__ . '/config.php'
];
foreach ($configPaths as $cp) {
    if (file_exists($cp)) {
        require_once $cp;
        break;
    }
}

// Development / Production Environment Error Handling
$devMode = defined('DEV_MODE') ? DEV_MODE : (getenv('PHP_DEV') === '1' || getenv('PHP_DEV') === 'true');
if ($devMode) {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    error_reporting(E_ALL);
}

$host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
$port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: '3306');
$dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'sajilo_db');
$username = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'root');
$password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
$charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset={$charset}";
$pdo = null;
$dbError = null;

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5
    ]);

    // Auto-repair missing columns on tables if DB is connected
    $columnsToEnsure = [
        'enrollments' => [
            'interested_country' => "VARCHAR(80) DEFAULT NULL"
        ],
        'institutions' => [
            'about' => "TEXT DEFAULT NULL",
            'fees' => "TEXT DEFAULT NULL",
            'admission_process' => "TEXT DEFAULT NULL",
            'location_detail' => "TEXT DEFAULT NULL",
            'courses' => "TEXT DEFAULT NULL",
            'map_link' => "TEXT DEFAULT NULL",
            'is_top' => "TINYINT DEFAULT 0",
            'fee_document' => "VARCHAR(255) DEFAULT NULL",
            'status' => "VARCHAR(20) DEFAULT 'Active'",
            'display_order' => "INT DEFAULT 0"
        ],
        'destinations' => [
            'about' => "TEXT DEFAULT NULL",
            'gallery' => "TEXT DEFAULT NULL",
            'map_link' => "TEXT DEFAULT NULL",
            'display_order' => "INT DEFAULT 0"
        ],
        'course_streams' => [
            'desc' => "TEXT DEFAULT NULL",
            'img' => "VARCHAR(255) DEFAULT NULL",
            'icon' => "VARCHAR(50) DEFAULT 'fa-graduation-cap'",
            'display_order' => "INT DEFAULT 0"
        ],
        'guides' => [
            'author' => "VARCHAR(100) DEFAULT 'Academic Counselor'",
            'meta_title' => "VARCHAR(255) DEFAULT NULL",
            'meta_description' => "TEXT DEFAULT NULL",
            'canonical_url' => "VARCHAR(500) DEFAULT NULL",
            'og_image' => "VARCHAR(500) DEFAULT NULL",
            'category' => "VARCHAR(100) DEFAULT 'Admission Guidance'",
            'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
        ],
        'seo_pages' => [
            'meta_keywords' => "TEXT DEFAULT NULL",
            'twitter_card' => "VARCHAR(50) DEFAULT 'summary_large_image'"
        ]
    ];

    foreach ($columnsToEnsure as $table => $cols) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
            $existingCols = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($cols as $colName => $colDef) {
                if (!in_array($colName, $existingCols, true)) {
                    $pdo->exec("ALTER TABLE `$table` ADD COLUMN `$colName` $colDef");
                }
            }
        } catch (PDOException $e) {
            // Table might not exist yet; safe to ignore
        }
    }

    // Auto-ensure login_attempts table exists for rate-limiting
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `login_attempts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(150) DEFAULT NULL,
            `ip_address` VARCHAR(45) NOT NULL,
            `attempt_count` INT DEFAULT 1,
            `first_attempt_at` DATETIME NOT NULL,
            `last_attempt_at` DATETIME NOT NULL,
            `blocked_until` DATETIME DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_ip_email` (`ip_address`, `email`),
            INDEX `idx_blocked` (`blocked_until`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Auto-ensure seo_pages table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `seo_pages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `page_type` VARCHAR(100) DEFAULT 'static',
            `page_id` INT NULL,
            `slug` VARCHAR(255) NOT NULL UNIQUE,
            `page_name` VARCHAR(255) NOT NULL,
            `meta_title` VARCHAR(255) NOT NULL,
            `meta_description` TEXT NOT NULL,
            `h1` VARCHAR(255) DEFAULT NULL,
            `canonical_url` VARCHAR(500) DEFAULT NULL,
            `og_title` VARCHAR(255) DEFAULT NULL,
            `og_description` TEXT DEFAULT NULL,
            `og_image` VARCHAR(500) DEFAULT NULL,
            `robots` VARCHAR(100) DEFAULT 'index,follow',
            `schema_type` VARCHAR(100) DEFAULT 'EducationalOrganization',
            `status` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_slug` (`slug`),
            INDEX `idx_type_id` (`page_type`, `page_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Auto-ensure seo_redirects table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `seo_redirects` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `source_url` VARCHAR(255) NOT NULL UNIQUE,
            `target_url` VARCHAR(255) NOT NULL,
            `status_code` INT DEFAULT 301,
            `hit_count` INT DEFAULT 0,
            `is_active` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX `idx_source` (`source_url`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed initial default SEO pages if empty
        $seoCount = (int)$pdo->query("SELECT COUNT(*) FROM `seo_pages`")->fetchColumn();
        if ($seoCount === 0) {
            $defaultSeoPages = [
                [
                    'static', null, 'home', 'Homepage',
                    'Krishna Education Foundation – Education Consultancy in Nepal',
                    'Krishna Education Foundation is a trusted education consultancy in Nepal helping students with admissions, scholarships, and visa guidance for India and abroad.',
                    'Krishna Education Foundation – Education Consultancy in Nepal',
                    'https://krishnaeducationfoundation.edu.np/',
                    'Krishna Education Foundation – Education Consultancy in Nepal',
                    'Empowering Nepalese students with expert counseling, accredited university admissions, and scholarship assistance.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'about', 'About Us',
                    'About Us | Krishna Education Foundation Biratnagar Nepal',
                    'Learn about Krishna Education Foundation, our certified education counselors, mission, accreditation, and commitment to ethical student admissions.',
                    'About Krishna Education Foundation',
                    'https://krishnaeducationfoundation.edu.np/about/',
                    'About Us | Krishna Education Foundation',
                    'Discover our journey, dedicated counselors, and trusted academic advisory services for Nepalese students.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'services', 'Our Services',
                    'Education Consultancy Services in Nepal | Krishna Education Foundation',
                    'Explore our educational services including university selection, career counseling, documentation processing, scholarship guidance, and visa support.',
                    'Our Comprehensive Education Services',
                    'https://krishnaeducationfoundation.edu.np/services/',
                    'Education Services in Nepal | Krishna Education Foundation',
                    'Full-cycle educational advisory from course selection to arrival and scholarship support.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-india', 'Study in India',
                    'Study in India from Nepal | Krishna Education Foundation',
                    'Complete guide for Nepali students to study in India. Discover top Indian universities, engineering, nursing, medical, and pharmacy courses with scholarships.',
                    'Study in India for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/study-in-india/',
                    'Study in India from Nepal | Krishna Education Foundation',
                    'Explore top universities, courses, transparent fees, and scholarship opportunities in India.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-australia', 'Study in Australia',
                    'Study in Australia from Nepal | Courses, Universities & Visa Guidance',
                    'Expert guidance for Nepali students applying to top Australian universities. Learn about Genuine Student (GS) criteria, CRICOS courses, fees, and post-study work visas.',
                    'Study in Australia for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/study-in-australia/',
                    'Study in Australia from Nepal | Krishna Education Foundation',
                    'Step-by-step guidance on Australian university admissions, GS requirements, and student visas.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-uk', 'Study in UK',
                    'Study in UK from Nepal | Top Universities, CAS & Graduate Visa',
                    'Discover studying in the UK for Nepalese students. Access Russell Group universities, CAS guidance, IELTS waiver options, and 2-year post-study Graduate Route visas.',
                    'Study in the UK for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/study-in-uk/',
                    'Study in UK from Nepal | Krishna Education Foundation',
                    'Complete assistance with UK university admissions, CAS letters, and Tier 4 student visa applications.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-usa', 'Study in USA',
                    'Study in USA from Nepal | I-20, Universities & F-1 Visa Guidance',
                    'Pursue higher education in the USA. Get expert support for US university applications, I-20 issuance, SEVIS documentation, and F-1 visa interview preparation.',
                    'Study in the USA for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/study-in-usa/',
                    'Study in USA from Nepal | Krishna Education Foundation',
                    'Guidance for Nepalese students on US admissions, STEM OPT extensions, and F-1 student visas.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-canada', 'Study in Canada',
                    'Study in Canada from Nepal | DLI Colleges, SDS & PGWP Guide',
                    'Comprehensive guide for Nepali students to study in Canada at Designated Learning Institutions (DLI). Learn about SDS stream, tuition costs, and PGWP work permits.',
                    'Study in Canada for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/study-in-canada/',
                    'Study in Canada from Nepal | Krishna Education Foundation',
                    'Apply to Canadian universities and colleges with full assistance on study permits and SDS filing.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'universities', 'Universities',
                    'Top Indian & International Universities for Nepali Students',
                    'Explore accredited universities in India and abroad partnering with Krishna Education Foundation. Compare programs, locations, and admission criteria.',
                    'Partner Universities & Educational Institutions',
                    'https://krishnaeducationfoundation.edu.np/universities/',
                    'Partner Universities | Krishna Education Foundation',
                    'Browse UGC, AICTE, and INC approved institutions for higher education.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'courses', 'Courses',
                    'Popular Courses & Degrees for Nepali Students | KEF Nepal',
                    'Explore top courses including B.Sc Nursing, B.Tech Engineering, B.Pharm, BBA, BCA, BDS, and MBBS with recognized accreditations and scholarship opportunities.',
                    'Popular Academic Courses & Degree Streams',
                    'https://krishnaeducationfoundation.edu.np/courses/',
                    'Courses & Programs | Krishna Education Foundation',
                    'Find the right undergraduate and postgraduate programs matching your career goals.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'scholarships', 'Scholarships',
                    'Scholarships for Nepali Students in India & Abroad | KEF',
                    'Discover available merit scholarships, institutional fee waivers, and government schemes for Nepali students studying in India, UK, USA, Canada, and Australia.',
                    'Scholarship Opportunities for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/scholarships/',
                    'Scholarship Guidance | Krishna Education Foundation',
                    'Apply for up to 50%-100% tuition scholarships in top universities.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'visa-guidance', 'Visa Guidance',
                    'Student Visa Guidance & Documentation | Krishna Education Foundation',
                    'Professional visa counseling, document attestation, financial verification, and interview preparation for Nepali students traveling abroad.',
                    'Student Visa & Documentation Guidance',
                    'https://krishnaeducationfoundation.edu.np/visa-guidance/',
                    'Visa Guidance | Krishna Education Foundation',
                    'Step-by-step guidance on genuine documentation, biometrics, and visa filing.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'ielts', 'IELTS Preparation',
                    'IELTS Preparation in Nepal | Format, Scoring & Test Practice',
                    'Comprehensive IELTS Academic & General preparation guide. Master Listening, Reading, Writing, and Speaking modules with certified instructor tips.',
                    'IELTS Preparation Guide for Nepali Students',
                    'https://krishnaeducationfoundation.edu.np/ielts/',
                    'IELTS Preparation | Krishna Education Foundation',
                    'Master your IELTS exam with structured practice, band score tips, and mock tests.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'pte', 'PTE Preparation',
                    'PTE Academic Preparation in Nepal | AI Scoring & Test Format',
                    'PTE Academic preparation guide for Nepalese students. Learn scoring strategies, section breakdown, and tips for fast score reporting to international universities.',
                    'PTE Academic Preparation & Scoring Guide',
                    'https://krishnaeducationfoundation.edu.np/pte/',
                    'PTE Academic Preparation | Krishna Education Foundation',
                    'Achieve your target PTE score with computer-based practice strategies.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'cee-preparation', 'CEE Preparation',
                    'CEE Preparation in Nepal | Medical & Dental Entrance Guidance',
                    'Complete guidance on Nepal Common Entrance Examination (CEE) for MBBS, BDS, B.Sc Nursing, and B.Pharm admissions under Medical Education Commission (MEC).',
                    'CEE Entrance Preparation for Medical & Health Sciences',
                    'https://krishnaeducationfoundation.edu.np/cee-preparation/',
                    'CEE Preparation Guide | Krishna Education Foundation',
                    'Prepare effectively for medical and allied health entrance examinations in Nepal and India.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'ctevt-preparation', 'CTEVT Preparation',
                    'CTEVT Entrance Preparation in Nepal | Diploma & Nursing Courses',
                    'Guide to CTEVT entrance exams for Staff Nurse, Diploma in Pharmacy, Lab Technology, and Engineering diploma courses in Nepal.',
                    'CTEVT Entrance Preparation Guide',
                    'https://krishnaeducationfoundation.edu.np/ctevt-preparation/',
                    'CTEVT Preparation | Krishna Education Foundation',
                    'Crack CTEVT diploma entrance exams with syllabus breakdown and study guidance.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'career-counselling', 'Career Counselling',
                    'Career Counselling & Academic Mentorship in Nepal | KEF',
                    'Personalized 1-on-1 career counselling for Nepali students after +2, SEE, and graduation. Identify strengths, career trajectories, and optimal academic paths.',
                    'Personalized Career Counselling for Students',
                    'https://krishnaeducationfoundation.edu.np/career-counselling/',
                    'Career Counselling | Krishna Education Foundation',
                    'Make informed education and career decisions with professional counselors.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'blog', 'Blog & Guides',
                    'Educational Blog & Admission Guides | Krishna Education Foundation',
                    'Stay informed with latest articles, admission updates, university rankings, scholarship notifications, and visa guidelines for Nepali students.',
                    'Educational Resource Guides & Articles',
                    'https://krishnaeducationfoundation.edu.np/blog/',
                    'Educational Blog | Krishna Education Foundation',
                    'Expert guides and informative articles on higher education opportunities.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'contact', 'Contact Us',
                    'Contact Krishna Education Foundation | Biratnagar Nepal',
                    'Get in touch with Krishna Education Foundation. Visit our Biratnagar office, call +977 9807765812, or send an online inquiry for free education counseling.',
                    'Contact Krishna Education Foundation',
                    'https://krishnaeducationfoundation.edu.np/contact/',
                    'Contact Us | Krishna Education Foundation',
                    'Reach out to our counseling team for personalized education guidance.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'student-success-stories', 'Student Success Stories',
                    'Student Success Stories & Reviews | Krishna Education Foundation',
                    'Read authentic reviews and success stories from Nepali students enrolled in top Indian and overseas universities through Krishna Education Foundation.',
                    'Student Reviews & Success Stories',
                    'https://krishnaeducationfoundation.edu.np/student-success-stories/',
                    'Student Success Stories | Krishna Education Foundation',
                    'Discover testimonials and verified feedback from our students.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ]
            ];

            $stmtInsertSeo = $pdo->prepare("INSERT INTO `seo_pages` 
                (`page_type`, `page_id`, `slug`, `page_name`, `meta_title`, `meta_description`, `h1`, `canonical_url`, `og_title`, `og_description`, `og_image`, `robots`, `schema_type`, `status`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($defaultSeoPages as $p) {
                $stmtInsertSeo->execute($p);
            }
        }
    } catch (PDOException $e) {
        // Safe to ignore if DB user lacks create table privileges
    }

} catch (PDOException $e) {
    $pdo = null;
    $dbError = $e->getMessage();
    error_log("Database connection failure: " . $dbError);
}

// Helper: Secure Client IP resolution
if (!function_exists('getClientIp')) {
    function getClientIp(): string {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

// Helper: Rate limiting check (F-06)
if (!function_exists('checkRateLimit')) {
    function checkRateLimit(PDO $pdo, string $email): array {
        $ip = getClientIp();
        $emailNorm = strtolower(trim($email));

        try {
            $stmt = $pdo->prepare("SELECT blocked_until, attempt_count FROM login_attempts WHERE (ip_address = :ip OR email = :email) AND blocked_until > NOW() ORDER BY blocked_until DESC LIMIT 1");
            $stmt->execute([':ip' => $ip, ':email' => $emailNorm]);
            $row = $stmt->fetch();

            if ($row) {
                $blockedUntil = strtotime($row['blocked_until']);
                $remaining = max(1, $blockedUntil - time());
                return [
                    'allowed' => false,
                    'remaining' => $remaining,
                    'msg' => "Too many failed login attempts. Please try again in {$remaining} seconds."
                ];
            }
        } catch (PDOException $e) {
            error_log("Rate limit check error: " . $e->getMessage());
        }

        return ['allowed' => true, 'remaining' => 0, 'msg' => ''];
    }
}

// Helper: Record failed login attempt
if (!function_exists('recordFailedAttempt')) {
    function recordFailedAttempt(PDO $pdo, string $email): void {
        $ip = getClientIp();
        $emailNorm = strtolower(trim($email));
        $now = date('Y-m-d H:i:s');

        try {
            $stmt = $pdo->prepare("SELECT id, attempt_count, first_attempt_at FROM login_attempts WHERE ip_address = :ip AND (email = :email OR email IS NULL) AND last_attempt_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE) ORDER BY id DESC LIMIT 1");
            $stmt->execute([':ip' => $ip, ':email' => $emailNorm]);
            $row = $stmt->fetch();

            if ($row) {
                $newCount = (int)$row['attempt_count'] + 1;
                $blockedUntil = null;
                if ($newCount >= 5) {
                    $blockedUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                }
                $update = $pdo->prepare("UPDATE login_attempts SET attempt_count = :count, last_attempt_at = :last, blocked_until = :blocked WHERE id = :id");
                $update->execute([
                    ':count' => $newCount,
                    ':last' => $now,
                    ':blocked' => $blockedUntil,
                    ':id' => $row['id']
                ]);
            } else {
                $ins = $pdo->prepare("INSERT INTO login_attempts (email, ip_address, attempt_count, first_attempt_at, last_attempt_at) VALUES (:email, :ip, 1, :first, :last)");
                $ins->execute([
                    ':email' => $emailNorm,
                    ':ip' => $ip,
                    ':first' => $now,
                    ':last' => $now
                ]);
            }
        } catch (PDOException $e) {
            error_log("Record failed attempt error: " . $e->getMessage());
        }
    }
}

// Helper: Reset rate limiting on successful login
if (!function_exists('resetRateLimit')) {
    function resetRateLimit(PDO $pdo, string $email): void {
        $ip = getClientIp();
        $emailNorm = strtolower(trim($email));
        try {
            $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = :ip OR email = :email");
            $stmt->execute([':ip' => $ip, ':email' => $emailNorm]);
        } catch (PDOException $e) {
            error_log("Reset rate limit error: " . $e->getMessage());
        }
    }
}

// Helper: Format Font Awesome icon class safely with prefix and intelligent fallbacks
if (!function_exists('formatFaIcon')) {
    function formatFaIcon(?string $icon, string $label = '', string $default = 'fa-solid fa-graduation-cap'): string {
        $icon = trim((string)$icon);
        if ($icon === '') {
            $lbl = strtolower($label);
            if (strpos($lbl, 'scholarship') !== false || strpos($lbl, 'success') !== false || strpos($lbl, 'award') !== false) {
                return 'fa-solid fa-award';
            }
            if (strpos($lbl, 'satisfaction') !== false || strpos($lbl, 'rate') !== false || strpos($lbl, 'happy') !== false) {
                return 'fa-solid fa-face-smile';
            }
            if (strpos($lbl, 'recruit') !== false || strpos($lbl, 'student') !== false || strpos($lbl, 'admission') !== false || strpos($lbl, 'year') !== false) {
                return 'fa-solid fa-user-graduate';
            }
            return $default;
        }

        if ($icon === 'fa-smile' || $icon === 'smile') {
            return 'fa-solid fa-face-smile';
        }

        if (preg_match('/\b(fa-solid|fa-regular|fa-brands|fa-light|fa-thin|fa-duotone|fas|far|fab|fal|fat|fad)\b/', $icon)) {
            return $icon;
        }

        if (strpos($icon, 'fa-') === 0) {
            return 'fa-solid ' . $icon;
        }

        return 'fa-solid fa-' . $icon;
    }
}

// Safely auto-load SEO component if present, or define defensive fallbacks to prevent 500 crashes
if (file_exists(__DIR__ . '/seo.php')) {
    require_once __DIR__ . '/seo.php';
} else {
    if (!function_exists('getSeoData')) {
        function getSeoData(?string $pageType = 'static', ?int $pageId = null, ?string $customSlug = null): array {
            $slug = $customSlug ?: basename($_SERVER['PHP_SELF'] ?? 'index.php', '.php');
            return [
                'slug' => $slug,
                'page_name' => ucwords(str_replace(['-', '_'], ' ', $slug)),
                'meta_title' => 'Krishna Education Foundation – Education Consultancy in Nepal',
                'meta_description' => 'Krishna Education Foundation is a trusted education consultancy in Nepal helping students with admissions, scholarships, and visa guidance for India and abroad.',
                'meta_keywords' => 'Education Consultancy in Nepal, Study in India, Study Abroad',
                'h1' => 'Krishna Education Foundation',
                'canonical_url' => 'https://krishnaeducationfoundation.edu.np/' . ($slug !== 'index' && $slug !== 'home' ? $slug . '/' : ''),
                'og_title' => 'Krishna Education Foundation',
                'og_description' => 'Expert education counseling and university admissions.',
                'og_image' => 'https://krishnaeducationfoundation.edu.np/assets/images/logo1.png',
                'og_type' => 'website',
                'robots' => 'index,follow',
                'schema_type' => 'EducationalOrganization',
                'status' => 1
            ];
        }
    }
    if (!function_exists('generateMetaTags')) {
        function generateMetaTags(array $seoData): string {
            $title = $seoData['meta_title'] ?? 'Krishna Education Foundation';
            $desc = $seoData['meta_description'] ?? '';
            return "<title>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</title>\n<meta name=\"description\" content=\"" . htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') . "\">\n";
        }
    }
    if (!function_exists('generateOpenGraphTags')) {
        function generateOpenGraphTags(array $seoData): string {
            return "";
        }
    }
    if (!function_exists('generateTwitterCardTags')) {
        function generateTwitterCardTags(array $seoData): string {
            return "";
        }
    }
    if (!function_exists('generateSchemaMarkup')) {
        function generateSchemaMarkup(array $seoData, ?string $type = null, array $extraData = []): string {
            return "";
        }
    }
    if (!function_exists('generateBreadcrumbs')) {
        function generateBreadcrumbs(array $items): string {
            return "";
        }
    }
}




