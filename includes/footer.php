<?php
if (!isset($basePath)) {
    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..'));
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'])) : '';
    $basePath = '/';
    if ($docRoot !== '' && strpos($projectRoot, $docRoot) === 0) {
        $basePath = substr($projectRoot, strlen($docRoot));
        if ($basePath === false) {
            $basePath = '/';
        } else {
            $basePath = '/' . trim($basePath, '/') . '/';
        }
    }
    if ($basePath === '//') {
        $basePath = '/';
    }
}

// Fetch social links and verify settings are loaded
$dbSocials = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query('SELECT * FROM social_links ORDER BY display_order ASC');
        $dbSocials = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (!isset($siteSettings) || empty($siteSettings)) {
            $stmtSet = $pdo->query('SELECT `key`, `value` FROM site_settings');
            $siteSettings = $stmtSet->fetchAll(PDO::FETCH_KEY_PAIR);
        }
    } catch (PDOException $e) {
        // Fallback
    }
}
if (empty($dbSocials)) {
    $dbSocials = [
        ['platform_name' => 'Facebook', 'url' => 'https://www.facebook.com/visionpointedu', 'icon' => 'fa-brands fa-facebook'],
        ['platform_name' => 'Instagram', 'url' => 'https://www.instagram.com/visionpointedu', 'icon' => 'fa-brands fa-instagram'],
        ['platform_name' => 'YouTube', 'url' => 'https://youtube.com/@visionpointedu', 'icon' => 'fa-brands fa-youtube'],
        ['platform_name' => 'WhatsApp', 'url' => 'https://wa.me/9779807765812', 'icon' => 'fa-brands fa-whatsapp']
    ];
}
?>
<footer class="susankya-site-footer text-white pt-5 pb-4 mt-5" style="background-color: #0458A0 !important; border-top: 4px solid #D91F23;">
    <div class="container">
        <div class="row g-4">
            <!-- Column 1: Info & Brand Widget -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <h5 class="text-white fw-bold mb-3 d-flex align-items-center">
                    <?php
                    $logoPath = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo1.png';
                    $logoUrl = (strpos($logoPath, 'http') === 0) ? $logoPath : $basePath . $logoPath;
                    $localLogoFile = __DIR__ . '/../' . ltrim($logoPath, '/');
                    if (file_exists($localLogoFile)) {
                        $logoUrl .= '?v=' . filemtime($localLogoFile);
                    }
                    if (file_exists($localLogoFile) || (strpos($logoPath, 'http') === 0)): ?>
                        <img src="<?= h($logoUrl) ?>" alt="Vision Point Logo" class="me-2 rounded shadow-sm border border-secondary" style="max-height: 44px; width: auto; object-fit: contain;">
                    <?php else: ?>
                        <i class="fa-solid fa-graduation-cap me-2 text-danger"></i>
                    <?php endif; ?>
                    <span class="text-white"><?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?></span>
                </h5>
                <div class="mb-2">
                    <span class="badge bg-warning text-dark px-2.5 py-1 rounded-pill extra-small fw-bold">
                        <i class="fa-solid fa-certificate me-1 text-danger"></i> Est. 2070 B.S. &bull; MoEST Approved &bull; TITI Certified
                    </span>
                </div>
                <p class="text-white small mb-3" style="line-height: 1.7;">
                    Established in 2070 B.S. (वि.सं. २०७०) in Biratnagar, Vision Point Educational Service is approved by the <strong>Ministry of Education, Science & Technology (MoEST)</strong> with <strong>TITI certified counselors</strong>, providing ethical admission guidance and scholarships for top-tier universities in India and abroad.
                </p>
                <!-- Social Links -->
                <div class="d-flex align-items-center gap-2 mt-3">
                    <?php foreach ($dbSocials as $social): ?>
                        <a href="<?= htmlspecialchars($social['url']) ?>" target="_blank" class="footer-social-btn text-white d-flex align-items-center justify-content-center rounded-circle border border-white-subtle text-decoration-none" style="width: 36px; height: 36px; transition: all 0.3s ease;" title="<?= htmlspecialchars($social['platform_name']) ?>">
                            <i class="<?= htmlspecialchars($social['icon']) ?> small"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Column 2: Quick Links -->
            <div class="col-6 col-md-3 col-lg-2 mb-3">
                <h6 class="text-uppercase fw-bold mb-3 text-warning" style="letter-spacing: 0.8px; font-size: 13px;">Quick Links</h6>
                <ul class="list-unstyled text-white small mb-0">
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>index.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Home</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>about.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> About Us</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>about.php#counselors-team" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Certified Counselors</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>study-in-india.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Study in India</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>cities.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Institutions</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>courses.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Courses</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>scholarships.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Scholarships</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>reviews.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Reviews & Feedback</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>contact.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Contact Us</a></li>
                </ul>
            </div>
            
            <!-- Column 3: Top Programs -->
            <div class="col-6 col-md-3 col-lg-2 mb-3">
                <h6 class="text-uppercase fw-bold mb-3 text-warning" style="letter-spacing: 0.8px; font-size: 13px;">Top Programs</h6>
                <ul class="list-unstyled text-white small mb-0">
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>courses.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> B.Sc. Nursing</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>courses.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> B.Tech / CSE</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>courses.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Pharmacy</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>courses.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Management</a></li>
                    <li class="mb-2"><a href="<?= htmlspecialchars($basePath) ?>courses.php" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Dental / MBBS</a></li>
                    <li class="mb-2"><a href="#" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" class="footer-link text-white text-decoration-none"><i class="fa-solid fa-chevron-right me-1 extra-small text-danger"></i> Scholarships</a></li>
                </ul>
            </div>
            
            <!-- Column 4: Contact & Location Info -->
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <h6 class="text-uppercase fw-bold mb-3 text-warning" style="letter-spacing: 0.8px; font-size: 13px;">Contact Us</h6>
                <ul class="list-unstyled text-white small mb-3">
                    <li class="mb-2.5 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-phone mt-1 text-danger"></i>
                        <?php 
                            $rawPhone = $siteSettings['contact_phone'] ?? '+977 9807765812';
                            $firstPhone = trim(explode('/', $rawPhone)[0]);
                            $cleanPhone = preg_replace('/[^0-9+]/', '', $firstPhone);
                        ?>
                        <a href="tel:<?= h($cleanPhone) ?>" class="footer-link text-white text-decoration-none"><?= h($rawPhone) ?></a>
                    </li>
                    <li class="mb-2.5 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-envelope mt-1 text-danger"></i>
                        <a href="mailto:<?= h($siteSettings['contact_email'] ?? 'info@visionpoint.edu.np') ?>" class="footer-link text-white text-decoration-none"><?= h($siteSettings['contact_email'] ?? 'info@visionpoint.edu.np') ?></a>
                    </li>
                    <li class="mb-2.5 d-flex align-items-start gap-2">
                        <i class="fa-solid fa-location-dot mt-1 text-danger"></i>
                        <span class="text-white"><?= h($siteSettings['contact_location'] ?? 'Vision Point Educational Service, Main Road (Opposite Traffic Police Office / Roadcess Chowk), Biratnagar 56613, Morang, Nepal') ?></span>
                    </li>
                </ul>
                <button class="btn btn-secondary btn-sm rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                    <i class="fa-solid fa-paper-plane me-1"></i> Apply for Scholarship
                </button>
            </div>
        </div>
        
        <hr class="border-secondary my-4" style="opacity: 0.3;">
        
        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center text-md-start text-white small mb-2 mb-md-0">
                &copy; <?= date('Y') ?> <?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?> (Est. 2070 B.S.). Approved by MoEST &bull; TITI Certified Counselors. All rights reserved.
            </div>
            <div class="col-12 col-md-6 text-center text-md-end text-white extra-small">
                <span>Designed & Optimized for Nepalese Students</span>
            </div>
        </div>
    </div>
