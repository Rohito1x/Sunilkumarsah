<?php
http_response_code(404);
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = [
    'meta_title' => '404 - Page Not Found | Vision Point Educational Service',
    'meta_description' => 'The page you requested could not be found. Explore our study in India guides, universities, scholarships, and career counseling.',
    'robots' => 'noindex, follow'
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5 my-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-7">
            <div class="card border-0 p-5 rounded-4 shadow bg-white">
                <div class="mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase">Error 404</span>
                </div>
                <h1 class="fw-bold text-primary display-4 mb-2">Page Not Found</h1>
                <p class="text-muted mb-4" style="line-height: 1.7;">
                    The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
                </p>

                <!-- Search form -->
                <form action="<?= h($basePath) ?>cities.php" method="GET" class="d-flex gap-2 max-w-500 mx-auto mb-4">
                    <input type="text" name="search" class="form-control rounded-pill px-4" placeholder="Search courses, universities, cities...">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold text-white"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>

                <h6 class="fw-bold text-dark mb-3">Popular Helpful Links</h6>
                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                    <a href="<?= h($basePath) ?>study-in-india.php" class="btn btn-sm btn-outline-primary rounded-pill">Study in India</a>
                    <a href="<?= h($basePath) ?>scholarships.php" class="btn btn-sm btn-outline-primary rounded-pill">Scholarships</a>
                    <a href="<?= h($basePath) ?>test-preparation.php" class="btn btn-sm btn-outline-primary rounded-pill">IELTS & PTE</a>
                    <a href="<?= h($basePath) ?>guides.php" class="btn btn-sm btn-outline-primary rounded-pill">Blog & Guides</a>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-sm btn-outline-primary rounded-pill">Contact Us</a>
                </div>

                <div>
                    <a href="<?= h($basePath) ?>index.php" class="btn btn-primary px-4 py-2 rounded-pill fw-semibold shadow-sm">
                        <i class="fa-solid fa-house me-2"></i>Return to Homepage
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

