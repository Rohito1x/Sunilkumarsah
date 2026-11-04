<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'guides');
require_once __DIR__ . '/includes/header.php';

$guides = [];
if (isset($pdo) && $pdo) {
    try {
        $stmtG = $pdo->query("SELECT id, title, slug, summary as `desc`, image_path as img, publish_date as date FROM guides WHERE status = 'published' ORDER BY publish_date DESC, id DESC");
        $guides = $stmtG->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $guides = [];
    }
}

if (empty($guides)) {
    $guides = [
        [
            'id' => 1,
            'title' => 'Complete Guide for Nepalese Students Studying in India (2026 Edition)',
            'slug' => 'complete-guide-studying-in-india-2026',
            'desc' => 'Explore everything you need to know about pursuing higher education in India: top universities, eligibility, scholarships, fees, and documentation.',
            'img' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=600&auto=format&fit=crop&q=60',
            'date' => '2026-05-15'
        ],
        [
            'id' => 2,
            'title' => 'Top 10 Scholarships in India for Nepalese Citizens',
            'slug' => 'top-10-scholarships-nepali-students-india',
            'desc' => 'A curated breakdown of merit-based, institutional, and Embassy scholarship opportunities available for Nepali scholars in Indian universities.',
            'img' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60',
            'date' => '2026-04-20'
        ],
        [
            'id' => 3,
            'title' => 'B.Sc Nursing in India: Colleges, INC Recognition & Admissions',
            'slug' => 'bsc-nursing-in-india-guide',
            'desc' => 'Detailed walkthrough on INC recognized nursing colleges in Bangalore, Tamil Nadu, Punjab, and Delhi NCR with hostel facilities and clinical training.',
            'img' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=600&auto=format&fit=crop&q=60',
            'date' => '2026-03-10'
        ]
    ];
}
?>

<!-- Breadcrumbs and Hero Section -->
<section class="py-5 text-white" style="background: linear-gradient(135deg, #0458A0 0%, #033663 100%);">
    <div class="container py-3">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'Blog & Educational Guides' => 'guides.php']) ?>
        <h1 class="fw-bold text-white mb-2 display-5 mt-3">
            Education Guides & Articles
        </h1>
        <p class="text-white-50 mt-2 mb-0 small max-w-600" style="line-height: 1.7;">Verified articles, admission roadmaps, scholarship announcements, and regulatory guidelines for Nepalese students.</p>
    </div>
</section>

<!-- Guides List Grid -->
<section class="py-5 bg-light-subtle">
    <div class="container py-3">
        <!-- Live search bar for blogs -->
        <div class="row mb-5 justify-content-between align-items-center g-3">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="input-group rounded-pill border border-light shadow-sm overflow-hidden bg-white">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="guides-search-input" class="form-control border-0 bg-transparent py-2" placeholder="Search guides & articles..." style="box-shadow: none; font-size: 14px;">
                </div>
            </div>
            
            <div class="col-12 col-md-auto">
                <span class="text-muted small fw-medium"><i class="fa-solid fa-file-invoice text-warning me-1"></i> Showing <?= count($guides) ?> published guides</span>
            </div>
        </div>

        <div class="row g-4" id="guides-grid-container">
            <?php if (!empty($guides)): ?>
                <?php foreach ($guides as $guide): 
                    $guideImg = !empty($guide['img']) ? ( (strpos($guide['img'], 'http') === 0) ? $guide['img'] : $basePath . $guide['img'] ) : 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=600&auto=format&fit=crop&q=60';
                    $formattedDate = !empty($guide['date']) ? date('M. d, Y', strtotime($guide['date'])) : 'Recent Post';
                    $excerpt = !empty($guide['desc']) ? (strlen($guide['desc']) > 115 ? substr($guide['desc'], 0, 112) . '...' : $guide['desc']) : 'Read expert articles, eligibility updates, and step-by-step guides from our counselors...';
                ?>
                    <div class="col-12 col-md-6 col-lg-4 guide-grid-item" data-title="<?= strtolower(h($guide['title'])) ?>">
                        <div class="card border border-light rounded-4 shadow-sm bg-white overflow-hidden hover-scale h-100 d-flex flex-column justify-content-between p-0">
                            <!-- Image Header -->
                            <a href="guide-detail.php?id=<?= $guide['id'] ?>" class="d-block position-relative" style="height: 220px; overflow: hidden;">
                                <img src="<?= h($guideImg) ?>" alt="<?= h($guide['title']) ?>" class="w-100 h-100 object-fit-cover">
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-primary text-white rounded-pill px-3 py-2 small shadow-sm"><i class="fa-solid fa-book-open me-1"></i>Resource Guide</span>
                                </div>
                            </a>
                            
                            <!-- Body Contents -->
                            <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                                <div>
                                    <!-- Date meta -->
                                    <div class="d-flex align-items-center text-muted small mb-2 gap-2" style="font-size: 13px;">
                                        <i class="fa-solid fa-calendar-days text-primary"></i>
                                        <span><?= h($formattedDate) ?></span>
                                    </div>
                                    
                                    <!-- Title -->
                                    <h5 class="fw-bold text-primary mb-2" style="line-height: 1.4;">
                                        <a href="guide-detail.php?id=<?= $guide['id'] ?>" class="text-decoration-none text-primary hover-underline"><?= h($guide['title']) ?></a>
                                    </h5>
                                    
                                    <!-- Excerpt -->
                                    <p class="text-muted small mb-0"><?= h($excerpt) ?></p>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-4 pt-3 border-top border-light-subtle d-flex align-items-center justify-content-between">
                                    <a href="guide-detail.php?id=<?= $guide['id'] ?>" class="text-warning text-decoration-none fw-bold small hover-underline d-inline-flex align-items-center">
                                        Read Full Guide <i class="fa-solid fa-arrow-right ms-1" style="font-size: 11px;"></i>
                                    </a>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 font-semibold" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-college="Guide Interest: <?= h($guide['title']) ?>">
                                        Consult Counsel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-newspaper text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold text-primary mb-1">No resource guides available yet</h5>
                    <p class="text-muted small">Step-by-step documentation and blogs will be uploaded shortly.</p>
                </div>
            <?php endif; ?>

            <!-- Search empty state -->
            <div class="col-12 text-center py-5" id="no-guides-matches" style="display: none;">
                <div class="max-w-400 mx-auto">
                    <i class="fa-solid fa-magnifying-glass-minus text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold text-primary mb-1">No guides matched your search</h5>
                    <p class="text-muted small">Try checking your spelling or search for a different topic name.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('guides-search-input');
    var guideCards = document.querySelectorAll('.guide-grid-item');
    var matchesPlaceholder = document.getElementById('no-guides-matches');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = searchInput.value.toLowerCase().trim();
            var matchedCount = 0;

            guideCards.forEach(function(card) {
                var title = card.getAttribute('data-title');
                if (title.includes(q)) {
                    card.style.setProperty('display', 'block', 'important');
                    matchedCount++;
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });

            if (guideCards.length > 0) {
                if (matchedCount === 0) {
                    matchesPlaceholder.style.display = 'block';
                } else {
                    matchesPlaceholder.style.display = 'none';
                }
            }
        });
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