</footer>

<style>
.footer-link {
    transition: all 0.25s ease;
}
.footer-link:hover {
    color: #ffffff !important;
    padding-left: 4px;
}
.footer-social-btn:hover {
    background-color: #D91F23 !important;
    border-color: #D91F23 !important;
    transform: translateY(-3px);
}
.extra-small {
    font-size: 11px;
}
</style>

<?php require_once __DIR__ . '/modals.php'; ?>

<!-- Mobile Bottom Navigation Bar -->
<div class="mobile-bottom-nav d-flex d-md-none fixed-bottom bg-white border-top shadow-lg justify-content-around align-items-center py-1" style="z-index: 1030;">
    <a href="<?= htmlspecialchars($basePath) ?>index.php" class="nav-item text-center text-decoration-none <?= (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'active-nav-item text-primary' : 'text-muted' ?>" style="flex: 1;">
        <div class="nav-icon-circle">
            <i class="fa-solid fa-house"></i>
        </div>
        <span class="small fw-semibold nav-label">Home</span>
    </a>
    <a href="<?= htmlspecialchars($basePath) ?>study-in-india.php" class="nav-item text-center text-decoration-none <?= (basename($_SERVER['PHP_SELF']) === 'study-in-india.php') ? 'active-nav-item text-primary' : 'text-muted' ?>" style="flex: 1;">
        <div class="nav-icon-circle">
            <i class="fa-solid fa-flag"></i>
        </div>
        <span class="small fw-semibold nav-label">Study in India</span>
    </a>
    
    <!-- Central Circular Floating Enroll Button -->
    <a href="#" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" class="nav-item text-center text-decoration-none mobile-enroll-nav-btn" style="flex: 1;">
        <div class="mobile-enroll-icon-wrap shadow">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span class="small fw-bold mobile-enroll-text">Enroll Now</span>
    </a>

    <a href="<?= htmlspecialchars($basePath) ?>cities.php" class="nav-item text-center text-decoration-none <?= (basename($_SERVER['PHP_SELF']) === 'cities.php') ? 'active-nav-item text-primary' : 'text-muted' ?>" style="flex: 1;">
        <div class="nav-icon-circle">
            <i class="fa-solid fa-building-columns"></i>
        </div>
        <span class="small fw-semibold nav-label">Institutions</span>
    </a>
    <a href="<?= htmlspecialchars($basePath) ?>courses.php" class="nav-item text-center text-decoration-none <?= (basename($_SERVER['PHP_SELF']) === 'courses.php') ? 'active-nav-item text-primary' : 'text-muted' ?>" style="flex: 1;">
        <div class="nav-icon-circle">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <span class="small fw-semibold nav-label">Courses</span>
    </a>
