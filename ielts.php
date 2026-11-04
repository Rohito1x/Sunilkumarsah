<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is the difference between IELTS Academic and General Training?',
        'a' => 'IELTS Academic is required for higher education admissions at universities in Australia, UK, USA, and Canada. IELTS General Training is primarily used for migration, work permits, and vocational training.'
    ],
    [
        'q' => 'How long is the IELTS test result valid?',
        'a' => 'IELTS Test Report Forms (TRF) are valid for 2 years from the examination test date.'
    ],
    [
        'q' => 'What are the 4 test components in IELTS?',
        'a' => 'Listening (30 mins, 4 sections, 40 questions), Reading (60 mins, 3 texts, 40 questions), Writing (60 mins, Task 1 report/letter & Task 2 essay), and Speaking (11–14 mins face-to-face interview).'
    ],
    [
        'q' => 'What target band score is needed for undergraduate and postgraduate studies?',
        'a' => 'Most Bachelor programs require an overall 6.0 (no band less than 5.5). Most Master programs require an overall 6.5 (no band less than 6.0), with healthcare courses requiring 7.0+.'
    ]
];

$pageSeo = getSeoData('static', null, 'ielts');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #032b50 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Test Preparation' => 'test-preparation.php', 'IELTS Preparation' => 'ielts.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-language me-1"></i> International English Language Testing System
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">IELTS Preparation in Nepal</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Master all 4 IELTS modules — Listening, Reading, Writing, and Speaking — with experienced certified trainers, weekly mock testing, and individualized feedback in Biratnagar.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-user-plus me-2"></i>Enroll for IELTS Class
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Book Free Mock Test
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <div class="display-3 fw-bold text-warning mb-1">7.5+</div>
                    <h5 class="fw-bold text-white mb-1">Average Target Score</h5>
                    <p class="text-white-50 small mb-0">Proven Band Improvement Strategies</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Module Breakdown -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Exam Structure</span>
            <h2 class="fw-bold text-primary display-6">IELTS 4-Section Comprehensive Breakdown</h2>
            <p class="text-muted small">Understanding the format and time management strategy for each module.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-headphones fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Listening</h3>
                    <div class="badge bg-light text-dark mb-3">30 Mins &bull; 40 Questions</div>
                    <p class="text-muted small mb-0">Monologues and conversations covering academic and daily social contexts. Practice with native British, Australian, and North American accents.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-book-open fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Reading</h3>
                    <div class="badge bg-light text-dark mb-3">60 Mins &bull; 3 Long Passages</div>
                    <p class="text-muted small mb-0">Skimming, scanning, True/False/Not Given, paragraph matching, and summary completion techniques from authentic academic journals.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-pen-nib fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Writing</h3>
                    <div class="badge bg-light text-dark mb-3">60 Mins &bull; 2 Tasks</div>
                    <p class="text-muted small mb-0">Task 1 visual data report (150 words) and Task 2 structured formal argumentative essay (250 words) adhering to band descriptors.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-comments fs-4 text-danger"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Speaking</h3>
                    <div class="badge bg-light text-dark mb-3">11–14 Mins &bull; 3 Parts</div>
                    <p class="text-muted small mb-0">Introduction, 2-minute cue card monologue, and two-way deep discussion focusing on fluency, lexical resource, and pronunciation.</p>
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
            <h2 class="fw-bold text-primary display-6">IELTS Preparation FAQ</h2>
            <p class="text-muted small">Everything you need to know about test dates, scoring, and class schedules.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionIelts">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingIelts<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseIelts<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseIelts<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionIelts">
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
        <h2 class="fw-bold text-white display-6 mb-3">Join Our Next IELTS Batch in Biratnagar</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Morning, afternoon, and evening batches with comprehensive study materials included.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Register for IELTS Class
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

