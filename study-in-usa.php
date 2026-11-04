<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is an I-20 and SEVIS fee for studying in the USA?',
        'a' => 'An I-20 is the official Certificate of Eligibility issued by a SEVP-certified US university confirming admission. The SEVIS I-901 fee (currently $350 for F-1 students) must be paid before scheduling your US Embassy visa interview.'
    ],
    [
        'q' => 'What is the STEM OPT 3-Year work extension in the USA?',
        'a' => 'International students with F-1 visas graduating with degrees in Science, Technology, Engineering, or Mathematics (STEM) are eligible for an initial 12-month Optional Practical Training (OPT) plus a 24-month STEM OPT extension, totaling 36 months (3 years) of US work authorization.'
    ],
    [
        'q' => 'Can Nepali students get merit scholarships in US universities?',
        'a' => 'Yes. US universities offer institutional merit scholarships ranging from $2,000 up to full tuition waivers based on high school (+2/A-Level) GPA, SAT/ACT scores, and statement of purpose.'
    ],
    [
        'q' => 'What are the main intake semesters in the United States?',
        'a' => 'The primary intake is Fall (August/September), which offers the largest course selection and scholarship funding. Secondary intake is Spring (January), and select universities offer Summer (May/June) terms.'
    ],
    [
        'q' => 'How can students prepare for the US F-1 visa interview in Kathmandu?',
        'a' => 'KEF conducts extensive mock visa interviews focused on explaining university choice, academic preparedness, financial documentation clarity, and clear intent to return or utilize skills post-graduation.'
    ]
];

$pageSeo = getSeoData('static', null, 'study-in-usa');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #0d1e33 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Study Abroad' => 'study-abroad.php', 'Study in USA' => 'study-in-usa.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-flag-usa me-1"></i> SEVP Accredited US Universities
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Study in the USA for Nepali Students</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Pursue world-class undergraduate and graduate degrees with generous merit scholarships, 3-year STEM OPT extensions, and personalized F-1 visa interview preparation.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="USA">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Apply for US Admission
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-calendar-check me-2"></i>Book F-1 Visa Counseling
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-microchip text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">STEM OPT Extension</h5>
                    <p class="text-white-50 small mb-0">Up to 3 Years Post-Graduation US Work Authorization</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- USA Key Metrics -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-university text-primary fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">4,000+ Colleges</h4>
                    <p class="text-muted extra-small mb-0">Unmatched choice of majors, minors & double degrees</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-award text-success fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Merit Aid</h4>
                    <p class="text-muted extra-small mb-0">GPA-based scholarships & assistantships (TA/RA)</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-laptop-code text-warning fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">3-Year STEM OPT</h4>
                    <p class="text-muted extra-small mb-0">36 months of practical training in Tech & Engineering</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-building-user text-danger fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">On-Campus Jobs</h4>
                    <p class="text-muted extra-small mb-0">Up to 20 hrs/week during semesters on F-1 status</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Admission Guide -->
<section class="py-5">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">US Academic Pathway</span>
                <h2 class="fw-bold text-primary display-6 mb-3">Innovation, Research & Global Career Launchpad</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    The American education system provides unmatched academic flexibility, allowing students to declare majors in their second year, pursue interdisciplinary double majors, and participate in cutting-edge university research labs.
                </p>
                <div class="p-3 rounded-3 bg-light border-start border-primary border-4 mb-3">
                    <h4 class="fw-bold text-dark fs-6 mb-1">I-20 Application & Fast Processing</h4>
                    <p class="text-muted small mb-0">We assist in preparing bank statements, affidavit of support (I-134), and scholarship matching to secure prompt I-20 issuance.</p>
                </div>
                <div class="p-3 rounded-3 bg-light border-start border-success border-4">
                    <h4 class="fw-bold text-dark fs-6 mb-1">F-1 Visa Interview Coaching in Biratnagar</h4>
                    <p class="text-muted small mb-0">Comprehensive mock sessions simulating US Embassy interviews to build confidence and articulate academic goals clearly.</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 border shadow-sm">
                    <h3 class="fw-bold text-primary fs-5 mb-3"><i class="fa-solid fa-star me-2 text-warning"></i>Popular STEM & Business Majors</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-code text-primary me-2"></i> Computer Science, Software Eng & AI</span>
                            <span class="badge bg-primary-subtle text-primary">3-Year OPT</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-chart-pie text-success me-2"></i> Data Analytics & Business Intelligence</span>
                            <span class="badge bg-success-subtle text-success">STEM Designated</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-dna text-danger me-2"></i> Biomedical Engineering & Biotechnology</span>
                            <span class="badge bg-danger-subtle text-danger">Research Hub</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-briefcase text-warning me-2"></i> Finance, Economics & Business Admin</span>
                            <span class="badge bg-warning-subtle text-dark">AACSB Accredited</span>
                        </li>
                        <li class="p-2 d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-robot text-secondary me-2"></i> Mechanical & Electrical Engineering</span>
                            <span class="badge bg-secondary-subtle text-secondary">ABET Accredited</span>
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
            <h2 class="fw-bold text-primary display-6">United States Admissions FAQ</h2>
            <p class="text-muted small">Key facts regarding I-20 issuance, SEVIS fees, and F-1 visa interviews.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionUsa">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingUsa<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseUsa<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseUsa<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionUsa">
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
        <h2 class="fw-bold text-white display-6 mb-3">Unlock Your American Academic Dream</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Connect with our US admissions counselors in Biratnagar to evaluate your GPA and schedule your I-20 application.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="USA">
            <i class="fa-solid fa-paper-plane me-2"></i>Apply for US Admissions
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

