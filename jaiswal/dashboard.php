<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../includes/db.php';

// Auth check
if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

$alert = null;

// CSRF Token Generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Activity logging helper
function logActivity($action, $details = null) {
    global $pdo;
    if (!isset($pdo) || !$pdo) {
        return;
    }
    try {
        $stmt = $pdo->prepare('INSERT INTO activity_log (admin_id, admin_email, action, details, ip_address) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([
            $_SESSION['admin_id'] ?? null,
            $_SESSION['admin_email'] ?? 'System',
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? ''
        ]);
    } catch (PDOException $e) {
        // Fail silently
    }
}

// Helper: Secure file upload
function handleFileUpload($fileKey, $prefix = 'img_') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }
    
    if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $_FILES[$fileKey]['error']);
    }
    
    $fileName = $_FILES[$fileKey]['name'];
    $fileTmp = $_FILES[$fileKey]['tmp_name'];
    $fileSize = $_FILES[$fileKey]['size'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    
    if (!in_array($ext, $allowed)) {
        throw new Exception('Invalid file type. Only image files (JPG, PNG, WEBP, GIF, BMP, SVG) are allowed.');
    }
    
    if ($fileSize > 5 * 1024 * 1024) {
        throw new Exception('File size exceeds the 5MB limit.');
    }
    
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $newFileName = uniqid($prefix, true) . '.' . $ext;
    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
        return 'uploads/' . $newFileName;
    } else {
        throw new Exception('Failed to save uploaded image.');
    }
}

// Helper: Secure document upload
function handleDocUpload($fileKey, $prefix = 'doc_') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }
    
    if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload error: ' . $_FILES[$fileKey]['error']);
    }
    
    $fileName = $_FILES[$fileKey]['name'];
    $fileTmp = $_FILES[$fileKey]['tmp_name'];
    $fileSize = $_FILES[$fileKey]['size'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'jpg', 'jpeg', 'png'];
    
    if (!in_array($ext, $allowed)) {
        throw new Exception('Invalid file type. Allowed files: PDF, DOC, DOCX, XLS, XLSX, TXT, JPG, PNG.');
    }
    
    if ($fileSize > 10 * 1024 * 1024) { // 10MB limit
        throw new Exception('File size exceeds the 10MB limit.');
    }
    
    $uploadDir = __DIR__ . '/../uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    $newFileName = uniqid($prefix, true) . '.' . $ext;
    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
        return 'uploads/' . $newFileName;
    } else {
        throw new Exception('Failed to save uploaded document.');
    }
}

function handlePdfUpload($fileKey, $prefix = 'pdf_') {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] == UPLOAD_ERR_NO_FILE) {
        return null;
    }
    
    if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('PDF upload error code: ' . $_FILES[$fileKey]['error']);
    }
    
    $fileName = $_FILES[$fileKey]['name'];
    $fileTmp = $_FILES[$fileKey]['tmp_name'];
    $fileSize = $_FILES[$fileKey]['size'];
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if ($ext !== 'pdf') {
        throw new Exception('Invalid file type. Only PDF documents are allowed.');
    }
    
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($fileTmp);
        if ($mime !== 'application/pdf') {
            throw new Exception('Security Error: Uploaded file is not a valid PDF document (MIME type mismatch).');
        }
    }
    
    if ($fileSize > 20 * 1024 * 1024) { // 20MB limit
        throw new Exception('PDF size exceeds the 20MB limit.');
    }
    
    $uploadDir = __DIR__ . '/../uploads/pdfs/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $newFileName = $prefix . bin2hex(random_bytes(16)) . '.pdf';
    if (move_uploaded_file($fileTmp, $uploadDir . $newFileName)) {
        return 'uploads/pdfs/' . $newFileName;
    } else {
        throw new Exception('Failed to save uploaded PDF.');
    }
}

