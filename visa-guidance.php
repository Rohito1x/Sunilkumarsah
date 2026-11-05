<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$faqs = [
    [
        'q' => 'Do Nepali students need a visa to study in India?',
        'a' => 'No. Under the 1950 Indo-Nepal Friendship Treaty, Nepali citizens have full freedom of movement and study rights in India without a student visa. A valid Nepalese citizenship certificate or passport is required.'
    ],
    [
        'q' => 'What documents are required for an Australian Student Visa (Subclass 500)?',
        'a' => 'Confirmation of Enrolment (CoE), Genuine Student (GS) statement, English proficiency test (IELTS/PTE), Overseas Student Health Cover (OSHC), financial proof of funds (living expenses & 1-year tuition), and biometric verification.'
    ],
    [
        'q' => 'What is the UK Student Visa (Tier 4) process?',
        'a' => 'Requires a valid CAS letter from a licensed UK university, 28-day maintenance funds proof, Tuberculosis (TB) medical test certificate, Immigration Health Surcharge (IHS) payment, and biometric submission at VFS Global in Kathmandu.'
    ],
    [
        'q' => 'How does Vision Point prepare students for visa interviews?',
        'a' => 'We conduct multiple one-on-one mock interview sessions focusing on course rationale, career goals, university selection justification, and financial clarity.'
    ]
];

$pageSeo = getSeoData('static', null, 'visa-guidance');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #033663 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Services' => 'services.php', 'Visa Guidance' => 'visa-guidance.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-passport me-1"></i> Documentation & Visa Processing
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Student Visa Guidance & Documentation</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Expert assistance with genuine document verification, financial sponsorship preparation, mock interview coaching, and seamless visa filing for Nepali students.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-file-circle-check me-2"></i>Book Visa Assessment
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-location-dot me-2"></i>Visit Biratnagar Office
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-shield-halved text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">High Success Track</h5>
                    <p class="text-white-50 small mb-0">Rigorous Document Auditing & Mock Preparation</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Country-Specific Visa Overview -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-flag text-danger fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">India</h4>
                    <p class="text-muted extra-small mb-0">Visa-Free Travel & Study under 1950 Friendship Treaty</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-earth-oceania text-primary fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">Australia</h4>
                    <p class="text-muted extra-small mb-0">Subclass 500 Visa & Genuine Student (GS) Compliance</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-landmark-dome text-warning fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">United Kingdom</h4>
                    <p class="text-muted extra-small mb-0">CAS Letter Verification & 28-Day Financial Rule</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card p-3 border-0 shadow-sm rounded-4 text-center h-100">
                    <i class="fa-solid fa-flag-usa text-success fs-2 mb-2"></i>
                    <h4 class="fw-bold text-dark fs-6 mb-1">USA & Canada</h4>
                    <p class="text-muted extra-small mb-0">F-1 Embassy Interviews & SDS GIC Applications</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Step by Step Process -->
<section class="py-5">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Visa Advisory</span>
            <h2 class="fw-bold text-primary display-6">Our Visa Support Methodology</h2>
            <p class="text-muted small">We ensure every document meets embassy standards before application submission.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill px-3 py-1 mb-3">Phase 1</div>
                    <h3 class="fw-bold text-dark fs-6 mb-2">Document Audit</h3>
                    <p class="text-muted small mb-0">Comprehensive verification of academic records, relationship verification certificates, and property valuation proofs.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill px-3 py-1 mb-3">Phase 2</div>
                    <h3 class="fw-bold text-dark fs-6 mb-2">Financial File Prep</h3>
                    <p class="text-muted small mb-0">Assistance with education loan sanction letters, tax clearance certificates, and approved bank balance statements.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-light rounded-4 h-100 border">
                    <div class="badge bg-primary text-white rounded-pill px-3 py-1 mb-3">Phase 3</div>
                    <h3 class="fw-bold text-dark fs-6 mb-2">SOP & GS Drafting</h3>
                    <p class="text-muted small mb-0">Personalized review of Statements of Purpose (SOP) to articulate career alignment and eliminate generic phrasing.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="p-4 bg-light rounded-4 h-100 border">
                    <div class="badge bg-success text-white rounded-pill px-3 py-1 mb-3">Phase 4</div>
                    <h3 class="fw-bold text-dark fs-6 mb-2">Mock Interview</h3>
                    <p class="text-muted small mb-0">Face-to-face simulated interview sessions to build confidence and prepare for embassy officer questions.</p>
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
            <h2 class="fw-bold text-primary display-6">Visa & Border Guidelines FAQ</h2>
            <p class="text-muted small">Clear answers regarding student documentation, financial sponsorships, and visa interviews.</p>
        </div>

        <div class="accordion max-w-800 mx-auto shadow-sm rounded-4 overflow-hidden" id="faqAccordionVisa">
            <?php foreach ($faqs as $idx => $f): ?>
                <div class="accordion-item border-0 border-bottom">
                    <h3 class="accordion-header" id="headingVisa<?= $idx ?>">
                        <button class="accordion-button <?= $idx !== 0 ? 'collapsed' : '' ?> fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseVisa<?= $idx ?>" aria-expanded="<?= $idx === 0 ? 'true' : 'false' ?>">
                            <?= h($f['q']) ?>
                        </button>
                    </h3>
                    <div id="collapseVisa<?= $idx ?>" class="accordion-collapse collapse <?= $idx === 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordionVisa">
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
        <h2 class="fw-bold text-white display-6 mb-3">Schedule Your Free Visa Assessment</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Let our experienced visa counselors in Biratnagar review your documentation and guide your application.</p>
        <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
            <i class="fa-solid fa-paper-plane me-2"></i>Book Visa Counseling
        </button>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

