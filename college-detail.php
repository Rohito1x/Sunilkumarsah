<?php
require_once __DIR__ . '/includes/db.php';

$uni = null;
$pdfs = [];
$rawId = $_GET['id'] ?? null;

if ($rawId !== null) {
    if (!ctype_digit((string)$rawId) || (int)$rawId <= 0) {
        http_response_code(404);
        require_once __DIR__ . '/404.php';
        exit;
    }
    $id = (int)$rawId;
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM institutions WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            $uni = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmtP = $pdo->prepare("SELECT * FROM institution_pdfs WHERE institution_id = ? AND is_active = 1 ORDER BY display_order ASC, title ASC");
            $stmtP->execute([$id]);
            $pdfs = $stmtP->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("College detail fetch DB error: " . $e->getMessage());
        }
    }
}

if (!$uni) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

require_once __DIR__ . '/includes/header.php';


// Fetch course streams offered by this university
$offeredStreams = [];
if (!empty($uni['courses']) && isset($pdo)) {
    try {
        $courseIds = explode(',', $uni['courses']);
        // Sanitize IDs to be integers
        $courseIds = array_map('intval', $courseIds);
        if (!empty($courseIds)) {
            $inClause = implode(',', $courseIds);
            $stmtC = $pdo->query("SELECT * FROM course_streams WHERE id IN ($inClause) ORDER BY display_order ASC");
            $offeredStreams = $stmtC->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        // Fallback
    }
}

$uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
?>

<!-- Breadcrumb Section -->
<section class="breadcrumb-section py-3" style="background-color: #f8f9fa;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 fw-medium small">
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-muted"><i class="fa fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>cities.php" class="text-decoration-none text-muted">Institutions</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page"><?= h($uni['name']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- College Hero Banner -->
<section class="position-relative text-white py-5 d-flex align-items-center" style="background: linear-gradient(135deg, #102e48 0%, #18446c 100%); min-height: 250px;">
    <div class="container position-relative z-3">
        <div class="row align-items-center g-4">
            <div class="col-12 col-md-3 text-center text-md-start">
                <div class="bg-white p-3 rounded-4 shadow-sm d-inline-block" style="width: 150px; height: 150px; overflow: hidden;">
                    <img src="<?= h($uniImg) ?>" alt="<?= h($uni['name']) ?>" class="w-100 h-100 object-fit-contain">
                </div>
            </div>
            <div class="col-12 col-md-9 text-center text-md-start">
                <h1 class="fw-extrabold text-white mb-3" style="font-size: 2.25rem;"><?= h($uni['name']) ?></h1>
                
                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-3 mt-2">
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2"><i class="fa-solid fa-map-marker text-warning me-1"></i><?= h($uni['city']) ?> (<?= h($uni['region']) ?>)</span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-2"><i class="fa-solid fa-building text-info me-1"></i>Partner Institution</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Details Layout -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row g-5">
            <!-- Left Details Column -->
            <div class="col-lg-8">
                <!-- About Card -->
                <div class="mb-5">
                    <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-university text-warning me-2"></i>About <?= h($uni['name']) ?></h3>
                    <?php if (!empty($uni['about'])): ?>
                        <div class="text-muted" style="line-height: 1.8; white-space: pre-line;">
                            <?= $uni['about'] ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted" style="line-height: 1.8;">
                            <?= h($uni['name']) ?>, located in <?= h($uni['city']) ?>, <?= h($uni['region']) ?>, is a well-regarded partner institution. It offers premium academic environments, high-quality learning materials, and modern laboratory systems to empower students in their professional education.
                        </p>
                        <p class="text-muted" style="line-height: 1.8;">
                            For international and Nepalese students, <?= h($uni['name']) ?> provides direct admission pathways and merit scholarship programs based on academic records. Explore specific courses and fee details using the sections below.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Fees Section -->
                <div class="mb-5 p-4 bg-light rounded-4 border border-light-subtle">
                    <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-indian-rupee-sign text-warning me-2"></i>Fees & Scholarships</h3>
                    <?php if (!empty($uni['fees'])): ?>
                        <div class="text-muted mb-3" style="line-height: 1.8; white-space: pre-line;">
                            <?= $uni['fees'] ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-3">Fees vary by stream, level (Undergraduate/Postgraduate), and academic year. Merit-based scholarships of up to 50%-100% tuition waivers are available for Nepalese students with strong +2 grades. Contact our counselors for a detailed cost layout.</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($uni['fee_document'])): ?>
                        <div class="mt-3 pt-3 border-top border-light-subtle">
                            <a href="<?= h($basePath . $uni['fee_document']) ?>" target="_blank" class="btn btn-secondary rounded-pill py-2.5 px-4 fw-bold text-white d-inline-flex align-items-center gap-2">
                                <i class="fa-solid fa-file-pdf"></i>
                                <span>Download Fee Structure (PDF)</span>
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pdfs)): ?>
                        <div class="mt-4 pt-4 border-top border-light-subtle">
                            <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-folder-open text-secondary me-2"></i>Official University Documents & Resources</h5>
                            <div class="row g-3">
                                <?php foreach ($pdfs as $pdf): ?>
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 bg-white border border-light rounded-3 shadow-sm h-100 d-flex align-items-start gap-3">
                                            <div class="text-danger mt-1">
                                                <i class="fa-solid fa-file-pdf fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="fw-bold text-primary mb-1" style="font-size: 14px;"><?= h($pdf['title']) ?></h6>
                                                <?php if (!empty($pdf['description'])): ?>
                                                    <p class="text-muted mb-2 extra-small" style="font-size: 11px;"><?= h($pdf['description']) ?></p>
                                                <?php endif; ?>
                                                <a href="<?= h($basePath . $pdf['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill py-1 px-3 fw-semibold d-inline-flex align-items-center gap-1.5" style="font-size: 11px;">
                                                    <i class="fa-solid fa-download"></i> View / Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Courses Offered -->
                <div class="mb-5">
                    <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-book-open text-warning me-2"></i>Offered Courses & Streams</h3>
                    <p class="text-muted mb-4">The university offers specialized programs under the following academic streams:</p>
                    
                    <?php if (!empty($offeredStreams)): ?>
                        <div class="row g-3">
                            <?php foreach ($offeredStreams as $stream): ?>
                                <div class="col-6 col-md-4">
                                    <a href="<?= h($basePath) ?>courses.php" class="text-decoration-none">
                                        <div class="card h-100 border border-light p-3 rounded-3 shadow-sm text-center course-card">
                                            <div class="bg-light text-primary rounded-circle p-2 d-flex align-items-center justify-content-center mx-auto mb-2 course-card-img" style="width: 50px; height: 50px;">
                                                <i class="fa-solid <?= h($stream['icon'] ?? 'fa-graduation-cap') ?> fs-5"></i>
                                            </div>
                                            <h6 class="fw-bold text-primary mb-0 small text-truncate w-100"><?= h($stream['name']) ?></h6>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-light border border-light-subtle text-muted py-3 px-4">
                            Details about offered programs will be updated soon. Please consult our counseling team.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Admission Process -->
                <div class="mb-4">
                    <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-circle-check text-warning me-2"></i>Admission Process</h3>
                    <?php if (!empty($uni['admission_process'])): ?>
                        <div class="text-muted" style="line-height: 1.8; white-space: pre-line;">
                            <?= $uni['admission_process'] ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted mb-0">Admission process typically includes a document validation check followed by counseling and seat allocation. For direct admission with scholarship waivers, submit your SEE and +2 transcript documents through our enrollment form.</p>
                    <?php endif; ?>
                </div>

                <!-- Location Map -->
                <?php if (!empty($uni['map_link'])): ?>
                    <div class="mb-4 border-top pt-4">
                        <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-map-location-dot text-warning me-2"></i>Location Map</h3>
                        <div class="rounded-4 overflow-hidden shadow-sm border border-light-subtle" style="height: 350px;">
                            <?php if (strpos($uni['map_link'], '<iframe') !== false): ?>
                                <style>
                                    .map-container-iframe iframe {
                                        width: 100% !important;
                                        height: 100% !important;
                                        border: 0 !important;
                                    }
                                </style>
                                <div class="map-container-iframe w-100 h-100">
                                    <?= $uni['map_link'] ?>
                                </div>
                            <?php else: ?>
                                <iframe src="https://maps.google.com/maps?q=<?= urlencode($uni['map_link']) ?>&t=&z=14&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar Column -->
            <div class="col-lg-4">
                <!-- Apply / Counsel CTA card -->
                <div class="card border border-light rounded-4 shadow-sm p-4 text-center bg-light mb-4 sticky-top" style="top: 100px; z-index: 10;">
                    <h4 class="fw-bold text-primary mb-2">Get Counselled Today</h4>
                    <p class="text-muted small mb-4">Expert Admission & Scholarship Guidance for <?= h($uni['name']) ?></p>
                    
                    <button class="btn btn-secondary w-100 py-3 fw-bold rounded-pill mb-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-college="<?= h($uni['name']) ?>">
                        <i class="fa-solid fa-paper-plane me-2"></i>Apply for Admission
                    </button>
                    
                    <hr class="my-3">
                    
                    <div class="text-center">
                        <span class="text-muted small">OR call counselor at</span>
                        <div class="fw-bold text-primary mt-1"><?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?></div>
                    </div>
                </div>

                <!-- Have Any Question widget -->
                <div class="card border border-light rounded-4 shadow-sm p-4 bg-primary text-white mb-4">
                    <h5 class="fw-bold mb-3 text-white">Have Any Question?</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-phone text-warning mt-1"></i>
                            <div>
                                <div class="small text-white-50">Call Counselor</div>
                                <a href="tel:<?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?>" class="text-white fw-bold text-decoration-none"><?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?></a>
                            </div>
                        </li>
                        <li class="mb-3 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-envelope text-warning mt-1"></i>
                            <div>
                                <div class="small text-white-50">Email Support</div>
                                <a href="mailto:<?= htmlspecialchars($siteSettings['contact_email'] ?? 'info@krishnaeducation.com.np') ?>" class="text-white fw-bold text-decoration-none"><?= htmlspecialchars($siteSettings['contact_email'] ?? 'info@krishnaeducation.com.np') ?></a>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-map-marker text-warning mt-1"></i>
                            <div>
                                <div class="small text-white-50">Our Office</div>
                                <span class="small text-white-50"><?= htmlspecialchars($siteSettings['contact_location'] ?? 'Biratnagar, Nepal') ?></span>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
