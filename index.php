<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'home');
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/institutions.php';

// Fetch data from database
$destinations = [];
$courses = [];
$guides = [];
$funFacts = [];
$countries = [];
$teamMembers = [];

if (isset($pdo)) {
    try {
        $stmtD = $pdo->query('SELECT * FROM destinations ORDER BY display_order ASC, name ASC');
        $destinations = $stmtD->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    try {
        $stmtC = $pdo->query('SELECT * FROM course_streams ORDER BY display_order ASC, name ASC');
        $courses = $stmtC->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    try {
        $stmtG = $pdo->query("SELECT id, title, publish_date as date, image_path as img, slug, summary as `desc` FROM guides WHERE status = 'published' ORDER BY publish_date DESC, id DESC");
        $guides = $stmtG->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    try {
        $stmtF = $pdo->query('SELECT * FROM fun_facts ORDER BY display_order ASC');
        $funFacts = $stmtF->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
    
    try {
        $stmtCo = $pdo->query('SELECT * FROM countries WHERE is_active = 1 ORDER BY display_order ASC, name ASC');
        $countries = $stmtCo->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
    
    try {
        $stmtBa = $pdo->query('SELECT * FROM bank_accounts WHERE is_active = 1 ORDER BY display_order ASC, bank_name ASC');
        $bankAccounts = $stmtBa->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    try {
        $stmtRev = $pdo->query("SELECT * FROM reviews WHERE status = 'Approved' ORDER BY created_at DESC");
        $homeReviews = $stmtRev->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}

    try {
        $stmtTm = $pdo->query("SELECT * FROM team_members WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
        $teamMembers = $stmtTm->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {}
}
$bankAccounts = $bankAccounts ?? [];
$homeReviews = $homeReviews ?? [];
$teamMembers = $teamMembers ?? [];

if (empty($homeReviews)) {
    $homeReviews = [
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
}

if (empty($teamMembers)) {
    $teamMembers = [
        [
            'id' => 1,
            'name' => 'Roshni Bhattarai',
            'role' => 'Chief Counseling Officer & Senior Advisor',
            'department' => 'Study in India',
            'qualification' => 'TITI Certified Senior Counselor (CTEVT) | M.A. Counseling Psychology',
            'specialization' => 'MBBS, BDS, B.Sc Nursing, B.Tech CSE, Scholarship Allocations',
            'bio' => '10+ years of dedicated academic counseling helping over 1,200 Nepali students secure direct admissions and up to 100% scholarships across premier Indian universities.',
            'experience_years' => 10,
            'email' => 'roshni@visionpoint.edu.np',
            'phone' => '+977-9807765812',
            'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&auto=format&fit=crop&q=80',
            'display_order' => 1
        ],
        [
            'id' => 2,
            'name' => 'Suresh K. Nepal',
            'role' => 'Head of Study Abroad & Visa Compliance',
            'department' => 'Study Abroad',
            'qualification' => 'Certified Qualified Education Agent Counselor (QEAC) | MBA',
            'specialization' => 'Australia (Subclass 500), UK CAS, USA F-1, Canada SDS',
            'bio' => 'Licensed overseas counselor with 8+ years specializing in Genuine Student (GS) compliance, Statement of Purpose (SOP) auditing, and complex visa applications.',
            'experience_years' => 8,
            'email' => 'suresh@visionpoint.edu.np',
            'phone' => '+977-9807765812',
            'photo' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=500&auto=format&fit=crop&q=80',
            'display_order' => 2
        ],
        [
            'id' => 3,
            'name' => 'Pooja Regmi',
            'role' => 'Director of Healthcare & Allied Science Admissions',
            'department' => 'Study in India',
            'qualification' => 'TITI Certified Education Counselor | B.Sc Nursing, M.Sc Health Admin',
            'specialization' => 'B.Sc Nursing, Post Basic Nursing, B.Pharm, Paramedical & Equivalence',
            'bio' => '7+ years guiding prospective nurses and allied medical students through INC/NMC council recognitions, hospital tie-ups, and clinical internship setups.',
            'experience_years' => 7,
            'email' => 'pooja@visionpoint.edu.np',
            'phone' => '+977-9807765812',
            'photo' => 'https://images.unsplash.com/photo-1594824813511-193e819b1388?w=500&auto=format&fit=crop&q=80',
            'display_order' => 3
        ],
        [
            'id' => 4,
            'name' => 'Amit Shrestha',
            'role' => 'Head of Test Preparation & English Department',
            'department' => 'Test Preparation',
            'qualification' => 'British Council & Pearson Certified Master Trainer | M.A. English Literature',
            'specialization' => 'IELTS Academic (Band 7.5+), PTE Academic (Score 65+), SAT, Duolingo',
            'bio' => '9+ years helping hundreds of Nepali students attain high test scores with tailored mock tests, band score diagnostic rubrics, and modern computer-based lab training.',
            'experience_years' => 9,
            'email' => 'amit@visionpoint.edu.np',
            'phone' => '+977-9807765812',
            'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=500&auto=format&fit=crop&q=80',
            'display_order' => 4
        ],
        [
            'id' => 5,
            'name' => 'Anil Kumar Jha',
            'role' => 'Senior Admissions & University Liaison Officer',
            'department' => 'Admissions & Scholarships',
            'qualification' => 'TITI Certified Education Counselor | BBA, PGD Educational Management',
            'specialization' => 'Engineering (AI, Data Science, Mechanical), Management (MBA, BBA)',
            'bio' => 'Direct liaison with 80+ partner institutions in Delhi NCR, Punjab, Bangalore, and Pune. Expert in negotiating institutional fee concessions for Nepali students.',
            'experience_years' => 6,
            'email' => 'anil@visionpoint.edu.np',
            'phone' => '+977-9807765812',
            'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=500&auto=format&fit=crop&q=80',
            'display_order' => 5
        ],
        [
            'id' => 6,
            'name' => 'Sunita Thapa',
            'role' => 'Student Welfare & Pre-Departure Coordinator',
            'department' => 'Student Care',
            'qualification' => 'B.A. Social Work & Student Welfare Specialist',
            'specialization' => 'Hostel Inspection, Border & Airport Transit, Medical Insurance, Nepal Embassy Registration',
            'bio' => 'Ensures seamless transitions for students relocating from Nepal to college campuses, providing continuous emergency assistance and guardian coordination.',
            'experience_years' => 5,
            'email' => 'sunita@visionpoint.edu.np',
            'phone' => '+977-9807765812',
            'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=500&auto=format&fit=crop&q=80',
            'display_order' => 6
        ]
    ];
}
?>

<!-- Hero Banner Section -->
<section class="hero-section d-flex align-items-center">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-7 text-start">
                <!-- 1. Small Live Badge -->
                <div class="d-inline-flex align-items-center gap-2 bg-white px-3 py-2 rounded-pill shadow-sm border border-light mb-4 hero-badge-container hero-reveal-item">
                    <span class="d-inline-block bg-success rounded-circle pulse-dot" style="width: 8px; height: 8px;"></span>
                    <span class="text-primary fw-bold small uppercase" style="font-size: 12px; letter-spacing: 0.5px;">
                        <i class="fa-solid fa-certificate text-danger me-1"></i> Est. 2070 B.S. &bull; MoEST Approved &bull; TITI Certified Counselors
                    </span>
                </div>
                
                <!-- 2. Main Heading -->
                <h1 class="hero-title fw-extrabold display-4 mb-3 hero-reveal-item">
                    <?php
                        $title = $siteSettings['hero_title'] ?? "Unlock Your Full\nEducational Potential in India";
                        $styledTitle = nl2br(htmlspecialchars($title));
                        $styledTitle = str_replace(
                            ['Educational Potential', 'Study in India', 'Unlock Your Full'], 
                            ['<span class="text-gradient-secondary">Educational Potential</span>', '<span class="text-gradient-primary">Study in India</span>', 'Unlock Your <span class="text-gradient-primary">Full</span>'], 
                            $styledTitle
                        );
                        echo $styledTitle;
                    ?>
                </h1>
                
                <!-- 3. Description Paragraph -->
                <p class="hero-subtitle lead text-muted mb-4 fs-5 hero-reveal-item" style="max-width: 600px; line-height: 1.6;">
                    <?= htmlspecialchars($siteSettings['hero_subtitle'] ?? 'Maximize your potential and reach new academic peaks with our expert guidance, university listings, and scholarships every step of the way.') ?>
                </p>
                
                <!-- 4. CTA Buttons & Search Wrapper -->
                <div class="hero-cta-wrapper hero-reveal-item">
                    <!-- Premium Search Bar -->
                    <form action="<?= h($basePath) ?>cities.php" method="GET" class="hero-search-form p-1.5 p-sm-2 bg-white rounded-pill shadow d-flex align-items-center mt-2 max-w-600">
                        <div class="d-flex align-items-center flex-grow-1 ps-2 ps-sm-3">
                            <i class="fa-solid fa-magnifying-glass text-primary fs-5 me-2"></i>
                            <input type="text" name="search" placeholder="Search programs, colleges, streams..." class="form-control border-0 bg-transparent flex-grow-1" style="box-shadow:none; font-size:15px;">
                        </div>
                        <button type="submit" class="btn btn-secondary px-3 px-sm-4 py-2.5 py-sm-3 rounded-pill fw-bold text-white shadow-sm hover-scale d-flex align-items-center gap-1.5 flex-shrink-0">
                            <span class="d-none d-sm-inline">Search Programs</span>
                            <span class="d-inline d-sm-none">Search</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </form>
                    
                    <!-- Popular Searches / Quick Links -->
                    <div class="popular-searches mt-3 d-flex align-items-center flex-wrap gap-2 text-muted small">
                        <span class="fw-semibold">Popular Streams:</span>
                        <a href="courses.php" class="badge bg-light text-primary border border-light-subtle rounded-pill text-decoration-none px-3 py-2 quick-tag-hover">Engineering</a>
                        <a href="courses.php" class="badge bg-light text-primary border border-light-subtle rounded-pill text-decoration-none px-3 py-2 quick-tag-hover">Nursing</a>
                        <a href="courses.php" class="badge bg-light text-primary border border-light-subtle rounded-pill text-decoration-none px-3 py-2 quick-tag-hover">Management</a>
                        <a href="courses.php" class="badge bg-light text-primary border border-light-subtle rounded-pill text-decoration-none px-3 py-2 quick-tag-hover">IT & CS</a>
                    </div>

                    <!-- Trust Points -->
                    <div class="row g-3 mt-4 pt-2 border-top border-light-subtle max-w-600">
                        <div class="col-4 d-flex align-items-center gap-2">
                            <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-calendar-check"></i>
                            </div>
                            <div class="lh-sm">
                                <div class="fw-bold text-dark" style="font-size: 13px;">Est. 2070 B.S.</div>
                                <div class="text-muted" style="font-size: 11px;">13+ Yrs of Trust</div>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center gap-2">
                            <div class="bg-warning-subtle text-warning-emphasis rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>
                            <div class="lh-sm">
                                <div class="fw-bold text-dark" style="font-size: 13px;">TITI Certified</div>
                                <div class="text-muted" style="font-size: 11px;">Expert Counselors</div>
                            </div>
                        </div>
                        <div class="col-4 d-flex align-items-center gap-2">
                            <div class="bg-success-subtle text-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-landmark"></i>
                            </div>
                            <div class="lh-sm">
                                <div class="fw-bold text-dark" style="font-size: 13px;">MoEST Approved</div>
                                <div class="text-muted" style="font-size: 11px;">Ministry Education</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Graphic Right Side -->
            <div class="col-12 col-lg-5 position-relative mt-4 mt-lg-0">
                <div class="hero-image-wrapper position-relative">
                    <!-- Abstract Background Shapes -->
                    <div class="position-absolute bg-warning rounded-circle opacity-10 float-shape-1" style="width: 350px; height: 350px; top: -30px; right: -50px; z-index: -1;"></div>
                    <div class="position-absolute bg-primary rounded-circle opacity-5 float-shape-2" style="width: 250px; height: 250px; bottom: -50px; left: -50px; z-index: -1;"></div>
                    
                    <?php
                        $heroImg = 'assets/images/student_hero.png';
                        if (!file_exists(__DIR__ . '/' . $heroImg)) {
                            $heroImg = !empty($siteSettings['hero_banner_image']) ? $siteSettings['hero_banner_image'] : 'assets/images/image1.png';
                        }
                        $heroImgUrl = (strpos($heroImg, 'http') === 0) ? $heroImg : $basePath . $heroImg;
                        $localHeroPath = __DIR__ . '/' . ltrim($heroImg, '/');
                        if (file_exists($localHeroPath)) {
                            $heroImgUrl .= (strpos($heroImgUrl, '?') === false ? '?' : '&') . 'v=' . filemtime($localHeroPath);
                        }

                        // Resolve dynamic video configuration (uploaded file, custom URL, or YouTube embed)
                        $rawVideo = !empty($siteSettings['hero_video_path']) ? $siteSettings['hero_video_path'] : 'assets/videos/vision_point_intro.mp4';
                        $videoTitle = !empty($siteSettings['hero_video_title']) ? $siteSettings['hero_video_title'] : 'Vision Point Educational Service — Campus Spotlight';

                        $isYouTube = (
                            strpos($rawVideo, 'youtube.com') !== false ||
                            strpos($rawVideo, 'youtu.be') !== false
                        );

                        $youtubeEmbedUrl = '';
                        $ytId = '';
                        if ($isYouTube) {
                            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i', $rawVideo, $match)) {
                                $ytId = $match[1];
                                $youtubeEmbedUrl = 'https://www.youtube.com/embed/' . $ytId;
                            } else {
                                $youtubeEmbedUrl = $rawVideo;
                            }
                        }

                        $videoFileUrl = (strpos($rawVideo, 'http') === 0) ? $rawVideo : ($basePath . ltrim($rawVideo, '/'));
                        $localVideoFile = __DIR__ . '/' . ltrim($rawVideo, '/');
                        if (file_exists($localVideoFile)) {
                            $videoFileUrl .= '?v=' . filemtime($localVideoFile);
                        }
                    ?>
                    <!-- Premium Framed Video Showcase with Interactive Overlay -->
                    <div class="hero-card-frame position-relative rounded-4 overflow-hidden shadow-lg border border-white border-opacity-75">
                        <div class="hero-video-container position-relative overflow-hidden rounded-4" style="background: #031b33;">
                            <?php if ($isYouTube): ?>
                                <div class="ratio ratio-16x9" style="min-height: 380px;">
                                    <iframe src="<?= h($youtubeEmbedUrl) ?>?autoplay=1&mute=1&loop=1&playlist=<?= h($ytId) ?>" class="w-100 h-100 rounded-4" style="border:0;" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                                </div>
                            <?php else: ?>
                                <video id="heroIntroVideo" class="w-100 object-fit-cover d-block rounded-4" autoplay muted loop playsinline preload="auto" style="max-height: 500px; min-height: 380px; object-position: center;">
                                    <source src="<?= h($videoFileUrl) ?>" type="video/mp4">
                                    <img src="<?= h($heroImgUrl) ?>" class="img-fluid w-100 object-fit-cover rounded-4" alt="Vision Point Education">
                                </video>
                                
                                <!-- Subtle dark gradient overlay for text readability -->
                                <div class="hero-video-overlay position-absolute top-0 start-0 w-100 h-100 pointer-events-none" style="background: linear-gradient(180deg, rgba(4, 27, 49, 0.15) 0%, rgba(4, 27, 49, 0.45) 100%);"></div>

                                <!-- Video Control Pills (Top-Left) -->
                                <div class="hero-video-controls position-absolute top-3 start-3 d-flex align-items-center gap-2 z-3">
                                    <button type="button" id="heroVideoSoundToggle" class="btn btn-sm btn-dark bg-opacity-75 text-white border-0 rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 backdrop-blur shadow-sm transition-smooth" style="font-size: 12px;" title="Toggle Sound">
                                        <i class="fa-solid fa-volume-xmark" id="heroSoundIcon"></i>
                                        <span id="heroSoundText">Unmute</span>
                                    </button>
                                    <button type="button" id="heroVideoPlayPause" class="btn btn-sm btn-dark bg-opacity-75 text-white border-0 rounded-circle d-flex align-items-center justify-content-center backdrop-blur shadow-sm transition-smooth" style="width: 32px; height: 32px;" title="Play/Pause">
                                        <i class="fa-solid fa-pause" id="heroPlayPauseIcon"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-dark bg-opacity-75 text-white border-0 rounded-circle d-flex align-items-center justify-content-center backdrop-blur shadow-sm transition-smooth" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#videoTourModal" title="Watch in Theater Lightbox">
                                        <i class="fa-solid fa-expand"></i>
                                    </button>
                                </div>
                            <?php endif; ?>

                            <!-- Live Tour Badge (Top-Right) -->
                            <div class="position-absolute top-3 end-3 bg-white bg-opacity-95 backdrop-blur px-3 py-1.5 rounded-pill shadow border border-light-subtle d-flex align-items-center gap-2 z-3">
                                <span class="d-inline-block bg-danger rounded-circle pulse-dot" style="width: 8px; height: 8px;"></span>
                                <span class="fw-bold text-dark" style="font-size: 11px;">Campus Spotlight</span>
                            </div>

                            <!-- Float Badge (Bottom-Left) -->
                            <div class="position-absolute bottom-3 start-3 bg-white bg-opacity-95 backdrop-blur p-2.5 rounded-3 shadow border border-light-subtle d-none d-md-flex align-items-center gap-2 z-3 float-card-1 anim-float" style="max-width: 240px;">
                                <div class="bg-success text-white rounded-circle p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 34px; height: 34px;">
                                    <i class="fa-solid fa-check fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 12px;">Scholarship Secured</h6>
                                    <p class="mb-0 text-muted extra-small" style="font-size: 10px;">Over 80% Success Rate</p>
                                </div>
                            </div>

                            <!-- Admissions open badge (Bottom-Right) -->
                            <div class="position-absolute bottom-3 end-3 bg-primary bg-opacity-95 text-white backdrop-blur px-3 py-1.5 rounded-pill shadow d-flex align-items-center gap-1.5 z-3">
                                <i class="fa-solid fa-fire text-warning small"></i>
                                <span class="fw-bold" style="font-size: 11px;">2026 Admissions Open</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- High Performance Intersection Observer Script for Hero Scroll Reveal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const heroSection = document.querySelector('.hero-section');
    if (!heroSection) return;

    // Respect user's prefers-reduced-motion setting
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        heroSection.classList.add('hero-reveal-active');
        return;
    }

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3 // Trigger when ~30% of hero content is in viewport
    };

    const heroObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                heroSection.classList.add('hero-reveal-active');
                observer.unobserve(entry.target); // Animate only once per page load
            }
        });
    }, observerOptions);

    heroObserver.observe(heroSection);
});
</script>

