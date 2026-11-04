<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'test-preparation');
require_once __DIR__ . '/includes/header.php';

// Sanitize requested test tab
$activeTest = isset($_GET['test']) ? strtolower(trim($_GET['test'])) : 'ielts';
$allowedTests = ['ielts', 'pte', 'toefl', 'sat'];
if (!in_array($activeTest, $allowedTests)) {
    $activeTest = 'ielts';
}

// Fetch test-preparation page content
$pageData = null;
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = 'test-preparation' LIMIT 1");
        $stmt->execute();
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fallbacks
$pageTitle = $pageData['title'] ?? 'Test Preparation';
$heroTitle = $pageData['hero_title'] ?? 'Test Preparation';
$heroSubtitle = $pageData['hero_subtitle'] ?? 'Our expert teachers, mock tests, and smart study materials ensure you achieve your target scores on the first attempt.';
$tests = [];
$features = [];

if ($pageData && !empty($pageData['content'])) {
    $content = json_decode($pageData['content'], true);
    $tests = $content['tests'] ?? [];
    $features = $content['features'] ?? [];
} else {
    // Defaults
    $tests = [
        'ielts' => [
            'name' => 'International English Language Testing System (IELTS)',
            'desc' => 'IELTS is the world\'s most popular English language proficiency test for higher education and global migration. It evaluates your skills in listening, reading, writing, and speaking.',
            'duration' => '2.5 Hr',
            'range' => '0 - 9',
            'target' => '6.5+',
            'validity' => '2 Yrs',
            'format' => [
                ['Listening', '30 minutes (4 sections, 40 questions)'],
                ['Reading', '60 minutes (3 texts, 40 questions)'],
                ['Writing', '60 minutes (2 tasks - descriptive & essay)'],
                ['Speaking', '11–14 minutes (Face-to-face interview)']
            ]
        ],
        'pte' => [
            'name' => 'Pearson Test of English (PTE Academic)',
            'desc' => 'PTE Academic is a computer-based English language test accepted by educational institutions and governments globally. It utilizes AI scoring for rapid results, typically within 48 hours.',
            'duration' => '2.0 Hr',
            'range' => '10 - 90',
            'target' => '58+',
            'validity' => '2 Yrs',
            'format' => [
                ['Speaking & Writing', '54–67 minutes (combined section with essays/read alouds)'],
                ['Reading', '29–30 minutes (multiple-choice, fill in blanks)'],
                ['Listening', '30–43 minutes (dictation, summaries, audio playback)']
            ]
        ],
        'toefl' => [
            'name' => 'Test of English as a Foreign Language (TOEFL iBT)',
            'desc' => 'TOEFL iBT measures your ability to use and understand English at the university level. It is widely preferred in the USA and preferred by over 11,000 institutions internationally.',
            'duration' => '2.0 Hr',
            'range' => '0 - 120',
            'target' => '80+',
            'validity' => '2 Yrs',
            'format' => [
                ['Reading', '35 minutes (20 questions from academic texts)'],
                ['Listening', '36 minutes (28 questions from lectures/conversations)'],
                ['Speaking', '16 minutes (4 integrated tasks)'],
                ['Writing', '29 minutes (2 tasks - academic discussion & integrated)']
            ]
        ],
        'sat' => [
            'name' => 'Scholastic Assessment Test (Digital SAT)',
            'desc' => 'The SAT is a standardized college admission test used in the United States and several other countries. It measures high school students\' readiness for college-level courses, focusing on Math and English.',
            'duration' => '2.1 Hr',
            'range' => '400-1600',
            'target' => '1300+',
            'validity' => '5 Yrs',
            'format' => [
                ['Reading & Writing', '64 minutes (54 questions on passages and sentences)'],
                ['Mathematics', '70 minutes (44 questions covering algebra, advanced math, geometry)']
            ]
        ]
    ];
    $features = [
        ['title' => 'Certified Instructors', 'text' => 'Learn from highly experienced teachers certified by British Council and Pearson.', 'icon' => 'fa-chalkboard-user'],
        ['title' => 'State-of-the-Art Computer Lab', 'text' => 'Practice on full-length simulator tests exactly representing the real digital tests.', 'icon' => 'fa-laptop'],
        ['title' => 'Weekly Mock Tests', 'text' => 'Evaluate your weak points with detailed performance feedbacks and review sessions every Sunday.', 'icon' => 'fa-file-signature'],
        ['title' => 'Free Study Materials', 'text' => 'Get access to premium books, vocabulary logs, and mock portals without extra costs.', 'icon' => 'fa-book-open-reader']
    ];
}
?>

