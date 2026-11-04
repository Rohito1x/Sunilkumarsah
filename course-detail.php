<?php
require_once __DIR__ . '/includes/db.php';

$stream = null;
$rawId = $_GET['id'] ?? null;

if ($rawId !== null) {
    if (!ctype_digit((string)$rawId) || (int)$rawId <= 0) {
        http_response_code(404);
        require_once __DIR__ . '/404.php';
        exit;
    }
    $id = (int)$rawId;
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM course_streams WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $stream = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Course detail fetch DB error: " . $e->getMessage());
        }
    }
}

if (!$stream) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

require_once __DIR__ . '/includes/header.php';


// Fetch all course streams for navigation pills
$allStreams = [];
if (isset($pdo)) {
    try {
        $allStreams = $pdo->query("SELECT * FROM course_streams ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fetch institutions offering this stream
$offeringColleges = [];
if (isset($pdo)) {
    try {
        $allUnis = $pdo->query("SELECT * FROM institutions ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($allUnis as $uni) {
            $uniCourses = !empty($uni['courses']) ? explode(',', $uni['courses']) : [];
            if (in_array($stream['id'], $uniCourses)) {
                $offeringColleges[] = $uni;
            }
        }
    } catch (PDOException $e) {
        // Fallback
    }
}

$streamImg = !empty($stream['img']) ? ( (strpos($stream['img'], 'http') === 0) ? $stream['img'] : $basePath . $stream['img'] ) : 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800&auto=format&fit=crop&q=60';
?>

<!-- Breadcrumbs Section -->
<section class="breadcrumb-section py-3" style="background-color: #f8f9fa;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 fw-medium small">
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-muted"><i class="fa fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>courses.php" class="text-decoration-none text-muted">Courses</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page"><?= h($stream['name']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Course Header Section -->
<section class="py-5 border-bottom border-light-subtle">
    <div class="container">
        <article class="section-intro mb-4">
            <h1 class="section-title fw-bold text-primary mb-2">Explore <span class="text-warning">Available Courses</span></h1>
            <p class="text-muted mb-0 small">Search here to find your perfect course in seconds</p>
        </article>

        <!-- Dynamic Course Navigation Pills -->
        <?php if (!empty($allStreams)): ?>
            <div class="mb-4">
                <!-- Search Course Input -->
                <div class="mb-3" style="max-width: 400px;">
                    <div class="input-group input-group-sm rounded-pill border border-light shadow-sm overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="stream-search-pills" class="form-control border-0 bg-transparent py-2" placeholder="Filter courses..." style="box-shadow: none; font-size: 13px;">
                    </div>
                </div>

                <!-- Navigation Pills -->
                <div class="d-flex flex-wrap gap-2" id="streams-pills-list">
                    <?php 
                    $visibleCount = 8;
                    foreach ($allStreams as $idx => $s): 
                        $isActive = ($s['id'] === $stream['id']);
                        $isCollapsed = ($idx >= $visibleCount);
                    ?>
                        <a href="course-detail.php?id=<?= $s['id'] ?>" 
                           class="btn btn-sm rounded-pill px-3 py-2 border d-flex align-items-center gap-2 stream-pill-item <?= $isActive ? 'btn-primary border-primary' : 'btn-outline-primary bg-white text-primary border-light-subtle' ?>" 
                           data-name="<?= strtolower(h($s['name'])) ?>"
                           style="<?= $isCollapsed ? 'display: none !important;' : '' ?>">
                            <i class="fa-solid <?= h($s['icon'] ?? 'fa-graduation-cap') ?> small"></i>
                            <span><?= h($s['name']) ?></span>
                        </a>
                    <?php endforeach; ?>

                    <?php if (count($allStreams) > $visibleCount): ?>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2" id="toggle-streams-btn" data-collapsed="true">
                            See all Courses <i class="fa-solid fa-chevron-down ms-1 small"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- About Course Section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <!-- Left description -->
            <div class="col-12 col-md-6 col-lg-7">
                <article class="paragraph-module text-muted" style="line-height: 1.8;">
                    <h2 class="section-title fw-bold text-primary mb-3">About <span class="text-warning"><?= h($stream['name']) ?></span></h2>
                    <?php if (!empty($stream['desc'])): ?>
                        <div style="white-space: pre-line;">
                            <?= $stream['desc'] ?>
                        </div>
                    <?php else: ?>
                        <p>Pursuing a degree in <?= h($stream['name']) ?> offers rich placement opportunities, dynamic practical laboratories, and direct connections to corporate entities and research sectors in India.</p>
                        <p>Our partner institutions provide modern classrooms, accredited training paths, and scholarship packages specifically designed to support international and Nepalese applicants.</p>
                    <?php endif; ?>
                </article>
            </div>
            
            <!-- Right graphic cover -->
            <div class="col-12 col-md-6 col-lg-5">
                <div class="rounded-4 overflow-hidden shadow" style="height: 320px;">
                    <img src="<?= h($streamImg) ?>" class="w-100 h-100 object-fit-cover" alt="<?= h($stream['name']) ?>">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Offering Colleges Section -->
<section class="py-5 bg-light border-top border-light-subtle">
    <div class="container py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
            <h3 class="fw-bold text-primary mb-0"><i class="fa-solid fa-graduation-cap text-warning me-2"></i>Colleges Offering <?= h($stream['name']) ?></h3>
            
            <!-- Search for Colleges -->
            <div style="max-width: 350px; width: 100%;">
                <div class="input-group rounded-pill border border-light shadow-sm overflow-hidden bg-white">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="colleges-search-input" class="form-control border-0 bg-transparent py-2" placeholder="Search colleges..." style="box-shadow: none; font-size: 14px;">
                </div>
            </div>
        </div>

        <div class="row g-4" id="colleges-table-list">
            <?php if (!empty($offeringColleges)): ?>
                <?php foreach ($offeringColleges as $uni): 
                    $uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                ?>
                    <div class="col-12 college-row-item" data-name="<?= strtolower(h($uni['name'])) ?>">
                        <div class="card border border-light rounded-4 p-3 shadow-sm bg-white hover-scale">
                            <div class="row align-items-center g-3">
                                <!-- Left thumbnail -->
                                <div class="col-12 col-md-2 text-center">
                                    <div class="bg-light rounded-3 p-2 d-inline-block shadow-sm" style="width: 100px; height: 100px; overflow: hidden;">
                                        <img src="<?= h($uniImg) ?>" class="w-100 h-100 object-fit-contain" alt="<?= h($uni['name']) ?>">
                                    </div>
                                </div>
                                
                                <!-- Middle details -->
                                <div class="col-12 col-md-7 border-end-md border-light-subtle pe-md-4">
                                    <h5 class="fw-bold text-primary mb-2">
                                        <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none text-primary hover-underline"><?= h($uni['name']) ?></a>
                                    </h5>
                                    
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        <span class="badge bg-light text-primary rounded-pill border small"><i class="fa-solid fa-map-marker text-warning me-1"></i><?= h($uni['city']) ?> (<?= h($uni['region']) ?>)</span>
                                        <span class="badge bg-light text-secondary rounded-pill border small"><i class="fa-solid fa-building me-1"></i>Partner Institution</span>
                                    </div>
                                    
                                    <?php if (!empty($uni['location_detail'])): ?>
                                        <p class="text-muted small mb-0"><i class="fa-solid fa-location-dot text-danger me-1"></i> <?= h($uni['location_detail']) ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Right actions -->
                                <div class="col-12 col-md-3 text-center">
                                    <button class="btn btn-secondary w-100 rounded-pill py-2 btn-sm fw-semibold mb-2 enroll-btn" data-college="<?= h($uni['name']) ?>" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                                        Apply Now
                                    </button>
                                    <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="btn btn-outline-primary w-100 rounded-pill py-2 btn-sm fw-semibold">
                                        Program Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5" id="no-colleges-empty-placeholder">
                    <div class="max-w-400 mx-auto">
                        <i class="fa-solid fa-building-columns text-muted display-4 mb-3"></i>
                        <h5 class="fw-bold text-primary mb-1">No colleges offer this course stream yet</h5>
                        <p class="text-muted small">Admissions will be updated shortly. Please consult our support team.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Search empty state -->
            <div class="col-12 text-center py-5" id="no-colleges-matches" style="display: none;">
                <div class="max-w-400 mx-auto">
                    <i class="fa-solid fa-magnifying-glass-minus text-muted display-4 mb-3"></i>
                    <h5 class="fw-bold text-primary mb-1">No colleges matched your search</h5>
                    <p class="text-muted small">Try checking your spelling or search for a different college name.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter & Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Streams Pills Filtering
    var streamSearch = document.getElementById('stream-search-pills');
    var streamPills = document.querySelectorAll('.stream-pill-item');
    var toggleStreamBtn = document.getElementById('toggle-streams-btn');
    var visibleLimit = 8;

    if (streamSearch) {
        streamSearch.addEventListener('input', function() {
            var q = streamSearch.value.toLowerCase().trim();
            streamPills.forEach(function(pill) {
                var name = pill.getAttribute('data-name');
                if (q === '' || name.includes(q)) {
                    pill.style.setProperty('display', 'flex', 'important');
                } else {
                    pill.style.setProperty('display', 'none', 'important');
                }
            });
            if (toggleStreamBtn) {
                toggleStreamBtn.style.display = q === '' ? 'block' : 'none';
            }
        });
    }

    if (toggleStreamBtn) {
        toggleStreamBtn.addEventListener('click', function() {
            var isCollapsed = toggleStreamBtn.getAttribute('data-collapsed') === 'true';
            if (isCollapsed) {
                // Show all
                streamPills.forEach(pill => pill.style.setProperty('display', 'flex', 'important'));
                toggleStreamBtn.setAttribute('data-collapsed', 'false');
                toggleStreamBtn.innerHTML = 'Show less <i class="fa-solid fa-chevron-up ms-1 small"></i>';
            } else {
                // Collapse back to visible limit
                streamPills.forEach(function(pill, idx) {
                    if (idx >= visibleLimit) {
                        pill.style.setProperty('display', 'none', 'important');
                    }
                });
                toggleStreamBtn.setAttribute('data-collapsed', 'true');
                toggleStreamBtn.innerHTML = 'See all Courses <i class="fa-solid fa-chevron-down ms-1 small"></i>';
            }
        });
    }

    // 2. Colleges search filtering
    var collegesSearch = document.getElementById('colleges-search-input');
    var collegeRows = document.querySelectorAll('.college-row-item');
    var matchesPlaceholder = document.getElementById('no-colleges-matches');

    if (collegesSearch) {
        collegesSearch.addEventListener('input', function() {
            var q = collegesSearch.value.toLowerCase().trim();
            var matchedCount = 0;

            collegeRows.forEach(function(row) {
                var name = row.getAttribute('data-name');
                if (name.includes(q)) {
                    row.style.display = 'block';
                    matchedCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (collegeRows.length > 0) {
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
