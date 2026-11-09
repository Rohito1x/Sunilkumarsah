<?php
$dbDestinations = [];
$dbStreams = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query('SELECT name FROM destinations ORDER BY display_order ASC, name ASC');
        $dbDestinations = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        $stmt2 = $pdo->query('SELECT name FROM course_streams ORDER BY display_order ASC, name ASC');
        $dbStreams = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        // Fallback
    }
}
?>
<!-- ================= MODALS ================= -->

<!-- Enrollment Form Modal -->
<div class="modal fade" id="scholarshipFormModal" tabindex="-1" aria-labelledby="enrollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered split-modal-dialog">
        <div class="modal-content split-modal-content border-0 animate-fade-in">
            <div class="split-modal-row row g-0">
                <!-- Left Sidebar -->
                <div class="col-md-5 split-modal-left">
                    <div>
                        <div class="split-modal-left-title"><span>Transform</span> your education</div>
                        <p class="split-modal-left-subtitle mb-2">Fill out the form below, and we'll get back to you as soon as possible.</p>
                    </div>
                    <div class="my-2 text-center w-100 flex-grow-1 d-flex align-items-center justify-content-center">
                        <?php
                        $modalImgPath = 'assets/images/student_hero.png';
                        if (!file_exists(__DIR__ . '/../' . $modalImgPath)) {
                            $modalImgPath = 'assets/images/image1.png';
                        }
                        $modalLocalFile = __DIR__ . '/../' . $modalImgPath;
                        $modalImgUrl = $basePath . $modalImgPath;
                        if (file_exists($modalLocalFile)) {
                            $modalImgUrl .= '?v=' . filemtime($modalLocalFile);
                        }
                        ?>
                        <img src="<?= h($modalImgUrl) ?>" alt="Transform Your Education" class="split-modal-left-img">
                    </div>
                    <div class="mt-2 text-center">
                        <?php
                        $logoPath = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo1.png';
                        $logoUrl = (strpos($logoPath, 'http') === 0) ? $logoPath : $basePath . $logoPath;
                        if (file_exists(__DIR__ . '/../' . $logoPath) || (strpos($logoPath, 'http') === 0)): ?>
                            <img src="<?= h($logoUrl) ?>" alt="Vision Point Logo" class="img-fluid rounded shadow-sm" style="max-height: 55px; width: auto; object-fit: contain; border: none;">
                        <?php else: ?>
                            <i class="fa-solid fa-graduation-cap fs-2 text-primary"></i>
                        <?php endif; ?>
                        <div class="fw-bold text-primary mt-1 small" style="font-size: 12px;"><?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?></div>
                    </div>
                </div>
                
                <!-- Right Form Area -->
                <div class="col-md-7 split-modal-right">
                    <button type="button" class="split-modal-close-btn" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    
                    <!-- Mobile Compact Student & Brand Highlight -->
                    <div class="d-md-none d-flex align-items-center gap-3 p-2 mb-3 bg-light rounded-3 border">
                        <img src="<?= h($modalImgUrl) ?>" alt="Vision Point Student" class="rounded-circle shadow-sm object-fit-cover" style="width: 48px; height: 48px; object-position: center 20%; flex-shrink: 0;">
                        <div>
                            <div class="fw-bold text-primary small" style="line-height: 1.2;"><?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?></div>
                            <div class="text-muted" style="font-size: 11px;">Your bright future starts here</div>
                        </div>
                    </div>
                    
                    <h4 class="split-modal-right-title">Enrollment <span>Form</span> 🎓</h4>
                    
                    <form id="enroll_form">
                        <input type="hidden" name="csrf_token" value="<?= h(class_exists('Csrf') ? Csrf::getToken() : ($_SESSION['csrf_token'] ?? '')) ?>">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 form-group">
                                <label>Fullname <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="Enter your Full name" required>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" placeholder="Address">
                            </div>
                            
                            <div class="col-12 col-sm-6 form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="example@email.com" required>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" class="form-control" placeholder="Phone Number" required>
                            </div>
                            
                            <div class="col-12 col-sm-6 form-group">
                                <label>Interested Country <span class="text-danger">*</span></label>
                                <select name="interested_country" class="form-select" required>
                                    <option value="">Select Interested Country</option>
                                    <option value="India" selected>India (Top Universities)</option>
                                    <option value="Australia">Australia</option>
                                    <option value="UK">United Kingdom (UK)</option>
                                    <option value="Canada">Canada</option>
                                    <option value="US">United States (USA)</option>
                                    <option value="Bangladesh">Bangladesh</option>
                                    <option value="Europe">Europe</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Interested City</label>
                                <select name="interested_city" class="form-select">
                                    <option value="">Select City</option>
                                    <?php if (!empty($dbDestinations)): ?>
                                        <?php foreach ($dbDestinations as $dest): ?>
                                            <option value="<?= h($dest) ?>"><?= h($dest) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Bangalore (Bengaluru)">Bangalore (Bengaluru)</option>
                                        <option value="Delhi (Delhi NCR)">Delhi (Delhi NCR)</option>
                                        <option value="Pune">Pune</option>
                                        <option value="Coimbatore">Coimbatore</option>
                                        <option value="Dehradun">Dehradun</option>
                                        <option value="Mumbai">Mumbai</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-12 col-sm-6 form-group">
                                <label>Interested College</label>
                                <input type="text" name="interested_college" id="modal_interested_college" class="form-control" placeholder="Select Interested College">
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Courses <span class="text-danger">*</span></label>
                                <select name="faculty" class="form-select" required>
                                    <option value="">Select Interested Course</option>
                                    <?php if (!empty($dbStreams)): ?>
                                        <?php foreach ($dbStreams as $stream): ?>
                                            <option value="<?= h($stream) ?>"><?= h($stream) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Nursing">Nursing</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Pharmacy">Pharmacy</option>
                                        <option value="Management">Management</option>
                                        <option value="Computer Application / IT">Computer Application / IT</option>
                                        <option value="Dental">Dental</option>
                                        <option value="Medical">Medical</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <div class="col-12 col-sm-6 form-group">
                                <label>Level <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="">Select Level</option>
                                    <option value="Undergraduate" selected>Undergraduate (Bachelor's)</option>
                                    <option value="Diploma">Diploma / CTEVT</option>
                                    <option value="Postgraduate">Postgraduate (Master's)</option>
                                    <option value="Doctorate">Doctorate / Ph.D / MD</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Interested Program</label>
                                <input type="text" name="interested_program" class="form-control" placeholder="Select Interested Program">
                            </div>
                            
                            <div class="col-12 col-sm-6 form-group">
                                <label>Highest Level of Education <span class="text-danger">*</span></label>
                                <select name="highest_level_of_education" class="form-select" required>
                                    <option value="">Select Highest Level of Education</option>
                                    <option value="+2 / Higher Secondary (+2 / NEB / CBSE)" selected>+2 / Higher Secondary (Grade 12 / NEB / CBSE)</option>
                                    <option value="SEE / Class 10">SEE / Class 10 (Secondary)</option>
                                    <option value="Undergraduate / Bachelor's">Undergraduate / Bachelor's</option>
                                    <option value="Diploma / CTEVT">Diploma / CTEVT</option>
                                    <option value="Postgraduate / Master's">Postgraduate / Master's</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Percentage/GPA/CGPA <span class="text-danger">*</span></label>
                                <input type="text" name="percentage" class="form-control" placeholder="Percentage/GPA/CGPA" required>
                            </div>
                            
                            <!-- Optional / Supporting fields (collapsed or small) -->
                            <div class="col-12 col-sm-6 form-group">
                                <label>Passport Size Photo <span class="text-muted">(Optional)</span></label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Any Message / Query</label>
                                <textarea name="message" class="form-control" rows="1" placeholder="Optional query..."></textarea>
                            </div>
                            
                            <div class="col-12 mt-4 text-end">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" id="submit_enroll_form" class="btn btn-secondary rounded-pill px-5">Submit Form</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Form Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-5">
                <i class="fa-solid fa-circle-check text-success fs-1 mb-3"></i>
                <h3 class="fw-bold text-primary mb-2">Thank You!</h3>
                <p class="text-muted mb-4">Your inquiry has been successfully recorded. Our education counselor will get back to you shortly.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Newsletter Success Modal -->
<div class="modal fade" id="newsletter_success_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body text-center p-5">
                <i class="fa-solid fa-envelope-open-text text-primary fs-1 mb-3"></i>
                <h3 class="fw-bold text-primary mb-2">Subscribed!</h3>
                <p class="text-muted mb-4">Thank you for subscribing to our newsletter. You will receive updates about Indian institutions, admission deadlines, and scholarship details.</p>
                <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- University Detail Modal -->
<div class="modal fade" id="uniDetailModal" tabindex="-1" aria-labelledby="uniDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #102e48 0%, #18446c 100%); border-bottom: none;">
                <h5 class="modal-title fw-bold" id="uniDetailModalLabel">Institution Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row align-items-center mb-4">
                    <div class="col-12 col-md-4 text-center mb-3 mb-md-0">
                        <img id="modal-uni-img" src="" class="img-fluid rounded border p-2" style="max-height: 140px; object-fit: contain; background-color: #f8f9fa;" alt="University Logo">
                    </div>
                    <div class="col-12 col-md-8">
                        <h3 id="modal-uni-name" class="fw-bold text-primary mb-2" style="font-size: 24px;"></h3>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <span class="badge bg-light text-primary rounded-pill border"><i class="fa-solid fa-map-marker text-warning me-1"></i><span id="modal-uni-city"></span></span>
                            <span class="badge bg-light text-secondary rounded-pill border"><i class="fa-solid fa-earth-asia text-info me-1"></i><span id="modal-uni-region"></span></span>
                        </div>
                        <div class="p-2 px-3 bg-light rounded-3 d-inline-block w-100">
                            <p class="text-muted small mb-0"><i class="fa-solid fa-location-dot text-danger me-1"></i> <strong class="text-dark">Address:</strong> <span id="modal-uni-location-detail" class="text-secondary"></span></p>
                        </div>
                    </div>
                </div>
                
                <div class="border-top pt-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100 border border-light-subtle">
                                <h6 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="fa-solid fa-indian-rupee-sign text-warning fs-5 me-2"></i>Fees & Scholarships</h6>
                                
                                <!-- Dynamic Course Selection for Fees -->
                                <div id="modal-fee-select-container" style="display: none;" class="mb-3">
                                    <label class="form-label small text-muted">Select Course to View Fee</label>
                                    <select id="modal-fee-select" class="form-select form-select-sm rounded-pill"></select>
                                    <div id="modal-course-fee-display" class="alert alert-info py-2 px-3 small mt-3 mb-0" style="display: none;"></div>
                                </div>
                                
                                <div id="modal-uni-fees" class="text-muted small" style="white-space: pre-line; line-height: 1.6;"></div>
                                
                                <!-- Scholarship Info Display -->
                                <div id="modal-scholarships-display" class="text-muted small mt-3 pt-3 border-top" style="white-space: pre-line; line-height: 1.6; display: none;"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="p-3 bg-light rounded-4 h-100 border border-light-subtle">
                                <h6 class="fw-bold text-primary mb-3 d-flex align-items-center"><i class="fa-solid fa-clipboard-list text-info fs-5 me-2"></i>Admission Process</h6>
                                <div id="modal-uni-admission" class="text-muted small" style="white-space: pre-line; line-height: 1.6;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 d-flex justify-content-between p-3 px-4">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                <button type="button" id="modal-apply-btn" class="btn btn-secondary btn-sm rounded-pill px-4 hover-scale d-flex align-items-center gap-2 text-white">
                    <span>Enroll / Apply Now</span>
                    <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Global Submit Review & Feedback Modal -->
<div class="modal fade" id="submitReviewModal" tabindex="-1" aria-labelledby="submitReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-primary text-white py-3 px-4">
                <h5 class="modal-title fw-bold text-white mb-0" id="submitReviewModalLabel">
                    <i class="fa-solid fa-star text-warning me-2"></i>Write a Review & Feedback
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 bg-white">
                <div id="modal-review-alert-container"></div>
                <form id="modal-review-form" method="POST" action="<?= h($basePath ?? '/') ?>submit_form.php">
                    <input type="hidden" name="csrf_token" value="<?= h(class_exists('Csrf') ? Csrf::getToken() : ($_SESSION['csrf_token'] ?? '')) ?>">
                    <input type="hidden" name="action" value="submit_review">
                    <input type="hidden" name="rating" id="modal-selected-rating-val" value="5">

                    <!-- Interactive Star Rating Picker -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-bold text-dark d-block mb-2">Select Your Rating *</label>
                        <div class="star-rating-picker d-inline-flex gap-2 p-2 bg-light rounded-pill border">
                            <i class="fa-solid fa-star modal-star-picker-icon text-warning fs-3 px-1" data-value="1" title="Poor" style="cursor: pointer;"></i>
                            <i class="fa-solid fa-star modal-star-picker-icon text-warning fs-3 px-1" data-value="2" title="Fair" style="cursor: pointer;"></i>
                            <i class="fa-solid fa-star modal-star-picker-icon text-warning fs-3 px-1" data-value="3" title="Good" style="cursor: pointer;"></i>
                            <i class="fa-solid fa-star modal-star-picker-icon text-warning fs-3 px-1" data-value="4" title="Very Good" style="cursor: pointer;"></i>
                            <i class="fa-solid fa-star modal-star-picker-icon text-warning fs-3 px-1" data-value="5" title="Excellent" style="cursor: pointer;"></i>
                        </div>
                        <div class="mt-2 text-warning fw-bold small" id="modal-rating-text-label">5 Stars - Excellent</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Full Name *</label>
                            <input type="text" class="form-control bg-light" name="name" placeholder="e.g. Aarav Patel" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Email Address *</label>
                            <input type="email" class="form-control bg-light" name="email" placeholder="e.g. aarav@example.com" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Course / Institution (Optional)</label>
                            <input type="text" class="form-control bg-light" name="course_college" placeholder="e.g. B.Tech CSE - Galgotias University">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Headline / Title (Optional)</label>
                            <input type="text" class="form-control bg-light" name="title" placeholder="e.g. Outstanding Support">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small">Detailed Review & Feedback *</label>
                        <textarea class="form-control bg-light" name="comment" rows="4" placeholder="Share your experience with Vision Point Educational Service..." required></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" id="modal-btn-submit-review" class="btn btn-primary btn-lg rounded-pill fw-bold text-white px-5 py-2.5 shadow hover-scale">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            <i class="fa-solid fa-paper-plane me-2"></i>Submit Review & Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Global Payment Receipt Submission Modal -->
<div class="modal fade" id="paymentReceiptModal" tabindex="-1" aria-labelledby="paymentReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header py-3 px-4 text-white" style="background: linear-gradient(135deg, #0458A0 0%, #022340 100%);">
                <div>
                    <h5 class="modal-title fw-bold text-white mb-0" id="paymentReceiptModalLabel">
                        <i class="fa-solid fa-receipt text-warning me-2"></i>Send Payment Receipt / Voucher
                    </h5>
                    <small class="text-white-50">Upload your payment proof in <strong>.jpg, .jpeg, .pdf, or .png</strong> format along with your location.</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 p-md-5 bg-white">
                <div id="payment-receipt-alert-container"></div>

                <form id="payment-receipt-form" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= h(class_exists('Csrf') ? Csrf::getToken() : ($_SESSION['csrf_token'] ?? '')) ?>">
                    <input type="hidden" name="action" value="submit_payment_receipt">

                    <!-- Payer Information -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Student / Payer Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-user"></i></span>
                                <input type="text" class="form-control bg-light border-start-0" name="full_name" placeholder="Full name of student or parent" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Phone / WhatsApp Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" class="form-control bg-light border-start-0" name="phone_number" placeholder="e.g. 9807765812" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-envelope"></i></span>
                                <input type="email" class="form-control bg-light border-start-0" name="email" placeholder="e.g. student@example.com" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <!-- LOCATION OPTION -->
                            <label class="form-label fw-semibold text-dark small">Location / Branch <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-location-dot text-danger"></i></span>
                                <select name="location" id="receipt-location-select" class="form-select bg-light border-start-0" required>
                                    <option value="">-- Choose Location / Nearest Branch --</option>
                                    <option value="Biratnagar (Head Office - Main Road)">Biratnagar (Head Office - Main Road)</option>
                                    <option value="Kathmandu (Counseling & Processing)">Kathmandu (Counseling & Processing)</option>
                                    <option value="Janakpur (Branch Office)">Janakpur (Branch Office)</option>
                                    <option value="Dharan / Itahari">Dharan / Itahari</option>
                                    <option value="Chitwan (Bharatpur)">Chitwan (Bharatpur)</option>
                                    <option value="Pokhara">Pokhara</option>
                                    <option value="Butwal">Butwal</option>
                                    <option value="Bhadrapur / Birtamode">Bhadrapur / Birtamode</option>
                                    <option value="India (Direct University Transfer)">India (Direct University Transfer)</option>
                                    <option value="Online / Other Location">Online / Other Location</option>
                                </select>
                            </div>
                            <input type="text" name="custom_location" id="receipt-custom-location" class="form-control bg-light mt-2" placeholder="Please specify your city / district" style="display: none;">
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Amount Paid (NPR / INR) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted fw-bold">Rs.</span>
                                <input type="number" step="0.01" min="1" class="form-control bg-light border-start-0 font-monospace fw-bold" name="amount" placeholder="e.g. 25000" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Payment Method / Deposited To <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-building-columns"></i></span>
                                <select name="payment_method" class="form-select bg-light border-start-0" required>
                                    <option value="Fonepay / Mobile Banking QR" selected>Fonepay / Mobile Banking QR Scanner</option>
                                    <option value="Citizens Bank International Ltd.">Citizens Bank International Ltd.</option>
                                    <option value="Siddhartha Bank Ltd.">Siddhartha Bank Ltd.</option>
                                    <option value="Bank Transfer (ConnectIPS / Wire)">Bank Transfer (ConnectIPS / Wire)</option>
                                    <option value="eSewa / Khalti Wallet">eSewa / Khalti Digital Wallet</option>
                                    <option value="Cash Deposit at Office Counter">Cash Deposit at Office Counter</option>
                                    <option value="Other Method">Other Payment Method</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Transaction / Reference ID (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-hashtag"></i></span>
                                <input type="text" class="form-control bg-light border-start-0 font-monospace" name="transaction_id" placeholder="e.g. Fonepay Trace ID / UTR / Voucher No.">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold text-dark small">Course / Remarks (Optional)</label>
                            <input type="text" class="form-control bg-light" name="remarks" placeholder="e.g. Admission fee for B.Tech / Nursing">
                        </div>
                    </div>

                    <!-- Payment Receipt File Upload (.jpg, .jpeg, .pdf, .png) -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-paperclip text-primary me-1"></i> Attach Payment Receipt File <span class="text-danger">*</span></span>
                            <span class="badge bg-light text-primary border" style="font-size: 11px;">.jpg, .jpeg, .png, .pdf</span>
                        </label>
                        <div class="border border-2 border-dashed rounded-4 p-3 text-center bg-light position-relative hover-lift transition-smooth">
                            <input type="file" name="receipt_file" id="receipt-file-input" class="form-control" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" required>
                            <div class="form-text small text-muted mt-2">
                                <i class="fa-solid fa-circle-check text-success me-1"></i>Allowed formats: <strong>JPG, JPEG, PNG, PDF</strong> (Max size: 10 MB).
                            </div>

                            <!-- Live Attachment Preview Container -->
                            <div id="receipt-file-preview-container" class="mt-3 p-3 bg-white rounded-3 border d-none">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div id="receipt-preview-icon" class="fs-2 text-danger">
                                            <i class="fa-solid fa-file-pdf"></i>
                                        </div>
                                        <img id="receipt-preview-image" src="" alt="Receipt Preview" class="rounded border p-1 d-none" style="max-height: 60px; max-width: 60px; object-fit: contain;">
                                        <div class="text-start">
                                            <div id="receipt-preview-filename" class="fw-bold small text-dark text-break">filename.pdf</div>
                                            <div id="receipt-preview-filesize" class="text-muted extra-small">0 KB</div>
                                        </div>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="fa-solid fa-check me-1"></i>Valid File
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 pt-2 border-top">
                        <?php
                            $waNumber = $siteSettings['contact_whatsapp'] ?? '9807765812';
                            $waDigits = preg_replace('/\D+/', '', $waNumber);
                            if (strlen($waDigits) === 10 && strpos($waDigits, '9') === 0) {
                                $waDigits = '977' . $waDigits;
                            }
                        ?>
                        <a href="https://wa.me/<?= h($waDigits) ?>?text=<?= urlencode('Hello Vision Point, I have deposited fees and would like to send my payment receipt.') ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline-success rounded-pill px-4 py-2.5 fw-semibold btn-sm w-100 w-sm-auto">
                            <i class="fa-brands fa-whatsapp me-2 fs-6"></i>Or Send via WhatsApp
                        </a>

                        <button type="submit" id="btn-submit-payment-receipt" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow hover-scale w-100 w-sm-auto">
                            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                            <i class="fa-solid fa-paper-plane me-2"></i>Send Payment Receipt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Location option "Other" toggle
    const receiptLocSelect = document.getElementById('receipt-location-select');
    const receiptCustomLoc = document.getElementById('receipt-custom-location');
    if (receiptLocSelect && receiptCustomLoc) {
        receiptLocSelect.addEventListener('change', function() {
            if (this.value === 'Online / Other Location') {
                receiptCustomLoc.style.display = 'block';
                receiptCustomLoc.required = true;
                receiptCustomLoc.focus();
            } else {
                receiptCustomLoc.style.display = 'none';
                receiptCustomLoc.required = false;
            }
        });
    }

    // 2. Receipt file preview (Image vs PDF)
    const receiptFileInput = document.getElementById('receipt-file-input');
    const previewContainer = document.getElementById('receipt-file-preview-container');
    const previewIcon = document.getElementById('receipt-preview-icon');
    const previewImage = document.getElementById('receipt-preview-image');
    const previewFilename = document.getElementById('receipt-preview-filename');
    const previewFilesize = document.getElementById('receipt-preview-filesize');

    if (receiptFileInput && previewContainer) {
        receiptFileInput.addEventListener('change', function() {
            const file = this.files && this.files[0];
            if (!file) {
                previewContainer.classList.add('d-none');
                return;
            }

            const fileName = file.name;
            const fileSize = (file.size / 1024).toFixed(1) + ' KB';
            const fileExt = fileName.split('.').pop().toLowerCase();

            previewFilename.textContent = fileName;
            previewFilesize.textContent = fileSize;
            previewContainer.classList.remove('d-none');

            if (['jpg', 'jpeg', 'png'].includes(fileExt)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.classList.remove('d-none');
                    previewIcon.classList.add('d-none');
                };
                reader.readAsDataURL(file);
            } else if (fileExt === 'pdf') {
                previewImage.classList.add('d-none');
                previewIcon.classList.remove('d-none');
                previewIcon.innerHTML = '<i class="fa-solid fa-file-pdf text-danger"></i>';
            } else {
                previewImage.classList.add('d-none');
                previewIcon.classList.remove('d-none');
                previewIcon.innerHTML = '<i class="fa-solid fa-file-lines text-primary"></i>';
            }
        });
    }

    // 3. Payment Receipt AJAX submission
    const receiptForm = document.getElementById('payment-receipt-form');
    if (receiptForm) {
        receiptForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-submit-payment-receipt');
            const alertBox = document.getElementById('payment-receipt-alert-container');
            const spinner = btn.querySelector('.spinner-border');

            if (spinner) spinner.classList.remove('d-none');
            btn.disabled = true;

            const formData = new FormData(receiptForm);

            fetch('<?= h($basePath ?? '/') ?>submit_form.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (spinner) spinner.classList.add('d-none');
                btn.disabled = false;
                if (data.success) {
                    alertBox.innerHTML = '<div class="alert alert-success rounded-3 mb-4"><i class="fa-solid fa-circle-check me-2"></i><strong>Success!</strong> ' + data.message + '</div>';
                    receiptForm.reset();
                    if (previewContainer) previewContainer.classList.add('d-none');
                    if (receiptCustomLoc) receiptCustomLoc.style.display = 'none';
                    setTimeout(() => {
                        const modalEl = document.getElementById('paymentReceiptModal');
                        if (modalEl && typeof bootstrap !== 'undefined') {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                    }, 4000);
                } else {
                    alertBox.innerHTML = '<div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>' + data.message + '</div>';
                }
            })
            .catch(() => {
                if (spinner) spinner.classList.add('d-none');
                btn.disabled = false;
                alertBox.innerHTML = '<div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>Submission failed. Please check your network connection and try again.</div>';
            });
        });
    }

    const modalStars = document.querySelectorAll('.modal-star-picker-icon');
    const modalRatingInput = document.getElementById('modal-selected-rating-val');
    const modalRatingLabel = document.getElementById('modal-rating-text-label');

    const labels = { 1: '1 Star - Poor', 2: '2 Stars - Fair', 3: '3 Stars - Good', 4: '4 Stars - Very Good', 5: '5 Stars - Excellent' };

    function setModalStars(val) {
        modalStars.forEach(s => {
            const v = parseInt(s.getAttribute('data-value'));
            if (v <= val) {
                s.classList.remove('fa-regular', 'opacity-25');
                s.classList.add('fa-solid', 'text-warning');
            } else {
                s.classList.remove('fa-solid');
                s.classList.add('fa-regular', 'text-warning', 'opacity-25');
            }
        });
        if (modalRatingLabel) modalRatingLabel.textContent = labels[val] || (val + ' Stars');
    }

    modalStars.forEach(s => {
        s.addEventListener('mouseover', function() { setModalStars(parseInt(this.getAttribute('data-value'))); });
        s.addEventListener('mouseout', function() { setModalStars(parseInt(modalRatingInput.value) || 5); });
        s.addEventListener('click', function() {
            const val = parseInt(this.getAttribute('data-value'));
            modalRatingInput.value = val;
            setModalStars(val);
        });
    });

    const modalForm = document.getElementById('modal-review-form');
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('modal-btn-submit-review');
            const alertBox = document.getElementById('modal-review-alert-container');
            const spinner = btn.querySelector('.spinner-border');

            if (spinner) spinner.classList.remove('d-none');
            btn.disabled = true;

            fetch('<?= h($basePath ?? '/') ?>submit_form.php', {
                method: 'POST',
                body: new FormData(modalForm)
            })
            .then(res => res.json())
            .then(data => {
                if (spinner) spinner.classList.add('d-none');
                btn.disabled = false;
                if (data.success) {
                    alertBox.innerHTML = '<div class="alert alert-success rounded-3 mb-4"><i class="fa-solid fa-circle-check me-2"></i>' + data.message + '</div>';
                    modalForm.reset();
                    modalRatingInput.value = 5;
                    setModalStars(5);
                    setTimeout(() => {
                        window.location.href = '<?= h($basePath ?? '/') ?>reviews.php';
                    }, 1200);
                } else {
                    alertBox.innerHTML = '<div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>' + data.message + '</div>';
                }
            })
            .catch(() => {
                if (spinner) spinner.classList.add('d-none');
                btn.disabled = false;
                alertBox.innerHTML = '<div class="alert alert-danger rounded-3 mb-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>Submission failed. Please try again.</div>';
            });
        });
    }
});
</script>
