<?php
header('Content-Type: application/json');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/includes/db.php';

// Helper response function
function jsonResponse($success, $message, $data = [], $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode(array_merge([
        'success' => $success,
        'message' => $message
    ], $data));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Invalid request method', [], 405);
}

// CSRF Validation
$csrfToken = isset($_POST['csrf_token']) ? trim((string)$_POST['csrf_token']) : '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
    jsonResponse(false, 'Security check failed. Please refresh the page and try again.', [], 403);
}

// Ensure database connection is active
if (!isset($pdo) || !$pdo) {
    jsonResponse(false, 'Database service is currently undergoing maintenance. Please reach out to us at info@visionpoint.edu.np or +977 9807765812.', [], 503);
}

$action = isset($_POST['action']) ? trim((string)$_POST['action']) : '';

// 1. Newsletter subscription
if ($action === 'subscribe') {
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Please provide a valid email address.');
    }
    
    try {
        // Check if already subscribed
        $stmt = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            jsonResponse(true, 'You are already subscribed!');
        }
        
        $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
        $stmt->execute([$email]);
        jsonResponse(true, 'Successfully subscribed to the newsletter.');
    } catch (PDOException $e) {
        error_log("Newsletter subscription DB error: " . $e->getMessage());
        jsonResponse(false, 'Unable to process your request at this time. Please try again later.', [], 500);
    }
}

// Mappings for inquiries and enrollments IDs to human-readable strings
$countriesMap = [
    '9' => 'India', '5' => 'US', '6' => 'Canada', '4' => 'UK', 
    '3' => 'Australia', '10' => 'Bangladesh', '7' => 'Europe'
];

$coursesMap = [
    '2' => 'Nursing', '1' => 'Engineering', '6' => 'Pharmacy', 
    '9' => 'Management', '14' => 'Computer Application / IT', '3' => 'Dental',
    '17' => 'Behavioral and Social Science', '18' => 'Allied Health Science',
    '4' => 'Agriculture', '10' => 'Medical', '16' => 'Design', 
    '8' => 'Paramedical', '7' => 'Arts and Humanities', 
    '13' => 'Media Studies & Communication', '5' => 'Science', 
    '11' => 'Hotel Management', '12' => 'Law', '15' => 'Education'
];

$levelsMap = [
    '3' => 'Diploma', '2' => 'Postgraduate', '1' => 'Undergraduate'
];

$citiesMap = [
    '31' => 'Rajasthan', '19' => 'Hyderabad', '22' => 'Ahmedabad', 
    '21' => 'Varanasi', '35' => 'Warangal', '36' => 'Salem', 
    '18' => 'Chennai', '17' => 'Pune', '32' => 'Dehradun', 
    '20' => 'Kolkata', '27' => 'Nagpur', '28' => 'Chandigarh', 
    '29' => 'Punjab', '30' => 'Shimla', '24' => 'Visakhapatnam', 
    '25' => 'Bhubaneswar', '26' => 'Coimbatore', '14' => 'Mumbai', 
    '16' => 'Bangalore (Bengaluru)', '15' => 'Delhi (Delhi NCR)'
];

// 2. Contact form submission
if ($action === 'contact') {
    $fullName = isset($_POST['full_name']) ? trim((string)$_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    $phone = isset($_POST['phone_number']) ? trim((string)$_POST['phone_number']) : '';
    $address = isset($_POST['address']) ? trim((string)$_POST['address']) : '';
    $countryId = isset($_POST['country']) ? trim((string)$_POST['country']) : '';
    $courseId = isset($_POST['course']) ? trim((string)$_POST['course']) : '';
    $message = isset($_POST['message']) ? trim((string)$_POST['message']) : '';
    
    if ($fullName === '' || $email === '' || $phone === '') {
        jsonResponse(false, 'Name, email, and phone number are required.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Please provide a valid email address.');
    }
    
    // Resolve mappings
    $country = isset($countriesMap[$countryId]) ? $countriesMap[$countryId] : $countryId;
    $course = isset($coursesMap[$courseId]) ? $coursesMap[$courseId] : $courseId;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO inquiries (full_name, email, phone_number, address, country, course, message, form_type) VALUES (?, ?, ?, ?, ?, ?, ?, 'contact_form')");
        $stmt->execute([$fullName, $email, $phone, $address, $country, $course, $message]);
        jsonResponse(true, 'Your inquiry has been submitted successfully.');
    } catch (PDOException $e) {
        error_log("Contact inquiry DB error: " . $e->getMessage());
        jsonResponse(false, 'Unable to process your request at this time. Please try again later.', [], 500);
    }
}

