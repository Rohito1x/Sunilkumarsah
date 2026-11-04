<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What is the Genuine Student (GS) requirement for Australia?',
        'a' => 'The Genuine Student (GS) requirement replaced the GTE requirement in early 2024. It assesses an applicant\'s genuine intention to obtain a world-class education in Australia, their course choice, career trajectory in Nepal or globally, and financial capability.'
    ],
    [
        'q' => 'What are the minimum IELTS/PTE scores required for Australian universities?',
        'a' => 'Generally, undergraduate Bachelor programs require an overall IELTS band of 6.0 (no band less than 5.5) or PTE 50+. Postgraduate Master programs typically require an overall IELTS 6.5 (no band less than 6.0) or PTE 58+.'
    ],
    [
        'q' => 'What are the post-study work rights (Subclass 485) in Australia?',
        'a' => 'Graduates of eligible higher education programs can apply for the Temporary Graduate Visa (Subclass 485). Bachelor graduates generally receive 2 years, Master by coursework graduates receive 2 years, and PhD graduates receive 3 years, with additional regional extensions.'
    ],
    [
        'q' => 'What are the primary intake periods in Australia?',
        'a' => 'The main intakes are Semester 1 (February/March) and Semester 2 (July/August), with select universities offering a minor Term 3 intake (October/November).'
    ],
    [
        'q' => 'What is the estimated cost of living in Australia for international students?',
        'a' => 'As per Australian Department of Home Affairs guidelines, the 12-month living cost requirement is approximately AUD 29,710 per year for a single student.'
    ]
];

$pageSeo = getSeoData('static', null, 'study-in-australia');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #063964 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Study Abroad' => 'study-abroad.php', 'Study in Australia' => 'study-in-australia.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-earth-oceania me-1"></i> CRICOS Accredited Programs
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Study in Australia for Nepali Students</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Access world-class education, post-study work opportunities (Subclass 485), and globally recognized qualifications across Sydney, Melbourne, Brisbane, Adelaide, and Perth.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="Australia">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Apply for Australia Admission
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-phone me-2"></i>Get Free GS Assessment
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-graduation-cap text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">Key Intakes</h5>
                    <p class="text-white-50 small mb-0">February / March &bull; July / August &bull; November</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Australia Overview Cards -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-building-columns text-primary fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Top Universities</h4>
                    <p class="text-muted extra-small mb-0">Group of Eight (Go8) & Tier-1 public universities</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-briefcase text-success fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Post-Study Work</h4>
                    <p class="text-muted extra-small mb-0">Up to 2–4 years PSW visa (Subclass 485)</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-language text-warning fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">English Tests</h4>
                    <p class="text-muted extra-small mb-0">IELTS, PTE Academic & TOEFL iBT accepted</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-sack-dollar text-danger fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Part-Time Work</h4>
                    <p class="text-muted extra-small mb-0">48 hours per fortnight during study terms</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Study in Australia & Top Courses -->
<section class="py-5">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">Why Australia?</span>
                <h2 class="fw-bold text-primary display-6 mb-3">World-Class Academic Standards & High Quality of Life</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    Australia is one of the top destinations for Nepalese students due to its practical education framework, strict ESOS (Education Services for Overseas Students) Act protections, multicultural cities, and high post-graduation employability.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-light border-start border-primary border-4">
                            <h4 class="fw-bold text-dark fs-6 mb-1">Genuine Student (GS) Compliance</h4>
                            <p class="text-muted small mb-0">We guide students in preparing clear, evidence-backed GS statements detailing study rationale and future value.</p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-3 bg-light border-start border-success border-4">
                            <h4 class="fw-bold text-dark fs-6 mb-1">Financial Documentation Guidance</h4>
                            <p class="text-muted small mb-0">Thorough verification of education loans, bank balances, and annual taxable income proofs required by Australian High Commission.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 border shadow-sm">
                    <h3 class="fw-bold text-primary fs-5 mb-3"><i class="fa-solid fa-list-check me-2 text-warning"></i>Popular Courses for Nepali Students</h3>
                    <ul class="list-unstyled mb-0">
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-laptop-code text-primary me-2"></i> Bachelor / Master of IT & Cybersecurity</span>
                            <span class="badge bg-primary-subtle text-primary">High Demand</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-user-nurse text-danger me-2"></i> Nursing & Public Health (AHPRA Accredited)</span>
                            <span class="badge bg-danger-subtle text-danger">PR Pathway</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-calculator text-success me-2"></i> Professional Accounting & Finance</span>
                            <span class="badge bg-success-subtle text-success">CPA Accredited</span>
                        </li>
                        <li class="p-2 border-bottom d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-gears text-warning me-2"></i> Civil, Mechanical & Software Engineering</span>
                            <span class="badge bg-warning-subtle text-dark">Engineers Australia</span>
                        </li>
                        <li class="p-2 d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-hotel text-secondary me-2"></i> Hospitality & Commercial Cookery</span>
                            <span class="badge bg-secondary-subtle text-secondary">Skill Assessment</span>
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
            <h2 class="fw-bold text-primary display-6">Australia Admissions FAQ</h2>
            <p class="text-muted small">Answers to key questions regarding Australian student visas, financial requirements, and admission timelines.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionAus">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingAus<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAus<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseAus<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionAus">
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
        <h2 class="fw-bold text-white display-6 mb-3">Apply to Top Australian Universities with KEF</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Get your academic transcripts assessed and submit your application for the upcoming Australian intake.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="Australia">
            <i class="fa-solid fa-paper-plane me-2"></i>Book Free Australian Counseling
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