<!-- Institutions Marquee Ticker Section -->
<section class="py-5 bg-white border-bottom border-light">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Top Institutions in India</h2>
                <p class="text-muted mb-0">Discover our Top-Ranked Universities and Colleges in India</p>
            </div>
            <a href="cities.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">View All</a>
        </div>
        
        <?php 
        // Retrieve top active institutions or fallback set
        $topUnis = [];
        foreach ($institutions as $uni) {
            if (isset($uni['is_top']) && $uni['is_top'] == 1) {
                $topUnis[] = $uni;
            }
        }
        if (empty($topUnis)) {
            $topUnis = array_slice($institutions, 0, 10);
        }
        ?>

        <?php if (!empty($topUnis)): ?>
        <div class="inst-ticker-slider">
            <div class="inst-ticker-track">
                <!-- First Set of Institution Cards -->
                <?php foreach ($topUnis as $uni): 
                    $uniImg = !empty($uni['img']) ? ((strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img']) : 'assets/images/logo1.png';
                ?>
                    <div class="inst-ticker-card">
                        <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none">
                            <div class="card border border-light-subtle rounded-3 text-center h-100 p-3 shadow-sm bg-white d-flex flex-column align-items-center justify-content-center">
                                <div class="inst-circle-badge mx-auto mb-2">
                                    <img src="<?= h($uniImg) ?>" alt="<?= h($uni['name']) ?>">
                                </div>
                                <div class="card-body p-0 text-center w-100">
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 13px; line-height: 1.3; color: #242628 !important; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 34px;">
                                        <?= h($uni['name']) ?>
                                    </h6>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>

                <!-- Duplicate Set for Seamless Infinite Scrolling -->
                <?php foreach ($topUnis as $uni): 
                    $uniImg = !empty($uni['img']) ? ((strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img']) : 'assets/images/logo1.png';
                ?>
                    <div class="inst-ticker-card">
                        <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none">
                            <div class="card border border-light-subtle rounded-3 text-center h-100 p-3 shadow-sm bg-white d-flex flex-column align-items-center justify-content-center">
                                <div class="inst-circle-badge mx-auto mb-2">
                                    <img src="<?= h($uniImg) ?>" alt="<?= h($uni['name']) ?>">
                                </div>
                                <div class="card-body p-0 text-center w-100">
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 13px; line-height: 1.3; color: #242628 !important; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 34px;">
                                        <?= h($uni['name']) ?>
                                    </h6>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <style>
    .inst-ticker-slider {
        width: 100% !important;
        overflow: hidden !important;
        position: relative !important;
        padding: 10px 0 !important;
        display: block !important;
    }

    .inst-ticker-track {
        display: flex !important;
        flex-wrap: nowrap !important;
        width: max-content !important;
        gap: 20px !important;
        will-change: transform !important;
        animation: instTickerScroll 35s linear infinite !important;
    }

    .inst-ticker-slider:hover .inst-ticker-track {
        animation-play-state: paused !important;
    }

    .inst-circle-badge {
        width: 80px !important;
        height: 80px !important;
        min-width: 80px !important;
        min-height: 80px !important;
        border-radius: 50% !important;
        overflow: hidden !important;
        border: none !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06) !important;
        padding: 0 !important;
    }

    .inst-circle-badge img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        border-radius: 50% !important;
        display: block !important;
    }

    .inst-ticker-card {
        flex: 0 0 190px !important;
        width: 190px !important;
        min-width: 190px !important;
        max-width: 190px !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
    }

    .inst-ticker-card:hover {
        transform: translateY(-4px) !important;
    }

    @media (max-width: 992px) {
        .inst-ticker-card {
            flex: 0 0 160px !important;
            width: 160px !important;
            min-width: 160px !important;
            max-width: 160px !important;
        }
    }

    @media (max-width: 768px) {
        .inst-ticker-card {
            flex: 0 0 140px !important;
            width: 140px !important;
            min-width: 140px !important;
            max-width: 140px !important;
        }
    }

    @keyframes instTickerScroll {
        0% {
            transform: translate3d(0, 0, 0);
        }
        100% {
            transform: translate3d(-50%, 0, 0);
        }
    }
    </style>
</section>

<!-- Top Featured Universities Slider Section -->
<section class="py-5 bg-light border-bottom border-light-subtle position-relative">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3 fw-bold mb-1 d-flex align-items-center">
                    <span class="rounded-circle bg-warning-subtle text-warning d-inline-flex align-items-center justify-content-center me-2 shadow-sm" style="width: 36px; height: 36px; min-width: 36px; font-size: 17px;">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </span>
                    <span>Top Featured Universities</span>
                </h2>
                <p class="text-muted mb-0 small">Explore top-ranked Indian universities with direct admission and scholarship assistance</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-primary rounded-circle btn-sm d-flex align-items-center justify-content-center top-uni-prev-btn" style="width: 38px; height: 38px;" aria-label="Previous">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button class="btn btn-outline-primary rounded-circle btn-sm d-flex align-items-center justify-content-center top-uni-next-btn" style="width: 38px; height: 38px;" aria-label="Next">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <a href="<?= h($basePath) ?>cities.php" class="btn btn-primary rounded-pill px-4 btn-sm fw-semibold ms-2 d-inline-flex align-items-center gap-2">
                    <span>View All</span>
                    <i class="fa-solid fa-arrow-right small"></i>
                </a>
            </div>
        </div>
        
        <div class="top-uni-slider-viewport overflow-hidden py-2 px-1">
            <div class="top-uni-slider-track d-flex gap-4">
                <?php 
                $featuredUnis = !empty($topUnis) ? $topUnis : array_slice($institutions, 0, 12);
                if (!empty($featuredUnis)):
                    foreach ($featuredUnis as $uni):
                        $uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                ?>
                    <div class="top-uni-card-item">
                        <div class="card h-100 border border-light-subtle shadow-sm rounded-4 overflow-hidden bg-white d-flex flex-column justify-content-between hover-scale-sm">
                            <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none text-dark">
                                <div class="position-relative overflow-hidden" style="height: 190px;">
                                    <img src="<?= h($uniImg) ?>" class="w-100 h-100 object-fit-cover transition-smooth" alt="<?= h($uni['name']) ?>">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-warning text-dark shadow-sm d-flex align-items-center gap-1 py-1.5 px-3 fw-bold rounded-pill" style="font-size: 11px;">
                                            <i class="fa-solid fa-star text-white"></i> Top Ranked
                                        </span>
                                    </div>
                                </div>
                                <div class="p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 fw-semibold" style="font-size: 11px;">
                                            <i class="fa-solid fa-location-dot me-1 text-danger"></i><?= h($uni['city']) ?> (<?= h($uni['region']) ?>)
                                        </span>
                                        <span class="text-muted small" style="font-size: 11px;">
                                            <i class="fa-solid fa-shield-halved text-success me-1"></i>Verified
                                        </span>
                                    </div>
                                    <h5 class="fw-bold text-primary mb-2 line-clamp-2" style="font-size: 17px; line-height: 1.4;"><?= h($uni['name']) ?></h5>
                                    <p class="text-muted small mb-0 line-clamp-2" style="font-size: 12px; line-height: 1.5;">
                                        <?= !empty($uni['about']) ? h(mb_strimwidth(strip_tags($uni['about']), 0, 90, '...')) : 'Offering top tier academic programs, modern campus facilities, and scholarship opportunities for international students.' ?>
                                    </p>
                                </div>
                            </a>
                            <div class="p-4 pt-0 d-flex gap-2">
                                <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="btn btn-outline-primary flex-grow-1 rounded-pill py-2 btn-sm fw-semibold text-center text-decoration-none">
                                    View Details
                                </a>
                                <button class="btn btn-secondary flex-grow-1 rounded-pill py-2 btn-sm fw-semibold enroll-btn" data-college="<?= h($uni['name']) ?>" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                                    Apply Now
                                </button>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach;
                endif;
                ?>
            </div>
        </div>
        
        <!-- Pagination Dots -->
        <div class="top-uni-dots-container d-flex justify-content-center gap-2 mt-4"></div>

        <div class="text-center mt-3">
            <a href="cities.php" class="btn btn-primary rounded-pill px-5 py-2.5 fw-bold shadow-sm">
                Explore All 100+ Partner Universities <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>

    <style>
    .top-uni-card-item {
        flex: 0 0 calc(33.333% - 16px);
        min-width: 310px;
        box-sizing: border-box;
    }
    @media (max-width: 992px) {
        .top-uni-card-item {
            flex: 0 0 calc(50% - 12px);
            min-width: 280px;
        }
    }
    @media (max-width: 576px) {
        .top-uni-card-item {
            flex: 0 0 100%;
            min-width: 100%;
        }
    }
    .top-uni-slider-track {
        transition: transform 0.5s cubic-bezier(0.25, 1, 0.5, 1);
        will-change: transform;
    }
    .top-uni-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #cbd5e1;
        border: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .top-uni-dot.active {
        width: 28px;
        border-radius: 12px;
        background-color: var(--primary-color, #102e48);
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const track = document.querySelector('.top-uni-slider-track');
        if (!track) return;

        const items = Array.from(track.querySelectorAll('.top-uni-card-item'));
        if (items.length === 0) return;

        const prevBtn = document.querySelector('.top-uni-prev-btn');
        const nextBtn = document.querySelector('.top-uni-next-btn');
        const dotsContainer = document.querySelector('.top-uni-dots-container');

        let currentIndex = 0;
        let autoPlayTimer = null;

        function getVisibleCards() {
            const width = window.innerWidth;
            if (width >= 992) return 3;
            if (width >= 576) return 2;
            return 1;
        }

        function getMaxIndex() {
            const visible = getVisibleCards();
            return Math.max(0, items.length - visible);
        }

        function buildDots() {
            if (!dotsContainer) return;
            dotsContainer.innerHTML = '';
            const maxIdx = getMaxIndex();
            for (let i = 0; i <= maxIdx; i++) {
                const dot = document.createElement('button');
                dot.className = 'top-uni-dot' + (i === 0 ? ' active' : '');
                dot.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                dot.addEventListener('click', function() {
                    slideTo(i);
                    resetAutoPlay();
                });
                dotsContainer.appendChild(dot);
            }
        }

        function updateDots() {
            if (!dotsContainer) return;
            const dots = dotsContainer.querySelectorAll('.top-uni-dot');
            dots.forEach((dot, idx) => {
                if (idx === currentIndex) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        }

        function slideTo(index) {
            const maxIdx = getMaxIndex();
            if (index < 0) index = maxIdx;
            if (index > maxIdx) index = 0;

            currentIndex = index;

            const firstItem = items[0];
            const itemWidth = firstItem.getBoundingClientRect().width;
            const gap = 24;
            const translateVal = -currentIndex * (itemWidth + gap);

            track.style.transform = `translateX(${translateVal}px)`;
            updateDots();
        }

        if (prevBtn) {
            prevBtn.addEventListener('click', function() {
                slideTo(currentIndex - 1);
                resetAutoPlay();
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', function() {
                slideTo(currentIndex + 1);
                resetAutoPlay();
            });
        }

        function startAutoPlay() {
            if (autoPlayTimer) return;
            autoPlayTimer = setInterval(function() {
                slideTo(currentIndex + 1);
            }, 4000);
        }

        function stopAutoPlay() {
            if (autoPlayTimer) {
                clearInterval(autoPlayTimer);
                autoPlayTimer = null;
            }
        }

        function resetAutoPlay() {
            stopAutoPlay();
            startAutoPlay();
        }

        const viewport = document.querySelector('.top-uni-slider-viewport');
        if (viewport) {
            viewport.addEventListener('mouseenter', stopAutoPlay);
            viewport.addEventListener('mouseleave', startAutoPlay);

            // Native Touch Swipe Gesture Support for iOS & Android
            let touchStartX = 0;
            let touchEndX = 0;
            viewport.addEventListener('touchstart', function(e) {
                touchStartX = e.changedTouches[0].screenX;
                stopAutoPlay();
            }, {passive: true});

            viewport.addEventListener('touchend', function(e) {
                touchEndX = e.changedTouches[0].screenX;
                const diff = touchStartX - touchEndX;
                if (Math.abs(diff) > 40) {
                    if (diff > 0) {
                        slideTo(currentIndex + 1);
                    } else {
                        slideTo(currentIndex - 1);
                    }
                }
                startAutoPlay();
            }, {passive: true});
        }

        window.addEventListener('resize', function() {
            buildDots();
            slideTo(Math.min(currentIndex, getMaxIndex()));
        });

        buildDots();
        slideTo(0);
        startAutoPlay();
    });
    </script>
</section>



<!-- Dedicated Video Spotlight Section: Campus Life & Student Experience -->
<section class="py-5 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #021a30 0%, #043866 50%, #064c8a 100%);">
    <!-- Ambient Glow Orbs -->
    <div class="position-absolute rounded-circle opacity-10" style="width: 500px; height: 500px; top: -100px; left: -100px; background: radial-gradient(circle, #D91F23 0%, transparent 70%); filter: blur(60px); pointer-events: none;"></div>
    <div class="position-absolute rounded-circle opacity-15" style="width: 500px; height: 500px; bottom: -100px; right: -100px; background: radial-gradient(circle, #0458A0 0%, transparent 70%); filter: blur(60px); pointer-events: none;"></div>

    <div class="container py-4 position-relative z-2">
        <div class="text-center max-w-800 mx-auto mb-5">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3 shadow-sm">
                <i class="fa-solid fa-play-circle me-1.5 text-danger"></i> Campus Life & Student Experience
            </span>
            <h2 class="display-6 fw-extrabold text-white mb-3">Your Gateway to Top-Tier Universities in India & Abroad</h2>
            <p class="text-white-50 lead fs-6 mb-0" style="line-height: 1.8;">
                From high-tech lecture halls and NABH teaching hospitals to safe on-campus residential hostels with authentic dining—discover what life looks like when you study through Vision Point.
            </p>
        </div>

        <div class="row align-items-center g-5">
            <!-- Left: Cinematic Video Player Frame -->
            <div class="col-lg-7">
                <div class="position-relative rounded-4 overflow-hidden shadow-lg border border-white border-opacity-20" style="background: #000000;">
                    <?php if ($isYouTube): ?>
                        <div class="ratio ratio-16x9">
                            <iframe src="<?= h($youtubeEmbedUrl) ?>" class="w-100 h-100" style="border:0;" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                        </div>
                    <?php else: ?>
                        <!-- Theater Video Element -->
                        <video id="spotlightVideo" class="w-100 d-block" playsinline loop muted autoplay preload="auto" style="border-radius: 16px; aspect-ratio: 16/9; object-fit: cover;">
                            <source src="<?= h($videoFileUrl) ?>" type="video/mp4">
                            Your browser does not support HTML5 video.
                        </video>

                        <!-- Theater Overlay Controls -->
                        <div class="position-absolute bottom-0 start-0 w-100 p-3 p-md-4 d-flex align-items-center justify-content-between text-white" style="background: linear-gradient(0deg, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.4) 60%, transparent 100%);">
                            <div class="d-flex align-items-center gap-3">
                                <button type="button" id="spotlightPlayBtn" class="btn btn-warning text-dark rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 44px; height: 44px;" title="Play/Pause">
                                    <i class="fa-solid fa-pause fs-6" id="spotlightPlayIcon"></i>
                                </button>
                                <button type="button" id="spotlightMuteBtn" class="btn btn-outline-light rounded-pill px-3 py-1.5 d-flex align-items-center gap-2 small" title="Toggle Sound">
                                    <i class="fa-solid fa-volume-xmark" id="spotlightMuteIcon"></i>
                                    <span id="spotlightMuteText">Unmute</span>
                                </button>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-1.5 small fw-normal">
                                    <i class="fa-solid fa-video me-1 text-warning"></i> HD Video
                                </span>
                                <button type="button" class="btn btn-outline-light rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;" data-bs-toggle="modal" data-bs-target="#videoTourModal" title="Fullscreen Theater Lightbox">
                                    <i class="fa-solid fa-expand"></i>
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: 4 Value Pillars -->
            <div class="col-lg-5">
                <div class="d-flex flex-column gap-3">
                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 backdrop-blur transition-smooth">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2.5 bg-warning text-dark rounded-3 flex-shrink-0">
                                <i class="fa-solid fa-building-columns fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white fs-6 mb-1">Direct University Affiliations</h5>
                                <p class="text-white-50 extra-small mb-0" style="line-height: 1.6;">Official provisional offer letters from university admissions directorates with zero middlemen.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 backdrop-blur transition-smooth">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2.5 bg-success text-white rounded-3 flex-shrink-0">
                                <i class="fa-solid fa-award fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white fs-6 mb-1">Up to 100% Merit Scholarships</h5>
                                <p class="text-white-50 extra-small mb-0" style="line-height: 1.6;">Fee waivers and institutional concessions clearly documented on your official offer letter.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 backdrop-blur transition-smooth">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2.5 bg-info text-white rounded-3 flex-shrink-0">
                                <i class="fa-solid fa-bed fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white fs-6 mb-1">Safe Hostels & Food Assurance</h5>
                                <p class="text-white-50 extra-small mb-0" style="line-height: 1.6;">Verified campus hostels with biometric security and separate dining for boys and girls.</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-15 backdrop-blur transition-smooth">
                        <div class="d-flex align-items-start gap-3">
                            <div class="p-2.5 bg-danger text-white rounded-3 flex-shrink-0">
                                <i class="fa-solid fa-shield-halved fs-5"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-white fs-6 mb-1">100% Council Verified Degrees</h5>
                                <p class="text-white-50 extra-small mb-0" style="line-height: 1.6;">Adherence to Nepal Medical (NMC), Nursing (NNC), and Engineering (NEC) Council standards.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 pt-2">
                        <button class="btn btn-warning text-dark fw-bold px-4 py-2.5 rounded-pill shadow flex-grow-1" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                            <i class="fa-solid fa-paper-plane me-2"></i>Apply for Free Counseling
                        </button>
                        <a href="cities.php" class="btn btn-outline-light px-4 py-2.5 rounded-pill fw-semibold">
                            <i class="fa-solid fa-compass me-1"></i>Explore Colleges
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Destinations Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Top Destinations to Study</h2>
                <p class="text-muted mb-0">Explore the major cities in India where our partner institutions are situated.</p>
            </div>
            <a href="cities.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">View All</a>
        </div>
        
        <div class="city-slider">
            <div class="city-slider-track">
                <?php 
                $displayDestinations = !empty($destinations) ? $destinations : [
                    ['id' => 1, 'name' => 'Delhi (Delhi NCR)', 'img' => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 2, 'name' => 'Bangalore (Bengaluru)', 'img' => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 3, 'name' => 'Mumbai', 'img' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 4, 'name' => 'Coimbatore', 'img' => 'https://images.unsplash.com/photo-1605649487212-47bdab064df7?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 5, 'name' => 'Bhubaneswar', 'img' => 'https://images.unsplash.com/photo-1626014903708-ec99c855a452?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 6, 'name' => 'Visakhapatnam', 'img' => 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 7, 'name' => 'Shimla', 'img' => 'https://images.unsplash.com/photo-1626621341517-bbf3d9990a23?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 8, 'name' => 'Punjab', 'img' => 'https://images.unsplash.com/photo-1609137144813-7d9921338f24?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 9, 'name' => 'Chandigarh', 'img' => 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 10, 'name' => 'Nagpur', 'img' => 'https://images.unsplash.com/photo-1570168007204-dfb528c6958f?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 11, 'name' => 'Kolkata', 'img' => 'https://images.unsplash.com/photo-1558431382-27e303142255?w=400&auto=format&fit=crop&q=60'],
                    ['id' => 12, 'name' => 'Dehradun', 'img' => 'https://images.unsplash.com/photo-1587474260584-136574528ed5?w=400&auto=format&fit=crop&q=60']
                ];
                ?>
                <!-- First Set of Cards -->
                <?php foreach ($displayDestinations as $dest): ?>
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
                <?php foreach ($displayDestinations as $dest): ?>
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

<!-- Courses Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Explore Available Streams</h2>
                <p class="text-muted mb-0">Choose your field of study from a wide array of career options.</p>
            </div>
            <a href="courses.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">View All</a>
        </div>
        
        <div class="row g-3">
            <?php foreach (array_slice($courses, 0, 6) as $course): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-2">
                    <div class="card course-card border-0 text-center h-100 p-4 shadow-sm">
                        <div class="d-flex align-items-center justify-content-center overflow-hidden rounded-circle mx-auto mb-3" style="width: 70px; height: 70px;">
                            <?php 
                                $courseImg = !empty($course['img']) ? ( (strpos($course['img'], 'http') === 0) ? $course['img'] : $basePath . $course['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                            ?>
                            <img src="<?= h($courseImg) ?>" alt="<?= h($course['name']) ?>" class="w-100 h-100 object-fit-cover course-card-img">
                        </div>
                        <h6 class="fw-bold text-primary mb-0"><?= h($course['name']) ?></h6>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!isset($siteSettings['accreditation_enabled']) || $siteSettings['accreditation_enabled'] === '1'): ?>
<?php
$accredTitle = !empty($siteSettings['accreditation_title']) ? $siteSettings['accreditation_title'] : 'Accreditation';
$accredSubtitle = !empty($siteSettings['accreditation_subtitle']) ? $siteSettings['accreditation_subtitle'] : 'Trusted excellence backed by top accreditations';

// Fetch accreditations from database
$dbAccreds = [];
if (isset($pdo)) {
    try {
        $stmtAcc = $pdo->query("SELECT * FROM accreditations WHERE is_active = 1 ORDER BY display_order ASC, id ASC");
        $dbAccreds = $stmtAcc->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {
        $dbAccreds = [];
    }
}

$intAccreds = array_values(array_filter($dbAccreds, function($item) {
    return strtolower($item['category']) === 'international';
}));

$natAccreds = array_values(array_filter($dbAccreds, function($item) {
    return strtolower($item['category']) === 'national';
}));

// Fallbacks if empty
if (empty($intAccreds)) {
    $intAccreds = [
        ['title' => 'International Accreditation 1', 'logo' => 'int1.jpeg'],
        ['title' => 'International Accreditation 2', 'logo' => 'int2.jpeg'],
        ['title' => 'International Accreditation 3', 'logo' => 'int3.jpeg']
    ];
}
if (empty($natAccreds)) {
    $natAccreds = [
        ['title' => 'Nepal Government / MOEST', 'logo' => 'nat1.jpeg'],
        ['title' => 'CTEVT Accreditation', 'logo' => 'nat2.jpeg'],
        ['title' => 'National Council Accreditation', 'logo' => 'nat3.jpeg']
    ];
}
?>
<!-- Accreditation Section -->
<section class="py-5" style="background-color: #FAF7F5;">
    <div class="container py-3">
        <div class="text-center mb-4">
            <h2 class="fw-extrabold text-dark mb-1 fs-2"><?= h($accredTitle) ?></h2>
            <p class="text-secondary small mb-0"><?= h($accredSubtitle) ?></p>
        </div>

        <!-- Filter Switcher Pills -->
        <div class="text-center mb-4">
            <div class="d-inline-flex bg-white rounded-3 shadow-sm border p-1" id="accreditation-toggle-group">
                <button type="button" class="btn accreditation-tab-btn btn-sm rounded-3 px-4 py-2 fw-bold active-accred-btn" data-target="international" style="font-size: 14px; transition: all 0.3s ease;">
                    International
                </button>
                <button type="button" class="btn accreditation-tab-btn btn-sm rounded-3 px-4 py-2 fw-bold text-dark" data-target="national" style="font-size: 14px; transition: all 0.3s ease;">
                    National
                </button>
            </div>
        </div>

        <!-- International Grid (Active by default) -->
        <div class="row g-4 justify-content-center accreditation-content-grid" id="accred-grid-international">
            <?php foreach ($intAccreds as $accred):
                $rawLogo = $accred['logo'];
                $logoUrl = (strpos($rawLogo, 'http') === 0) ? $rawLogo : ( (strpos($rawLogo, 'uploads/') === 0 || file_exists(__DIR__ . '/' . $rawLogo)) ? $basePath . $rawLogo : (file_exists(__DIR__ . '/assets/images/' . $rawLogo) ? $basePath . 'assets/images/' . $rawLogo : $basePath . $rawLogo) );
            ?>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 d-flex align-items-center justify-content-center hover-scale transition-smooth" style="min-height: 140px;" title="<?= h($accred['title'] ?? 'International Accreditation') ?>">
                        <img src="<?= h($logoUrl) ?>" alt="<?= h($accred['title'] ?? 'International Accreditation Logo') ?>" class="img-fluid" style="max-height: 85px; width: auto; object-fit: contain;">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- National Grid (Hidden by default) -->
        <div class="row g-4 justify-content-center accreditation-content-grid d-none" id="accred-grid-national">
            <?php foreach ($natAccreds as $accred):
                $rawLogo = $accred['logo'];
                $logoUrl = (strpos($rawLogo, 'http') === 0) ? $rawLogo : ( (strpos($rawLogo, 'uploads/') === 0 || file_exists(__DIR__ . '/' . $rawLogo)) ? $basePath . $rawLogo : (file_exists(__DIR__ . '/assets/images/' . $rawLogo) ? $basePath . 'assets/images/' . $rawLogo : $basePath . $rawLogo) );
            ?>
                <div class="col-12 col-sm-6 col-md-4">
                    <div class="card border-0 rounded-4 shadow-sm bg-white p-4 h-100 d-flex align-items-center justify-content-center hover-scale transition-smooth" style="min-height: 140px;" title="<?= h($accred['title'] ?? 'National Accreditation') ?>">
                        <img src="<?= h($logoUrl) ?>" alt="<?= h($accred['title'] ?? 'National Accreditation Logo') ?>" class="img-fluid" style="max-height: 85px; width: auto; object-fit: contain;">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<style>
.active-accred-btn {
    background-color: #D91F23 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 10px rgba(217, 31, 35, 0.25) !important;
}
.accreditation-tab-btn:not(.active-accred-btn):hover {
    background-color: #f1f5f9 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const accredBtns = document.querySelectorAll('.accreditation-tab-btn');
    const accredGrids = document.querySelectorAll('.accreditation-content-grid');

    accredBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            accredBtns.forEach(b => {
                b.classList.remove('active-accred-btn');
                b.classList.add('text-dark');
            });
            this.classList.add('active-accred-btn');
            this.classList.remove('text-dark');

            const target = this.getAttribute('data-target');
            accredGrids.forEach(grid => {
                if (grid.id === 'accred-grid-' + target) {
                    grid.classList.remove('d-none');
                } else {
                    grid.classList.add('d-none');
                }
            });
        });
    });
});
</script>

<!-- Leadership Perspectives Showcase: Chairman Say & Director Message -->
<section class="py-5 bg-light border-top border-bottom">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold text-uppercase mb-2">
                <i class="fa-solid fa-crown text-danger me-1"></i> Leadership Perspectives
            </span>
            <h2 class="fw-bold text-primary display-6">Voices of Trust & Commitment</h2>
            <p class="text-muted small">Guided by ethical principles, Ministry of Education (MoEST) recognition, and TITI-certified professional excellence since 2070 B.S.</p>
        </div>

        <div class="row g-4">
            <!-- Chairman Say Card -->
            <div class="col-lg-6">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4 bg-white d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle overflow-hidden shadow-sm flex-shrink-0" style="width: 65px; height: 65px;">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=200&auto=format&fit=crop&q=80" alt="Chairman - Vision Point" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <span class="badge bg-danger-subtle text-danger rounded-pill px-2.5 py-1 extra-small fw-bold mb-1">Chairman Say • अध्यक्षको भनाइ</span>
                                <h5 class="fw-bold text-dark fs-6 mb-0">Chairman's Perspective</h5>
                                <p class="text-muted extra-small mb-0">Vision Point Educational Service (Est. 2070 B.S.)</p>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded-3 border-start border-3 border-danger mb-3">
                            <p class="fst-italic text-dark mb-0 small" style="line-height: 1.7;">
                                "When we founded Vision Point in 2070 B.S. in Biratnagar, our core commitment was simple yet uncompromising: to provide honest, transparent, and student-centered educational guidance without commercial bias."
                            </p>
                        </div>
                        <p class="text-muted small" style="line-height: 1.7;">
                            Every student carries the heartfelt trust of their family. Our advisory is strictly compliant with the Ministry of Education, Science and Technology (MoEST) and professional counselor standards certified by TITI.
                        </p>
                    </div>
                    <div class="pt-2 border-top mt-auto d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small"><i class="fa-solid fa-shield-halved text-success me-1"></i> Ethical Counseling Guarantee</span>
                        <a href="about.php" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                            Read Chairman Say <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Director Message Card -->
            <div class="col-lg-6">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4 bg-white d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle overflow-hidden shadow-sm flex-shrink-0" style="width: 65px; height: 65px;">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=200&auto=format&fit=crop&q=80" alt="Managing Director - Vision Point" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div>
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5 py-1 extra-small fw-bold mb-1">Director Message • निर्देशकको सन्देश</span>
                                <h5 class="fw-bold text-dark fs-6 mb-0">Managing Director</h5>
                                <p class="text-muted extra-small mb-0">Vision Point Educational Service (Est. 2070 B.S.)</p>
                            </div>
                        </div>
                        <div class="p-3 bg-light rounded-3 border-start border-3 border-primary mb-3">
                            <p class="fst-italic text-dark mb-0 small" style="line-height: 1.7;">
                                "Our senior counselors are formally trained and certified by TITI (Training Institute for Technical Instruction). Operating with official MoEST approval, we bridge students directly to 80+ top universities with 100% transparent fee structures."
                            </p>
                        </div>
                        <p class="text-muted small" style="line-height: 1.7;">
                            From institutional scholarship advocacy and council recognitions (NMC, NNC, NEC, NPC) to pre-departure travel orientation and on-campus guardian support, we stand with our students at every step.
                        </p>
                    </div>
                    <div class="pt-2 border-top mt-auto d-flex align-items-center justify-content-between">
                        <span class="text-muted extra-small"><i class="fa-solid fa-award text-primary me-1"></i> Direct University Tie-ups</span>
                        <a href="about.php" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                            Read Director Message <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Fun Facts Section -->
<section class="fun-facts-section position-relative py-5">
    <div class="container py-3">
        <div class="row align-items-center g-4">
            <div class="col-12 col-lg-5">
                <h2 class="section-title text-white fs-2 fw-extrabold mb-3" style="line-height: 1.25;">
                    A Reflection of Our Commitment to <span class="text-warning position-relative d-inline-block">Excellence
                        <svg class="position-absolute start-0 w-100" style="bottom: -10px; height: 12px;" viewBox="0 0 100 20" preserveAspectRatio="none">
                            <path d="M0 15 Q 50 0, 100 15" stroke="#EED814" stroke-width="4" fill="none" stroke-linecap="round" />
                        </svg>
                    </span>
                </h2>
                <p class="text-white-50 fs-6 mt-3">Our track record speaks for itself. Over the years, we have helped hundreds of Nepali students find their dream colleges and pathways in India.</p>
            </div>
            <div class="col-12 col-lg-7">
                <div class="row g-3 g-md-0 align-items-center fun-facts-row">
                    <?php if (!empty($funFacts)): ?>
                        <?php 
                        $factCount = count($funFacts);
                        $colClass = ($factCount == 4) ? 'col-12 col-sm-6 col-md-3' : (($factCount == 2) ? 'col-12 col-md-6' : 'col-12 col-md-4');
                        ?>
                        <?php foreach ($funFacts as $idx => $fact): ?>
                            <div class="<?= $colClass ?> stat-item <?= $idx > 0 ? 'stat-item-bordered' : '' ?>">
                                <div class="stat-icon-wrap shadow-sm">
                                    <i class="<?= h(formatFaIcon($fact['icon'] ?? '', $fact['label'] ?? '')) ?>"></i>
                                </div>
                                <div class="stat-number text-white fw-bold">
                                    <?= h($fact['number']) ?>
                                </div>
                                <div class="stat-label text-white-50 mt-1"><?= h($fact['label']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 col-md-4 stat-item">
                            <div class="stat-icon-wrap shadow-sm">
                                <i class="fa-solid fa-building-columns"></i>
                            </div>
                            <div class="stat-number text-white fw-bold">123+</div>
                            <div class="stat-label text-white-50 mt-1">Admissions to Top Universities</div>
                        </div>
                        <div class="col-12 col-md-4 stat-item stat-item-bordered">
                            <div class="stat-icon-wrap shadow-sm">
                                <i class="fa-solid fa-award"></i>
                            </div>
                            <div class="stat-number text-white fw-bold">80%</div>
                            <div class="stat-label text-white-50 mt-1">Scholarship Success Rate</div>
                        </div>
                        <div class="col-12 col-md-4 stat-item stat-item-bordered">
                            <div class="stat-icon-wrap shadow-sm">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <div class="stat-number text-white fw-bold">95%</div>
                            <div class="stat-label text-white-50 mt-1">Student Satisfaction Rate</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Guides Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="section-intro mb-0">
                <h2 class="section-title text-primary fs-3">Recent Guides & Updates</h2>
                <p class="text-muted mb-0">Read resources and step-by-step guides compiled for Nepali students studying in India.</p>
            </div>
            <a href="guides.php" class="btn btn-outline-primary rounded-pill px-4 btn-sm fw-semibold">View All</a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($guides as $guide): 
                $guideImg = !empty($guide['img']) ? ( (strpos($guide['img'], 'http') === 0) ? $guide['img'] : $basePath . $guide['img'] ) : 'https://images.unsplash.com/photo-1517842645767-c639042777db?w=600&auto=format&fit=crop&q=60';
                $formattedDate = !empty($guide['date']) ? date('M d, Y', strtotime($guide['date'])) : 'Recent';
            ?>
                <div class="col-12 col-md-4">
                    <div class="card guide-card border-0 overflow-hidden shadow-sm h-100 hover-scale bg-white">
                        <a href="guide-detail.php?id=<?= $guide['id'] ?>" class="d-block" style="height: 200px; overflow: hidden;">
                            <img src="<?= h($guideImg) ?>" class="w-100 h-100 object-fit-cover" alt="<?= h($guide['title']) ?>">
                        </a>
                        <div class="card-body p-4">
                            <div class="text-muted small mb-2"><i class="fa-solid fa-calendar me-2"></i><?= h($formattedDate) ?></div>
                            <h5 class="fw-bold text-primary mb-3">
                                <a href="guide-detail.php?id=<?= $guide['id'] ?>" class="text-decoration-none text-primary hover-underline"><?= h($guide['title']) ?></a>
                            </h5>
                            <a href="guide-detail.php?id=<?= $guide['id'] ?>" class="text-secondary fw-semibold text-decoration-none small">Read More <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Certified Education Counselors & Advisory Team Section -->
<section class="py-5 bg-white border-top border-light position-relative" id="counselors-team">
    <div class="container py-3">
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div>
                <div class="d-inline-flex align-items-center gap-2 bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold extra-small text-uppercase mb-2">
                    <i class="fa-solid fa-certificate"></i> MoEST Approved &bull; TITI Certified Counselors
                </div>
                <h2 class="fw-extrabold text-dark mb-1" style="font-size: 2rem; color: #1e293b;">Meet Our Certified Education Counselors</h2>
                <p class="text-muted mb-0 small" style="max-width: 680px;">
                    Trained and certified by CTEVT's Training Institute for Technical Instruction (TITI) and global education councils. Dedicated to ethical, transparent, and student-first academic counseling.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-danger btn-sm rounded-pill px-4 fw-bold hover-scale" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                    <i class="fa-solid fa-calendar-check me-1"></i> Book Free Consultation
                </button>
                <a href="<?= h($basePath) ?>about.php#counselors-team" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold hover-scale">
                    All Team Profiles <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- Counselor Grid -->
        <div class="row g-4">
            <?php foreach ($teamMembers as $member): 
                $mRawPhoto = !empty($member['photo']) ? $member['photo'] : 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=500&auto=format&fit=crop&q=80';
                $mPhoto = (strpos($mRawPhoto, 'http') === 0 || strpos($mRawPhoto, '/') === 0) ? $mRawPhoto : ($basePath . $mRawPhoto);
                $isTiti = stripos($member['qualification'] ?? '', 'TITI') !== false;
                $isQeac = stripos($member['qualification'] ?? '', 'QEAC') !== false;
                $isBritishCouncil = stripos($member['qualification'] ?? '', 'British Council') !== false;
                $firstName = explode(' ', $member['name'])[0];
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border rounded-4 overflow-hidden shadow-sm counselor-card-premium bg-white position-relative d-flex flex-column justify-content-between">
                    <!-- Top Department & Experience Bar -->
                    <div class="counselor-top-bar d-flex justify-content-between align-items-center px-4 py-2 bg-light border-bottom">
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1 extra-small fw-semibold">
                            <i class="fa-solid fa-briefcase me-1"></i><?= h($member['department']) ?>
                        </span>
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle rounded-pill px-2.5 py-1 extra-small fw-bold">
                            <i class="fa-solid fa-clock-rotate-left me-1 text-primary"></i><?= (int)$member['experience_years'] ?>+ Years Exp
                        </span>
                    </div>

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex align-items-start gap-3 mb-3">
                            <div class="position-relative flex-shrink-0" style="width: 72px; height: 72px;">
                                <img src="<?= h($mPhoto) ?>" alt="<?= h($member['name']) ?>" class="w-100 h-100 rounded-circle object-fit-cover shadow-sm border border-2 border-primary">
                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle pulse-dot" style="width: 13px; height: 13px;" title="Active Advisor"></span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-1.5 flex-wrap mb-1">
                                    <?php if ($isTiti): ?>
                                        <span class="badge bg-danger text-white rounded-pill extra-small px-2 py-0.5" title="Trained & Certified by CTEVT TITI">
                                            <i class="fa-solid fa-award me-1"></i>TITI Certified
                                        </span>
                                    <?php elseif ($isQeac): ?>
                                        <span class="badge bg-success text-white rounded-pill extra-small px-2 py-0.5">
                                            <i class="fa-solid fa-stamp me-1"></i>QEAC Certified
                                        </span>
                                    <?php elseif ($isBritishCouncil): ?>
                                        <span class="badge bg-info text-dark rounded-pill extra-small px-2 py-0.5">
                                            <i class="fa-solid fa-chalkboard-user me-1"></i>Master Trainer
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white rounded-pill extra-small px-2 py-0.5">
                                            <i class="fa-solid fa-user-check me-1"></i>Certified
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="fw-bold text-dark mb-0 fs-6"><?= h($member['name']) ?></h5>
                                <div class="text-primary small fw-semibold"><?= h($member['role']) ?></div>
                            </div>
                        </div>

                        <!-- Qualification & Specialization -->
                        <div class="mb-3 bg-light rounded-3 p-2.5 small">
                            <div class="text-secondary mb-1 extra-small">
                                <i class="fa-solid fa-graduation-cap text-danger me-1"></i><strong>Education:</strong> <?= h($member['qualification']) ?>
                            </div>
                            <div class="text-muted extra-small">
                                <i class="fa-solid fa-tags text-primary me-1"></i><strong>Specialty:</strong> <?= h($member['specialization']) ?>
                            </div>
                        </div>

                        <p class="text-secondary small mb-4 flex-grow-1" style="line-height: 1.55;">
                            <?= h($member['bio']) ?>
                        </p>

                        <!-- Card Action CTAs -->
                        <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between gap-2">
                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-semibold w-100 hover-scale" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                                <i class="fa-solid fa-user-plus me-1"></i> Consult <?= h($firstName) ?>
                            </button>
                            <a href="tel:<?= h($member['phone'] ?? '+9779807765812') ?>" class="btn btn-light btn-sm rounded-circle border shadow-2xs text-primary flex-shrink-0 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Call Advisor">
                                <i class="fa-solid fa-phone"></i>
                            </a>
                            <a href="mailto:<?= h($member['email'] ?? 'info@visionpoint.edu.np') ?>" class="btn btn-light btn-sm rounded-circle border shadow-2xs text-secondary flex-shrink-0 d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="Email Advisor">
                                <i class="fa-solid fa-envelope"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Staff Credential Trust Footer Strip -->
        <div class="mt-4 p-3 bg-light rounded-4 border d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 44px; height: 44px;">
                    <i class="fa-solid fa-shield-halved fs-5"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark small mb-0">Government of Nepal MoEST & TITI Certified Advisory Standards</div>
                    <div class="text-muted extra-small">All counselors follow strict ethical counseling codes, zero visa misrepresentation, and direct transparent fee invoices.</div>
                </div>
            </div>
            <a href="tel:+9779807765812" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold hover-scale">
                <i class="fa-solid fa-headset me-1"></i> Helpline: +977 9807765812
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5" style="background-color: #FAF7F5;" id="testimonials">
    <div class="container py-3">
        <!-- Section Header -->
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-3">
            <div class="section-intro mb-0">
                <div class="d-inline-flex align-items-center gap-2 bg-danger-subtle text-danger px-3 py-1 rounded-pill fw-bold extra-small text-uppercase mb-2">
                    <i class="fa-solid fa-star text-warning"></i> VERIFIED STUDENT EXPERIENCES
                </div>
                <h2 class="fw-extrabold text-dark mb-1" style="font-size: 2rem; color: #1e293b;">Trusted by Students & Families Across Nepal</h2>
                <p class="text-muted mb-0 small" style="max-width: 680px;">
                    Real feedback and success stories from Nepali students who achieved their dream college admissions, scholarships, and international visas through Vision Point.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 btn-sm fw-bold shadow-sm hover-scale" data-bs-toggle="modal" data-bs-target="#homeAddReviewModal" style="border-color: #D91F23 !important; color: #D91F23 !important;">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Write a Review
                </button>
                <button type="button" class="btn btn-danger rounded-pill px-4 btn-sm fw-bold shadow-sm hover-scale toggle-home-reviews-btn" style="background-color: #D91F23 !important; border-color: #D91F23 !important; color: #ffffff !important;">
                    View All Reviews <i class="fa-solid fa-chevron-down ms-1"></i>
                </button>
            </div>
        </div>

        <!-- Trust Stats Bar -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 text-center h-100">
                    <div class="text-warning fs-5 fw-bold mb-1">
                        <i class="fa-solid fa-star"></i> 5.0 / 5.0
                    </div>
                    <div class="text-muted extra-small">Average Student Rating</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 text-center h-100">
                    <div class="text-primary fs-5 fw-bold mb-1">
                        <i class="fa-solid fa-user-graduate"></i> 1,200+
                    </div>
                    <div class="text-muted extra-small">Admissions Guided</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 text-center h-100">
                    <div class="text-success fs-5 fw-bold mb-1">
                        <i class="fa-solid fa-award"></i> 80%
                    </div>
                    <div class="text-muted extra-small">Scholarship Success Rate</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 rounded-4 shadow-sm bg-white p-3 text-center h-100">
                    <div class="text-danger fs-5 fw-bold mb-1">
                        <i class="fa-solid fa-shield-check"></i> 100%
                    </div>
                    <div class="text-muted extra-small">Council Equivalence Valid</div>
                </div>
            </div>
        </div>

        <!-- Review Cards Primary Grid (Top 3) -->
        <div class="row g-4 justify-content-center">
            <?php 
            $primaryReviews = array_slice($homeReviews, 0, 3);
            $extraReviews = array_slice($homeReviews, 3);
            
            foreach ($primaryReviews as $rev): 
                $revName = h($rev['name']);
                $revCourse = !empty($rev['course_college']) ? h($rev['course_college']) : 'Student';
                $revTitle = !empty($rev['title']) ? h($rev['title']) : 'Excellent Guidance';
                $revComment = h($rev['comment']);
                $revRating = !empty($rev['rating']) ? (int)$rev['rating'] : 5;
                $revDate = !empty($rev['created_at']) ? date('M d, Y', strtotime($rev['created_at'])) : 'Recent';
                $rawAvatar = !empty($rev['avatar']) ? $rev['avatar'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80';
                $revAvatar = (strpos($rawAvatar, 'http') === 0 || strpos($rawAvatar, '/') === 0) ? $rawAvatar : ($basePath . $rawAvatar);
            ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm bg-white h-100 overflow-hidden position-relative d-flex flex-column justify-content-between testimonial-card-custom hover-scale p-4">
                        <div>
                            <!-- Star Rating & Quote Mark -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center text-warning small gap-1">
                                    <?php for ($s = 1; $s <= 5; $s++): ?>
                                        <i class="fa-solid fa-star <?= $s <= $revRating ? 'text-warning' : 'text-black-50' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small px-2 py-0.5">
                                    <i class="fa-solid fa-circle-check me-1"></i>Verified Student
                                </span>
                            </div>

                            <h6 class="fw-bold text-dark mb-2"><?= $revTitle ?></h6>
                            <p class="text-secondary mb-4 small" style="line-height: 1.6; font-size: 0.92rem;">
                                &ldquo;<?= $revComment ?>&rdquo;
                            </p>
                        </div>

                        <!-- Student Profile Footer -->
                        <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-2.5">
                                <img src="<?= $revAvatar ?>" alt="<?= $revName ?>" class="rounded-circle object-fit-cover shadow-2xs border" style="width: 46px; height: 46px;">
                                <div>
                                    <div class="fw-bold text-dark mb-0 small"><?= $revName ?></div>
                                    <div class="text-muted extra-small text-truncate" style="max-width: 170px;" title="<?= $revCourse ?>"><?= $revCourse ?></div>
                                </div>
                            </div>
                            <span class="text-muted extra-small flex-shrink-0"><?= $revDate ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if (!empty($extraReviews)): ?>
            <!-- Additional Reviews Expanded Grid (Inline View All on Home Page) -->
            <div class="row g-4 justify-content-center mt-1 d-none" id="home-extra-reviews">
                <?php foreach ($extraReviews as $rev): 
                    $revName = h($rev['name']);
                    $revCourse = !empty($rev['course_college']) ? h($rev['course_college']) : 'Student';
                    $revTitle = !empty($rev['title']) ? h($rev['title']) : 'Smooth Admission Process';
                    $revComment = h($rev['comment']);
                    $revRating = !empty($rev['rating']) ? (int)$rev['rating'] : 5;
                    $revDate = !empty($rev['created_at']) ? date('M d, Y', strtotime($rev['created_at'])) : 'Recent';
                    $rawAvatar = !empty($rev['avatar']) ? $rev['avatar'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80';
                    $revAvatar = (strpos($rawAvatar, 'http') === 0 || strpos($rawAvatar, '/') === 0) ? $rawAvatar : ($basePath . $rawAvatar);
                ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 rounded-4 shadow-sm bg-white h-100 overflow-hidden position-relative d-flex flex-column justify-content-between testimonial-card-custom hover-scale p-4">
                            <div>
                                <!-- Star Rating & Quote Mark -->
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="d-flex align-items-center text-warning small gap-1">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <i class="fa-solid fa-star <?= $s <= $revRating ? 'text-warning' : 'text-black-50' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill extra-small px-2 py-0.5">
                                        <i class="fa-solid fa-circle-check me-1"></i>Verified Student
                                    </span>
                                </div>

                                <h6 class="fw-bold text-dark mb-2"><?= $revTitle ?></h6>
                                <p class="text-secondary mb-4 small" style="line-height: 1.6; font-size: 0.92rem;">
                                    &ldquo;<?= $revComment ?>&rdquo;
                                </p>
                            </div>

                            <!-- Student Profile Footer -->
                            <div class="pt-3 border-top mt-auto d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <img src="<?= $revAvatar ?>" alt="<?= $revName ?>" class="rounded-circle object-fit-cover shadow-2xs border" style="width: 46px; height: 46px;">
                                    <div>
                                        <div class="fw-bold text-dark mb-0 small"><?= $revName ?></div>
                                        <div class="text-muted extra-small text-truncate" style="max-width: 170px;" title="<?= $revCourse ?>"><?= $revCourse ?></div>
                                    </div>
                                </div>
                                <span class="text-muted extra-small flex-shrink-0"><?= $revDate ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4 pt-2 d-flex align-items-center justify-content-center gap-3 flex-wrap">
            <?php if (!empty($extraReviews)): ?>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2 fw-bold shadow-sm hover-scale toggle-home-reviews-btn" style="border-color: #D91F23 !important; color: #D91F23 !important;">
                    View All Reviews <i class="fa-solid fa-chevron-down ms-1"></i>
                </button>
            <?php endif; ?>
            <a href="<?= h($basePath) ?>reviews.php" class="btn btn-danger rounded-pill px-4 py-2 fw-bold shadow-sm hover-scale text-decoration-none" style="background-color: #D91F23 !important; border-color: #D91F23 !important; color: #ffffff !important;">
                Explore Dedicated Reviews Page <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtns = document.querySelectorAll('.toggle-home-reviews-btn');
    const extraGrid = document.getElementById('home-extra-reviews');

    if (toggleBtns.length && extraGrid) {
        toggleBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                if (extraGrid.classList.contains('d-none')) {
                    extraGrid.classList.remove('d-none');
                    toggleBtns.forEach(b => {
                        b.innerHTML = 'Show Less <i class="fa-solid fa-chevron-up ms-1"></i>';
                    });
                } else {
                    extraGrid.classList.add('d-none');
                    toggleBtns.forEach(b => {
                        b.innerHTML = 'View All Reviews <i class="fa-solid fa-chevron-down ms-1"></i>';
                    });
                }
            });
        });
    }
});
</script>

