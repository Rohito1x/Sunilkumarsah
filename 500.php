<?php
http_response_code(500);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border border-light p-5 rounded-4 shadow-sm bg-white">
                <i class="fa-solid fa-server text-secondary display-1 mb-3"></i>
                <h1 class="fw-bold text-primary display-4">500</h1>
                <h3 class="fw-semibold text-dark mb-3">Server Error</h3>
                <p class="text-muted mb-4">Unable to process your request at this time. Please try again later.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="<?= h($basePath ?? '/') ?>index.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold"><i class="fa-solid fa-house me-2"></i>Return Home</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
