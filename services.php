<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'services');
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #033663 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Our Services' => 'services.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-graduation-cap me-1"></i> End-to-End Educational Services
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Our Comprehensive Education Services</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    From personalized course and college selection to scholarship advocacy, application filing, visa documentation, and pre-departure briefings, Vision Point Educational Service guides you through every step.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-clipboard-list me-2"></i>Apply for Service
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-comments me-2"></i>Talk to an Advisor
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-handshake-angle text-warning display-3 mb-3"></i>
                    <h5 class="fw-bold text-white mb-2">Dedicated Student Support</h5>
                    <p class="small text-white-50 mb-0">Personalized advisory tailored to your academic background, career ambitions, and budget.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">What We Offer</span>
            <h2 class="fw-bold text-primary display-6">Our Core Consultancy Offerings</h2>
            <p class="text-muted small">Designed to provide complete clarity and hassle-free admission management for Nepalese students.</p>
        </div>

        <div class="row g-4">
            <!-- 1. University & Course Selection -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-building-columns fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">University Selection</h3>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Comparing universities based on NAAC grading, NIRF rankings, faculty profiles, laboratory infrastructure, hospital attachments, and campus placements.
                    </p>
                    <a href="<?= h($basePath) ?>cities.php" class="text-primary fw-bold small text-decoration-none mt-auto">
                        Explore Universities <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 2. Career Counseling -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-compass fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">1-on-1 Career Counselling</h3>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Helping students after SEE, +2, and graduation identify the most viable degree paths in Nursing, Engineering, IT, Management, Pharmacy, and Allied Health.
                    </p>
                    <a href="<?= h($basePath) ?>career-counselling.php" class="text-primary fw-bold small text-decoration-none mt-auto">
                        Learn More <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 3. Admission & Application Processing -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-file-circle-check fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Admission Processing</h3>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Managing application submissions, transcript evaluations, eligibility verification, and prompt issuance of official provisional admission offer letters.
                    </p>
                    <a href="<?= h($basePath) ?>study-in-india.php" class="text-primary fw-bold small text-decoration-none mt-auto">
                        Admission Details <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 4. Scholarship Assistance -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-award fs-4 text-info"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Scholarship Guidance</h3>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Assisting meritorious Nepalese students in securing university merit scholarships, early bird fee concessions, and government-sponsored grants.
                    </p>
                    <a href="<?= h($basePath) ?>scholarships.php" class="text-primary fw-bold small text-decoration-none mt-auto">
                        View Scholarships <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 5. Visa & Documentation -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-passport fs-4 text-danger"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Visa Guidance</h3>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Complete documentation assistance for student visas (Australia, UK, USA, Canada) and border protocol briefing for study in India.
                    </p>
                    <a href="<?= h($basePath) ?>visa-guidance.php" class="text-primary fw-bold small text-decoration-none mt-auto">
                        Visa Procedures <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>

            <!-- 6. Test Preparation -->
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-book-open-reader fs-4 text-secondary"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Test Preparation</h3>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Structured preparation modules and mock test series for IELTS, PTE Academic, CEE Medical Entrance, and CTEVT Diploma exams.
                    </p>
                    <a href="<?= h($basePath) ?>test-preparation.php" class="text-primary fw-bold small text-decoration-none mt-auto">
                        Test Prep Details <i class="fa-solid fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Additional Specialized Services -->
<section class="py-5 bg-white border-top">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">End-to-End Care</span>
            <h2 class="fw-bold text-primary display-6">Comprehensive Student Support Services</h2>
            <p class="text-muted small">Beyond admissions and visas, we provide specialized assistance to ensure peace of mind for both students and parents.</p>
        </div>

        <div class="row g-4">
            <!-- 1. Education Loan & Financial File Prep -->
            <div class="col-md-4">
                <div class="card h-100 p-4 border rounded-4 shadow-sm">
                    <div class="p-3 bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="fa-solid fa-coins fs-4"></i>
                    </div>
                    <h4 class="fw-bold text-primary fs-5 mb-2">Education Loan Guidance</h4>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Assistance with collateral documentation, bank balance certificates, and educational loan sanction letters with Class 'A' commercial banks across Nepal.
                    </p>
                    <ul class="list-unstyled text-muted extra-small mb-0">
                        <li class="mb-1.5"><i class="fa-solid fa-check text-success me-2"></i>Bank tie-up advisory (Nabil, Global IME, RBB)</li>
                        <li class="mb-1.5"><i class="fa-solid fa-check text-success me-2"></i>Annual fee disbursement coordination</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i>Tax clearance & valuation alignment</li>
                    </ul>
                </div>
            </div>

            <!-- 2. Pre-Departure & Airport Assistance -->
            <div class="col-md-4">
                <div class="card h-100 p-4 border rounded-4 shadow-sm">
                    <div class="p-3 bg-warning-subtle text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="fa-solid fa-plane-departure fs-4 text-warning"></i>
                    </div>
                    <h4 class="fw-bold text-primary fs-5 mb-2">Pre-Departure & Arrival Support</h4>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Detailed orientation sessions before departure covering travel packing lists, campus hostel check-in, local transit, and emergency contacts.
                    </p>
                    <ul class="list-unstyled text-muted extra-small mb-0">
                        <li class="mb-1.5"><i class="fa-solid fa-check text-success me-2"></i>Airport meet-and-greet coordination</li>
                        <li class="mb-1.5"><i class="fa-solid fa-check text-success me-2"></i>Forex card & fee transfer guidance</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i>Local SIM card & campus induction support</li>
                    </ul>
                </div>
            </div>

            <!-- 3. Council Equivalence & MoFA Attestation -->
            <div class="col-md-4">
                <div class="card h-100 p-4 border rounded-4 shadow-sm">
                    <div class="p-3 bg-success-subtle text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 52px; height: 52px;">
                        <i class="fa-solid fa-stamp fs-4 text-success"></i>
                    </div>
                    <h4 class="fw-bold text-primary fs-5 mb-2">Council Equivalence Support</h4>
                    <p class="text-muted small mb-3" style="line-height: 1.7;">
                        Step-by-step guidance on obtaining degree equivalence from Tribhuvan University (CDC) and licensure eligibility with professional councils in Nepal.
                    </p>
                    <ul class="list-unstyled text-muted extra-small mb-0">
                        <li class="mb-1.5"><i class="fa-solid fa-check text-success me-2"></i>TU CDC certificate equivalence filing</li>
                        <li class="mb-1.5"><i class="fa-solid fa-check text-success me-2"></i>Notary public & MoFA document attestation</li>
                        <li><i class="fa-solid fa-check text-success me-2"></i>NMC, NNC & NEC licensing prerequisites</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Process Overview -->
