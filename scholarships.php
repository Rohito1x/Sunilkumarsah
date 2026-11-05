<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'What scholarships are available for Nepali students studying in India?',
        'a' => 'Nepalese students can access Government of India bilateral schemes (such as COMPEX Scheme and ICCR Scholarships), as well as direct institutional merit scholarships offered by partner universities covering 25% to 100% of tuition fees.'
    ],
    [
        'q' => 'What criteria determine merit scholarship eligibility?',
        'a' => 'Eligibility is evaluated based on aggregate percentages in Secondary Education Examination (SEE) and +2 (NEB/CBSE), performance in university entrance assessments, and early registration.'
    ],
    [
        'q' => 'Does Vision Point Educational Service charge extra fees for scholarship applications?',
        'a' => 'No. We provide 100% transparent counseling and apply for institutional scholarships directly on your behalf without any hidden processing charges.'
    ],
    [
        'q' => 'Are scholarships available for B.Sc Nursing and Engineering (B.Tech)?',
        'a' => 'Yes. Top universities in Bangalore, Tamil Nadu, Punjab, and Delhi NCR allocate dedicated quota seats with tuition fee concessions for meritorious Nepalese students in engineering, nursing, pharmacy, and management.'
    ]
];

$pageSeo = getSeoData('static', null, 'scholarships');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #033663 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Scholarships' => 'scholarships.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-award me-1"></i> Merit & Bilateral Grants
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Scholarships for Nepali Students</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Discover up to 25%–100% tuition scholarships, merit fee waivers, and government-backed grants for higher education in top accredited Indian and overseas universities.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-hand-holding-dollar me-2"></i>Apply for Scholarship Evaluation
                    </button>
                    <a href="<?= h($basePath) ?>study-in-india.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-graduation-cap me-2"></i>Explore Eligible Programs
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-award text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">Up to 100% Tuition Aid</h5>
                    <p class="text-white-50 small mb-0">Merit-Based & Early Bird Concessions</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Scholarship Categories -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-medal fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Merit-Based Scholarships</h3>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        Concessions based on +2 GPA (typically 75%+ or 3.2+ GPA) offering direct tuition discounts between 25% and 75% for 4-year undergraduate and 2-year postgraduate degrees.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-landmark fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Government & Bilateral Schemes</h3>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        Guidance on official Embassy of India schemes such as COMPEX, ICCR (Indian Council for Cultural Relations), and AYUSH scholarships for Nepalese citizens.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-handshake fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Early Admission Concessions</h3>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        Exclusive early-decision fee reductions and hostel fee subsidies negotiated with partner universities for students who apply in the first admission cycle.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Partner University Scholarships Table -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Direct Concessions</span>
            <h2 class="fw-bold text-primary display-6">Featured Partner University Scholarships (2026 Intakes)</h2>
            <p class="text-muted small">Tuition waivers and merit concessions available through Vision Point's direct university representation agreements.</p>
        </div>

        <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead style="background: #0458A0; color: #ffffff;">
                    <tr>
                        <th class="py-3 px-3">Partner University</th>
                        <th class="py-3 px-3">Location</th>
                        <th class="py-3 px-3">Eligible Programs</th>
                        <th class="py-3 px-3 text-center">Scholarship Coverage</th>
                        <th class="py-3 px-3">Minimum Criteria</th>
                        <th class="py-3 px-3 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white small">
                    <tr>
                        <td class="fw-bold text-dark py-3 px-3">SRM Institute of Science & Technology</td>
                        <td class="text-muted py-3 px-3">Chennai, Tamil Nadu</td>
                        <td class="py-3 px-3">B.Tech, B.Sc Nursing, BBA</td>
                        <td class="text-center py-3 px-3"><span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill fs-7">25% – 50% Tuition Waiver</span></td>
                        <td class="text-muted py-3 px-3">80%+ in +2 PCM / PCB</td>
                        <td class="text-center py-3 px-3"><button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Apply</button></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-dark py-3 px-3">Chandigarh University (CU)</td>
                        <td class="text-muted py-3 px-3">Mohali / Punjab</td>
                        <td class="py-3 px-3">B.Tech, B.Pharm, B.Sc Nursing, MBA</td>
                        <td class="text-center py-3 px-3"><span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill fs-7">30% – 100% Fee Concession</span></td>
                        <td class="text-muted py-3 px-3">CUCET / 75%+ in +2</td>
                        <td class="text-center py-3 px-3"><button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Apply</button></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-dark py-3 px-3">Lovely Professional University (LPU)</td>
                        <td class="text-muted py-3 px-3">Phagwara, Punjab</td>
                        <td class="py-3 px-3">Engineering, Agriculture, Pharmacy, BCA</td>
                        <td class="text-center py-3 px-3"><span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill fs-7">20% – 50% Program Waiver</span></td>
                        <td class="text-muted py-3 px-3">LPUNEST / +2 Merit</td>
                        <td class="text-center py-3 px-3"><button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Apply</button></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-dark py-3 px-3">Parul University</td>
                        <td class="text-muted py-3 px-3">Vadodara, Gujarat</td>
                        <td class="py-3 px-3">Nursing, Paramedical, B.Tech, B.Pharm</td>
                        <td class="text-center py-3 px-3"><span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill fs-7">25% – 60% Tuition Aid</span></td>
                        <td class="text-muted py-3 px-3">Merit + Early Registration</td>
                        <td class="text-center py-3 px-3"><button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Apply</button></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-dark py-3 px-3">Sharda University</td>
                        <td class="text-muted py-3 px-3">Greater Noida (Delhi NCR)</td>
                        <td class="py-3 px-3">B.Tech, B.Sc Nursing, BBA, Law</td>
                        <td class="text-center py-3 px-3"><span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill fs-7">20% – 50% Fee Waiver</span></td>
                        <td class="text-muted py-3 px-3">SAARC Quota + 70%+ GPA</td>
                        <td class="text-center py-3 px-3"><button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Apply</button></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-dark py-3 px-3">Bangalore Group of Institutions</td>
                        <td class="text-muted py-3 px-3">Bangalore, Karnataka</td>
                        <td class="py-3 px-3">B.Sc Nursing, BPT, BMLT, Pharmacy</td>
                        <td class="text-center py-3 px-3"><span class="badge bg-success-subtle text-success fw-bold px-2.5 py-1.5 rounded-pill fs-7">Hostel & Fee Subsidy</span></td>
                        <td class="text-muted py-3 px-3">+2 Science 60%+ & Early Bird</td>
                        <td class="text-center py-3 px-3"><button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">Apply</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-muted extra-small mt-3 text-center mb-0"><i class="fa-solid fa-circle-info me-1"></i> Scholarships are awarded on a first-come, first-served basis subject to university quota limits. All waivers are officially stated in the admission offer letter.</p>
    </div>