</div>

<!-- jQuery -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- AJAX Forms Processing Script -->
<script>
$(document).ready(function() {
    // Global Modal Pre-fill Handler
    $(document).on('click', '[data-bs-target="#scholarshipFormModal"]', function() {
        var collegeName = $(this).attr('data-college') || '';
        var courseId = $(this).attr('data-course-id') || '';
        var modalTitle = $(this).attr('data-title') || '';
        
        $('#modal_interested_college').val(collegeName);
        $('select[name="faculty"]').val(courseId);

        if (modalTitle) {
            $('#scholarshipFormModal .split-modal-right-title').html(modalTitle);
        } else {
            var btnText = $(this).text().trim().toLowerCase();
            if (btnText.indexOf('scholarship') !== -1) {
                $('#scholarshipFormModal .split-modal-right-title').html('Apply for <span>Scholarship</span> 🎓');
            } else {
                $('#scholarshipFormModal .split-modal-right-title').html('Enrollment <span>Form</span> 🎓');
            }
        }
    });

    // University Details Modal Handler
    $(document).on('click', '[data-bs-target="#uniDetailModal"]', function() {
        var name = $(this).attr('data-name') || '';
        var city = $(this).attr('data-city') || '';
        var region = $(this).attr('data-region') || '';
        var img = $(this).attr('data-img') || '';
        
        var locationDetail = $(this).attr('data-location-detail') || '';
        if (!locationDetail.trim()) {
            locationDetail = city + ', ' + region;
        }
        
        var fees = $(this).attr('data-fees') || '';
        if (!fees.trim()) {
            fees = 'Please contact our counseling team for the latest fee structure, custom package rates, and available academic/merit scholarships for Nepali students.';
        }
        
        var admission = $(this).attr('data-admission') || '';
        if (!admission.trim()) {
            admission = "1. Submit the online enrollment form.\n2. Provide academic transcripts & copy of passport/citizenship.\n3. Participate in direct counseling interview.\n4. Secure provisional admission offer & register.";
        }
        
        $('#modal-uni-name').text(name);
        $('#modal-uni-city').text(city);
        $('#modal-uni-region').text(region);
        $('#modal-uni-img').attr('src', img).attr('alt', name);
        $('#modal-uni-location-detail').text(locationDetail);
        $('#modal-uni-admission').text(admission);
        
        // Associate college name to the apply button inside details modal
        $('#modal-apply-btn').attr('data-college', name);

        // Parse fees and set up the dropdown selection
        $('#modal-fee-select').empty().append('<option value="">-- Select Course --</option>');
        $('#modal-course-fee-display').hide().text('');
        $('#modal-scholarships-display').hide().text('');
        
        var courseFees = {};
        var scholarshipsText = '';
        
        if (fees.trim()) {
            var lines = fees.split('\n');
            var hasCourses = false;
            lines.forEach(function(line) {
                line = line.trim();
                if (!line) return;
                
                if (line.toLowerCase().indexOf('scholarship') !== -1) {
                    scholarshipsText += line + '\n';
                } else if (line.indexOf(':') !== -1) {
                    var colonIdx = line.indexOf(':');
                    var courseName = line.substring(0, colonIdx).trim();
                    var feeVal = line.substring(colonIdx + 1).trim();
                    
                    courseFees[courseName] = feeVal;
                    $('#modal-fee-select').append($('<option></option>').val(courseName).text(courseName));
                    hasCourses = true;
                } else {
                    scholarshipsText += line + '\n';
                }
            });
            
            if (hasCourses) {
                $('#modal-fee-select-container').show();
                $('#modal-uni-fees').hide();
            } else {
                $('#modal-fee-select-container').hide();
                $('#modal-uni-fees').show().text(fees);
            }
            
            if (scholarshipsText.trim()) {
                $('#modal-scholarships-display').show().text(scholarshipsText.trim());
            }
        } else {
            $('#modal-fee-select-container').hide();
            $('#modal-uni-fees').show().text('Please contact our counseling team for latest details.');
        }
        
        $('#modal-fee-select').data('course-fees', courseFees);
    });

    $(document).on('change', '#modal-fee-select', function() {
        var selectedCourse = $(this).val();
        var courseFees = $(this).data('course-fees') || {};
        if (selectedCourse && courseFees[selectedCourse]) {
            $('#modal-course-fee-display').show().html('<strong>Fee structure:</strong> ' + courseFees[selectedCourse]);
        } else {
            $('#modal-course-fee-display').hide().text('');
        }
    });

    // Handle smooth transition from details modal to enrollment modal
    $(document).on('click', '#modal-apply-btn', function() {
        var collegeName = $(this).attr('data-college') || '';
        
        // Hide the details modal
        var detailModal = bootstrap.Modal.getInstance(document.getElementById('uniDetailModal'));
        if (detailModal) {
            detailModal.hide();
        }
        
        // Wait for slide/fade effect to complete, then show the enrollment modal
        setTimeout(function() {
            var enrollModal = new bootstrap.Modal(document.getElementById('scholarshipFormModal'));
            $('#modal_interested_college').val(collegeName);
            $('#scholarshipFormModal .split-modal-right-title').html('Enrollment <span>Form</span> 🎓');
            enrollModal.show();
        }, 350);
    });

    // Contact Form Submission
    $('#contact-form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#submit-contact-form');
        var originalText = $btn.text();
        
        $btn.prop('disabled', true).text('Submitting...');
        
        $.ajax({
            url: '<?= htmlspecialchars($basePath) ?>submit_form.php',
            type: 'POST',
            data: $form.serialize() + '&action=contact',
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).text(originalText);
                if (response.success) {
                    var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                    myModal.show();
                    $form[0].reset();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(originalText);
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Enrollment Form Submission
    $('#enroll_form').on('submit', function(e) {
        e.preventDefault();
        var form = this;
        var formData = new FormData(form);
        formData.append('action', 'enroll');
        var $btn = $('#submit_enroll_form');
        var originalText = $btn.text();
        
        $btn.prop('disabled', true).text('Submitting...');
        
        $.ajax({
            url: '<?= htmlspecialchars($basePath) ?>submit_form.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).text(originalText);
                if (response.success) {
                    // Hide the enrollment modal first
                    bootstrap.Modal.getInstance(document.getElementById('scholarshipFormModal')).hide();
                    
                    // Show success modal
                    var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                    myModal.show();
                    form.reset();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(originalText);
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Newsletter Submission
    $('#subscribe_form').on('submit', function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $('#submit_subscribe_form');
        var originalText = $btn.text();
        
        $btn.prop('disabled', true).text('Subscribing...');
        
        $.ajax({
            url: '<?= htmlspecialchars($basePath) ?>submit_form.php',
            type: 'POST',
            data: $form.serialize() + '&action=subscribe',
            dataType: 'json',
            success: function(response) {
                $btn.prop('disabled', false).text(originalText);
                if (response.success) {
                    var myModal = new bootstrap.Modal(document.getElementById('newsletter_success_modal'));
                    myModal.show();
                    $form[0].reset();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                $btn.prop('disabled', false).text(originalText);
                alert('An error occurred. Please try again.');
            }
        });
    });

    // Auto-popup enrollment form modal if enabled in Admin panel settings
    <?php if (isset($siteSettings['auto_popup_enabled']) && $siteSettings['auto_popup_enabled'] === '1'): ?>
    if (!sessionStorage.getItem("autoPopupClosed")) {
        setTimeout(function() {
            var enrollModalEl = document.getElementById('scholarshipFormModal');
            if (enrollModalEl) {
                var enrollModal = new bootstrap.Modal(enrollModalEl);
                enrollModal.show();
                sessionStorage.setItem("autoPopupClosed", "true");
            }
        }, 1200);
    }
    <?php else: ?>
    if (typeof showEnrollModalOnLoad !== 'undefined' && showEnrollModalOnLoad) {
        var enrollModal = new bootstrap.Modal(document.getElementById('scholarshipFormModal'));
        enrollModal.show();
    }
    <?php endif; ?>

    // Dynamic light color styling for select dropdown placeholders
    function updateSelectPlaceholderColor() {
        $('select.form-select').each(function() {
            $(this).css({'color': '#8c96a3', 'text-shadow': '0 1px 2px rgba(0, 0, 0, 0.08)'});
        });
    }
    updateSelectPlaceholderColor();
    $(document).on('change', 'select.form-select', function() {
        updateSelectPlaceholderColor();
    });
    $('#scholarshipFormModal').on('shown.bs.modal', function() {
        updateSelectPlaceholderColor();
    });
});
</script>


<!-- Floating Quick Contact & Scroll-to-Top -->
<?php
$floatingWa = !empty($siteSettings['contact_whatsapp']) ? $siteSettings['contact_whatsapp'] : (!empty($siteSettings['contact_phone']) ? $siteSettings['contact_phone'] : '9779807765812');
$floatingWaDigits = preg_replace('/[^0-9]/', '', $floatingWa);
if (empty($floatingWaDigits)) {
    $floatingWaDigits = '9779807765812';
}
?>
<a href="https://wa.me/<?= h($floatingWaDigits) ?>?text=Hello%20Vision%20Point,%20I%20would%20like%20to%20inquire%20about%20university%20admissions." target="_blank" rel="noopener noreferrer" class="floating-whatsapp-btn" title="Chat with Admissions Counselor on WhatsApp" aria-label="WhatsApp Chat">
    <i class="fa-brands fa-whatsapp"></i>
</a>
<button type="button" class="floating-back-to-top" id="btnBackToTop" title="Scroll to Top" aria-label="Scroll to Top">
    <i class="fa-solid fa-arrow-up"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnBackToTop = document.getElementById('btnBackToTop');
    if (btnBackToTop) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                btnBackToTop.classList.add('active');
            } else {
                btnBackToTop.classList.remove('active');
            }
        });
        btnBackToTop.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
</script>

</body>
</html>
