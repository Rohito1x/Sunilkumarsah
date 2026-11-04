<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is the Common Entrance Examination (CEE) in Nepal?',
        'a' => 'The Common Entrance Examination (CEE) is the centralized medical and health sciences entrance examination conducted annually by the Medical Education Commission (MEC) of Nepal for admission into MBBS, BDS, B.Sc Nursing, B.Pharm, BPT, and other undergraduate health science programs.'
    ],
    [
        'q' => 'What is the examination pattern and marking scheme of CEE?',
        'a' => 'CEE consists of 200 multiple-choice questions (MCQs) carrying 200 marks with a duration of 3 hours. Negative marking of 0.25 marks is applied for every incorrect answer.'
    ],
    [
        'q' => 'What subjects are covered in CEE for MBBS/BDS?',
        'a' => 'Physics (50 marks), Chemistry (50 marks), Zoology (40 marks), Botany (40 marks), and Mental Agility Test / MAT (20 marks).'
    ],
    [
        'q' => 'Is CEE eligibility required for Nepali students who want to study MBBS abroad/India?',
        'a' => 'Yes. Under MEC regulations, Nepali citizens seeking admission to undergraduate medical programs (MBBS/BDS) in India or foreign countries must qualify in the MEC CEE examination to obtain the Eligibility Certificate required for licensing by the Nepal Medical Council (NMC).'
    ]
];

$pageSeo = getSeoData('static', null, 'cee-preparation');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #0d2843 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Test Preparation' => 'test-preparation.php', 'CEE Preparation' => 'cee-preparation.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-stethoscope me-1"></i> Medical Education Commission (MEC) Nepal
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">CEE Medical Entrance Preparation</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Crack the MEC Common Entrance Examination (CEE) for MBBS, BDS, B.Sc Nursing, and Allied Health Sciences with top medical faculty, question bank drills, and OMR mock tests in Biratnagar.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-user-doctor me-2"></i>Join CEE Crash Batch
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-file-lines me-2"></i>Download CEE Syllabus
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-heart-pulse text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">200 MCQs &bull; 3 Hours</h5>
                    <p class="text-white-50 small mb-0">Physics &bull; Chemistry &bull; Biology &bull; MAT</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Subject Weightage -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Syllabus Structure</span>
            <h2 class="fw-bold text-primary display-6">CEE 200-Mark Subject Distribution</h2>
            <p class="text-muted small">Strictly aligned with the Medical Education Commission (MEC) undergraduate guidelines.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <h3 class="fw-bold text-primary display-6 mb-1">80</h3>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Biology (Botany + Zoology)</h4>
                    <p class="text-muted extra-small mb-0">40% of total score: Cell biology, Genetics, Human Physiology, Ecology</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <h3 class="fw-bold text-primary display-6 mb-1">50</h3>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Chemistry</h4>
                    <p class="text-muted extra-small mb-0">25% of total score: Physical, Organic, and Inorganic Chemistry</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <h3 class="fw-bold text-primary display-6 mb-1">50</h3>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Physics</h4>
                    <p class="text-muted extra-small mb-0">25% of total score: Mechanics, Optics, Electromagnetism, Modern Physics</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <h3 class="fw-bold text-warning display-6 mb-1">20</h3>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Mental Agility (MAT)</h4>
                    <p class="text-muted extra-small mb-0">10% of total score: Verbal, Numerical, and Abstract Reasoning</p>
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
            <h2 class="fw-bold text-primary display-6">CEE Medical Entrance FAQ</h2>
            <p class="text-muted small">Learn about eligibility, MEC registration, and foreign admission equivalency.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionCee">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingCee<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCee<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseCee<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionCee">
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
        <h2 class="fw-bold text-white display-6 mb-3">Reserve Your Seat for CEE Intensive Coaching</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Limited seats per batch to ensure individual mentoring and problem-solving sessions.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Apply for CEE Classes
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