<!-- WRITE REVIEW MODAL (HOME PAGE) -->
<div class="modal fade" id="homeAddReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header text-white py-3" style="background-color: #D91F23 !important;">
                <h5 class="modal-title fw-bold text-white"><i class="fa-solid fa-pen-to-square me-2"></i>Write a Student Review</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="home-review-form" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="submit_review">
                <div class="modal-body p-4">
                    <div id="home-review-alert"></div>
                    <div class="row g-3">
                        <div class="col-12 col-md-6 form-group">
                            <label class="form-label fw-semibold small text-dark">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Yugandhar Yadav" required>
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label class="form-label fw-semibold small text-dark">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="e.g. yugandhar@example.com" required>
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label class="form-label fw-semibold small text-dark">Course / College (Subtitle)</label>
                            <input type="text" name="course_college" class="form-control" placeholder="e.g. B.Tech CSE (SRM College)">
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label class="form-label fw-semibold small text-dark">Star Rating <span class="text-danger">*</span></label>
                            <select name="rating" class="form-select">
                                <option value="5" selected>5 Stars - Excellent</option>
                                <option value="4">4 Stars - Very Good</option>
                                <option value="3">3 Stars - Good</option>
                                <option value="2">2 Stars - Fair</option>
                                <option value="1">1 Star - Poor</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label class="form-label fw-semibold small text-dark">Upload Profile Photo (Optional)</label>
                            <input type="file" name="avatar_file" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12 col-md-6 form-group">
                            <label class="form-label fw-semibold small text-dark">Or Photo Image URL (Optional)</label>
                            <input type="text" name="avatar" class="form-control" placeholder="https://example.com/photo.jpg">
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label fw-semibold small text-dark">Headline / Short Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Exceptional Guidance & Support!">
                        </div>
                        <div class="col-12 form-group">
                            <label class="form-label fw-semibold small text-dark">Your Review / Experience <span class="text-danger">*</span></label>
                            <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience studying through Vision Point Educational Service..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" id="home_review_submit_btn" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold" style="background-color: #D91F23 !important; border-color: #D91F23 !important;">Submit Review</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.testimonial-card-custom {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid #EDEDED !important;
}
.testimonial-card-custom:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08) !important;
}
.testimonial-red-banner {
    background-color: #D91F23 !important;
    color: #ffffff !important;
    padding: 12px 24px !important;
}
.speech-arrow-up {
    position: absolute;
    top: -9px;
    left: 45px;
    width: 0;
    height: 0;
    border-left: 9px solid transparent;
    border-right: 9px solid transparent;
    border-bottom: 9px solid #D91F23;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const homeRevForm = document.getElementById('home-review-form');
    if (homeRevForm) {
        homeRevForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('home_review_submit_btn');
            const alertContainer = document.getElementById('home-review-alert');
            const origText = btn.innerText;
            btn.disabled = true;
            btn.innerText = 'Submitting...';

            const formData = new FormData(this);

            fetch('<?= h($basePath) ?>submit_form.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.innerText = origText;
                if (data.success) {
                    alertContainer.innerHTML = `<div class="alert alert-success alert-dismissible fade show rounded-3 small mb-3" role="alert"><i class="fa-solid fa-circle-check me-2"></i>${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
                    homeRevForm.reset();
                } else {
                    alertContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3" role="alert"><i class="fa-solid fa-triangle-exclamation me-2"></i>${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerText = origText;
                alertContainer.innerHTML = `<div class="alert alert-danger alert-dismissible fade show rounded-3 small mb-3" role="alert"><i class="fa-solid fa-triangle-exclamation me-2"></i>An error occurred. Please try again.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            });
        });
    }
});
</script>