<div class="container py-5 animate-fade-in">
    <div class="row g-5">
        <!-- Main Content Area -->
        <div class="col-lg-8">
            <span class="badge bg-warning text-dark fw-bold mb-3 px-3 py-2 rounded-pill uppercase">
                <i class="fa-solid fa-graduation-cap me-1"></i> Score High with Us
            </span>
            <h1 class="fw-bold text-primary mb-4" style="font-size: 2.75rem; letter-spacing: -0.5px;"><?= h($pageTitle) ?></h1>
            <p class="lead text-muted mb-4"><?= h($heroSubtitle) ?></p>
            
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-fill border-0 mb-4 bg-light p-2 rounded-4 shadow-sm" id="testPrepTabs" role="tablist" style="gap: 5px;">
                <?php foreach ($allowedTests as $tKey): 
                    $tName = ($tKey === 'pte') ? 'PTE Academic' : strtoupper($tKey);
                ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold rounded-3 py-3 border-0 transition-smooth <?= ($activeTest === $tKey) ? 'active bg-white text-danger shadow-sm' : 'text-primary bg-transparent' ?>" 
                                id="<?= $tKey ?>-tab" data-bs-toggle="tab" data-bs-target="#<?= $tKey ?>-content" type="button" role="tab" aria-controls="<?= $tKey ?>-content" aria-selected="<?= ($activeTest === $tKey) ? 'true' : 'false' ?>">
                            <?= h($tName) ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content bg-white p-4 rounded-4 shadow-sm border border-light" id="testPrepTabsContent">
                
                <?php foreach ($tests as $tKey => $test): ?>
                    <div class="tab-pane fade <?= ($activeTest === $tKey) ? 'show active' : '' ?>" id="<?= $tKey ?>-content" role="tabpanel" aria-labelledby="<?= $tKey ?>-tab">
                        <h4 class="fw-bold text-primary mb-3"><?= h($test['name']) ?></h4>
                        <p class="text-muted"><?= h($test['desc']) ?></p>
                        
                        <div class="row g-3 my-4">
                            <div class="col-6 col-sm-3 text-center">
                                <div class="bg-light p-3 rounded-3 border border-light">
                                    <span class="fw-bold text-danger d-block fs-4"><?= h($test['duration']) ?></span>
                                    <span class="text-muted small">Duration</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3 text-center">
                                <div class="bg-light p-3 rounded-3 border border-light">
                                    <span class="fw-bold text-danger d-block fs-4"><?= h($test['range']) ?></span>
                                    <span class="text-muted small">Score Range</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3 text-center">
                                <div class="bg-light p-3 rounded-3 border border-light">
                                    <span class="fw-bold text-danger d-block fs-4"><?= h($test['target']) ?></span>
                                    <span class="text-muted small">Target Score</span>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3 text-center">
                                <div class="bg-light p-3 rounded-3 border border-light">
                                    <span class="fw-bold text-danger d-block fs-4"><?= h($test['validity']) ?></span>
                                    <span class="text-muted small">Validity</span>
                                </div>
                            </div>
                        </div>

                        <h5 class="fw-bold text-primary mb-3">Exam Format</h5>
                        <ul class="list-group list-group-flush mb-4">
                            <?php foreach ($test['format'] as $fmt): ?>
                                <li class="list-group-item bg-transparent border-light py-2"><strong class="text-dark"><?= h($fmt[0]) ?>:</strong> <?= h($fmt[1]) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        
                        <button class="btn btn-secondary rounded-pill px-4" data-course-id="14" data-college="<?= h(strtoupper($tKey)) ?> Test Preparation" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                            Book Trial <?= h(strtoupper($tKey)) ?> Class
                        </button>
                    </div>
                <?php endforeach; ?>

            </div>

            <!-- Features of Prep Classes -->
            <h3 class="fw-bold text-primary mb-4 mt-5">Why Prepare at Krishna Education Foundation?</h3>
            <div class="row g-4">
                <?php foreach ($features as $feat): ?>
                    <div class="col-12 col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <div class="bg-light text-danger rounded-circle p-3 d-flex align-items-center justify-content-center" style="min-width: 50px; height: 50px;">
                                <i class="fa-solid <?= h($feat['icon'] ?? 'fa-graduation-cap') ?> fs-5"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-primary mb-1"><?= h($feat['title']) ?></h6>
                                <p class="text-muted small"><?= h($feat['text']) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
        
        <!-- Sidebar Form CTA -->
        <div class="col-lg-4">
            <div class="card border border-light shadow-sm p-4 sticky-top bg-light" style="top: 100px; z-index: 10;">
                <h4 class="fw-bold text-primary mb-3">Trial Class Booking</h4>
                <p class="text-muted small">We run daily morning and afternoon batches for IELTS and PTE. Sign up today for a 3-day free trial class to check our training methods.</p>
                <hr>
                <button class="btn btn-secondary w-100 py-3 fw-bold rounded-pill mb-3" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-college="Test Prep Trial Class">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>Book Trial Seat
                </button>
                <div class="text-center">
                    <span class="text-muted small">OR call reception directly</span>
                    <div class="fw-bold text-primary mt-1"><?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS to handle dynamic tab selection based on URL parameter -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Custom active states switching for Bootstrap Nav Tabs
    var tabTriggerEl = document.querySelectorAll('#testPrepTabs button');
    tabTriggerEl.forEach(function(el) {
        el.addEventListener('click', function(e) {
            // Remove active style classes from other buttons
            tabTriggerEl.forEach(function(btn) {
                btn.classList.remove('bg-white', 'text-danger', 'shadow-sm');
                btn.classList.add('text-primary', 'bg-transparent');
            });
            // Add active style classes to clicked button
            this.classList.remove('text-primary', 'bg-transparent');
            this.classList.add('bg-white', 'text-danger', 'shadow-sm');
        });
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