// 3. Enrollment form submission
if ($action === 'enroll') {
    $fullName = isset($_POST['full_name']) ? trim((string)$_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    $phone = isset($_POST['phone_number']) ? trim((string)$_POST['phone_number']) : '';
    $address = isset($_POST['address']) ? trim((string)$_POST['address']) : '';
    $interestedCountry = isset($_POST['interested_country']) ? trim((string)$_POST['interested_country']) : '';
    $levelId = isset($_POST['level']) ? trim((string)$_POST['level']) : '';
    $cityId = isset($_POST['interested_city']) ? trim((string)$_POST['interested_city']) : '';
    $college = isset($_POST['interested_college']) ? trim((string)$_POST['interested_college']) : '';
    $facultyId = isset($_POST['faculty']) ? trim((string)$_POST['faculty']) : '';
    $program = isset($_POST['interested_program']) ? trim((string)$_POST['interested_program']) : '';
    $highestEduId = isset($_POST['highest_level_of_education']) ? trim((string)$_POST['highest_level_of_education']) : '';
    $percentage = isset($_POST['percentage']) ? trim((string)$_POST['percentage']) : '';
    $message = isset($_POST['message']) ? trim((string)$_POST['message']) : '';
    
    if ($fullName === '' || $email === '' || $phone === '') {
        jsonResponse(false, 'Full name, email, and phone number are required.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Please provide a valid email address.');
    }
    
    // Resolve mappings
    $level = isset($levelsMap[$levelId]) ? $levelsMap[$levelId] : $levelId;
    $city = isset($citiesMap[$cityId]) ? $citiesMap[$cityId] : $cityId;
    $faculty = isset($coursesMap[$facultyId]) ? $coursesMap[$facultyId] : $facultyId;
    $highestEdu = isset($levelsMap[$highestEduId]) ? $levelsMap[$highestEduId] : $highestEduId;
    
    // File upload handling for student photo
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
            jsonResponse(false, 'Photo upload failed. Please try again.');
        }
        
        $fileName = $_FILES['photo']['name'];
        $fileTmpName = $_FILES['photo']['tmp_name'];
        $fileSize = $_FILES['photo']['size'];
        
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Strict allowlist: SVG and TIFF excluded to prevent Stored XSS / file abuse
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($fileExt, $allowedExts, true)) {
            jsonResponse(false, 'Invalid file type. Only JPG, JPEG, PNG, GIF, and WEBP image files are allowed.');
        }
        
        // Extra MIME type verification
        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($fileTmpName);
            $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($mime, $allowedMimes, true)) {
                jsonResponse(false, 'Uploaded file is not a valid image.');
            }
        }
        
        // Limit photo size to 5MB
        if ($fileSize > 5 * 1024 * 1024) {
            jsonResponse(false, 'Image size exceeds the 5MB limit.');
        }
        
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $newFileName = uniqid('student_', true) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;
        
        if (is_uploaded_file($fileTmpName) && move_uploaded_file($fileTmpName, $destPath)) {
            $photoPath = 'uploads/' . $newFileName;
        } else {
            jsonResponse(false, 'Failed to save uploaded photo.');
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO enrollments (full_name, email, phone_number, address, interested_country, level, interested_city, interested_college, faculty, interested_program, highest_education, percentage, message, photo_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $email, $phone, $address, $interestedCountry, $level, $city, $college, $faculty, $program, $highestEdu, $percentage, $message, $photoPath]);
        jsonResponse(true, 'Your enrollment form has been submitted successfully.');
    } catch (PDOException $e) {
        error_log("Enrollment form submission DB error: " . $e->getMessage());
        jsonResponse(false, 'Unable to process your request at this time. Please try again later.', [], 500);
    }
}

// 4. Review & Feedback Submission
if ($action === 'submit_review' || $action === 'review') {
    $name = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
    $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
    $courseCollege = isset($_POST['course_college']) ? trim((string)$_POST['course_college']) : '';
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 5;
    $title = isset($_POST['title']) ? trim((string)$_POST['title']) : '';
    $comment = isset($_POST['comment']) ? trim((string)$_POST['comment']) : '';
    $avatar = isset($_POST['avatar']) ? trim((string)$_POST['avatar']) : '';
    
    if ($name === '' || $email === '' || $comment === '') {
        jsonResponse(false, 'Name, email, and detailed review comment are required.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(false, 'Please enter a valid email address.');
    }
    
    if ($rating < 1 || $rating > 5) {
        $rating = 5;
    }
    
    if ($title === '') {
        $title = $rating >= 4 ? 'Great Experience' : 'Student Feedback';
    }

    // Handle photo upload if provided
    if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/uploads/';
        if (!file_exists($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }
        $fileTmpName = $_FILES['avatar_file']['tmp_name'];
        $fileName = $_FILES['avatar_file']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($fileExt, $allowedExts, true)) {
            $newFileName = uniqid('rev_avatar_', true) . '.' . $fileExt;
            $destPath = $uploadDir . $newFileName;
            if (move_uploaded_file($fileTmpName, $destPath)) {
                $avatar = 'uploads/' . $newFileName;
            }
        }
    }

    try {
        $pdo->exec("ALTER TABLE reviews ADD COLUMN avatar VARCHAR(255) DEFAULT NULL");
    } catch (\Throwable $e) {}
    
    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (name, email, course_college, avatar, rating, title, comment, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$name, $email, $courseCollege, $avatar, $rating, $title, $comment]);
        $reviewId = $pdo->lastInsertId();
        
        jsonResponse(true, 'Thank you! Your review has been submitted successfully and will be published once approved by our admin team.');
    } catch (PDOException $e) {
        error_log("Review submission DB error: " . $e->getMessage());
        jsonResponse(false, 'Unable to process your request at this time. Please try again later.', [], 500);
    }
}

jsonResponse(false, 'Invalid action');