</section>

<!-- Required Documents & Verification -->
<section class="py-5">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">How to Qualify</span>
                <h2 class="fw-bold text-primary display-6 mb-3">Simple 4-Step Scholarship Application</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    Our advisors review your academic portfolio and match you directly with institutions offering maximum financial relief for your selected course stream.
                </p>
                <div class="d-flex flex-column gap-3 mt-4">
                    <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                        <h4 class="fw-bold text-dark fs-6 mb-1">1. Academic Portfolio Evaluation</h4>
                        <p class="text-muted small mb-0">Submit SEE and +2 transcripts for grade point analysis.</p>
                    </div>
                    <div class="p-3 bg-light rounded-3 border-start border-warning border-4">
                        <h4 class="fw-bold text-dark fs-6 mb-1">2. University Scholarship Matching</h4>
                        <p class="text-muted small mb-0">Select from verified colleges offering eligible fee concessions.</p>
                    </div>
                    <div class="p-3 bg-light rounded-3 border-start border-success border-4">
                        <h4 class="fw-bold text-dark fs-6 mb-1">3. Official Scholarship Letter</h4>
                        <p class="text-muted small mb-0">Receive provisional admission with tuition waiver clearly documented.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="p-4 bg-light rounded-4 border shadow-sm">
                    <h3 class="fw-bold text-primary fs-5 mb-3"><i class="fa-solid fa-file-invoice text-warning me-2"></i>Documents Needed for Assessment</h3>
                    <ul class="list-unstyled text-muted small mb-0">
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <span><strong>SEE / 10th Marksheet:</strong> Grade sheet & Character Certificate.</span>
                        </li>
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <span><strong>+2 / Intermediate Transcript:</strong> Official grade sheet or expected result letter.</span>
                        </li>
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <span><strong>Citizenship / Passport:</strong> Valid identity proof for Nepalese citizen status.</span>
                        </li>
                        <li class="mb-2.5 d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <span><strong>Passport Sized Photos:</strong> Recent clean portrait photographs.</span>
                        </li>
                        <li class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-circle-check text-success mt-1"></i>
                            <span><strong>Entrance Scorecards (Optional):</strong> NEET, JEE, or University entrance ranks if applicable.</span>
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
            <h2 class="fw-bold text-primary display-6">Scholarship Inquiries FAQ</h2>
            <p class="text-muted small">Learn about eligibility, coverage percentages, and scholarship renewal rules.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionSch">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingSch<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSch<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseSch<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionSch">
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
        <h2 class="fw-bold text-white display-6 mb-3">Check Your Scholarship Eligibility Today</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Submit your +2 transcripts for instant scholarship assessment from our counseling team.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Apply for Scholarship
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

