<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is a CAS (Confirmation of Acceptance for Studies) letter for the UK?',
        'a' => 'A CAS letter is a unique electronic reference number issued by a UK Home Office licensed university sponsor confirming your unconditional admission offer and tuition payment details. It is mandatory for your Student Visa application.'
    ],
    [
        'q' => 'Can Nepali students study in the UK without IELTS?',
        'a' => 'Yes, select UK universities offer IELTS waivers if you have scored 70%+ in English in your +2 examination (NEB/HSEB) or if you complete an internal university English language interview/assessment.'
    ],
    [
        'q' => 'What is the UK 2-Year Graduate Route (Post-Study Work Visa)?',
        'a' => 'The Graduate Route enables international students completing an undergraduate (Bachelor\'s) or postgraduate (Master\'s) degree from an eligible UK higher education provider to work or look for work in the UK for 2 years (3 years for doctoral graduates) without needing a sponsor.'
    ],
    [
        'q' => 'What are the main university intakes in the United Kingdom?',
        'a' => 'The major intake is September/October (Autumn intake), followed by a secondary January/February (Spring intake) and select May/June programs.'
    ],
    [
        'q' => 'What are the financial maintenance fund requirements for UK Student Visas?',
        'a' => 'Students must hold 28 days of living expense funds in an approved bank: £1,334 per month (up to 9 months, £12,006) for London institutions, or £1,023 per month (up to 9 months, £9,207) for universities outside London.'
    ]
];

$pageSeo = getSeoData('static', null, 'study-in-uk');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #152438 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Study Abroad' => 'study-abroad.php', 'Study in UK' => 'study-in-uk.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-landmark-dome me-1"></i> Russell Group & Top UK Institutions
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Study in the UK for Nepali Students</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Experience world-renowned academic heritage with 1-year Master's programs, 2-year Graduate Route work visas, and generous merit scholarships across London, Manchester, Birmingham, and Edinburgh.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="UK">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Apply for UK Admission
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-file-lines me-2"></i>Get Free CAS Assessment
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-clock-rotate-left text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">Fast-Track Degrees</h5>
                    <p class="text-white-50 small mb-0">3-Year Bachelor's &bull; 1-Year Master's Programs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- UK Highlights Grid -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-bolt text-warning fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">1-Year Master's</h4>
                    <p class="text-muted extra-small mb-0">Save time and tuition expenses with 1-year degrees</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-briefcase text-success fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Graduate Route</h4>
                    <p class="text-muted extra-small mb-0">2-year post-study work visa without job sponsor</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-certificate text-primary fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">IELTS Waiver</h4>
                    <p class="text-muted extra-small mb-0">Available for eligible +2 English scores (70%+)</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-clock text-danger fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Work While Studying</h4>
                    <p class="text-muted extra-small mb-0">20 hours per week during academic sessions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Overview & Popular Streams -->
<section class="py-5">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">UK Education Advantage</span>
                <h2 class="fw-bold text-primary display-6 mb-3">Prestigious Qualifications Recognized Worldwide</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    The UK higher education system is renowned for academic rigor, innovative research, and strong industry linkages. Shorter degree durations allow Nepalese graduates to enter the global workforce one year earlier than in other nations.
                </p>
                <div class="p-3 rounded-3 bg-light border-start border-primary border-4 mb-3">
                    <h4 class="fw-bold text-dark fs-6 mb-1">CAS (Confirmation of Acceptance) Support</h4>
                    <p class="text-muted small mb-0">Our advisors review all financial statements, TB certificates, and credibility interview guidelines to guarantee smooth CAS issuance.</p>
                </div>
                <div class="p-3 rounded-3 bg-light border-start border-warning border-4">
                    <h4 class="fw-bold text-dark fs-6 mb-1">Vice-Chancellor Merit Scholarships</h4>
                    <p class="text-muted small mb-0">Guidance on applying for early bird awards and merit discounts of £1,500 to £5,000 off tuition fees.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 border shadow-sm">
                    <h3 class="fw-bold text-primary fs-5 mb-3"><i class="fa-solid fa-graduation-cap me-2 text-primary"></i>Top In-Demand Courses in the UK</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-laptop-code text-primary me-2"></i> Data Science, AI & Software Engineering</span>
                            <span class="badge bg-primary-subtle text-primary">High PSW Hire</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-briefcase text-success me-2"></i> MBA, International Business & Marketing</span>
                            <span class="badge bg-success-subtle text-success">AMBA Accredited</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-scale-balanced text-warning me-2"></i> LLM International Law & Corporate Governance</span>
                            <span class="badge bg-warning-subtle text-dark">Global Scope</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-heart-pulse text-danger me-2"></i> Public Health & Healthcare Management</span>
                            <span class="badge bg-danger-subtle text-danger">NHS Careers</span>
                        </li>
                        <li class="p-2 d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-money-bill-trend-up text-secondary me-2"></i> Finance, Fintech & Banking</span>
                            <span class="badge bg-secondary-subtle text-secondary">London City</span>
                        </li>
                    </ul>
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
            <h2 class="fw-bold text-primary display-6">United Kingdom Admissions FAQ</h2>
            <p class="text-muted small">Important facts on CAS letters, financial requirements, and visa compliance.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionUk">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingUk<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUk<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseUk<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionUk">
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
        <h2 class="fw-bold text-white display-6 mb-3">Begin Your UK Academic Journey with Vision Point</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Book a consultation to evaluate your transcripts and short-list top UK universities for upcoming intakes.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="UK">
            <i class="fa-solid fa-paper-plane me-2"></i>Apply for UK Counseling
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

