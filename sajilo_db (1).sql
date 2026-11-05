-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2026 at 07:18 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sajilo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `admin_email` varchar(150) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `email`, `password_hash`, `created_at`) VALUES
(1, 'admin@visionpoint.edu.np', '$2y$10$F6MrsxYw2Af/P8ZBz9O4JesQ3zXGoA1KJSffZD.7MO.TTjv0nTsR6', '2026-06-27 19:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `is_active`, `display_order`) VALUES
(1, 'India', 1, 1),
(2, 'US', 1, 2),
(3, 'Canada', 1, 3),
(4, 'UK', 1, 4),
(5, 'Australia', 1, 5),
(6, 'Bangladesh', 1, 6),
(7, 'Europe', 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `course_streams`
--

CREATE TABLE `course_streams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT 'fa-graduation-cap',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_streams`
--

INSERT INTO `course_streams` (`id`, `name`, `icon`, `display_order`, `created_at`) VALUES
(1, 'Nursing', 'fa-user-nurse', 1, '2026-06-28 12:29:13'),
(2, 'Engineering', 'fa-gears', 2, '2026-06-28 12:29:13'),
(3, 'Pharmacy', 'fa-pills', 3, '2026-06-28 12:29:13'),
(4, 'Management', 'fa-briefcase', 4, '2026-06-28 12:29:13'),
(5, 'Computer Application / IT', 'fa-laptop-code', 5, '2026-06-28 12:29:13'),
(6, 'Dental', 'fa-tooth', 6, '2026-06-28 12:29:13'),
(7, 'Behavioral and Social Science', 'fa-people-group', 7, '2026-06-28 12:29:13'),
(8, 'Allied Health Science', 'fa-heart-pulse', 8, '2026-06-28 12:29:13'),
(9, 'Agriculture', 'fa-wheat-awn', 9, '2026-06-28 12:29:13'),
(10, 'Medical', 'fa-stethoscope', 10, '2026-06-28 12:29:13'),
(11, 'Design', 'fa-compass-drafting', 11, '2026-06-28 12:29:13'),
(12, 'Paramedical', 'fa-kit-medical', 12, '2026-06-28 12:29:13'),
(13, 'Arts and Humanities', 'fa-palette', 13, '2026-06-28 12:29:13'),
(14, 'Media Studies & Communication', 'fa-bullhorn', 14, '2026-06-28 12:29:13'),
(15, 'Science', 'fa-microscope', 15, '2026-06-28 12:29:13'),
(16, 'Hotel Management', 'fa-bell-concierge', 16, '2026-06-28 12:29:13'),
(17, 'Law', 'fa-scale-balanced', 17, '2026-06-28 12:29:13'),
(18, 'Education', 'fa-chalkboard-user', 18, '2026-06-28 12:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `destinations`
--

CREATE TABLE `destinations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `region` varchar(100) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `destinations`
--

INSERT INTO `destinations` (`id`, `name`, `region`, `img`, `display_order`, `created_at`) VALUES
(1, 'Delhi (Delhi NCR)', 'Delhi', 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=500&auto=format&fit=crop&q=60', 1, '2026-06-28 12:29:13'),
(2, 'Bangalore (Bengaluru)', 'Bangalore', 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60', 2, '2026-06-28 12:29:13'),
(3, 'Mumbai', 'Mumbai & Pune', 'https://images.unsplash.com/photo-1566552881560-0be862a7c445?w=500&auto=format&fit=crop&q=60', 3, '2026-06-28 12:29:13'),
(4, 'Coimbatore', 'South India', 'assets/images/coimbatore.jpg', 4, '2026-06-28 12:29:13'),
(5, 'Bhubaneswar', 'Bhubaneswar & East', 'assets/images/bhubaneswar.jpg', 5, '2026-06-28 12:29:13'),
(6, 'Visakhapatnam', 'South India', 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=500&auto=format&fit=crop&q=60', 6, '2026-06-28 12:29:13'),
(7, 'Shimla', 'Himachal & North', 'https://images.unsplash.com/photo-1562608264-a690e7552aa9?w=500&auto=format&fit=crop&q=60', 7, '2026-06-28 12:29:13'),
(8, 'Punjab', 'Haryana & Punjab', 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=500&auto=format&fit=crop&q=60', 8, '2026-06-28 12:29:13'),
(9, 'Chandigarh', 'Haryana & Punjab', 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=500&auto=format&fit=crop&q=60', 9, '2026-06-28 12:29:13'),
(10, 'Nagpur', 'Mumbai & Pune', 'https://images.unsplash.com/photo-1566552881560-0be862a7c445?w=500&auto=format&fit=crop&q=60', 10, '2026-06-28 12:29:13'),
(11, 'Kolkata', 'Kolkata', 'https://images.unsplash.com/photo-1558431382-27e303142255?w=500&auto=format&fit=crop&q=60', 11, '2026-06-28 12:29:13'),
(12, 'Dehradun', 'Dehradun', 'https://images.unsplash.com/photo-1589136777351-fdc9c9c8c482?w=500&auto=format&fit=crop&q=60', 12, '2026-06-28 12:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `address` varchar(180) DEFAULT NULL,
  `level` varchar(80) DEFAULT NULL,
  `interested_city` varchar(80) DEFAULT NULL,
  `interested_college` varchar(120) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `interested_program` varchar(100) DEFAULT NULL,
  `highest_education` varchar(80) DEFAULT NULL,
  `percentage` varchar(30) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fun_facts`
--

CREATE TABLE `fun_facts` (
  `id` int(11) NOT NULL,
  `number` varchar(50) NOT NULL,
  `label` varchar(150) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fun_facts`
--

INSERT INTO `fun_facts` (`id`, `number`, `label`, `icon`, `display_order`) VALUES
(1, '123+', 'Admissions Seeded', 'fa-graduation-cap', 1),
(2, '80%', 'Scholarship Success', 'fa-award', 2),
(3, '95%', 'Satisfaction Rate', 'fa-smile', 3);

-- --------------------------------------------------------

--
-- Table structure for table `guides`
--

CREATE TABLE `guides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `guides`
--

INSERT INTO `guides` (`id`, `title`, `slug`, `summary`, `content`, `image_path`, `publish_date`, `status`, `created_at`) VALUES
(1, 'Best Nursing Colleges in Bangalore', 'best-nursing-colleges-in-bangalore', 'Explore the leading Nursing institutes in Bangalore recognized by INC, detailing admission options, fees, and clinical internship setups.', '<p>Bangalore is widely known as a hub for Nursing education in India, attracting thousands of students from Nepal annually. Key factors driving this trend include state-of-the-art hospitals, INC (Indian Nursing Council) certifications, and robust clinical internship environments.</p><p>Top institutions like Krupanidhi College of Nursing, Narayana Health, and St. John\'s offer comprehensive degrees with excellent placement facilities. Contact our counselors to find the best seat options and available scholarships.</p>', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&auto=format&fit=crop&q=60', '2025-11-11', 'published', '2026-06-28 12:29:13'),
(2, 'B.Sc. Nursing in Bangalore', 'bsc-nursing-in-bangalore', 'A comprehensive guide on pursuing a 4-year B.Sc. Nursing in Bangalore, including eligibility criteria, state board registry, and placements.', '<p>A 4-year B.Sc. Nursing program in Bangalore provides students with professional training in anatomy, physiology, critical care, and community health. Graduates are registered under the Karnataka State Nursing Council, which enables them to work worldwide.</p><p>Eligibility requires 50% in +2 Science (Biology, Chemistry, Physics). Our consultancy offers direct admissions, hostel booking help, and up to 50% tuition waivers for eligible students.</p>', 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?w=600&auto=format&fit=crop&q=60', '2025-08-03', 'published', '2026-06-28 12:29:13'),
(3, 'Study Nursing in Bangalore', 'study-nursing-in-bangalore', 'Why Bangalore remains the hotbed for nursing education for Nepali students, highlighting budget accommodations and language transition.', '<p>For Nepalese students, Bangalore offers a comfortable blend of high academic quality and simple language transitions, as English and Hindi are widely understood. Accommodations near colleges range from college hostels to shared PG (paying guest) rooms, which are very budget-friendly.</p>', 'https://images.unsplash.com/photo-1516549655169-df83a0774514?w=600&auto=format&fit=crop&q=60', '2025-06-30', 'published', '2026-06-28 12:29:13'),
(4, 'Study in India Scholarships: A Comprehensive Guide for Nepali Students in 2026', 'study-in-india-scholarships-2026', 'Everything you need to know about applying for government and university merit scholarships when studying in India.', '<p>India offers numerous scholarships for international and Nepalese students through the COMPEX scheme, ICCR schemes, and direct university merit tie-ups. Learn how to draft a stellar application, submit transcripts, and negotiate fee waivers of up to 100%.</p>', 'assets/images/banner.png', '2025-08-24', 'published', '2026-06-28 12:29:13'),
(5, 'GNM Nursing at East Point College of Nursing', 'gnm-nursing-east-point', 'Deep dive into East Point College of Nursing GNM course: eligibility, hostel environment, and clinical rotations.', '<p>General Nursing and Midwifery (GNM) is a 3-year diploma course. East Point College of Nursing offers excellent infrastructure, a 1000+ bed parent hospital, and an active campus life. Ideal for students looking for professional entry-level clinical options in Bangalore.</p>', 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&auto=format&fit=crop&q=60', '2025-11-10', 'published', '2026-06-28 12:29:13'),
(6, 'CSE in India: Colleges and Careers', 'cse-in-india-colleges-careers', 'Guide to Computer Science Engineering (CSE) in top Indian universities, focusing on campus recruitments and skill-building cycles.', '<p>Computer Science Engineering continues to be a top career path. Learn about top-ranking universities in India, including Sharda University, Galgotias University, and Chandigarh University, which offer specialization programs in Artificial Intelligence, Machine Learning, and Cybersecurity.</p>', 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&auto=format&fit=crop&q=60', '2025-10-13', 'published', '2026-06-28 12:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `id` int(11) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone_number` varchar(30) NOT NULL,
  `address` varchar(180) DEFAULT NULL,
  `country` varchar(80) DEFAULT NULL,
  `course` varchar(80) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `form_type` varchar(50) DEFAULT 'contact_form',
  `status` varchar(30) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

CREATE TABLE `institutions` (
  `id` int(11) NOT NULL,
  `name` varchar(180) NOT NULL,
  `city` varchar(100) NOT NULL,
  `region` varchar(100) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `fees` text DEFAULT NULL,
  `admission_process` text DEFAULT NULL,
  `location_detail` text DEFAULT NULL,
  `courses` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `city`, `region`, `img`, `created_at`) VALUES
(1, 'Galgotias University Delhi', 'Delhi NCR', 'Delhi', 'assets/images/galgotias.jpg', '2026-06-27 19:12:10'),
(2, 'Sharda University- Delhi', 'Delhi NCR', 'Delhi', 'assets/images/sharda.jpg', '2026-06-27 19:12:10'),
(3, 'Noida International University', 'Delhi NCR', 'Delhi', 'assets/images/noida_international.jpg', '2026-06-27 19:12:10'),
(4, 'Manav Rachana University', 'Delhi NCR', 'Delhi', 'assets/images/manav_rachana.jpg', '2026-06-27 19:12:10'),
(5, 'Delhi Technological University', 'Delhi NCR', 'Delhi', 'assets/images/delhi_tech.jpg', '2026-06-27 19:12:10'),
(6, 'Amity University Noida', 'Delhi NCR', 'Delhi', 'assets/images/amity.png', '2026-06-27 19:12:10'),
(7, 'Subhaarti University', 'Delhi NCR', 'Delhi', 'assets/images/subharti.jpg', '2026-06-27 19:12:10'),
(8, 'GNOIT Group of Institutions', 'Delhi NCR', 'Delhi', 'assets/images/gniot.jpg', '2026-06-27 19:12:10'),
(9, 'Chandigarh University', 'Punjab', 'Haryana & Punjab', 'assets/images/chandigarh.jpg', '2026-06-27 19:12:10'),
(10, 'Chitkara University', 'Punjab', 'Haryana & Punjab', 'assets/images/chitkara.jpg', '2026-06-27 19:12:10'),
(11, 'CGC Landran', 'Punjab', 'Haryana & Punjab', 'assets/images/cgc_landran.jpg', '2026-06-27 19:12:10'),
(12, 'Maharishi Markandeshwar University', 'Haryana', 'Haryana & Punjab', 'assets/images/maharishi_markandeshwar.jpg', '2026-06-27 19:12:10'),
(13, 'Om Sterling Global University', 'Haryana', 'Haryana & Punjab', '', '2026-06-27 19:12:10'),
(14, 'LPU Punjab', 'Punjab', 'Haryana & Punjab', '', '2026-06-27 19:12:10'),
(15, 'Thapar Institute of Engineering & Tech', 'Punjab', 'Haryana & Punjab', '', '2026-06-27 19:12:10'),
(16, 'Graphic Era University', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(17, 'DIT University Dehradun', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(18, 'Quantum University', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(19, 'UPES Dehradun', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(20, 'Uttaranchal University', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(21, 'Dev Bhoomi Uttarakhand University', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(22, 'Tulas Institute', 'Dehradun', 'Dehradun', '', '2026-06-27 19:12:10'),
(23, 'SRMIST University Chennai', 'Chennai', 'South India', '', '2026-06-27 19:12:10'),
(24, 'Veltech University Chennai', 'Chennai', 'South India', '', '2026-06-27 19:12:10'),
(25, 'SVCET Chittoor AP', 'Andhra Pradesh', 'South India', '', '2026-06-27 19:12:10'),
(26, 'Amrita Vishwa Vidyapeetham', 'Coimbatore', 'South India', '', '2026-06-27 19:12:10'),
(27, 'Aditya University AP', 'Andhra Pradesh', 'South India', '', '2026-06-27 19:12:10'),
(28, 'Vellore Institute of Technology (VIT)', 'Vellore', 'South India', '', '2026-06-27 19:12:10'),
(29, 'Shiv Nadar University', 'Chennai', 'South India', '', '2026-06-27 19:12:10'),
(30, 'Chennai Institute of Technology', 'Chennai', 'South India', '', '2026-06-27 19:12:10'),
(31, 'Sri Eshwar College of Engineering', 'Coimbatore', 'South India', '', '2026-06-27 19:12:10'),
(32, 'KPR Institutions', 'Coimbatore', 'South India', '', '2026-06-27 19:12:10'),
(33, 'BMS College of Engineering Bangalore', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(34, 'CMR Institute of Technology', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(35, 'East Point College of Engineering', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(36, 'Acharya Institutions Bangalore', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(37, 'Nitte Meenakshi Institute of Technology', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(38, 'Dr. Ambedkar Institute of Technology', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(39, 'Christ University', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(40, 'M.S. Ramaiah Institute of Technology', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(41, 'Jain University', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(42, 'Krupanidhi Group of Institutions', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(43, 'Cambridge Institute of Technology', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(44, 'RR Group of Institutions', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(45, 'Bangalore Technological Institute', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(46, 'Aditya Nursing College', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(47, 'PES University', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(48, 'Padmashree Group of Institutions (Nursing)', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(49, 'SJES Group of Institutions', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(50, 'Aakash Group of Institutions', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(51, 'MNG Group of Institutions', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(52, 'Sri Siddhartha Medical College', 'Tumkur', 'Bangalore', '', '2026-06-27 19:12:10'),
(53, 'Siddaganga Medical College', 'Tumkur', 'Bangalore', '', '2026-06-27 19:12:10'),
(54, 'Hosmat Bethel College of Nursing', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(55, 'Shridevi College of Nursing', 'Tumkur', 'Bangalore', '', '2026-06-27 19:12:10'),
(56, 'RV University', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(57, 'Florence College of Nursing', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(58, 'New Horizon College of Engineering', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(59, 'AIMS Institutes', 'Bangalore', 'Bangalore', '', '2026-06-27 19:12:10'),
(60, 'DY Patil University', 'Mumbai', 'Mumbai & Pune', '', '2026-06-27 19:12:10'),
(61, 'SP Jain School of Global Management', 'Mumbai', 'Mumbai & Pune', '', '2026-06-27 19:12:10'),
(62, 'Sandip University', 'Nashik', 'Mumbai & Pune', '', '2026-06-27 19:12:10'),
(63, 'Symbiosis International University', 'Pune', 'Mumbai & Pune', '', '2026-06-27 19:12:10'),
(64, 'MIT World Peace University', 'Pune', 'Mumbai & Pune', '', '2026-06-27 19:12:10'),
(65, 'Pimpri Chinchwad University', 'Pune', 'Mumbai & Pune', '', '2026-06-27 19:12:10'),
(66, 'Siksha \'O\' Anusandhan (SOA) University', 'Bhubaneswar', 'Bhubaneswar & East', '', '2026-06-27 19:12:10'),
(67, 'KIIT University', 'Bhubaneswar', 'Bhubaneswar & East', '', '2026-06-27 19:12:10'),
(68, 'GIET University', 'Bhubaneswar', 'Bhubaneswar & East', '', '2026-06-27 19:12:10'),
(69, 'Sister Nivedita University (SNU)', 'Kolkata', 'Kolkata', '', '2026-06-27 19:12:10'),
(70, 'Adamas University', 'Kolkata', 'Kolkata', '', '2026-06-27 19:12:10'),
(71, 'IEM University', 'Kolkata', 'Kolkata', '', '2026-06-27 19:12:10'),
(72, 'Ganpat University', 'Gujarat', 'Gujarat', '', '2026-06-27 19:12:10'),
(73, 'Marwadi University', 'Gujarat', 'Gujarat', '', '2026-06-27 19:12:10'),
(74, 'RK University', 'Gujarat', 'Gujarat', '', '2026-06-27 19:12:10'),
(75, 'PP Savani University', 'Gujarat', 'Gujarat', '', '2026-06-27 19:12:10'),
(76, 'Pandit Deendayal Energy University (PDEU)', 'Gujarat', 'Gujarat', '', '2026-06-27 19:12:10'),
(77, 'Swarrnim Startup & Innovation University', 'Gujarat', 'Gujarat', '', '2026-06-27 19:12:10'),
(78, 'Shoolini University', 'Himachal', 'Himachal & North', '', '2026-06-27 19:12:10'),
(79, 'Assam Down Town University', 'Assam', 'East & North-East', '', '2026-06-27 19:12:10'),
(80, 'Royal Global University', 'Assam', 'East & North-East', '', '2026-06-27 19:12:10'),
(81, 'Sikkim Manipal University', 'Sikkim', 'East & North-East', '', '2026-06-27 19:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `navigation_menu`
--

CREATE TABLE `navigation_menu` (
  `id` int(11) NOT NULL,
  `label` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_visible` tinyint(4) DEFAULT 1,
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `navigation_menu`
--

INSERT INTO `navigation_menu` (`id`, `label`, `url`, `display_order`, `is_visible`, `parent_id`) VALUES
(1, 'Home', 'index.php', 1, 1, NULL),
(2, 'Study in India', 'study-in-india.php', 2, 1, NULL),
(3, 'Institutions', 'cities.php', 3, 1, NULL),
(4, 'Courses', 'courses.php', 4, 1, NULL),
(5, 'Guide', 'guides.php', 5, 1, NULL),
(6, 'Study Abroad', 'study-abroad.php', 6, 1, NULL),
(7, 'Test preparation', '#', 7, 1, NULL),
(8, 'IELTS', 'test-preparation.php?test=ielts', 1, 1, 7),
(9, 'PTE', 'test-preparation.php?test=pte', 2, 1, 7),
(10, 'TOEFL', 'test-preparation.php?test=toefl', 3, 1, 7),
(11, 'SAT', 'test-preparation.php?test=sat', 4, 1, 7);

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `hero_title` varchar(255) DEFAULT NULL,
  `hero_subtitle` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `hero_title`, `hero_subtitle`, `content`, `updated_at`) VALUES
(1, 'study-in-india', 'Study in India', 'Study in India', 'Explore all the premium universities and colleges in India where we facilitate direct admission and scholarships.', '{\"why_cards\":[{\"title\":\"Recognized Degrees\",\"icon\":\"fa-certificate\",\"text\":\"Degrees from Indian universities are globally recognized, and directly approved by major councils in Nepal (NMC, NEC, NNC, NPC).\"},{\"title\":\"Affordable Costs\",\"icon\":\"fa-sack-dollar\",\"text\":\"Get quality global education at a fraction of the cost of Western countries, with budget-friendly tuition fees and cost of living.\"},{\"title\":\"Cultural Proximity\",\"icon\":\"fa-handshake\",\"text\":\"Familiar culture, climate, and language make it incredibly easy for Nepalese students to adapt and settle in.\"},{\"title\":\"Scholarship Options\",\"icon\":\"fa-graduation-cap\",\"text\":\"Vast scholarships matching up to 50%-100% tuition coverage for students with strong academic scores.\"}],\"process_steps\":[{\"title\":\"Free Counselling\",\"text\":\"Consult with our counselors to select the right course, university, and state.\"},{\"title\":\"Eligibility Check\",\"text\":\"Submit your transcripts (+2\\/Diploma\\/Bachelor) for grade verification.\"},{\"title\":\"Scholarship Allocation\",\"text\":\"Apply for available consultancy and university merit scholarships.\"},{\"title\":\"Visa & Travel Guidance\",\"text\":\"Receive help with travel arrangements, border protocols, and hostel allocation.\"}]}', '2026-06-28 12:29:13'),
(2, 'study-abroad', 'Study Abroad', 'Study Abroad', 'Launch your global career by securing admissions in top universities and colleges across the world. Discover high-quality learning, multicultural campuses, and immense post-study work opportunities.', '{\"benefits\":[{\"title\":\"Academic Excellence\",\"icon\":\"fa-graduation-cap\",\"text\":\"Gain access to advanced curriculums, cutting-edge labs, and highly distinguished faculty members driving innovation.\"},{\"title\":\"Global Employability\",\"icon\":\"fa-briefcase\",\"text\":\"Foreign degrees hold massive weight with multinational corporations, equipping you for top salaries and career paths.\"},{\"title\":\"Cross-Cultural Exposure\",\"icon\":\"fa-globe\",\"text\":\"Build lifelong connections with peers from all continents, developing a truly global outlook and independent life skills.\"},{\"title\":\"Language & Growth\",\"icon\":\"fa-language\",\"text\":\"Polish your language fluency while gaining standard adaptability traits highly sought in competitive industries.\"}],\"destinations\":[{\"country\":\"United States of America\",\"code\":\"USA\",\"flag\":\"fa-flag-usa\",\"img\":\"https:\\/\\/images.unsplash.com\\/photo-1501594907352-04cda38ebc29?w=600&auto=format&fit=crop&q=60\",\"desc\":\"Home to Ivy League universities and world-renowned research facilities offering top-tier STEM and business majors.\",\"benefits\":\"OPT work opportunities, top-ranked institutions, diverse cultural landscape.\"},{\"country\":\"United Kingdom\",\"code\":\"UK\",\"flag\":\"fa-landmark\",\"img\":\"https:\\/\\/images.unsplash.com\\/photo-1513635269975-59663e0ac1ad?w=600&auto=format&fit=crop&q=60\",\"desc\":\"Deep academic history with accelerated degree paths (3-year Bachelor, 1-year Master) recognized globally.\",\"benefits\":\"2-year post-study work visa (Graduate Route), fast-track graduation, rich culture.\"},{\"country\":\"Canada\",\"code\":\"CA\",\"flag\":\"fa-leaf\",\"img\":\"https:\\/\\/images.unsplash.com\\/photo-1507608869274-d3177c8bb4c7?w=600&auto=format&fit=crop&q=60\",\"desc\":\"Highly welcoming policies, stunning natural environments, and premium quality of life with affordable living.\",\"benefits\":\"Post-Graduation Work Permit (PGWP), pathways to Permanent Residency (PR), safe campus life.\"},{\"country\":\"Australia\",\"code\":\"AU\",\"flag\":\"fa-austral-sign\",\"img\":\"https:\\/\\/images.unsplash.com\\/photo-1523482580672-f109ba8cb9be?w=600&auto=format&fit=crop&q=60\",\"desc\":\"Vibrant cities, world-class teaching standards, and extensive student support with top career prospects.\",\"benefits\":\"Extended post-study work options, sunshine & great beaches, strong economy.\"}],\"steps\":[{\"title\":\"Step 1: Consultation & Target Setting\",\"text\":\"Evaluate academic credentials and map them to preferred countries, courses, and financial budgets.\"},{\"title\":\"Step 2: English Proficiency Tests\",\"text\":\"Enroll in test prep classes (IELTS\\/PTE\\/TOEFL) and secure competitive bands needed for visa requirements.\"},{\"title\":\"Step 3: Document Compilation & Application\",\"text\":\"Prepare Statements of Purpose (SOP), letters of recommendation, and submit applications to shortlist universities.\"},{\"title\":\"Step 4: Visa Processing & Flight\",\"text\":\"Compile financial documentations, clear visa interviews, attend pre-departure orientations, and fly!\"}]}', '2026-06-28 12:29:13'),
(3, 'test-preparation', 'Test Preparation', 'Test Preparation', 'Our expert teachers, interactive mock tests, and smart study materials ensure you achieve your target scores in IELTS, PTE, TOEFL, and SAT on the first attempt.', '{\"tests\":{\"ielts\":{\"name\":\"International English Language Testing System (IELTS)\",\"desc\":\"IELTS is the world\'s most popular English language proficiency test for higher education and global migration. It evaluates your skills in listening, reading, writing, and speaking.\",\"duration\":\"2.5 Hr\",\"range\":\"0 - 9\",\"target\":\"6.5+\",\"validity\":\"2 Yrs\",\"format\":[[\"Listening\",\"30 minutes (4 sections, 40 questions)\"],[\"Reading\",\"60 minutes (3 texts, 40 questions)\"],[\"Writing\",\"60 minutes (2 tasks - descriptive & essay)\"],[\"Speaking\",\"11\\u201314 minutes (Face-to-face interview)\"]]},\"pte\":{\"name\":\"Pearson Test of English (PTE Academic)\",\"desc\":\"PTE Academic is a computer-based English language test accepted by educational institutions and governments globally. It utilizes AI scoring for rapid results, typically within 48 hours.\",\"duration\":\"2.0 Hr\",\"range\":\"10 - 90\",\"target\":\"58+\",\"validity\":\"2 Yrs\",\"format\":[[\"Speaking & Writing\",\"54\\u201367 minutes (combined section with essays\\/read alouds)\"],[\"Reading\",\"29\\u201330 minutes (multiple-choice, fill in blanks)\"],[\"Listening\",\"30\\u201343 minutes (dictation, summaries, audio playback)\"]]},\"toefl\":{\"name\":\"Test of English as a Foreign Language (TOEFL iBT)\",\"desc\":\"TOEFL iBT measures your ability to use and understand English at the university level. It is widely preferred in the USA and preferred by over 11,000 institutions internationally.\",\"duration\":\"2.0 Hr\",\"range\":\"0 - 120\",\"target\":\"80+\",\"validity\":\"2 Yrs\",\"format\":[[\"Reading\",\"35 minutes (20 questions from academic texts)\"],[\"Listening\",\"36 minutes (28 questions from lectures\\/conversations)\"],[\"Speaking\",\"16 minutes (4 integrated tasks)\"],[\"Writing\",\"29 minutes (2 tasks - academic discussion & integrated)\"]]},\"sat\":{\"name\":\"Scholastic Assessment Test (Digital SAT)\",\"desc\":\"The SAT is a standardized college admission test used in the United States and several other countries. It measures high school students\' readiness for college-level courses, focusing on Math and English.\",\"duration\":\"2.1 Hr\",\"range\":\"400-1600\",\"target\":\"1300+\",\"validity\":\"5 Yrs\",\"format\":[[\"Reading & Writing\",\"64 minutes (54 questions on passages and sentences)\"],[\"Mathematics\",\"70 minutes (44 questions covering algebra, advanced math, geometry)\"]]}},\"features\":[{\"title\":\"Certified Instructors\",\"text\":\"Learn from highly experienced teachers certified by British Council and Pearson.\",\"icon\":\"fa-chalkboard-user\"},{\"title\":\"State-of-the-Art Computer Lab\",\"text\":\"Practice on full-length simulator tests exactly representing the real digital tests.\",\"icon\":\"fa-laptop\"},{\"title\":\"Weekly Mock Tests\",\"text\":\"Evaluate your weak points with detailed performance feedbacks and review sessions every Sunday.\",\"icon\":\"fa-file-signature\"},{\"title\":\"Free Study Materials\",\"text\":\"Get access to premium books, vocabulary logs, and mock portals without extra costs.\",\"icon\":\"fa-book-open-reader\"}]}', '2026-06-28 12:29:13');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `key` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`key`, `value`) VALUES
('contact_email', 'info@visionpoint.edu.np'),
('contact_location', 'Vision Point Educational Service, Vasundhara Complex, 12 Roadcess Chowk, Biratnagar 56613, Biratnagar, Nepal'),
('contact_phone', '+977 9807765812'),
('footer_about', 'Vision Point Educational Service is Nepal\'s premier choice for studying in India, offering expert admission guidance and scholarships for top-tier Indian universities.'),
('footer_copyright', ''),
('hero_banner_image', 'assets/images/banner.png'),
('hero_subtitle', 'Maximize your potential and reach new academic peaks with our expert guidance, university listings, and scholarships every step of the way.'),
('hero_title', 'Unlock Your Full \r\nEducational Potential in India'),
('primary_color', '#102e48'),
('primary_light', '#18446c'),
('secondary_color', '#e56920'),
('secondary_hover', '#d25a17'),
('site_logo', 'assets/images/logo1.png'),
('site_name', 'Vision Point Educational Service'),
('site_favicon', 'assets/images/img4.png'),
('site_title', 'Vision Point Educational Service | Study Abroad & India');

-- --------------------------------------------------------

--
-- Table structure for table `social_links`
--

CREATE TABLE `social_links` (
  `id` int(11) NOT NULL,
  `platform_name` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `social_links`
--

INSERT INTO `social_links` (`id`, `platform_name`, `url`, `icon`, `display_order`) VALUES
(1, 'Facebook', 'https://www.facebook.com/share/1LuozTGdwc/', 'fa-brands fa-facebook', 1),
(2, 'Instagram', 'https://www.instagram.com/kefnepal.official?igsh=MTl5azkwYmRrMndlaQ==', 'fa-brands fa-instagram', 2),
(3, 'Twitter', 'https://twitter.com', 'fa-brands fa-twitter', 3),
(4, 'Youtube', 'https://youtube.com/@kefnepal?si=Mzd-jDJC-953AX6T', 'fa-brands fa-youtube', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course_streams`
--
ALTER TABLE `course_streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `destinations`
--
ALTER TABLE `destinations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fun_facts`
--
ALTER TABLE `fun_facts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `guides`
--
ALTER TABLE `guides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `institutions`
--
ALTER TABLE `institutions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `navigation_menu`
--
ALTER TABLE `navigation_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `social_links`
--
ALTER TABLE `social_links`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `course_streams`
--
ALTER TABLE `course_streams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `destinations`
--
ALTER TABLE `destinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fun_facts`
--
ALTER TABLE `fun_facts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `guides`
--
ALTER TABLE `guides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `institutions`
--
ALTER TABLE `institutions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `navigation_menu`
--
ALTER TABLE `navigation_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `social_links`
--
ALTER TABLE `social_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `navigation_menu`
--
ALTER TABLE `navigation_menu`
  ADD CONSTRAINT `navigation_menu_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `navigation_menu` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
