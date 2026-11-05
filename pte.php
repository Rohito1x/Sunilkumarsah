<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is PTE Academic?',
        'a' => 'PTE Academic (Pearson Test of English) is a 2-hour, 100% computer-based and AI-scored English proficiency test accepted for study visas by 100% of universities in Australia, New Zealand, and extensive institutions in the UK, USA, and Canada.'
    ],
    [
        'q' => 'How quickly are PTE results released?',
        'a' => 'PTE results are typically released within 24 to 48 hours of completing the test, making it ideal for tight admission deadlines.'
    ],
    [
        'q' => 'What are the main sections in the PTE Academic test?',
        'a' => 'The test consists of 3 parts: Part 1: Speaking & Writing (54–67 mins), Part 2: Reading (29–30 mins), and Part 3: Listening (30–43 mins).'
    ],
    [
        'q' => 'What score is equivalent to IELTS 6.5 or 7.0 in PTE?',
        'a' => 'A PTE Academic score of 58 is roughly equivalent to IELTS 6.5, and a PTE score of 65 is equivalent to IELTS 7.0.'
    ]
];

$pageSeo = getSeoData('static', null, 'pte');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #1a3249 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Test Preparation' => 'test-preparation.php', 'PTE Academic' => 'pte.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-laptop me-1"></i> Pearson Test of English Academic
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">PTE Academic Preparation in Nepal</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Achieve your target score (65+ / 79+) with AI-based mock tests, proven templates for Read Aloud, Repeat Sentence, Summarize Written Text, and Write from Dictation in Biratnagar.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-user-plus me-2"></i>Enroll for PTE Class
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-desktop me-2"></i>Take PTE Lab Test
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <div class="display-3 fw-bold text-warning mb-1">48 Hrs</div>
                    <h5 class="fw-bold text-white mb-1">Fast Result Delivery</h5>
                    <p class="text-white-50 small mb-0">100% Computer & AI Scored</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Section Breakdown -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Test Modules</span>
            <h2 class="fw-bold text-primary display-6">PTE 3-Part Test Structure</h2>
            <p class="text-muted small">Targeted training for high-weightage question types across all 3 parts.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-microphone fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Speaking & Writing</h3>
                    <div class="badge bg-light text-dark mb-3">54–67 Mins</div>
                    <p class="text-muted small mb-0">Read Aloud, Repeat Sentence, Describe Image, Re-tell Lecture, Answer Short Questions, Summarize Written Text, and Essay Writing.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-book-open-reader fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Reading</h3>
                    <div class="badge bg-light text-dark mb-3">29–30 Mins</div>
                    <p class="text-muted small mb-0">Reading & Writing Fill in the Blanks, Multiple Choice (Single/Multiple), Re-order Paragraphs, and Reading Fill in the Blanks.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-headphones-simple fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Listening</h3>
                    <div class="badge bg-light text-dark mb-3">30–43 Mins</div>
                    <p class="text-muted small mb-0">Summarize Spoken Text, Multiple Choice, Fill in the Blanks, Highlight Correct Summary, Select Missing Word, and Write from Dictation.</p>
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
            <h2 class="fw-bold text-primary display-6">PTE Academic FAQ</h2>
            <p class="text-muted small">Answers to common inquiries regarding PTE exam scoring, validity, and registration.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionPte">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingPte<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePte<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapsePte<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionPte">
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
        <h2 class="fw-bold text-white display-6 mb-3">Practice on High-Tech PTE Software at Vision Point Biratnagar</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Dedicated computer lab with individual headsets and real-time AI scoring diagnostics.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Register for PTE Class
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