// Handle CRUD and Bulk Operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // CSRF Check
    $csrfToken = isset($_POST['csrf_token']) ? trim((string)$_POST['csrf_token']) : '';
    if ($csrfToken !== $_SESSION['csrf_token']) {
        die("Security token validation failed. Please refresh the page and try again.");
    }
    
    $action = (string)$_POST['action'];
    
    try {
        // ENROLLMENTS & INQUIRIES ACTIONS
        if ($action === 'delete_enrollment' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM enrollments WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Enrollment #$id");
            $alert = ['type' => 'success', 'msg' => 'Enrollment deleted successfully.'];
        }
        
        if ($action === 'delete_inquiry' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM inquiries WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Inquiry #$id");
            $alert = ['type' => 'success', 'msg' => 'Inquiry deleted successfully.'];
        }
        
        if ($action === 'delete_subscriber' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Subscriber #$id");
            $alert = ['type' => 'success', 'msg' => 'Subscriber deleted successfully.'];
        }

        if ($action === 'delete_review' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Review #$id");
            $alert = ['type' => 'success', 'msg' => 'Student review deleted successfully.'];
        }

        if ($action === 'toggle_review_status' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("UPDATE reviews SET status = CASE WHEN status = 'Approved' THEN 'Pending' ELSE 'Approved' END WHERE id = ?");
            $stmt->execute([$id]);
            logActivity("Toggled Status for Review #$id");
            $alert = ['type' => 'success', 'msg' => 'Review status toggled successfully.'];
        }

        if ($action === 'add_review') {
            $name = trim((string)$_POST['name']);
            $email = trim((string)$_POST['email']);
            $course = trim((string)$_POST['course_college']);
            $avatar = trim((string)($_POST['avatar'] ?? ''));
            $rating = (int)$_POST['rating'];
            $title = trim((string)$_POST['title']);
            $comment = trim((string)$_POST['comment']);
            $status = isset($_POST['status']) ? trim((string)$_POST['status']) : 'Approved';
            $createdAt = !empty($_POST['created_at']) ? date('Y-m-d H:i:s', strtotime($_POST['created_at'])) : date('Y-m-d H:i:s');

            if ($name === '' || $email === '' || $comment === '') {
                throw new Exception('Student Name, Email, and Review Comment are required.');
            }

            try {
                $pdo->exec("ALTER TABLE reviews ADD COLUMN avatar VARCHAR(255) DEFAULT NULL");
            } catch (\Throwable $e) {}

            $stmt = $pdo->prepare("INSERT INTO reviews (name, email, course_college, avatar, rating, title, comment, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $course, $avatar, $rating, $title, $comment, $status, $createdAt]);
            $revId = $pdo->lastInsertId();
            logActivity("Added Review #$revId", $name);
            $alert = ['type' => 'success', 'msg' => 'Student review added successfully.'];
        }

        if ($action === 'edit_review' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim((string)$_POST['name']);
            $email = trim((string)$_POST['email']);
            $course = trim((string)$_POST['course_college']);
            $avatar = trim((string)($_POST['avatar'] ?? ''));
            $rating = (int)$_POST['rating'];
            $title = trim((string)$_POST['title']);
            $comment = trim((string)$_POST['comment']);
            $status = trim((string)$_POST['status']);
            $createdAt = !empty($_POST['created_at']) ? date('Y-m-d H:i:s', strtotime($_POST['created_at'])) : date('Y-m-d H:i:s');

            if ($name === '' || $email === '' || $comment === '') {
                throw new Exception('Student Name, Email, and Review Comment are required.');
            }

            try {
                $pdo->exec("ALTER TABLE reviews ADD COLUMN avatar VARCHAR(255) DEFAULT NULL");
            } catch (\Throwable $e) {}

            $stmt = $pdo->prepare("UPDATE reviews SET name = ?, email = ?, course_college = ?, avatar = ?, rating = ?, title = ?, comment = ?, status = ?, created_at = ? WHERE id = ?");
            $stmt->execute([$name, $email, $course, $avatar, $rating, $title, $comment, $status, $createdAt, $id]);
            logActivity("Updated Review #$id", $name);
            $alert = ['type' => 'success', 'msg' => 'Student review updated successfully.'];
        }

        if ($action === 'bulk_delete_reviews' && isset($_POST['ids'])) {
            $ids = array_map('intval', (array)$_POST['ids']);
            if (!empty($ids)) {
                $in = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM reviews WHERE id IN ($in)");
                $stmt->execute($ids);
                logActivity("Bulk Deleted Reviews", "IDs: " . implode(',', $ids));
                $alert = ['type' => 'success', 'msg' => 'Selected reviews deleted successfully.'];
            }
        }
        
        if ($action === 'bulk_delete_enrollments' && isset($_POST['ids'])) {
            $ids = array_map('intval', (array)$_POST['ids']);
            if (!empty($ids)) {
                $in = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id IN ($in)");
                $stmt->execute($ids);
                logActivity("Bulk Deleted Enrollments", "IDs: " . implode(',', $ids));
                $alert = ['type' => 'success', 'msg' => 'Selected enrollments deleted successfully.'];
            }
        }
        
        if ($action === 'bulk_delete_inquiries' && isset($_POST['ids'])) {
            $ids = array_map('intval', (array)$_POST['ids']);
            if (!empty($ids)) {
                $in = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM inquiries WHERE id IN ($in)");
                $stmt->execute($ids);
                logActivity("Bulk Deleted Inquiries", "IDs: " . implode(',', $ids));
                $alert = ['type' => 'success', 'msg' => 'Selected inquiries deleted successfully.'];
            }
        }

        if ($action === 'edit_enrollment' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $fullName = trim((string)$_POST['full_name']);
            $email = trim((string)$_POST['email']);
            $phone = trim((string)$_POST['phone_number']);
            $address = trim((string)$_POST['address']);
            $level = trim((string)$_POST['level']);
            $city = trim((string)$_POST['interested_city']);
            $college = trim((string)$_POST['interested_college']);
            $faculty = trim((string)$_POST['faculty']);
            $program = trim((string)$_POST['interested_program']);
            $interestedCountry = trim((string)$_POST['interested_country']);
            $edu = trim((string)$_POST['highest_education']);
            $percentage = trim((string)$_POST['percentage']);
            $message = trim((string)$_POST['message']);
            $status = trim((string)$_POST['status']);
            
            // Handle Photo Edit
            $photoPath = trim((string)$_POST['existing_photo']);
            if (isset($_POST['delete_photo']) && $_POST['delete_photo'] === '1') {
                if (!empty($photoPath) && file_exists(__DIR__ . '/../' . $photoPath)) {
                    unlink(__DIR__ . '/../' . $photoPath);
                }
                $photoPath = null;
            }
            $uploadedPhoto = handleFileUpload('photo', 'student_');
            if ($uploadedPhoto) {
                if (!empty($photoPath) && file_exists(__DIR__ . '/../' . $photoPath)) {
                    unlink(__DIR__ . '/../' . $photoPath);
                }
                $photoPath = $uploadedPhoto;
            }

            $stmt = $pdo->prepare('UPDATE enrollments SET full_name = ?, email = ?, phone_number = ?, address = ?, interested_country = ?, level = ?, interested_city = ?, interested_college = ?, faculty = ?, interested_program = ?, highest_education = ?, percentage = ?, message = ?, status = ?, photo_path = ? WHERE id = ?');
            $stmt->execute([$fullName, $email, $phone, $address, $interestedCountry, $level, $city, $college, $faculty, $program, $edu, $percentage, $message, $status, $photoPath, $id]);
            logActivity("Updated Enrollment #$id");
            $alert = ['type' => 'success', 'msg' => 'Enrollment updated successfully.'];
        }

        if ($action === 'edit_inquiry' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $fullName = trim((string)$_POST['full_name']);
            $email = trim((string)$_POST['email']);
            $phone = trim((string)$_POST['phone_number']);
            $address = trim((string)$_POST['address']);
            $country = trim((string)$_POST['country']);
            $course = trim((string)$_POST['course']);
            $message = trim((string)$_POST['message']);
            $status = trim((string)$_POST['status']);

            $stmt = $pdo->prepare('UPDATE inquiries SET full_name = ?, email = ?, phone_number = ?, address = ?, country = ?, course = ?, message = ?, status = ? WHERE id = ?');
            $stmt->execute([$fullName, $email, $phone, $address, $country, $course, $message, $status, $id]);
            logActivity("Updated Inquiry #$id");
            $alert = ['type' => 'success', 'msg' => 'Inquiry updated successfully.'];
        }

        // UNIVERSITIES ACTIONS
        if ($action === 'add_university') {
            $name = trim((string)$_POST['name']);
            $city = trim((string)$_POST['city']);
            $region = trim((string)$_POST['region']);
            $about = trim((string)$_POST['about']);
            $fees = trim((string)$_POST['fees']);
            $admission_process = trim((string)$_POST['admission_process']);
            $location_detail = trim((string)$_POST['location_detail']);
            $courses = isset($_POST['courses']) && is_array($_POST['courses']) ? implode(',', array_map('intval', $_POST['courses'])) : '';
            $map_link = trim((string)$_POST['map_link']);
            $is_top = isset($_POST['is_top']) ? 1 : 0;
            $status = isset($_POST['status']) ? trim((string)$_POST['status']) : 'Active';
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            
            $img = trim((string)$_POST['img']);
            $uploadedImg = handleFileUpload('img_file', 'uni_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $fee_document = '';
            $uploadedDoc = handleDocUpload('fee_doc_file', 'feedoc_');
            if ($uploadedDoc) {
                $fee_document = $uploadedDoc;
            }
            
            if ($name === '' || $city === '' || $region === '') {
                $alert = ['type' => 'danger', 'msg' => 'Name, City, and Region are required.'];
            } else {
                $stmt = $pdo->prepare('INSERT INTO institutions (name, city, region, img, about, fees, admission_process, location_detail, courses, map_link, is_top, fee_document, status, display_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $city, $region, $img, $about, $fees, $admission_process, $location_detail, $courses, $map_link, $is_top, $fee_document, $status, $display_order]);
                $uniId = $pdo->lastInsertId();
                logActivity("Added University #$uniId", $name);
                $alert = ['type' => 'success', 'msg' => 'University added successfully.'];
            }
        }

        if ($action === 'edit_university' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim((string)$_POST['name']);
            $city = trim((string)$_POST['city']);
            $region = trim((string)$_POST['region']);
            $about = trim((string)$_POST['about']);
            $fees = trim((string)$_POST['fees']);
            $admission_process = trim((string)$_POST['admission_process']);
            $location_detail = trim((string)$_POST['location_detail']);
            $courses = isset($_POST['courses']) && is_array($_POST['courses']) ? implode(',', array_map('intval', $_POST['courses'])) : '';
            $map_link = trim((string)$_POST['map_link']);
            $is_top = isset($_POST['is_top']) ? 1 : 0;
            $status = isset($_POST['status']) ? trim((string)$_POST['status']) : 'Active';
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            
            $img = trim((string)$_POST['img']);
            $uploadedImg = handleFileUpload('img_file', 'uni_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $fee_document = isset($_POST['existing_fee_doc']) ? trim((string)$_POST['existing_fee_doc']) : '';
            $uploadedDoc = handleDocUpload('fee_doc_file', 'feedoc_');
            if ($uploadedDoc) {
                $fee_document = $uploadedDoc;
            }
            
            if ($name === '' || $city === '' || $region === '') {
                $alert = ['type' => 'danger', 'msg' => 'Name, City, and Region are required.'];
            } else {
                $stmt = $pdo->prepare('UPDATE institutions SET name = ?, city = ?, region = ?, img = ?, about = ?, fees = ?, admission_process = ?, location_detail = ?, courses = ?, map_link = ?, is_top = ?, fee_document = ?, status = ?, display_order = ? WHERE id = ?');
                $stmt->execute([$name, $city, $region, $img, $about, $fees, $admission_process, $location_detail, $courses, $map_link, $is_top, $fee_document, $status, $display_order, $id]);
                logActivity("Updated University #$id", $name);
                $alert = ['type' => 'success', 'msg' => 'University updated successfully.'];
            }
        }

        if ($action === 'delete_university' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM institutions WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted University #$id");
            $alert = ['type' => 'success', 'msg' => 'University deleted successfully.'];
        }

        if ($action === 'toggle_top_university' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('UPDATE institutions SET is_top = IF(is_top = 1, 0, 1) WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Toggled Top Featured Status for University #$id");
            $alert = ['type' => 'success', 'msg' => 'University top featured status toggled successfully.'];
        }

        if ($action === 'toggle_notification') {
            $stmtGet = $pdo->prepare("SELECT `value` FROM site_settings WHERE `key` = 'notification_enabled'");
            $stmtGet->execute();
            $val = $stmtGet->fetchColumn();
            $newStatus = ($val === '1') ? '0' : '1';
            
            $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`) VALUES ('notification_enabled', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            $stmt->execute([$newStatus]);
            logActivity("Toggled Site Notifications " . ($newStatus === '1' ? 'ON' : 'OFF'));
            $alert = ['type' => 'success', 'msg' => 'Notification alert status turned ' . ($newStatus === '1' ? 'ON' : 'OFF') . ' successfully.'];
        }

        if ($action === 'toggle_auto_popup') {
            $stmtGet = $pdo->prepare("SELECT `value` FROM site_settings WHERE `key` = 'auto_popup_enabled'");
            $stmtGet->execute();
            $val = $stmtGet->fetchColumn();
            $newStatus = ($val === '1') ? '0' : '1';
            
            $stmt = $pdo->prepare("INSERT INTO site_settings (`key`, `value`) VALUES ('auto_popup_enabled', ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            $stmt->execute([$newStatus]);
            logActivity("Toggled Auto Popup Modal " . ($newStatus === '1' ? 'ON' : 'OFF'));
            $alert = ['type' => 'success', 'msg' => 'Auto Popup Modal status turned ' . ($newStatus === '1' ? 'ON' : 'OFF') . ' successfully.'];
        }




        if ($action === 'add_program') {
            $name = trim((string)$_POST['name']);
            $college_name = trim((string)$_POST['college_name']);
            $location = trim((string)$_POST['location']);
            $fee = trim((string)$_POST['fee']);
            $duration = trim((string)$_POST['duration']);
            $stream = trim((string)$_POST['stream']);
            $level = trim((string)$_POST['level']);
            $intake = trim((string)$_POST['intake']);
            $details_link = trim((string)$_POST['details_link']);
            
            $logo_path = '';
            $uploadedLogo = handleFileUpload('logo_file', 'prog_');
            if ($uploadedLogo) {
                $logo_path = $uploadedLogo;
            }
            
            if ($name === '' || $college_name === '' || $location === '' || $fee === '') {
                $alert = ['type' => 'danger', 'msg' => 'Program Name, College, Location, and Fee are required.'];
            } else {
                $stmt = $pdo->prepare('INSERT INTO programs (name, college_name, location, fee, duration, stream, level, intake, logo_path, details_link) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
                $stmt->execute([$name, $college_name, $location, $fee, $duration, $stream, $level, $intake, $logo_path, $details_link]);
                $progId = $pdo->lastInsertId();
                logActivity("Added Program #$progId", $name);
                $alert = ['type' => 'success', 'msg' => 'Program added successfully.'];
            }
        }

        if ($action === 'edit_program' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim((string)$_POST['name']);
            $college_name = trim((string)$_POST['college_name']);
            $location = trim((string)$_POST['location']);
            $fee = trim((string)$_POST['fee']);
            $duration = trim((string)$_POST['duration']);
            $stream = trim((string)$_POST['stream']);
            $level = trim((string)$_POST['level']);
            $intake = trim((string)$_POST['intake']);
            $details_link = trim((string)$_POST['details_link']);
            
            $logo_path = trim((string)$_POST['logo_path']);
            $uploadedLogo = handleFileUpload('logo_file', 'prog_');
            if ($uploadedLogo) {
                $logo_path = $uploadedLogo;
            }
            
            if ($name === '' || $college_name === '' || $location === '' || $fee === '') {
                $alert = ['type' => 'danger', 'msg' => 'Program Name, College, Location, and Fee are required.'];
            } else {
                $stmt = $pdo->prepare('UPDATE programs SET name = ?, college_name = ?, location = ?, fee = ?, duration = ?, stream = ?, level = ?, intake = ?, logo_path = ?, details_link = ? WHERE id = ?');
                $stmt->execute([$name, $college_name, $location, $fee, $duration, $stream, $level, $intake, $logo_path, $details_link, $id]);
                logActivity("Updated Program #$id", $name);
                $alert = ['type' => 'success', 'msg' => 'Program updated successfully.'];
            }
        }

        if ($action === 'delete_program' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM programs WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Program #$id");
            $alert = ['type' => 'success', 'msg' => 'Program deleted successfully.'];
        }

        if ($action === 'bulk_delete_universities' && isset($_POST['ids'])) {
            $ids = array_map('intval', (array)$_POST['ids']);
            if (!empty($ids)) {
                $in = str_repeat('?,', count($ids) - 1) . '?';
                $stmt = $pdo->prepare("DELETE FROM institutions WHERE id IN ($in)");
                $stmt->execute($ids);
                logActivity("Bulk Deleted Universities", "IDs: " . implode(',', $ids));
                $alert = ['type' => 'success', 'msg' => 'Selected universities deleted successfully.'];
            }
        }

        // DESTINATIONS/CITIES ACTIONS
        if ($action === 'add_destination') {
            $name = trim((string)$_POST['name']);
            $region = trim((string)$_POST['region']);
            $about = trim((string)$_POST['about']);
            $gallery = trim((string)$_POST['gallery']);
            $map_link = trim((string)$_POST['map_link']);
            $order = (int)$_POST['display_order'];
            
            $img = trim((string)$_POST['img']);
            $uploadedImg = handleFileUpload('img_file', 'dest_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $stmt = $pdo->prepare('INSERT INTO destinations (name, region, img, about, gallery, map_link, display_order) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$name, $region, $img, $about, $gallery, $map_link, $order]);
            logActivity("Added Destination", $name);
            $alert = ['type' => 'success', 'msg' => 'Destination added successfully.'];
        }

        if ($action === 'edit_destination' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim((string)$_POST['name']);
            $region = trim((string)$_POST['region']);
            $about = trim((string)$_POST['about']);
            $gallery = trim((string)$_POST['gallery']);
            $map_link = trim((string)$_POST['map_link']);
            $order = (int)$_POST['display_order'];
            
            $img = trim((string)$_POST['img']);
            $uploadedImg = handleFileUpload('img_file', 'dest_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $stmt = $pdo->prepare('UPDATE destinations SET name = ?, region = ?, img = ?, about = ?, gallery = ?, map_link = ?, display_order = ? WHERE id = ?');
            $stmt->execute([$name, $region, $img, $about, $gallery, $map_link, $order, $id]);
            logActivity("Updated Destination #$id", $name);
            $alert = ['type' => 'success', 'msg' => 'Destination updated successfully.'];
        }

        if ($action === 'delete_destination' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM destinations WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Destination #$id");
            $alert = ['type' => 'success', 'msg' => 'Destination deleted successfully.'];
        }

        // COURSE STREAMS ACTIONS
        if ($action === 'add_stream') {
            $name = trim((string)$_POST['name']);
            $icon = trim((string)$_POST['icon']);
            $order = (int)$_POST['display_order'];
            $desc = trim((string)$_POST['desc']);
            
            $img = '';
            $uploadedImg = handleFileUpload('img_file', 'course_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $stmt = $pdo->prepare('INSERT INTO course_streams (name, icon, display_order, `desc`, img) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$name, $icon, $order, $desc, $img]);
            logActivity("Added Course Stream", $name);
            $alert = ['type' => 'success', 'msg' => 'Course stream added successfully.'];
        }

        if ($action === 'edit_stream' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim((string)$_POST['name']);
            $icon = trim((string)$_POST['icon']);
            $order = (int)$_POST['display_order'];
            $desc = trim((string)$_POST['desc']);
            
            $img = trim((string)$_POST['img']);
            $uploadedImg = handleFileUpload('img_file', 'course_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $stmt = $pdo->prepare('UPDATE course_streams SET name = ?, icon = ?, display_order = ?, `desc` = ?, img = ? WHERE id = ?');
            $stmt->execute([$name, $icon, $order, $desc, $img, $id]);
            logActivity("Updated Course Stream #$id", $name);
            $alert = ['type' => 'success', 'msg' => 'Course stream updated successfully.'];
        }

        if ($action === 'delete_stream' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM course_streams WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Course Stream #$id");
            $alert = ['type' => 'success', 'msg' => 'Course stream deleted successfully.'];
        }

        // GUIDES ACTIONS
        if ($action === 'add_guide') {
            $title = trim((string)$_POST['title']);
            $slug = trim((string)$_POST['slug']);
            if ($slug === '') {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
            }
            $summary = trim((string)$_POST['summary']);
            $content = trim((string)$_POST['content']);
            $status = trim((string)$_POST['status']);
            $pubDate = !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d');
            
            $img = trim((string)$_POST['image_path']);
            $uploadedImg = handleFileUpload('img_file', 'guide_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $stmt = $pdo->prepare('INSERT INTO guides (title, slug, summary, content, image_path, publish_date, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $slug, $summary, $content, $img, $pubDate, $status]);
            logActivity("Added Guide", $title);
            $alert = ['type' => 'success', 'msg' => 'Guide added successfully.'];
        }

        if ($action === 'edit_guide' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $title = trim((string)$_POST['title']);
            $slug = trim((string)$_POST['slug']);
            if ($slug === '') {
                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
            }
            $summary = trim((string)$_POST['summary']);
            $content = trim((string)$_POST['content']);
            $status = trim((string)$_POST['status']);
            $pubDate = !empty($_POST['publish_date']) ? $_POST['publish_date'] : date('Y-m-d');
            
            $img = trim((string)$_POST['image_path']);
            $uploadedImg = handleFileUpload('img_file', 'guide_');
            if ($uploadedImg) {
                $img = $uploadedImg;
            }
            
            $stmt = $pdo->prepare('UPDATE guides SET title = ?, slug = ?, summary = ?, content = ?, image_path = ?, publish_date = ?, status = ? WHERE id = ?');
            $stmt->execute([$title, $slug, $summary, $content, $img, $pubDate, $status, $id]);
            logActivity("Updated Guide #$id", $title);
            $alert = ['type' => 'success', 'msg' => 'Guide updated successfully.'];
        }

        if ($action === 'delete_guide' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM guides WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Guide #$id");
            $alert = ['type' => 'success', 'msg' => 'Guide deleted successfully.'];
        }

        // STATIC PAGES CONTENT ACTIONS
        if ($action === 'edit_page' && isset($_POST['slug'])) {
            $slug = trim((string)$_POST['slug']);
            $title = trim((string)$_POST['title']);
            $heroTitle = trim((string)$_POST['hero_title']);
            $heroSubtitle = trim((string)$_POST['hero_subtitle']);
            $rawContent = trim((string)$_POST['content']);
            
            // Re-validate JSON
            json_decode($rawContent);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format in page content: ' . json_last_error_msg());
            }

            $stmt = $pdo->prepare('UPDATE pages SET title = ?, hero_title = ?, hero_subtitle = ?, content = ? WHERE slug = ?');
            $stmt->execute([$title, $heroTitle, $heroSubtitle, $rawContent, $slug]);
            logActivity("Updated Static Page content", $slug);
            $alert = ['type' => 'success', 'msg' => 'Page content updated successfully.'];
        }

        // NAVIGATION MENUS ACTIONS
        if ($action === 'add_menu') {
            $label = trim((string)$_POST['label']);
            $url = trim((string)$_POST['url']);
            $order = (int)$_POST['display_order'];
            $visible = isset($_POST['is_visible']) ? 1 : 0;
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            $stmt = $pdo->prepare('INSERT INTO navigation_menu (label, url, display_order, is_visible, parent_id) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$label, $url, $order, $visible, $parentId]);
            logActivity("Added Menu Item", $label);
            $alert = ['type' => 'success', 'msg' => 'Menu item added successfully.'];
        }

        if ($action === 'edit_menu' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $label = trim((string)$_POST['label']);
            $url = trim((string)$_POST['url']);
            $order = (int)$_POST['display_order'];
            $visible = isset($_POST['is_visible']) ? 1 : 0;
            $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;
            
            $stmt = $pdo->prepare('UPDATE navigation_menu SET label = ?, url = ?, display_order = ?, is_visible = ?, parent_id = ? WHERE id = ?');
            $stmt->execute([$label, $url, $order, $visible, $parentId, $id]);
            logActivity("Updated Menu Item #$id", $label);
            $alert = ['type' => 'success', 'msg' => 'Menu item updated successfully.'];
        }

        if ($action === 'delete_menu' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM navigation_menu WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Menu Item #$id");
            $alert = ['type' => 'success', 'msg' => 'Menu item deleted successfully.'];
        }

        // COUNTRIES ACTIONS
        if ($action === 'add_country') {
            $name = trim((string)$_POST['name']);
            $order = (int)$_POST['display_order'];
            
            $stmt = $pdo->prepare('INSERT INTO countries (name, display_order) VALUES (?, ?)');
            $stmt->execute([$name, $order]);
            logActivity("Added Form Option Country", $name);
            $alert = ['type' => 'success', 'msg' => 'Country option added successfully.'];
        }

        if ($action === 'edit_country' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $name = trim((string)$_POST['name']);
            $order = (int)$_POST['display_order'];
            $active = isset($_POST['is_active']) ? 1 : 0;
            
            $stmt = $pdo->prepare('UPDATE countries SET name = ?, display_order = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$name, $order, $active, $id]);
            logActivity("Updated Form Option Country #$id", $name);
            $alert = ['type' => 'success', 'msg' => 'Country option updated successfully.'];
        }

        if ($action === 'delete_country' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM countries WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Form Option Country #$id");
            $alert = ['type' => 'success', 'msg' => 'Country option deleted successfully.'];
        }

        // SOCIAL LINKS ACTIONS
        if ($action === 'add_social') {
            $platform = trim((string)$_POST['platform_name']);
            $url = trim((string)$_POST['url']);
            $icon = trim((string)$_POST['icon']);
            $order = (int)$_POST['display_order'];
            
            $stmt = $pdo->prepare('INSERT INTO social_links (platform_name, url, icon, display_order) VALUES (?, ?, ?, ?)');
            $stmt->execute([$platform, $url, $icon, $order]);
            logActivity("Added Social Link", $platform);
            $alert = ['type' => 'success', 'msg' => 'Social link added successfully.'];
        }

        if ($action === 'edit_social' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $platform = trim((string)$_POST['platform_name']);
            $url = trim((string)$_POST['url']);
            $icon = trim((string)$_POST['icon']);
            $order = (int)$_POST['display_order'];
            
            $stmt = $pdo->prepare('UPDATE social_links SET platform_name = ?, url = ?, icon = ?, display_order = ? WHERE id = ?');
            $stmt->execute([$platform, $url, $icon, $order, $id]);
            logActivity("Updated Social Link #$id", $platform);
            $alert = ['type' => 'success', 'msg' => 'Social link updated successfully.'];
        }

        if ($action === 'delete_social' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM social_links WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Social Link #$id");
            $alert = ['type' => 'success', 'msg' => 'Social link deleted successfully.'];
        }

        // FUN FACTS / STATS ACTIONS
        if ($action === 'add_fact') {
            $number = trim((string)$_POST['number']);
            $label = trim((string)$_POST['label']);
            $icon = formatFaIcon(trim((string)$_POST['icon']), $label);
            $order = (int)$_POST['display_order'];
            
            $stmt = $pdo->prepare('INSERT INTO fun_facts (number, label, icon, display_order) VALUES (?, ?, ?, ?)');
            $stmt->execute([$number, $label, $icon, $order]);
            logActivity("Added Fun Fact Statistic", $label);
            $alert = ['type' => 'success', 'msg' => 'Statistic fact added successfully.'];
        }

        if ($action === 'edit_fact' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $number = trim((string)$_POST['number']);
            $label = trim((string)$_POST['label']);
            $icon = formatFaIcon(trim((string)$_POST['icon']), $label);
            $order = (int)$_POST['display_order'];
            
            $stmt = $pdo->prepare('UPDATE fun_facts SET number = ?, label = ?, icon = ?, display_order = ? WHERE id = ?');
            $stmt->execute([$number, $label, $icon, $order, $id]);
            logActivity("Updated Fun Fact Statistic #$id", $label);
            $alert = ['type' => 'success', 'msg' => 'Statistic fact updated successfully.'];
        }

        if ($action === 'delete_fact' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM fun_facts WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Fun Fact Statistic #$id");
            $alert = ['type' => 'success', 'msg' => 'Statistic fact deleted successfully.'];
        }

        // ACCREDITATIONS CRUD ACTIONS
        if ($action === 'add_accreditation') {
            $title = trim((string)$_POST['title']);
            $category = strtolower(trim((string)$_POST['category']));
            if (!in_array($category, ['international', 'national'])) {
                $category = 'international';
            }
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $logo = trim((string)($_POST['logo'] ?? ''));
            $uploadedLogo = handleFileUpload('logo_file', 'accred_');
            if ($uploadedLogo) {
                $logo = $uploadedLogo;
            }
            
            if ($title === '') {
                throw new Exception('Accreditation Title / Name is required.');
            }
            if ($logo === '') {
                throw new Exception('Please upload an Accreditation Logo image or provide a logo path.');
            }
            
            $stmt = $pdo->prepare('INSERT INTO accreditations (title, category, logo, display_order, is_active) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$title, $category, $logo, $display_order, $is_active]);
            $accId = $pdo->lastInsertId();
            logActivity("Added Accreditation #$accId", "$title ($category)");
            $alert = ['type' => 'success', 'msg' => 'Accreditation partner added successfully.'];
        }

        if ($action === 'edit_accreditation' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $title = trim((string)$_POST['title']);
            $category = strtolower(trim((string)$_POST['category']));
            if (!in_array($category, ['international', 'national'])) {
                $category = 'international';
            }
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $logo = isset($_POST['existing_logo']) ? trim((string)$_POST['existing_logo']) : '';
            $uploadedLogo = handleFileUpload('logo_file', 'accred_');
            if ($uploadedLogo) {
                $logo = $uploadedLogo;
            }
            
            if ($title === '') {
                throw new Exception('Accreditation Title / Name is required.');
            }
            
            $stmt = $pdo->prepare('UPDATE accreditations SET title = ?, category = ?, logo = ?, display_order = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$title, $category, $logo, $display_order, $is_active, $id]);
            logActivity("Updated Accreditation #$id", "$title ($category)");
            $alert = ['type' => 'success', 'msg' => 'Accreditation partner updated successfully.'];
        }

        if ($action === 'delete_accreditation' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM accreditations WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Accreditation #$id");
            $alert = ['type' => 'success', 'msg' => 'Accreditation partner deleted successfully.'];
        }

        if ($action === 'toggle_accreditation_status' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('UPDATE accreditations SET is_active = CASE WHEN is_active = 1 THEN 0 ELSE 1 END WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Toggled Accreditation Status #$id");
            $alert = ['type' => 'success', 'msg' => 'Accreditation status updated successfully.'];
        }

        if ($action === 'update_accreditation_settings') {
            $acc_title = trim((string)($_POST['accreditation_title'] ?? 'Accreditation'));
            $acc_sub = trim((string)($_POST['accreditation_subtitle'] ?? 'Trusted excellence backed by top accreditations'));
            $acc_enabled = isset($_POST['accreditation_enabled']) ? '1' : '0';
            
            $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
            $stmt->execute(['accreditation_title', $acc_title]);
            $stmt->execute(['accreditation_subtitle', $acc_sub]);
            $stmt->execute(['accreditation_enabled', $acc_enabled]);
            
            logActivity("Updated Accreditation Section Settings");
            $alert = ['type' => 'success', 'msg' => 'Accreditation section settings updated successfully.'];
        }

        // SEO MANAGER & 301 REDIRECT ACTIONS
        if ($action === 'add_seo_page') {
            $page_name = trim((string)$_POST['page_name']);
            $slug = trim((string)$_POST['slug']);
            $meta_title = trim((string)$_POST['meta_title']);
            $meta_description = trim((string)$_POST['meta_description']);
            $meta_keywords = trim((string)$_POST['meta_keywords']);
            $canonical_url = trim((string)$_POST['canonical_url']);
            $og_title = trim((string)$_POST['og_title']);
            $og_description = trim((string)$_POST['og_description']);
            $twitter_card = trim((string)$_POST['twitter_card'] ?: 'summary_large_image');
            $robots = trim((string)$_POST['robots'] ?: 'index, follow');
            $schema_type = trim((string)$_POST['schema_type'] ?: 'WebPage');
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            $og_image = trim((string)($_POST['og_image'] ?? ''));
            $uploadedOg = handleFileUpload('og_image_file', 'seo_');
            if ($uploadedOg) {
                $og_image = $uploadedOg;
            }

            if ($page_name === '' || $slug === '') {
                throw new Exception('Page Name and Slug are required.');
            }

            $stmt = $pdo->prepare("INSERT INTO seo_pages (page_name, slug, meta_title, meta_description, meta_keywords, canonical_url, og_title, og_description, og_image, twitter_card, robots, schema_type, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            $stmt->execute([$page_name, $slug, $meta_title, $meta_description, $meta_keywords, $canonical_url, $og_title, $og_description, $og_image, $twitter_card, $robots, $schema_type, $status]);
            $seoId = $pdo->lastInsertId();
            logActivity("Added SEO Route #$seoId", "$page_name ($slug)");
            $alert = ['type' => 'success', 'msg' => 'SEO route added successfully.'];
        }

        if ($action === 'edit_seo_page' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $page_name = trim((string)$_POST['page_name']);
            $slug = trim((string)$_POST['slug']);
            $meta_title = trim((string)$_POST['meta_title']);
            $meta_description = trim((string)$_POST['meta_description']);
            $meta_keywords = trim((string)$_POST['meta_keywords']);
            $canonical_url = trim((string)$_POST['canonical_url']);
            $og_title = trim((string)$_POST['og_title']);
            $og_description = trim((string)$_POST['og_description']);
            $twitter_card = trim((string)$_POST['twitter_card'] ?: 'summary_large_image');
            $robots = trim((string)$_POST['robots'] ?: 'index, follow');
            $schema_type = trim((string)$_POST['schema_type'] ?: 'WebPage');
            $status = isset($_POST['status']) ? (int)$_POST['status'] : 1;

            $og_image = isset($_POST['existing_og_image']) ? trim((string)$_POST['existing_og_image']) : '';
            $uploadedOg = handleFileUpload('og_image_file', 'seo_');
            if ($uploadedOg) {
                $og_image = $uploadedOg;
            }

            if ($page_name === '' || $slug === '') {
                throw new Exception('Page Name and Slug are required.');
            }

            $stmt = $pdo->prepare("UPDATE seo_pages SET page_name = ?, slug = ?, meta_title = ?, meta_description = ?, meta_keywords = ?, canonical_url = ?, og_title = ?, og_description = ?, og_image = ?, twitter_card = ?, robots = ?, schema_type = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$page_name, $slug, $meta_title, $meta_description, $meta_keywords, $canonical_url, $og_title, $og_description, $og_image, $twitter_card, $robots, $schema_type, $status, $id]);
            logActivity("Updated SEO Route #$id", "$page_name ($slug)");
            $alert = ['type' => 'success', 'msg' => 'SEO settings updated successfully.'];
        }

        if ($action === 'delete_seo_page' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM seo_pages WHERE id = ?");
            $stmt->execute([$id]);
            logActivity("Deleted SEO Route #$id");
            $alert = ['type' => 'success', 'msg' => 'SEO page configuration deleted.'];
        }

        if ($action === 'add_seo_redirect') {
            $old_url = trim((string)$_POST['old_url']);
            $new_url = trim((string)$_POST['new_url']);
            $status_code = isset($_POST['status_code']) ? (int)$_POST['status_code'] : 301;
            $notes = trim((string)($_POST['notes'] ?? ''));
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($old_url === '' || $new_url === '') {
                throw new Exception('Source URL and Target URL are required.');
            }

            $stmt = $pdo->prepare("INSERT INTO seo_redirects (old_url, new_url, status_code, notes, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$old_url, $new_url, $status_code, $notes, $is_active]);
            $redirId = $pdo->lastInsertId();
            logActivity("Added 301 Redirect #$redirId", "$old_url -> $new_url");
            $alert = ['type' => 'success', 'msg' => '301 Redirect added successfully.'];
        }

        if ($action === 'edit_seo_redirect' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $old_url = trim((string)$_POST['old_url']);
            $new_url = trim((string)$_POST['new_url']);
            $status_code = isset($_POST['status_code']) ? (int)$_POST['status_code'] : 301;
            $notes = trim((string)($_POST['notes'] ?? ''));
            $is_active = isset($_POST['is_active']) ? 1 : 0;

            if ($old_url === '' || $new_url === '') {
                throw new Exception('Source URL and Target URL are required.');
            }

            $stmt = $pdo->prepare("UPDATE seo_redirects SET old_url = ?, new_url = ?, status_code = ?, notes = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$old_url, $new_url, $status_code, $notes, $is_active, $id]);
            logActivity("Updated Redirect #$id", "$old_url -> $new_url");
            $alert = ['type' => 'success', 'msg' => '301 Redirect updated successfully.'];
        }

        if ($action === 'delete_seo_redirect' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare("DELETE FROM seo_redirects WHERE id = ?");
            $stmt->execute([$id]);
            logActivity("Deleted Redirect #$id");
            $alert = ['type' => 'success', 'msg' => '301 Redirect deleted successfully.'];
        }

        // FILE MANAGER ACTIONS
        if ($action === 'upload_file') {
            $uploadedPath = handleFileUpload('file', 'mgr_');
            if ($uploadedPath) {
                logActivity("Uploaded File", basename($uploadedPath));
                $alert = ['type' => 'success', 'msg' => 'File uploaded successfully to: ' . $uploadedPath];
            }
        }
        
        if ($action === 'delete_file' && isset($_POST['filename'])) {
            $file = basename($_POST['filename']);
            $path = __DIR__ . '/../uploads/' . $file;
            if (file_exists($path)) {
                unlink($path);
                logActivity("Deleted Uploaded File", $file);
                $alert = ['type' => 'success', 'msg' => "File '$file' deleted successfully."];
            } else {
                throw new Exception("File not found.");
            }
        }

        // ADMIN USERS CRUD
        if ($action === 'add_admin') {
            $email = trim((string)$_POST['email']);
            $password = (string)$_POST['password'];
            if ($email === '' || $password === '') {
                throw new Exception('Email and Password are required.');
            }
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (?, ?)');
            $stmt->execute([$email, $hash]);
            logActivity("Added Admin User", $email);
            $alert = ['type' => 'success', 'msg' => 'Administrator added successfully.'];
        }

        if ($action === 'edit_admin' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $email = trim((string)$_POST['email']);
            $password = (string)$_POST['password'];
            
            if ($email === '') {
                throw new Exception('Email is required.');
            }
            
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('UPDATE admin_users SET email = ?, password_hash = ? WHERE id = ?');
                $stmt->execute([$email, $hash, $id]);
            } else {
                $stmt = $pdo->prepare('UPDATE admin_users SET email = ? WHERE id = ?');
                $stmt->execute([$email, $id]);
            }
            logActivity("Updated Admin User #$id", $email);
            $alert = ['type' => 'success', 'msg' => 'Administrator updated successfully.'];
        }

        if ($action === 'delete_admin' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            if ($id === (int)$_SESSION['admin_id']) {
                throw new Exception('You cannot delete your own administrator account.');
            }
            $stmt = $pdo->prepare('DELETE FROM admin_users WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Admin User #$id");
            $alert = ['type' => 'success', 'msg' => 'Administrator deleted successfully.'];
        }

        // SITE CONFIGURATIONS ACTIONS
        if ($action === 'update_site_settings') {
            $settingsToUpdate = [
                'primary_color', 'primary_light', 'secondary_color', 'secondary_hover',
                'site_title', 'site_name', 'hero_title', 'hero_subtitle', 'contact_phone',
                'contact_email', 'contact_location', 'footer_about', 'footer_copyright',
                'qr_code_title', 'qr_code_description', 'notification_title', 'notification_message',
                'notification_banner_color', 'notification_link', 'accreditation_title', 'accreditation_subtitle',
                'gsc_verification_code', 'ga_measurement_id'
            ];
            
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('INSERT INTO site_settings (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)');
            foreach ($settingsToUpdate as $key) {
                $val = isset($_POST[$key]) ? (string)$_POST[$key] : '';
                $stmt->execute([$key, $val]);
            }
            
            // Handle Notification and Auto-Popup enabled checkboxes explicitly
            $notification_enabled = isset($_POST['notification_enabled']) ? '1' : '0';
            $stmt->execute(['notification_enabled', $notification_enabled]);

            $auto_popup_enabled = isset($_POST['auto_popup_enabled']) ? '1' : '0';
            $stmt->execute(['auto_popup_enabled', $auto_popup_enabled]);

            // Handle QR code enabled checkbox explicitly
            $qr_enabled = isset($_POST['qr_code_enabled']) ? '1' : '0';
            $stmt->execute(['qr_code_enabled', $qr_enabled]);

            // Handle Accreditation section enabled checkbox explicitly
            $accred_enabled = isset($_POST['accreditation_enabled']) ? '1' : '0';
            $stmt->execute(['accreditation_enabled', $accred_enabled]);

            
            // Handle Site Logo Upload override
            $uploadedLogo = handleFileUpload('site_logo_file', 'logo_');
            if ($uploadedLogo) {
                $stmt->execute(['site_logo', $uploadedLogo]);
            }
            
            // Handle Hero Image Upload override
            $uploadedHero = handleFileUpload('hero_banner_image_file', 'banner_');
            if ($uploadedHero) {
                $stmt->execute(['hero_banner_image', $uploadedHero]);
            }
            
            // Handle QR code image Upload override
            $uploadedQR = handleFileUpload('qr_code_image_file', 'qr_');
            if ($uploadedQR) {
                $stmt->execute(['qr_code_image', $uploadedQR]);
            }
            
            $pdo->commit();
            logActivity("Updated Site Settings Configuration");
            $alert = ['type' => 'success', 'msg' => 'Site configurations and layouts saved successfully.'];
        }

        // BANK ACCOUNTS ACTIONS
        if ($action === 'add_bank_account') {
            $bank_name = trim((string)$_POST['bank_name']);
            $account_name = trim((string)$_POST['account_name']);
            $account_number = trim((string)$_POST['account_number']);
            $branch = trim((string)$_POST['branch']);
            $account_type = trim((string)$_POST['account_type']);
            $swift_code = trim((string)$_POST['swift_code']);
            $ifsc_code = trim((string)($_POST['ifsc_code'] ?? ''));
            $instructions = trim((string)$_POST['instructions']);
            $remarks = trim((string)($_POST['remarks'] ?? ''));
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;

            if ($bank_name === '' || $account_name === '' || $account_number === '') {
                throw new Exception('Bank Name, Account Name, and Account Number are required.');
            }

            if ($is_default === 1) {
                $pdo->exec("UPDATE bank_accounts SET is_default = 0");
            }

            $stmt = $pdo->prepare('INSERT INTO bank_accounts (bank_name, account_name, account_number, branch, account_type, swift_code, ifsc_code, instructions, remarks, display_order, is_active, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([$bank_name, $account_name, $account_number, $branch, $account_type, $swift_code, $ifsc_code, $instructions, $remarks, $display_order, $is_active, $is_default]);
            $bankId = $pdo->lastInsertId();
            logActivity("Added Bank Account #$bankId", $bank_name);
            $alert = ['type' => 'success', 'msg' => 'Bank account added successfully.'];
        }

        if ($action === 'edit_bank_account' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $bank_name = trim((string)$_POST['bank_name']);
            $account_name = trim((string)$_POST['account_name']);
            $account_number = trim((string)$_POST['account_number']);
            $branch = trim((string)$_POST['branch']);
            $account_type = trim((string)$_POST['account_type']);
            $swift_code = trim((string)$_POST['swift_code']);
            $ifsc_code = trim((string)($_POST['ifsc_code'] ?? ''));
            $instructions = trim((string)$_POST['instructions']);
            $remarks = trim((string)($_POST['remarks'] ?? ''));
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            $is_default = isset($_POST['is_default']) ? 1 : 0;

            if ($bank_name === '' || $account_name === '' || $account_number === '') {
                throw new Exception('Bank Name, Account Name, and Account Number are required.');
            }

            if ($is_default === 1) {
                $pdo->exec("UPDATE bank_accounts SET is_default = 0");
            }

            $stmt = $pdo->prepare('UPDATE bank_accounts SET bank_name = ?, account_name = ?, account_number = ?, branch = ?, account_type = ?, swift_code = ?, ifsc_code = ?, instructions = ?, remarks = ?, display_order = ?, is_active = ?, is_default = ? WHERE id = ?');
            $stmt->execute([$bank_name, $account_name, $account_number, $branch, $account_type, $swift_code, $ifsc_code, $instructions, $remarks, $display_order, $is_active, $is_default, $id]);
            logActivity("Updated Bank Account #$id", $bank_name);
            $alert = ['type' => 'success', 'msg' => 'Bank account updated successfully.'];
        }

        if ($action === 'delete_bank_account' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $stmt = $pdo->prepare('DELETE FROM bank_accounts WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Bank Account #$id");
            $alert = ['type' => 'success', 'msg' => 'Bank account deleted successfully.'];
        }

        if ($action === 'delete_qr_code') {
            $stmt = $pdo->prepare("UPDATE site_settings SET value = '' WHERE `key` = 'qr_code_image'");
            $stmt->execute();
            logActivity("Deleted Payment QR Code");
            $alert = ['type' => 'success', 'msg' => 'Payment QR Code image deleted successfully.'];
        }

        // INSTITUTION PDFS ACTIONS
        if ($action === 'add_institution_pdf') {
            $institution_id = (int)$_POST['institution_id'];
            $title = trim((string)$_POST['title']);
            $description = trim((string)$_POST['description']);
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $file_path = handlePdfUpload('pdf_file', 'pdfdoc_');
            if (!$file_path) {
                throw new Exception('A valid PDF file upload is required.');
            }
            
            if ($title === '' || $institution_id <= 0) {
                throw new Exception('PDF Title and Partner Institution are required.');
            }
            
            $stmt = $pdo->prepare('INSERT INTO institution_pdfs (institution_id, title, description, file_path, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$institution_id, $title, $description, $file_path, $display_order, $is_active]);
            $pdfId = $pdo->lastInsertId();
            logActivity("Uploaded Institution PDF #$pdfId", $title);
            $alert = ['type' => 'success', 'msg' => 'University PDF document uploaded successfully.'];
        }

        if ($action === 'edit_institution_pdf' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            $institution_id = (int)$_POST['institution_id'];
            $title = trim((string)$_POST['title']);
            $description = trim((string)$_POST['description']);
            $display_order = isset($_POST['display_order']) ? (int)$_POST['display_order'] : 0;
            $is_active = isset($_POST['is_active']) ? 1 : 0;
            
            $file_path = isset($_POST['existing_file_path']) ? trim((string)$_POST['existing_file_path']) : '';
            $uploadedPdf = handlePdfUpload('pdf_file', 'pdfdoc_');
            if ($uploadedPdf) {
                $file_path = $uploadedPdf;
            }
            
            if ($title === '' || $institution_id <= 0 || $file_path === '') {
                throw new Exception('PDF Title, Partner Institution, and File are required.');
            }
            
            $stmt = $pdo->prepare('UPDATE institution_pdfs SET institution_id = ?, title = ?, description = ?, file_path = ?, display_order = ?, is_active = ? WHERE id = ?');
            $stmt->execute([$institution_id, $title, $description, $file_path, $display_order, $is_active, $id]);
            logActivity("Updated Institution PDF #$id", $title);
            $alert = ['type' => 'success', 'msg' => 'University PDF document updated successfully.'];
        }

        if ($action === 'delete_institution_pdf' && isset($_POST['id'])) {
            $id = (int)$_POST['id'];
            
            $stmt = $pdo->prepare('SELECT file_path FROM institution_pdfs WHERE id = ?');
            $stmt->execute([$id]);
            $file = $stmt->fetchColumn();
            if ($file && file_exists(__DIR__ . '/../' . $file)) {
                unlink(__DIR__ . '/../' . $file);
            }
            
            $stmt = $pdo->prepare('DELETE FROM institution_pdfs WHERE id = ?');
            $stmt->execute([$id]);
            logActivity("Deleted Institution PDF #$id");
            $alert = ['type' => 'success', 'msg' => 'University PDF document deleted successfully.'];
        }

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $alert = ['type' => 'danger', 'msg' => 'Error: ' . $e->getMessage()];
    }
}

// Default stats & lists initialization
$totalEnrollments = 0;
$totalInquiries = 0;
$totalSubscribers = 0;
$enrollmentStatusCounts = [];
$inquiryStatusCounts = [];
$enrollments = [];
$inquiries = [];
$subscribers = [];
$dbReviews = [];
$dbInstitutions = [];
$dbDestinations = [];
$dbStreams = [];
$dbGuides = [];
$dbPages = [];
$dbMenus = [];
$dbCountries = [];
$dbSocials = [];
$dbFacts = [];
$dbAdmins = [];
$dbLogs = [];
$dbPrograms = [];
$dbBankAccounts = [];
$dbInstitutionPdfs = [];
$streamMap = [];
$siteSettings = [];
$cities = [];
$faculties = [];
$levels = [];
$countries = [];
$uniRegions = [];
$uniCities = [];
$months = [];
$enrollTrend = [];
$inqTrend = [];
$facultyLabels = [];
$facultyData = [];
$uploadedFiles = [];

// Check if database is active
if (!isset($pdo) || !$pdo) {
    $alert = ['type' => 'warning', 'msg' => 'Database is not connected (' . htmlspecialchars($dbError ?? 'Check config.php') . '). Please update your database credentials in config.php.'];
} else {
    // Fetch totals & lists
    try {
        $totalEnrollments = $pdo->query('SELECT COUNT(*) FROM enrollments')->fetchColumn() ?: 0;
        $totalInquiries = $pdo->query('SELECT COUNT(*) FROM inquiries')->fetchColumn() ?: 0;
        $totalSubscribers = $pdo->query('SELECT COUNT(*) FROM newsletter_subscribers')->fetchColumn() ?: 0;
        
        // Status breakdowns
        $enrollmentStatusCounts = $pdo->query("SELECT status, COUNT(*) as count FROM enrollments GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
        $inquiryStatusCounts = $pdo->query("SELECT status, COUNT(*) as count FROM inquiries GROUP BY status")->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // Fetch lists
        $enrollments = $pdo->query('SELECT * FROM enrollments ORDER BY created_at DESC')->fetchAll() ?: [];
        $inquiries = $pdo->query('SELECT * FROM inquiries ORDER BY created_at DESC')->fetchAll() ?: [];
        $subscribers = $pdo->query('SELECT * FROM newsletter_subscribers ORDER BY created_at DESC')->fetchAll() ?: [];
        try {
            $dbReviews = $pdo->query('SELECT * FROM reviews ORDER BY created_at DESC')->fetchAll() ?: [];
        } catch (\Throwable $e) {
            $dbReviews = [];
        }
        $dbInstitutions = $pdo->query('SELECT * FROM institutions ORDER BY created_at DESC')->fetchAll() ?: [];
        $dbDestinations = $pdo->query('SELECT * FROM destinations ORDER BY display_order ASC, name ASC')->fetchAll() ?: [];
        $dbStreams = $pdo->query('SELECT * FROM course_streams ORDER BY display_order ASC, name ASC')->fetchAll() ?: [];
        $dbGuides = $pdo->query('SELECT * FROM guides ORDER BY publish_date DESC, id DESC')->fetchAll() ?: [];
        $dbPages = $pdo->query('SELECT * FROM pages ORDER BY slug ASC')->fetchAll() ?: [];
        $dbMenus = $pdo->query('SELECT * FROM navigation_menu ORDER BY display_order ASC')->fetchAll() ?: [];
        $dbCountries = $pdo->query('SELECT * FROM countries ORDER BY display_order ASC, name ASC')->fetchAll() ?: [];
        $dbSocials = $pdo->query('SELECT * FROM social_links ORDER BY display_order ASC')->fetchAll() ?: [];
        $dbFacts = $pdo->query('SELECT * FROM fun_facts ORDER BY display_order ASC')->fetchAll() ?: [];
        $dbAdmins = $pdo->query('SELECT * FROM admin_users ORDER BY id ASC')->fetchAll() ?: [];
        $dbLogs = $pdo->query('SELECT * FROM activity_log ORDER BY created_at DESC LIMIT 100')->fetchAll() ?: [];
        $dbPrograms = $pdo->query('SELECT * FROM programs ORDER BY created_at DESC')->fetchAll() ?: [];
        $dbBankAccounts = $pdo->query('SELECT * FROM bank_accounts ORDER BY display_order ASC, bank_name ASC')->fetchAll() ?: [];
        $dbInstitutionPdfs = $pdo->query('SELECT ip.*, i.name as institution_name FROM institution_pdfs ip JOIN institutions i ON ip.institution_id = i.id ORDER BY ip.display_order ASC, ip.title ASC')->fetchAll() ?: [];
        
        // Accreditations auto-table & fetch
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS `accreditations` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(150) NOT NULL,
                `category` VARCHAR(50) NOT NULL DEFAULT 'international',
                `logo` VARCHAR(255) NOT NULL,
                `display_order` INT DEFAULT 0,
                `is_active` TINYINT DEFAULT 1,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // Auto-seed if empty
            $accCount = $pdo->query("SELECT COUNT(*) FROM accreditations")->fetchColumn();
            if ($accCount == 0) {
                $stmtSeedAcc = $pdo->prepare("INSERT INTO accreditations (title, category, logo, display_order, is_active) VALUES (?, ?, ?, ?, ?)");
                $defaultAccreds = [
                    ['International Accreditation 1', 'international', 'int1.jpeg', 1, 1],
                    ['International Accreditation 2', 'international', 'int2.jpeg', 2, 1],
                    ['International Accreditation 3', 'international', 'int3.jpeg', 3, 1],
                    ['Nepal Government / MOEST', 'national', 'nat1.jpeg', 1, 1],
                    ['CTEVT Accreditation', 'national', 'nat2.jpeg', 2, 1],
                    ['National Council Accreditation', 'national', 'nat3.jpeg', 3, 1]
                ];
                foreach ($defaultAccreds as $dAcc) {
                    $stmtSeedAcc->execute($dAcc);
                }
            }
            $dbAccreditations = $pdo->query('SELECT * FROM accreditations ORDER BY category ASC, display_order ASC, id ASC')->fetchAll() ?: [];
        } catch (\Throwable $e) {
            $dbAccreditations = [];
        }

        // Fetch SEO Pages and 301 Redirects
        try {
            $dbSeoPages = $pdo->query('SELECT * FROM seo_pages ORDER BY id ASC')->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            $dbSeoPages = [];
        }

        try {
            $dbSeoRedirects = $pdo->query('SELECT * FROM seo_redirects ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (\Throwable $e) {
            $dbSeoRedirects = [];
        }

        // Map course stream names for display lists
        foreach ($dbStreams as $s) {
            $streamMap[$s['id']] = $s['name'];
        }
        
        // Fetch Settings
        $stmtSettings = $pdo->query('SELECT `key`, `value` FROM site_settings');
        $siteSettings = $stmtSettings->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        // Dynamically obtain unique filter values
        $cities = array_unique(array_filter(array_column($enrollments, 'interested_city')));
        $faculties = array_unique(array_filter(array_column($enrollments, 'faculty')));
        $levels = array_unique(array_filter(array_column($enrollments, 'level')));
        $countries = array_unique(array_filter(array_column($inquiries, 'country')));
        
        $uniRegions = array_unique(array_filter(array_column($dbInstitutions, 'region')));
        $uniCities = array_unique(array_filter(array_column($dbInstitutions, 'city')));

        // Build Chart.js lead statistics
        for ($i = 5; $i >= 0; $i--) {
            $m = date('Y-m', strtotime("-$i months"));
            $months[] = date('F', strtotime("-$i months"));
            
            $stmtE = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
            $stmtE->execute([$m]);
            $enrollTrend[] = (int)$stmtE->fetchColumn();

            $stmtI = $pdo->prepare("SELECT COUNT(*) FROM inquiries WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
            $stmtI->execute([$m]);
            $inqTrend[] = (int)$stmtI->fetchColumn();
        }

        $facultyCounts = $pdo->query("SELECT faculty, COUNT(*) as count FROM enrollments WHERE faculty IS NOT NULL AND faculty != '' GROUP BY faculty ORDER BY count DESC LIMIT 8")->fetchAll() ?: [];
        $facultyLabels = array_column($facultyCounts, 'faculty');
        $facultyData = array_map('intval', array_column($facultyCounts, 'count'));

    } catch (PDOException $e) {
        $alert = ['type' => 'danger', 'msg' => 'Database Query Error: ' . $e->getMessage()];
    }
}

// File Manager uploaded files scan
$uploadDir = __DIR__ . '/../uploads/';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($uploadDir . $file)) {
            $uploadedFiles[] = [
                'name' => $file,
                'size' => filesize($uploadDir . $file),
                'date' => filemtime($uploadDir . $file),
                'path' => 'uploads/' . $file
            ];
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
        <!-- Dashboard Header -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
            <div>
                <h2 class="fw-bold text-primary mb-1">Management Portal</h2>
                <p class="text-muted mb-0">Control student leads, portal inquiries, and dynamic site configurations.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Quick Notification ON/OFF Toggle -->
                <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="toggle_notification">
                    <?php if (isset($siteSettings['notification_enabled']) && $siteSettings['notification_enabled'] === '1'): ?>
                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold" title="Site Notification Banner is currently ON. Click to turn OFF.">
                            <i class="fa-solid fa-bell me-1"></i> Notice Banner: ON
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" title="Site Notification Banner is currently OFF. Click to turn ON.">
                            <i class="fa-solid fa-bell-slash me-1"></i> Notice Banner: OFF
                        </button>
                    <?php endif; ?>
                </form>

                <!-- Quick Auto-Popup ON/OFF Toggle -->
                <form method="POST" class="d-inline">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="action" value="toggle_auto_popup">
                    <?php if (isset($siteSettings['auto_popup_enabled']) && $siteSettings['auto_popup_enabled'] === '1'): ?>
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold" title="Auto Popup Modal is currently ON. Click to turn OFF.">
                            <i class="fa-solid fa-window-restore me-1"></i> Auto Popup: ON
                        </button>
                    <?php else: ?>
                        <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold" title="Auto Popup Modal is currently OFF. Click to turn ON.">
                            <i class="fa-regular fa-window-restore me-1"></i> Auto Popup: OFF
                        </button>
                    <?php endif; ?>
                </form>

                <span class="badge bg-primary text-white py-2 px-3 rounded-pill"><i class="fa-regular fa-calendar me-2"></i><?= date('F j, Y') ?></span>
            </div>

        </div>

        <?php if ($alert): ?>
            <div class="alert alert-<?= htmlspecialchars($alert['type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                <i class="fa-solid <?= $alert['type'] === 'success' ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-danger' ?> me-2"></i>
                <?= htmlspecialchars($alert['msg']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-3">
                <div class="card dashboard-card card-enrollments border-start border-primary border-4 p-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Enrollments</div>
                        <h3 class="fw-bold text-primary mb-1 mt-1"><?= (int)$totalEnrollments ?></h3>
                        <div class="text-muted small">
                            <span class="text-warning fw-bold"><?= (int)($enrollmentStatusCounts['Pending'] ?? 0) ?></span> Pend &bull;
                            <span class="text-success fw-bold"><?= (int)($enrollmentStatusCounts['Admitted'] ?? 0) ?></span> Adm
                        </div>
                    </div>
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-graduation-cap fs-5"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card card-inquiries border-start border-warning border-4 p-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Total Inquiries</div>
                        <h3 class="fw-bold text-primary mb-1 mt-1"><?= (int)$totalInquiries ?></h3>
                        <div class="text-muted small">
                            <span class="text-warning fw-bold"><?= (int)($inquiryStatusCounts['Pending'] ?? 0) ?></span> New &bull;
                            <span class="text-info fw-bold"><?= (int)($inquiryStatusCounts['Contacted'] ?? 0) ?></span> Con
                        </div>
                    </div>
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-circle-question fs-5"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card card-subscribers border-start border-success border-4 p-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Subscribers</div>
                        <h3 class="fw-bold text-primary mb-1 mt-1"><?= (int)$totalSubscribers ?></h3>
                        <div class="text-muted small text-success"><i class="fa-solid fa-arrow-trend-up me-1"></i>Active list</div>
                    </div>
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-paper-plane fs-5"></i>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card dashboard-card border-start border-info border-4 p-3 d-flex flex-row align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">Colleges</div>
                        <h3 class="fw-bold text-primary mb-1 mt-1"><?= count($dbInstitutions) ?></h3>
                        <div class="text-muted small text-info"><i class="fa-solid fa-hotel me-1"></i>Partner list</div>
                    </div>
                    <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="fa-solid fa-university fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Tabs -->
        <div class="card dashboard-card border-light shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <ul class="nav nav-pills card-header-pills" id="adminTabs" role="tablist" style="gap: 5px; flex-wrap: wrap;">
                    <li class="nav-item"><button class="nav-link active fw-bold px-3 rounded-pill btn-sm" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-panel" type="button" role="tab">Overview</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="enrollments-tab" data-bs-toggle="tab" data-bs-target="#enrollments-panel" type="button" role="tab">Enrollments</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="inquiries-tab" data-bs-toggle="tab" data-bs-target="#inquiries-panel" type="button" role="tab">Inquiries</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="subscribers-tab" data-bs-toggle="tab" data-bs-target="#subscribers-panel" type="button" role="tab">Subscribers</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-panel" type="button" role="tab">Reviews & Feedback</button></li>
                    
                    <!-- Content CRUD Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold px-3 rounded-pill btn-sm text-dark bg-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Content CRUDs</a>
                        <ul class="dropdown-menu border-0 shadow-lg">
                            <li><button class="dropdown-item" id="universities-tab" data-bs-toggle="tab" data-bs-target="#universities-panel" type="button" role="tab">Partner Universities</button></li>
                            <li><button class="dropdown-item" id="university-pdfs-tab" data-bs-toggle="tab" data-bs-target="#university-pdfs-panel" type="button" role="tab">University PDFs</button></li>
                            <li><button class="dropdown-item" id="destinations-tab" data-bs-toggle="tab" data-bs-target="#destinations-panel" type="button" role="tab">Destinations / Cities</button></li>
                            <li><button class="dropdown-item" id="streams-tab" data-bs-toggle="tab" data-bs-target="#streams-panel" type="button" role="tab">Course Streams</button></li>
                            <li><button class="dropdown-item" id="guides-tab" data-bs-toggle="tab" data-bs-target="#guides-panel" type="button" role="tab">Guides & Blog Posts</button></li>
                            <li><button class="dropdown-item" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages-panel" type="button" role="tab">Static Page Content</button></li>
                            <li><button class="dropdown-item" id="programs-tab" data-bs-toggle="tab" data-bs-target="#programs-panel" type="button" role="tab">Available Programs</button></li>
                            <li><button class="dropdown-item" id="accreditations-tab" data-bs-toggle="tab" data-bs-target="#accreditations-panel" type="button" role="tab"><i class="fa-solid fa-award me-2 text-warning"></i>Accreditations</button></li>
                        </ul>
                    </li>

                    <!-- Configs Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold px-3 rounded-pill btn-sm text-dark bg-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Configs & Settings</a>
                        <ul class="dropdown-menu border-0 shadow-lg">
                            <li><button class="dropdown-item" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-panel" type="button" role="tab">General Site Settings</button></li>
                            <li><button class="dropdown-item" id="bank-accounts-tab" data-bs-toggle="tab" data-bs-target="#bank-accounts-panel" type="button" role="tab">Bank Accounts</button></li>
                            <li><button class="dropdown-item" id="social-tab" data-bs-toggle="tab" data-bs-target="#social-panel" type="button" role="tab">Social Media Links</button></li>
                            <li><button class="dropdown-item" id="navigation-tab" data-bs-toggle="tab" data-bs-target="#navigation-panel" type="button" role="tab">Navigation Menu</button></li>
                            <li><button class="dropdown-item" id="countries-tab" data-bs-toggle="tab" data-bs-target="#countries-panel" type="button" role="tab">Countries (Forms)</button></li>
                            <li><button class="dropdown-item" id="facts-tab" data-bs-toggle="tab" data-bs-target="#facts-panel" type="button" role="tab">Fun Facts / Stats</button></li>
                        </ul>
                    </li>

                    <!-- SEO Suite Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-bold px-3 rounded-pill btn-sm text-white" style="background: linear-gradient(135deg, #0458A0, #033663);" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-chart-line me-1 text-warning"></i> SEO Suite
                        </a>
                        <ul class="dropdown-menu border-0 shadow-lg">
                            <li><button class="dropdown-item" id="seo-pages-tab" data-bs-toggle="tab" data-bs-target="#seo-pages-panel" type="button" role="tab"><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>SEO Manager (SERP)</button></li>
                            <li><button class="dropdown-item" id="seo-audit-tab" data-bs-toggle="tab" data-bs-target="#seo-audit-panel" type="button" role="tab"><i class="fa-solid fa-stethoscope me-2 text-success"></i>SEO Diagnostic Audit</button></li>
                            <li><button class="dropdown-item" id="seo-redirects-tab" data-bs-toggle="tab" data-bs-target="#seo-redirects-panel" type="button" role="tab"><i class="fa-solid fa-arrow-turn-down me-2 text-warning"></i>301 Redirects Manager</button></li>
                        </ul>
                    </li>

                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="admins-tab" data-bs-toggle="tab" data-bs-target="#admins-panel" type="button" role="tab">Admins</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="filemanager-tab" data-bs-toggle="tab" data-bs-target="#filemanager-panel" type="button" role="tab">File Manager</button></li>
                    <li class="nav-item"><button class="nav-link fw-bold px-3 rounded-pill btn-sm" id="logs-tab" data-bs-toggle="tab" data-bs-target="#logs-panel" type="button" role="tab">Logs</button></li>
                </ul>
            </div>
            <div class="card-body px-0 px-md-3">
                <div class="tab-content" id="adminTabsContent">
                    
                    <!-- OVERVIEW PANEL -->
                    <div class="tab-pane fade show active" id="overview-panel" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-12 col-lg-8">
                                <div class="card p-3 shadow-sm border border-light">
                                    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-chart-line me-2 text-warning"></i>Lead Trends (Last 6 Months)</h5>
                                    <canvas id="trendsChart" style="max-height: 300px;"></canvas>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4">
                                <div class="card p-3 shadow-sm border border-light h-100">
                                    <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-chart-pie me-2 text-warning"></i>Course Preference</h5>
                                    <canvas id="facultyChart" style="max-height: 250px;"></canvas>
                                </div>
                            </div>
                            
                            <!-- Recent Activity Log Inside Overview -->
                            <div class="col-12">
                                <div class="card p-3 shadow-sm border border-light">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Recent Operations</h5>
                                        <button onclick="switchTab('logs-tab')" class="btn btn-outline-primary btn-sm rounded-pill px-3">View All Logs</button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Admin</th>
                                                    <th>Operation Action</th>
                                                    <th>IP Address</th>
                                                    <th>Logged At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($dbLogs, 0, 5) as $log): ?>
                                                    <tr>
                                                        <td><strong><?= h($log['admin_email']) ?></strong></td>
                                                        <td><span class="badge bg-light text-primary"><?= h($log['action']) ?></span> <span class="text-muted small"><?= h($log['details']) ?></span></td>
                                                        <td><code class="small text-secondary"><?= h($log['ip_address']) ?></code></td>
                                                        <td><span class="small text-muted"><?= date('M j, Y H:i', strtotime($log['created_at'])) ?></span></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ENROLLMENTS TAB -->
                    <div class="tab-pane fade" id="enrollments-panel" role="tabpanel">
                        <div class="filter-bar">
                            <div class="row g-3">
                                <div class="col-12 col-md-4">
                                    <div class="search-input-group">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <input type="text" id="enrollSearch" class="form-control" placeholder="Search by name, email, phone...">
                                    </div>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select id="enrollLevelFilter" class="form-select filter-select">
                                        <option value="">All Levels</option>
                                        <?php foreach ($levels as $l): ?>
                                            <option value="<?= htmlspecialchars($l) ?>"><?= htmlspecialchars($l) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select id="enrollFacultyFilter" class="form-select filter-select">
                                        <option value="">All Streams</option>
                                        <?php foreach ($faculties as $f): ?>
                                            <option value="<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select id="enrollCityFilter" class="form-select filter-select">
                                        <option value="">All Cities</option>
                                        <?php foreach ($cities as $c): ?>
                                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select id="enrollStatusFilter" class="form-select filter-select">
                                        <option value="">All Statuses</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Under Review">Under Review</option>
                                        <option value="Contacted">Contacted</option>
                                        <option value="Admitted">Admitted</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div id="enrollBulkBar" class="bulk-actions-wrap mb-0" style="display:none; padding:6px 15px;">
                                <div><span class="badge bg-primary me-2" id="enrollSelectedCount">0</span> selected</div>
                                <form method="POST" id="enrollBulkForm" onsubmit="return confirm('Delete selected?');" class="ms-3 d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="bulk_delete_enrollments">
                                    <div id="enrollBulkCheckboxContainer"></div>
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3"><i class="fa-solid fa-trash me-2"></i>Delete</button>
                                </form>
                            </div>
                            <div class="ms-auto">
                                <a id="btnExportPDFEnrollments" href="export_pdf.php?type=enrollments" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-2"><i class="fa-solid fa-file-pdf me-1"></i>PDF Report</a>
                                <a href="export_csv.php?type=enrollments" target="_blank" class="btn btn-export btn-sm"><i class="fa-solid fa-file-csv me-1"></i>CSV Export</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0" id="enrollments-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center"><input type="checkbox" id="enrollSelectAll" class="custom-control-input"></th>
                                        <th>Photo</th>
                                        <th>Details</th>
                                        <th>Academics</th>
                                        <th>Course/City Preferred</th>
                                        <th>Pipeline Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($enrollments)): ?>
                                        <tr><td colspan="7" class="text-center text-muted py-5"><i class="fa-regular fa-folder-open fs-2 mb-2 d-block"></i>No student records found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($enrollments as $enroll): 
                                            $statusClass = strtolower(str_replace(' ', '-', $enroll['status'] ?? 'pending'));
                                        ?>
                                            <tr data-level="<?= htmlspecialchars($enroll['level']) ?>" data-faculty="<?= htmlspecialchars($enroll['faculty']) ?>" data-city="<?= htmlspecialchars($enroll['interested_city']) ?>" data-status="<?= htmlspecialchars($enroll['status'] ?? 'Pending') ?>">
                                                <td class="text-center"><input type="checkbox" name="row_checkbox" class="custom-control-input enroll-select-checkbox" value="<?= $enroll['id'] ?>"></td>
                                                <td>
                                                    <?php if (!empty($enroll['photo_path'])): ?>
                                                        <img src="../<?= htmlspecialchars($enroll['photo_path']) ?>" alt="Photo" class="rounded-circle border" style="width: 48px; height: 48px; object-fit: cover; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#viewEnrollModal<?= $enroll['id'] ?>">
                                                    <?php else: ?>
                                                        <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                                            <i class="fa-solid fa-user fs-6"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary"><?= htmlspecialchars($enroll['full_name']) ?></div>
                                                    <div class="text-muted small"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($enroll['email']) ?></div>
                                                    <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($enroll['phone_number']) ?></div>
                                                    <div class="text-muted small"><i class="fa-solid fa-map-marker-alt me-1"></i><?= htmlspecialchars($enroll['address']) ?></div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?= htmlspecialchars($enroll['highest_education']) ?></div>
                                                    <div class="text-muted small">Score: <?= htmlspecialchars($enroll['percentage']) ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-primary"><?= htmlspecialchars($enroll['level']) ?> - <?= htmlspecialchars($enroll['faculty']) ?></span>
                                                    <?php if (!empty($enroll['interested_country'])): ?>
                                                        <span class="badge bg-light text-success"><i class="fa-solid fa-earth-americas me-1"></i><?= htmlspecialchars($enroll['interested_country']) ?></span>
                                                    <?php endif; ?>
                                                    <div class="text-muted small mt-1"><i class="fa-solid fa-university me-1"></i><?= htmlspecialchars($enroll['interested_college']) ?></div>
                                                    <div class="text-muted small"><i class="fa-solid fa-location-dot me-1"></i><?= htmlspecialchars($enroll['interested_city']) ?></div>
                                                </td>
                                                <td>
                                                    <span id="badge-enrollment-<?= $enroll['id'] ?>" class="status-badge status-<?= $statusClass ?> mb-2">
                                                        <?= htmlspecialchars($enroll['status'] ?? 'Pending') ?>
                                                    </span>
                                                    <select class="status-select-inline d-block status-select-ajax" data-id="<?= $enroll['id'] ?>" data-type="enrollment">
                                                        <option value="Pending" <?= ($enroll['status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="Under Review" <?= ($enroll['status'] ?? '') === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                                        <option value="Contacted" <?= ($enroll['status'] ?? '') === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                                                        <option value="Admitted" <?= ($enroll['status'] ?? '') === 'Admitted' ? 'selected' : '' ?>>Admitted</option>
                                                        <option value="Cancelled" <?= ($enroll['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#viewEnrollModal<?= $enroll['id'] ?>"><i class="fa-solid fa-eye"></i></button>
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editEnrollModal<?= $enroll['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this enrollment?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_enrollment">
                                                            <input type="hidden" name="id" value="<?= $enroll['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- VIEW ENROLLMENT DETAIL MODAL -->
                                            <div class="modal fade" id="viewEnrollModal<?= $enroll['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0">
                                                        <div class="modal-header bg-light py-3">
                                                            <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-address-card me-2"></i>Enrollment Profile</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="text-center mb-4">
                                                                <?php if (!empty($enroll['photo_path'])): ?>
                                                                    <a href="../<?= htmlspecialchars($enroll['photo_path']) ?>" target="_blank">
                                                                        <img src="../<?= htmlspecialchars($enroll['photo_path']) ?>" alt="Student Photo" class="rounded border p-1 shadow-sm" style="width: 120px; height: 120px; object-fit: cover;">
                                                                    </a>
                                                                <?php else: ?>
                                                                    <div class="bg-light text-secondary rounded-circle d-inline-flex align-items-center justify-content-center border" style="width: 100px; height: 100px;">
                                                                        <i class="fa-solid fa-user fs-1"></i>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <hr>
                                                            <div class="row g-3">
                                                                <div class="col-6"><span class="text-muted small d-block">Full Name</span><span class="fw-semibold text-dark"><?= htmlspecialchars($enroll['full_name']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Pipeline Status</span><span class="badge bg-secondary"><?= htmlspecialchars($enroll['status'] ?? 'Pending') ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Email</span><span class="small"><?= htmlspecialchars($enroll['email']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Phone</span><span class="small"><?= htmlspecialchars($enroll['phone_number']) ?></span></div>
                                                                <div class="col-12"><span class="text-muted small d-block">Address</span><span class="text-dark small"><?= htmlspecialchars($enroll['address']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Interested Country</span><span class="text-dark fw-semibold small"><?= htmlspecialchars($enroll['interested_country'] ?? 'N/A') ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Desired Level</span><span class="text-dark fw-semibold small"><?= htmlspecialchars($enroll['level']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Selected Stream</span><span class="text-dark fw-semibold small"><?= htmlspecialchars($enroll['faculty']) ?></span></div>
                                                                <div class="col-12"><span class="text-muted small d-block">Additional Message</span><div class="p-2 bg-light rounded small mt-1 text-secondary"><?= nl2br(htmlspecialchars($enroll['message'])) ?></div></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- EDIT ENROLLMENT MODAL -->
                                            <div class="modal fade" id="editEnrollModal<?= $enroll['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0">
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_enrollment">
                                                            <input type="hidden" name="id" value="<?= $enroll['id'] ?>">
                                                            <input type="hidden" name="existing_photo" value="<?= htmlspecialchars($enroll['photo_path'] ?? '') ?>">
                                                            
                                                            <div class="modal-header bg-light py-3">
                                                                <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Student Enrollment</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Full Name</label>
                                                                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($enroll['full_name']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Pipeline Status</label>
                                                                        <select name="status" class="form-select">
                                                                            <option value="Pending" <?= ($enroll['status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                            <option value="Under Review" <?= ($enroll['status'] ?? '') === 'Under Review' ? 'selected' : '' ?>>Under Review</option>
                                                                            <option value="Contacted" <?= ($enroll['status'] ?? '') === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                                                                            <option value="Admitted" <?= ($enroll['status'] ?? '') === 'Admitted' ? 'selected' : '' ?>>Admitted</option>
                                                                            <option value="Cancelled" <?= ($enroll['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Email Address</label>
                                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($enroll['email']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Phone Number</label>
                                                                        <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($enroll['phone_number']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label>Address</label>
                                                                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($enroll['address']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label>Interested Country</label>
                                                                        <input type="text" name="interested_country" class="form-control" value="<?= htmlspecialchars($enroll['interested_country'] ?? '') ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label>Level</label>
                                                                        <input type="text" name="level" class="form-control" value="<?= htmlspecialchars($enroll['level']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label>Faculty/Stream</label>
                                                                        <input type="text" name="faculty" class="form-control" value="<?= htmlspecialchars($enroll['faculty']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label>Program Name</label>
                                                                        <input type="text" name="interested_program" class="form-control" value="<?= htmlspecialchars($enroll['interested_program']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Preferred College</label>
                                                                        <input type="text" name="interested_college" class="form-control" value="<?= htmlspecialchars($enroll['interested_college']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Interested City</label>
                                                                        <input type="text" name="interested_city" class="form-control" value="<?= htmlspecialchars($enroll['interested_city']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Prior Qualification</label>
                                                                        <input type="text" name="highest_education" class="form-control" value="<?= htmlspecialchars($enroll['highest_education']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Score / Percentage</label>
                                                                        <input type="text" name="percentage" class="form-control" value="<?= htmlspecialchars($enroll['percentage']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Passport Photo</label>
                                                                        <input type="file" name="photo" class="form-control" accept="image/*">
                                                                        <?php if (!empty($enroll['photo_path'])): ?>
                                                                            <div class="mt-2 d-flex align-items-center gap-2">
                                                                                <img src="../<?= htmlspecialchars($enroll['photo_path']) ?>" style="width: 50px; height: 50px; object-fit:cover;" class="rounded border">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input" type="checkbox" name="delete_photo" value="1" id="deletePhotoCheck<?= $enroll['id'] ?>">
                                                                                    <label class="form-check-label text-danger small" for="deletePhotoCheck<?= $enroll['id'] ?>">Delete Photo</label>
                                                                                </div>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label>Message</label>
                                                                        <textarea name="message" class="form-control" rows="3"><?= htmlspecialchars($enroll['message']) ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- INQUIRIES TAB -->
                    <div class="tab-pane fade" id="inquiries-panel" role="tabpanel">
                        <div class="filter-bar">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="search-input-group">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <input type="text" id="inquirySearch" class="form-control" placeholder="Search by name, email, phone...">
                                    </div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <select id="inquiryCountryFilter" class="form-select filter-select">
                                        <option value="">All Countries</option>
                                        <?php foreach ($countries as $c): ?>
                                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-6 col-md-3">
                                    <select id="inquiryStatusFilter" class="form-select filter-select">
                                        <option value="">All Statuses</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Contacted">Contacted</option>
                                        <option value="Closed">Closed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div id="inquiryBulkBar" class="bulk-actions-wrap mb-0" style="display:none; padding:6px 15px;">
                                <div><span class="badge bg-primary me-2" id="inquirySelectedCount">0</span> selected</div>
                                <form method="POST" id="inquiryBulkForm" onsubmit="return confirm('Delete selected?');" class="ms-3 d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="bulk_delete_inquiries">
                                    <div id="inquiryBulkCheckboxContainer"></div>
                                    <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3"><i class="fa-solid fa-trash me-2"></i>Delete</button>
                                </form>
                            </div>
                            <div class="ms-auto">
                                <a id="btnExportPDFInquiries" href="export_pdf.php?type=inquiries" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-2"><i class="fa-solid fa-file-pdf me-1"></i>PDF Report</a>
                                <a href="export_csv.php?type=inquiries" target="_blank" class="btn btn-export btn-sm"><i class="fa-solid fa-file-csv me-1"></i>CSV Export</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0" id="inquiries-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;" class="text-center"><input type="checkbox" id="inquirySelectAll" class="custom-control-input"></th>
                                        <th>Lead Details</th>
                                        <th>Interests</th>
                                        <th>Form Source</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($inquiries)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-5"><i class="fa-regular fa-folder-open fs-2 mb-2 d-block"></i>No portal inquiries found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($inquiries as $inq): 
                                            $statusClass = strtolower(str_replace(' ', '-', $inq['status'] ?? 'pending'));
                                        ?>
                                            <tr data-country="<?= htmlspecialchars($inq['country'] ?? '') ?>" data-status="<?= htmlspecialchars($inq['status'] ?? 'Pending') ?>">
                                                <td class="text-center"><input type="checkbox" name="row_checkbox" class="custom-control-input inquiry-select-checkbox" value="<?= $inq['id'] ?>"></td>
                                                <td>
                                                    <div class="fw-bold text-primary"><?= htmlspecialchars($inq['full_name']) ?></div>
                                                    <div class="text-muted small"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($inq['email']) ?></div>
                                                    <div class="text-muted small"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($inq['phone_number']) ?></div>
                                                    <?php if (!empty($inq['address'])): ?>
                                                        <div class="text-muted small"><i class="fa-solid fa-map-marker-alt me-1"></i><?= htmlspecialchars($inq['address']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark">Country: <?= htmlspecialchars($inq['country'] ?? 'N/A') ?></div>
                                                    <div class="text-muted small">Course: <?= htmlspecialchars($inq['course'] ?? 'N/A') ?></div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-secondary"><?= htmlspecialchars(str_replace('_', ' ', $inq['form_type'])) ?></span>
                                                    <div class="text-muted extra-small mt-1"><?= date('M j, Y g:i A', strtotime($inq['created_at'])) ?></div>
                                                </td>
                                                <td>
                                                    <span id="badge-inquiry-<?= $inq['id'] ?>" class="status-badge status-<?= $statusClass ?> mb-2">
                                                        <?= htmlspecialchars($inq['status'] ?? 'Pending') ?>
                                                    </span>
                                                    <select class="status-select-inline d-block status-select-ajax" data-id="<?= $inq['id'] ?>" data-type="inquiry">
                                                        <option value="Pending" <?= ($inq['status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                        <option value="Contacted" <?= ($inq['status'] ?? '') === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                                                        <option value="Closed" <?= ($inq['status'] ?? '') === 'Closed' ? 'selected' : '' ?>>Closed</option>
                                                        <option value="Cancelled" <?= ($inq['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#viewInqModal<?= $inq['id'] ?>"><i class="fa-solid fa-eye"></i></button>
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editInqModal<?= $inq['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this inquiry?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_inquiry">
                                                            <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- VIEW INQUIRY DETAIL MODAL -->
                                            <div class="modal fade" id="viewInqModal<?= $inq['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0">
                                                        <div class="modal-header bg-light py-3">
                                                            <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-circle-question me-2"></i>Inquiry Profile</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-3">
                                                                <div class="col-6"><span class="text-muted small d-block">Full Name</span><span class="fw-semibold text-dark"><?= htmlspecialchars($inq['full_name']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Pipeline Status</span><span class="badge bg-secondary"><?= htmlspecialchars($inq['status'] ?? 'Pending') ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Email</span><span class="small"><?= htmlspecialchars($inq['email']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Phone</span><span class="small"><?= htmlspecialchars($inq['phone_number']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Country Interest</span><span class="fw-semibold text-primary"><?= htmlspecialchars($inq['country'] ?? 'N/A') ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Course Interest</span><span class="fw-semibold text-primary"><?= htmlspecialchars($inq['course'] ?? 'N/A') ?></span></div>
                                                                <div class="col-12"><span class="text-muted small d-block">Inquirer Message</span><div class="p-2 bg-light rounded small mt-1 text-secondary"><?= nl2br(htmlspecialchars($inq['message'])) ?></div></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- EDIT INQUIRY MODAL -->
                                            <div class="modal fade" id="editInqModal<?= $inq['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0">
                                                        <form method="POST">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_inquiry">
                                                            <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                                                            
                                                            <div class="modal-header bg-light py-3">
                                                                <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Portal Inquiry</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Full Name</label>
                                                                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($inq['full_name']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Pipeline Status</label>
                                                                        <select name="status" class="form-select">
                                                                            <option value="Pending" <?= ($inq['status'] ?? 'Pending') === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                                            <option value="Contacted" <?= ($inq['status'] ?? '') === 'Contacted' ? 'selected' : '' ?>>Contacted</option>
                                                                            <option value="Closed" <?= ($inq['status'] ?? '') === 'Closed' ? 'selected' : '' ?>>Closed</option>
                                                                            <option value="Cancelled" <?= ($inq['status'] ?? '') === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Email Address</label>
                                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($inq['email']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Phone Number</label>
                                                                        <input type="text" name="phone_number" class="form-control" value="<?= htmlspecialchars($inq['phone_number']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label>Address</label>
                                                                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($inq['address']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Country Interest</label>
                                                                        <input type="text" name="country" class="form-control" value="<?= htmlspecialchars($inq['country']) ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label>Course Interest</label>
                                                                        <input type="text" name="course" class="form-control" value="<?= htmlspecialchars($inq['course']) ?>">
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label>Message</label>
                                                                        <textarea name="message" class="form-control" rows="3"><?= htmlspecialchars($inq['message']) ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- SUBSCRIBERS TAB -->
                    <div class="tab-pane fade" id="subscribers-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Newsletter Subscriber List</h5>
                            <div>
                                <a id="btnExportPDFSubscribers" href="export_pdf.php?type=subscribers" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-3 me-2"><i class="fa-solid fa-file-pdf me-1"></i>PDF Report</a>
                                <a href="export_csv.php?type=subscribers" target="_blank" class="btn btn-export btn-sm"><i class="fa-solid fa-file-csv me-1"></i>CSV Export</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 10%">ID</th>
                                        <th>Email Address</th>
                                        <th>Subscribed At</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($subscribers)): ?>
                                        <tr><td colspan="4" class="text-center text-muted py-5"><i class="fa-regular fa-folder-open fs-2 mb-2 d-block"></i>No newsletter subscribers found.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($subscribers as $sub): ?>
                                            <tr>
                                                <td><?= $sub['id'] ?></td>
                                                <td><strong class="text-dark"><?= htmlspecialchars($sub['email']) ?></strong></td>
                                                <td><?= date('Y-m-d H:i', strtotime($sub['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Remove subscriber?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_subscriber">
                                                        <input type="hidden" name="id" value="<?= $sub['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i> Remove</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- REVIEWS & FEEDBACK TAB -->
                    <div class="tab-pane fade" id="reviews-panel" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                            <div>
                                <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-star text-warning me-2"></i>Student Reviews & Feedback</h5>
                                <p class="text-muted small mb-0">Manage student ratings, approve/hide testimonials, edit feedback, or add custom reviews.</p>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-primary rounded-pill px-3 py-2">Total: <?= count($dbReviews) ?></span>
                                <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addReviewModal"><i class="fa-solid fa-plus me-1"></i>Add Review</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 5%">ID</th>
                                        <th>Avatar & Student</th>
                                        <th>Course / Institution</th>
                                        <th>Rating & Title</th>
                                        <th>Comment</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dbReviews)): ?>
                                        <tr><td colspan="8" class="text-center text-muted py-5"><i class="fa-regular fa-folder-open fs-2 mb-2 d-block"></i>No student reviews submitted yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($dbReviews as $rev): 
                                            $avatarImg = !empty($rev['avatar']) ? $rev['avatar'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80';
                                            $revDateVal = !empty($rev['created_at']) ? date('Y-m-d', strtotime($rev['created_at'])) : date('Y-m-d');
                                        ?>
                                            <tr>
                                                <td><strong>#<?= $rev['id'] ?></strong></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="<?= htmlspecialchars($avatarImg) ?>" alt="<?= htmlspecialchars($rev['name']) ?>" class="rounded-circle object-fit-cover shadow-sm flex-shrink-0" style="width: 40px; height: 40px; border: 1px solid #ddd;">
                                                        <div>
                                                            <div class="fw-bold text-dark"><?= htmlspecialchars($rev['name']) ?></div>
                                                            <div class="text-muted small"><i class="fa-solid fa-envelope me-1"></i><?= htmlspecialchars($rev['email']) ?></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-primary small"><i class="fa-solid fa-graduation-cap me-1"></i><?= htmlspecialchars($rev['course_college'] ?? 'N/A') ?></span>
                                                </td>
                                                <td>
                                                    <div class="text-warning small mb-1">
                                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                                            <i class="<?= $s <= (int)$rev['rating'] ? 'fa-solid' : 'fa-regular text-muted opacity-25' ?> fa-star"></i>
                                                        <?php endfor; ?>
                                                        <strong class="text-dark ms-1"><?= (int)$rev['rating'] ?>/5</strong>
                                                    </div>
                                                    <div class="fw-semibold small text-dark"><?= htmlspecialchars($rev['title'] ?? '') ?></div>
                                                </td>
                                                <td style="max-width: 240px;">
                                                    <div class="text-secondary small text-truncate" title="<?= htmlspecialchars($rev['comment']) ?>">
                                                        "<?= htmlspecialchars($rev['comment']) ?>"
                                                    </div>
                                                </td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="toggle_review_status">
                                                        <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                                                        <?php if ($rev['status'] === 'Approved'): ?>
                                                            <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-0 fw-bold" title="Click to hide from website" style="font-size: 12px;">Approved</button>
                                                        <?php else: ?>
                                                            <button type="submit" class="btn btn-sm btn-warning rounded-pill px-3 py-0 fw-bold text-dark" title="Click to approve on website" style="font-size: 12px;">Pending</button>
                                                        <?php endif; ?>
                                                    </form>
                                                </td>
                                                <td class="small text-muted"><?= date('M d, Y', strtotime($rev['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-primary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#viewReviewModal<?= $rev['id'] ?>" title="View Details"><i class="fa-solid fa-eye"></i></button>
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editReviewModal<?= $rev['id'] ?>" title="Edit Review"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_review">
                                                            <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete Review"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- VIEW REVIEW DETAIL MODAL -->
                                            <div class="modal fade" id="viewReviewModal<?= $rev['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 rounded-4">
                                                        <div class="modal-header bg-light py-3">
                                                            <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-star text-warning me-2"></i>Review Details #<?= $rev['id'] ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="row g-3">
                                                                <div class="col-6"><span class="text-muted small d-block">Student Name</span><span class="fw-bold text-dark"><?= htmlspecialchars($rev['name']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Status</span><span class="badge bg-<?= $rev['status'] === 'Approved' ? 'success' : 'warning text-dark' ?>"><?= htmlspecialchars($rev['status']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Email</span><span class="small"><?= htmlspecialchars($rev['email']) ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Course / College</span><span class="small fw-semibold text-primary"><?= htmlspecialchars($rev['course_college'] ?? 'N/A') ?></span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Rating</span><span class="text-warning fw-bold"><?= (int)$rev['rating'] ?> Stars (<?= (int)$rev['rating'] ?>/5)</span></div>
                                                                <div class="col-6"><span class="text-muted small d-block">Date Posted</span><span class="small text-muted"><?= date('M d, Y', strtotime($rev['created_at'])) ?></span></div>
                                                                <div class="col-12"><span class="text-muted small d-block">Avatar URL</span><span class="small text-break"><?= htmlspecialchars($rev['avatar'] ?? 'Default') ?></span></div>
                                                                <div class="col-12"><span class="text-muted small d-block">Headline Title</span><span class="fw-semibold text-dark"><?= htmlspecialchars($rev['title']) ?></span></div>
                                                                <div class="col-12"><span class="text-muted small d-block">Full Feedback Comment</span><div class="p-3 bg-light rounded-3 small mt-1 text-dark" style="white-space: pre-line;">"<?= htmlspecialchars($rev['comment']) ?>"</div></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- EDIT REVIEW MODAL -->
                                            <div class="modal fade" id="editReviewModal<?= $rev['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 rounded-4">
                                                        <form method="POST">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_review">
                                                            <input type="hidden" name="id" value="<?= $rev['id'] ?>">
                                                            <div class="modal-header bg-light py-3">
                                                                <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Review #<?= $rev['id'] ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="form-label fw-semibold small">Student Full Name *</label>
                                                                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($rev['name']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="form-label fw-semibold small">Email Address *</label>
                                                                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($rev['email']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="form-label fw-semibold small">Course / College (Subtitle)</label>
                                                                        <input type="text" name="course_college" class="form-control" placeholder="e.g. B.Tech CSE(SRM College)" value="<?= htmlspecialchars($rev['course_college'] ?? '') ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="form-label fw-semibold small">Student Avatar / Profile Photo URL</label>
                                                                        <input type="text" name="avatar" class="form-control" placeholder="e.g. https://images.unsplash.com/... or assets/images/user1.jpg" value="<?= htmlspecialchars($rev['avatar'] ?? '') ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label class="form-label fw-semibold small">Review Date *</label>
                                                                        <input type="date" name="created_at" class="form-control" value="<?= $revDateVal ?>">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label class="form-label fw-semibold small">Star Rating *</label>
                                                                        <select name="rating" class="form-select">
                                                                            <option value="5" <?= (int)$rev['rating'] === 5 ? 'selected' : '' ?>>5 Stars - Excellent</option>
                                                                            <option value="4" <?= (int)$rev['rating'] === 4 ? 'selected' : '' ?>>4 Stars - Very Good</option>
                                                                            <option value="3" <?= (int)$rev['rating'] === 3 ? 'selected' : '' ?>>3 Stars - Good</option>
                                                                            <option value="2" <?= (int)$rev['rating'] === 2 ? 'selected' : '' ?>>2 Stars - Fair</option>
                                                                            <option value="1" <?= (int)$rev['rating'] === 1 ? 'selected' : '' ?>>1 Star - Poor</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label class="form-label fw-semibold small">Status *</label>
                                                                        <select name="status" class="form-select">
                                                                            <option value="Approved" <?= $rev['status'] === 'Approved' ? 'selected' : '' ?>>Approved (Visible)</option>
                                                                            <option value="Pending" <?= $rev['status'] === 'Pending' ? 'selected' : '' ?>>Pending (Hidden)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label class="form-label fw-semibold small">Headline / Short Title</label>
                                                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($rev['title'] ?? '') ?>">
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label class="form-label fw-semibold small">Detailed Feedback Comment *</label>
                                                                        <textarea name="comment" class="form-control" rows="4" required><?= htmlspecialchars($rev['comment']) ?></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ADD NEW REVIEW MODAL (ADMIN) -->
                    <div class="modal fade" id="addReviewModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 rounded-4">
                                <form method="POST">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="add_review">
                                    <div class="modal-header bg-primary text-white py-3">
                                        <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-plus-circle me-2"></i>Add New Student Review</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6 form-group">
                                                <label class="form-label fw-semibold small">Student Full Name *</label>
                                                <input type="text" name="name" class="form-control" placeholder="e.g. Yugandhar Yadav" required>
                                            </div>
                                            <div class="col-12 col-md-6 form-group">
                                                <label class="form-label fw-semibold small">Email Address *</label>
                                                <input type="email" name="email" class="form-control" placeholder="e.g. yugandhar@example.com" required>
                                            </div>
                                            <div class="col-12 col-md-6 form-group">
                                                <label class="form-label fw-semibold small">Course / College (Subtitle)</label>
                                                <input type="text" name="course_college" class="form-control" placeholder="e.g. B.Tech CSE(SRM College)">
                                            </div>
                                            <div class="col-12 col-md-6 form-group">
                                                <label class="form-label fw-semibold small">Student Avatar / Profile Photo URL</label>
                                                <input type="text" name="avatar" class="form-control" placeholder="e.g. https://images.unsplash.com/... or assets/images/user1.jpg">
                                            </div>
                                            <div class="col-12 col-md-4 form-group">
                                                <label class="form-label fw-semibold small">Review Date *</label>
                                                <input type="date" name="created_at" class="form-control" value="<?= date('Y-m-d') ?>">
                                            </div>
                                            <div class="col-12 col-md-4 form-group">
                                                <label class="form-label fw-semibold small">Star Rating *</label>
                                                <select name="rating" class="form-select">
                                                    <option value="5" selected>5 Stars - Excellent</option>
                                                    <option value="4">4 Stars - Very Good</option>
                                                    <option value="3">3 Stars - Good</option>
                                                    <option value="2">2 Stars - Fair</option>
                                                    <option value="1">1 Star - Poor</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-4 form-group">
                                                <label class="form-label fw-semibold small">Status *</label>
                                                <select name="status" class="form-select">
                                                    <option value="Approved" selected>Approved (Visible)</option>
                                                    <option value="Pending">Pending (Hidden)</option>
                                                </select>
                                            </div>
                                            <div class="col-12 form-group">
                                                <label class="form-label fw-semibold small">Headline / Short Title</label>
                                                <input type="text" name="title" class="form-control" placeholder="e.g. Exceptional Counseling & Seamless Admission!">
                                            </div>
                                            <div class="col-12 form-group">
                                                <label class="form-label fw-semibold small">Detailed Feedback Comment *</label>
                                                <textarea name="comment" class="form-control" rows="4" placeholder="Write student's review content here..." required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Review</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- UNIVERSITIES PANEL (CRUD) -->
                    <div class="tab-pane fade" id="universities-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Partner Institutions</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addUniModal"><i class="fa-solid fa-plus me-1"></i>Add Institution</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Logo</th>
                                        <th>Institution Name</th>
                                        <th>Location</th>
                                        <th>Region</th>
                                        <th>Offered Courses</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Top Notch?</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbInstitutions as $uni): 
                                        $uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : '../' . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                                    ?>
                                        <tr>
                                            <td><?= $uni['id'] ?></td>
                                            <td><img src="<?= htmlspecialchars($uniImg) ?>" class="rounded border p-1" style="width: 60px; height: 35px; object-fit: contain;"></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($uni['name']) ?></strong></td>
                                            <td><span class="badge bg-light text-primary"><?= htmlspecialchars($uni['city']) ?></span></td>
                                            <td><span class="badge bg-light text-secondary"><?= htmlspecialchars($uni['region']) ?></span></td>
                                            <td>
                                                <div style="max-width: 250px; display: flex; flex-wrap: wrap; gap: 4px;">
                                                    <?php 
                                                    $uniCoursesList = !empty($uni['courses']) ? explode(',', $uni['courses']) : [];
                                                    $hasCourses = false;
                                                    foreach ($uniCoursesList as $cid) {
                                                        if (isset($streamMap[$cid])) {
                                                            $hasCourses = true;
                                                            echo '<span class="badge bg-light text-dark border" style="font-size: 10px;">' . htmlspecialchars($streamMap[$cid]) . '</span>';
                                                        }
                                                    }
                                                    if (!$hasCourses) {
                                                        echo '<span class="text-muted small">None</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-dark border"><?= (int)($uni['display_order'] ?? 0) ?></span></td>
                                            <td>
                                                <?php if (isset($uni['status']) && $uni['status'] === 'Inactive'): ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (isset($uni['is_top']) && $uni['is_top'] == 1): ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"><i class="fa-solid fa-star me-1 text-warning"></i>Yes</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-muted border rounded-pill">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editUniModal<?= $uni['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete university?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_university">
                                                        <input type="hidden" name="id" value="<?= $uni['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT UNIVERSITY MODAL -->
                                        <div class="modal fade" id="editUniModal<?= $uni['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_university">
                                                        <input type="hidden" name="id" value="<?= $uni['id'] ?>">
                                                        <input type="hidden" name="img" value="<?= htmlspecialchars($uni['img'] ?? '') ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Partner Institution</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>University Name</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($uni['name']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>City Location</label>
                                                                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($uni['city']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Region Filter</label>
                                                                <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($uni['region']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Detailed Location / Address</label>
                                                                <input type="text" name="location_detail" class="form-control" value="<?= htmlspecialchars($uni['location_detail'] ?? '') ?>" placeholder="e.g. Noida, Uttar Pradesh, India">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>About / Description</label>
                                                                <textarea name="about" class="form-control" rows="3" placeholder="Description of the university..."><?= htmlspecialchars($uni['about'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Fees & Scholarship Structure</label>
                                                                <textarea name="fees" class="form-control" rows="3" placeholder="e.g. Tuition: INR 1,50,000/yr. up to 50% merit scholarships."><?= htmlspecialchars($uni['fees'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Admission Process Detail</label>
                                                                <textarea name="admission_process" class="form-control" rows="3" placeholder="e.g. Online enrollment -> counseling interview -> provisional admission."><?= htmlspecialchars($uni['admission_process'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Google Map Link / Embed Code</label>
                                                                <textarea name="map_link" class="form-control" rows="2" placeholder="e.g. https://maps.app.goo.gl/... or <iframe src='...'></iframe>"><?= htmlspecialchars($uni['map_link'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label class="d-block mb-2">Offered Courses / Streams</label>
                                                                <div class="d-flex flex-wrap gap-3 border rounded p-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                                                                    <?php 
                                                                    $selectedCourses = !empty($uni['courses']) ? explode(',', $uni['courses']) : [];
                                                                    foreach ($dbStreams as $stream): 
                                                                    ?>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="courses[]" value="<?= $stream['id'] ?>" id="edit_stream_<?= $uni['id'] ?>_<?= $stream['id'] ?>" <?= in_array($stream['id'], $selectedCourses) ? 'checked' : '' ?>>
                                                                            <label class="form-check-label small" for="edit_stream_<?= $uni['id'] ?>_<?= $stream['id'] ?>">
                                                                                <?= htmlspecialchars($stream['name']) ?>
                                                                            </label>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            </div>
                                                            <div class="form-check mb-3">
                                                                <input class="form-check-input" type="checkbox" name="is_top" value="1" id="edit_uni_is_top_<?= $uni['id'] ?>" <?= (isset($uni['is_top']) && $uni['is_top'] == 1) ? 'checked' : '' ?>>
                                                                <label class="form-check-label fw-bold text-primary" for="edit_uni_is_top_<?= $uni['id'] ?>">
                                                                    Mark as Top Notch Institution
                                                                </label>
                                                                <div class="text-muted small">If checked, this institution will be showcased in the homepage premium slider and marked with a golden star in lists.</div>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>University Status</label>
                                                                <select name="status" class="form-select">
                                                                    <option value="Active" <?= (isset($uni['status']) && $uni['status'] === 'Active') ? 'selected' : '' ?>>Active</option>
                                                                    <option value="Inactive" <?= (isset($uni['status']) && $uni['status'] === 'Inactive') ? 'selected' : '' ?>>Inactive</option>
                                                                </select>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Display Order</label>
                                                                <input type="number" name="display_order" class="form-control" value="<?= (int)($uni['display_order'] ?? 0) ?>" min="0">
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Image Logo File (Upload)</label>
                                                                <input type="file" name="img_file" class="form-control" accept="image/*">
                                                                <?php if (!empty($uni['img'])): ?>
                                                                    <div class="mt-2 text-muted small">Current Path: <code><?= htmlspecialchars($uni['img']) ?></code></div>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Fee Structure Document (PDF/Doc/Image)</label>
                                                                <input type="file" name="fee_doc_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                                                                <input type="hidden" name="existing_fee_doc" value="<?= htmlspecialchars($uni['fee_document'] ?? '') ?>">
                                                                <?php if (!empty($uni['fee_document'])): ?>
                                                                    <div class="mt-2 text-muted small">
                                                                        Current Document: <a href="<?= htmlspecialchars($basePath . $uni['fee_document']) ?>" target="_blank" class="fw-semibold"><?= basename($uni['fee_document']) ?></a>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- DESTINATIONS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="destinations-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Destinations & Study Cities</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addDestModal"><i class="fa-solid fa-plus me-1"></i>Add Destination</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Thumbnail</th>
                                        <th>Destination Name</th>
                                        <th>Region Category</th>
                                        <th>Order</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbDestinations as $dest): 
                                        $destImg = !empty($dest['img']) ? ( (strpos($dest['img'], 'http') === 0) ? $dest['img'] : '../' . $dest['img'] ) : '';
                                    ?>
                                        <tr>
                                            <td><?= $dest['id'] ?></td>
                                            <td>
                                                <?php if ($destImg): ?>
                                                    <img src="<?= htmlspecialchars($destImg) ?>" class="rounded border p-1" style="width: 50px; height: 50px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="bg-light border rounded text-center small text-muted" style="width:50px; height:50px; line-height:50px;"><i class="fa-solid fa-image"></i></div>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($dest['name']) ?></strong></td>
                                            <td><span class="badge bg-light text-primary"><?= htmlspecialchars($dest['region']) ?></span></td>
                                            <td><code><?= $dest['display_order'] ?></code></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editDestModal<?= $dest['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete destination?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_destination">
                                                        <input type="hidden" name="id" value="<?= $dest['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT DESTINATION MODAL -->
                                        <div class="modal fade" id="editDestModal<?= $dest['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_destination">
                                                        <input type="hidden" name="id" value="<?= $dest['id'] ?>">
                                                        <input type="hidden" name="img" value="<?= htmlspecialchars($dest['img'] ?? '') ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Destination</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Destination Name</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($dest['name']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Region Category</label>
                                                                <input type="text" name="region" class="form-control" value="<?= htmlspecialchars($dest['region']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Display Order</label>
                                                                <input type="number" name="display_order" class="form-control" value="<?= $dest['display_order'] ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>About / Description</label>
                                                                <textarea name="about" class="form-control" rows="3" placeholder="Description of the city..."><?= htmlspecialchars($dest['about'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Gallery Images (comma separated URLs/paths)</label>
                                                                <textarea name="gallery" class="form-control" rows="2" placeholder="e.g. assets/images/delhi1.jpg, assets/images/delhi2.jpg"><?= htmlspecialchars($dest['gallery'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Google Map Link / Embed Code</label>
                                                                <textarea name="map_link" class="form-control" rows="2" placeholder="e.g. https://maps.app.goo.gl/... or <iframe src='...'></iframe>"><?= htmlspecialchars($dest['map_link'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Upload New Photo Image</label>
                                                                <input type="file" name="img_file" class="form-control" accept="image/*">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- COURSE STREAMS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="streams-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Course Streams</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addStreamModal"><i class="fa-solid fa-plus me-1"></i>Add Stream</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon Preview</th>
                                        <th>Icon Code</th>
                                        <th>Stream Name</th>
                                        <th>Order</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbStreams as $stream): ?>
                                        <tr>
                                            <td><?= $stream['id'] ?></td>
                                            <td><div class="bg-light text-center border rounded d-inline-block" style="width:40px; height:40px; line-height:40px;"><i class="fa-solid <?= htmlspecialchars($stream['icon']) ?> fs-5 text-secondary"></i></div></td>
                                            <td><code><?= htmlspecialchars($stream['icon']) ?></code></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($stream['name']) ?></strong></td>
                                            <td><code><?= $stream['display_order'] ?></code></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editStreamModal<?= $stream['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete stream?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_stream">
                                                        <input type="hidden" name="id" value="<?= $stream['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT STREAM MODAL -->
                                        <div class="modal fade" id="editStreamModal<?= $stream['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_stream">
                                                        <input type="hidden" name="id" value="<?= $stream['id'] ?>">
                                                        <input type="hidden" name="img" value="<?= htmlspecialchars($stream['img'] ?? '') ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Course Stream</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Stream Name</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($stream['name']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Font Awesome Icon (e.g. <code>fa-laptop-code</code>)</label>
                                                                <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($stream['icon']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Display Order</label>
                                                                <input type="number" name="display_order" class="form-control" value="<?= $stream['display_order'] ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Description</label>
                                                                <textarea name="desc" class="form-control" rows="3" placeholder="Prepare for clinical and hospital environments..."><?= htmlspecialchars($stream['desc'] ?? '') ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Banner Image File (Upload)</label>
                                                                <input type="file" name="img_file" class="form-control" accept="image/*">
                                                                <?php if (!empty($stream['img'])): ?>
                                                                    <div class="mt-2 text-muted small">Current Path: <code><?= htmlspecialchars($stream['img']) ?></code></div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- GUIDES PANEL (CRUD) -->
                    <div class="tab-pane fade" id="guides-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Guides & Blog Posts</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addGuideModal"><i class="fa-solid fa-plus me-1"></i>Write Article</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>Guide Title</th>
                                        <th>Slug URL</th>
                                        <th>Publish Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbGuides as $guide): 
                                        $guideImg = !empty($guide['image_path']) ? ( (strpos($guide['image_path'], 'http') === 0) ? $guide['image_path'] : '../' . $guide['image_path'] ) : 'assets/images/banner.png';
                                    ?>
                                        <tr>
                                            <td><img src="<?= htmlspecialchars($guideImg) ?>" class="rounded border" style="width: 60px; height: 40px; object-fit: cover;"></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($guide['title']) ?></strong></td>
                                            <td><code class="small text-secondary"><?= htmlspecialchars($guide['slug']) ?></code></td>
                                            <td><span class="small text-muted"><?= htmlspecialchars($guide['publish_date']) ?></span></td>
                                            <td><span class="badge bg-<?= $guide['status'] === 'published' ? 'success' : 'warning' ?>"><?= h($guide['status']) ?></span></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editGuideModal<?= $guide['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete guide article?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_guide">
                                                        <input type="hidden" name="id" value="<?= $guide['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT GUIDE MODAL -->
                                        <div class="modal fade" id="editGuideModal<?= $guide['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content border-0">
                                                    <form method="POST" enctype="multipart/form-data">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_guide">
                                                        <input type="hidden" name="id" value="<?= $guide['id'] ?>">
                                                        <input type="hidden" name="image_path" value="<?= htmlspecialchars($guide['image_path'] ?? '') ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Guide Article</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Article Title</label>
                                                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($guide['title']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Slug URL (leave empty to auto-generate)</label>
                                                                <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($guide['slug']) ?>">
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-6 form-group mb-3">
                                                                    <label>Publish Status</label>
                                                                    <select name="status" class="form-select">
                                                                        <option value="published" <?= $guide['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                                                        <option value="draft" <?= $guide['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-6 form-group mb-3">
                                                                    <label>Publish Date</label>
                                                                    <input type="date" name="publish_date" class="form-control" value="<?= htmlspecialchars($guide['publish_date']) ?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Short Summary / Subtitle</label>
                                                                <textarea name="summary" class="form-control" rows="2" required><?= htmlspecialchars($guide['summary']) ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Full Content (supports HTML tags)</label>
                                                                <textarea name="content" class="form-control" rows="8" required><?= htmlspecialchars($guide['content']) ?></textarea>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Cover Cover Image (Upload)</label>
                                                                <input type="file" name="img_file" class="form-control" accept="image/*">
                                                                <?php if (!empty($guide['image_path'])): ?>
                                                                    <div class="mt-2 text-muted small">Current Path: <code><?= htmlspecialchars($guide['image_path']) ?></code></div>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- PAGES CONTENT PANEL (CRUD) -->
                    <div class="tab-pane fade" id="pages-panel" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Static Pages Dynamic Content Editor</h5>
                        <p class="text-muted small">Manage titles, hero descriptions, and JSON content blocks directly used in static page layouts.</p>
                        
                        <div class="row g-4">
                            <?php foreach ($dbPages as $page): ?>
                                <div class="col-12 col-md-4">
                                    <div class="card p-3 shadow-sm border border-light h-100 d-flex flex-column justify-content-between">
                                        <div>
                                            <h6 class="fw-bold text-primary mb-1"><?= h($page['title']) ?></h6>
                                            <code class="small text-secondary d-block mb-3">Slug: <?= h($page['slug']) ?>.php</code>
                                            <p class="text-muted small mb-0">Hero: <em>"<?= h(substr($page['hero_subtitle'], 0, 80)) ?>..."</em></p>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-outline-primary btn-sm rounded-pill w-100" data-bs-toggle="modal" data-bs-target="#editPageModal<?= h($page['slug']) ?>"><i class="fa-solid fa-edit me-1"></i>Edit Page Layout</button>
                                        </div>
                                    </div>
                                </div>

                                <!-- EDIT STATIC PAGE MODAL -->
                                <div class="modal fade" id="editPageModal<?= h($page['slug']) ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0">
                                            <form method="POST">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                <input type="hidden" name="action" value="edit_page">
                                                <input type="hidden" name="slug" value="<?= h($page['slug']) ?>">
                                                
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title fw-bold text-primary">Edit Page Content: <?= h($page['title']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <div class="form-group mb-3">
                                                        <label>Page Navigation Title</label>
                                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($page['title']) ?>" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label>Hero Title Tag</label>
                                                        <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($page['hero_title']) ?>" required>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label>Hero Subtitle / Description Text</label>
                                                        <textarea name="hero_subtitle" class="form-control" rows="3" required><?= htmlspecialchars($page['hero_subtitle']) ?></textarea>
                                                    </div>
                                                    <div class="form-group mb-3">
                                                        <label>Raw JSON Content block (why cards, roadmap steps, etc.)</label>
                                                        <textarea name="content" class="form-control font-monospace text-secondary small" rows="10" required><?= htmlspecialchars($page['content']) ?></textarea>
                                                        <div class="mt-2 text-muted small"><i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>Ensure it remains a valid JSON structure. Be careful with brackets.</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- GENERAL SITE CONFIGS PANEL -->
                    <div class="tab-pane fade" id="settings-panel" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Site Visual Settings & Contact Details</h5>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="action" value="update_site_settings">
                            
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Portal Styling Colors (HSL Hex)</h6>
                                        <div class="row g-3">
                                            <div class="col-6 form-group">
                                                <label>Primary Theme Color</label>
                                                <input type="color" name="primary_color" class="form-control form-control-color w-100" value="<?= htmlspecialchars($siteSettings['primary_color'] ?? '#102e48') ?>">
                                            </div>
                                            <div class="col-6 form-group">
                                                <label>Primary Light</label>
                                                <input type="color" name="primary_light" class="form-control form-control-color w-100" value="<?= htmlspecialchars($siteSettings['primary_light'] ?? '#18446c') ?>">
                                            </div>
                                            <div class="col-6 form-group">
                                                <label>Secondary Accent Color</label>
                                                <input type="color" name="secondary_color" class="form-control form-control-color w-100" value="<?= htmlspecialchars($siteSettings['secondary_color'] ?? '#e56920') ?>">
                                            </div>
                                            <div class="col-6 form-group">
                                                <label>Secondary Hover</label>
                                                <input type="color" name="secondary_hover" class="form-control form-control-color w-100" value="<?= htmlspecialchars($siteSettings['secondary_hover'] ?? '#d25a17') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Hero Section Banner</h6>
                                        <div class="form-group mb-3">
                                            <label>Hero Title Heading</label>
                                            <input type="text" name="hero_title" class="form-control" value="<?= htmlspecialchars($siteSettings['hero_title'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Hero Subtitle Description</label>
                                            <textarea name="hero_subtitle" class="form-control" rows="2"><?= htmlspecialchars($siteSettings['hero_subtitle'] ?? '') ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Hero Banner Graphic Image (Upload)</label>
                                            <input type="file" name="hero_banner_image_file" class="form-control" accept="image/*">
                                            <?php if (!empty($siteSettings['hero_banner_image'])): ?>
                                                <div class="mt-2 small text-muted">Active path: <code><?= htmlspecialchars($siteSettings['hero_banner_image']) ?></code></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Official Contact & Branding Parameters</h6>
                                        <div class="form-group mb-3">
                                            <label>Site Window Title</label>
                                            <input type="text" name="site_title" class="form-control" value="<?= htmlspecialchars($siteSettings['site_title'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Site Brand Name</label>
                                            <input type="text" name="site_name" class="form-control" value="<?= htmlspecialchars($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Site Logo File (Upload)</label>
                                            <input type="file" name="site_logo_file" class="form-control" accept="image/*">
                                            <?php if (!empty($siteSettings['site_logo'])): ?>
                                                <div class="mt-2 small text-muted">Active path: <code><?= htmlspecialchars($siteSettings['site_logo']) ?></code></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Contact Phone (multiple split with '/')</label>
                                            <input type="text" name="contact_phone" class="form-control" value="<?= htmlspecialchars($siteSettings['contact_phone'] ?? '') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Contact Email</label>
                                            <input type="email" name="contact_email" class="form-control" value="<?= htmlspecialchars($siteSettings['contact_email'] ?? '') ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Contact Location Address</label>
                                            <input type="text" name="contact_location" class="form-control" value="<?= htmlspecialchars($siteSettings['contact_location'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light h-100">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Footer Widgets</h6>
                                        <div class="form-group mb-3">
                                            <label>About Us Footer Description</label>
                                            <textarea name="footer_about" class="form-control" rows="3"><?= htmlspecialchars($siteSettings['footer_about'] ?? '') ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Footer Copyright text</label>
                                            <input type="text" name="footer_copyright" class="form-control" value="<?= htmlspecialchars($siteSettings['footer_copyright'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light h-100">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="fa-solid fa-bell text-warning me-2"></i>Site Notification Alert & Auto-Popup Settings</h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="notification_enabled" value="1" id="notificationEnabledToggle" <?= (isset($siteSettings['notification_enabled']) && $siteSettings['notification_enabled'] === '1') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold text-success" for="notificationEnabledToggle">Enable Top Notification Announcement Banner (ON/OFF)</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="auto_popup_enabled" value="1" id="autoPopupEnabledToggle" <?= (isset($siteSettings['auto_popup_enabled']) && $siteSettings['auto_popup_enabled'] === '1') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold text-danger" for="autoPopupEnabledToggle">Enable Auto-Popup Enrollment Modal on Page Load (ON/OFF)</label>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Notification Header Title (e.g. <code>Admissions Open 2026!</code>)</label>
                                            <input type="text" name="notification_title" class="form-control" value="<?= htmlspecialchars($siteSettings['notification_title'] ?? 'Admissions Open 2026-2027!') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Notification Message Text</label>
                                            <textarea name="notification_message" class="form-control" rows="2"><?= htmlspecialchars($siteSettings['notification_message'] ?? 'Get up to 100% Scholarship for Studying in Top Universities in India. Apply today!') ?></textarea>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6 form-group">
                                                <label>Banner Background Color</label>
                                                <input type="color" name="notification_banner_color" class="form-control form-control-color w-100" value="<?= htmlspecialchars($siteSettings['notification_banner_color'] ?? '#DC281E') ?>">
                                            </div>
                                            <div class="col-6 form-group">
                                                <label>Button Target Link (Optional)</label>
                                                <input type="text" name="notification_link" class="form-control" placeholder="e.g. courses.php" value="<?= htmlspecialchars($siteSettings['notification_link'] ?? '') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light h-100">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Homepage QR Code Payment</h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="qr_code_enabled" value="1" id="qrCodeEnabledToggle" <?= (isset($siteSettings['qr_code_enabled']) && $siteSettings['qr_code_enabled'] === '1') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold text-primary" for="qrCodeEnabledToggle">Enable Homepage QR Code Display</label>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>QR Code Title</label>
                                            <input type="text" name="qr_code_title" class="form-control" value="<?= htmlspecialchars($siteSettings['qr_code_title'] ?? 'Scan to Pay') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>QR Code Description / Instructions</label>
                                            <textarea name="qr_code_description" class="form-control" rows="2"><?= htmlspecialchars($siteSettings['qr_code_description'] ?? '') ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>QR Code Image File (Upload)</label>
                                            <input type="file" name="qr_code_image_file" class="form-control" accept="image/*">
                                            <?php if (!empty($siteSettings['qr_code_image'])): ?>
                                                <div class="mt-2 small text-muted">Active path: <code><?= htmlspecialchars($siteSettings['qr_code_image']) ?></code></div>
                                                <img src="<?= htmlspecialchars('../' . $siteSettings['qr_code_image']) ?>" class="mt-2 img-fluid rounded border p-1 bg-light shadow-sm" style="max-height: 100px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-light h-100">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3"><i class="fa-solid fa-award text-warning me-2"></i>Accreditation Section Setup</h6>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="accreditation_enabled" value="1" id="accredSettingsEnabledToggle" <?= (!isset($siteSettings['accreditation_enabled']) || $siteSettings['accreditation_enabled'] === '1') ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-bold text-primary" for="accredSettingsEnabledToggle">Enable Homepage Accreditation Section</label>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label>Accreditation Heading Title</label>
                                            <input type="text" name="accreditation_title" class="form-control" value="<?= htmlspecialchars($siteSettings['accreditation_title'] ?? 'Accreditation') ?>">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Accreditation Subtitle / Description</label>
                                            <textarea name="accreditation_subtitle" class="form-control" rows="2"><?= htmlspecialchars($siteSettings['accreditation_subtitle'] ?? 'Trusted excellence backed by top accreditations') ?></textarea>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fa-solid fa-circle-info text-info me-1"></i>To manage partner logos, upload new accreditation credentials, and re-order cards, use the <a href="#" onclick="switchTab('accreditations-tab'); return false;" class="fw-bold text-primary text-decoration-underline">Accreditations Manager</a> tab.
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-12">
                                    <div class="card p-3 border border-light">
                                        <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">
                                            <i class="fa-solid fa-chart-line text-success me-2"></i>Google Search Console & Google Analytics 4
                                        </h6>
                                        <div class="row g-3">
                                            <div class="col-12 col-md-6 form-group">
                                                <label class="form-label fw-semibold">Google Search Console Verification Code / Token</label>
                                                <input type="text" name="gsc_verification_code" class="form-control font-monospace" placeholder="e.g. google-site-verification token or code" value="<?= htmlspecialchars($siteSettings['gsc_verification_code'] ?? '') ?>">
                                                <div class="form-text text-muted">Generates <code>&lt;meta name="google-site-verification" content="..."&gt;</code> in all page headers.</div>
                                            </div>
                                            <div class="col-12 col-md-6 form-group">
                                                <label class="form-label fw-semibold">Google Analytics 4 Measurement ID</label>
                                                <input type="text" name="ga_measurement_id" class="form-control font-monospace" placeholder="e.g. G-XXXXXXXXXX" value="<?= htmlspecialchars($siteSettings['ga_measurement_id'] ?? '') ?>">
                                                <div class="form-text text-muted">Automatically loads the async Google tag (gtag.js) on all pages.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-secondary rounded-pill px-5 py-3 fw-bold">Save Settings Configurations</button>
                            </div>
                        </form>
                    </div>

                    <!-- SOCIAL MEDIA LINKS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="social-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Social Media Widget Links</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSocialModal"><i class="fa-solid fa-plus me-1"></i>Add Social Link</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon Preview</th>
                                        <th>Platform</th>
                                        <th>Target URL</th>
                                        <th>Order</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbSocials as $soc): ?>
                                        <tr>
                                            <td><?= $soc['id'] ?></td>
                                            <td><div class="bg-light text-center border rounded d-inline-block" style="width:35px; height:35px; line-height:35px;"><i class="<?= htmlspecialchars($soc['icon']) ?> fs-5 text-secondary"></i></div></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($soc['platform_name']) ?></strong></td>
                                            <td><a href="<?= htmlspecialchars($soc['url']) ?>" target="_blank" class="small text-secondary"><?= htmlspecialchars($soc['url']) ?></a></td>
                                            <td><code><?= $soc['display_order'] ?></code></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editSocialModal<?= $soc['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete social link?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_social">
                                                        <input type="hidden" name="id" value="<?= $soc['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT SOCIAL MODAL -->
                                        <div class="modal fade" id="editSocialModal<?= $soc['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_social">
                                                        <input type="hidden" name="id" value="<?= $soc['id'] ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Social Link</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Platform Name</label>
                                                                <input type="text" name="platform_name" class="form-control" value="<?= htmlspecialchars($soc['platform_name']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Target URL</label>
                                                                <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($soc['url']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Font Awesome Icon (e.g. <code>fa-brands fa-facebook</code>)</label>
                                                                <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($soc['icon']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Display Order</label>
                                                                <input type="number" name="display_order" class="form-control" value="<?= $soc['display_order'] ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- NAVIGATION MENUS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="navigation-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Navigation Header Menus</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addMenuModal"><i class="fa-solid fa-plus me-1"></i>Add Menu Item</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Label</th>
                                        <th>Link Href</th>
                                        <th>Order</th>
                                        <th>Visible</th>
                                        <th>Parent Category</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbMenus as $menu): 
                                        $parentName = 'None';
                                        if ($menu['parent_id']) {
                                            foreach ($dbMenus as $mTemp) {
                                                if ($mTemp['id'] == $menu['parent_id']) {
                                                    $parentName = $mTemp['label'];
                                                    break;
                                                }
                                            }
                                        }
                                    ?>
                                        <tr>
                                            <td>
                                                <strong class="<?= $menu['parent_id'] ? 'text-secondary ps-3' : 'text-dark' ?>">
                                                    <?= $menu['parent_id'] ? '↳ ' : '' ?><?= htmlspecialchars($menu['label']) ?>
                                                </strong>
                                            </td>
                                            <td><code class="small text-secondary"><?= htmlspecialchars($menu['url']) ?></code></td>
                                            <td><code><?= $menu['display_order'] ?></code></td>
                                            <td><span class="badge bg-<?= $menu['is_visible'] ? 'success' : 'danger' ?>"><?= $menu['is_visible'] ? 'Yes' : 'No' ?></span></td>
                                            <td><span class="badge bg-light text-secondary"><?= htmlspecialchars($parentName) ?></span></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editMenuModal<?= $menu['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete menu item?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_menu">
                                                        <input type="hidden" name="id" value="<?= $menu['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT MENU MODAL -->
                                        <div class="modal fade" id="editMenuModal<?= $menu['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_menu">
                                                        <input type="hidden" name="id" value="<?= $menu['id'] ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Menu Item</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Menu Label</label>
                                                                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($menu['label']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>URL / File path (e.g. <code>index.php</code>)</label>
                                                                <input type="text" name="url" class="form-control" value="<?= htmlspecialchars($menu['url']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Parent Dropdown Item</label>
                                                                <select name="parent_id" class="form-select">
                                                                    <option value="">None (Top Level)</option>
                                                                    <?php foreach ($dbMenus as $p): ?>
                                                                        <?php if ($p['parent_id'] === null && $p['id'] != $menu['id']): ?>
                                                                            <option value="<?= $p['id'] ?>" <?= $menu['parent_id'] == $p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['label']) ?></option>
                                                                        <?php endif; ?>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-6 form-group">
                                                                    <label>Display Order</label>
                                                                    <input type="number" name="display_order" class="form-control" value="<?= $menu['display_order'] ?>" required>
                                                                </div>
                                                                <div class="col-6 d-flex align-items-end ps-3">
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="is_visible<?= $menu['id'] ?>" <?= $menu['is_visible'] ? 'checked' : '' ?>>
                                                                        <label class="form-check-label fw-bold" for="is_visible<?= $menu['id'] ?>">Make Visible</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- COUNTRIES DROPDOWNS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="countries-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Form Dropdown Interest Countries</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCountryModal"><i class="fa-solid fa-plus me-1"></i>Add Country</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Country Option Name</th>
                                        <th>Display Order</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbCountries as $cnt): ?>
                                        <tr>
                                            <td><?= $cnt['id'] ?></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($cnt['name']) ?></strong></td>
                                            <td><code><?= $cnt['display_order'] ?></code></td>
                                            <td><span class="badge bg-<?= $cnt['is_active'] ? 'success' : 'danger' ?>"><?= $cnt['is_active'] ? 'Active' : 'Disabled' ?></span></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editCountryModal<?= $cnt['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete country option?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_country">
                                                        <input type="hidden" name="id" value="<?= $cnt['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT COUNTRY MODAL -->
                                        <div class="modal fade" id="editCountryModal<?= $cnt['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_country">
                                                        <input type="hidden" name="id" value="<?= $cnt['id'] ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Country Option</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Country Option Name</label>
                                                                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cnt['name']) ?>" required>
                                                            </div>
                                                            <div class="row g-3">
                                                                <div class="col-6 form-group">
                                                                    <label>Display Order</label>
                                                                    <input type="number" name="display_order" class="form-control" value="<?= $cnt['display_order'] ?>" required>
                                                                </div>
                                                                <div class="col-6 d-flex align-items-end ps-3">
                                                                    <div class="form-check mb-2">
                                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_country<?= $cnt['id'] ?>" <?= $cnt['is_active'] ? 'checked' : '' ?>>
                                                                        <label class="form-check-label fw-bold" for="is_active_country<?= $cnt['id'] ?>">Make Active</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FUN FACTS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="facts-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">Dynamic Fun Facts & Statistics</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addFactModal"><i class="fa-solid fa-plus me-1"></i>Add Fact Stat</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Icon Preview</th>
                                        <th>Stat Number</th>
                                        <th>Label Description</th>
                                        <th>Order</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbFacts as $fact): ?>
                                        <tr>
                                            <td><?= $fact['id'] ?></td>
                                            <td><div class="bg-light text-center border rounded d-inline-block" style="width:35px; height:35px; line-height:35px;"><i class="<?= htmlspecialchars(formatFaIcon($fact['icon'] ?? '', $fact['label'] ?? '')) ?> fs-5 text-secondary"></i></div></td>
                                            <td><strong class="text-primary fs-5"><?= htmlspecialchars($fact['number']) ?></strong></td>
                                            <td><span class="text-dark fw-bold"><?= htmlspecialchars($fact['label']) ?></span></td>
                                            <td><code><?= $fact['display_order'] ?></code></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editFactModal<?= $fact['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                    <form method="POST" class="d-inline" onsubmit="return confirm('Delete fact stat?');">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="delete_fact">
                                                        <input type="hidden" name="id" value="<?= $fact['id'] ?>">
                                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT FUN FACT MODAL -->
                                        <div class="modal fade" id="editFactModal<?= $fact['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_fact">
                                                        <input type="hidden" name="id" value="<?= $fact['id'] ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Fun Fact</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Stat Number (e.g. <code>123+</code> or <code>85%</code>)</label>
                                                                <input type="text" name="number" class="form-control" value="<?= htmlspecialchars($fact['number']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Label Description</label>
                                                                <input type="text" name="label" class="form-control" value="<?= htmlspecialchars($fact['label']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Font Awesome Icon (e.g. <code>fa-award</code>)</label>
                                                                <input type="text" name="icon" class="form-control" value="<?= htmlspecialchars($fact['icon']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>Display Order</label>
                                                                <input type="number" name="display_order" class="form-control" value="<?= $fact['display_order'] ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Save changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ADMIN USER MANAGEMENT PANEL (CRUD) -->
                    <div class="tab-pane fade" id="admins-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-primary mb-0">System Administrators</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addAdminModal"><i class="fa-solid fa-plus me-1"></i>Add Admin</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Admin Email</th>
                                        <th>Created Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($dbAdmins as $admin): ?>
                                        <tr>
                                            <td><?= $admin['id'] ?></td>
                                            <td><strong class="text-dark"><?= htmlspecialchars($admin['email']) ?></strong></td>
                                            <td><span class="small text-muted"><?= date('Y-m-d H:i', strtotime($admin['created_at'])) ?></span></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editAdminModal<?= $admin['id'] ?>"><i class="fa-solid fa-key"></i> Pass / Edit</button>
                                                    <?php if ($admin['id'] !== (int)$_SESSION['admin_id']): ?>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete admin account?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_admin">
                                                            <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-light text-secondary rounded-pill">Active Self</span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- EDIT ADMIN MODAL -->
                                        <div class="modal fade" id="editAdminModal<?= $admin['id'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0">
                                                    <form method="POST">
                                                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                        <input type="hidden" name="action" value="edit_admin">
                                                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                                                        
                                                        <div class="modal-header bg-light">
                                                            <h5 class="modal-title fw-bold text-primary">Edit Admin credentials</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="form-group mb-3">
                                                                <label>Admin Email Address</label>
                                                                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                                            </div>
                                                            <div class="form-group mb-3">
                                                                <label>New Password (leave empty to keep current)</label>
                                                                <input type="password" name="password" class="form-control" placeholder="Type new password">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Update Account</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- FILE MANAGER PANEL -->
                    <div class="tab-pane fade" id="filemanager-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-primary mb-0">Portal Image Upload Manager</h5>
                            <form method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="action" value="upload_file">
                                <input type="file" name="file" class="form-control form-control-sm" accept="image/*" required>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3"><i class="fa-solid fa-cloud-arrow-up me-1"></i>Upload</button>
                            </form>
                        </div>
                        
                        <div class="row g-3">
                            <?php if (empty($uploadedFiles)): ?>
                                <div class="col-12 text-center text-muted py-5"><i class="fa-regular fa-image fs-1 mb-2 d-block"></i>No files uploaded in <code>uploads/</code>.</div>
                            <?php else: ?>
                                <?php foreach ($uploadedFiles as $f): 
                                    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
                                    $sizeMb = round($f['size'] / (1024 * 1024), 2);
                                ?>
                                    <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                                        <div class="card h-100 border border-light shadow-sm text-center p-2 rounded-3" style="position:relative;">
                                            <img src="../<?= htmlspecialchars($f['path']) ?>" style="height:100px; object-fit:contain;" class="rounded border mb-2 w-100 bg-white">
                                            <div style="font-size:11px; word-break:break-all;" class="fw-bold text-truncate" title="<?= htmlspecialchars($f['name']) ?>"><?= htmlspecialchars($f['name']) ?></div>
                                            <div class="text-muted small" style="font-size:9.5px;"><?= $sizeMb ?> MB &bull; <?= date('M j', $f['date']) ?></div>
                                            <div class="d-flex justify-content-center gap-1 mt-2">
                                                <button class="btn btn-light btn-sm text-primary rounded-pill btn-copy-path" data-path="<?= htmlspecialchars($f['path']) ?>" style="font-size:10px;"><i class="fa-solid fa-copy"></i> Copy Path</button>
                                                <form method="POST" onsubmit="return confirm('Delete uploaded file?');" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="delete_file">
                                                    <input type="hidden" name="filename" value="<?= htmlspecialchars($f['name']) ?>">
                                                    <button type="submit" class="btn btn-light btn-sm text-danger rounded-pill" style="font-size:10px;" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- ACTIVITY LOGS PANEL -->
                    <div class="tab-pane fade" id="logs-panel" role="tabpanel">
                        <h5 class="fw-bold text-primary mb-3">Console System Activity History Log</h5>
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Admin Email</th>
                                        <th>Operation Action</th>
                                        <th>Target Description</th>
                                        <th>IP Address</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dbLogs)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4">No activities logged yet.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($dbLogs as $log): ?>
                                            <tr>
                                                <td><?= $log['id'] ?></td>
                                                <td><strong class="text-dark"><?= htmlspecialchars($log['admin_email']) ?></strong></td>
                                                <td><span class="badge bg-light text-primary"><?= htmlspecialchars($log['action']) ?></span></td>
                                                <td><span class="text-secondary small"><?= htmlspecialchars($log['details'] ?? 'N/A') ?></span></td>
                                                <td><code class="small text-secondary"><?= htmlspecialchars($log['ip_address']) ?></code></td>
                                                <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- AVAILABLE PROGRAMS PANEL -->
                    <div class="tab-pane fade" id="programs-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-graduation-cap me-2 text-warning"></i>Available Programs</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProgramModal">
                                <i class="fa-solid fa-plus me-1"></i>Add Program
                            </button>
                        </div>

                        <!-- Search Bar Container -->
                        <div class="p-3 bg-light rounded-3 mb-4" style="background-color: #f0f4f8 !important;">
                            <div class="input-group search-input-group border-0" style="max-width: 400px;">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="adminProgramSearch" class="form-control border-0" placeholder="Search for Programs..." style="border-radius: 30px; padding: 10px 15px 10px 45px;">
                            </div>
                        </div>

                        <!-- Programs Cards List -->
                        <div class="row g-3" id="adminProgramsContainer">
                            <?php if (empty($dbPrograms)): ?>
                                <div class="col-12 text-center text-muted py-4">No programs available yet. Add one to get started!</div>
                            <?php else: ?>
                                <?php foreach ($dbPrograms as $prog): 
                                    $logoImg = !empty($prog['logo_path']) ? ( (strpos($prog['logo_path'], 'http') === 0) ? $prog['logo_path'] : '../' . $prog['logo_path'] ) : '';
                                ?>
                                    <div class="col-12 program-card-row-item" data-name="<?= h(strtolower($prog['name'])) ?>" data-college="<?= h(strtolower($prog['college_name'])) ?>" data-stream="<?= h(strtolower($prog['stream'])) ?>">
                                        <div class="card p-3 border-0 shadow-sm rounded-3 mb-3 position-relative" style="background-color: #ffffff; border: 1px solid #eef2f6 !important;">
                                            
                                            <!-- Admin Edit/Delete actions in top right -->
                                            <div class="position-absolute" style="top: 15px; right: 15px; z-index: 10;">
                                                <button class="btn btn-sm btn-outline-secondary rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editProgramModal<?= $prog['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="delete_program">
                                                    <input type="hidden" name="id" value="<?= $prog['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fa-solid fa-trash"></i></button>
                                                </form>
                                            </div>

                                            <div class="row align-items-center">
                                                <!-- Left: Logo -->
                                                <div class="col-12 col-md-2 text-center text-md-start mb-3 mb-md-0">
                                                    <?php if ($logoImg): ?>
                                                        <img src="<?= htmlspecialchars($logoImg) ?>" class="rounded border p-2" style="max-height: 80px; max-width: 100%; object-fit: contain;">
                                                    <?php else: ?>
                                                        <div class="bg-light rounded border text-muted d-flex flex-column align-items-center justify-content-center" style="height: 90px; width: 100%; min-width: 120px;">
                                                            <i class="fa-solid fa-graduation-cap fs-2 mb-1 text-secondary"></i>
                                                            <span class="extra-small text-muted" style="font-size: 10px;">Default Course Logo</span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Center: Details -->
                                                <div class="col-12 col-md-8">
                                                    <h5 class="fw-bold text-primary mb-2 pe-5" style="color: #0d6efd !important;"><?= htmlspecialchars($prog['name']) ?></h5>
                                                    
                                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                                        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1"><i class="fa-solid fa-map-marker-alt text-warning"></i> <?= htmlspecialchars($prog['location']) ?></span>
                                                        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1"><i class="fa-solid fa-wallet text-info"></i> <?= htmlspecialchars($prog['fee']) ?></span>
                                                        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1"><i class="fa-solid fa-clock text-primary"></i> <?= htmlspecialchars($prog['duration']) ?></span>
                                                        <?php if (!empty($prog['stream'])): ?>
                                                            <span class="badge bg-light text-secondary border d-flex align-items-center gap-1"><i class="fa-solid fa-book text-success"></i> <?= htmlspecialchars($prog['stream']) ?></span>
                                                        <?php endif; ?>
                                                        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1"><i class="fa-solid fa-user-graduate text-secondary"></i> <?= htmlspecialchars($prog['level']) ?></span>
                                                        <span class="badge bg-light text-secondary border d-flex align-items-center gap-1"><i class="fa-solid fa-calendar-check text-danger"></i> <?= htmlspecialchars($prog['intake']) ?></span>
                                                    </div>
                                                    
                                                    <div class="text-muted small"><i class="fa-solid fa-university me-1 text-secondary"></i> College: <strong><?= htmlspecialchars($prog['college_name']) ?></strong></div>
                                                </div>

                                                <!-- Right: Front buttons -->
                                                <div class="col-12 col-md-2 text-center text-md-end mt-3 mt-md-0 border-start ps-3">
                                                    <button class="btn btn-danger rounded-pill px-4 w-100 mb-2 btn-sm fw-bold" disabled>Apply Now</button>
                                                    <a href="<?= !empty($prog['details_link']) ? htmlspecialchars($prog['details_link']) : '#' ?>" class="text-danger small fw-bold text-decoration-underline d-block text-center w-100" style="pointer-events: none;">Program Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- EDIT PROGRAM MODAL -->
                                    <div class="modal fade" id="editProgramModal<?= $prog['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0">
                                                <form method="POST" enctype="multipart/form-data">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="action" value="edit_program">
                                                    <input type="hidden" name="id" value="<?= $prog['id'] ?>">
                                                    <input type="hidden" name="logo_path" value="<?= htmlspecialchars($prog['logo_path'] ?? '') ?>">
                                                    
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold text-primary">Edit Program #<?= $prog['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <div class="form-group mb-3">
                                                            <label>Program / Course Name</label>
                                                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($prog['name']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>University / College Name</label>
                                                            <input type="text" name="college_name" class="form-control" value="<?= htmlspecialchars($prog['college_name']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Location</label>
                                                            <input type="text" name="location" class="form-control" value="<?= htmlspecialchars($prog['location']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Fee Structure</label>
                                                            <input type="text" name="fee" class="form-control" value="<?= htmlspecialchars($prog['fee']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Duration</label>
                                                            <input type="text" name="duration" class="form-control" value="<?= htmlspecialchars($prog['duration']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Academic Stream</label>
                                                            <input type="text" name="stream" class="form-control" value="<?= htmlspecialchars($prog['stream']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Level</label>
                                                            <select name="level" class="form-select" required>
                                                                <option value="Undergraduate" <?= $prog['level'] === 'Undergraduate' ? 'selected' : '' ?>>Undergraduate</option>
                                                                <option value="Postgraduate" <?= $prog['level'] === 'Postgraduate' ? 'selected' : '' ?>>Postgraduate</option>
                                                                <option value="Diploma" <?= $prog['level'] === 'Diploma' ? 'selected' : '' ?>>Diploma</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Intake Season</label>
                                                            <input type="text" name="intake" class="form-control" value="<?= htmlspecialchars($prog['intake']) ?>" required>
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Program Details Link</label>
                                                            <input type="text" name="details_link" class="form-control" value="<?= htmlspecialchars($prog['details_link'] ?? '') ?>">
                                                        </div>
                                                        <div class="form-group mb-3">
                                                            <label>Change Logo File</label>
                                                            <input type="file" name="logo_file" class="form-control" accept="image/*">
                                                            <?php if (!empty($prog['logo_path'])): ?>
                                                                <div class="mt-2">
                                                                    <img src="../<?= htmlspecialchars($prog['logo_path']) ?>" class="rounded border p-1" style="max-height: 40px;">
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-0">
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- BANK ACCOUNTS PANEL -->
                    <div class="tab-pane fade" id="bank-accounts-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-bank text-warning me-2"></i>Bank Accounts</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addBankModal">
                                <i class="fa-solid fa-plus me-1"></i>Add Bank Account
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0 bg-white border rounded">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Bank Name</th>
                                        <th>Account Name</th>
                                        <th>Account Number</th>
                                        <th>Branch</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dbBankAccounts)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">No bank accounts configured yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dbBankAccounts as $bank): ?>
                                            <tr>
                                                <td><?= $bank['id'] ?></td>
                                                <td><strong class="text-dark"><?= htmlspecialchars($bank['bank_name']) ?></strong></td>
                                                <td><?= htmlspecialchars($bank['account_name']) ?></td>
                                                <td><code class="font-monospace text-primary fw-bold" style="font-size: 14px;"><?= htmlspecialchars($bank['account_number']) ?></code></td>
                                                <td><?= htmlspecialchars($bank['branch'] ?: 'N/A') ?></td>
                                                <td><span class="badge bg-light text-dark border"><?= (int)$bank['display_order'] ?></span></td>
                                                <td>
                                                    <?php if ($bank['is_active'] == 1): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editBankModal<?= $bank['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this bank account?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_bank_account">
                                                            <input type="hidden" name="id" value="<?= $bank['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- EDIT BANK MODAL -->
                                            <div class="modal fade" id="editBankModal<?= $bank['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <form method="POST">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_bank_account">
                                                            <input type="hidden" name="id" value="<?= $bank['id'] ?>">
                                                            
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-building-columns me-2"></i>Edit Bank Account #<?= $bank['id'] ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-3">
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                                                        <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($bank['bank_name']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                                                                        <input type="text" name="account_name" class="form-control" value="<?= htmlspecialchars($bank['account_name']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="fw-semibold">Account Number <span class="text-danger">*</span></label>
                                                                        <input type="text" name="account_number" class="form-control font-monospace" value="<?= htmlspecialchars($bank['account_number']) ?>" required>
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="fw-semibold">Branch Name</label>
                                                                        <input type="text" name="branch" class="form-control" value="<?= htmlspecialchars($bank['branch'] ?? '') ?>" placeholder="e.g. Putalisadak Branch">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label class="fw-semibold">Account Type</label>
                                                                        <input type="text" name="account_type" class="form-control" value="<?= htmlspecialchars($bank['account_type'] ?? '') ?>" placeholder="e.g. Current, Savings">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label class="fw-semibold">SWIFT Code</label>
                                                                        <input type="text" name="swift_code" class="form-control font-monospace" value="<?= htmlspecialchars($bank['swift_code'] ?? '') ?>" placeholder="e.g. SUBNEPKA">
                                                                    </div>
                                                                    <div class="col-12 col-md-4 form-group">
                                                                        <label class="fw-semibold">IFSC Code (if applicable)</label>
                                                                        <input type="text" name="ifsc_code" class="form-control font-monospace" value="<?= htmlspecialchars($bank['ifsc_code'] ?? '') ?>" placeholder="e.g. SBIN0001234">
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label class="fw-semibold">Payment Instructions</label>
                                                                        <textarea name="instructions" class="form-control" rows="2" placeholder="e.g. Specify student name in transfer remarks."><?= htmlspecialchars($bank['instructions'] ?? '') ?></textarea>
                                                                    </div>
                                                                    <div class="col-12 form-group">
                                                                        <label class="fw-semibold">Internal Remarks / Notes</label>
                                                                        <input type="text" name="remarks" class="form-control" value="<?= htmlspecialchars($bank['remarks'] ?? '') ?>" placeholder="Optional notes about this account">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 form-group">
                                                                        <label class="fw-semibold">Display Order</label>
                                                                        <input type="number" name="display_order" class="form-control" value="<?= (int)$bank['display_order'] ?>" min="0">
                                                                    </div>
                                                                    <div class="col-12 col-md-6 d-flex align-items-center gap-4 pt-3">
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="is_default" value="1" id="edit_bank_default_<?= $bank['id'] ?>" <?= (!empty($bank['is_default']) && $bank['is_default'] == 1) ? 'checked' : '' ?>>
                                                                            <label class="form-check-label fw-bold text-primary" for="edit_bank_default_<?= $bank['id'] ?>">Set Primary Account</label>
                                                                        </div>
                                                                        <div class="form-check">
                                                                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_bank_active_<?= $bank['id'] ?>" <?= $bank['is_active'] == 1 ? 'checked' : '' ?>>
                                                                            <label class="form-check-label fw-bold text-success" for="edit_bank_active_<?= $bank['id'] ?>">Active (Visible)</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light border-0">
                                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- UNIVERSITY PDFS PANEL -->
                    <div class="tab-pane fade" id="university-pdfs-panel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="fw-bold text-primary mb-0"><i class="fa-solid fa-file-pdf me-2 text-warning"></i>University Documents & PDFs</h5>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addPdfModal">
                                <i class="fa-solid fa-plus me-1"></i>Upload Document
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0 bg-white border rounded">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Document Title</th>
                                        <th>Associated Institution</th>
                                        <th>File Path</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dbInstitutionPdfs)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No PDF documents uploaded yet.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dbInstitutionPdfs as $pdf): ?>
                                            <tr>
                                                <td><?= $pdf['id'] ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <i class="fa-solid fa-file-pdf text-danger fs-5"></i>
                                                        <strong class="text-dark"><?= htmlspecialchars($pdf['title']) ?></strong>
                                                    </div>
                                                    <?php if (!empty($pdf['description'])): ?>
                                                        <div class="text-muted small-text extra-small"><?= htmlspecialchars($pdf['description']) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge bg-light text-primary"><?= htmlspecialchars($pdf['institution_name']) ?></span></td>
                                                <td>
                                                    <a href="<?= htmlspecialchars($basePath . $pdf['file_path']) ?>" target="_blank" class="btn btn-sm btn-link p-0 fw-semibold text-decoration-none">
                                                        <i class="fa-solid fa-external-link-alt me-1"></i>View PDF File
                                                    </a>
                                                </td>
                                                <td><span class="badge bg-light text-dark border"><?= (int)$pdf['display_order'] ?></span></td>
                                                <td>
                                                    <?php if ($pdf['is_active'] == 1): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editPdfModal<?= $pdf['id'] ?>"><i class="fa-solid fa-pen-to-square"></i></button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete this PDF document?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_institution_pdf">
                                                            <input type="hidden" name="id" value="<?= $pdf['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- EDIT PDF MODAL -->
                                            <div class="modal fade" id="editPdfModal<?= $pdf['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0">
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_institution_pdf">
                                                            <input type="hidden" name="id" value="<?= $pdf['id'] ?>">
                                                            <input type="hidden" name="existing_file_path" value="<?= htmlspecialchars($pdf['file_path']) ?>">
                                                            
                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-primary">Edit PDF Document</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="form-group mb-3">
                                                                    <label>Document Title</label>
                                                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($pdf['title']) ?>" required>
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label>Description / Type</label>
                                                                    <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($pdf['description'] ?? '') ?>" placeholder="e.g. Brochure, Admission Guidelines, Fee Table">
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label>Associate with University</label>
                                                                    <select name="institution_id" class="form-select" required>
                                                                        <option value="">-- Select Institution --</option>
                                                                        <?php foreach ($dbInstitutions as $uni): ?>
                                                                            <option value="<?= $uni['id'] ?>" <?= $pdf['institution_id'] == $uni['id'] ? 'selected' : '' ?>><?= htmlspecialchars($uni['name']) ?></option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label>Change PDF File (Upload)</label>
                                                                    <input type="file" name="pdf_file" class="form-control" accept=".pdf">
                                                                    <div class="mt-2 text-muted small">Current File: <code><?= htmlspecialchars(basename($pdf['file_path'])) ?></code></div>
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label>Display Order</label>
                                                                    <input type="number" name="display_order" class="form-control" value="<?= (int)$pdf['display_order'] ?>" min="0">
                                                                </div>
                                                                <div class="form-check mb-3">
                                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_pdf_active_<?= $pdf['id'] ?>" <?= $pdf['is_active'] == 1 ? 'checked' : '' ?>>
                                                                    <label class="form-check-label" for="edit_pdf_active_<?= $pdf['id'] ?>">Active (Visible on College Details)</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light border-0">
                                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-5">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ADD PROGRAM MODAL -->
                    <div class="modal fade" id="addProgramModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                    <input type="hidden" name="action" value="add_program">
                                    <input type="hidden" name="logo_path" value="">
                                    
                                    <div class="modal-header bg-light">
                                        <h5 class="modal-title fw-bold text-primary">Add Available Program</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="form-group mb-3">
                                            <label>Program / Course Name</label>
                                            <input type="text" name="name" class="form-control" placeholder="e.g. B.Design in association with Industry Partners" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>University / College Name</label>
                                            <input type="text" name="college_name" class="form-control" placeholder="e.g. Manav Rachana University" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Location</label>
                                            <input type="text" name="location" class="form-control" placeholder="e.g. Delhi NCR" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Fee Structure</label>
                                            <input type="text" name="fee" class="form-control" placeholder="e.g. INR 295,400 /Year" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Duration</label>
                                            <input type="text" name="duration" class="form-control" placeholder="e.g. 4 years" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Academic Stream</label>
                                            <input type="text" name="stream" class="form-control" placeholder="e.g. Design" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Level</label>
                                            <select name="level" class="form-select" required>
                                                <option value="Undergraduate">Undergraduate</option>
                                                <option value="Postgraduate">Postgraduate</option>
                                                <option value="Diploma">Diploma</option>
                                            </select>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Intake Season</label>
                                            <input type="text" name="intake" class="form-control" value="Intake: July" required>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Program Details Link</label>
                                            <input type="text" name="details_link" class="form-control" placeholder="e.g. https://details-link.com">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>Logo Image File</label>
                                            <input type="file" name="logo_file" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-0">
                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary rounded-pill px-5">Add Program</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- jQuery Live Search Script -->
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const searchInput = document.getElementById('adminProgramSearch');
                        if (searchInput) {
                            searchInput.addEventListener('input', function() {
                                const query = this.value.toLowerCase().trim();
                                const items = document.querySelectorAll('.program-card-row-item');
                                
                                items.forEach(item => {
                                    const name = item.getAttribute('data-name');
                                    const college = item.getAttribute('data-college');
                                    const stream = item.getAttribute('data-stream');
                                    
                                    if (name.includes(query) || college.includes(query) || stream.includes(query)) {
                                        item.style.display = 'block';
                                    } else {
                                        item.style.display = 'none';
                                    }
                                });
                            });
                        }
                    });
                    </script>

                    <!-- ACCREDITATIONS PANEL (CRUD) -->
                    <div class="tab-pane fade" id="accreditations-panel" role="tabpanel">
                        <!-- Top Card: Section Title & Subtitle Configuration -->
                        <div class="card p-3 border border-light shadow-sm rounded-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <h5 class="fw-bold text-primary mb-1"><i class="fa-solid fa-sliders text-warning me-2"></i>Accreditation Section Settings</h5>
                                    <p class="text-muted small mb-0">Control the visibility, heading title, and description text for the Accreditation section on the homepage.</p>
                                </div>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <input type="hidden" name="action" value="update_accreditation_settings">
                                
                                <div class="row g-3 align-items-center">
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small fw-bold text-dark">Section Title</label>
                                        <input type="text" name="accreditation_title" class="form-control form-control-sm" value="<?= htmlspecialchars($siteSettings['accreditation_title'] ?? 'Accreditation') ?>" required placeholder="e.g. Accreditation">
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <label class="form-label small fw-bold text-dark">Section Subtitle / Tagline</label>
                                        <input type="text" name="accreditation_subtitle" class="form-control form-control-sm" value="<?= htmlspecialchars($siteSettings['accreditation_subtitle'] ?? 'Trusted excellence backed by top accreditations') ?>" required placeholder="e.g. Trusted excellence backed by top accreditations">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label class="form-label small fw-bold text-dark d-block">Section Visibility</label>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="accreditation_enabled" value="1" id="accredEnabledToggle" <?= (!isset($siteSettings['accreditation_enabled']) || $siteSettings['accreditation_enabled'] === '1') ? 'checked' : '' ?>>
                                                <label class="form-check-label small fw-semibold text-success" for="accredEnabledToggle">Show on Homepage</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold">
                                                <i class="fa-solid fa-floppy-disk me-1"></i>Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Main Table Card: Accreditation Logos / Items List -->
                        <div class="card p-3 border border-light shadow-sm rounded-4">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                                <div>
                                    <h5 class="fw-bold text-primary mb-1"><i class="fa-solid fa-award text-warning me-2"></i>Accreditation Logos & Partners</h5>
                                    <p class="text-muted small mb-0">Manage national and international accreditation badges, government affiliations, and certifying logos.</p>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button class="btn btn-primary btn-sm rounded-pill px-4 fw-bold" data-bs-toggle="modal" data-bs-target="#addAccredModal">
                                        <i class="fa-solid fa-plus me-1"></i>Add Accreditation
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Pills for Quick Filtering -->
                            <div class="d-flex gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-bold accred-filter-btn active" data-filter="all">
                                    All (<?= count($dbAccreditations) ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1 fw-bold accred-filter-btn" data-filter="international">
                                    <i class="fa-solid fa-earth-americas me-1"></i>International (<?= count(array_filter($dbAccreditations, fn($a) => strtolower($a['category']) === 'international')) ?>)
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1 fw-bold accred-filter-btn" data-filter="national">
                                    <i class="fa-solid fa-flag me-1"></i>National (<?= count(array_filter($dbAccreditations, fn($a) => strtolower($a['category']) === 'national')) ?>)
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-middle table-hover mb-0 bg-white border rounded">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 60px;">ID</th>
                                            <th style="width: 140px;">Logo Preview</th>
                                            <th>Accreditation / Organization Name</th>
                                            <th style="width: 140px;">Category</th>
                                            <th style="width: 90px;" class="text-center">Order</th>
                                            <th style="width: 110px;" class="text-center">Status</th>
                                            <th style="width: 140px;" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($dbAccreditations)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-5">
                                                    <i class="fa-solid fa-award fs-1 text-secondary mb-2 d-block"></i>
                                                    No accreditations found. Click <strong>"Add Accreditation"</strong> above to create your first partner accreditation.
                                                </td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($dbAccreditations as $acc): 
                                                $rawLogo = $acc['logo'];
                                                $logoSrc = (strpos($rawLogo, 'http') === 0) ? $rawLogo : ( (strpos($rawLogo, 'uploads/') === 0) ? '../' . $rawLogo : (file_exists(__DIR__ . '/../' . $rawLogo) ? '../' . $rawLogo : (file_exists(__DIR__ . '/../assets/images/' . $rawLogo) ? '../assets/images/' . $rawLogo : '../' . $rawLogo)) );
                                                $catLower = strtolower($acc['category']);
                                            ?>
                                                <tr class="accred-row-item" data-category="<?= htmlspecialchars($catLower) ?>">
                                                    <td><code>#<?= $acc['id'] ?></code></td>
                                                    <td>
                                                        <div class="p-2 bg-light border rounded text-center d-flex align-items-center justify-content-center" style="width: 110px; height: 60px;">
                                                            <img src="<?= htmlspecialchars($logoSrc) ?>" alt="<?= htmlspecialchars($acc['title']) ?>" style="max-height: 48px; max-width: 95px; object-fit: contain;">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong class="text-dark d-block"><?= htmlspecialchars($acc['title']) ?></strong>
                                                        <span class="text-muted small" style="font-size: 11px;">Path: <code><?= htmlspecialchars($acc['logo']) ?></code></span>
                                                    </td>
                                                    <td>
                                                        <?php if ($catLower === 'international'): ?>
                                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 fw-bold">
                                                                <i class="fa-solid fa-earth-americas me-1"></i>International
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1 fw-bold">
                                                                <i class="fa-solid fa-flag me-1"></i>National
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-light text-dark border"><?= (int)$acc['display_order'] ?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="toggle_accreditation_status">
                                                            <input type="hidden" name="id" value="<?= $acc['id'] ?>">
                                                            <?php if ($acc['is_active'] == 1): ?>
                                                                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Click to Deactivate">
                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1"><i class="fa-solid fa-check me-1"></i>Active</span>
                                                                </button>
                                                            <?php else: ?>
                                                                <button type="submit" class="btn btn-sm btn-link p-0 text-decoration-none" title="Click to Activate">
                                                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1"><i class="fa-solid fa-ban me-1"></i>Inactive</span>
                                                                </button>
                                                            <?php endif; ?>
                                                        </form>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex justify-content-center gap-1">
                                                            <button class="btn btn-outline-secondary btn-sm rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#editAccredModal<?= $acc['id'] ?>" title="Edit Accreditation">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this accreditation?');">
                                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                <input type="hidden" name="action" value="delete_accreditation">
                                                                <input type="hidden" name="id" value="<?= $acc['id'] ?>">
                                                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2" title="Delete">
                                                                    <i class="fa-solid fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <!-- EDIT ACCREDITATION MODAL -->
                                                <div class="modal fade" id="editAccredModal<?= $acc['id'] ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg rounded-4">
                                                            <form method="POST" enctype="multipart/form-data">
                                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                                <input type="hidden" name="action" value="edit_accreditation">
                                                                <input type="hidden" name="id" value="<?= $acc['id'] ?>">
                                                                <input type="hidden" name="existing_logo" value="<?= htmlspecialchars($acc['logo']) ?>">
                                                                
                                                                <div class="modal-header bg-light">
                                                                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-award text-warning me-2"></i>Edit Accreditation #<?= $acc['id'] ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body p-4">
                                                                    <div class="form-group mb-3">
                                                                        <label class="form-label fw-semibold">Accreditation / Organization Name</label>
                                                                        <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($acc['title']) ?>" required>
                                                                    </div>
                                                                    <div class="form-group mb-3">
                                                                        <label class="form-label fw-semibold">Category / Tab Type</label>
                                                                        <select name="category" class="form-select" required>
                                                                            <option value="international" <?= $catLower === 'international' ? 'selected' : '' ?>>International</option>
                                                                            <option value="national" <?= $catLower === 'national' ? 'selected' : '' ?>>National</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="form-group mb-3">
                                                                        <label class="form-label fw-semibold">Current Logo</label>
                                                                        <div class="p-2 bg-light border rounded text-center mb-2" style="max-height: 80px;">
                                                                            <img src="<?= htmlspecialchars($logoSrc) ?>" alt="Current Logo" style="max-height: 60px; object-fit: contain;">
                                                                        </div>
                                                                        <label class="form-label fw-semibold">Change Logo File (Upload New)</label>
                                                                        <input type="file" name="logo_file" class="form-control" accept="image/*">
                                                                        <div class="form-text text-muted">Leave empty to keep the existing logo image.</div>
                                                                    </div>
                                                                    <div class="form-group mb-3">
                                                                        <label class="form-label fw-semibold">Display Order</label>
                                                                        <input type="number" name="display_order" class="form-control" value="<?= (int)$acc['display_order'] ?>" min="0">
                                                                    </div>
                                                                    <div class="form-check form-switch mb-2">
                                                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_active_<?= $acc['id'] ?>" <?= $acc['is_active'] == 1 ? 'checked' : '' ?>>
                                                                        <label class="form-check-label fw-semibold" for="edit_active_<?= $acc['id'] ?>">Active (Visible on Website)</label>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light border-0">
                                                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Save Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Accreditation Category Filter Script -->
                        <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const filterBtns = document.querySelectorAll('.accred-filter-btn');
                            const rows = document.querySelectorAll('.accred-row-item');

                            filterBtns.forEach(btn => {
                                btn.addEventListener('click', function() {
                                    filterBtns.forEach(b => b.classList.remove('active'));
                                    this.classList.add('active');

                                    const targetFilter = this.getAttribute('data-filter');
                                    rows.forEach(row => {
                                        if (targetFilter === 'all' || row.getAttribute('data-category') === targetFilter) {
                                            row.style.display = '';
                                        } else {
                                            row.style.display = 'none';
                                        }
                                    });
                                });
                            });
                        });
                        </script>
                    </div>

                    <!-- ============================================================== -->
                    <!-- 1. SEO PAGES MANAGER PANEL                                     -->
                    <!-- ============================================================== -->
                    <div class="tab-pane fade" id="seo-pages-panel" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="fa-solid fa-magnifying-glass text-warning me-2"></i>SEO Page Manager & SERP Simulator
                                </h4>
                                <p class="text-muted small mb-0">Manage meta titles, descriptions, canonical URLs, OG social tags, and preview live Google search appearances.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="../sitemap.xml" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="fa-solid fa-sitemap me-1 text-warning"></i>View Sitemap
                                </a>
                                <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addSeoPageModal">
                                    <i class="fa-solid fa-plus me-1"></i>Add SEO Route
                                </button>
                            </div>
                        </div>

                        <!-- Filter Controls -->
                        <div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-light">
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                                        <input type="text" id="seoSearchInput" class="form-control border-start-0" placeholder="Search page name, slug, or title...">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3">
                                    <select id="seoRobotsFilter" class="form-select">
                                        <option value="">All Robots Directives</option>
                                        <option value="index, follow">index, follow</option>
                                        <option value="noindex, follow">noindex, follow</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-3 text-md-end text-muted small">
                                    Total Configured Routes: <strong class="text-primary"><?= count($dbSeoPages) ?></strong>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Pages Table -->
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0 bg-white border rounded" id="seoPagesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th style="width: 180px;">Page Name & Route</th>
                                        <th>Google SERP Meta Title</th>
                                        <th>Meta Description</th>
                                        <th style="width: 120px;">Robots</th>
                                        <th style="width: 90px;" class="text-center">Status</th>
                                        <th style="width: 120px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dbSeoPages)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <i class="fa-solid fa-magnifying-glass fs-1 text-secondary mb-2 d-block"></i>
                                                No SEO routes found. Click <strong>"Add SEO Route"</strong> to configure meta tags for your pages.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dbSeoPages as $sp): 
                                            $tLen = mb_strlen($sp['meta_title'] ?? '');
                                            $dLen = mb_strlen($sp['meta_description'] ?? '');
                                            $tBadgeClass = ($tLen >= 50 && $tLen <= 60) ? 'bg-success' : (($tLen > 0 && $tLen < 50) ? 'bg-warning text-dark' : ($tLen > 60 ? 'bg-danger' : 'bg-secondary'));
                                            $dBadgeClass = ($dLen >= 120 && $dLen <= 160) ? 'bg-success' : (($dLen > 0 && $dLen < 120) ? 'bg-warning text-dark' : ($dLen > 160 ? 'bg-danger' : 'bg-secondary'));
                                        ?>
                                            <tr class="seo-row" data-slug="<?= htmlspecialchars(strtolower($sp['slug'])) ?>" data-robots="<?= htmlspecialchars($sp['robots'] ?? '') ?>">
                                                <td><code>#<?= $sp['id'] ?></code></td>
                                                <td>
                                                    <strong class="text-primary d-block"><?= htmlspecialchars($sp['page_name']) ?></strong>
                                                    <code class="small text-muted">/<?= htmlspecialchars($sp['slug']) ?></code>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold text-dark text-truncate" style="max-width: 260px;" title="<?= htmlspecialchars($sp['meta_title']) ?>">
                                                        <?= htmlspecialchars($sp['meta_title'] ?: '—') ?>
                                                    </div>
                                                    <span class="badge <?= $tBadgeClass ?> rounded-pill" style="font-size: 10px;">
                                                        <?= $tLen ?> chars <?= ($tLen >= 50 && $tLen <= 60) ? '✓ Optimal' : '' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="text-muted small text-truncate" style="max-width: 320px;" title="<?= htmlspecialchars($sp['meta_description']) ?>">
                                                        <?= htmlspecialchars($sp['meta_description'] ?: '—') ?>
                                                    </div>
                                                    <span class="badge <?= $dBadgeClass ?> rounded-pill" style="font-size: 10px;">
                                                        <?= $dLen ?> chars <?= ($dLen >= 120 && $dLen <= 160) ? '✓ Optimal' : '' ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border small"><?= htmlspecialchars($sp['robots'] ?? 'index, follow') ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= ($sp['status'] ?? 1) == 1 ? 'success' : 'secondary' ?>">
                                                        <?= ($sp['status'] ?? 1) == 1 ? 'Active' : 'Disabled' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editSeoModal<?= $sp['id'] ?>" title="Edit SEO Details">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete SEO configuration for route <?= htmlspecialchars($sp['slug']) ?>?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_seo_page">
                                                            <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- EDIT SEO MODAL FOR #<?= $sp['id'] ?> -->
                                            <div class="modal fade" id="editSeoModal<?= $sp['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <form method="POST" enctype="multipart/form-data">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_seo_page">
                                                            <input type="hidden" name="id" value="<?= $sp['id'] ?>">
                                                            <input type="hidden" name="existing_og_image" value="<?= htmlspecialchars($sp['og_image'] ?? '') ?>">

                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-primary">
                                                                    <i class="fa-solid fa-magnifying-glass text-warning me-2"></i>Edit SEO Route: <code><?= htmlspecialchars($sp['slug']) ?></code>
                                                                </h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="row g-4">
                                                                    <!-- Left: Form inputs -->
                                                                    <div class="col-12 col-lg-7">
                                                                        <div class="row g-3">
                                                                            <div class="col-12 col-md-6 form-group">
                                                                                <label class="fw-semibold">Page / Section Name <span class="text-danger">*</span></label>
                                                                                <input type="text" name="page_name" class="form-control" value="<?= htmlspecialchars($sp['page_name']) ?>" required>
                                                                            </div>
                                                                            <div class="col-12 col-md-6 form-group">
                                                                                <label class="fw-semibold">Route Slug <span class="text-danger">*</span></label>
                                                                                <input type="text" name="slug" class="form-control font-monospace" value="<?= htmlspecialchars($sp['slug']) ?>" required>
                                                                            </div>

                                                                            <!-- Meta Title with Counter -->
                                                                            <div class="col-12 form-group">
                                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                    <label class="fw-semibold">Meta Title Tag <span class="text-danger">*</span></label>
                                                                                    <span class="badge bg-light text-dark border small" id="titleCount_edit_<?= $sp['id'] ?>">
                                                                                        <?= mb_strlen($sp['meta_title'] ?? '') ?>/60 chars (Optimal: 50-60)
                                                                                    </span>
                                                                                </div>
                                                                                <input type="text" name="meta_title" id="metaTitleInput_edit_<?= $sp['id'] ?>" class="form-control seo-title-sync" data-preview-id="serpPreview_edit_<?= $sp['id'] ?>" value="<?= htmlspecialchars($sp['meta_title'] ?? '') ?>" maxlength="70" required>
                                                                            </div>

                                                                            <!-- Meta Description with Counter -->
                                                                            <div class="col-12 form-group">
                                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                                    <label class="fw-semibold">Meta Description Tag <span class="text-danger">*</span></label>
                                                                                    <span class="badge bg-light text-dark border small" id="descCount_edit_<?= $sp['id'] ?>">
                                                                                        <?= mb_strlen($sp['meta_description'] ?? '') ?>/160 chars (Optimal: 120-160)
                                                                                    </span>
                                                                                </div>
                                                                                <textarea name="meta_description" id="metaDescInput_edit_<?= $sp['id'] ?>" class="form-control seo-desc-sync" data-preview-id="serpPreview_edit_<?= $sp['id'] ?>" rows="3" maxlength="180" required><?= htmlspecialchars($sp['meta_description'] ?? '') ?></textarea>
                                                                            </div>

                                                                            <div class="col-12 form-group">
                                                                                <label class="fw-semibold">Focus Keywords</label>
                                                                                <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars($sp['meta_keywords'] ?? '') ?>" placeholder="e.g. study in india, mbbs admission nepal, biratnagar consultancy">
                                                                            </div>

                                                                            <div class="col-12 form-group">
                                                                                <label class="fw-semibold">Canonical URL Override (Optional)</label>
                                                                                <input type="url" name="canonical_url" class="form-control font-monospace" value="<?= htmlspecialchars($sp['canonical_url'] ?? '') ?>" placeholder="https://visionpoint.edu.np/...">
                                                                                <div class="form-text text-muted">Leave blank to auto-generate canonical link based on route slug.</div>
                                                                            </div>

                                                                            <div class="col-12 col-md-6 form-group">
                                                                                <label class="fw-semibold">Robots Indexing</label>
                                                                                <select name="robots" class="form-select">
                                                                                    <option value="index, follow" <?= ($sp['robots'] ?? '') === 'index, follow' ? 'selected' : '' ?>>index, follow (Default - Index in Google)</option>
                                                                                    <option value="noindex, follow" <?= ($sp['robots'] ?? '') === 'noindex, follow' ? 'selected' : '' ?>>noindex, follow (Hide from Google)</option>
                                                                                    <option value="noindex, nofollow" <?= ($sp['robots'] ?? '') === 'noindex, nofollow' ? 'selected' : '' ?>>noindex, nofollow (Complete block)</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-12 col-md-6 form-group">
                                                                                <label class="fw-semibold">Schema.org Type</label>
                                                                                <select name="schema_type" class="form-select">
                                                                                    <option value="WebPage" <?= ($sp['schema_type'] ?? '') === 'WebPage' ? 'selected' : '' ?>>WebPage</option>
                                                                                    <option value="EducationalOrganization" <?= ($sp['schema_type'] ?? '') === 'EducationalOrganization' ? 'selected' : '' ?>>EducationalOrganization</option>
                                                                                    <option value="Article" <?= ($sp['schema_type'] ?? '') === 'Article' ? 'selected' : '' ?>>Article / Guide</option>
                                                                                    <option value="Course" <?= ($sp['schema_type'] ?? '') === 'Course' ? 'selected' : '' ?>>Course</option>
                                                                                    <option value="FAQPage" <?= ($sp['schema_type'] ?? '') === 'FAQPage' ? 'selected' : '' ?>>FAQPage</option>
                                                                                    <option value="ContactPage" <?= ($sp['schema_type'] ?? '') === 'ContactPage' ? 'selected' : '' ?>>ContactPage</option>
                                                                                    <option value="AboutPage" <?= ($sp['schema_type'] ?? '') === 'AboutPage' ? 'selected' : '' ?>>AboutPage</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-12 col-md-6 form-group">
                                                                                <label class="fw-semibold">Twitter Card Type</label>
                                                                                <select name="twitter_card" class="form-select">
                                                                                    <option value="summary_large_image" <?= ($sp['twitter_card'] ?? '') === 'summary_large_image' ? 'selected' : '' ?>>summary_large_image</option>
                                                                                    <option value="summary" <?= ($sp['twitter_card'] ?? '') === 'summary' ? 'selected' : '' ?>>summary</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-12 col-md-6 form-group">
                                                                                <label class="fw-semibold">Status</label>
                                                                                <select name="status" class="form-select">
                                                                                    <option value="1" <?= ($sp['status'] ?? 1) == 1 ? 'selected' : '' ?>>Active (Enabled)</option>
                                                                                    <option value="0" <?= ($sp['status'] ?? 1) == 0 ? 'selected' : '' ?>>Disabled</option>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-12 form-group">
                                                                                <label class="fw-semibold">Open Graph Social Image (Upload / URL)</label>
                                                                                <input type="file" name="og_image_file" class="form-control" accept="image/*">
                                                                                <?php if (!empty($sp['og_image'])): ?>
                                                                                    <div class="mt-2 text-muted small">Current: <code><?= htmlspecialchars($sp['og_image']) ?></code></div>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Right: Live Google SERP Simulator -->
                                                                    <div class="col-12 col-lg-5">
                                                                        <div class="card border border-light bg-light p-3 rounded-4 sticky-top" style="top: 20px;">
                                                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                                                <h6 class="fw-bold text-primary mb-0">
                                                                                    <i class="fa-brands fa-google text-danger me-1"></i>Google SERP Simulator
                                                                                </h6>
                                                                                <div class="btn-group btn-group-sm" role="group">
                                                                                    <button type="button" class="btn btn-outline-primary active btn-serp-mode" data-mode="desktop" data-target="serpPreview_edit_<?= $sp['id'] ?>"><i class="fa-solid fa-desktop me-1"></i>Desktop</button>
                                                                                    <button type="button" class="btn btn-outline-primary btn-serp-mode" data-mode="mobile" data-target="serpPreview_edit_<?= $sp['id'] ?>"><i class="fa-solid fa-mobile-screen me-1"></i>Mobile</button>
                                                                                </div>
                                                                            </div>

                                                                            <!-- SERP Preview Container -->
                                                                            <div class="serp-preview-box bg-white p-3 rounded-3 shadow-sm border" id="serpPreview_edit_<?= $sp['id'] ?>">
                                                                                <!-- Favicon & Site Name -->
                                                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                                                    <div class="bg-light rounded-circle p-1 border d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                                                                        <img src="../assets/images/logo1.png" alt="Favicon" style="max-height: 18px; max-width: 18px; object-fit: contain;">
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="text-dark small fw-bold" style="font-size: 13px; line-height: 1.2;">Vision Point Educational Service</div>
                                                                                        <div class="text-muted" style="font-size: 11px; line-height: 1.2;">https://visionpoint.edu.np &rsaquo; <?= htmlspecialchars($sp['slug']) ?></div>
                                                                                    </div>
                                                                                </div>

                                                                                <!-- SERP Title -->
                                                                                <h5 class="serp-title text-primary mb-1 mt-2" style="font-family: Arial, sans-serif; font-size: 18px; line-height: 1.3; color: #1a0dab !important; cursor: pointer; text-decoration: none;">
                                                                                    <?= htmlspecialchars($sp['meta_title'] ?: 'Vision Point Educational Service | Best Education Consultancy') ?>
                                                                                </h5>

                                                                                <!-- SERP Description -->
                                                                                <p class="serp-desc text-muted mb-0" style="font-family: Arial, sans-serif; font-size: 13px; line-height: 1.5; color: #4d5156 !important;">
                                                                                    <?= htmlspecialchars($sp['meta_description'] ?: 'Vision Point Educational Service is Nepal\'s top education consultancy in Biratnagar for Study in India admissions, engineering, nursing, and medical scholarships.') ?>
                                                                                </p>
                                                                            </div>

                                                                            <div class="mt-3 p-2 bg-white rounded border small">
                                                                                <div class="d-flex justify-content-between mb-1">
                                                                                    <span>Title Width:</span>
                                                                                    <strong class="text-success"><span id="titlePixelWidth_edit_<?= $sp['id'] ?>"><?= min(580, max(100, $tLen * 9)) ?></span>px / 600px</strong>
                                                                                </div>
                                                                                <div class="progress" style="height: 6px;">
                                                                                    <div class="progress-bar <?= ($tLen >= 50 && $tLen <= 60) ? 'bg-success' : 'bg-warning' ?>" id="titleProgress_edit_<?= $sp['id'] ?>" style="width: <?= min(100, ($tLen / 60) * 100) ?>%"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light border-0">
                                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Save SEO Settings</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- 2. SEO DIAGNOSTIC AUDIT PANEL                                  -->
                    <!-- ============================================================== -->
                    <div class="tab-pane fade" id="seo-audit-panel" role="tabpanel">
                        <?php
                        // Calculate audit diagnostics
                        $auditTotal = count($dbSeoPages);
                        $auditMissingTitle = 0;
                        $auditMissingDesc = 0;
                        $auditOptimalTitle = 0;
                        $auditOptimalDesc = 0;
                        $auditRobotsBlocked = 0;

                        foreach ($dbSeoPages as $ap) {
                            $tl = mb_strlen(trim($ap['meta_title'] ?? ''));
                            $dl = mb_strlen(trim($ap['meta_description'] ?? ''));
                            if ($tl === 0) $auditMissingTitle++;
                            elseif ($tl >= 50 && $tl <= 60) $auditOptimalTitle++;

                            if ($dl === 0) $auditMissingDesc++;
                            elseif ($dl >= 120 && $dl <= 160) $auditOptimalDesc++;

                            if (strpos($ap['robots'] ?? '', 'noindex') !== false) {
                                $auditRobotsBlocked++;
                            }
                        }

                        $auditScore = ($auditTotal > 0) ? round((($auditOptimalTitle + $auditOptimalDesc) / ($auditTotal * 2)) * 100) : 100;
                        ?>
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="fa-solid fa-stethoscope text-success me-2"></i>Technical SEO Diagnostic Health Audit
                                </h4>
                                <p class="text-muted small mb-0">Automated scanner checks meta tags, sitemaps, robots.txt, canonical URLs, and schema markup.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary btn-sm rounded-pill px-4" onclick="location.reload();">
                                    <i class="fa-solid fa-rotate me-1"></i>Re-run SEO Audit
                                </button>
                            </div>
                        </div>

                        <!-- KPI Summary Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-md-3">
                                <div class="card p-3 border-0 shadow-sm rounded-4 bg-white h-100 border-start border-primary border-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small fw-semibold">Overall SEO Score</span>
                                            <h3 class="fw-extrabold text-primary mb-0 mt-1"><?= $auditScore ?>%</h3>
                                        </div>
                                        <div class="bg-primary-subtle text-primary rounded-circle p-3">
                                            <i class="fa-solid fa-gauge-high fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-success small fw-semibold"><i class="fa-solid fa-circle-check me-1"></i><?= $auditOptimalTitle ?> / <?= $auditTotal ?> Optimal Titles</div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card p-3 border-0 shadow-sm rounded-4 bg-white h-100 border-start border-success border-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small fw-semibold">Dynamic Sitemap</span>
                                            <h3 class="fw-extrabold text-success mb-0 mt-1">Active</h3>
                                        </div>
                                        <div class="bg-success-subtle text-success rounded-circle p-3">
                                            <i class="fa-solid fa-sitemap fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2 small"><a href="../sitemap.xml" target="_blank" class="text-decoration-none fw-semibold">Verify sitemap.xml &rsaquo;</a></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card p-3 border-0 shadow-sm rounded-4 bg-white h-100 border-start border-info border-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small fw-semibold">Robots.txt</span>
                                            <h3 class="fw-extrabold text-info mb-0 mt-1">Configured</h3>
                                        </div>
                                        <div class="bg-info-subtle text-info rounded-circle p-3">
                                            <i class="fa-solid fa-robot fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2 small"><a href="../robots.txt" target="_blank" class="text-decoration-none fw-semibold">View robots.txt &rsaquo;</a></div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="card p-3 border-0 shadow-sm rounded-4 bg-white h-100 border-start border-warning border-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted small fw-semibold">Missing Metas</span>
                                            <h3 class="fw-extrabold text-warning mb-0 mt-1"><?= $auditMissingTitle + $auditMissingDesc ?></h3>
                                        </div>
                                        <div class="bg-warning-subtle text-warning rounded-circle p-3">
                                            <i class="fa-solid fa-triangle-exclamation fs-4"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-muted small"><?= $auditMissingTitle ?> titles, <?= $auditMissingDesc ?> descriptions</div>
                                </div>
                            </div>
                        </div>

                        <!-- Health Audit Table -->
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <h5 class="fw-bold text-primary mb-3">
                                <i class="fa-solid fa-list-check text-warning me-2"></i>Route-by-Route SEO Audit Breakdown
                            </h5>
                            <div class="table-responsive">
                                <table class="table align-middle table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Route URL</th>
                                            <th>Title Health</th>
                                            <th>Description Health</th>
                                            <th>Canonical</th>
                                            <th>Robots</th>
                                            <th>Schema Type</th>
                                            <th class="text-center">Score</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dbSeoPages as $aud): 
                                            $tLen = mb_strlen(trim($aud['meta_title'] ?? ''));
                                            $dLen = mb_strlen(trim($aud['meta_description'] ?? ''));
                                            
                                            // Calculate per-page health score
                                            $pScore = 100;
                                            if ($tLen === 0) $pScore -= 40;
                                            elseif ($tLen < 50 || $tLen > 60) $pScore -= 15;

                                            if ($dLen === 0) $pScore -= 40;
                                            elseif ($dLen < 120 || $dLen > 160) $pScore -= 15;

                                            $scoreBadge = ($pScore >= 90) ? 'bg-success' : (($pScore >= 70) ? 'bg-warning text-dark' : 'bg-danger');
                                        ?>
                                            <tr>
                                                <td>
                                                    <strong class="text-primary"><?= htmlspecialchars($aud['page_name']) ?></strong>
                                                    <div class="small text-muted">/<?= htmlspecialchars($aud['slug']) ?></div>
                                                </td>
                                                <td>
                                                    <?php if ($tLen >= 50 && $tLen <= 60): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-check me-1"></i><?= $tLen ?> chars (Optimal)</span>
                                                    <?php elseif ($tLen > 0): ?>
                                                        <span class="badge bg-warning-subtle text-dark border border-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i><?= $tLen ?> chars (Needs polish)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-xmark me-1"></i>Missing Title</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($dLen >= 120 && $dLen <= 160): ?>
                                                        <span class="badge bg-success-subtle text-success border border-success"><i class="fa-solid fa-check me-1"></i><?= $dLen ?> chars (Optimal)</span>
                                                    <?php elseif ($dLen > 0): ?>
                                                        <span class="badge bg-warning-subtle text-dark border border-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i><?= $dLen ?> chars (Needs polish)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger-subtle text-danger border border-danger"><i class="fa-solid fa-xmark me-1"></i>Missing Description</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-secondary border small"><i class="fa-solid fa-link me-1"></i><?= !empty($aud['canonical_url']) ? 'Explicit' : 'Auto' ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-dark border small"><?= htmlspecialchars($aud['robots'] ?? 'index, follow') ?></span>
                                                </td>
                                                <td>
                                                    <code class="small text-primary"><?= htmlspecialchars($aud['schema_type'] ?? 'WebPage') ?></code>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?= $scoreBadge ?> rounded-pill px-3"><?= $pScore ?>%</span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editSeoModal<?= $aud['id'] ?>">
                                                        <i class="fa-solid fa-gear me-1"></i>Optimize
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ============================================================== -->
                    <!-- 3. 301 REDIRECTS MANAGER PANEL                                 -->
                    <!-- ============================================================== -->
                    <div class="tab-pane fade" id="seo-redirects-panel" role="tabpanel">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                            <div>
                                <h4 class="fw-bold text-primary mb-1">
                                    <i class="fa-solid fa-arrow-turn-down text-warning me-2"></i>301 & 302 URL Redirects Manager
                                </h4>
                                <p class="text-muted small mb-0">Prevent broken 404 links and preserve search rank by seamlessly redirecting old URLs to new destinations.</p>
                            </div>
                            <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addRedirectModal">
                                <i class="fa-solid fa-plus me-1"></i>Add Redirect Rule
                            </button>
                        </div>

                        <!-- Redirects Table -->
                        <div class="table-responsive">
                            <table class="table align-middle table-hover mb-0 bg-white border rounded">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">ID</th>
                                        <th>Source Old URL</th>
                                        <th>Destination Target URL</th>
                                        <th style="width: 120px;" class="text-center">Status Code</th>
                                        <th>Notes / Description</th>
                                        <th style="width: 100px;" class="text-center">Status</th>
                                        <th style="width: 120px;" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($dbSeoRedirects)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-5">
                                                <i class="fa-solid fa-arrow-turn-down fs-1 text-secondary mb-2 d-block"></i>
                                                No 301 redirects configured yet. Click <strong>"Add Redirect Rule"</strong> to create URL mappings.
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($dbSeoRedirects as $red): ?>
                                            <tr>
                                                <td><code>#<?= $red['id'] ?></code></td>
                                                <td><code class="text-danger fw-bold"><?= htmlspecialchars($red['old_url']) ?></code></td>
                                                <td><code class="text-success fw-bold"><?= htmlspecialchars($red['new_url']) ?></code></td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $red['status_code'] == 301 ? 'primary' : 'warning text-dark' ?>">
                                                        <?= (int)$red['status_code'] ?> <?= $red['status_code'] == 301 ? 'Permanent' : 'Temporary' ?>
                                                    </span>
                                                </td>
                                                <td><span class="text-muted small"><?= htmlspecialchars($red['notes'] ?: '—') ?></span></td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= ($red['is_active'] ?? 1) == 1 ? 'success' : 'secondary' ?>">
                                                        <?= ($red['is_active'] ?? 1) == 1 ? 'Active' : 'Disabled' ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="modal" data-bs-target="#editRedirectModal<?= $red['id'] ?>">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Delete redirect rule for <?= htmlspecialchars($red['old_url']) ?>?');">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="delete_seo_redirect">
                                                            <input type="hidden" name="id" value="<?= $red['id'] ?>">
                                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- EDIT REDIRECT MODAL -->
                                            <div class="modal fade" id="editRedirectModal<?= $red['id'] ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                                        <form method="POST">
                                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                            <input type="hidden" name="action" value="edit_seo_redirect">
                                                            <input type="hidden" name="id" value="<?= $red['id'] ?>">

                                                            <div class="modal-header bg-light">
                                                                <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-pen-to-square me-2"></i>Edit Redirect #<?= $red['id'] ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4">
                                                                <div class="form-group mb-3">
                                                                    <label class="fw-semibold">Source Old URL (From) <span class="text-danger">*</span></label>
                                                                    <input type="text" name="old_url" class="form-control font-monospace" value="<?= htmlspecialchars($red['old_url']) ?>" required>
                                                                    <div class="form-text text-muted">e.g. <code>/about-us.html</code> or <code>old-services.php</code></div>
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label class="fw-semibold">Destination Target URL (To) <span class="text-danger">*</span></label>
                                                                    <input type="text" name="new_url" class="form-control font-monospace" value="<?= htmlspecialchars($red['new_url']) ?>" required>
                                                                    <div class="form-text text-muted">e.g. <code>/about</code> or <code>https://visionpoint.edu.np/about.php</code></div>
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label class="fw-semibold">HTTP Status Code</label>
                                                                    <select name="status_code" class="form-select">
                                                                        <option value="301" <?= $red['status_code'] == 301 ? 'selected' : '' ?>>301 Moved Permanently (Pass SEO Value)</option>
                                                                        <option value="302" <?= $red['status_code'] == 302 ? 'selected' : '' ?>>302 Found (Temporary Redirect)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group mb-3">
                                                                    <label class="fw-semibold">Internal Notes / Reason</label>
                                                                    <input type="text" name="notes" class="form-control" value="<?= htmlspecialchars($red['notes'] ?? '') ?>" placeholder="e.g. Legacy URL migration">
                                                                </div>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_redir_active_<?= $red['id'] ?>" <?= ($red['is_active'] ?? 1) == 1 ? 'checked' : '' ?>>
                                                                    <label class="form-check-label fw-semibold" for="edit_redir_active_<?= $red['id'] ?>">Active (Enable redirect)</label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light border-0">
                                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- ================= MODALS TO ADD ITEMS ================= -->

        <!-- ADD ACCREDITATION MODAL -->
        <div class="modal fade" id="addAccredModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_accreditation">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-award text-warning me-2"></i>Add New Accreditation Partner</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">Accreditation / Organization Name</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Government of Nepal / MOEST, CTEVT, British Council" required>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">Category / Tab Type</label>
                                <select name="category" class="form-select" required>
                                    <option value="international">International</option>
                                    <option value="national">National</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">Accreditation Logo File (Upload)</label>
                                <input type="file" name="logo_file" class="form-control" accept="image/*" required>
                                <div class="form-text text-muted">Recommended: PNG, JPG, or SVG on transparent or white background.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="form-label fw-semibold">Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_accred_active" checked>
                                <label class="form-check-label fw-semibold" for="add_accred_active">Active (Visible on Website)</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Add Accreditation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD UNIVERSITY MODAL -->
        <div class="modal fade" id="addUniModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_university">
                        <input type="hidden" name="img" value="">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Partner Institution</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>University Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Sharda University" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>City Location</label>
                                <input type="text" name="city" class="form-control" placeholder="e.g. Delhi NCR" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Region Filter</label>
                                <input type="text" name="region" class="form-control" placeholder="e.g. Delhi" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Detailed Location / Address</label>
                                <input type="text" name="location_detail" class="form-control" placeholder="e.g. Noida, Uttar Pradesh, India">
                            </div>
                            <div class="form-group mb-3">
                                <label>About / Description</label>
                                <textarea name="about" class="form-control" rows="3" placeholder="Description of the university..."></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Fees & Scholarship Structure</label>
                                <textarea name="fees" class="form-control" rows="3" placeholder="e.g. Tuition: INR 1,50,000/yr. up to 50% merit scholarships."></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Admission Process Detail</label>
                                <textarea name="admission_process" class="form-control" rows="3" placeholder="e.g. Online enrollment -> counseling interview -> provisional admission."></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Google Map Link / Embed Code</label>
                                <textarea name="map_link" class="form-control" rows="2" placeholder="e.g. https://maps.app.goo.gl/... or <iframe src='...'></iframe>"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="d-block mb-2">Offered Courses / Streams</label>
                                <div class="d-flex flex-wrap gap-3 border rounded p-3 bg-white" style="max-height: 200px; overflow-y: auto;">
                                    <?php foreach ($dbStreams as $stream): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="courses[]" value="<?= $stream['id'] ?>" id="add_stream_<?= $stream['id'] ?>">
                                            <label class="form-check-label small" for="add_stream_<?= $stream['id'] ?>">
                                                <?= htmlspecialchars($stream['name']) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_top" value="1" id="add_uni_is_top" checked>
                                <label class="form-check-label fw-bold text-primary" for="add_uni_is_top">
                                    Mark as Top Notch Institution
                                </label>
                                <div class="text-muted small">If checked, this institution will be showcased in the homepage premium slider and marked with a golden star in lists.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label>University Status</label>
                                <select name="status" class="form-select">
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="form-group mb-3">
                                <label>Image Logo File (Upload)</label>
                                <input type="file" name="img_file" class="form-control" accept="image/*" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Fee Structure Document (PDF/Doc/Image)</label>
                                <input type="file" name="fee_doc_file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Institution</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD DESTINATION MODAL -->
        <div class="modal fade" id="addDestModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_destination">
                        <input type="hidden" name="img" value="">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Study City Destination</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Destination City Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Bangalore" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Region Category</label>
                                <input type="text" name="region" class="form-control" placeholder="e.g. Bangalore" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>About / Description</label>
                                <textarea name="about" class="form-control" rows="3" placeholder="Description of the city..."></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Gallery Images (comma separated URLs/paths)</label>
                                <textarea name="gallery" class="form-control" rows="2" placeholder="e.g. assets/images/delhi1.jpg, assets/images/delhi2.jpg"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Google Map Link / Embed Code</label>
                                <textarea name="map_link" class="form-control" rows="2" placeholder="e.g. https://maps.app.goo.gl/... or <iframe src='...'></iframe>"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Upload Graphic Image File</label>
                                <input type="file" name="img_file" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Destination</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD COURSE STREAM MODAL -->
        <div class="modal fade" id="addStreamModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_stream">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Course Stream</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Stream Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Architecture" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Font Awesome Icon Code (e.g. <code>fa-hotel</code>)</label>
                                <input type="text" name="icon" class="form-control" placeholder="fa-gear" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Description</label>
                                <textarea name="desc" class="form-control" rows="3" placeholder="Description of the course stream"></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Banner Image File (Upload)</label>
                                <input type="file" name="img_file" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Stream</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD GUIDE ARTICLE MODAL -->
        <div class="modal fade" id="addGuideModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_guide">
                        <input type="hidden" name="image_path" value="">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Write Guide Resource Article</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Article Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. How to study B.Sc in India" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Slug URL (leave empty to auto-generate)</label>
                                <input type="text" name="slug" class="form-control" placeholder="e.g. how-to-study-bsc-in-india">
                            </div>
                            <div class="row g-3">
                                <div class="col-6 form-group mb-3">
                                    <label>Publish Status</label>
                                    <select name="status" class="form-select">
                                        <option value="published" selected>Published</option>
                                        <option value="draft">Draft</option>
                                    </select>
                                </div>
                                <div class="col-6 form-group mb-3">
                                    <label>Publish Date</label>
                                    <input type="date" name="publish_date" class="form-control" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label>Short Summary / Subtitle</label>
                                <textarea name="summary" class="form-control" rows="2" placeholder="Write a short summary..." required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Full Content (supports HTML tags)</label>
                                <textarea name="content" class="form-control" rows="8" placeholder="Write the main body text..." required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label>Upload Cover Cover Image</label>
                                <input type="file" name="img_file" class="form-control" accept="image/*" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Article</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD MENU ITEM MODAL -->
        <div class="modal fade" id="addMenuModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_menu">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Navigation Menu Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Menu Label</label>
                                <input type="text" name="label" class="form-control" placeholder="e.g. Services" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>URL / File path (e.g. <code>services.php</code>)</label>
                                <input type="text" name="url" class="form-control" placeholder="e.g. index.php" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Parent Dropdown Item</label>
                                <select name="parent_id" class="form-select">
                                    <option value="" selected>None (Top Level)</option>
                                    <?php foreach ($dbMenus as $p): ?>
                                        <?php if ($p['parent_id'] === null): ?>
                                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['label']) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-6 form-group">
                                    <label>Display Order</label>
                                    <input type="number" name="display_order" class="form-control" value="0" required>
                                </div>
                                <div class="col-6 d-flex align-items-end ps-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="is_visible" value="1" id="is_visible_add" checked>
                                        <label class="form-check-label fw-bold" for="is_visible_add">Make Visible</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Menu Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD COUNTRY OPTION MODAL -->
        <div class="modal fade" id="addCountryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_country">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Country Option</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Country Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Bangladesh" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Option</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD SOCIAL LINK MODAL -->
        <div class="modal fade" id="addSocialModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_social">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Social Link</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Platform Name</label>
                                <input type="text" name="platform_name" class="form-control" placeholder="e.g. LinkedIn" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Target URL</label>
                                <input type="text" name="url" class="form-control" placeholder="e.g. https://linkedin.com" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Font Awesome Icon (e.g. <code>fa-brands fa-linkedin</code>)</label>
                                <input type="text" name="icon" class="form-control" placeholder="fa-brands fa-facebook" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Link</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD FUN STAT FACT MODAL -->
        <div class="modal fade" id="addFactModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_fact">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add Fun Fact Stat</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Stat Number (e.g. <code>99%</code>)</label>
                                <input type="text" name="number" class="form-control" placeholder="e.g. 100+" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Label Description</label>
                                <input type="text" name="label" class="form-control" placeholder="e.g. Happy Students" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Font Awesome Icon Code (e.g. <code>fa-award</code>)</label>
                                <input type="text" name="icon" class="form-control" placeholder="fa-star" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" required>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4">Add Fact Stat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD ADMIN USER MODAL -->
        <div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_admin">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Add System Administrator</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Admin Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="e.g. counselor@visionpoint.edu.np" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Enter secure password" required>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD BANK MODAL -->
        <div class="modal fade" id="addBankModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_bank_account">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-plus-circle me-2"></i>Add New Bank Account</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12 col-md-6 form-group">
                                    <label class="fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. Siddhartha Bank Limited" required>
                                </div>
                                <div class="col-12 col-md-6 form-group">
                                    <label class="fw-semibold">Account Holder Name <span class="text-danger">*</span></label>
                                    <input type="text" name="account_name" class="form-control" placeholder="e.g. VISION POINT EDUCATIONAL SERVICE PVT. LTD." required>
                                </div>
                                <div class="col-12 col-md-6 form-group">
                                    <label class="fw-semibold">Account Number <span class="text-danger">*</span></label>
                                    <input type="text" name="account_number" class="form-control font-monospace" placeholder="e.g. 55509839020" required>
                                </div>
                                <div class="col-12 col-md-6 form-group">
                                    <label class="fw-semibold">Branch Name</label>
                                    <input type="text" name="branch" class="form-control" placeholder="e.g. Putalisadak Branch">
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label class="fw-semibold">Account Type</label>
                                    <input type="text" name="account_type" class="form-control" placeholder="e.g. Corporate Current Account">
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label class="fw-semibold">SWIFT Code</label>
                                    <input type="text" name="swift_code" class="form-control font-monospace" placeholder="e.g. SUBNEPKA">
                                </div>
                                <div class="col-12 col-md-4 form-group">
                                    <label class="fw-semibold">IFSC Code (if applicable)</label>
                                    <input type="text" name="ifsc_code" class="form-control font-monospace" placeholder="e.g. SBIN0001234">
                                </div>
                                <div class="col-12 form-group">
                                    <label class="fw-semibold">Payment Instructions</label>
                                    <textarea name="instructions" class="form-control" rows="2" placeholder="e.g. Please mention student name and contact number in transaction remarks."></textarea>
                                </div>
                                <div class="col-12 form-group">
                                    <label class="fw-semibold">Internal Remarks / Notes</label>
                                    <input type="text" name="remarks" class="form-control" placeholder="Optional notes for admin panel">
                                </div>
                                <div class="col-12 col-md-6 form-group">
                                    <label class="fw-semibold">Display Order</label>
                                    <input type="number" name="display_order" class="form-control" value="0" min="0">
                                </div>
                                <div class="col-12 col-md-6 d-flex align-items-center gap-4 pt-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_default" value="1" id="add_bank_default">
                                        <label class="form-check-label fw-bold text-primary" for="add_bank_default">Set Primary Account</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_bank_active" checked>
                                        <label class="form-check-label fw-bold text-success" for="add_bank_active">Active (Visible)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5">Add Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD PDF MODAL -->
        <div class="modal fade" id="addPdfModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_institution_pdf">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">Upload University PDF</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label>Document Title</label>
                                <input type="text" name="title" class="form-control" placeholder="e.g. Sharda University Fee Structure 2026" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Description / Type</label>
                                <input type="text" name="description" class="form-control" placeholder="e.g. Fee Structure, Brochure, Admission Details">
                            </div>
                            <div class="form-group mb-3">
                                <label>Associate with University</label>
                                <select name="institution_id" class="form-select" required>
                                    <option value="">-- Select Institution --</option>
                                    <?php foreach ($dbInstitutions as $uni): ?>
                                        <option value="<?= $uni['id'] ?>"><?= htmlspecialchars($uni['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label>PDF File (Upload)</label>
                                <input type="file" name="pdf_file" class="form-control" accept=".pdf" required>
                                <div class="text-muted extra-small mt-1">Allowed extension: PDF. Max size: 20MB.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label>Display Order</label>
                                <input type="number" name="display_order" class="form-control" value="0" min="0">
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_pdf_active" checked>
                                <label class="form-check-label" for="add_pdf_active">Active (Visible on College Details)</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5">Upload File</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD SEO PAGE ROUTE MODAL -->
        <div class="modal fade" id="addSeoPageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_seo_page">
                        
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary">
                                <i class="fa-solid fa-plus-circle text-warning me-2"></i>Add New SEO Page Route
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- Left: Form inputs -->
                                <div class="col-12 col-lg-7">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6 form-group">
                                            <label class="fw-semibold">Page / Section Name <span class="text-danger">*</span></label>
                                            <input type="text" name="page_name" class="form-control" placeholder="e.g. Study in Germany" required>
                                        </div>
                                        <div class="col-12 col-md-6 form-group">
                                            <label class="fw-semibold">Route Slug <span class="text-danger">*</span></label>
                                            <input type="text" name="slug" id="addSeoSlug" class="form-control font-monospace" placeholder="e.g. study-in-germany" required>
                                            <div class="form-text text-muted">Clean URL path (e.g. <code>study-in-germany</code>).</div>
                                        </div>

                                        <!-- Meta Title with Counter -->
                                        <div class="col-12 form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="fw-semibold">Meta Title Tag <span class="text-danger">*</span></label>
                                                <span class="badge bg-light text-dark border small" id="addSeoTitleCount">
                                                    0/60 chars (Optimal: 50-60)
                                                </span>
                                            </div>
                                            <input type="text" name="meta_title" id="addSeoTitleInput" class="form-control seo-title-sync" data-preview-id="addSerpPreview" placeholder="e.g. Study in Germany from Nepal | Top Universities & Free Tuition" maxlength="70" required>
                                        </div>

                                        <!-- Meta Description with Counter -->
                                        <div class="col-12 form-group">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="fw-semibold">Meta Description Tag <span class="text-danger">*</span></label>
                                                <span class="badge bg-light text-dark border small" id="addSeoDescCount">
                                                    0/160 chars (Optimal: 120-160)
                                                </span>
                                            </div>
                                            <textarea name="meta_description" id="addSeoDescInput" class="form-control seo-desc-sync" data-preview-id="addSerpPreview" rows="3" maxlength="180" placeholder="e.g. Complete guide for Nepalese students planning to study in Germany. Explore top public universities, English-taught programs, Blocked Account, and visa steps." required></textarea>
                                        </div>

                                        <div class="col-12 form-group">
                                            <label class="fw-semibold">Focus Keywords</label>
                                            <input type="text" name="meta_keywords" class="form-control" placeholder="e.g. study in germany nepal, public universities germany, blocked account">
                                        </div>

                                        <div class="col-12 form-group">
                                            <label class="fw-semibold">Canonical URL Override (Optional)</label>
                                            <input type="url" name="canonical_url" class="form-control font-monospace" placeholder="https://visionpoint.edu.np/study-in-germany">
                                            <div class="form-text text-muted">Leave blank to auto-generate canonical link.</div>
                                        </div>

                                        <div class="col-12 col-md-6 form-group">
                                            <label class="fw-semibold">Robots Indexing</label>
                                            <select name="robots" class="form-select">
                                                <option value="index, follow" selected>index, follow (Default - Index in Google)</option>
                                                <option value="noindex, follow">noindex, follow (Hide from Google)</option>
                                                <option value="noindex, nofollow">noindex, nofollow (Complete block)</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 form-group">
                                            <label class="fw-semibold">Schema.org Type</label>
                                            <select name="schema_type" class="form-select">
                                                <option value="WebPage" selected>WebPage</option>
                                                <option value="EducationalOrganization">EducationalOrganization</option>
                                                <option value="Article">Article / Guide</option>
                                                <option value="Course">Course</option>
                                                <option value="FAQPage">FAQPage</option>
                                                <option value="ContactPage">ContactPage</option>
                                                <option value="AboutPage">AboutPage</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 form-group">
                                            <label class="fw-semibold">Twitter Card Type</label>
                                            <select name="twitter_card" class="form-select">
                                                <option value="summary_large_image" selected>summary_large_image</option>
                                                <option value="summary">summary</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-6 form-group">
                                            <label class="fw-semibold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="1" selected>Active (Enabled)</option>
                                                <option value="0">Disabled</option>
                                            </select>
                                        </div>

                                        <div class="col-12 form-group">
                                            <label class="fw-semibold">Open Graph Social Image (Upload)</label>
                                            <input type="file" name="og_image_file" class="form-control" accept="image/*">
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Live Google SERP Simulator -->
                                <div class="col-12 col-lg-5">
                                    <div class="card border border-light bg-light p-3 rounded-4 sticky-top" style="top: 20px;">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold text-primary mb-0">
                                                <i class="fa-brands fa-google text-danger me-1"></i>Google SERP Simulator
                                            </h6>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-primary active btn-serp-mode" data-mode="desktop" data-target="addSerpPreview"><i class="fa-solid fa-desktop me-1"></i>Desktop</button>
                                                <button type="button" class="btn btn-outline-primary btn-serp-mode" data-mode="mobile" data-target="addSerpPreview"><i class="fa-solid fa-mobile-screen me-1"></i>Mobile</button>
                                            </div>
                                        </div>

                                        <!-- SERP Preview Container -->
                                        <div class="serp-preview-box bg-white p-3 rounded-3 shadow-sm border" id="addSerpPreview">
                                            <!-- Favicon & Site Name -->
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="bg-light rounded-circle p-1 border d-flex align-items-center justify-content-center" style="width: 26px; height: 26px;">
                                                    <img src="../assets/images/logo1.png" alt="Favicon" style="max-height: 18px; max-width: 18px; object-fit: contain;">
                                                </div>
                                                <div>
                                                    <div class="text-dark small fw-bold" style="font-size: 13px; line-height: 1.2;">Vision Point Educational Service</div>
                                                    <div class="text-muted" style="font-size: 11px; line-height: 1.2;">https://visionpoint.edu.np &rsaquo; <span id="addSerpSlugPreview">new-page</span></div>
                                                </div>
                                            </div>

                                            <!-- SERP Title -->
                                            <h5 class="serp-title text-primary mb-1 mt-2" style="font-family: Arial, sans-serif; font-size: 18px; line-height: 1.3; color: #1a0dab !important; cursor: pointer; text-decoration: none;">
                                                Enter Meta Title...
                                            </h5>

                                            <!-- SERP Description -->
                                            <p class="serp-desc text-muted mb-0" style="font-family: Arial, sans-serif; font-size: 13px; line-height: 1.5; color: #4d5156 !important;">
                                                Enter Meta Description snippet to simulate live appearance on Google Search Results...
                                            </p>
                                        </div>

                                        <div class="mt-3 p-2 bg-white rounded border small">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span>Title Pixel Length:</span>
                                                <strong class="text-primary"><span id="addTitlePixel">0</span>px / 600px</strong>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" id="addTitleProgressBar" style="width: 0%"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Create SEO Route</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ADD 301 REDIRECT MODAL -->
        <div class="modal fade" id="addRedirectModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="add_seo_redirect">

                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-plus-circle me-2"></i>Add URL Redirect Rule</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="form-group mb-3">
                                <label class="fw-semibold">Source Old URL (From) <span class="text-danger">*</span></label>
                                <input type="text" name="old_url" class="form-control font-monospace" placeholder="e.g. /old-course.php or courses/old-name" required>
                                <div class="form-text text-muted">The outdated or legacy incoming request URL path.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-semibold">Destination Target URL (To) <span class="text-danger">*</span></label>
                                <input type="text" name="new_url" class="form-control font-monospace" placeholder="e.g. /courses or https://visionpoint.edu.np/courses" required>
                                <div class="form-text text-muted">The destination where users and search crawlers should be routed.</div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-semibold">HTTP Status Code</label>
                                <select name="status_code" class="form-select">
                                    <option value="301" selected>301 Moved Permanently (Recommended for SEO)</option>
                                    <option value="302">302 Found (Temporary Redirect)</option>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="fw-semibold">Internal Notes / Reason</label>
                                <input type="text" name="notes" class="form-control" placeholder="e.g. Redirecting legacy site URL structure">
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="add_redir_active" checked>
                                <label class="form-check-label fw-semibold" for="add_redir_active">Active (Enable redirect immediately)</label>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Create Redirect</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Switch tabs programmatically
            function switchTab(tabId) {
                const triggerEl = document.querySelector('#' + tabId);
                if (triggerEl) {
                    bootstrap.Tab.getOrCreateInstance(triggerEl).show();
                }
            }

            $(document).ready(function() {
                // Copy link paths in File Manager
                $('.btn-copy-path').on('click', function() {
                    const relativePath = $(this).data('path');
                    const absoluteUrl = window.location.origin + '/' + relativePath;
                    
                    navigator.clipboard.writeText(relativePath).then(function() {
                        alert('Relative path copied: ' + relativePath);
                    }, function() {
                        // Fallback copy
                        const tempInput = $('<input>');
                        $('body').append(tempInput);
                        tempInput.val(relativePath).select();
                        document.execCommand('copy');
                        tempInput.remove();
                        alert('Relative path copied: ' + relativePath);
                    });
                });

                // Status dropdown AJAX update (inline table updates)
                $('.status-select-ajax').on('change', function() {
                    const $select = $(this);
                    const id = $select.data('id');
                    const type = $select.data('type');
                    const status = $select.val();
                    
                    // Show updating state
                    $select.prop('disabled', true);
                    
                    $.ajax({
                        url: 'update_status.php',
                        type: 'POST',
                        data: {
                            id: id,
                            type: type,
                            status: status,
                            csrf_token: '<?= $_SESSION['csrf_token'] ?>'
                        },
                        dataType: 'json',
                        success: function(response) {
                            $select.prop('disabled', false);
                            if (response.success) {
                                // Update badge color and content
                                const badgeId = `#badge-${type}-${id}`;
                                const $badge = $(badgeId);
                                $badge.text(status);
                                
                                // Remove old status classes
                                $badge.removeClass(function (index, className) {
                                    return (className.match (/(^|\s)status-\S+/g) || []).join(' ');
                                });
                                
                                // Add new status class
                                const cleanClass = 'status-' + status.toLowerCase().replace(' ', '-');
                                $badge.addClass(cleanClass);
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function() {
                            $select.prop('disabled', false);
                            alert('An error occurred while updating status.');
                        }
                    });
                });

                // Filter Enrollments Table Live
                function filterEnrollmentsTable() {
                    const searchVal = $('#enrollSearch').val().toLowerCase();
                    const levelFilter = $('#enrollLevelFilter').val();
                    const facultyFilter = $('#enrollFacultyFilter').val();
                    const cityFilter = $('#enrollCityFilter').val();
                    const statusFilter = $('#enrollStatusFilter').val();
                    
                    // Update PDF parameters
                    let pdfUrl = `export_pdf.php?type=enrollments&search=${encodeURIComponent(searchVal)}&level=${encodeURIComponent(levelFilter)}&faculty=${encodeURIComponent(facultyFilter)}&city=${encodeURIComponent(cityFilter)}&status=${encodeURIComponent(statusFilter)}`;
                    $('#btnExportPDFEnrollments').attr('href', pdfUrl);

                    $('#enrollments-table tbody tr').each(function() {
                        const $row = $(this);
                        if ($row.find('td').length === 1) return; // Skip empty row
                        
                        const text = $row.text().toLowerCase();
                        const rowLevel = $row.data('level');
                        const rowFaculty = $row.data('faculty');
                        const rowCity = $row.data('city');
                        const rowStatus = $row.data('status');
                        
                        const matchesSearch = text.includes(searchVal);
                        const matchesLevel = !levelFilter || rowLevel === levelFilter;
                        const matchesFaculty = !facultyFilter || rowFaculty === facultyFilter;
                        const matchesCity = !cityFilter || rowCity === cityFilter;
                        const matchesStatus = !statusFilter || rowStatus === statusFilter;
                        
                        if (matchesSearch && matchesLevel && matchesFaculty && matchesCity && matchesStatus) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    });
                }
                
                $('#enrollSearch').on('keyup', filterEnrollmentsTable);
                $('.filter-select').on('change', filterEnrollmentsTable);

                // Filter Inquiries Table Live
                function filterInquiriesTable() {
                    const searchVal = $('#inquirySearch').val().toLowerCase();
                    const countryFilter = $('#inquiryCountryFilter').val();
                    const statusFilter = $('#inquiryStatusFilter').val();
                    
                    // Update PDF parameters
                    let pdfUrl = `export_pdf.php?type=inquiries&search=${encodeURIComponent(searchVal)}&country=${encodeURIComponent(countryFilter)}&status=${encodeURIComponent(statusFilter)}`;
                    $('#btnExportPDFInquiries').attr('href', pdfUrl);

                    $('#inquiries-table tbody tr').each(function() {
                        const $row = $(this);
                        if ($row.find('td').length === 1) return; // Skip empty row
                        
                        const text = $row.text().toLowerCase();
                        const rowCountry = $row.data('country');
                        const rowStatus = $row.data('status');
                        
                        const matchesSearch = text.includes(searchVal);
                        const matchesCountry = !countryFilter || rowCountry === countryFilter;
                        const matchesStatus = !statusFilter || rowStatus === statusFilter;
                        
                        if (matchesSearch && matchesCountry && matchesStatus) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    });
                }
                
                $('#inquirySearch').on('keyup', filterInquiriesTable);
                $('#inquiryCountryFilter, #inquiryStatusFilter').on('change', filterInquiriesTable);

                // Checkbox controls: Enrollments
                $('#enrollSelectAll').on('change', function() {
                    $('.enroll-select-checkbox').prop('checked', this.checked).trigger('change');
                });
                
                $(document).on('change', '.enroll-select-checkbox', function() {
                    const selected = $('.enroll-select-checkbox:checked');
                    $('#enrollSelectedCount').text(selected.length);
                    
                    // Populate bulk form IDs
                    const container = $('#enrollBulkCheckboxContainer').empty();
                    selected.each(function() {
                        container.append($('<input type="hidden" name="ids[]">').val(this.value));
                    });
                    
                    if (selected.length > 0) {
                        $('#enrollBulkBar').css('display', 'flex');
                    } else {
                        $('#enrollBulkBar').hide();
                    }
                });

                // Checkbox controls: Inquiries
                $('#inquirySelectAll').on('change', function() {
                    $('.inquiry-select-checkbox').prop('checked', this.checked).trigger('change');
                });
                
                $(document).on('change', '.inquiry-select-checkbox', function() {
                    const selected = $('.inquiry-select-checkbox:checked');
                    $('#inquirySelectedCount').text(selected.length);
                    
                    // Populate bulk form IDs
                    const container = $('#inquiryBulkCheckboxContainer').empty();
                    selected.each(function() {
                        container.append($('<input type="hidden" name="ids[]">').val(this.value));
                    });
                    
                    if (selected.length > 0) {
                        $('#inquiryBulkBar').css('display', 'flex');
                    } else {
                        $('#inquiryBulkBar').hide();
                    }
                });

                // ==========================================
                // SEO SUITE JAVASCRIPT LOGIC
                // ==========================================

                // 1. Activate tab from URL parameter (?tab=seo-pages, ?tab=seo-audit, etc.)
                const urlParams = new URLSearchParams(window.location.search);
                const tabParam = urlParams.get('tab');
                if (tabParam) {
                    const cleanTabId = tabParam.endsWith('-tab') ? tabParam : tabParam + '-tab';
                    switchTab(cleanTabId);
                }

                // 2. SEO Meta Title & Description Live Character Counters & SERP Simulator Sync
                function updateTitleMetrics(input, previewBoxId, counterId, pixelId, progressId) {
                    const text = input.val();
                    const len = text.length;
                    
                    if (counterId) {
                        const $counter = $('#' + counterId);
                        let badgeClass = 'bg-secondary';
                        let optimalText = '';
                        if (len >= 50 && len <= 60) {
                            badgeClass = 'bg-success';
                            optimalText = ' (✓ Optimal)';
                        } else if (len > 0 && len < 50) {
                            badgeClass = 'bg-warning text-dark';
                            optimalText = ' (Short)';
                        } else if (len > 60) {
                            badgeClass = 'bg-danger';
                            optimalText = ' (Too Long)';
                        }
                        $counter.attr('class', 'badge ' + badgeClass + ' small').text(len + '/60 chars' + optimalText);
                    }

                    if (previewBoxId) {
                        const $titleElem = $('#' + previewBoxId + ' .serp-title');
                        if (text.trim().length > 0) {
                            $titleElem.text(text);
                        } else {
                            $titleElem.text('Enter Meta Title...');
                        }
                    }

                    const approxPx = Math.min(600, Math.round(len * 9.2));
                    if (pixelId) {
                        $('#' + pixelId).text(approxPx);
                    }
                    if (progressId) {
                        const percent = Math.min(100, Math.round((len / 60) * 100));
                        const $bar = $('#' + progressId);
                        $bar.css('width', percent + '%');
                        if (len >= 50 && len <= 60) {
                            $bar.attr('class', 'progress-bar bg-success');
                        } else if (len > 60) {
                            $bar.attr('class', 'progress-bar bg-danger');
                        } else {
                            $bar.attr('class', 'progress-bar bg-warning');
                        }
                    }
                }

                function updateDescMetrics(input, previewBoxId, counterId) {
                    const text = input.val();
                    const len = text.length;

                    if (counterId) {
                        const $counter = $('#' + counterId);
                        let badgeClass = 'bg-secondary';
                        let optimalText = '';
                        if (len >= 120 && len <= 160) {
                            badgeClass = 'bg-success';
                            optimalText = ' (✓ Optimal)';
                        } else if (len > 0 && len < 120) {
                            badgeClass = 'bg-warning text-dark';
                            optimalText = ' (Short)';
                        } else if (len > 160) {
                            badgeClass = 'bg-danger';
                            optimalText = ' (Too Long)';
                        }
                        $counter.attr('class', 'badge ' + badgeClass + ' small').text(len + '/160 chars' + optimalText);
                    }

                    if (previewBoxId) {
                        const $descElem = $('#' + previewBoxId + ' .serp-desc');
                        if (text.trim().length > 0) {
                            $descElem.text(text);
                        } else {
                            $descElem.text('Enter Meta Description snippet to simulate live appearance on Google Search Results...');
                        }
                    }
                }

                // Attach events to Add SEO Modal
                $('#addSeoTitleInput').on('input keyup change', function() {
                    updateTitleMetrics($(this), 'addSerpPreview', 'addSeoTitleCount', 'addTitlePixel', 'addTitleProgressBar');
                });

                $('#addSeoDescInput').on('input keyup change', function() {
                    updateDescMetrics($(this), 'addSerpPreview', 'addSeoDescCount');
                });

                $('#addSeoSlug').on('input keyup change', function() {
                    const slugVal = $(this).val().trim() || 'new-page';
                    $('#addSerpSlugPreview').text(slugVal);
                });

                // Attach events to dynamic Edit SEO Modals
                $(document).on('input keyup change', '.seo-title-sync', function() {
                    const $input = $(this);
                    const previewId = $input.data('preview-id');
                    const modalId = $input.closest('.modal').attr('id');
                    const idMatch = modalId ? modalId.replace('editSeoModal', '') : null;
                    if (idMatch) {
                        updateTitleMetrics($input, previewId, 'titleCount_edit_' + idMatch, 'titlePixelWidth_edit_' + idMatch, 'titleProgress_edit_' + idMatch);
                    }
                });

                $(document).on('input keyup change', '.seo-desc-sync', function() {
                    const $input = $(this);
                    const previewId = $input.data('preview-id');
                    const modalId = $input.closest('.modal').attr('id');
                    const idMatch = modalId ? modalId.replace('editSeoModal', '') : null;
                    if (idMatch) {
                        updateDescMetrics($input, previewId, 'descCount_edit_' + idMatch);
                    }
                });

                // Desktop / Mobile SERP Preview Toggle
                $(document).on('click', '.btn-serp-mode', function() {
                    const $btn = $(this);
                    const mode = $btn.data('mode');
                    const targetId = $btn.data('target');
                    const $box = $('#' + targetId);

                    $btn.parent().find('.btn-serp-mode').removeClass('active');
                    $btn.addClass('active');

                    if (mode === 'mobile') {
                        $box.css({
                            'max-width': '380px',
                            'border-radius': '16px',
                            'margin': '0 auto',
                            'box-shadow': '0 4px 16px rgba(0,0,0,0.1)'
                        });
                    } else {
                        $box.css({
                            'max-width': '100%',
                            'border-radius': '8px',
                            'margin': '0',
                            'box-shadow': 'none'
                        });
                    }
                });

                // Live Filter for SEO Pages Table
                function filterSeoPages() {
                    const q = $('#seoSearchInput').val().toLowerCase();
                    const robots = $('#seoRobotsFilter').val();

                    $('#seoPagesTable tbody tr.seo-row').each(function() {
                        const $row = $(this);
                        const text = $row.text().toLowerCase();
                        const rowRobots = $row.data('robots');

                        const matchesQ = text.includes(q);
                        const matchesRobots = !robots || rowRobots === robots;

                        if (matchesQ && matchesRobots) {
                            $row.show();
                        } else {
                            $row.hide();
                        }
                    });
                }

                $('#seoSearchInput').on('keyup input', filterSeoPages);
                $('#seoRobotsFilter').on('change', filterSeoPages);
            });

            // CHART CONFIGURATIONS
            document.addEventListener("DOMContentLoaded", function() {
                // Line Chart - Lead trends
                const ctxLine = document.getElementById('trendsChart').getContext('2d');
                new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($months) ?>,
                        datasets: [
                            {
                                label: 'Student Enrollments',
                                data: <?= json_encode($enrollTrend) ?>,
                                borderColor: '#102e48',
                                backgroundColor: 'rgba(16, 46, 72, 0.05)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3
                            },
                            {
                                label: 'Portal Inquiries',
                                data: <?= json_encode($inqTrend) ?>,
                                borderColor: '#e56920',
                                backgroundColor: 'rgba(229, 105, 32, 0.05)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });

                // Pie Chart - Faculty stream breakdown
                const ctxPie = document.getElementById('facultyChart').getContext('2d');
                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: <?= json_encode($facultyLabels) ?>,
                        datasets: [{
                            data: <?= json_encode($facultyData) ?>,
                            backgroundColor: [
                                '#102e48', '#18446c', '#e56920', '#d25a17', 
                                '#eac818', '#6c757d', '#198754', '#0dcaf0'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    font: { size: 10 }
                                }
                            }
                        }
                    }
                });
            });
        </script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
