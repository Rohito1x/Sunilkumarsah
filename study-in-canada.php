<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is a Designated Learning Institution (DLI) in Canada?',
        'a' => 'A Designated Learning Institution (DLI) is a Canadian university or college approved by provincial or territorial governments to host international students. Only students with an acceptance letter from a DLI can apply for a Canadian study permit.'
    ],
    [
        'q' => 'What are the rules for the Post-Graduation Work Permit (PGWP) in Canada?',
        'a' => 'Graduates from eligible full-time DLI programs of at least 8 months can receive a PGWP. Programs of 2 years or more typically qualify for an open 3-year PGWP, allowing graduates to gain valuable Canadian work experience.'
    ],
    [
        'q' => 'What is the Guaranteed Investment Certificate (GIC) requirement?',
        'a' => 'International students applying under the Student Direct Stream (SDS) or regular study permit streams must purchase a GIC from a designated Canadian bank (currently CAD 20,635) to prove sufficient living expenses for their first year.'
    ],
    [
        'q' => 'What are the English test score requirements for Canada?',
        'a' => 'Under SDS guidelines, an overall IELTS Academic band of 6.0 (or PTE Academic score of 60+) is required. Non-SDS applications also accept overall IELTS 6.0 / PTE 58+ depending on specific college criteria.'
    ],
    [
        'q' => 'What are the primary intake seasons in Canada?',
        'a' => 'The major intake is Fall (September), which offers the most comprehensive program selections. Secondary intake is Winter (January), and smaller Spring/Summer (May) intakes are offered by select colleges.'
    ]
];

$pageSeo = getSeoData('static', null, 'study-in-canada');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #1c3653 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Study Abroad' => 'study-abroad.php', 'Study in Canada' => 'study-in-canada.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-leaf me-1"></i> Designated Learning Institutions (DLI)
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Study in Canada for Nepali Students</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Experience world-standard academic excellence, welcoming multicultural communities, and up to 3-year Post-Graduation Work Permits (PGWP) across Ontario, British Columbia, Alberta, and Quebec.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="Canada">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Apply for Canada Admission
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-phone me-2"></i>Get Free SDS Assessment
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-briefcase text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">PGWP Work Permit</h5>
                    <p class="text-white-50 small mb-0">Up to 3 Years Open Work Permit Post-Graduation</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Canada Key Highlights -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-building-columns text-primary fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">DLI Colleges</h4>
                    <p class="text-muted extra-small mb-0">Government accredited public colleges & universities</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-passport text-success fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Fast SDS Stream</h4>
                    <p class="text-muted extra-small mb-0">Accelerated study permit processing with GIC</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-clock text-warning fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Off-Campus Work</h4>
                    <p class="text-muted extra-small mb-0">20 hrs/week during study & full-time in scheduled breaks</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-shield-halved text-danger fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Safe Environment</h4>
                    <p class="text-muted extra-small mb-0">Consistently ranked among safest global destinations</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Deep Dive Section -->
<section class="py-5">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">Canadian Education System</span>
                <h2 class="fw-bold text-primary display-6 mb-3">Practical Co-op Programs & High Industry Integration</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    Canadian institutions are celebrated for Co-op (cooperative education) programs, allowing students to complete paid 4-to-8 month professional internships in their study discipline prior to graduation.
                </p>
                <div class="p-3 rounded-3 bg-light border-start border-primary border-4 mb-3">
                    <h4 class="fw-bold text-dark fs-6 mb-1">Student Direct Stream (SDS) Guidance</h4>
                    <p class="text-muted small mb-0">We assist in fulfilling all SDS prerequisites: upfront 1-year tuition payment, CAD 20,635 GIC account setup, and medical examination.</p>
                </div>
                <div class="p-3 rounded-3 bg-light border-start border-success border-4">
                    <h4 class="fw-bold text-dark fs-6 mb-1">Provincial Attestation Letter (PAL) Processing</h4>
                    <p class="text-muted small mb-0">Full compliance with IRCC provincial cap limits and timely PAL letter acquisition from educational institutions.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 border shadow-sm">
                    <h3 class="fw-bold text-primary fs-5 mb-3"><i class="fa-solid fa-book-open text-danger me-2"></i>Popular Programs in Canada</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-laptop-code text-primary me-2"></i> Computer Programming, Cloud & Cybersecurity</span>
                            <span class="badge bg-primary-subtle text-primary">Co-op Options</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-briefcase text-success me-2"></i> Global Business Management & Supply Chain</span>
                            <span class="badge bg-success-subtle text-success">Post-Degree Diplomas</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-heart-pulse text-danger me-2"></i> Healthcare Leadership & Practical Nursing</span>
                            <span class="badge bg-danger-subtle text-danger">Provincial Demand</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-gears text-warning me-2"></i> Civil & Mechanical Engineering Technology</span>
                            <span class="badge bg-warning-subtle text-dark">TAC Accredited</span>
                        </li>
                        <li class="p-2 d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-hotel text-secondary me-2"></i> Hospitality, Culinary & Event Management</span>
                            <span class="badge bg-secondary-subtle text-secondary">Paid Internships</span>
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
            <h2 class="fw-bold text-primary display-6">Canada Admissions FAQ</h2>
            <p class="text-muted small">Essential guidance regarding Canadian study permits, GIC requirements, and post-graduation work permits.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionCan">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingCan<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCan<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseCan<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionCan">
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
        <h2 class="fw-bold text-white display-6 mb-3">Begin Your Canadian Study Permit Application</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Connect with our Canada admissions specialists in Biratnagar to evaluate your profile and apply to top DLI colleges.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="Canada">
            <i class="fa-solid fa-paper-plane me-2"></i>Apply for Canada Counseling
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

