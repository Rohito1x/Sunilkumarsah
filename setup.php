<?php
/**
 * Setup script for Vision Point Educational Service Website Database
 * Security Protected: Execution disabled in production environments.
 */

if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// Security Guard: Prevent unauthorized web execution in production
if (php_sapi_name() !== 'cli' && (!defined('DEV_MODE') || !DEV_MODE)) {
    http_response_code(403);
    exit('403 Forbidden: setup.php is disabled for public production access.');
}

$host = defined('DB_HOST') ? DB_HOST : (getenv('DB_HOST') ?: 'localhost');
$port = defined('DB_PORT') ? DB_PORT : (getenv('DB_PORT') ?: '3306');
$username = defined('DB_USER') ? DB_USER : (getenv('DB_USER') ?: 'zptvncco_sah');
$password = defined('DB_PASS') ? DB_PASS : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : 'Rohit98185@#');
$dbname = defined('DB_NAME') ? DB_NAME : (getenv('DB_NAME') ?: 'zptvncco_Sunil');

try {
    // Try connecting directly with database name first (supported in all cPanel shared hosting)
    try {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to database `$dbname`.<br>";
    } catch (PDOException $e) {
        // If DB doesn't exist yet, connect without DB name to create it
        $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$dbname`");
        echo "Database `$dbname` created or verified.<br>";
    }
    
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");

    $sqlFile = __DIR__ . '/database.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "<b style='color:green;'>SUCCESS: All tables & data from database.sql imported successfully!</b><br>";
    }

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

    echo "<hr><p><b>Next Steps:</b></p>";
    echo "<p><a href='index.php'>Go to Website Homepage</a></p>";
    echo "<p><a href='login.php'>Go to Admin Login</a></p>";
    exit;
    
    // Create admin_users table
    $pdo->exec("CREATE TABLE `admin_users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `password_hash` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `admin_users` created.<br>";
    
    // Create newsletter_subscribers table
    $pdo->exec("CREATE TABLE `newsletter_subscribers` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `newsletter_subscribers` created.<br>";
    
    // Create inquiries table
    $pdo->exec("CREATE TABLE `inquiries` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `full_name` VARCHAR(120) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone_number` VARCHAR(30) NOT NULL,
        `address` VARCHAR(180) DEFAULT NULL,
        `country` VARCHAR(80) DEFAULT NULL,
        `course` VARCHAR(80) DEFAULT NULL,
        `message` TEXT DEFAULT NULL,
        `form_type` VARCHAR(50) DEFAULT 'contact_form', -- contact_form or enquiry_form
        `status` VARCHAR(30) DEFAULT 'Pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `inquiries` created.<br>";
    
    // Create enrollments table
    $pdo->exec("CREATE TABLE `enrollments` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `full_name` VARCHAR(120) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone_number` VARCHAR(30) NOT NULL,
        `address` VARCHAR(180) DEFAULT NULL,
        `interested_country` VARCHAR(80) DEFAULT NULL,
        `level` VARCHAR(80) DEFAULT NULL,
        `interested_city` VARCHAR(80) DEFAULT NULL,
        `interested_college` VARCHAR(120) DEFAULT NULL,
        `faculty` VARCHAR(100) DEFAULT NULL,
        `interested_program` VARCHAR(100) DEFAULT NULL,
        `highest_education` VARCHAR(80) DEFAULT NULL,
        `percentage` VARCHAR(30) DEFAULT NULL,
        `message` TEXT DEFAULT NULL,
        `photo_path` VARCHAR(255) DEFAULT NULL,
        `status` VARCHAR(30) DEFAULT 'Pending',
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `enrollments` created.<br>";

    // Create login_attempts table for rate limiting
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
    echo "Table `login_attempts` created.<br>";


    // Create institutions table
    // Create institutions table
    $pdo->exec("CREATE TABLE `institutions` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(180) NOT NULL,
        `city` VARCHAR(100) NOT NULL,
        `region` VARCHAR(100) NOT NULL,
        `img` VARCHAR(255) DEFAULT NULL,
        `about` TEXT DEFAULT NULL,
        `fees` TEXT DEFAULT NULL,
        `admission_process` TEXT DEFAULT NULL,
        `location_detail` TEXT DEFAULT NULL,
        `courses` TEXT DEFAULT NULL,
        `map_link` TEXT DEFAULT NULL,
        `is_top` TINYINT DEFAULT 0,
        `status` VARCHAR(20) DEFAULT 'Active',
        `display_order` INT DEFAULT 0,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `institutions` created.<br>";

    // Create institution_pdfs table
    $pdo->exec("CREATE TABLE `institution_pdfs` (
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
    echo "Table `institution_pdfs` created.<br>";

    // Create bank_accounts table
    $pdo->exec("CREATE TABLE `bank_accounts` (
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
    echo "Table `bank_accounts` created.<br>";

    // Create site_settings table
    $pdo->exec("CREATE TABLE `site_settings` (
        `key` VARCHAR(50) PRIMARY KEY,
        `value` TEXT NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    echo "Table `site_settings` created.<br>";

    // Create reviews table
    $pdo->exec("CREATE TABLE `reviews` (
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
    echo "Table `reviews` created.<br>";

    // Seed default reviews
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
    echo "Default student reviews seeded.<br>";
    
    // Seed secure admin account: admin@visionpoint.edu.np / adminSecure2026!
    $adminEmail = 'admin@visionpoint.edu.np';
    $adminPassHash = '$2y$10$e4/3wIE6LaBZVNmLIOT7beYwV5G8emOCE2JzEyRrBJHFs/5s4IE/y';
    
    $stmt = $pdo->prepare("INSERT INTO `admin_users` (`email`, `password_hash`) VALUES (?, ?)");
    $stmt->execute([$adminEmail, $adminPassHash]);
    echo "Default secure administrator seeded: <b>$adminEmail</b><br>";

    // Seed default settings
    $settings = [
        'site_logo' => 'assets/images/logo1.png',
        'site_favicon' => 'assets/images/img4.png',
        'hero_banner_image' => 'assets/images/image1.png',
        'primary_color' => '#0458A0',
        'primary_light' => '#0365B5',
        'secondary_color' => '#D91F23',
        'secondary_hover' => '#C60F13',

        'site_title' => 'Vision Point Educational Service | Study Abroad & India',
        'site_name' => 'Vision Point Educational Service',
        'hero_title' => "Unlock Your Full \r\nEducational Potential in India",
        'hero_subtitle' => 'Maximize your potential and reach new academic peaks with our expert guidance, university listings, and scholarships every step of the way.',
        'contact_phone' => '+977 9807765812',
        'contact_email' => 'info@visionpoint.edu.np',
        'contact_location' => 'Vision Point Educational Service, Main Road (Opposite Traffic Police Office / Roadcess Chowk), Biratnagar 56613, Morang, Nepal',
        'qr_code_enabled' => '1',
        'qr_code_image' => 'assets/images/siddhartha_qr.png',
        'qr_code_title' => 'Scan to Pay',
        'qr_code_description' => 'Scan this QR code to make bank transfer payments securely.'
    ];
    $stmt = $pdo->prepare("INSERT INTO `site_settings` (`key`, `value`) VALUES (?, ?)");
    foreach ($settings as $k => $v) {
        $stmt->execute([$k, $v]);
    }
    echo "Default site configurations seeded.<br>";

    // Seed default institutions list
    $defaultInstitutions = [
        ['name' => 'Galgotias University Delhi', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/galgotias.jpg'],
        ['name' => 'Sharda University- Delhi', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/sharda.jpg'],
        ['name' => 'Noida International University', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/noida_international.jpg'],
        ['name' => 'Manav Rachana University', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/manav_rachana.jpg'],
        ['name' => 'Delhi Technological University', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/delhi_tech.jpg'],
        ['name' => 'Amity University Noida', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/amity.png'],
        ['name' => 'Subhaarti University', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/subharti.jpg'],
        ['name' => 'GNOIT Group of Institutions', 'city' => 'Delhi NCR', 'region' => 'Delhi', 'img' => 'assets/images/gniot.jpg'],
        ['name' => 'Chandigarh University', 'city' => 'Punjab', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/chandigarh.jpg'],
        ['name' => 'Chitkara University', 'city' => 'Punjab', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/chitkara.jpg'],
        ['name' => 'CGC Landran', 'city' => 'Punjab', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/cgc_landran.jpg'],
        ['name' => 'Maharishi Markandeshwar University', 'city' => 'Haryana', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/maharishi_markandeshwar.jpg'],
        ['name' => 'Om Sterling Global University', 'city' => 'Haryana', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/un/download (1).jpg'],
        ['name' => 'LPU Punjab', 'city' => 'Punjab', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/un/download (10).jpg'],
        ['name' => 'Thapar Institute of Engineering & Tech', 'city' => 'Punjab', 'region' => 'Haryana & Punjab', 'img' => 'assets/images/un/download (11).jpg'],
        ['name' => 'Graphic Era University', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (12).jpg'],
        ['name' => 'DIT University Dehradun', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (13).jpg'],
        ['name' => 'Quantum University', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (14).jpg'],
        ['name' => 'UPES Dehradun', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (15).jpg'],
        ['name' => 'Uttaranchal University', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (16).jpg'],
        ['name' => 'Dev Bhoomi Uttarakhand University', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (17).jpg'],
        ['name' => 'Tulas Institute', 'city' => 'Dehradun', 'region' => 'Dehradun', 'img' => 'assets/images/un/download (18).jpg'],
        ['name' => 'SRMIST University Chennai', 'city' => 'Chennai', 'region' => 'South India', 'img' => 'assets/images/un/download (19).jpg'],
        ['name' => 'Veltech University Chennai', 'city' => 'Chennai', 'region' => 'South India', 'img' => 'assets/images/un/download (2).jpg'],
        ['name' => 'SVCET Chittoor AP', 'city' => 'Andhra Pradesh', 'region' => 'South India', 'img' => 'assets/images/un/download (20).jpg'],
        ['name' => 'Amrita Vishwa Vidyapeetham', 'city' => 'Coimbatore', 'region' => 'South India', 'img' => 'assets/images/un/download (21).jpg'],
        ['name' => 'Aditya University AP', 'city' => 'Andhra Pradesh', 'region' => 'South India', 'img' => 'assets/images/un/download (22).jpg'],
        ['name' => 'Vellore Institute of Technology (VIT)', 'city' => 'Vellore', 'region' => 'South India', 'img' => 'assets/images/un/download (23).jpg'],
        ['name' => 'Shiv Nadar University', 'city' => 'Chennai', 'region' => 'South India', 'img' => 'assets/images/un/download (24).jpg'],
        ['name' => 'Chennai Institute of Technology', 'city' => 'Chennai', 'region' => 'South India', 'img' => 'assets/images/un/download (25).jpg'],
        ['name' => 'Sri Eshwar College of Engineering', 'city' => 'Coimbatore', 'region' => 'South India', 'img' => 'assets/images/un/download (26).jpg'],
        ['name' => 'KPR Institutions', 'city' => 'Coimbatore', 'region' => 'South India', 'img' => 'assets/images/un/download (3).jpg'],
        ['name' => 'BMS College of Engineering Bangalore', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (4).jpg'],
        ['name' => 'CMR Institute of Technology', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (5).jpg'],
        ['name' => 'East Point College of Engineering', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (6).jpg'],
        ['name' => 'Acharya Institutions Bangalore', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (7).jpg'],
        ['name' => 'Nitte Meenakshi Institute of Technology', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (8).jpg'],
        ['name' => 'Dr. Ambedkar Institute of Technology', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (9).jpg'],
        ['name' => 'Christ University', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download.jpg'],
        ['name' => 'M.S. Ramaiah Institute of Technology', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/images (1).jpg'],
        ['name' => 'Jain University', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/images (2).jpg'],
        ['name' => 'Krupanidhi Group of Institutions', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/images (3).jpg'],
        ['name' => 'Cambridge Institute of Technology', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/images (4).jpg'],
        ['name' => 'RR Group of Institutions', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/images (5).jpg'],
        ['name' => 'Bangalore Technological Institute', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/images.jpg'],
        ['name' => 'Aditya Nursing College', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (1).jpg'],
        ['name' => 'PES University', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (10).jpg'],
        ['name' => 'Padmashree Group of Institutions (Nursing)', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (11).jpg'],
        ['name' => 'SJES Group of Institutions', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (12).jpg'],
        ['name' => 'Aakash Group of Institutions', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (13).jpg'],
        ['name' => 'MNG Group of Institutions', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (14).jpg'],
        ['name' => 'Sri Siddhartha Medical College', 'city' => 'Tumkur', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (15).jpg'],
        ['name' => 'Siddaganga Medical College', 'city' => 'Tumkur', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (16).jpg'],
        ['name' => 'Hosmat Bethel College of Nursing', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (17).jpg'],
        ['name' => 'Shridevi College of Nursing', 'city' => 'Tumkur', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (18).jpg'],
        ['name' => 'RV University', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (19).jpg'],
        ['name' => 'Florence College of Nursing', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (2).jpg'],
        ['name' => 'New Horizon College of Engineering', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (20).jpg'],
        ['name' => 'AIMS Institutes', 'city' => 'Bangalore', 'region' => 'Bangalore', 'img' => 'assets/images/un/download (21).jpg'],
        ['name' => 'DY Patil University', 'city' => 'Mumbai', 'region' => 'Mumbai & Pune', 'img' => 'assets/images/un/download (22).jpg'],
        ['name' => 'SP Jain School of Global Management', 'city' => 'Mumbai', 'region' => 'Mumbai & Pune', 'img' => 'assets/images/un/download (23).jpg'],
        ['name' => 'Sandip University', 'city' => 'Nashik', 'region' => 'Mumbai & Pune', 'img' => 'assets/images/un/download (24).jpg'],
        ['name' => 'Symbiosis International University', 'city' => 'Pune', 'region' => 'Mumbai & Pune', 'img' => 'assets/images/un/download (25).jpg'],
        ['name' => 'MIT World Peace University', 'city' => 'Pune', 'region' => 'Mumbai & Pune', 'img' => 'assets/images/un/download (26).jpg'],
        ['name' => 'Pimpri Chinchwad University', 'city' => 'Pune', 'region' => 'Mumbai & Pune', 'img' => 'assets/images/un/download (3).jpg'],
        ['name' => 'Siksha \'O\' Anusandhan (SOA) University', 'city' => 'Bhubaneswar', 'region' => 'Bhubaneswar & East', 'img' => 'assets/images/un/download (4).jpg'],
        ['name' => 'KIIT University', 'city' => 'Bhubaneswar', 'region' => 'Bhubaneswar & East', 'img' => 'assets/images/un/download (5).jpg'],
        ['name' => 'GIET University', 'city' => 'Bhubaneswar', 'region' => 'Bhubaneswar & East', 'img' => 'assets/images/un/download (6).jpg'],
        ['name' => 'Sister Nivedita University (SNU)', 'city' => 'Kolkata', 'region' => 'Kolkata', 'img' => 'assets/images/un/download (7).jpg'],
        ['name' => 'Adamas University', 'city' => 'Kolkata', 'region' => 'Kolkata', 'img' => 'assets/images/un/download (8).jpg'],
        ['name' => 'IEM University', 'city' => 'Kolkata', 'region' => 'Kolkata', 'img' => 'assets/images/un/download (9).jpg'],
        ['name' => 'Ganpat University', 'city' => 'Gujarat', 'region' => 'Gujarat', 'img' => 'assets/images/un/download.jpg'],
        ['name' => 'Marwadi University', 'city' => 'Gujarat', 'region' => 'Gujarat', 'img' => 'assets/images/un/images (1).jpg'],
        ['name' => 'RK University', 'city' => 'Gujarat', 'region' => 'Gujarat', 'img' => 'assets/images/un/images (2).jpg'],
        ['name' => 'PP Savani University', 'city' => 'Gujarat', 'region' => 'Gujarat', 'img' => 'assets/images/un/images (3).jpg'],
        ['name' => 'Pandit Deendayal Energy University (PDEU)', 'city' => 'Gujarat', 'region' => 'Gujarat', 'img' => 'assets/images/un/images (4).jpg'],
        ['name' => 'Swarrnim Startup & Innovation University', 'city' => 'Gujarat', 'region' => 'Gujarat', 'img' => 'assets/images/un/images (5).jpg'],
        ['name' => 'Shoolini University', 'city' => 'Himachal', 'region' => 'Himachal & North', 'img' => 'assets/images/un/images.jpg'],
        ['name' => 'Assam Down Town University', 'city' => 'Assam', 'region' => 'East & North-East', 'img' => 'assets/images/un/download (1).jpg'],
        ['name' => 'Royal Global University', 'city' => 'Assam', 'region' => 'East & North-East', 'img' => 'assets/images/un/download (10).jpg'],
        ['name' => 'Sikkim Manipal University', 'city' => 'Sikkim', 'region' => 'East & North-East', 'img' => 'assets/images/un/download (11).jpg']
    ];

    $stmt = $pdo->prepare("INSERT INTO `institutions` (`name`, `city`, `region`, `img`, `is_top`) VALUES (?, ?, ?, ?, 1)");
    foreach ($defaultInstitutions as $uni) {
        $stmt->execute([$uni['name'], $uni['city'], $uni['region'], $uni['img']]);
    }
    echo "Default partner institutions seeded: " . count($defaultInstitutions) . " items.<br><br>";
    
    echo "<h3>Setup Completed Successfully!</h3>";
    
} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
