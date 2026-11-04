-- Krishna Education Foundation Study in India Database Schema
-- Ready for import into Hostinger / cPanel phpMyAdmin

DROP TABLE IF EXISTS `admin_users`;
DROP TABLE IF EXISTS `newsletter_subscribers`;
DROP TABLE IF EXISTS `inquiries`;
DROP TABLE IF EXISTS `enrollments`;
DROP TABLE IF EXISTS `institutions`;
DROP TABLE IF EXISTS `institution_pdfs`;
DROP TABLE IF EXISTS `bank_accounts`;
DROP TABLE IF EXISTS `site_settings`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `destinations`;
DROP TABLE IF EXISTS `course_streams`;
DROP TABLE IF EXISTS `guides`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `navigation_menu`;
DROP TABLE IF EXISTS `countries`;
DROP TABLE IF EXISTS `social_links`;
DROP TABLE IF EXISTS `fun_facts`;
DROP TABLE IF EXISTS `activity_log`;
DROP TABLE IF EXISTS `programs`;
DROP TABLE IF EXISTS `login_attempts`;

-- 1. Admin Users Table
CREATE TABLE `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed default admin account (admin@krishnaeducation.info / adminSecure2026!)
INSERT INTO `admin_users` (`email`, `password_hash`) VALUES
('admin@krishnaeducation.info', '$2y$10$e4/3wIE6LaBZVNmLIOT7beYwV5G8emOCE2JzEyRrBJHFs/5s4IE/y');

-- 2. Newsletter Subscribers Table
CREATE TABLE `newsletter_subscribers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Inquiries Table
CREATE TABLE `inquiries` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `phone_number` VARCHAR(30) NOT NULL,
    `address` VARCHAR(180) DEFAULT NULL,
    `country` VARCHAR(80) DEFAULT NULL,
    `course` VARCHAR(80) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `form_type` VARCHAR(50) DEFAULT 'contact_form',
    `status` VARCHAR(30) DEFAULT 'Pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Enrollments Table
