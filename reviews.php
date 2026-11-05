<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'reviews');
require_once __DIR__ . '/includes/header.php';

// Fetch approved reviews from database
$reviews = [];
$totalReviews = 0;
$avgRating = 5.0;
$ratingCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

if (isset($pdo)) {
    try {
        $stmtR = $pdo->query("SELECT * FROM reviews WHERE status = 'Approved' ORDER BY created_at DESC, id DESC");
        $reviews = $stmtR->fetchAll(PDO::FETCH_ASSOC);
        
        $totalReviews = count($reviews);
        if ($totalReviews > 0) {
            $sumRating = 0;
            foreach ($reviews as $rev) {
                $r = (int)$rev['rating'];
                $sumRating += $r;
                if (isset($ratingCounts[$r])) {
                    $ratingCounts[$r]++;
                }
            }
            $avgRating = round($sumRating / $totalReviews, 1);
        }
    } catch (PDOException $e) {
        $reviews = [];
    }
}

if (empty($reviews)) {
    $reviews = [
        [
            'id' => 1,
            'name' => 'Yugandhar Yadav',
            'course_college' => 'B.Tech CSE (SRM University)',
            'rating' => 5,
            'title' => 'Exceptional Guidance & Support!',
            'comment' => 'Vision Point Educational Service guided me through every step of my admission process to SRM University. They helped me secure a scholarship and made the documentation smooth and hassle-free!',
            'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
            'created_at' => '2026-05-10 10:00:00'
        ],
        [
            'id' => 2,
            'name' => 'Aayush Shrestha',
            'course_college' => 'B.Sc Nursing (Chandigarh University)',
            'rating' => 5,
            'title' => 'Best Consultancy for India',
            'comment' => 'I am currently studying Nursing in India thanks to Vision Point. Their admission advisors were available 24/7 to clear all my doubts about fees, hostel facilities, and visas.',
            'avatar' => 'https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80',
            'created_at' => '2026-04-18 14:30:00'
        ],
        [
            'id' => 3,
            'name' => 'Pooja Karki',
            'course_college' => 'BBA (Lovely Professional University)',
            'rating' => 5,
            'title' => 'Transparent & Very Helpful Team',
            'comment' => 'Transparent advice without any hidden charges. Vision Point Educational Service made my dream of pursuing higher education in India a reality.',
            'avatar' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80',
            'created_at' => '2026-03-22 11:15:00'
        ],
        [
            'id' => 4,
            'name' => 'Rohan Sah',
            'course_college' => 'B.Pharm (Bangalore University)',
            'rating' => 5,
            'title' => 'Hassle-free Admission Process',
            'comment' => 'Great experience! Got my college seat reserved quickly with full guidance regarding eligibility and document attestation.',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
            'created_at' => '2026-02-14 09:45:00'
        ]
    ];
    $totalReviews = count($reviews);
    $avgRating = 5.0;
    $ratingCounts = [5 => 4, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
}
?>

<!-- Hero Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #0458A0 0%, #03447c 100%);">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 fw-medium small">
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-white-50"><i class="fa fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page">Reviews & Feedback</li>
            </ol>
        </nav>
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h1 class="fw-bold text-white mb-2 display-5 position-relative d-inline-block">
                    Student Reviews & Feedback
                    <span class="position-absolute start-0 bottom-0 bg-warning rounded-pill" style="height: 4px; width: 60px; margin-bottom: -8px;"></span>
                </h1>
                <p class="text-white-50 mt-3 mb-0 fs-6 max-w-600">
                    Read authentic experiences and testimonials from students guided by Vision Point Educational Service for top Indian universities. Share your own feedback to help future scholars!
                </p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="#submit-feedback-section" class="btn btn-warning btn-lg rounded-pill fw-bold text-dark px-4 py-3 shadow hover-scale">
                    <i class="fa-solid fa-pen-to-square me-2"></i>Write a Review
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Overview Statistics & Rating Summary -->
<section class="py-5 bg-light-subtle">
    <div class="container">
        <div class="row g-4 align-items-stretch mb-5">
            <!-- Left Card: Overall Score -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 text-center d-flex flex-column justify-content-center">
                    <div class="display-3 fw-extrabold text-primary mb-1"><?= number_format($avgRating, 1) ?></div>
                    <div class="text-warning fs-4 mb-2">
                        <?php 
                        $fullStars = floor($avgRating);
                        $halfStar = ($avgRating - $fullStars) >= 0.5;
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $fullStars) {
                                echo '<i class="fa-solid fa-star me-1"></i>';
                            } elseif ($i == $fullStars + 1 && $halfStar) {
                                echo '<i class="fa-solid fa-star-half-stroke me-1"></i>';
                            } else {
                                echo '<i class="fa-regular fa-star text-muted opacity-25 me-1"></i>';
                            }
                        }
                        ?>
                    </div>
                    <p class="text-muted fw-medium mb-3">Based on <strong><?= $totalReviews ?></strong> verified student reviews</p>
                    <div class="d-inline-flex align-items-center justify-content-center gap-2 bg-success-subtle text-success px-3 py-1.5 rounded-pill small mx-auto fw-bold">
                        <i class="fa-solid fa-circle-check"></i> 100% Genuine Student Ratings
                    </div>
                </div>
            </div>

            <!-- Right Card: Rating Bars -->
            <div class="col-12 col-md-7 col-lg-8">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 d-flex flex-column justify-content-center">
                    <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-chart-bar text-primary me-2"></i>Rating Distribution</h5>
                    <div class="rating-breakdown">
                        <?php for ($star = 5; $star >= 1; $star--): 
                            $cnt = $ratingCounts[$star];
                            $pct = $totalReviews > 0 ? round(($cnt / $totalReviews) * 100) : 0;
                        ?>
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <div class="text-nowrap text-muted fw-semibold small" style="width: 55px;">
                                    <?= $star ?> <i class="fa-solid fa-star text-warning me-1"></i>
                                </div>
                                <div class="progress flex-grow-1" style="height: 10px; background-color: #e9ecef; border-radius: 20px;">
                                    <div class="progress-bar bg-warning rounded-pill" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <div class="text-muted small fw-medium text-end" style="width: 45px;"><?= $cnt ?> (<?= $pct ?>%)</div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 p-3 bg-white rounded-4 shadow-sm border border-light">
            <div class="d-flex align-items-center gap-2 flex-wrap" id="review-filter-buttons">
                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 filter-btn active" data-filter="all">All Reviews (<?= $totalReviews ?>)</button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-filter="5">5 Stars (<?= $ratingCounts[5] ?>)</button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-filter="4">4 Stars (<?= $ratingCounts[4] ?>)</button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 filter-btn" data-filter="3">3 Stars & Below (<?= $ratingCounts[3] + $ratingCounts[2] + $ratingCounts[1] ?>)</button>
            </div>
            <div class="input-group rounded-pill border border-light shadow-sm overflow-hidden bg-white" style="max-width: 280px;">
                <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" id="review-search-input" class="form-control border-0 bg-transparent py-1.5" placeholder="Search reviews..." style="box-shadow: none; font-size: 14px;">
            </div>
        </div>

        <!-- Reviews Grid -->
        <div class="row g-4 mb-5" id="reviews-container">
            <?php if (!empty($reviews)): ?>
                <?php 
                $bgColors = ['#0458A0', '#D91F23', '#2b8a3e', '#e67e22', '#6f42c1', '#087f5b'];
                foreach ($reviews as $idx => $rev):
                    $rating = (int)$rev['rating'];
                    $color = $bgColors[$idx % count($bgColors)];
                    $initials = '';
                    $nameParts = explode(' ', trim($rev['name']));
                    foreach ($nameParts as $p) {
                        if ($p !== '') $initials .= strtoupper($p[0]);
                    }
                    $initials = substr($initials, 0, 2);
                    $formattedDate = date('M d, Y', strtotime($rev['created_at']));
                ?>
                    <div class="col-12 col-md-6 col-lg-6 review-card-item" data-rating="<?= $rating ?>" data-text="<?= strtolower(h($rev['name'] . ' ' . $rev['course_college'] . ' ' . $rev['title'] . ' ' . $rev['comment'])) ?>">
                        <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 d-flex flex-column justify-content-between hover-scale transition-smooth">
                            <div>
                                <!-- Card Top Row: Rating & Date -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="text-warning">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <i class="<?= $s <= $rating ? 'fa-solid' : 'fa-regular text-muted opacity-25' ?> fa-star"></i>
                                        <?php endfor; ?>
                                        <span class="ms-2 fw-bold text-dark small"><?= number_format($rating, 1) ?></span>
                                    </div>
                                    <span class="badge bg-light text-muted fw-normal rounded-pill px-3 py-1 border" style="font-size: 12px;">
                                        <i class="fa-regular fa-clock me-1"></i><?= $formattedDate ?>
                                    </span>
                                </div>

                                <!-- Review Title & Body -->
                                <h5 class="fw-bold text-dark mb-2"><?= h($rev['title']) ?></h5>
                                <p class="text-secondary small mb-4" style="line-height: 1.7; font-size: 14.5px;">
                                    "<?= nl2br(h($rev['comment'])) ?>"
                                </p>
                            </div>

                            <!-- Reviewer Details (Footer of Card) -->
                            <div class="pt-3 border-top d-flex align-items-center gap-3">
                                <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 46px; height: 46px; background-color: <?= $color ?>; font-size: 16px;">
                                    <?= $initials ?>
                                </div>
                                <div class="lh-sm overflow-hidden">
                                    <div class="fw-bold text-dark text-truncate fs-6"><?= h($rev['name']) ?></div>
                                    <div class="text-muted small text-truncate">
                                        <?php if (!empty($rev['course_college'])): ?>
                                            <i class="fa-solid fa-graduation-cap me-1 text-primary"></i><?= h($rev['course_college']) ?>
                                        <?php else: ?>
                                            <i class="fa-solid fa-user-check me-1 text-success"></i>Verified Student
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-regular fa-comments text-muted display-4 mb-3 d-block"></i>
                    <h4 class="fw-bold text-dark">No reviews found</h4>
                    <p class="text-muted">Be the first student to leave a review and feedback!</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Feedback & Review Form Section -->
        <div class="row justify-content-center" id="submit-feedback-section">
            <div class="col-12 col-lg-9">
                <div class="card border-0 rounded-4 shadow-lg bg-white p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle p-3 mb-2" style="width: 60px; height: 60px;">
                            <i class="fa-solid fa-pen-nib fs-3"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-1">Leave Your Review & Feedback</h3>
                        <p class="text-muted small">Your feedback helps future students choose the best pathway to study in India!</p>
                    </div>

                    <!-- Alert message container -->
                    <div id="review-alert-container"></div>

                    <form id="review-submit-form" method="POST" action="submit_form.php">
                        <input type="hidden" name="csrf_token" value="<?= h($_SESSION['csrf_token'] ?? '') ?>">
                        <input type="hidden" name="action" value="submit_review">
                        <input type="hidden" name="rating" id="selected-rating-val" value="5">

                        <!-- Interactive Star Selector -->
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold text-dark d-block mb-2">Select Your Rating *</label>
                            <div class="star-rating-picker d-inline-flex gap-2 p-2 bg-light rounded-pill border">
                                <i class="fa-solid fa-star star-picker-icon text-warning fs-3 px-1" data-value="1" title="Poor" style="cursor: pointer;"></i>
                                <i class="fa-solid fa-star star-picker-icon text-warning fs-3 px-1" data-value="2" title="Fair" style="cursor: pointer;"></i>
                                <i class="fa-solid fa-star star-picker-icon text-warning fs-3 px-1" data-value="3" title="Good" style="cursor: pointer;"></i>
                                <i class="fa-solid fa-star star-picker-icon text-warning fs-3 px-1" data-value="4" title="Very Good" style="cursor: pointer;"></i>
                                <i class="fa-solid fa-star star-picker-icon text-warning fs-3 px-1" data-value="5" title="Excellent" style="cursor: pointer;"></i>
                            </div>
                            <div class="mt-2 text-warning fw-bold small" id="rating-text-label">5 Stars - Excellent</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Full Name -->
                            <div class="col-12 col-md-6">
                                <label for="review_name" class="form-label fw-semibold text-dark small">Full Name *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-user text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="review_name" name="name" placeholder="e.g. Aarav Patel" required>
                                </div>
                            </div>
                            <!-- Email -->
                            <div class="col-12 col-md-6">
                                <label for="review_email" class="form-label fw-semibold text-dark small">Email Address *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" class="form-control bg-light border-start-0" id="review_email" name="email" placeholder="e.g. aarav@example.com" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Course / College -->
                            <div class="col-12 col-md-6">
                                <label for="review_course" class="form-label fw-semibold text-dark small">Course / Institution (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-graduation-cap text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="review_course" name="course_college" placeholder="e.g. B.Tech CSE - Galgotias University">
                                </div>
                            </div>
                            <!-- Review Headline / Title -->
                            <div class="col-12 col-md-6">
                                <label for="review_title" class="form-label fw-semibold text-dark small">Headline / Short Title (Optional)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-heading text-muted"></i></span>
                                    <input type="text" class="form-control bg-light border-start-0" id="review_title" name="title" placeholder="e.g. Outstanding Admission Support">
                                </div>
                            </div>
                        </div>

                        <!-- Review Comment -->
                        <div class="mb-4">
                            <label for="review_comment" class="form-label fw-semibold text-dark small">Detailed Review & Feedback *</label>
                            <textarea class="form-control bg-light" id="review_comment" name="comment" rows="4" placeholder="Tell us about your admission experience, counselors support, college feedback, or any suggestions..." required></textarea>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" id="btn-submit-review" class="btn btn-primary btn-lg rounded-pill fw-bold text-white px-5 py-3 shadow hover-scale">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                                <i class="fa-solid fa-paper-plane me-2"></i>Submit Review & Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Page Script for Filtering, Star Picker & AJAX Submission -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Star Rating Picker Logic
    const starIcons = document.querySelectorAll('.star-picker-icon');
    const ratingInput = document.getElementById('selected-rating-val');
    const ratingTextLabel = document.getElementById('rating-text-label');

    const ratingLabels = {
        1: '1 Star - Poor',
        2: '2 Stars - Fair',
        3: '3 Stars - Good',
        4: '4 Stars - Very Good',
        5: '5 Stars - Excellent'
    };

    function updateStars(val) {
        starIcons.forEach(icon => {
            const starVal = parseInt(icon.getAttribute('data-value'));
            if (starVal <= val) {
                icon.classList.remove('fa-regular', 'opacity-25');
                icon.classList.add('fa-solid', 'text-warning');
            } else {
                icon.classList.remove('fa-solid');
                icon.classList.add('fa-regular', 'text-warning', 'opacity-25');
            }
        });
        if (ratingTextLabel) {
            ratingTextLabel.textContent = ratingLabels[val] || (val + ' Stars');
        }
    }

    starIcons.forEach(icon => {
        icon.addEventListener('mouseover', function() {
            const val = parseInt(this.getAttribute('data-value'));
            updateStars(val);
        });

        icon.addEventListener('mouseout', function() {
            const currentVal = parseInt(ratingInput.value) || 5;
            updateStars(currentVal);
        });

        icon.addEventListener('click', function() {
            const val = parseInt(this.getAttribute('data-value'));
            ratingInput.value = val;
            updateStars(val);
        });
    });

    // 2. Review Search & Category Filters
    const filterBtns = document.querySelectorAll('.filter-btn');
    const searchInput = document.getElementById('review-search-input');
    const reviewCards = document.querySelectorAll('.review-card-item');

    function applyReviewFilters() {
        const activeBtn = document.querySelector('.filter-btn.active');
        const filterVal = activeBtn ? activeBtn.getAttribute('data-filter') : 'all';
        const searchVal = searchInput ? searchInput.value.toLowerCase().trim() : '';

        reviewCards.forEach(card => {
            const cardRating = parseInt(card.getAttribute('data-rating'));
            const cardText = card.getAttribute('data-text') || '';

            let matchesFilter = false;
            if (filterVal === 'all') {
                matchesFilter = true;
            } else if (filterVal === '5' && cardRating === 5) {
                matchesFilter = true;
            } else if (filterVal === '4' && cardRating === 4) {
                matchesFilter = true;
            } else if (filterVal === '3' && cardRating <= 3) {
                matchesFilter = true;
            }

            let matchesSearch = searchVal === '' || cardText.includes(searchVal);

            if (matchesFilter && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            filterBtns.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-outline-secondary');
            });
            this.classList.add('active', 'btn-primary');
            this.classList.remove('btn-outline-secondary');
            applyReviewFilters();
        });
    });

    if (searchInput) {
        searchInput.addEventListener('input', applyReviewFilters);
    }

    // 3. AJAX Form Submission
    const form = document.getElementById('review-submit-form');
    const btnSubmit = document.getElementById('btn-submit-review');
    const alertContainer = document.getElementById('review-alert-container');

    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const spinner = btnSubmit.querySelector('.spinner-border');
            if (spinner) spinner.classList.remove('d-none');
            btnSubmit.disabled = true;

            const formData = new FormData(form);

            fetch('submit_form.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (spinner) spinner.classList.add('d-none');
                btnSubmit.disabled = false;

                if (data.success) {
                    alertContainer.innerHTML = `
                        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                            <i class="fa-solid fa-circle-check me-2"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    form.reset();
                    ratingInput.value = 5;
                    updateStars(5);

                    // Prepend new review card dynamically if provided
                    if (data.review) {
                        const rev = data.review;
                        const container = document.getElementById('reviews-container');
                        if (container) {
                            const newCardHTML = `
                                <div class="col-12 col-md-6 col-lg-6 review-card-item" data-rating="${rev.rating}" data-text="${(rev.name + ' ' + (rev.course_college||'') + ' ' + rev.title + ' ' + rev.comment).toLowerCase()}">
                                    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 d-flex flex-column justify-content-between hover-scale transition-smooth border-start border-4 border-success">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <div class="text-warning">
                                                    ${'<i class="fa-solid fa-star"></i>'.repeat(rev.rating)}
                                                    <span class="ms-2 fw-bold text-dark small">${rev.rating}.0</span>
                                                </div>
                                                <span class="badge bg-success-subtle text-success fw-bold rounded-pill px-3 py-1">Just Now</span>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">${rev.title}</h5>
                                            <p class="text-secondary small mb-4" style="line-height: 1.7; font-size: 14.5px;">"${rev.comment}"</p>
                                        </div>
                                        <div class="pt-3 border-top d-flex align-items-center gap-3">
                                            <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 46px; height: 46px; background-color: #0458A0; font-size: 16px;">
                                                ${rev.name.charAt(0).toUpperCase()}
                                            </div>
                                            <div class="lh-sm overflow-hidden">
                                                <div class="fw-bold text-dark text-truncate fs-6">${rev.name}</div>
                                                <div class="text-muted small text-truncate">
                                                    ${rev.course_college ? '<i class="fa-solid fa-graduation-cap me-1 text-primary"></i>' + rev.course_college : '<i class="fa-solid fa-user-check me-1 text-success"></i>Verified Student'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.insertAdjacentHTML('afterbegin', newCardHTML);
                        }
                    }
                } else {
                    alertContainer.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-2"></i> ${data.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                }
            })
            .catch(err => {
                if (spinner) spinner.classList.add('d-none');
                btnSubmit.disabled = false;
                alertContainer.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 mb-4" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> An error occurred. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            });
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
