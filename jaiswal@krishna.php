<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/includes/db.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: jaiswal/dashboard.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrfToken = isset($_POST['csrf_token']) ? trim((string)$_POST['csrf_token']) : '';
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $alert = ['type' => 'danger', 'msg' => 'Security check failed. Please refresh the page and try again.'];
    } else {
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

        if ($email === '' || $password === '') {
            $alert = ['type' => 'danger', 'msg' => 'Email and password are required.'];
        } else {
            // Check server-side rate limit (F-06)
            $rateLimit = checkRateLimit($pdo, $email);
            if (!$rateLimit['allowed']) {
                $alert = ['type' => 'danger', 'msg' => $rateLimit['msg']];
            } else {
                try {
                    $stmt = $pdo->prepare('SELECT id, email, password_hash FROM admin_users WHERE email = :email LIMIT 1');
                    $stmt->execute([':email' => $email]);
                    $user = $stmt->fetch();

                    if (!$user || !password_verify($password, $user['password_hash'])) {
                        recordFailedAttempt($pdo, $email);
                        $alert = ['type' => 'danger', 'msg' => 'Invalid email or password.'];
                    } else {
                        // Successful login - reset rate limit counter & regenerate session ID
                        resetRateLimit($pdo, $email);
                        session_regenerate_id(true);

                        $_SESSION['admin_id'] = (int)$user['id'];
                        $_SESSION['admin_email'] = $user['email'];

                        header('Location: jaiswal/dashboard.php');
                        exit;
                    }
                } catch (PDOException $e) {
                    error_log("Login database error: " . $e->getMessage());
                    $alert = ['type' => 'danger', 'msg' => 'An error occurred during authentication. Please try again.'];
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card border border-light p-4 rounded-4 shadow bg-light">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-lock text-primary fs-1 mb-2"></i>
                    <h3 class="fw-bold text-primary">Admin Sign In</h3>
                    <p class="text-muted small">Sign in to manage inquiries and enrollments.</p>
                </div>

                <?php if ($alert): ?>
                    <div class="alert alert-<?= h($alert['type']) ?> py-2 small"><?= h($alert['msg']) ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                    <div class="form-group mb-3">
                        <label>Admin Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" required autocomplete="email">
                    </div>
                    <div class="form-group mb-4">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required autocomplete="current-password">
                    </div>
                    <button class="btn btn-primary w-100 py-2 fw-semibold rounded-pill" type="submit">Sign In</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

