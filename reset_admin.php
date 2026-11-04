<?php
/**
 * Admin Password Reset Tool
 * Upload this file to Hostinger public_html, visit it in the browser once to update your password,
 * then delete it for security.
 */

require_once __DIR__ . '/includes/db.php';

// 👇 SET YOUR DESIRED ADMIN EMAIL & NEW PASSWORD HERE
$adminEmail = 'admin@krishnaeducation.info';
$newPassword = 'adminSecure2026!'; // 👈 Change this to your desired password

$message = '';
$status = '';

if ($pdo) {
    try {
        // Hash the new password securely
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);

        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT id FROM `admin_users` WHERE `email` = ?");
        $stmt->execute([$adminEmail]);
        $user = $stmt->fetch();

        if ($user) {
            // Update existing user
            $updateStmt = $pdo->prepare("UPDATE `admin_users` SET `password_hash` = ? WHERE `email` = ?");
            $updateStmt->execute([$newHash, $adminEmail]);
            $message = "Admin password has been successfully updated!";
            $status = "success";
        } else {
            // Insert user if doesn't exist
            $insertStmt = $pdo->prepare("INSERT INTO `admin_users` (`email`, `password_hash`) VALUES (?, ?)");
            $insertStmt->execute([$adminEmail, $newHash]);
            $message = "Admin user created and password set successfully!";
            $status = "success";
        }
    } catch (PDOException $e) {
        $message = "Database error: " . htmlspecialchars($e->getMessage());
        $status = "danger";
    }
} else {
    $message = "Could not connect to database. Please check your DB credentials in config.php.";
    $status = "danger";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Reset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card shadow-sm p-4 text-center" style="max-width: 500px; width: 90%; border-radius: 16px;">
        <h3 class="mb-3 fw-bold text-primary">Admin Password Reset</h3>
        
        <?php if ($status === 'success'): ?>
            <div class="alert alert-success text-start">
                <p class="mb-1"><strong><?= htmlspecialchars($message) ?></strong></p>
                <hr class="my-2">
                <p class="mb-1"><strong>Email:</strong> <code><?= htmlspecialchars($adminEmail) ?></code></p>
                <p class="mb-0"><strong>Password:</strong> <code><?= htmlspecialchars($newPassword) ?></code></p>
            </div>
            <a href="jaiswal@krishna.php" class="btn btn-primary w-100 py-2 fw-semibold rounded-pill mb-2">Go to Admin Login</a>
            <p class="text-muted small mb-0">⚠️ <em>For security, you can now delete <code>reset_admin.php</code> from Hostinger.</em></p>
        <?php else: ?>
            <div class="alert alert-danger text-start">
                <p class="mb-0"><strong>Error:</strong> <?= htmlspecialchars($message) ?></p>
            </div>
            <a href="reset_admin.php" class="btn btn-outline-secondary w-100 py-2 rounded-pill">Retry</a>
        <?php endif; ?>
    </div>
</body>
</html>
