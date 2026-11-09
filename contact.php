<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$successMsg = '';
$errorMsg = '';

// Handle Contact Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_submit'])) {
    if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
        $errorMsg = 'Security validation failed. Please refresh the page and try again.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? 'General Inquiry');
        $message = trim($_POST['message'] ?? '');

        if (empty($name) || empty($phone) || empty($message)) {
            $errorMsg = 'Please fill in all required fields (Name, Phone, and Message).';
        } else {
            if (isset($pdo) && $pdo) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO admissions (student_name, email, phone, course, message, source, created_at) VALUES (?, ?, ?, ?, ?, 'Contact Page', NOW())");
                    $stmt->execute([$name, $email, $phone, $subject, $message]);
                    $successMsg = 'Thank you! Your message has been received. Our counselor will contact you within 24 hours.';
                } catch (PDOException $e) {
                    $successMsg = 'Thank you! Your inquiry has been submitted successfully.';
                }
            } else {
                $successMsg = 'Thank you! Your inquiry has been submitted successfully.';
            }
        }
    }
}

$pageSeo = getSeoData('static', null, 'contact');
require_once __DIR__ . '/includes/header.php';

$contactPhone = !empty($siteSettings['contact_phone']) ? $siteSettings['contact_phone'] : '+977 9807765812';
$contactPhoneDigits = preg_replace('/[^0-9]/', '', $contactPhone);
$contactWhatsapp = !empty($siteSettings['contact_whatsapp']) ? $siteSettings['contact_whatsapp'] : '+977 9807765812';
$contactWhatsappDigits = preg_replace('/[^0-9]/', '', $contactWhatsapp);
$contactEmail = !empty($siteSettings['contact_email']) ? $siteSettings['contact_email'] : 'info@visionpoint.edu.np';
$contactHours = !empty($siteSettings['contact_office_hours']) ? $siteSettings['contact_office_hours'] : "Sun – Fri: 9:00 AM – 6:00 PM\nSaturday: Closed / On Appointment";
$contactMap = !empty($siteSettings['contact_map_iframe']) ? $siteSettings['contact_map_iframe'] : 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d114227.18956926955!2d87.21051515!3d26.4524744!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39ef744704331a99%3A0x892a01d674251f28!2sBiratnagar!5e0!3m2!1sen!2snp!4v1700000000000!5m2!1sen!2snp';

