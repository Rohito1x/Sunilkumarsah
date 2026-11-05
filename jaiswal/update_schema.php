<?php
/**
 * Database update script for Vision Point Educational Service Bureau website
 * Creates new tables and seeds them with initial data.
 */

// Auth check - ensure only logged in admins (or local CLI script) can run this
if (php_sapi_name() !== 'cli') {
    session_start();
    if (!isset($_SESSION['admin_id'])) {
        die("Unauthorized access. Please login to run this script.");
    }
}

require_once __DIR__ . '/../includes/db.php';

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected to the database successfully.<br>\n";

    // 1. Destinations / Cities Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `destinations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `region` VARCHAR(100) NOT NULL,
        `img` VARCHAR(255) DEFAULT NULL,
        `about` TEXT DEFAULT NULL,
        `gallery` TEXT DEFAULT NULL,
        `map_link` TEXT DEFAULT NULL,
        `display_order` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `destinations` created or verified.<br>\n";

    // 2. Course Streams Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `course_streams` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `icon` VARCHAR(50) DEFAULT 'fa-graduation-cap',
        `display_order` INT DEFAULT 0,
        `desc` TEXT DEFAULT NULL,
        `img` VARCHAR(255) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `course_streams` created or verified.<br>\n";

    // 3. Guides & Blog Posts Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `guides` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `summary` TEXT DEFAULT NULL,
        `content` TEXT DEFAULT NULL,
        `image_path` VARCHAR(255) DEFAULT NULL,
        `publish_date` DATE DEFAULT NULL,
        `status` VARCHAR(20) DEFAULT 'published',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `guides` created or verified.<br>\n";

    // 4. Fun Facts & Statistics Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `fun_facts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `number` VARCHAR(50) NOT NULL,
        `label` VARCHAR(150) NOT NULL,
        `icon` VARCHAR(50) DEFAULT NULL,
        `display_order` INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `fun_facts` created or verified.<br>\n";

    // 5. Social Media Links Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `social_links` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `platform_name` VARCHAR(100) NOT NULL,
        `url` VARCHAR(255) NOT NULL,
        `icon` VARCHAR(50) NOT NULL,
        `display_order` INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `social_links` created or verified.<br>\n";

    // 6. Navigation Menu Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `navigation_menu` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `label` VARCHAR(100) NOT NULL,
        `url` VARCHAR(255) NOT NULL,
        `display_order` INT DEFAULT 0,
        `is_visible` TINYINT DEFAULT 1,
        `parent_id` INT DEFAULT NULL,
        FOREIGN KEY (`parent_id`) REFERENCES `navigation_menu`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `navigation_menu` created or verified.<br>\n";

    // 7. Pages Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `pages` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `slug` VARCHAR(50) NOT NULL UNIQUE,
        `title` VARCHAR(255) NOT NULL,
        `hero_title` VARCHAR(255) DEFAULT NULL,
        `hero_subtitle` TEXT DEFAULT NULL,
        `content` LONGTEXT DEFAULT NULL,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `pages` created or verified.<br>\n";

    // 8. Countries Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `countries` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `is_active` TINYINT DEFAULT 1,
        `display_order` INT DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `countries` created or verified.<br>\n";

    // 9. Activity Log Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `activity_log` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `admin_id` INT DEFAULT NULL,
        `admin_email` VARCHAR(150) DEFAULT NULL,
        `action` VARCHAR(100) NOT NULL,
        `details` TEXT DEFAULT NULL,
        `ip_address` VARCHAR(45) DEFAULT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`admin_id`) REFERENCES `admin_users`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `activity_log` created or verified.<br>\n";

    // 10. Accreditations Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `accreditations` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(150) NOT NULL,
        `category` VARCHAR(50) NOT NULL DEFAULT 'international',
        `logo` VARCHAR(255) NOT NULL,
        `display_order` INT DEFAULT 0,
        `is_active` TINYINT DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `accreditations` created or verified.<br>\n";


    // ================= SEED DATA =================

    // Seed Destinations if empty
    $destCount = $pdo->query("SELECT COUNT(*) FROM destinations")->fetchColumn();
    if ($destCount == 0) {
        $destinations = [
            ['Delhi (Delhi NCR)', 'Delhi', 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=500&auto=format&fit=crop&q=60', 1],
            ['Bangalore (Bengaluru)', 'Bangalore', 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60', 2],
            ['Mumbai', 'Mumbai & Pune', 'https://images.unsplash.com/photo-1566552881560-0be862a7c445?w=500&auto=format&fit=crop&q=60', 3],
            ['Coimbatore', 'South India', 'assets/images/coimbatore.jpg', 4],
            ['Bhubaneswar', 'Bhubaneswar & East', 'assets/images/bhubaneswar.jpg', 5],
            ['Visakhapatnam', 'South India', 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=500&auto=format&fit=crop&q=60', 6],
            ['Shimla', 'Himachal & North', 'https://images.unsplash.com/photo-1562608264-a690e7552aa9?w=500&auto=format&fit=crop&q=60', 7],
            ['Punjab', 'Haryana & Punjab', 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=500&auto=format&fit=crop&q=60', 8],
            ['Chandigarh', 'Haryana & Punjab', 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60', 9],
            ['Nagpur', 'Mumbai & Pune', 'https://images.unsplash.com/photo-1566552881560-0be862a7c445?w=500&auto=format&fit=crop&q=60', 10],
            ['Kolkata', 'Kolkata', 'https://images.unsplash.com/photo-1558431382-27e303142255?w=500&auto=format&fit=crop&q=60', 11],
            ['Dehradun', 'Dehradun', 'https://images.unsplash.com/photo-1589136777351-fdc9c9c8c482?w=500&auto=format&fit=crop&q=60', 12]
        ];
        $stmt = $pdo->prepare("INSERT INTO destinations (name, region, img, display_order) VALUES (?, ?, ?, ?)");
        foreach ($destinations as $d) {
            $stmt->execute($d);
        }
        echo "Seeded destinations table.<br>\n";
    }

    // Seed Course Streams if empty
    $courseCount = $pdo->query("SELECT COUNT(*) FROM course_streams")->fetchColumn();
    if ($courseCount == 0) {
        $courses = [
            ['Nursing', 'fa-user-nurse', 1],
            ['Engineering', 'fa-gears', 2],
            ['Pharmacy', 'fa-pills', 3],
            ['Management', 'fa-briefcase', 4],
            ['Computer Application / IT', 'fa-laptop-code', 5],
            ['Dental', 'fa-tooth', 6],
            ['Behavioral and Social Science', 'fa-people-group', 7],
            ['Allied Health Science', 'fa-heart-pulse', 8],
            ['Agriculture', 'fa-wheat-awn', 9],
            ['Medical', 'fa-stethoscope', 10],
            ['Design', 'fa-compass-drafting', 11],
            ['Paramedical', 'fa-kit-medical', 12],
            ['Arts and Humanities', 'fa-palette', 13],
            ['Media Studies & Communication', 'fa-bullhorn', 14],
            ['Science', 'fa-microscope', 15],
            ['Hotel Management', 'fa-bell-concierge', 16],
            ['Law', 'fa-scale-balanced', 17],
            ['Education', 'fa-chalkboard-user', 18]
        ];
        $stmt = $pdo->prepare("INSERT INTO course_streams (name, icon, display_order) VALUES (?, ?, ?)");
        foreach ($courses as $c) {
            $stmt->execute($c);
        }
        echo "Seeded course_streams table.<br>\n";
    }

    // Seed Guides if empty
    $guidesCount = $pdo->query("SELECT COUNT(*) FROM guides")->fetchColumn();
    if ($guidesCount == 0) {
        $guides = [
            [
                'Best Nursing Colleges in Bangalore', 
                'best-nursing-colleges-in-bangalore', 
                'Explore the leading Nursing institutes in Bangalore recognized by INC, detailing admission options, fees, and clinical internship setups.',
                '<p>Bangalore is widely known as a hub for Nursing education in India, attracting thousands of students from Nepal annually. Key factors driving this trend include state-of-the-art hospitals, INC (Indian Nursing Council) certifications, and robust clinical internship environments.</p><p>Top institutions like Krupanidhi College of Nursing, Narayana Health, and St. John\'s offer comprehensive degrees with excellent placement facilities. Contact our counselors to find the best seat options and available scholarships.</p>',
                'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&auto=format&fit=crop&q=60',
                '2025-11-11'
            ],
            [
                'B.Sc. Nursing in Bangalore', 
                'bsc-nursing-in-bangalore', 
                'A comprehensive guide on pursuing a 4-year B.Sc. Nursing in Bangalore, including eligibility criteria, state board registry, and placements.',
                '<p>A 4-year B.Sc. Nursing program in Bangalore provides students with professional training in anatomy, physiology, critical care, and community health. Graduates are registered under the Karnataka State Nursing Council, which enables them to work worldwide.</p><p>Eligibility requires 50% in +2 Science (Biology, Chemistry, Physics). Our consultancy offers direct admissions, hostel booking help, and up to 50% tuition waivers for eligible students.</p>',
                'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?w=600&auto=format&fit=crop&q=60',
                '2025-08-03'
            ],
            [
                'Study Nursing in Bangalore', 
                'study-nursing-in-bangalore', 
                'Why Bangalore remains the hotbed for nursing education for Nepali students, highlighting budget accommodations and language transition.',
                '<p>For Nepalese students, Bangalore offers a comfortable blend of high academic quality and simple language transitions, as English and Hindi are widely understood. Accommodations near colleges range from college hostels to shared PG (paying guest) rooms, which are very budget-friendly.</p>',
                'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=600&auto=format&fit=crop&q=60',
                '2025-06-30'
            ],
            [
                'Study in India Scholarships: A Comprehensive Guide for Nepali Students in 2026',
                'study-in-india-scholarships-2026',
                'Everything you need to know about applying for government and university merit scholarships when studying in India.',
                '<p>India offers numerous scholarships for international and Nepalese students through the COMPEX scheme, ICCR schemes, and direct university merit tie-ups. Learn how to draft a stellar application, submit transcripts, and negotiate fee waivers of up to 100%.</p>',
                'assets/images/banner.png',
                '2025-08-24'
            ],
            [
                'GNM Nursing at East Point College of Nursing',
                'gnm-nursing-east-point',
                'Deep dive into East Point College of Nursing GNM course: eligibility, hostel environment, and clinical rotations.',
                '<p>General Nursing and Midwifery (GNM) is a 3-year diploma course. East Point College of Nursing offers excellent infrastructure, a 1000+ bed parent hospital, and an active campus life. Ideal for students looking for professional entry-level clinical options in Bangalore.</p>',
                'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&auto=format&fit=crop&q=60',
                '2025-11-10'
            ],
            [
                'CSE in India: Colleges and Careers',
                'cse-in-india-colleges-careers',
                'Guide to Computer Science Engineering (CSE) in top Indian universities, focusing on campus recruitments and skill-building cycles.',
                '<p>Computer Science Engineering continues to be a top career path. Learn about top-ranking universities in India, including Sharda University, Galgotias University, and Chandigarh University, which offer specialization programs in Artificial Intelligence, Machine Learning, and Cybersecurity.</p>',
                'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=60',
                '2025-10-13'
            ]
        ];
        $stmt = $pdo->prepare("INSERT INTO guides (title, slug, summary, content, image_path, publish_date) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($guides as $g) {
            $stmt->execute($g);
        }
        echo "Seeded guides table.<br>\n";
    }

    // Seed Fun Facts if empty
    $factsCount = $pdo->query("SELECT COUNT(*) FROM fun_facts")->fetchColumn();
    if ($factsCount == 0) {
        $facts = [
            ['123+', 'Admissions Seeded', 'fa-graduation-cap', 1],
            ['80%', 'Scholarship Success', 'fa-award', 2],
            ['95%', 'Satisfaction Rate', 'fa-smile', 3]
        ];
        $stmt = $pdo->prepare("INSERT INTO fun_facts (number, label, icon, display_order) VALUES (?, ?, ?, ?)");
        foreach ($facts as $f) {
            $stmt->execute($f);
        }
        echo "Seeded fun_facts table.<br>\n";
    }

    // Seed Social Links if empty
    $socialCount = $pdo->query("SELECT COUNT(*) FROM social_links")->fetchColumn();
    if ($socialCount == 0) {
        $socials = [
            ['Facebook', 'https://www.facebook.com/share/1LuozTGdwc/', 'fa-brands fa-facebook', 1],
            ['Instagram', 'https://www.instagram.com/kefnepal.official?igsh=MTl5azkwYmRrMndlaQ==', 'fa-brands fa-instagram', 2],
            ['Twitter', 'https://twitter.com', 'fa-brands fa-twitter', 3],
            ['Youtube', 'https://youtube.com/@kefnepal?si=Mzd-jDJC-953AX6T', 'fa-brands fa-youtube', 4]
        ];
        $stmt = $pdo->prepare("INSERT INTO social_links (platform_name, url, icon, display_order) VALUES (?, ?, ?, ?)");
        foreach ($socials as $s) {
            $stmt->execute($s);
        }
        echo "Seeded social_links table.<br>\n";
    }

    // Seed Navigation Menu if empty
    $menuCount = $pdo->query("SELECT COUNT(*) FROM navigation_menu")->fetchColumn();
    if ($menuCount == 0) {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("INSERT INTO navigation_menu (label, url, display_order, is_visible, parent_id) VALUES (?, ?, ?, 1, NULL)");
        
        $stmt->execute(['Home', 'index.php', 1]);
        $stmt->execute(['Study in India', 'study-in-india.php', 2]);
        $stmt->execute(['Institutions', 'cities.php', 3]);
        $stmt->execute(['Courses', 'courses.php', 4]);
        $stmt->execute(['Guide', 'guides.php', 5]);
        $stmt->execute(['Study Abroad', 'study-abroad.php', 6]);
        
        $stmt->execute(['Test preparation', '#', 7]);
        $parent_id = $pdo->lastInsertId();
        
        $stmtSub = $pdo->prepare("INSERT INTO navigation_menu (label, url, display_order, is_visible, parent_id) VALUES (?, ?, ?, 1, ?)");
        $stmtSub->execute(['IELTS', 'test-preparation.php?test=ielts', 1, $parent_id]);
        $stmtSub->execute(['PTE', 'test-preparation.php?test=pte', 2, $parent_id]);
        $stmtSub->execute(['TOEFL', 'test-preparation.php?test=toefl', 3, $parent_id]);
        $stmtSub->execute(['SAT', 'test-preparation.php?test=sat', 4, $parent_id]);
        
        $pdo->commit();
        echo "Seeded navigation_menu table.<br>\n";
    }

    // Seed Countries if empty
    $countryCount = $pdo->query("SELECT COUNT(*) FROM countries")->fetchColumn();
    if ($countryCount == 0) {
        $countries = [
            ['India', 1],
            ['US', 2],
            ['Canada', 3],
            ['UK', 4],
            ['Australia', 5],
            ['Bangladesh', 6],
            ['Europe', 7]
        ];
        $stmt = $pdo->prepare("INSERT INTO countries (name, display_order) VALUES (?, ?)");
        foreach ($countries as $c) {
            $stmt->execute($c);
        }
        echo "Seeded countries table.<br>\n";
    }

    // Seed Static Pages if empty
    $pagesCount = $pdo->query("SELECT COUNT(*) FROM pages")->fetchColumn();
    if ($pagesCount == 0) {
        // Study in India Details
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

        // Study Abroad Details
        $studyAbroadContent = json_encode([
            'benefits' => [
                ['title' => 'Academic Excellence', 'icon' => 'fa-graduation-cap', 'text' => 'Gain access to advanced curriculums, cutting-edge labs, and highly distinguished faculty members driving innovation.'],
                ['title' => 'Global Employability', 'icon' => 'fa-briefcase', 'text' => 'Foreign degrees hold massive weight with multinational corporations, equipping you for top salaries and career paths.'],
                ['title' => 'Cross-Cultural Exposure', 'icon' => 'fa-globe', 'text' => 'Build lifelong connections with peers from all continents, developing a truly global outlook and independent life skills.'],
                ['title' => 'Language & Growth', 'icon' => 'fa-language', 'text' => 'Polish your language fluency while gaining standard adaptability traits highly sought in competitive industries.']
            ],
            'destinations' => [
                [
                    'country' => 'United States of America',
                    'code' => 'USA',
                    'flag' => 'fa-flag-usa',
                    'img' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=600&auto=format&fit=crop&q=60',
                    'desc' => 'Home to Ivy League universities and world-renowned research facilities offering top-tier STEM and business majors.',
                    'benefits' => 'OPT work opportunities, top-ranked institutions, diverse cultural landscape.'
                ],
                [
                    'country' => 'United Kingdom',
                    'code' => 'UK',
                    'flag' => 'fa-landmark',
                    'img' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=600&auto=format&fit=crop&q=60',
                    'desc' => 'Deep academic history with accelerated degree paths (3-year Bachelor, 1-year Master) recognized globally.',
                    'benefits' => '2-year post-study work visa (Graduate Route), fast-track graduation, rich culture.'
                ],
                [
                    'country' => 'Canada',
                    'code' => 'CA',
                    'flag' => 'fa-leaf',
                    'img' => 'https://images.unsplash.com/photo-1507608869274-d3177c8bb4c7?w=600&auto=format&fit=crop&q=60',
                    'desc' => 'Highly welcoming policies, stunning natural environments, and premium quality of life with affordable living.',
                    'benefits' => 'Post-Graduation Work Permit (PGWP), pathways to Permanent Residency (PR), safe campus life.'
                ],
                [
                    'country' => 'Australia',
                    'code' => 'AU',
                    'flag' => 'fa-austral-sign',
                    'img' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=600&auto=format&fit=crop&q=60',
                    'desc' => 'Vibrant cities, world-class teaching standards, and extensive student support with top career prospects.',
                    'benefits' => 'Extended post-study work options, sunshine & great beaches, strong economy.'
                ]
            ],
            'steps' => [
                ['title' => 'Step 1: Consultation & Target Setting', 'text' => 'Evaluate academic credentials and map them to preferred countries, courses, and financial budgets.'],
                ['title' => 'Step 2: English Proficiency Tests', 'text' => 'Enroll in test prep classes (IELTS/PTE/TOEFL) and secure competitive bands needed for visa requirements.'],
                ['title' => 'Step 3: Document Compilation & Application', 'text' => 'Prepare Statements of Purpose (SOP), letters of recommendation, and submit applications to shortlist universities.'],
                ['title' => 'Step 4: Visa Processing & Flight', 'text' => 'Compile financial documentations, clear visa interviews, attend pre-departure orientations, and fly!']
            ]
        ]);

        // Test Preparation Details
        $testPrepContent = json_encode([
            'tests' => [
                'ielts' => [
                    'name' => 'International English Language Testing System (IELTS)',
                    'desc' => 'IELTS is the world\'s most popular English language proficiency test for higher education and global migration. It evaluates your skills in listening, reading, writing, and speaking.',
                    'duration' => '2.5 Hr',
                    'range' => '0 - 9',
                    'target' => '6.5+',
                    'validity' => '2 Yrs',
                    'format' => [
                        ['Listening', '30 minutes (4 sections, 40 questions)'],
                        ['Reading', '60 minutes (3 texts, 40 questions)'],
                        ['Writing', '60 minutes (2 tasks - descriptive & essay)'],
                        ['Speaking', '11–14 minutes (Face-to-face interview)']
                    ]
                ],
                'pte' => [
                    'name' => 'Pearson Test of English (PTE Academic)',
                    'desc' => 'PTE Academic is a computer-based English language test accepted by educational institutions and governments globally. It utilizes AI scoring for rapid results, typically within 48 hours.',
                    'duration' => '2.0 Hr',
                    'range' => '10 - 90',
                    'target' => '58+',
                    'validity' => '2 Yrs',
                    'format' => [
                        ['Speaking & Writing', '54–67 minutes (combined section with essays/read alouds)'],
                        ['Reading', '29–30 minutes (multiple-choice, fill in blanks)'],
                        ['Listening', '30–43 minutes (dictation, summaries, audio playback)']
                    ]
                ],
                'toefl' => [
                    'name' => 'Test of English as a Foreign Language (TOEFL iBT)',
                    'desc' => 'TOEFL iBT measures your ability to use and understand English at the university level. It is widely preferred in the USA and preferred by over 11,000 institutions internationally.',
                    'duration' => '2.0 Hr',
                    'range' => '0 - 120',
                    'target' => '80+',
                    'validity' => '2 Yrs',
                    'format' => [
                        ['Reading', '35 minutes (20 questions from academic texts)'],
                        ['Listening', '36 minutes (28 questions from lectures/conversations)'],
                        ['Speaking', '16 minutes (4 integrated tasks)'],
                        ['Writing', '29 minutes (2 tasks - academic discussion & integrated)']
                    ]
                ],
                'sat' => [
                    'name' => 'Scholastic Assessment Test (Digital SAT)',
                    'desc' => 'The SAT is a standardized college admission test used in the United States and several other countries. It measures high school students\' readiness for college-level courses, focusing on Math and English.',
                    'duration' => '2.1 Hr',
                    'range' => '400-1600',
                    'target' => '1300+',
                    'validity' => '5 Yrs',
                    'format' => [
                        ['Reading & Writing', '64 minutes (54 questions on passages and sentences)'],
                        ['Mathematics', '70 minutes (44 questions covering algebra, advanced math, geometry)']
                    ]
                ]
            ],
            'features' => [
                ['title' => 'Certified Instructors', 'text' => 'Learn from highly experienced teachers certified by British Council and Pearson.', 'icon' => 'fa-chalkboard-user'],
                ['title' => 'State-of-the-Art Computer Lab', 'text' => 'Practice on full-length simulator tests exactly representing the real digital tests.', 'icon' => 'fa-laptop'],
                ['title' => 'Weekly Mock Tests', 'text' => 'Evaluate your weak points with detailed performance feedbacks and review sessions every Sunday.', 'icon' => 'fa-file-signature'],
                ['title' => 'Free Study Materials', 'text' => 'Get access to premium books, vocabulary logs, and mock portals without extra costs.', 'icon' => 'fa-book-open-reader']
            ]
        ]);

        $stmt = $pdo->prepare("INSERT INTO pages (slug, title, hero_title, hero_subtitle, content) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['study-in-india', 'Study in India', 'Study in India', 'Explore all the premium universities and colleges in India where we facilitate direct admission and scholarships.', $studyInIndiaContent]);
        $stmt->execute(['study-abroad', 'Study Abroad', 'Study Abroad', 'Launch your global career by securing admissions in top universities and colleges across the world. Discover high-quality learning, multicultural campuses, and immense post-study work opportunities.', $studyAbroadContent]);
        $stmt->execute(['test-preparation', 'Test Preparation', 'Test Preparation', 'Our expert teachers, interactive mock tests, and smart study materials ensure you achieve your target scores in IELTS, PTE, TOEFL, and SAT on the first attempt.', $testPrepContent]);
        
        echo "Seeded pages table.<br>\n";
    }

    // Seed additional dynamic settings including Indo Global Theme Colors
    $settings = [
        'primary_color' => '#DC281E',
        'primary_light' => '#F00000',
        'secondary_color' => '#111111',
        'secondary_hover' => '#222222',
        'hero_banner_image' => 'assets/images/banner.png',
        'footer_about' => 'Vision Point Educational Service is Nepal\'s premier choice for studying in India, offering expert admission guidance and scholarships for top-tier Indian universities.',
        'footer_copyright' => ''
    ];
    $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    foreach ($settings as $k => $v) {
        $stmt->execute([$k, $v]);
    }
    echo "Seeded additional site settings.<br>\n";


    // --- 10. Custom Enhancements (Vision Point Educational Service) ---
    // Ensure all required columns exist in institutions & destinations tables
    $schemaColumns = [
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
        ]
    ];
    foreach ($schemaColumns as $tbl => $cols) {
        try {
            $stmt = $pdo->query("SHOW COLUMNS FROM `$tbl`");
            $existing = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($cols as $colName => $colDef) {
                if (!in_array($colName, $existing, true)) {
                    $pdo->exec("ALTER TABLE `$tbl` ADD COLUMN `$colName` $colDef");
                    echo "Added column `$colName` to `$tbl`.<br>\n";
                }
            }
        } catch (PDOException $e) {
            // Ignore table missing errors
        }
    }


    // Create institution_pdfs table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `institution_pdfs` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `institution_id` INT NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `display_order` INT DEFAULT 0,
        `is_active` TINYINT DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `institution_pdfs` created or verified.<br>\n";

    // Create bank_accounts table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `bank_accounts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `bank_name` VARCHAR(150) NOT NULL,
        `account_name` VARCHAR(150) NOT NULL,
        `account_number` VARCHAR(50) NOT NULL,
        `branch` VARCHAR(150) DEFAULT NULL,
        `account_type` VARCHAR(50) DEFAULT NULL,
        `swift_code` VARCHAR(50) DEFAULT NULL,
        `instructions` TEXT DEFAULT NULL,
        `display_order` INT DEFAULT 0,
        `is_active` TINYINT DEFAULT 1,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `bank_accounts` created or verified.<br>\n";

    // Create reviews table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `reviews` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(120) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `course_college` VARCHAR(180) DEFAULT NULL,
        `rating` INT NOT NULL DEFAULT 5,
        `title` VARCHAR(255) DEFAULT NULL,
        `comment` TEXT NOT NULL,
        `status` VARCHAR(20) DEFAULT 'Approved',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `reviews` created or verified.<br>\n";

    // Seed default reviews if empty
    $reviewsCount = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    if ($reviewsCount == 0) {
        $reviews = [
            ['Priya Sharma', 'priya.sharma@example.com', 'B.Tech CSE - Galgotias University', 5, 'Exceptional Guidance & Seamless Admission!', 'Vision Point Educational Service guided me step-by-step through my application to Galgotias University. From documentation to fee payment and hostel accommodation in Delhi NCR, everything was hassle-free. Highly recommended!', 'Approved'],
            ['Aashish Adhikari', 'aashish.adhikari@example.com', 'B.Sc Nursing - East Point College Bangalore', 5, 'Best Education Consultancy in Nepal', 'I was very nervous about studying abroad, but the counselors at Vision Point explained every detail clearly. They secured a tuition scholarship for my nursing degree in Bangalore and helped with visa processing.', 'Approved'],
            ['Suman Shrestha', 'suman.shrestha@example.com', 'B.Pharm - Sharda University', 5, 'Honest Counselors and Zero Hidden Charges', 'Unlike other agencies, Vision Point Educational Service was completely transparent about college fees and living expenses. The counseling session in Biratnagar was eye-opening and super helpful.', 'Approved'],
            ['Rohan Thapa', 'rohan.thapa@example.com', 'BBA - Chandigarh University', 4, 'Great Support & Fast Processing', 'The admission team processed my documentation extremely fast. I received my admission offer letter within 3 days. Thank you Vision Point team for making my study in India dream come true!', 'Approved']
        ];
        $stmtRev = $pdo->prepare("INSERT INTO `reviews` (`name`, `email`, `course_college`, `rating`, `title`, `comment`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($reviews as $rev) {
            $stmtRev->execute($rev);
        }
        echo "Seeded default reviews.<br>\n";
    }

    // Ensure Reviews link exists in navigation_menu
    $navCheck = $pdo->query("SELECT COUNT(*) FROM navigation_menu WHERE url = 'reviews.php'")->fetchColumn();
    if ($navCheck == 0) {
        $stmtNav = $pdo->prepare("INSERT INTO navigation_menu (label, url, display_order, is_visible, parent_id) VALUES (?, ?, ?, 1, NULL)");
        $stmtNav->execute(['Reviews', 'reviews.php', 8]);
        echo "Added 'Reviews' to navigation menu.<br>\n";
    }

    // Seed/Update Admin User (admin@visionpoint.edu.np / adminSecure2026!)
    // First remove the default hardcoded admin account admin@krishnaeducation.com.np
    $pdo->exec("DELETE FROM `admin_users` WHERE `email` = 'admin@krishnaeducation.com.np'");
    
    // Add or update the new admin user
    $adminEmail = 'admin@visionpoint.edu.np';
    $adminHash = '$2y$10$e4/3wIE6LaBZVNmLIOT7beYwV5G8emOCE2JzEyRrBJHFs/5s4IE/y'; // hash of adminSecure2026!
    $stmtAdmin = $pdo->prepare("INSERT INTO `admin_users` (`email`, `password_hash`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `password_hash` = VALUES(`password_hash`)");
    $stmtAdmin->execute([$adminEmail, $adminHash]);
    echo "Seeded secure administrator: <b>$adminEmail</b><br>\n";

    // Seed QR Code settings keys
    $qrSettings = [
        'qr_code_enabled' => '0',
        'qr_code_image' => '',
        'qr_code_title' => 'Scan to Pay',
        'qr_code_description' => 'Scan this QR code to make bank transfer payments securely.',
        'accreditation_title' => 'Accreditation',
        'accreditation_subtitle' => 'Trusted excellence backed by top accreditations',
        'accreditation_enabled' => '1'
    ];
    $stmtQr = $pdo->prepare("INSERT INTO site_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    foreach ($qrSettings as $k => $v) {
        $stmtQr->execute([$k, $v]);
    }
    echo "Seeded QR code & accreditation configuration keys.<br>\n";

    // Seed Accreditations if empty
    $accredCount = $pdo->query("SELECT COUNT(*) FROM accreditations")->fetchColumn();
    if ($accredCount == 0) {
        $accreditations = [
            ['International Accreditation 1', 'international', 'int1.jpeg', 1, 1],
            ['International Accreditation 2', 'international', 'int2.jpeg', 2, 1],
            ['International Accreditation 3', 'international', 'int3.jpeg', 3, 1],
            ['Nepal Government / MOEST', 'national', 'nat1.jpeg', 1, 1],
            ['CTEVT Accreditation', 'national', 'nat2.jpeg', 2, 1],
            ['National Council Accreditation', 'national', 'nat3.jpeg', 3, 1]
        ];
        $stmtAccred = $pdo->prepare("INSERT INTO accreditations (title, category, logo, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
        foreach ($accreditations as $acc) {
            $stmtAccred->execute($acc);
        }
        echo "Seeded accreditations table.<br>\n";
    }

    echo "<h3>All Schema and Data Seeding Completed Successfully!</h3>\n";

} catch (PDOException $e) {
    die("Database upgrade failed: " . $e->getMessage());
}