<section class="py-5">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">How We Work</span>
            <h2 class="fw-bold text-primary display-6">Our 5-Step Admission Journey</h2>
            <p class="text-muted small">A streamlined, transparent pathway from your first consultation to your first day on campus.</p>
        </div>

        <div class="row g-4 text-center">
            <div class="col-6 col-md">
                <div class="p-3 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill mb-2 px-3 py-1">Step 1</div>
                    <h4 class="fw-bold fs-6 text-dark mb-1">Counselling</h4>
                    <p class="text-muted extra-small mb-0">Evaluate grades, interests & budget.</p>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-3 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill mb-2 px-3 py-1">Step 2</div>
                    <h4 class="fw-bold fs-6 text-dark mb-1">Shortlisting</h4>
                    <p class="text-muted extra-small mb-0">Select accredited colleges & courses.</p>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-3 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill mb-2 px-3 py-1">Step 3</div>
                    <h4 class="fw-bold fs-6 text-dark mb-1">Offer Letter</h4>
                    <p class="text-muted extra-small mb-0">Secure provisional admission offer.</p>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-3 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill mb-2 px-3 py-1">Step 4</div>
                    <h4 class="fw-bold fs-6 text-dark mb-1">Scholarship</h4>
                    <p class="text-muted extra-small mb-0">Apply for fee concessions & waivers.</p>
                </div>
            </div>
            <div class="col-6 col-md">
                <div class="p-3 bg-light rounded-4 h-100 border">
                    <div class="badge bg-success text-white rounded-pill mb-2 px-3 py-1">Step 5</div>
                    <h4 class="fw-bold fs-6 text-dark mb-1">Departure</h4>
                    <p class="text-muted extra-small mb-0">Hostel booking & travel briefing.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Guarantees Comparison Table -->
<section class="py-5 bg-light border-top">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">The Vision Point Promise</span>
            <h2 class="fw-bold text-primary display-6">Why Vision Point Stands Apart</h2>
            <p class="text-muted small">Compare our transparent advisory standard with traditional commercial consultancy practices.</p>
        </div>

        <div class="table-responsive shadow-sm rounded-4 overflow-hidden">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead style="background: #0458A0; color: #ffffff;">
                    <tr>
                        <th class="py-3 px-4" style="width: 35%;">Service Parameter</th>
                        <th class="py-3 px-4 text-center" style="width: 35%; background: #03447c;">Vision Point Educational Service</th>
                        <th class="py-3 px-4 text-center text-white-50" style="width: 30%;">Traditional Agents</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr>
                        <td class="fw-bold py-3 px-4 text-dark"><i class="fa-solid fa-handshake text-primary me-2"></i>University Representation</td>
                        <td class="text-center text-success fw-bold py-3 px-4"><i class="fa-solid fa-circle-check me-1"></i> 100% Direct University Tie-ups</td>
                        <td class="text-center text-muted py-3 px-4">Often routed through 3rd party brokers</td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3 px-4 text-dark"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i>Fee Transparency</td>
                        <td class="text-center text-success fw-bold py-3 px-4"><i class="fa-solid fa-circle-check me-1"></i> Official College Receipts Only</td>
                        <td class="text-center text-muted py-3 px-4">Hidden processing fees & markup rates</td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3 px-4 text-dark"><i class="fa-solid fa-award text-primary me-2"></i>Scholarship Allocation</td>
                        <td class="text-center text-success fw-bold py-3 px-4"><i class="fa-solid fa-circle-check me-1"></i> Documented in Official Offer Letter</td>
                        <td class="text-center text-muted py-3 px-4">Verbal promises without official backing</td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3 px-4 text-dark"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Council Recognition Check</td>
                        <td class="text-center text-success fw-bold py-3 px-4"><i class="fa-solid fa-circle-check me-1"></i> Rigorous NMC / NNC / NEC Auditing</td>
                        <td class="text-center text-muted py-3 px-4">Rarely verified; high licensing risk</td>
                    </tr>
                    <tr>
                        <td class="fw-bold py-3 px-4 text-dark"><i class="fa-solid fa-phone-volume text-primary me-2"></i>Support During Studies</td>
                        <td class="text-center text-success fw-bold py-3 px-4"><i class="fa-solid fa-circle-check me-1"></i> 24/7 Helpline & Guardian Contact</td>
                        <td class="text-center text-muted py-3 px-4">Service terminates upon departure</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 text-center text-white" style="background: #0458A0;">
    <div class="container py-3">
        <h2 class="fw-bold text-white display-6 mb-3">Begin Your Application with Vision Point Educational Service</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Contact our counseling team in Biratnagar to review your documents and reserve your seat today.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-graduation-cap me-2"></i>Apply for Free Counseling
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

