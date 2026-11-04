<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

// Fetch study-in-india page content
$pageData = null;
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = 'study-in-india' LIMIT 1");
        $stmt->execute();
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fallbacks
$pageTitle = $pageData['title'] ?? 'Study in India for Nepali Students';
$heroTitle = $pageData['hero_title'] ?? 'Study in India for Nepali Students';
$heroSubtitle = $pageData['hero_subtitle'] ?? 'Explore accredited universities, in-demand courses, transparent fee structures, and scholarship opportunities for Nepalese students pursuing higher education in India.';
$whyCards = [];
$processSteps = [];

if ($pageData && !empty($pageData['content'])) {
    $content = json_decode($pageData['content'], true);
    $whyCards = $content['why_cards'] ?? [];
    $processSteps = $content['process_steps'] ?? [];
} else {
    // Hardcoded defaults
    $whyCards = [
        ['title' => 'Globally Recognized Degrees', 'icon' => 'fa-certificate', 'text' => 'Degrees from UGC and AICTE recognized Indian universities are approved by regulatory councils in Nepal (NMC, NEC, NNC, NPC).'],
        ['title' => 'Affordable Tuition & Living', 'icon' => 'fa-sack-dollar', 'text' => 'High-quality education with tuition fee structures and living costs significantly more affordable than Western study destinations.'],
        ['title' => 'Cultural & Geographic Proximity', 'icon' => 'fa-handshake', 'text' => 'Familiar culture, climate, food, and hassle-free visa-free travel between Nepal and India make adaptation effortless.'],
        ['title' => 'Extensive Scholarship Programs', 'icon' => 'fa-graduation-cap', 'text' => 'Merit-based university scholarships matching 25% to 100% tuition coverage for students with qualifying +2 and entrance scores.']
    ];
    $processSteps = [
        ['title' => '1. Free Career Counselling', 'text' => 'Consult with our advisors to choose the best-suited course, university, and state.'],
        ['title' => '2. Academic Eligibility Check', 'text' => 'Submit your transcripts (+2/Diploma/Bachelor) for official eligibility verification.'],
        ['title' => '3. Scholarship Allocation', 'text' => 'Apply for available university merit waivers and institutional concessions.'],
        ['title' => '4. Offer Letter & Registration', 'text' => 'Receive your official provisional admission offer and secure hostel accommodation.'],
        ['title' => '5. Pre-Departure & Border Briefing', 'text' => 'Complete travel orientation and border transit document checklists.']
    ];
}

