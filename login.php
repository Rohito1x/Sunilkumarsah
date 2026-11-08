<?php
require_once __DIR__ . '/includes/db.php';

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_id'])) {
    header('Location: vision/dashboard.php');
    exit;
}

$alert = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = isset($_POST['csrf_token']) ? trim((string)$_POST['csrf_token']) : ($_POST['_csrf_token'] ?? '');
    $sessionToken = $_SESSION['csrf_token'] ?? ($_SESSION[Csrf::TOKEN_KEY] ?? '');

    $csrfValid = false;
    if (!empty($sessionToken) && !empty($submittedToken) && hash_equals($sessionToken, $submittedToken)) {
        $csrfValid = true;
    }

    if (!$csrfValid) {
        $alert = ['type' => 'danger', 'msg' => 'Security check failed. Please refresh the page and try again.'];
    } else {
        $email = isset($_POST['email']) ? trim((string)$_POST['email']) : '';
        $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

        if ($email === '' || $password === '') {
            $alert = ['type' => 'danger', 'msg' => 'Email and password are required.'];
        } elseif (!isset($pdo) || !$pdo) {
            $alert = ['type' => 'danger', 'msg' => 'Database connection failed. Please check MySQL database user permissions and credentials in cPanel config.php.'];
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
                        // Successful login - reset rate limit counter & regenerate session ID with anti-fixation protection
                        resetRateLimit($pdo, $email);
                        if (class_exists('SessionManager')) {
                            SessionManager::regenerateOnAuth((int)$user['id'], $user['email']);
                        } else {
                            session_regenerate_id(true);
                            $_SESSION['admin_id'] = (int)$user['id'];
                            $_SESSION['admin_email'] = $user['email'];
                        }

                        header('Location: vision/dashboard.php');
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

// Ensure active CSRF token is available for the form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = Csrf::getToken();
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-4">
    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5 col-xl-4">
            <div class="card border-0 p-4 p-md-5 rounded-4 shadow-lg bg-white position-relative overflow-hidden">
                <div class="position-absolute top-0 start-0 w-100 bg-primary" style="height: 5px;"></div>
                
                <div class="text-center mb-4 pt-2">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-3 shadow-sm" style="width: 64px; height: 64px;">
                        <i class="fa-solid fa-shield-halved fs-3"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">Admin Portal</h3>
                    <p class="text-muted small">Sign in to manage inquiries, enrollments, and university data.</p>
                </div>

                <?php if ($alert): ?>
                    <div class="alert alert-<?= h($alert['type']) ?> py-2 px-3 small rounded-3 mb-3 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <div><?= h($alert['msg']) ?></div>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token']) ?>">
                    
                    <div class="form-group mb-3">
                        <label class="form-label fw-semibold text-dark small">Admin Email</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-start-0" placeholder="admin@visionpoint.edu.np" required autocomplete="email" value="<?= isset($_POST['email']) ? h($_POST['email']) : '' ?>">
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="form-label fw-semibold text-dark small">Password</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-key"></i></span>
                            <input type="password" name="password" class="form-control bg-light border-start-0" placeholder="Enter your secure password" required autocomplete="current-password">
                        </div>
                    </div>
                    
                    <button class="btn btn-primary w-100 py-2.5 fw-bold rounded-pill shadow-sm hover-scale mb-3" type="submit">
                        <i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Sign In
                    </button>

                    <div class="text-center">
                        <a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-muted small">
                            <i class="fa-solid fa-house me-1"></i> Return to Homepage
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