CREATE TABLE `enrollments` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Institutions Table
CREATE TABLE `institutions` (
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
    `fee_document` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(20) DEFAULT 'Active',
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `institutions` (`id`, `name`, `city`, `region`, `img`, `fees`, `admission_process`, `location_detail`, `courses`, `is_top`, `status`, `display_order`) VALUES
(1, 'SRM Institute of Science and Technology', 'Chennai', 'South India', 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60', 'B.Tech: INR 2.5L - 4.5L/yr\nB.Sc Nursing: INR 1.5L/yr\nBBA: INR 1.2L/yr', '1. Online Application Form\n2. Document Evaluation (10+2 transcripts)\n3. Provisional Admission Letter\n4. Visa & Registration Guidance', 'SRM Nagar, Kattankulathur, Chennai, Tamil Nadu', '1,9,14,2', 1, 'Active', 1),
(2, 'Chandigarh University', 'Mohali / Chandigarh', 'North India', 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60', 'B.Tech: INR 1.8L - 2.8L/yr\nB.Pharm: INR 1.4L/yr\nB.Sc Nursing: INR 1.3L/yr\nBBA: INR 1.1L/yr', '1. Direct Counseling & Registration\n2. Merit Assessment\n3. Scholarship Allocation\n4. Final Admission Offer', 'NH-95, Chandigarh-Ludhiana Highway, Mohali, Punjab', '1,2,6,9,14', 1, 'Active', 2),
(3, 'Lovely Professional University (LPU)', 'Jalandhar', 'North India', 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&auto=format&fit=crop&q=60', 'B.Tech: INR 2.0L - 3.2L/yr\nB.Sc Agri: INR 1.2L/yr\nB.Pharm: INR 1.5L/yr\nMBA: INR 2.5L/yr', '1. Online Application with KEF\n2. LPUNEST / Merit Evaluation\n3. Scholarship Grant\n4. Seat Confirmation', 'Jalandhar - Delhi G.T. Road, Phagwara, Punjab', '1,2,4,6,9,14,16', 1, 'Active', 3),
(4, 'Parul University', 'Vadodara', 'West India', 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&auto=format&fit=crop&q=60', 'B.Sc Nursing: INR 1.2L/yr\nBDS: INR 3.5L/yr\nB.Tech: INR 1.5L/yr\nB.Pharm: INR 1.2L/yr', '1. Counseling & Eligibility Verification\n2. Document Submission\n3. Instant Admission Confirmation\n4. Hostel Booking', 'P.O. Limda, Ta. Waghodia, Dist. Vadodara, Gujarat', '1,2,3,6,8,9,14', 1, 'Active', 4),
(5, 'Sharda University', 'Greater Noida (Delhi NCR)', 'North India', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=60', 'B.Tech: INR 2.2L/yr\nB.Sc Nursing: INR 1.6L/yr\nBBA: INR 1.8L/yr\nBDS: INR 4.0L/yr', '1. Submit Academic Records\n2. Faculty Evaluation\n3. Scholarship Application\n4. Admission Letter Issued', 'Plot No. 32-34, Knowledge Park III, Greater Noida, UP', '1,2,3,9,10,14', 1, 'Active', 5),
(6, 'Bangalore Group of Institutions', 'Bangalore', 'South India', 'https://images.unsplash.com/photo-1527891751199-7225231a68dd?w=600&auto=format&fit=crop&q=60', 'B.Sc Nursing: INR 1.4L/yr\nB.Pharm: INR 1.3L/yr\nBPT: INR 1.5L/yr\nBMLT: INR 1.1L/yr', '1. Counseling with KEF advisors\n2. Clinical Affiliation Verification\n3. Direct Seat Allotment', 'Off Sarjapur Road, Bangalore, Karnataka', '2,6,8,9,14,18', 1, 'Active', 6),
(7, 'Manipal Academy of Higher Education', 'Manipal', 'South India', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&auto=format&fit=crop&q=60', 'B.Tech: INR 3.5L - 5.0L/yr\nMBBS: Contact for Package\nBDS: INR 4.5L/yr\nB.Pharm: INR 2.5L/yr', '1. Online Entrance / Counseling\n2. Merit Verification\n3. Admission Allotment', 'Tiger Circle Road, Madhav Nagar, Manipal, Karnataka', '1,2,3,9,10,14', 1, 'Active', 7),
(8, 'Alliance University', 'Bangalore', 'South India', 'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?w=600&auto=format&fit=crop&q=60', 'BBA: INR 2.0L/yr\nMBA: INR 4.5L/yr\nBA LLB: INR 2.5L/yr\nB.Tech: INR 2.5L/yr', '1. Aptitude & Personal Interview\n2. Academic Evaluation\n3. Offer Letter Release', 'Chikkahagade Cross, Chandapura - Anekal Main Road, Bangalore', '9,12,14,1', 0, 'Active', 8);

-- 6. Institution PDFs Table
CREATE TABLE `institution_pdfs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `institution_id` INT NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Bank Accounts Table
CREATE TABLE `bank_accounts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `bank_name` VARCHAR(150) NOT NULL,
    `account_name` VARCHAR(150) NOT NULL,
    `account_number` VARCHAR(50) NOT NULL,
    `branch` VARCHAR(150) DEFAULT NULL,
    `account_type` VARCHAR(50) DEFAULT 'Current Account',
    `swift_code` VARCHAR(50) DEFAULT NULL,
    `instructions` TEXT DEFAULT NULL,
    `remarks` TEXT DEFAULT NULL,
    `is_default` TINYINT DEFAULT 0,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `bank_accounts` (`bank_name`, `account_name`, `account_number`, `branch`, `account_type`, `instructions`, `is_default`, `display_order`, `is_active`) VALUES
('Siddhartha Bank Ltd.', 'KRISHNA EDUCATION FOUNDATION', '001150248239', 'Biratnagar Branch', 'Current Account', 'Please mention Student Name & College in remarks while transferring tuition fees.', 1, 1, 1),
('Global IME Bank', 'KRISHNA EDUCATION FOUNDATION', '04501010009842', 'Main Road Biratnagar', 'Current Account', 'Deposit slips can be verified immediately via WhatsApp: +977 9807765812', 0, 2, 1);

-- 8. Course Streams Table
CREATE TABLE `course_streams` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `icon` VARCHAR(50) DEFAULT 'fa-graduation-cap',
    `desc` TEXT DEFAULT NULL,
    `img` VARCHAR(255) DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `course_streams` (`id`, `name`, `icon`, `desc`, `img`, `display_order`) VALUES
(1, 'Engineering', 'fa-gear', 'B.Tech, M.Tech across CSE, AI, Mechanical, Civil and Electronics.', 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=400&auto=format&fit=crop&q=60', 1),
(2, 'Nursing', 'fa-user-nurse', 'B.Sc Nursing, Post Basic & M.Sc Nursing with hospital affiliations.', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&auto=format&fit=crop&q=60', 2),
(3, 'Dental', 'fa-tooth', 'BDS & MDS programs in recognized dental colleges.', 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=400&auto=format&fit=crop&q=60', 3),
(4, 'Agriculture', 'fa-seedling', 'B.Sc Agriculture & Forestry with practical farming training.', 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=400&auto=format&fit=crop&q=60', 4),
(5, 'Science', 'fa-flask', 'B.Sc Physics, Chemistry, Biotechnology and Microbiology.', 'https://images.unsplash.com/photo-1532094349884-543bc11b234d?w=400&auto=format&fit=crop&q=60', 5),
(6, 'Pharmacy', 'fa-pills', 'B.Pharm, Pharm.D and M.Pharm with PCI approved laboratories.', 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=400&auto=format&fit=crop&q=60', 6),
(7, 'Arts and Humanities', 'fa-palette', 'BA Economics, English, Psychology and Political Science.', 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=400&auto=format&fit=crop&q=60', 7),
(8, 'Paramedical', 'fa-stethoscope', 'Medical Lab Tech, Radiology, OT Tech and Physiotherapy.', 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&auto=format&fit=crop&q=60', 8),
(9, 'Management', 'fa-briefcase', 'BBA, MBA with industry specializations, internships and placements.', 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400&auto=format&fit=crop&q=60', 9),
(10, 'Medical', 'fa-heart-pulse', 'MBBS & MD programs in premier medical universities.', 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=400&auto=format&fit=crop&q=60', 10),
(11, 'Hotel Management', 'fa-hotel', 'BHM with international paid internships in luxury hotel chains.', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&auto=format&fit=crop&q=60', 11),
(12, 'Law', 'fa-scale-balanced', 'BA LLB, BBA LLB & LLM programs recognized by Bar Council.', 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=400&auto=format&fit=crop&q=60', 12),
(13, 'Media Studies & Communication', 'fa-bullhorn', 'BJMC, Mass Media, Journalism, Public Relations & Advertising.', 'https://images.unsplash.com/photo-1504711434969-e33886168f5c?w=400&auto=format&fit=crop&q=60', 13),
(14, 'Computer Application / IT', 'fa-laptop-code', 'BCA, MCA, Data Science, Cloud Computing and Cybersecurity.', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&auto=format&fit=crop&q=60', 14),
(15, 'Education', 'fa-book-open-reader', 'B.Ed & M.Ed programs with modern pedagogical training.', 'https://images.unsplash.com/photo-1509062522246-3755977927d7?w=400&auto=format&fit=crop&q=60', 15),
(16, 'Design', 'fa-pen-ruler', 'Fashion Design, Interior Design, UI/UX and Graphic Design.', 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=400&auto=format&fit=crop&q=60', 16),
(17, 'Behavioral and Social Science', 'fa-brain', 'Clinical Psychology, Counseling, Sociology & Social Work.', 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=400&auto=format&fit=crop&q=60', 17),
(18, 'Allied Health Science', 'fa-heart-pulse', 'BMLT, BPT, Medical Imaging, Anesthesia and OT Technology.', 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&auto=format&fit=crop&q=60', 18);

-- 9. Destinations Table
CREATE TABLE `destinations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `region` VARCHAR(100) NOT NULL,
    `img` VARCHAR(255) DEFAULT NULL,
    `about` TEXT DEFAULT NULL,
    `gallery` TEXT DEFAULT NULL,
    `map_link` TEXT DEFAULT NULL,
    `display_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `destinations` (`id`, `name`, `region`, `img`, `about`, `display_order`) VALUES
(14, 'Mumbai', 'West India', 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=600&auto=format&fit=crop&q=60', 'India financial capital and premier hub for media, arts, and business education.', 1),
(15, 'Delhi (Delhi NCR)', 'North India', 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=600&auto=format&fit=crop&q=60', 'India educational and technological epicenter with top central and private universities.', 2),
(16, 'Bangalore (Bengaluru)', 'South India', 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=600&auto=format&fit=crop&q=60', 'Silicon Valley of Asia, known for premier tech, medical, and nursing colleges.', 3),
(17, 'Pune', 'West India', 'https://images.unsplash.com/photo-1608976328267-e673d3ec06ce?w=600&auto=format&fit=crop&q=60', 'Oxford of the East with a vibrant student culture and research institutions.', 4),
(18, 'Chennai', 'South India', 'https://images.unsplash.com/photo-1582510003544-4d00b7f74220?w=600&auto=format&fit=crop&q=60', 'Major academic and manufacturing center with prestigious engineering and medical colleges.', 5),
(19, 'Hyderabad', 'South India', 'https://images.unsplash.com/photo-1605379399642-870262d3d051?w=600&auto=format&fit=crop&q=60', 'Cyberabad with world-class universities and biomedical research institutions.', 6),
(28, 'Chandigarh / Punjab', 'North India', 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60', 'Clean, modern education hub home to Chandigarh University and LPU.', 7);

-- 10. Guides Table
CREATE TABLE `guides` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `category` VARCHAR(100) DEFAULT 'Admission Guidance',
    `summary` TEXT DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL,
    `image_path` VARCHAR(255) DEFAULT NULL,
    `status` VARCHAR(20) DEFAULT 'published',
    `publish_date` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `guides` (`id`, `title`, `slug`, `category`, `summary`, `image_path`, `status`, `publish_date`) VALUES
(1, 'Complete Guide for Nepalese Students Studying in India (2026 Edition)', 'complete-guide-studying-in-india-2026', 'Higher Education', 'Explore everything you need to know about pursuing higher education in India: top universities, eligibility, scholarships, fees, and documentation.', 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=60', 'published', '2026-05-15'),
(2, 'Top 10 Scholarships in India for Nepalese Citizens', 'top-10-scholarships-nepali-students-india', 'Scholarships', 'A curated breakdown of merit-based, institutional, and Embassy scholarship opportunities available for Nepali scholars in Indian universities.', 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60', 'published', '2026-04-20'),
(3, 'B.Sc Nursing in India: Colleges, Inc Recognition & Admissions', 'bsc-nursing-in-india-guide', 'Medical & Nursing', 'Detailed walkthrough on INC recognized nursing colleges in Bangalore, Tamil Nadu, Punjab, and Delhi NCR with hostel facilities and clinical training.', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&auto=format&fit=crop&q=60', 'published', '2026-03-10');

-- 11. Navigation Menu Table
CREATE TABLE `navigation_menu` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `parent_id` INT DEFAULT NULL,
    `label` VARCHAR(100) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `display_order` INT DEFAULT 0,
    `is_visible` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `navigation_menu` (`id`, `parent_id`, `label`, `url`, `display_order`, `is_visible`) VALUES
(1, NULL, 'Home', 'index.php', 1, 1),
(2, NULL, 'Study in India', 'study-in-india.php', 2, 1),
(3, NULL, 'Institutions', 'cities.php', 3, 1),
(4, NULL, 'Courses', 'courses.php', 4, 1),
(5, NULL, 'Guides', 'guides.php', 5, 1),
(6, NULL, 'Study Abroad', '#', 6, 1),
(7, 6, 'Study in USA', 'study-abroad-detail.php?code=USA', 1, 1),
(8, 6, 'Study in UK', 'study-abroad-detail.php?code=UK', 2, 1),
(9, 6, 'Study in Canada', 'study-abroad-detail.php?code=CAN', 3, 1),
(10, 6, 'Study in Australia', 'study-abroad-detail.php?code=AUS', 4, 1),
(11, 6, 'All Destinations', 'study-abroad.php', 5, 1),
(12, NULL, 'Test Prep', 'test-preparation.php', 7, 1),
(13, NULL, 'Reviews', 'reviews.php', 8, 1);

-- 12. Reviews Table
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(120) NOT NULL,
    `email` VARCHAR(150) NOT NULL,
    `course_college` VARCHAR(180) DEFAULT NULL,
    `avatar` VARCHAR(255) DEFAULT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `title` VARCHAR(255) DEFAULT NULL,
    `comment` TEXT NOT NULL,
    `status` VARCHAR(20) DEFAULT 'Approved',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `reviews` (`id`, `name`, `email`, `course_college`, `avatar`, `rating`, `title`, `comment`, `status`) VALUES
(1, 'Yugandhar Yadav', 'yugandhar.yadav@example.com', 'B.Tech CSE (SRM University)', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80', 5, 'Exceptional Guidance & Support!', 'Krishna Education Foundation guided me through every step of my admission process to SRM University. They helped me secure a scholarship and made the documentation smooth and hassle-free!', 'Approved'),
(2, 'Aayush Shrestha', 'aayush.shrestha@example.com', 'B.Sc Nursing (Chandigarh University)', 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80', 5, 'Best Consultancy for India', 'I am currently studying Nursing in India thanks to KEF. Their admission advisors were available 24/7 to clear all my doubts about fees, hostel facilities, and visas.', 'Approved'),
(3, 'Pooja Karki', 'pooja.karki@example.com', 'BBA (Lovely Professional University)', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80', 5, 'Transparent & Very Helpful Team', 'Transparent advice without any hidden charges. Krishna Education Foundation made my dream of pursuing higher education in India a reality.', 'Approved'),
(4, 'Rohan Sah', 'rohan.sah@example.com', 'B.Pharm (Bangalore University)', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80', 5, 'Hassle-free Admission Process', 'Great experience! Got my college seat reserved quickly with full guidance regarding eligibility and document attestation.', 'Approved');

-- 13. Accreditations Table
CREATE TABLE `accreditations` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `category` VARCHAR(50) NOT NULL DEFAULT 'international',
    `logo` VARCHAR(255) NOT NULL,
    `display_order` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `accreditations` (`id`, `title`, `category`, `logo`, `display_order`, `is_active`) VALUES
(1, 'International Accreditation 1', 'international', 'int1.jpeg', 1, 1),
(2, 'International Accreditation 2', 'international', 'int2.jpeg', 2, 1),
(3, 'International Accreditation 3', 'international', 'int3.jpeg', 3, 1),
(4, 'Nepal Government / MOEST', 'national', 'nat1.jpeg', 1, 1),
(5, 'CTEVT Accreditation', 'national', 'nat2.jpeg', 2, 1),
(6, 'National Council Accreditation', 'national', 'nat3.jpeg', 3, 1);

-- 14. Site Settings Table
CREATE TABLE `site_settings` (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `site_settings` (`key`, `value`) VALUES
('site_name', 'Vision Point Educational Service'),
('site_logo', 'assets/images/logo1.png'),
('site_favicon', 'assets/images/img4.png'),
('hero_banner_image', 'assets/images/image1.png'),
('primary_color', '#0458A0'),
('primary_light', '#0365B5'),
('secondary_color', '#D91F23'),
('secondary_hover', '#C60F13'),
('site_title', 'Vision Point Educational Service | Study Abroad & India'),
('hero_title', 'Unlock Your Full\r\nEducational Potential in India'),
('hero_subtitle', 'Maximize your potential and reach new academic peaks with our expert guidance, university listings, and scholarships every step of the way.'),
('contact_phone', '+977 9807765812'),
('contact_email', 'info@krishnaeducation.com.np'),
('contact_location', 'Krishna Education Foundation, Vasundhara Complex, 12 Roadcess Chowk, Biratnagar 56613, Biratnagar, Nepal'),
('footer_about', 'Krishna Education Foundation is Nepal\'s premier choice for studying in India, offering expert admission guidance and scholarships for top-tier Indian universities.'),
('qr_code_enabled', '1'),
('qr_code_image', 'assets/images/siddhartha_qr.png'),
('qr_code_title', 'Fonepay / Mobile Banking QR'),
('qr_code_description', 'Scan with any mobile banking app, eSewa, Khalti, or Fonepay to pay tuition & application fees securely.');

-- 14. Rate Limiting Table
CREATE TABLE `login_attempts` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 15. SEO Pages Table
DROP TABLE IF EXISTS `seo_pages`;
CREATE TABLE `seo_pages` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `page_type` VARCHAR(100) DEFAULT 'static',
    `page_id` INT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `page_name` VARCHAR(255) NOT NULL,
    `meta_title` VARCHAR(255) NOT NULL,
    `meta_description` TEXT NOT NULL,
    `meta_keywords` TEXT DEFAULT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `seo_pages` (`page_type`, `page_id`, `slug`, `page_name`, `meta_title`, `meta_description`, `meta_keywords`, `h1`, `canonical_url`, `og_title`, `og_description`, `og_image`, `robots`, `schema_type`, `status`) VALUES
('static', NULL, 'home', 'Home', 'Education Consultancy in Nepal | Krishna Education Foundation', 'Krishna Education Foundation is the best education consultancy in Nepal and premier study abroad consultancy for India, Australia, UK, USA, and Canada admissions.', 'Education Consultancy in Nepal, Best education consultancy in Nepal, study abroad consultancy Nepal', 'Education Consultancy in Nepal', 'https://krishnaeducationfoundation.edu.np/', 'Education Consultancy in Nepal | Krishna Education Foundation', 'Best education consultancy in Nepal and study abroad consultancy for India, Australia, UK, USA, and Canada admissions.', 'assets/images/logo1.png', 'index,follow', 'EducationalOrganization', 1),
('static', NULL, 'about', 'About Us', 'About Krishna Education Foundation Nepal | Trusted Consultancy', 'Learn about Krishna Education Foundation Nepal, a certified education consultancy in Nepal providing trusted university counseling, scholarships, and visa support.', 'Krishna Education Foundation Nepal, education consultancy Nepal', 'About Krishna Education Foundation Nepal', 'https://krishnaeducationfoundation.edu.np/about/', 'About Krishna Education Foundation Nepal | Trusted Consultancy', 'Learn about Krishna Education Foundation Nepal, a certified education consultancy in Nepal providing trusted university counseling and scholarships.', 'assets/images/logo1.png', 'index,follow', 'AboutPage', 1),
('static', NULL, 'study-in-india', 'Study in India', 'Study in India from Nepal | Indian Universities & Admissions', 'Complete guide to study in India from Nepal. Explore top Indian universities for Nepali students, fees, direct admissions, and 100% scholarship opportunities.', 'Study in India from Nepal, Indian universities for Nepali students', 'Study in India from Nepal', 'https://krishnaeducationfoundation.edu.np/study-in-india/', 'Study in India from Nepal | Indian Universities & Admissions', 'Complete guide to study in India from Nepal with top Indian universities for Nepali students, fees, and scholarships.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'study-in-australia', 'Study in Australia', 'Study in Australia from Nepal | CRICOS Universities & Visas', 'Study in Australia from Nepal. Krishna Education Foundation is your Australia education consultancy Nepal for Genuine Student (GS) compliance, CRICOS, and visas.', 'Study in Australia from Nepal, Australia education consultancy Nepal', 'Study in Australia from Nepal', 'https://krishnaeducationfoundation.edu.np/study-in-australia/', 'Study in Australia from Nepal | CRICOS Universities & Visas', 'Study in Australia from Nepal with premier Australia education consultancy Nepal support for Genuine Student criteria and visa processing.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'study-in-uk', 'Study in UK', 'Study in UK from Nepal | 1-Year Masters, CAS & UK Visas', 'Plan to study in UK from Nepal with Krishna Education Foundation, top UK education consultancy Nepal guiding students on CAS processing and Graduate Route visas.', 'Study in UK from Nepal, UK education consultancy Nepal', 'Study in UK from Nepal', 'https://krishnaeducationfoundation.edu.np/study-in-uk/', 'Study in UK from Nepal | 1-Year Masters, CAS & UK Visas', 'Expert guidance to study in UK from Nepal from trusted UK education consultancy Nepal advisors.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'study-in-usa', 'Study in USA', 'Study in USA from Nepal | Universities, F-1 Visa & OPT', 'Study in USA from Nepal. Leading USA education consultancy Nepal offering comprehensive F-1 visa preparation, STEM OPT guidance, and university scholarships.', 'Study in USA from Nepal, USA education consultancy Nepal', 'Study in USA from Nepal', 'https://krishnaeducationfoundation.edu.np/study-in-usa/', 'Study in USA from Nepal | Universities, F-1 Visa & OPT', 'Study in USA from Nepal with USA education consultancy Nepal counseling, F-1 visa coaching, and scholarships.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'study-in-canada', 'Study in Canada', 'Study in Canada from Nepal | SDS Colleges, GIC & Visas', 'Study in Canada from Nepal. Leading Canada education consultancy in Nepal for SDS stream, DLI public universities, GIC opening, and PGWP work permits.', 'Study in Canada from Nepal, Canada education consultancy Nepal, SDS Canada Nepal', 'Study in Canada from Nepal', 'https://krishnaeducationfoundation.edu.np/study-in-canada/', 'Study in Canada from Nepal | SDS Colleges, GIC & Visas', 'Comprehensive guidance to study in Canada from Nepal with SDS fast-track visa processing and DLI colleges.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'ielts', 'IELTS Preparation', 'IELTS Preparation in Nepal | Expert Coaching & Classes', 'Join top IELTS preparation in Nepal at Krishna Education Foundation. Intensive IELTS classes for high band scores across Listening, Reading, Writing, & Speaking.', 'IELTS preparation in Nepal, IELTS classes', 'IELTS Preparation in Nepal', 'https://krishnaeducationfoundation.edu.np/ielts/', 'IELTS Preparation in Nepal | Expert Coaching & Classes', 'Best IELTS preparation in Nepal with structured IELTS classes, mock tests, and British Council certified instructors.', 'assets/images/logo1.png', 'index,follow', 'Course', 1),
('static', NULL, 'pte', 'PTE Preparation', 'PTE Preparation in Nepal | Computer Mock Tests & Classes', 'Score 79+ with PTE preparation in Nepal. Join interactive PTE classes at Krishna Education Foundation featuring AI computer mock labs, practice drills, and tips.', 'PTE preparation in Nepal, PTE classes', 'PTE Preparation in Nepal', 'https://krishnaeducationfoundation.edu.np/pte/', 'PTE Preparation in Nepal | Computer Mock Tests & Classes', 'PTE preparation in Nepal with computerized mock testing and expert PTE classes for study abroad aspirants.', 'assets/images/logo1.png', 'index,follow', 'Course', 1),
('static', NULL, 'cee-preparation', 'CEE Preparation', 'CEE Preparation in Nepal | Medical Entrance Coaching', 'Crack MEC exam with CEE preparation in Nepal at Krishna Education Foundation. Proven CEE entrance preparation classes, mock tests, and expert faculty mentorship.', 'CEE preparation in Nepal, CEE entrance preparation', 'CEE Preparation in Nepal', 'https://krishnaeducationfoundation.edu.np/cee-preparation/', 'CEE Preparation in Nepal | Medical Entrance Coaching', 'Achieve top medical entrance ranks with CEE preparation in Nepal and comprehensive CEE entrance preparation classes.', 'assets/images/logo1.png', 'index,follow', 'Course', 1),
('static', NULL, 'ctevt-preparation', 'CTEVT Preparation', 'CTEVT Preparation in Nepal | Diploma Entrance Classes', 'Best CTEVT preparation in Nepal for HA, PCL Nursing, Pharmacy, and Engineering. Intensive CTEVT entrance preparation classes with model question sets and notes.', 'CTEVT preparation in Nepal, CTEVT entrance preparation', 'CTEVT Preparation in Nepal', 'https://krishnaeducationfoundation.edu.np/ctevt-preparation/', 'CTEVT Preparation in Nepal | Diploma Entrance Classes', 'Premier CTEVT preparation in Nepal with model exam series for CTEVT entrance preparation.', 'assets/images/logo1.png', 'index,follow', 'Course', 1),
('static', NULL, 'universities', 'Universities for Nepali Students', 'Universities for Nepali Students | Partner Institutions', 'Discover premier universities for Nepali students in India, UK, USA, Australia, and Canada. Filter top study abroad universities by region, fees, and rankings.', 'Universities for Nepali students, study abroad universities', 'Universities for Nepali Students', 'https://krishnaeducationfoundation.edu.np/universities/', 'Universities for Nepali Students | Partner Institutions', 'Explore verified universities for Nepali students and apply directly to top study abroad universities.', 'assets/images/logo1.png', 'index,follow', 'EducationalOrganization', 1),
('static', NULL, 'contact', 'Contact Us', 'Education Consultancy in Biratnagar Nepal | Contact KEF', 'Looking for an education counsellor near me? Visit Krishna Education Foundation, leading education consultancy in Biratnagar Nepal at Roadcess Chowk for guidance.', 'Education consultancy in Biratnagar Nepal, education counsellor near me', 'Education Consultancy in Biratnagar Nepal', 'https://krishnaeducationfoundation.edu.np/contact/', 'Education Consultancy in Biratnagar Nepal | Contact KEF', 'Contact top education consultancy in Biratnagar Nepal at Roadcess Chowk. Connect with an expert education counsellor near me.', 'assets/images/logo1.png', 'index,follow', 'ContactPage', 1),
('static', NULL, 'scholarships', 'Scholarships', 'Scholarships for Nepali Students in India & Abroad', 'Find merit, government, and bilateral scholarships for Nepali students in India and top foreign destinations. Apply for 100% tuition fee waivers.', 'Scholarships for Nepali students, study in india scholarships, COMPEX scholarship nepal', 'Scholarships for Nepali Students', 'https://krishnaeducationfoundation.edu.np/scholarships/', 'Scholarships for Nepali Students in India & Abroad', 'Explore scholarship opportunities for Nepali students in India and abroad.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'visa-guidance', 'Visa Guidance', 'Student Visa Guidance & Mock Interview Coaching Nepal', 'Expert student visa guidance in Nepal. Get comprehensive document checklists, financial verification guidance, and one-on-one mock interview preparation.', 'Student visa guidance Nepal, student visa interview coaching, visa documentation nepal', 'Student Visa Guidance & Interview Coaching', 'https://krishnaeducationfoundation.edu.np/visa-guidance/', 'Student Visa Guidance & Mock Interview Coaching Nepal', 'Comprehensive student visa guidance and embassy interview preparation.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'career-counselling', 'Career Counselling', 'Career Counselling in Nepal for Students After SEE & +2', 'Personalized career counselling in Nepal at Krishna Education Foundation. Guidance for students after SEE, +2 Science, Management, and Bachelor graduates.', 'Career counselling in Nepal, education counseling after +2 nepal, stream selection nepal', 'Career Counselling in Nepal', 'https://krishnaeducationfoundation.edu.np/career-counselling/', 'Career Counselling in Nepal for Students After SEE & +2', 'Get 1-on-1 career counselling in Nepal for stream and course selection.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'services', 'Our Services', 'Educational Consultancy Services in Nepal | Krishna Education', 'Explore complete educational consultancy services in Nepal: University shortlisting, scholarship grants, documentation attestation, and visa guidance.', 'Educational consultancy services in Nepal, study abroad services, admission processing nepal', 'Our Consultancy Services', 'https://krishnaeducationfoundation.edu.np/services/', 'Educational Consultancy Services in Nepal | Krishna Education', 'Comprehensive education consultancy services for Nepalese scholars.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1),
('static', NULL, 'guides', 'Blog & Guides', 'Education Guides & Study Abroad Articles for Nepali Students', 'Read expert education guides, admission roadmaps, scholarship announcements, and regulatory notices for Nepalese students planning to study in India & abroad.', 'Education guides nepal, study abroad blog nepal, indian university admission tips', 'Education Guides & Resources', 'https://krishnaeducationfoundation.edu.np/guides/', 'Education Guides & Study Abroad Articles for Nepali Students', 'Expert education guides and university roadmaps for Nepalese scholars.', 'assets/images/logo1.png', 'index,follow', 'WebPage', 1);

-- 16. SEO Redirects Table
DROP TABLE IF EXISTS `seo_redirects`;
CREATE TABLE `seo_redirects` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `source_url` VARCHAR(255) NOT NULL UNIQUE,
    `target_url` VARCHAR(255) NOT NULL,
    `status_code` INT DEFAULT 301,
    `hit_count` INT DEFAULT 0,
    `is_active` TINYINT DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_source` (`source_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