// Fetch Destinations
$destinations = [];
if (isset($pdo)) {
    try {
        $stmtD = $pdo->query('SELECT * FROM destinations ORDER BY display_order ASC, name ASC');
        $destinations = $stmtD->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fetch Course Streams
$courses = [];
if (isset($pdo)) {
    try {
        $stmtC = $pdo->query('SELECT * FROM course_streams ORDER BY display_order ASC, name ASC');
        $courses = $stmtC->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

$faqs = [
    [
        'q' => 'Can Nepali students study in India without a student visa?',
        'a' => 'Yes. Under the 1950 Indo-Nepal Treaty of Peace and Friendship, Nepalese citizens do not require a student visa to study in India. A valid Nepalese citizenship certificate or passport is required for travel and admission.'
    ],
    [
        'q' => 'Are Indian degrees recognized by Nepal councils (NMC, NNC, NEC, NPC)?',
        'a' => 'Yes. Degrees from UGC, AICTE, INC, PCI, and NMC recognized Indian universities are accredited and eligible for licensing examinations conducted by the respective professional councils in Nepal.'
    ],
    [
        'q' => 'What is the average cost of studying in India for Nepali students?',
        'a' => 'Tuition fees vary depending on the stream. For Engineering (B.Tech) and Nursing (B.Sc Nursing), annual tuition generally ranges from INR 1,00,000 to INR 2,50,000. Management (BBA/BCA) ranges from INR 80,000 to INR 1,80,000 per year.'
    ],
    [
        'q' => 'What documents are required for admission into Indian universities?',
        'a' => 'Students need SEE / 10th grade mark sheet & certificate, +2 / 12th grade transcript & provisional certificate, character certificate, copy of citizenship or passport, and recent passport-sized photographs.'
    ],
    [
        'q' => 'Are scholarships available for Nepalese students in Indian universities?',
        'a' => 'Yes. Most partner universities provide 25% to 100% tuition scholarships based on +2 aggregate percentages, entrance scores, or early admission quotas.'
    ],
    [
        'q' => 'What accommodation and food options are available for students?',
        'a' => 'Most universities offer on-campus residential hostels with separate wings for boys and girls, 24/7 security, Wi-Fi, and mess facilities serving vegetarian and non-vegetarian meals suitable for Nepali students.'
    ],
    [
        'q' => 'How does Krishna Education Foundation assist Nepali students?',
        'a' => 'KEF provides personalized course selection, verified university matching, direct admission letters, scholarship processing, hostel reservations, and continuous guardian support with 100% fee transparency.'
    ]
];

// Initialize dynamic SEO metadata with FAQ Schema
$pageSeo = getSeoData('static', null, 'study-in-india');
$pageSeoExtra = ['faqs' => $faqs];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/institutions.php';
?>

<!-- Hero Banner Section -->
<section class="hero-section py-5 d-flex align-items-center bg-light">
    <div class="container py-3">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Study in India' => 'study-in-india.php']) ?>
        <div class="row align-items-center g-5 mt-2">
            <div class="col-12 col-lg-7 text-start">
                <!-- Premium Live Badge -->
                <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-light mb-3 hero-badge-container">
                    <span class="d-inline-block bg-success rounded-circle pulse-dot" style="width: 8px; height: 8px;"></span>
                    <span class="text-primary fw-bold small uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-star text-warning me-1"></i> Admissions Open for 2026 Academic Session
                    </span>
                </div>

                <!-- Primary H1 -->
                <h1 class="hero-title display-5 fw-bold text-primary mb-3">Study in India for Nepali Students</h1>
                <p class="hero-subtitle lead text-muted mb-4" style="line-height: 1.7; font-size: 1.1rem;">
                    <?= h($heroSubtitle) ?>
                </p>
                
                <!-- Hero Title -->
                <h1 class="hero-title fw-extrabold display-4 mb-3">
                    <span class="text-gradient-primary">Study</span> in India
                </h1>
                
                <!-- Hero Subtitle -->
                <p class="hero-subtitle lead text-muted mb-4 fs-5" style="max-width: 600px; line-height: 1.6;">
                    Maximize your potential and reach new academic peaks with our expert support every step of the way.
                </p>
                
                <!-- Quick Info Open Badge -->
                <div class="bg-light border border-light p-3 rounded-4 shadow-sm mb-4 d-inline-block">
                    <h6 class="text-primary fw-bold mb-1"><i class="fa-solid fa-plane-departure text-warning me-2"></i>START ON YOUR JOURNEY WITH US</h6>
                    <h5 class="text-dark fw-bold mb-0">ADMISSION <span class="text-warning">OPEN FOR 2026</span></h5>
                </div>

                <!-- Premium Search Bar -->
                <form action="<?= h($basePath) ?>cities.php" method="GET" class="hero-search-form p-2 bg-white rounded-pill shadow d-flex align-items-center mt-2 max-w-600">
                    <div class="d-flex align-items-center flex-grow-1 ps-3">
                        <i class="fa-solid fa-magnifying-glass text-primary fs-5 me-2"></i>
                        <input type="text" name="search" placeholder="Search programs, colleges, streams..." class="form-control border-0 bg-transparent flex-grow-1" style="box-shadow:none; font-size:16px;">
                    </div>
                    <button type="submit" class="btn btn-secondary px-4 py-3 rounded-pill fw-bold text-white shadow-sm hover-scale d-flex align-items-center gap-2">
                        <span>Search</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>
            </div>
            
            <!-- Graphic Right Side -->
            <div class="col-12 col-lg-5 position-relative mt-4 mt-lg-0">
                <div class="hero-image-wrapper position-relative">
                    <div class="position-absolute bg-warning rounded-circle opacity-10 float-shape-1" style="width: 350px; height: 350px; top: -30px; right: -50px; z-index: -1;"></div>
                    <div class="position-absolute bg-primary rounded-circle opacity-5 float-shape-2" style="width: 250px; height: 250px; bottom: -50px; left: -50px; z-index: -1;"></div>
                    
                    <div class="hero-card-frame position-relative">
                        <?php 
                            $heroImg = 'assets/images/student_hero.png';
                            if (!file_exists(__DIR__ . '/' . $heroImg)) {
                                $heroImg = !empty($siteSettings['hero_banner_image']) ? $siteSettings['hero_banner_image'] : 'assets/images/image1.png';
                            }
                            $heroImgUrl = (strpos($heroImg, 'http') === 0) ? $heroImg : ($basePath . ltrim($heroImg, '/'));
                            $localHeroPath = __DIR__ . '/' . ltrim($heroImg, '/');
                            if (file_exists($localHeroPath)) {
                                $heroImgUrl .= '?v=' . filemtime($localHeroPath);
                            }
                        ?>
                        <img src="<?= h($heroImgUrl) ?>" class="img-fluid w-100 object-fit-cover transition-smooth hero-banner-image rounded-4 shadow" alt="Study in India Banner" style="max-height: 480px; min-height: 380px; object-position: center 20%;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Introduction Section -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <article>
                    <h2 class="fw-bold text-primary mb-3">Study in India with <?= h($siteSettings['site_name'] ?? 'Krishna Education Foundation') ?></h2>
                    <span class="text-muted d-block mb-3 fs-5"><?= h($siteSettings['site_name'] ?? 'Krishna Education Foundation') ?> is Nepal's premier choice for studying in India with admission opportunities and scholarships.</span>
                    <p class="text-muted">Unlock top-quality education at an affordable price. India is home to thousands of highly respected universities, modern research facilities, and a wide range of academic programs in fields like engineering, medicine, business, IT, and the arts. With vibrant cities like Delhi, Bangalore, and Mumbai, students benefit from an active campus life, a multicultural environment, and ample career opportunities.</p>
                    <p class="text-muted">
                        Our experienced team provides personalized support with university selection, admissions, documentation, and student counseling—ensuring a smooth journey from Nepal to India’s top educational institutions.
                    </p>
                    
                    <h4 class="fw-bold text-primary mt-4 mb-3">How We Help You Study in India Successfully</h4>
                    <p class="text-muted">Choosing to study in India is a smart investment in your future, and our professional counselors make the process simple and stress-free. From selecting the right university and course to navigating applications, we support you every step of the way.</p>
                    <p class="text-muted">We also assist with understanding living costs, scholarship options, entrance exams, and post-study opportunities. Our partnerships with reputed Indian institutions and extensive experience allow us to deliver complete, reliable guidance.</p>
                </article>
            </div>
            <div class="col-lg-5">
                <div class="card border border-light p-4 rounded-4 shadow-sm bg-white">
                    <img src="<?= h($basePath) ?>assets/images/student_hero.png?v=<?= filemtime(__DIR__ . '/assets/images/student_hero.png') ?>" alt="Study in India" class="img-fluid rounded-3 mb-3 w-100 object-fit-cover shadow-sm" style="max-height: 280px; object-position: center 20%;">
                    <p class="text-muted small mb-0">
                        <b><?= h($siteSettings['site_name'] ?? 'Krishna Education Foundation') ?></b> is a trusted educational consultancy based in Nepal, dedicated to helping students pursue higher education in India. With over a decade of experience, we specialize in guiding first-time applicants through every step of the process—from choosing the right university and program to handling admissions, documentation, and student support. Our mission is to make quality education accessible, affordable, and achievable for Nepali students by connecting them with top institutions across India.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partner Universities Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Our Partner Universities</h2>
                <p class="text-muted mb-0">Explore all the premium universities and colleges in India where we facilitate direct admission and scholarships.</p>
            </div>
            
            <!-- Quick Search Bar inside Partner Section -->
            <div class="d-flex align-items-center" style="max-width: 400px; width: 100%;">
                <div class="input-group rounded-pill border border-light shadow-sm overflow-hidden bg-light">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="uni-search-india" class="form-control border-0 bg-transparent py-2" placeholder="Search partner universities in India..." style="box-shadow: none; font-size: 14px;">
                </div>
            </div>
        </div>

        <div class="row g-4" id="partner-unis-grid">
            <?php foreach ($institutions as $uni): ?>
                <?php 
                    // Prepend basePath to relative image paths
                    $uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 uni-card-item" data-name="<?= strtolower(h($uni['name'])) ?>">
                    <div class="card h-100 border border-light shadow-sm d-flex flex-column justify-content-between rounded-4 overflow-hidden city-card">
                        <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none text-dark">
                            <div class="position-relative overflow-hidden" style="height: 160px;">
                                <img src="<?= h($uniImg) ?>" class="w-100 h-100 object-fit-cover city-card-img" alt="<?= h($uni['name']) ?>">
                                <?php if (isset($uni['is_top']) && $uni['is_top'] == 1): ?>
                                    <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark shadow-sm d-flex align-items-center gap-1 py-1 px-2 fw-bold rounded-pill" style="z-index: 2; font-size: 10px; border: 1px solid rgba(255, 255, 255, 0.4);">
                                        <i class="fa-solid fa-star text-white"></i> Top Notch
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="p-3">
                                <div class="mb-2">
                                    <span class="badge bg-light text-primary border border-light-subtle rounded-pill"><i class="fa-solid fa-map-marker text-warning me-1"></i><?= h($uni['city']) ?></span>
                                </div>
                                <h6 class="fw-bold text-primary mb-0" style="font-size: 15px; line-height: 1.4;"><?= h($uni['name']) ?></h6>
                            </div>
                        </a>
                        <div class="p-3 pt-0">
                            <button class="btn btn-secondary w-100 rounded-pill py-2 btn-sm fw-semibold enroll-btn" style="font-size: 13px;" data-college="<?= h($uni['name']) ?>" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                                Enroll Now
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Placeholder -->
        <div id="no-results-india" class="text-center py-5" style="display: none;">
            <div class="max-w-400 mx-auto">
                <i class="fa-solid fa-magnifying-glass-minus text-muted display-4 mb-3"></i>
                <h4 class="fw-bold text-primary mb-2">No Matches Found</h4>
                <p class="text-muted small mb-0">We couldn't find any institutions matching your search. Try adjusting your query or typing a different name.</p>
            </div>
        </div>
    </div>
</section>

<!-- What We Offer Section -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center mb-5">
            <h2 class="section-title text-primary fs-3">What We Offer</h2>
            <p class="text-muted max-w-600 mx-auto">We provide comprehensive support for Nepalese students aspiring to study in India, including:</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border border-light rounded-4 shadow-sm p-4 text-center course-card">
                    <div class="bg-primary-subtle text-primary rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-comments-dollar fs-3 text-primary"></i>
                    </div>
                    <h5 class="fw-bold text-primary mb-3">Expert Counseling</h5>
                    <p class="text-muted small mb-0">Get personalized guidance to choose the right course and university that matches your interests, goals, and budget.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border border-light rounded-4 shadow-sm p-4 text-center course-card">
                    <div class="bg-warning-subtle text-warning-emphasis rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-signature fs-3 text-warning"></i>
                    </div>
                    <h5 class="fw-bold text-primary mb-3">Hassle-Free Admission</h5>
                    <p class="text-muted small mb-0">We assist with every step of the admission process, from application to enrollment—making it smooth and stress-free.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border border-light rounded-4 shadow-sm p-4 text-center course-card">
                    <div class="bg-success-subtle text-success rounded-circle p-3 d-inline-flex align-items-center justify-content-center mx-auto mb-4" style="width: 70px; height: 70px;">
                        <i class="fa-solid fa-graduation-cap fs-3 text-success"></i>
                    </div>
                    <h5 class="fw-bold text-primary mb-3">Scholarship & Career</h5>
                    <p class="text-muted small mb-0">Learn about available scholarships, manage your living expenses, and explore future career opportunities in India and beyond.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Explore Streams Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Explore Available Streams</h2>
                <p class="text-muted mb-0">Discover standard academic courses and pathways in India's leading universities.</p>
            </div>
            <a href="courses.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">View All</a>
        </div>
        
        <div class="row g-3 justify-content-center">
            <?php foreach (array_slice($courses, 0, 12) as $course): ?>
                <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                    <a href="course-detail.php?id=<?= $course['id'] ?>" class="text-decoration-none">
                        <div class="card course-card border border-light text-center h-100 p-4 shadow-sm d-flex flex-column align-items-center justify-content-center">
                            <div class="bg-light text-primary rounded-circle p-3 d-flex align-items-center justify-content-center mb-3 course-card-img" style="width: 70px; height: 70px;">
                                <i class="fa-solid <?= h($course['icon'] ?? 'fa-graduation-cap') ?> fs-2 text-primary"></i>
                            </div>
                            <h6 class="fw-bold text-primary mb-0 small text-truncate w-100"><?= h($course['name']) ?></h6>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Visa-free Education and Documents Section -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=60" alt="Nepali students studying in India" class="img-fluid rounded-4 shadow-sm w-100" style="max-height: 400px; object-fit: cover;">
            </div>
            <div class="col-lg-7">
                <h2 class="fw-bold text-primary mb-3">Study in India – For Nepalese Students</h2>
                <p class="text-muted lead">Nepali students can pursue higher education in India without needing a student visa. Thanks to the special relationship between India and Nepal, the process is faster, smoother, and visa-free!</p>
                
                <h4 class="fw-bold text-primary mt-4 mb-3"><i class="fa-solid fa-file-invoice text-warning me-2"></i>Required Documents for Admission:</h4>
                <div class="row g-2">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>SEE Marksheet</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>SEE Character Certificate</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>+2 Transcript</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>+2 Provisional Certificate</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>+2 Character Certificate</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Migration Certificate</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Nepali Citizenship (both sides)</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Passport-size Photo</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Email Address</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Phone Number</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Permanent Address</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-success me-2"></i>Signature on plain paper</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Entrance Exams & Intake Periods -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row g-5">
            <div class="col-md-6">
                <div class="card h-100 border border-light shadow-sm p-4 rounded-4 bg-light">
                    <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-file-signature text-warning me-2"></i>Entrance Exams</h3>
                    <p class="text-muted mb-4">For admission to government and highly competitive technical universities, standard entrance exams are required:</p>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><strong>JEE</strong> – For BTech / Engineering programs</li>
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><strong>NEET</strong> – For MBBS / Medical programs</li>
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><strong>CAT</strong> – For MBA admissions</li>
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><strong>GATE</strong> – For MTech / Postgraduate engineering</li>
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><strong>IELTS / TOEFL</strong> – English proficiency (if applicable)</li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100 border border-light shadow-sm p-4 rounded-4 bg-light">
                    <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-calendar-days text-warning me-2"></i>Intake Periods</h3>
                    <p class="text-muted mb-4">Admissions occur primarily during two intake periods throughout the year:</p>
                    <ul class="list-group list-group-flush bg-transparent">
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted">
                            <h6 class="fw-bold text-primary mb-1"><span class="badge bg-primary text-white me-2">Summer Intake</span> June – July</h6>
                            <p class="small text-muted mb-0">The main intake season for almost all bachelor's, master's, and engineering courses.</p>
                        </li>
                        <li class="list-group-item bg-transparent border-0 ps-0 text-muted mt-3">
                            <h6 class="fw-bold text-primary mb-1"><span class="badge bg-secondary text-white me-2">Winter Intake</span> January</h6>
                            <p class="small text-muted mb-0">Available for limited diploma, certifications, and specialized courses.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Key Reasons to Study in India -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-7">
                <h2 class="fw-bold text-primary mb-3">Key Reasons to Study in India</h2>
                <p class="text-muted">India has emerged as a powerhouse for global education. The main advantages for Nepalese students include:</p>
                <div class="row g-2">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>Top-ranked universities</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>World-recognized degrees</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>Affordable tuition & low cost</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>English-medium programs</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>Scholarships for students</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>No student visa required</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>Diverse student-friendly cities</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>Popular technical courses</li>
                            <li class="list-group-item bg-transparent border-0 ps-0 text-muted small"><i class="fa-solid fa-circle-check text-warning me-2"></i>Abundant job opportunities</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5">
                <img src="https://images.unsplash.com/photo-1498243691581-b145c3f54a5c?w=600&auto=format&fit=crop&q=60" alt="Key Reasons to Study in India" class="img-fluid rounded-4 shadow-sm w-100" style="max-height: 380px; object-fit: cover;">
            </div>
        </div>
    </div>
</section>

<!-- Frequently Asked Questions (FAQs) -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <h2 class="fw-bold text-primary mb-4 text-center">Frequently Asked Questions (FAQs) About Studying in India</h2>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden border border-light" id="accordionFlushExample">
                    <?php foreach ($faqs as $i => $faq): ?>
                        <div class="accordion-item border-bottom border-light">
                            <h2 class="accordion-header" id="flush-heading<?= $i ?>">
                                <button class="accordion-button collapsed fw-semibold text-primary py-3 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse<?= $i ?>" aria-expanded="false" aria-controls="flush-collapse<?= $i ?>">
                                    <?= h($faq['q']) ?>
                                </button>
                            </h2>
                            <div id="flush-collapse<?= $i ?>" class="accordion-collapse collapse" aria-labelledby="flush-heading<?= $i ?>" data-bs-parent="#accordionFlushExample">
                                <div class="accordion-body text-muted bg-white"><?= h($faq['a']) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Top Destinations Section -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Top Destinations Study in India</h2>
                <p class="text-muted mb-0">India's Academic Gems: Explore the Top Study Destinations</p>
            </div>
            <a href="cities.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">View All</a>
        </div>
        
        <div class="city-slider">
            <div class="city-slider-track">
                <!-- First Set of Cards -->
                <?php foreach ($destinations as $dest): ?>
                    <div class="city-card">
                        <a href="city-detail.php?id=<?= $dest['id'] ?>" class="text-decoration-none">
                            <div class="card border-0 overflow-hidden text-center h-100 p-3 shadow-sm">
                                <img src="<?= (strpos($dest['img'], 'http') === 0) ? h($dest['img']) : h($basePath . $dest['img']) ?>" class="rounded-circle object-fit-cover mx-auto mb-3" style="width: 100px; height: 100px;" alt="<?= h($dest['name']) ?>">
                                <div class="card-body p-0">
                                    <h6 class="fw-bold text-primary mb-0"><?= h($dest['name']) ?></h6>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

                <!-- Duplicate for infinite scrolling -->
                <?php foreach ($destinations as $dest): ?>
                    <div class="city-card">
                        <a href="city-detail.php?id=<?= $dest['id'] ?>" class="text-decoration-none">
                            <div class="card border-0 overflow-hidden text-center h-100 p-3 shadow-sm">
                                <img src="<?= (strpos($dest['img'], 'http') === 0) ? h($dest['img']) : h($basePath . $dest['img']) ?>" class="rounded-circle object-fit-cover mx-auto mb-3" style="width: 100px; height: 100px;" alt="<?= h($dest['name']) ?>">
                                <div class="card-body p-0">
                                    <h6 class="fw-bold text-primary mb-0"><?= h($dest['name']) ?></h6>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <style>
    .city-slider {
        width: 100% !important;
        overflow: hidden !important;
        position: relative !important;
        padding: 10px 0 !important;
        display: block !important;
    }

    .city-slider-track {
        display: flex !important;
        flex-wrap: nowrap !important;
        width: max-content !important;
        gap: 20px !important;
        will-change: transform !important;
        animation: cityScroll 35s linear infinite !important;
    }

    .city-slider:hover .city-slider-track {
        animation-play-state: paused !important;
    }

    .city-card {
        flex: 0 0 200px !important;
        width: 200px !important;
        min-width: 200px !important;
        max-width: 200px !important;
        transition: transform 0.3s ease !important;
    }

    .city-card:hover {
        transform: translateY(-3px) !important;
    }

    @media (max-width: 992px) {
        .city-card {
            flex: 0 0 170px !important;
            width: 170px !important;
            min-width: 170px !important;
            max-width: 170px !important;
        }
    }

    @media (max-width: 768px) {
        .city-card {
            flex: 0 0 140px !important;
            width: 140px !important;
            min-width: 140px !important;
            max-width: 140px !important;
        }
    }

    @keyframes cityScroll {
        0% {
            transform: translate3d(-50%, 0, 0);
        }
        100% {
            transform: translate3d(0, 0, 0);
        }
    }
    </style>
</section>

<!-- Sticky Inquire / Sidebar Section (Sidebar Widget overlay) -->
<section class="py-5 bg-white border-top border-light">
    <div class="container">
        <div class="card border-0 bg-primary text-white p-5 rounded-4 shadow d-flex flex-row flex-wrap justify-content-between align-items-center gap-4">
            <div>
                <h3 class="fw-bold mb-2 text-white">Get Counselled Today</h3>
                <p class="mb-0 text-white-50 max-w-600">Reach out directly to find out about available seats, intake deadlines, and tuition waivers in India.</p>
            </div>
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <button class="btn btn-secondary py-3 px-4 fw-bold rounded-pill" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                    <i class="fa-solid fa-paper-plane me-2"></i>Apply for Scholarship
                </button>
                <div class="text-start">
                    <div class="text-white-50 small">OR call counselor at</div>
                    <div class="fw-bold fs-5 text-warning"><?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Functionality Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('uni-search-india');
    var cardItems = document.querySelectorAll('#partner-unis-grid .uni-card-item');
    var noResults = document.getElementById('no-results-india');

    function filterUniversities() {
        var query = searchInput.value.toLowerCase().trim();
        var visibleCount = 0;

        cardItems.forEach(function(item) {
            var name = item.getAttribute('data-name');
            if (name.includes(query)) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterUniversities);
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
