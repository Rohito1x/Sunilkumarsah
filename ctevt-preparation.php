<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is the CTEVT Entrance Examination in Nepal?',
        'a' => 'The CTEVT entrance exam is conducted by the Council for Technical Education and Vocational Training (CTEVT) for admission into 3-year Diploma/PCL level courses in General Medicine (HA), Nursing (PCL Nursing), Pharmacy, Lab Technology, Civil/Electrical/Computer Engineering, and Agriculture.'
    ],
    [
        'q' => 'What is the minimum eligibility for CTEVT Diploma programs?',
        'a' => 'Students who have passed the Secondary Education Examination (SEE) with minimum Grade C / GPA 2.0 (with compulsory Science, Mathematics, and English) are eligible to apply.'
    ],
    [
        'q' => 'What subjects are tested in the CTEVT entrance exam?',
        'a' => 'The entrance exam consists of 60 multiple-choice questions (MCQs) carrying 60 marks: Compulsory English (20 marks), Compulsory Science (20 marks), and Compulsory Mathematics (20 marks).'
    ],
    [
        'q' => 'Are CTEVT full scholarships (Classified / Merit) available?',
        'a' => 'Yes. CTEVT conducts a dedicated Classified (Free) Scholarship entrance examination where selected meritorious students study with 100% tuition waiver in government and affiliated polytechnic institutes.'
    ]
];

$pageSeo = getSeoData('static', null, 'ctevt-preparation');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #083b63 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Test Preparation' => 'test-preparation.php', 'CTEVT Preparation' => 'ctevt-preparation.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-gears me-1"></i> Technical Education & Vocational Training
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">CTEVT Entrance Preparation</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Achieve top ranks and secure classified scholarships in PCL Nursing, HA, Pharmacy, and Diploma in Engineering with expert CTEVT coaching after SEE in Biratnagar.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Enroll in CTEVT Class
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Take Diagnostic Test
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-certificate text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">HA &bull; PCL Nursing &bull; Engg</h5>
                    <p class="text-white-50 small mb-0">Classified Scholarship Training</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Course Highlights -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Popular Diploma Programs</span>
            <h2 class="fw-bold text-primary display-6">Key CTEVT Streams We Prepare For</h2>
            <p class="text-muted small">Specialized training modules for health sciences and engineering polytechnic branches.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-user-nurse fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">PCL Nursing & HA</h3>
                    <p class="text-muted small mb-0">General Medicine (Health Assistant) and Staff Nursing diploma entrance preparation with past paper sets.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-prescription-bottle-medical fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Diploma in Pharmacy</h3>
                    <p class="text-muted small mb-0">Focused curriculum on fundamental Chemistry, Biology, and Math for high-ranking pharmacy admission.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-microscope fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Lab & Radiography</h3>
                    <p class="text-muted small mb-0">Comprehensive preparation for Medical Lab Technology (CMLT) and Diagnostic Radiography.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-laptop-code fs-4 text-danger"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Diploma Engineering</h3>
                    <p class="text-muted small mb-0">Civil, Computer, Electrical, and Mechanical polytechnic engineering entrance preparation.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section with Schema -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Frequently Asked Questions</span>
            <h2 class="fw-bold text-primary display-6">CTEVT Preparation FAQ</h2>
            <p class="text-muted small">Guidance on exam forms, quotas, classified scholarships, and eligibility.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionCtevt">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingCtevt<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCtevt<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseCtevt<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionCtevt">
                        <div class="accordion-body text-muted small" style="line-height: 1.8;">
                            <?= h($f['a']) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-5 text-center text-white" style="background: #0458A0;">
    <div class="container py-3">
        <h2 class="fw-bold text-white display-6 mb-3">Begin Your CTEVT Preparation Journey with KEF</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Classes start immediately following the SEE board examinations.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Register for CTEVT Coaching
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

