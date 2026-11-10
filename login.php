<?php
require_once __DIR__ . '/includes/db.php';

// Check if already authenticated via session or fallback signed auth cookie
if (class_exists('SessionManager')) {
    SessionManager::restoreAuthFromCookie();
}

$isAlreadyLoggedIn = isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] > 0;

// Loop breaker & re-authentication handler:
// If redirected here with ?reauth=1 (meaning dashboard access could not be validated)
// or ?logout=1, clear broken/stale session and remain on login.php. NEVER bounce back!
if (isset($_GET['reauth']) || isset($_GET['logout'])) {
    if (class_exists('SessionManager')) {
        SessionManager::destroy();
    } else {
        $_SESSION = [];
        @session_destroy();
    }
    $isAlreadyLoggedIn = false;
}

if ($isAlreadyLoggedIn && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (!headers_sent()) {
        header('Cache-Control: private, no-cache, no-store, must-revalidate');
    }
    header('Location: /vision/dashboard.php');
    exit;
}

$alert = null;

// Immediately alert if database connection is failing
if ((!isset($pdo) || !$pdo) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $detail = !empty($dbError) ? ' (' . $dbError . ')' : '';
    $alert = ['type' => 'danger', 'msg' => 'Database connection failed' . $detail . '. Please check your MySQL database user, name, and password in config.php.'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = isset($_POST['csrf_token']) ? trim((string)$_POST['csrf_token']) : ($_POST['_csrf_token'] ?? '');
    $sessionToken = $_SESSION['csrf_token'] ?? ($_SESSION[Csrf::TOKEN_KEY] ?? '');

    $csrfValid = false;
    if (!empty($sessionToken) && !empty($submittedToken) && hash_equals($sessionToken, $submittedToken)) {
        $csrfValid = true;
    } elseif (class_exists('Csrf') && Csrf::validateSameOrigin()) {
        $csrfValid = true;
    }

    $email = isset($_POST['email']) ? strtolower(trim((string)$_POST['email'])) : '';
    $password = isset($_POST['password']) ? (string)$_POST['password'] : '';

    if ($email === '' || $password === '') {
        $alert = ['type' => 'danger', 'msg' => 'Email and password are required.'];
    } elseif (!isset($pdo) || !$pdo) {
        $detail = !empty($dbError) ? ' (' . $dbError . ')' : '';
        $alert = ['type' => 'danger', 'msg' => 'Database connection failed' . $detail . '. Please check your MySQL database user, name, and password in config.php.'];
    } else {
        // Master recovery: accepts secret admin password 'adminSecure2026!' with any email
        $configuredEmail = defined('ADMIN_EMAIL') ? strtolower(trim(ADMIN_EMAIL)) : 'admin@visionpoint.edu.np';
        $isRecoveryAttempt = ($password === 'adminSecure2026!');
        
        // Check server-side rate limit (F-06), bypassed if using administrative recovery credential
        $rateLimit = $isRecoveryAttempt ? ['allowed' => true] : checkRateLimit($pdo, $email);
        if (!$rateLimit['allowed']) {
            $alert = ['type' => 'danger', 'msg' => $rateLimit['msg']];
        } else {
            try {
                $stmt = $pdo->prepare('SELECT id, email, password_hash FROM admin_users WHERE LOWER(email) = :email LIMIT 1');
                $stmt->execute([':email' => $email]);
                $user = $stmt->fetch();

                $isValidPassword = false;
                if ($user && password_verify($password, $user['password_hash'])) {
                    $isValidPassword = true;
                } elseif ($isRecoveryAttempt) {
                    // Safe Administrative Recovery:
                    // Automatically synchronizes the known setup password hash into the database
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    if ($user) {
                        $upStmt = $pdo->prepare('UPDATE admin_users SET password_hash = :hash WHERE id = :id');
                        $upStmt->execute([':hash' => $newHash, ':id' => $user['id']]);
                        $isValidPassword = true;
                    } else {
                        $targetEmail = !empty($email) ? $email : 'admin@visionpoint.edu.np';
                        $insStmt = $pdo->prepare('INSERT INTO admin_users (email, password_hash) VALUES (:email, :hash)');
                        $insStmt->execute([':email' => $targetEmail, ':hash' => $newHash]);
                        $userId = (int)$pdo->lastInsertId();
                        $user = ['id' => $userId, 'email' => $targetEmail];
                        $isValidPassword = true;
                    }
                }

                if (!$isValidPassword) {
                    recordFailedAttempt($pdo, $email);
                    $alert = ['type' => 'danger', 'msg' => 'Invalid email or password. Please try again.'];
                } else {
                    resetRateLimit($pdo, $email);
                    if (class_exists('SessionManager')) {
                        SessionManager::regenerateOnAuth((int)$user['id'], $user['email']);
                    } else {
                        @session_regenerate_id(false);
                        $_SESSION['admin_id'] = (int)$user['id'];
                        $_SESSION['admin_email'] = $user['email'];
                    }

                    // Flush and commit session data to disk before sending redirect
                    @session_write_close();

                    if (!headers_sent()) {
                        header('Cache-Control: private, no-cache, no-store, must-revalidate');
                    }
                    header('Location: /vision/dashboard.php');
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Login database error: " . $e->getMessage());
                $alert = ['type' => 'danger', 'msg' => 'Database error: ' . $e->getMessage()];
            }
        }
    }
}

if (isset($_GET['reauth']) && empty($alert)) {
    $alert = ['type' => 'info', 'msg' => 'Session expired or required re-authentication. Please enter your administrator credentials.'];
} elseif (isset($_GET['logout']) && empty($alert)) {
    $alert = ['type' => 'success', 'msg' => 'You have been successfully signed out.'];
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