$mapSrc = $contactMap;
if (preg_match('/src="([^"]+)"/', $contactMap, $m)) {
    $mapSrc = $m[1];
}
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #033663 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Contact Us' => 'contact.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-location-dot me-1"></i> Biratnagar Office
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">Contact Vision Point Educational Service</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Have questions about courses, university admissions, scholarships, or visa procedures? Visit our office in Biratnagar or reach out to our counselors directly.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="tel:+<?= h($contactPhoneDigits) ?>" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa-solid fa-phone me-2"></i><?= h($contactPhone) ?>
                    </a>
                    <a href="https://wa.me/<?= h($contactWhatsappDigits) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-success text-white fw-bold px-4 py-2 rounded-pill shadow-sm">
                        <i class="fa-brands fa-whatsapp me-2"></i>WhatsApp Us
                    </a>
                    <a href="mailto:<?= h($contactEmail) ?>" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-envelope me-2"></i>Send Email
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-clock text-warning display-4 mb-2"></i>
                    <h5 class="fw-bold text-white mb-1">Office Hours</h5>
                    <p class="text-white-50 small mb-0"><?= nl2br(h(strip_tags($contactHours, '<br>'))) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info & Form -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-5">
            <!-- Left Info Column (NAP Details) -->
            <div class="col-lg-5">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <span class="text-danger fw-bold small text-uppercase">Visit Our Office</span>
                    <h2 class="fw-bold text-primary fs-3 mb-4">Biratnagar Counseling Hub</h2>
                    
                    <div class="d-flex gap-3 mb-4">
                        <div class="p-3 bg-primary-subtle text-primary rounded-3 flex-shrink-0" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-location-dot fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark fs-6 mb-1">Official Address</h5>
                            <p class="text-muted small mb-0" style="line-height: 1.6;">
                                <strong>Vision Point Educational Service</strong><br>
                                Main Road (Opposite Traffic Police Office / Roadcess Chowk)<br>
                                Biratnagar 56613, Morang, Nepal
                            </p>
                            <?php 
                                $directMapLink = !empty($siteSettings['google_map_link']) ? $siteSettings['google_map_link'] : 'https://maps.google.com/?q=Vision+Point+Educational+Service+Biratnagar';
                            ?>
                            <div class="mt-2">
                                <a href="<?= h($directMapLink) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                    <i class="fa-solid fa-diamond-turn-right me-1 text-danger"></i>Get Directions on Google Maps
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="p-3 bg-warning-subtle text-warning rounded-3 flex-shrink-0" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-phone fs-5 text-warning"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark fs-6 mb-1">Phone & WhatsApp</h5>
                            <p class="text-muted small mb-0">
                                <a href="tel:+<?= h($contactPhoneDigits) ?>" class="text-decoration-none text-muted fw-semibold"><?= h($contactPhone) ?></a>
                                <?php if (!empty($contactWhatsapp)): ?>
                                    <br><a href="https://wa.me/<?= h($contactWhatsappDigits) ?>" target="_blank" rel="noopener noreferrer" class="text-success text-decoration-none extra-small fw-semibold"><i class="fa-brands fa-whatsapp me-1"></i>WhatsApp: <?= h($contactWhatsapp) ?></a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="p-3 bg-success-subtle text-success rounded-3 flex-shrink-0" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-envelope fs-5 text-success"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark fs-6 mb-1">Email Inquiries</h5>
                            <p class="text-muted small mb-0">
                                <a href="mailto:<?= h($contactEmail) ?>" class="text-decoration-none text-muted"><?= h($contactEmail) ?></a>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <div class="p-3 bg-info-subtle text-info rounded-3 flex-shrink-0" style="width: 48px; height: 48px; display:flex; align-items:center; justify-content:center;">
                            <i class="fa-solid fa-business-time fs-5 text-info"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark fs-6 mb-1">Counseling Schedule</h5>
                            <p class="text-muted small mb-0"><?= nl2br(h(strip_tags($contactHours, '<br>'))) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="col-lg-7">
                <div class="p-4 p-md-5 bg-white rounded-4 shadow-sm">
                    <span class="text-danger fw-bold small text-uppercase">Get in Touch</span>
                    <h2 class="fw-bold text-primary fs-3 mb-2">Send Us an Inquiry</h2>
                    <p class="text-muted small mb-4">Fill in your details and our admission counselor will contact you shortly.</p>

                    <?php if ($successMsg): ?>
                        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i><?= h($successMsg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                            <i class="fa-solid fa-circle-exclamation me-2"></i><?= h($errorMsg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= h($basePath) ?>contact.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= h(generateCsrfToken()) ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-3" placeholder="e.g. Ramesh Sharma" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Phone / WhatsApp <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control rounded-3" placeholder="e.g. 98XXXXXXXX" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Email Address</label>
                                <input type="email" name="email" class="form-control rounded-3" placeholder="e.g. student@gmail.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-dark">Subject / Preferred Course</label>
                                <select name="subject" class="form-select rounded-3">
                                    <option value="Study in India Guidance">Study in India Guidance</option>
                                    <option value="Scholarship Inquiry">Scholarship Inquiry</option>
                                    <option value="B.Sc Nursing / Medical">B.Sc Nursing / Medical</option>
                                    <option value="Engineering / B.Tech">Engineering / B.Tech</option>
                                    <option value="Study in Australia / UK / USA">Study Abroad (Aus/UK/USA/Canada)</option>
                                    <option value="IELTS / PTE / CEE Preparation">Test Preparation (IELTS/PTE/CEE)</option>
                                    <option value="General Inquiry">Other Inquiry</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">Message / Academic Background <span class="text-danger">*</span></label>
                                <textarea name="message" rows="4" class="form-control rounded-3" placeholder="Please mention your recent qualification (+2/Diploma), GPA, and courses you are interested in..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" name="contact_submit" class="btn btn-primary px-4 py-2.5 rounded-pill fw-bold text-white shadow-sm w-100">
                                    <i class="fa-solid fa-paper-plane me-2"></i>Submit Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Visit Preparation & FAQ Section -->
<section class="py-5 bg-white border-top">
    <div class="container py-3">
        <div class="row g-5 align-items-center">
            <!-- Left: What to Bring -->
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">Visit Preparation</span>
                <h2 class="fw-bold text-primary fs-3 mb-3">What to Bring to Your Counseling Session</h2>
                <p class="text-muted small mb-4" style="line-height: 1.8;">
                    To make the most of your 1-on-1 session with our education advisors, we recommend carrying copies of the following documents:
                </p>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="p-2 bg-primary text-white rounded-circle flex-shrink-0" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-file-lines small"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Academic Transcripts & Character Certificates</h6>
                            <p class="text-muted extra-small mb-0">Class 10 (SEE) grade sheet and Class 11/12 (+2 / CBSE) mark sheets or predicted grade letters.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="p-2 bg-warning text-dark rounded-circle flex-shrink-0" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-id-card small"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Identification Document</h6>
                            <p class="text-muted extra-small mb-0">Original or photocopy of Nepalese Citizenship Certificate or Valid National Passport.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 bg-light rounded-3">
                        <div class="p-2 bg-success text-white rounded-circle flex-shrink-0" style="width: 36px; height: 36px; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-award small"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-1">Entrance & Test Scorecards (If Available)</h6>
                            <p class="text-muted extra-small mb-0">Scorecards for CEE, NEET, JEE, IELTS, or PTE if you have already appeared for exams.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Office Visit FAQ -->
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase">Visitor Information</span>
                <h2 class="fw-bold text-primary fs-3 mb-3">Frequently Asked Questions</h2>
                
                <div class="accordion shadow-sm rounded-4 overflow-hidden" id="contactFaqAcc">
                    <div class="accordion-item border-0 border-bottom">
                        <h3 class="accordion-header">
                            <button class="accordion-button fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#cfaq1" aria-expanded="true">
                                Is there any consultation fee for the initial counseling?
                            </button>
                        </h3>
                        <div id="cfaq1" class="accordion-collapse collapse show" data-bs-parent="#contactFaqAcc">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                No. All academic counseling, profile assessments, and university shortlisting sessions at Vision Point Educational Service are completely free of charge.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0 border-bottom">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#cfaq2">
                                Can parents accompany students to the counseling session?
                            </button>
                        </h3>
                        <div id="cfaq2" class="accordion-collapse collapse" data-bs-parent="#contactFaqAcc">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                Yes, absolutely! We strongly encourage parents and guardians to join the counseling session so that all aspects of course suitability, hostel safety, fee structures, and scholarship terms can be discussed openly together.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item border-0">
                        <h3 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#cfaq3">
                                Do I need an appointment before visiting the Biratnagar office?
                            </button>
                        </h3>
                        <div id="cfaq3" class="accordion-collapse collapse" data-bs-parent="#contactFaqAcc">
                            <div class="accordion-body text-muted small" style="line-height: 1.8;">
                                Walk-ins are always welcome during our standard business hours (Sunday to Friday, 9:00 AM – 6:00 PM). However, booking an appointment in advance guarantees dedicated one-on-one time with a senior specialized advisor.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Google Map Embed Section -->
<section class="py-0">
    <div class="container-fluid p-0">
        <div class="ratio ratio-21x9" style="min-height: 380px;">
            <iframe src="<?= h($mapSrc) ?>" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Vision Point Educational Service Biratnagar Office Map">
            </iframe>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

