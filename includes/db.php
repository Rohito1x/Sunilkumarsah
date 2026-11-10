<?php
// Database connection (PDO) & Security Configuration
require_once __DIR__ . '/security.php';

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
$dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'zptvncco_Sunil');
$username = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'zptvncco_sah');
$password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Rohit98185@#');
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
} catch (PDOException $e) {
    $dbError = $e->getMessage();
    // Fallback: Try alternate casing on Linux/cPanel (e.g. zptvncco_Sunil vs zptvncco_sunil)
    $altDbName = ($dbname === strtolower($dbname)) ? 'zptvncco_Sunil' : strtolower($dbname);
    if ($altDbName !== $dbname) {
        try {
            $dsnAlt = "mysql:host={$host};port={$port};dbname={$altDbName};charset={$charset}";
            $pdo = new PDO($dsnAlt, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5
            ]);
            $dbname = $altDbName;
            $dbError = null;
        } catch (PDOException $e2) {
            $pdo = null;
            $dbError = $e2->getMessage();
            error_log("Database connection failure: " . $dbError);
        }
    } else {
        error_log("Database connection failure: " . $dbError);
    }
}

if ($pdo) {
    try {
        // Auto-repair missing columns on tables if DB is connected
    $columnsToEnsure = [
        'bank_accounts' => [
            'qr_code' => "VARCHAR(255) DEFAULT NULL",
            'ifsc_code' => "VARCHAR(50) DEFAULT NULL",
            'instructions' => "TEXT DEFAULT NULL",
            'remarks' => "TEXT DEFAULT NULL",
            'is_default' => "TINYINT DEFAULT 0"
        ],
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
        ],
        'payment_receipts' => [
            'full_name' => "VARCHAR(150) NOT NULL",
            'email' => "VARCHAR(150) NOT NULL",
            'phone_number' => "VARCHAR(50) NOT NULL",
            'location' => "VARCHAR(150) NOT NULL",
            'amount' => "DECIMAL(12,2) DEFAULT NULL",
            'payment_method' => "VARCHAR(100) DEFAULT 'Fonepay / Bank QR'",
            'transaction_id' => "VARCHAR(100) DEFAULT NULL",
            'receipt_path' => "VARCHAR(255) NOT NULL",
            'remarks' => "TEXT DEFAULT NULL",
            'status' => "VARCHAR(30) DEFAULT 'Pending'",
            'admin_notes' => "TEXT DEFAULT NULL"
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

    // Auto-ensure admin_users table exists & has an active administrator
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS `admin_users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `password_hash` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $adminCount = (int)$pdo->query("SELECT COUNT(*) FROM `admin_users`")->fetchColumn();
        if ($adminCount === 0) {
            $initEmail = defined('ADMIN_EMAIL') ? strtolower(trim(ADMIN_EMAIL)) : 'admin@visionpoint.edu.np';
            $initHash = password_hash('adminSecure2026!', PASSWORD_BCRYPT);
            $stmtInit = $pdo->prepare("INSERT INTO `admin_users` (`email`, `password_hash`) VALUES (?, ?)");
            $stmtInit->execute([$initEmail, $initHash]);
        }
    } catch (PDOException $e) {
        // Safe to ignore if permissions are restricted
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

        // Auto-ensure payment_receipts table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS `payment_receipts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `full_name` VARCHAR(150) NOT NULL,
            `email` VARCHAR(150) NOT NULL,
            `phone_number` VARCHAR(50) NOT NULL,
            `location` VARCHAR(150) NOT NULL,
            `amount` DECIMAL(12,2) DEFAULT NULL,
            `payment_method` VARCHAR(100) DEFAULT 'Fonepay / Bank QR',
            `transaction_id` VARCHAR(100) DEFAULT NULL,
            `receipt_path` VARCHAR(255) NOT NULL,
            `remarks` TEXT DEFAULT NULL,
            `status` VARCHAR(30) DEFAULT 'Pending',
            `admin_notes` TEXT DEFAULT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX `idx_status` (`status`),
            INDEX `idx_location` (`location`),
            INDEX `idx_created` (`created_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        // Seed initial default SEO pages if empty
        $seoCount = (int)$pdo->query("SELECT COUNT(*) FROM `seo_pages`")->fetchColumn();
        if ($seoCount === 0) {
            $defaultSeoPages = [
                [
                    'static', null, 'home', 'Homepage',
                    'Vision Point Educational Service – Education Consultancy in Nepal',
                    'Vision Point Educational Service is a trusted education consultancy in Nepal helping students with admissions, scholarships, and visa guidance for India and abroad.',
                    'Vision Point Educational Service – Education Consultancy in Nepal',
                    'https://visionpoint.edu.np/',
                    'Vision Point Educational Service – Education Consultancy in Nepal',
                    'Empowering Nepalese students with expert counseling, accredited university admissions, and scholarship assistance.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'about', 'About Us',
                    'About Us | Vision Point Educational Service Biratnagar Nepal',
                    'Learn about Vision Point Educational Service, our certified education counselors, mission, accreditation, and commitment to ethical student admissions.',
                    'About Vision Point Educational Service',
                    'https://visionpoint.edu.np/about/',
                    'About Us | Vision Point Educational Service',
                    'Discover our journey, dedicated counselors, and trusted academic advisory services for Nepalese students.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'services', 'Our Services',
                    'Education Consultancy Services in Nepal | Vision Point Educational Service',
                    'Explore our educational services including university selection, career counseling, documentation processing, scholarship guidance, and visa support.',
                    'Our Comprehensive Education Services',
                    'https://visionpoint.edu.np/services/',
                    'Education Services in Nepal | Vision Point Educational Service',
                    'Full-cycle educational advisory from course selection to arrival and scholarship support.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-india', 'Study in India',
                    'Study in India from Nepal | Vision Point Educational Service',
                    'Complete guide for Nepali students to study in India. Discover top Indian universities, engineering, nursing, medical, and pharmacy courses with scholarships.',
                    'Study in India for Nepali Students',
                    'https://visionpoint.edu.np/study-in-india/',
                    'Study in India from Nepal | Vision Point Educational Service',
                    'Explore top universities, courses, transparent fees, and scholarship opportunities in India.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-australia', 'Study in Australia',
                    'Study in Australia from Nepal | Courses, Universities & Visa Guidance',
                    'Expert guidance for Nepali students applying to top Australian universities. Learn about Genuine Student (GS) criteria, CRICOS courses, fees, and post-study work visas.',
                    'Study in Australia for Nepali Students',
                    'https://visionpoint.edu.np/study-in-australia/',
                    'Study in Australia from Nepal | Vision Point Educational Service',
                    'Step-by-step guidance on Australian university admissions, GS requirements, and student visas.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-uk', 'Study in UK',
                    'Study in UK from Nepal | Top Universities, CAS & Graduate Visa',
                    'Discover studying in the UK for Nepalese students. Access Russell Group universities, CAS guidance, IELTS waiver options, and 2-year post-study Graduate Route visas.',
                    'Study in the UK for Nepali Students',
                    'https://visionpoint.edu.np/study-in-uk/',
                    'Study in UK from Nepal | Vision Point Educational Service',
                    'Complete assistance with UK university admissions, CAS letters, and Tier 4 student visa applications.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-usa', 'Study in USA',
                    'Study in USA from Nepal | I-20, Universities & F-1 Visa Guidance',
                    'Pursue higher education in the USA. Get expert support for US university applications, I-20 issuance, SEVIS documentation, and F-1 visa interview preparation.',
                    'Study in the USA for Nepali Students',
                    'https://visionpoint.edu.np/study-in-usa/',
                    'Study in USA from Nepal | Vision Point Educational Service',
                    'Guidance for Nepalese students on US admissions, STEM OPT extensions, and F-1 student visas.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'study-in-canada', 'Study in Canada',
                    'Study in Canada from Nepal | DLI Colleges, SDS & PGWP Guide',
                    'Comprehensive guide for Nepali students to study in Canada at Designated Learning Institutions (DLI). Learn about SDS stream, tuition costs, and PGWP work permits.',
                    'Study in Canada for Nepali Students',
                    'https://visionpoint.edu.np/study-in-canada/',
                    'Study in Canada from Nepal | Vision Point Educational Service',
                    'Apply to Canadian universities and colleges with full assistance on study permits and SDS filing.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'universities', 'Universities',
                    'Top Indian & International Universities for Nepali Students',
                    'Explore accredited universities in India and abroad partnering with Vision Point Educational Service. Compare programs, locations, and admission criteria.',
                    'Partner Universities & Educational Institutions',
                    'https://visionpoint.edu.np/universities/',
                    'Partner Universities | Vision Point Educational Service',
                    'Browse UGC, AICTE, and INC approved institutions for higher education.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'courses', 'Courses',
                    'Popular Courses & Degrees for Nepali Students | Vision Point Educational Service',
                    'Explore top courses including B.Sc Nursing, B.Tech Engineering, B.Pharm, BBA, BCA, BDS, and MBBS with recognized accreditations and scholarship opportunities.',
                    'Popular Academic Courses & Degree Streams',
                    'https://visionpoint.edu.np/courses/',
                    'Courses & Programs | Vision Point Educational Service',
                    'Find the right undergraduate and postgraduate programs matching your career goals.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'scholarships', 'Scholarships',
                    'Scholarships for Nepali Students in India & Abroad | Vision Point Educational Service',
                    'Discover available merit scholarships, institutional fee waivers, and government schemes for Nepali students studying in India, UK, USA, Canada, and Australia.',
                    'Scholarship Opportunities for Nepali Students',
                    'https://visionpoint.edu.np/scholarships/',
                    'Scholarship Guidance | Vision Point Educational Service',
                    'Apply for up to 50%-100% tuition scholarships in top universities.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'visa-guidance', 'Visa Guidance',
                    'Student Visa Guidance & Documentation | Vision Point Educational Service',
                    'Professional visa counseling, document attestation, financial verification, and interview preparation for Nepali students traveling abroad.',
                    'Student Visa & Documentation Guidance',
                    'https://visionpoint.edu.np/visa-guidance/',
                    'Visa Guidance | Vision Point Educational Service',
                    'Step-by-step guidance on genuine documentation, biometrics, and visa filing.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'ielts', 'IELTS Preparation',
                    'IELTS Preparation in Nepal | Format, Scoring & Test Practice',
                    'Comprehensive IELTS Academic & General preparation guide. Master Listening, Reading, Writing, and Speaking modules with certified instructor tips.',
                    'IELTS Preparation Guide for Nepali Students',
                    'https://visionpoint.edu.np/ielts/',
                    'IELTS Preparation | Vision Point Educational Service',
                    'Master your IELTS exam with structured practice, band score tips, and mock tests.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'pte', 'PTE Preparation',
                    'PTE Academic Preparation in Nepal | AI Scoring & Test Format',
                    'PTE Academic preparation guide for Nepalese students. Learn scoring strategies, section breakdown, and tips for fast score reporting to international universities.',
                    'PTE Academic Preparation & Scoring Guide',
                    'https://visionpoint.edu.np/pte/',
                    'PTE Academic Preparation | Vision Point Educational Service',
                    'Achieve your target PTE score with computer-based practice strategies.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'cee-preparation', 'CEE Preparation',
                    'CEE Preparation in Nepal | Medical & Dental Entrance Guidance',
                    'Complete guidance on Nepal Common Entrance Examination (CEE) for MBBS, BDS, B.Sc Nursing, and B.Pharm admissions under Medical Education Commission (MEC).',
                    'CEE Entrance Preparation for Medical & Health Sciences',
                    'https://visionpoint.edu.np/cee-preparation/',
                    'CEE Preparation Guide | Vision Point Educational Service',
                    'Prepare effectively for medical and allied health entrance examinations in Nepal and India.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'ctevt-preparation', 'CTEVT Preparation',
                    'CTEVT Entrance Preparation in Nepal | Diploma & Nursing Courses',
                    'Guide to CTEVT entrance exams for Staff Nurse, Diploma in Pharmacy, Lab Technology, and Engineering diploma courses in Nepal.',
                    'CTEVT Entrance Preparation Guide',
                    'https://visionpoint.edu.np/ctevt-preparation/',
                    'CTEVT Preparation | Vision Point Educational Service',
                    'Crack CTEVT diploma entrance exams with syllabus breakdown and study guidance.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'career-counselling', 'Career Counselling',
                    'Career Counselling & Academic Mentorship in Nepal | Vision Point Educational Service',
                    'Personalized 1-on-1 career counselling for Nepali students after +2, SEE, and graduation. Identify strengths, career trajectories, and optimal academic paths.',
                    'Personalized Career Counselling for Students',
                    'https://visionpoint.edu.np/career-counselling/',
                    'Career Counselling | Vision Point Educational Service',
                    'Make informed education and career decisions with professional counselors.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'blog', 'Blog & Guides',
                    'Educational Blog & Admission Guides | Vision Point Educational Service',
                    'Stay informed with latest articles, admission updates, university rankings, scholarship notifications, and visa guidelines for Nepali students.',
                    'Educational Resource Guides & Articles',
                    'https://visionpoint.edu.np/blog/',
                    'Educational Blog | Vision Point Educational Service',
                    'Expert guides and informative articles on higher education opportunities.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'contact', 'Contact Us',
                    'Contact Vision Point Educational Service | Biratnagar Nepal',
                    'Get in touch with Vision Point Educational Service. Visit our Biratnagar office, call +977 9807765812, or send an online inquiry for free education counseling.',
                    'Contact Vision Point Educational Service',
                    'https://visionpoint.edu.np/contact/',
                    'Contact Us | Vision Point Educational Service',
                    'Reach out to our counseling team for personalized education guidance.',
                    'assets/images/logo1.png',
                    'index,follow', 'EducationalOrganization', 1
                ],
                [
                    'static', null, 'student-success-stories', 'Student Success Stories',
                    'Student Success Stories & Reviews | Vision Point Educational Service',
                    'Read authentic reviews and success stories from Nepali students enrolled in top Indian and overseas universities through Vision Point Educational Service.',
                    'Student Reviews & Success Stories',
                    'https://visionpoint.edu.np/student-success-stories/',
                    'Student Success Stories | Vision Point Educational Service',
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

        // Auto-ensure pages table exists & seeded
        $pdo->exec("CREATE TABLE IF NOT EXISTS `pages` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `slug` VARCHAR(50) NOT NULL UNIQUE,
            `title` VARCHAR(255) NOT NULL,
            `hero_title` VARCHAR(255) DEFAULT NULL,
            `hero_subtitle` TEXT DEFAULT NULL,
            `content` LONGTEXT DEFAULT NULL,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $pagesCount = (int)$pdo->query("SELECT COUNT(*) FROM `pages`")->fetchColumn();
        if ($pagesCount === 0) {
            $studyInIndiaContent = json_encode([
                'why_cards' => [
                    ['title' => 'Recognized Degrees', 'icon' => 'fa-certificate', 'text' => 'Degrees from Indian universities are globally recognized, and directly approved by major councils in Nepal (NMC, NEC, NNC, NPC).'],
                    ['title' => 'Affordable Costs', 'icon' => 'fa-sack-dollar', 'text' => 'Get quality global education at a fraction of the cost of Western countries, with budget-friendly tuition fees and cost of living.'],
                    ['title' => 'Cultural Proximity', 'icon' => 'fa-handshake', 'text' => 'Familiar culture, climate, and language make it incredibly easy for Nepalese students to adapt and settle in.'],
                    ['title' => 'Scholarship Options', 'icon' => 'fa-graduation-cap', 'text' => 'Vast scholarships matching up to 50%-100% tuition coverage for students with strong academic scores.']
                ],
                'process_steps' => [
                    ['title' => 'Free Counselling', 'text' => 'Consult with our counselors to select the right course, university, and state.'],
                    ['title' => 'Eligibility Check', 'text' => 'Submit your transcripts (+2/Diploma/Bachelor) for grade verification.'],
                    ['title' => 'Scholarship Allocation', 'text' => 'Apply for available consultancy and university merit scholarships.'],
                    ['title' => 'Visa & Travel Guidance', 'text' => 'Receive help with travel arrangements, border protocols, and hostel allocation.']
                ]
            ]);

            $studyAbroadContent = json_encode([
                'benefits' => [
                    ['title' => 'Academic Excellence', 'icon' => 'fa-graduation-cap', 'text' => 'Gain access to advanced curriculums, cutting-edge labs, and highly distinguished faculty members driving innovation.'],
                    ['title' => 'Global Employability', 'icon' => 'fa-briefcase', 'text' => 'Foreign degrees hold massive weight with multinational corporations, equipping you for top salaries and career paths.'],
                    ['title' => 'Cross-Cultural Exposure', 'icon' => 'fa-globe', 'text' => 'Build lifelong connections with peers from all continents, developing a truly global outlook and independent life skills.'],
                    ['title' => 'Language & Growth', 'icon' => 'fa-language', 'text' => 'Polish your language fluency while gaining standard adaptability traits highly sought in competitive industries.']
                ],
                'destinations' => [
                    ['country' => 'United States of America', 'code' => 'USA', 'flag' => 'fa-flag-usa', 'img' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=600&auto=format&fit=crop&q=60', 'desc' => 'Home to Ivy League universities and world-renowned research facilities offering top-tier STEM and business majors.', 'benefits' => 'OPT work opportunities, top-ranked institutions, diverse cultural landscape.'],
                    ['country' => 'United Kingdom', 'code' => 'UK', 'flag' => 'fa-flag-uk', 'img' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=600&auto=format&fit=crop&q=60', 'desc' => 'Home to ancient academic heritage, short 1-year Master degrees, and 2-year post-study Graduate Route visas.', 'benefits' => '2-year graduate visa, short duration Master degrees, Russell Group universities.'],
                    ['country' => 'Australia', 'code' => 'AUS', 'flag' => 'fa-flag', 'img' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=600&auto=format&fit=crop&q=60', 'desc' => 'Renowned for world-class universities, high minimum wages, and vibrant multicultural student lifestyle.', 'benefits' => 'Post-study work rights, generous work hour limits, group of eight research leaders.'],
                    ['country' => 'Canada', 'code' => 'CAN', 'flag' => 'fa-flag', 'img' => 'https://images.unsplash.com/photo-1517935703635-27c946dfbfaf?w=600&auto=format&fit=crop&q=60', 'desc' => 'Leading global destination for quality education, student safety, and clear permanent residency pathways.', 'benefits' => 'Up to 3-year PGWP work permit, fast SDS visa processing, welcoming immigrant culture.']
                ]
            ]);

            $testPrepContent = json_encode([
                'courses' => [
                    ['name' => 'IELTS Academic & General', 'desc' => 'International English Language Testing System accepted across 140+ countries.', 'duration' => '6 Weeks (Daily 1.5 Hrs)', 'score_target' => 'Band 7.0+', 'modules' => 'Listening, Reading, Writing, Speaking with weekly mock tests'],
                    ['name' => 'PTE Academic', 'desc' => 'Pearson Test of English – Fast AI-scored computer test popular for Australia & UK.', 'duration' => '4-6 Weeks (Daily 1.5 Hrs)', 'score_target' => '65+ Score', 'modules' => 'Speaking & Writing, Reading, Listening on live computer simulator']
                ]
            ]);

            $stmtSeedPage = $pdo->prepare("INSERT INTO `pages` (`slug`, `title`, `hero_title`, `hero_subtitle`, `content`) VALUES (?, ?, ?, ?, ?)");
            $stmtSeedPage->execute(['study-in-india', 'Study in India', 'Study in India', 'Explore all the premium universities and colleges in India where we facilitate direct admission and scholarships.', $studyInIndiaContent]);
            $stmtSeedPage->execute(['study-abroad', 'Study Abroad', 'Study Abroad', 'Launch your global career by securing admissions in top universities and colleges across the world.', $studyAbroadContent]);
            $stmtSeedPage->execute(['test-preparation', 'Test Preparation', 'Test Preparation', 'Our expert teachers, interactive mock tests, and smart study materials ensure you achieve your target scores in IELTS, PTE, and TOEFL.', $testPrepContent]);
        }

        // Auto-ensure bank_accounts table exists & seeded
        $pdo->exec("CREATE TABLE IF NOT EXISTS `bank_accounts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `bank_name` VARCHAR(150) NOT NULL,
            `account_name` VARCHAR(150) NOT NULL,
            `account_number` VARCHAR(100) NOT NULL,
            `branch` VARCHAR(150) NOT NULL,
            `account_type` VARCHAR(50) DEFAULT 'Current Account',
            `swift_code` VARCHAR(50) DEFAULT 'CTZNNEPK',
            `payment_instructions` TEXT DEFAULT NULL,
            `notes` TEXT DEFAULT NULL,
            `display_order` INT DEFAULT 0,
            `is_active` TINYINT DEFAULT 1,
            `is_primary` TINYINT DEFAULT 1,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

        $bankCount = (int)$pdo->query("SELECT COUNT(*) FROM `bank_accounts`")->fetchColumn();
        if ($bankCount === 0) {
            $pdo->exec("INSERT INTO `bank_accounts` (`bank_name`, `account_name`, `account_number`, `branch`, `account_type`, `swift_code`, `payment_instructions`, `notes`, `display_order`, `is_active`, `is_primary`) VALUES ('Citizens Bank International Ltd.', 'VISION POINT EDUCATION SERVICE PVT.LTD', '0960100004966201', 'Vidhyapati Chowk, Janakpur', 'Current Account', 'CTZNNEPK', 'Please mention Student Full Name & Course in transaction remarks when depositing fees.', 'Primary Official Account', 0, 1, 1)");
        }
    } catch (PDOException $e) {
        // Safe to ignore if DB user lacks create table privileges
    }

} catch (PDOException $e) {
    error_log("Database initialization error: " . $e->getMessage());
}
}

// Helper: Secure Client IP resolution
if (!function_exists('getClientIp')) {
    function getClientIp(): string {
        if (class_exists('RateLimiter')) {
            return RateLimiter::getClientIp();
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

// Helper: Rate limiting check (F-06)
if (!function_exists('checkRateLimit')) {
    function checkRateLimit(?PDO $pdo, string $email): array {
        if (!$pdo) {
            return ['allowed' => true, 'remaining' => 0, 'msg' => ''];
        }
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
    function recordFailedAttempt(?PDO $pdo, string $email): void {
        if (!$pdo) {
            return;
        }
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
    function resetRateLimit(?PDO $pdo, string $email): void {
        if (!$pdo) {
            return;
        }
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
                'meta_title' => 'Vision Point Educational Service – Education Consultancy in Nepal',
                'meta_description' => 'Vision Point Educational Service is a trusted education consultancy in Nepal helping students with admissions, scholarships, and visa guidance for India and abroad.',
                'meta_keywords' => 'Education Consultancy in Nepal, Study in India, Study Abroad',
                'h1' => 'Vision Point Educational Service',
                'canonical_url' => 'https://visionpoint.edu.np/' . ($slug !== 'index' && $slug !== 'home' ? $slug . '/' : ''),
                'og_title' => 'Vision Point Educational Service',
                'og_description' => 'Expert education counseling and university admissions.',
                'og_image' => 'https://visionpoint.edu.np/assets/images/logo1.png',
                'og_type' => 'website',
                'robots' => 'index,follow',
                'schema_type' => 'EducationalOrganization',
                'status' => 1
            ];
        }
    }
    if (!function_exists('generateMetaTags')) {
        function generateMetaTags(array $seoData): string {
            $title = $seoData['meta_title'] ?? 'Vision Point Educational Service';
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




