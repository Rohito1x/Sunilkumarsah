<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'courses');
require_once __DIR__ . '/includes/header.php';

$courses = [];
if (isset($pdo) && $pdo) {
    try {
        $stmtC = $pdo->query('SELECT * FROM course_streams ORDER BY display_order ASC, name ASC');
        $courses = $stmtC->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $courses = [];
    }
}

if (empty($courses)) {
    $courses = [
        ['id' => 1, 'name' => 'Engineering', 'icon' => 'fa-gear', 'desc' => 'B.Tech, M.Tech across CSE, AI, Mechanical, Civil and Electronics.', 'img' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?w=400&auto=format&fit=crop&q=60'],
        ['id' => 2, 'name' => 'Nursing', 'icon' => 'fa-user-nurse', 'desc' => 'B.Sc Nursing, Post Basic & M.Sc Nursing with hospital affiliations.', 'img' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&auto=format&fit=crop&q=60'],
        ['id' => 6, 'name' => 'Pharmacy', 'icon' => 'fa-pills', 'desc' => 'B.Pharm, Pharm.D and M.Pharm with PCI approved laboratories.', 'img' => 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=400&auto=format&fit=crop&q=60'],
        ['id' => 9, 'name' => 'Management', 'icon' => 'fa-briefcase', 'desc' => 'BBA, MBA with industry specializations, internships and placements.', 'img' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400&auto=format&fit=crop&q=60'],
        ['id' => 14, 'name' => 'Computer Application / IT', 'icon' => 'fa-laptop-code', 'desc' => 'BCA, MCA, Data Science, Cloud Computing and Cybersecurity.', 'img' => 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&auto=format&fit=crop&q=60'],
        ['id' => 3, 'name' => 'Dental', 'icon' => 'fa-tooth', 'desc' => 'BDS & MDS programs in recognized dental colleges.', 'img' => 'https://images.unsplash.com/photo-1588776814546-1ffcf47267a5?w=400&auto=format&fit=crop&q=60'],
        ['id' => 18, 'name' => 'Allied Health Science', 'icon' => 'fa-heart-pulse', 'desc' => 'BMLT, BPT, Medical Imaging, Anesthesia and OT Technology.', 'img' => 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=400&auto=format&fit=crop&q=60'],
        ['id' => 4, 'name' => 'Agriculture', 'icon' => 'fa-seedling', 'desc' => 'B.Sc Agriculture & Forestry with practical farming training.', 'img' => 'https://images.unsplash.com/photo-1500937386664-56d1dfef3854?w=400&auto=format&fit=crop&q=60'],
        ['id' => 10, 'name' => 'Medical', 'icon' => 'fa-stethoscope', 'desc' => 'MBBS & MD programs in premier medical universities.', 'img' => 'https://images.unsplash.com/photo-1505751172876-fa1923c5c528?w=400&auto=format&fit=crop&q=60'],
        ['id' => 16, 'name' => 'Design', 'icon' => 'fa-palette', 'desc' => 'Fashion Design, Interior Design, UI/UX and Graphic Design.', 'img' => 'https://images.unsplash.com/photo-1513542789411-b6a5d4f31634?w=400&auto=format&fit=crop&q=60'],
        ['id' => 11, 'name' => 'Hotel Management', 'icon' => 'fa-hotel', 'desc' => 'BHM with international paid internships in luxury hotel chains.', 'img' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&auto=format&fit=crop&q=60'],
        ['id' => 12, 'name' => 'Law', 'icon' => 'fa-scale-balanced', 'desc' => 'BA LLB, BBA LLB & LLM programs recognized by Bar Council.', 'img' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=400&auto=format&fit=crop&q=60']
    ];
}
?>

<!-- Breadcrumb section -->
<section class="breadcrumb-section py-3" style="background-color: #f8f9fa;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 fw-medium small">
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-muted"><i class="fa fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page">Courses</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Page Header and Filter section -->
<section class="py-5 border-bottom border-light-subtle bg-white">
    <div class="container">
        <div class="row align-items-center justify-content-between g-4">
            <div class="col-12 col-md-7 col-lg-8">
                <article class="section-intro mb-0">
                    <h1 class="section-title fw-bold text-primary mb-2">Explore <span class="text-warning">Available Courses</span></h1>
                    <p class="text-muted mb-0 small">The benefits and advantages of studying in India with specialized learning streams</p>
                </article>
            </div>
            
            <!-- Search courses bar -->
            <div class="col-12 col-md-5 col-lg-4">
                <div class="input-group rounded-pill border border-light shadow-sm overflow-hidden bg-light">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                    <input type="text" id="courses-search-input" class="form-control border-0 bg-transparent py-2" placeholder="Search for available courses" style="box-shadow: none; font-size: 14px;">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Courses Grid section -->
<section class="py-5 bg-light-subtle">
    <div class="container py-3">
        <div class="row g-4 justify-content-center" id="courses-grid-container">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): 
                    $courseImg = !empty($course['img']) ? ( (strpos($course['img'], 'http') === 0) ? $course['img'] : $basePath . $course['img'] ) : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400&auto=format&fit=crop&q=60';
                ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 course-grid-item" data-name="<?= strtolower(h($course['name'])) ?>">
                        <div class="card border border-light rounded-4 text-center p-3 shadow-sm hover-scale bg-white h-100 d-flex flex-column justify-content-between">
                            <div>
                                <div class="mb-3 position-relative d-inline-block mx-auto rounded-circle overflow-hidden shadow-sm border border-light-subtle" style="width: 85px; height: 85px;">
                                    <img src="<?= h($courseImg) ?>" alt="<?= h($course['name']) ?>" class="w-100 h-100 object-fit-cover">
                                </div>
                                <h6 class="fw-bold mb-0" style="font-size: 14px;">
                                    <a href="course-detail.php?id=<?= $course['id'] ?>" class="text-decoration-none text-primary hover-underline"><?= h($course['name']) ?></a>
                                </h6>
                            </div>
                            <div class="mt-3 pt-2 border-top border-light-subtle">
                                <a href="course-detail.php?id=<?= $course['id'] ?>" class="text-warning text-decoration-none fw-semibold small hover-underline d-inline-flex align-items-center">
                                    Colleges <i class="fa-solid fa-arrow-right ms-1" style="font-size: 10px;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fa-solid fa-graduation-cap text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold text-primary mb-1">No course streams available yet</h5>
                    <p class="text-muted small">Academic faculties are being populated. Please check back later.</p>
                </div>
            <?php endif; ?>

            <!-- Search empty state -->
            <div class="col-12 text-center py-5" id="no-courses-matches" style="display: none;">
                <div class="max-w-400 mx-auto">
                    <i class="fa-solid fa-magnifying-glass-minus text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold text-primary mb-1">No course streams matched your search</h5>
                    <p class="text-muted small">Try checking your spelling or search for a different faculty name.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('courses-search-input');
    var courseCards = document.querySelectorAll('.course-grid-item');
    var matchesPlaceholder = document.getElementById('no-courses-matches');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            var q = searchInput.value.toLowerCase().trim();
            var matchedCount = 0;

            courseCards.forEach(function(card) {
                var name = card.getAttribute('data-name');
                if (name.includes(q)) {
                    card.style.setProperty('display', 'block', 'important');
                    matchedCount++;
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });

            if (courseCards.length > 0) {
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
