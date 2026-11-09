<?php
require_once __DIR__ . '/../includes/db.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    die("Unauthorized access.");
}

$type = isset($_GET['type']) ? trim((string)$_GET['type']) : '';
$allowedTypes = ['enrollments', 'inquiries', 'subscribers'];

if (!in_array($type, $allowedTypes)) {
    http_response_code(400);
    die("Invalid export type.");
}

try {
    // Fetch data based on type
    if ($type === 'enrollments') {
        $stmt = $pdo->query('SELECT id, full_name, email, phone_number, address, level, interested_city, interested_college, faculty, interested_program, highest_education, percentage, message, status, created_at FROM enrollments ORDER BY created_at DESC');
        $filename = 'enrollments_' . date('Y-m-d') . '.csv';
        $headers = ['ID', 'Full Name', 'Email', 'Phone Number', 'Address', 'Level', 'Interested City', 'Interested College', 'Faculty', 'Interested Program', 'Highest Education', 'Percentage/GPA', 'Message', 'Status', 'Created At'];
    } elseif ($type === 'inquiries') {
        $stmt = $pdo->query('SELECT id, full_name, email, phone_number, address, country, course, message, form_type, status, created_at FROM inquiries ORDER BY created_at DESC');
        $filename = 'inquiries_' . date('Y-m-d') . '.csv';
        $headers = ['ID', 'Full Name', 'Email', 'Phone Number', 'Address', 'Country', 'Course Interest', 'Message', 'Form Type', 'Status', 'Created At'];
    } else { // subscribers
        $stmt = $pdo->query('SELECT id, email, created_at FROM newsletter_subscribers ORDER BY created_at DESC');
        $filename = 'newsletter_subscribers_' . date('Y-m-d') . '.csv';
        $headers = ['ID', 'Email Address', 'Subscribed At'];
    }

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Disable caching
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Open file pointer to stdout
    $output = fopen('php://output', 'w');

    // Add UTF-8 BOM for Excel support
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Write headers
    fputcsv($output, $headers);

    // Write data rows
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    error_log("CSV Export DB error: " . $e->getMessage());
    http_response_code(500);
    die("Export failed. Please try again or contact system administrator.");
}