<!-- Newsletter Section -->
<section class="subscribe-newsletter-section bg-light">
    <div class="container">
        <div class="subscribe-box text-center">
            <h2 class="text-dark fw-bold mb-2">Subscribe to our Newsletter!</h2>
            <p class="text-dark mb-4 max-w-600 mx-auto" style="opacity: 0.85;">Stay informed about the latest admission cycles, scholarship details, and developments in Indian education.</p>
            
            <form id="subscribe_form" class="max-w-500 mx-auto bg-white p-1 rounded-pill d-flex align-items-center shadow-sm">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="email" name="email" placeholder="Enter your email" class="form-control border-0 bg-transparent flex-grow-1 px-4 text-dark" style="box-shadow:none;" required>
                <button type="submit" id="submit_subscribe_form" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<!-- Payments & QR Code Section -->
<?php
require_once __DIR__ . '/includes/payment_section.php';
?>

<!-- Contact Form Section -->
<section class="get-in-touch py-5 bg-white border-top border-light">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-12 col-md-6">
                <h2 class="section-title text-primary fs-2 mb-3">Get in touch with us</h2>
                <p class="text-muted mb-4">Fill out the inquiry form, and our education counselor will call you within 24 hours to clarify your questions about programs, fees, and scholarships.</p>
                
                <!-- Counselor Contact Highlight -->
                <div class="mt-4 p-3 bg-white rounded-4 d-flex align-items-center gap-3 border border-light-subtle shadow-sm counselor-spotlight-card" style="max-width: 410px; transition: transform 0.3s ease, box-shadow 0.3s ease;">
                    <div class="position-relative flex-shrink-0" style="width: 64px; height: 64px;">
                        <div class="overflow-hidden rounded-circle shadow-sm border border-2 border-primary w-100 h-100">
                            <img src="<?= h($basePath) ?>assets/images/counselor.png" alt="Senior Admissions Advisor - Vision Point Educational Service" class="w-100 h-100 object-fit-cover" style="object-position: center 15%;">
                        </div>
                        <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle pulse-dot" style="width: 14px; height: 14px;" title="Online Now"></span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-bold text-primary mb-0 fs-6">Aaradhya Sharma</div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle py-0 px-2 rounded-pill fw-semibold" style="font-size: 10px;"><i class="fa-solid fa-circle-check me-1"></i>Verified</span>
                        </div>
                        <div class="text-secondary small mb-1" style="font-size: 12px;"><i class="fa-solid fa-graduation-cap text-primary me-1"></i>Lead Admissions Advisor</div>
                        <div class="text-muted extra-small" style="font-size: 11px;">100% Free 1-on-1 University Guidance</div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6">
                <div class="card border border-light p-4 rounded-4 shadow-sm bg-light">
                    <form id="contact-form">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                        <div class="row">
                            <div class="col-12 col-sm-6 form-group">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="full_name" placeholder="Enter your Full name" required>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Phone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="phone_number" placeholder="Phone Number" required>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" name="email" placeholder="example@email.com" required>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Address</label>
                                <input type="text" class="form-control" name="address" placeholder="Kathmandu">
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Country Interest <span class="text-danger">*</span></label>
                                <select class="form-select" name="country" required>
                                    <option value="" selected>Select Country</option>
                                    <?php if (!empty($countries)): ?>
                                        <?php foreach ($countries as $c): ?>
                                            <option value="<?= h($c['name']) ?>"><?= h($c['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="India">India</option>
                                        <option value="US">US</option>
                                        <option value="Canada">Canada</option>
                                        <option value="UK">UK</option>
                                        <option value="Australia">Australia</option>
                                        <option value="Europe">Europe</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 form-group">
                                <label>Course Stream</label>
                                <select class="form-select" name="course">
                                    <option value="" selected>Select Course</option>
                                    <?php if (!empty($courses)): ?>
                                        <?php foreach ($courses as $c): ?>
                                            <option value="<?= h($c['name']) ?>"><?= h($c['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Nursing">Nursing</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Pharmacy">Pharmacy</option>
                                        <option value="Management">Management</option>
                                        <option value="Computer Application / IT">IT & Computer Applications</option>
                                        <option value="Medical">Medical (MBBS)</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-12 form-group">
                                <label>Message</label>
                                <textarea class="form-control" name="message" placeholder="What is your inquiry?" rows="3"></textarea>
                            </div>
                            <div class="col-12 mt-3">
                                <button type="submit" id="submit-contact-form" class="btn btn-secondary w-100 py-3 fw-bold rounded-pill">Submit Inquiry</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    var showEnrollModalOnLoad = true;
</script>
<!-- Video Tour Cinema Lightbox Modal -->
<div class="modal fade" id="videoTourModal" tabindex="-1" aria-labelledby="videoTourModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-xl">
        <div class="modal-content bg-dark border-0 rounded-4 overflow-hidden shadow-2xl">
            <div class="modal-header border-0 bg-dark text-white py-2 px-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fa-solid fa-play-circle text-warning fs-5"></i>
                    <h6 class="modal-title fw-bold text-white mb-0" id="videoTourModalLabel"><?= h($videoTitle) ?></h6>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-black position-relative text-center">
                <?php if ($isYouTube): ?>
                    <div class="ratio ratio-16x9">
                        <iframe id="modalTourIframe" src="<?= h($youtubeEmbedUrl) ?>" class="w-100 h-100" style="border:0;" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>
                    </div>
                <?php else: ?>
                    <video id="modalTourVideo" class="w-100 d-block" controls playsinline preload="metadata" style="max-height: 80vh; aspect-ratio: 16/9; object-fit: contain;">
                        <source src="<?= h($videoFileUrl) ?>" type="video/mp4">
                        Your browser does not support HTML5 video.
                    </video>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Video Playback & Sound Control Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Hero Video Controls
    const heroVideo = document.getElementById('heroIntroVideo');
    const heroSoundBtn = document.getElementById('heroVideoSoundToggle');
    const heroSoundIcon = document.getElementById('heroSoundIcon');
    const heroSoundText = document.getElementById('heroSoundText');
    const heroPlayPauseBtn = document.getElementById('heroVideoPlayPause');
    const heroPlayPauseIcon = document.getElementById('heroPlayPauseIcon');

    if (heroVideo && heroSoundBtn) {
        heroSoundBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (heroVideo.muted) {
                heroVideo.muted = false;
                heroSoundIcon.className = 'fa-solid fa-volume-high';
                heroSoundText.textContent = 'Mute';
            } else {
                heroVideo.muted = true;
                heroSoundIcon.className = 'fa-solid fa-volume-xmark';
                heroSoundText.textContent = 'Unmute';
            }
        });
    }

    if (heroVideo && heroPlayPauseBtn) {
        heroPlayPauseBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (heroVideo.paused) {
                heroVideo.play();
                heroPlayPauseIcon.className = 'fa-solid fa-pause';
            } else {
                heroVideo.pause();
                heroPlayPauseIcon.className = 'fa-solid fa-play';
            }
        });
    }

    // 2. Spotlight Video Controls
    const spotVideo = document.getElementById('spotlightVideo');
    const spotPlayBtn = document.getElementById('spotlightPlayBtn');
    const spotPlayIcon = document.getElementById('spotlightPlayIcon');
    const spotMuteBtn = document.getElementById('spotlightMuteBtn');
    const spotMuteIcon = document.getElementById('spotlightMuteIcon');
    const spotMuteText = document.getElementById('spotlightMuteText');

    if (spotVideo && spotPlayBtn) {
        spotPlayBtn.addEventListener('click', function() {
            if (spotVideo.paused) {
                spotVideo.play();
                spotPlayIcon.className = 'fa-solid fa-pause fs-6';
            } else {
                spotVideo.pause();
                spotPlayIcon.className = 'fa-solid fa-play fs-6';
            }
        });
    }

    if (spotVideo && spotMuteBtn) {
        spotMuteBtn.addEventListener('click', function() {
            if (spotVideo.muted) {
                spotVideo.muted = false;
                spotMuteIcon.className = 'fa-solid fa-volume-high';
                spotMuteText.textContent = 'Mute';
            } else {
                spotVideo.muted = true;
                spotMuteIcon.className = 'fa-solid fa-volume-xmark';
                spotMuteText.textContent = 'Unmute';
            }
        });
    }

    // 3. Modal Video Handling (Auto-play with sound on modal open, pause on close)
    const tourModal = document.getElementById('videoTourModal');
    const modalVideo = document.getElementById('modalTourVideo');

    if (tourModal && modalVideo) {
        tourModal.addEventListener('shown.bs.modal', function() {
            modalVideo.currentTime = 0;
            modalVideo.muted = false;
            modalVideo.play().catch(function() {
                // Autoplay with sound may be blocked by browser policy without user gesture
                modalVideo.muted = true;
                modalVideo.play();
            });
            // Pause background videos while modal is open
            if (heroVideo && !heroVideo.paused) heroVideo.pause();
            if (spotVideo && !spotVideo.paused) spotVideo.pause();
        });

        tourModal.addEventListener('hidden.bs.modal', function() {
            modalVideo.pause();
            // Resume background videos
            if (heroVideo) heroVideo.play();
            if (spotVideo) spotVideo.play();
        });
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
