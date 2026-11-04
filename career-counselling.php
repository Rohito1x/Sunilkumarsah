<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is 1-on-1 Career Counselling at Krishna Education Foundation?',
        'a' => 'Our 1-on-1 career counselling is an individualized advisory session where experienced counselors assess your academic strengths (+2 GPA/Marks), personal passions, career goals, and family budget to recommend the best matching courses and universities.'
    ],
    [
        'q' => 'Is career counselling suitable after +2 / Grade 12 results?',
        'a' => 'Yes. The transition after +2 Science, Management, or Humanities is the most crucial decision stage. We help students evaluate Medicine, Nursing, Engineering, IT, Management, and Liberal Arts degrees.'
    ],
    [
        'q' => 'Do parents attend the counselling session?',
        'a' => 'We encourage parents and guardians to participate in counselling sessions so that fee structures, university recognitions, hostel safety, and career outcomes can be discussed transparently.'
    ],
    [
        'q' => 'Is there any fee for initial career counselling?',
        'a' => 'Initial academic counseling and university shortlisting at Krishna Education Foundation are 100% free of charge.'
    ]
];

$pageSeo = getSeoData('static', null, 'career-counselling');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #063964 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Services' => 'services.php', 'Career Counselling' => 'career-counselling.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-compass me-1"></i> Personalized Academic Advisory
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Career Counselling in Nepal</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Discover the right degree path, accredited universities, and emerging career opportunities with expert 1-on-1 career counselling in Biratnagar.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-user-graduate me-2"></i>Book Free 1-on-1 Session
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-location-dot me-2"></i>Visit Biratnagar Office
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-people-arrows text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">100% Free Initial Advisory</h5>
                    <p class="text-white-50 small mb-0">For Students & Parents</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Counselling Stages -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Tailored Advisory</span>
            <h2 class="fw-bold text-primary display-6">Counselling for Every Academic Stage</h2>
            <p class="text-muted small">Personalized guidance based on where you are in your academic journey.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-school fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">After SEE / Grade 10</h3>
                    <p class="text-muted small mb-0">Choosing between +2 Science, Management, Humanities, or 3-year CTEVT technical diplomas based on aptitude.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-graduation-cap fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">After +2 / Intermediate</h3>
                    <p class="text-muted small mb-0">Bachelor degree selection: B.Tech, MBBS, B.Sc Nursing, BBA, BCA, B.Pharm, and Allied Health Sciences in India & abroad.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 text-center">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-briefcase fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">After Bachelor / Master's</h3>
                    <p class="text-muted small mb-0">Postgraduate programs: MBA, M.Tech, M.Sc Nursing, MPH, and global Master's in Australia, UK, USA, and Canada.</p>
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
            <h2 class="fw-bold text-primary display-6">Career Counselling FAQ</h2>
            <p class="text-muted small">Answers to common questions regarding career guidance and scheduling.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionCc">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingCc<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCc<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseCc<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionCc">
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
        <h2 class="fw-bold text-white display-6 mb-3">Speak with a Certified Career Advisor</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Book your free face-to-face consultation at our Biratnagar office or via video call.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Book Consultation
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

