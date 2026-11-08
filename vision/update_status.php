<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// CSRF validation
$csrfToken = isset($_POST['csrf_token']) ? trim((string)$_POST['csrf_token']) : '';
if (empty($_SESSION['csrf_token']) || $csrfToken !== $_SESSION['csrf_token']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'CSRF security check failed.']);
    exit;
}

// Get POST parameters
$type = isset($_POST['type']) ? trim((string)$_POST['type']) : '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status = isset($_POST['status']) ? trim((string)$_POST['status']) : '';

$allowedTypes = ['enrollment', 'inquiry'];
$allowedStatuses = ['Pending', 'Under Review', 'Contacted', 'Admitted', 'Cancelled'];

if (!in_array($type, $allowedTypes) || $id <= 0 || !in_array($status, $allowedStatuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
    exit;
}

try {
    $table = $type === 'enrollment' ? 'enrollments' : 'inquiries';
    $stmt = $pdo->prepare("UPDATE {$table} SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
