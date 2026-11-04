<?php
require_once __DIR__ . '/includes/db.php';

$dest = null;
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
            $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            $dest = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Destination detail fetch DB error: " . $e->getMessage());
        }
    }
}

if (!$dest) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

require_once __DIR__ . '/includes/header.php';


// Fetch all destinations for the navigation pills
$allDestinations = [];
if (isset($pdo)) {
    try {
        $allDestinations = $pdo->query("SELECT * FROM destinations ORDER BY display_order ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fetch institutions located in this destination (by region or name match)
$colleges = [];
if (isset($pdo)) {
    try {
        $stmtColleges = $pdo->prepare("SELECT * FROM institutions WHERE region = ? OR city = ? OR city LIKE ? ORDER BY name ASC");
        $stmtColleges->execute([$dest['region'], $dest['name'], '%' . $dest['name'] . '%']);
        $colleges = $stmtColleges->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Split gallery images
$galleryImages = !empty($dest['gallery']) ? explode(',', $dest['gallery']) : [];
$mainImg = !empty($dest['img']) ? ( (strpos($dest['img'], 'http') === 0) ? $dest['img'] : $basePath . $dest['img'] ) : 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=800&auto=format&fit=crop&q=60';
?>

<!-- Breadcrumbs Section -->
<section class="breadcrumb-section py-3" style="background-color: #f8f9fa;">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 fw-medium small">
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-muted"><i class="fa fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>cities.php" class="text-decoration-none text-muted">Destinations</a></li>
                <li class="breadcrumb-item active text-primary" aria-current="page"><?= h($dest['name']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Destination Hero Section -->
<section class="py-5 border-bottom border-light-subtle">
    <div class="container">
        <article class="section-intro mb-4">
            <h1 class="section-title fw-bold text-primary mb-2">Top Destinations <span class="text-warning">Study in India</span></h1>
            <p class="text-muted mb-0 small">India's Academic Gems: Explore the Top Study Destinations</p>
        </article>

        <!-- Dynamic Destination pills navigation -->
        <?php if (!empty($allDestinations)): ?>
            <div class="mb-4">
                <!-- Search Destinations Input -->
                <div class="mb-3" style="max-width: 400px;">
                    <div class="input-group input-group-sm rounded-pill border border-light shadow-sm overflow-hidden bg-light">
                        <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="dest-search-pills" class="form-control border-0 bg-transparent py-2" placeholder="Filter destinations..." style="box-shadow: none; font-size: 13px;">
                    </div>
                </div>

                <!-- Navigation Pills -->
                <div class="d-flex flex-wrap gap-2" id="destinations-pills-list">
                    <?php 
                    $visibleCount = 8;
                    foreach ($allDestinations as $idx => $d): 
                        $dImg = !empty($d['img']) ? ( (strpos($d['img'], 'http') === 0) ? $d['img'] : $basePath . $d['img'] ) : 'https://images.unsplash.com/photo-1596176530529-78163a4f7af2?w=100&auto=format&fit=crop&q=60';
                        $isActive = ($d['id'] === $dest['id']);
                        $isCollapsed = ($idx >= $visibleCount);
                    ?>
                        <a href="city-detail.php?id=<?= $d['id'] ?>" 
                           class="btn btn-sm rounded-pill px-3 py-2 border d-flex align-items-center gap-2 dest-pill-item <?= $isActive ? 'btn-primary border-primary' : 'btn-outline-primary bg-white text-primary border-light-subtle' ?>" 
                           data-name="<?= strtolower(h($d['name'])) ?>"
                           style="<?= $isCollapsed ? 'display: none !important;' : '' ?>">
                            <img src="<?= h($dImg) ?>" alt="" class="rounded-circle object-fit-cover" style="width: 20px; height: 20px;">
                            <span><?= h($d['name']) ?></span>
                        </a>
                    <?php endforeach; ?>

                    <?php if (count($allDestinations) > $visibleCount): ?>
                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-2" id="toggle-destinations-btn" data-collapsed="true">
                            See all Destinations <i class="fa-solid fa-chevron-down ms-1 small"></i>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- About & Gallery section -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <!-- Left description -->
            <div class="col-12 col-md-6 col-lg-7">
                <article class="paragraph-module text-muted" style="line-height: 1.8;">
                    <h2 class="section-title fw-bold text-primary mb-3">About <span class="text-warning"><?= h($dest['name']) ?></span></h2>
                    <?php if (!empty($dest['about'])): ?>
                        <div style="white-space: pre-line;">
                            <?= $dest['about'] ?>
                        </div>
                    <?php else: ?>
                        <p><?= h($dest['name']) ?> is an excellent academic destination in India offering modern infrastructure, prestigious partner institutions, and comprehensive learning environments for international students.</p>
                        <p>With affordable living costs and strong placement connections, the city welcomes thousands of Nepalese students for technical, management, and medical degrees every year.</p>
                    <?php endif; ?>
                </article>
            </div>
            
            <!-- Right gallery slider -->
            <div class="col-12 col-md-6 col-lg-5">
                <?php if (!empty($galleryImages)): ?>
                    <div id="cityCarousel" class="carousel slide carousel-fade shadow rounded-4 overflow-hidden" data-bs-ride="carousel">
                        <div class="carousel-inner" style="height: 320px;">
                            <?php foreach ($galleryImages as $index => $imgUrl): ?>
                                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100">
                                    <img src="<?= h(trim($imgUrl)) ?>" class="d-block w-100 h-100 object-fit-cover animate-fade-in" alt="City View">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#cityCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#cityCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    </div>
                <?php else: ?>
                    <div class="rounded-4 overflow-hidden shadow" style="height: 320px;">
                        <img src="<?= h($mainImg) ?>" class="w-100 h-100 object-fit-cover" alt="<?= h($dest['name']) ?>">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Location Map Section -->
<?php if (!empty($dest['map_link'])): ?>
    <section class="py-5 bg-white border-top border-light-subtle">
        <div class="container py-3">
            <h3 class="fw-bold text-primary mb-3"><i class="fa-solid fa-map-location-dot text-warning me-2"></i>Location Map</h3>
            <div class="rounded-4 overflow-hidden shadow-sm border border-light-subtle" style="height: 380px; width: 100%;">
                <?php if (strpos($dest['map_link'], '<iframe') !== false): ?>
                    <style>
                        .map-container-iframe iframe {
                            width: 100% !important;
                            height: 100% !important;
                            border: 0 !important;
                        }
                    </style>
                    <div class="map-container-iframe w-100 h-100">
                        <?= $dest['map_link'] ?>
                    </div>
                <?php else: ?>
                    <iframe src="https://maps.google.com/maps?q=<?= urlencode($dest['map_link']) ?>&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                <?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<!-- Available Colleges Section -->
<section class="py-5 bg-light border-top border-light-subtle">
    <div class="container py-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-3">
            <h3 class="fw-bold text-primary mb-0"><i class="fa-solid fa-building-columns text-warning me-2"></i>Available Colleges</h3>
            
            <!-- Search for Colleges -->
            <div style="max-width: 350px; width: 100%;">
                <div class="input-group rounded-pill border border-light shadow-sm overflow-hidden bg-white">
                    <span class="input-group-text bg-transparent border-0 ps-3"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="colleges-search-input" class="form-control border-0 bg-transparent py-2" placeholder="Search colleges here..." style="box-shadow: none; font-size: 14px;">
                </div>
            </div>
        </div>

        <div class="row g-4" id="colleges-grid-list">
            <?php if (!empty($colleges)): ?>
                <?php foreach ($colleges as $uni): ?>
                    <?php 
                    $uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                    ?>
                    <div class="col-12 col-md-6 col-lg-4 college-card-item" data-name="<?= strtolower(h($uni['name'])) ?>">
                        <div class="card h-100 border border-light shadow-sm d-flex flex-column justify-content-between rounded-4 overflow-hidden">
                            <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none text-dark">
                                <div class="position-relative overflow-hidden" style="height: 180px;">
                                    <img src="<?= h($uniImg) ?>" class="w-100 h-100 object-fit-cover city-card-img animate-hover-zoom" alt="<?= h($uni['name']) ?>">
                                </div>
                                <div class="p-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <span class="badge bg-light text-primary border border-light-subtle rounded-pill"><i class="fa-solid fa-map-marker text-warning me-1"></i><?= h($uni['city']) ?></span>
                                        <i class="fa-solid fa-graduation-cap text-warning fs-5"></i>
                                    </div>
                                    <h5 class="fw-bold text-primary mb-0" style="font-size: 17px; line-height: 1.4;"><?= h($uni['name']) ?></h5>
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
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5" id="no-colleges-empty-placeholder">
                    <div class="max-w-400 mx-auto">
                        <i class="fa-solid fa-graduation-cap text-muted display-4 mb-3"></i>
                        <h5 class="fw-bold text-primary mb-1">No colleges added yet</h5>
                        <p class="text-muted small">We are updating admissions options for <?= h($dest['name']) ?> shortly. Please contact our counseling support.</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- No search results placeholder -->
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
    // 1. Destinations Pills Filtering
    var destSearch = document.getElementById('dest-search-pills');
    var destPills = document.querySelectorAll('.dest-pill-item');
    var toggleDestBtn = document.getElementById('toggle-destinations-btn');
    var visibleLimit = 8;

    if (destSearch) {
        destSearch.addEventListener('input', function() {
            var q = destSearch.value.toLowerCase().trim();
            destPills.forEach(function(pill) {
                var name = pill.getAttribute('data-name');
                if (q === '' || name.includes(q)) {
                    pill.style.setProperty('display', 'flex', 'important');
                } else {
                    pill.style.setProperty('display', 'none', 'important');
                }
            });
            if (toggleDestBtn) {
                toggleDestBtn.style.display = q === '' ? 'block' : 'none';
            }
        });
    }

    if (toggleDestBtn) {
        toggleDestBtn.addEventListener('click', function() {
            var isCollapsed = toggleDestBtn.getAttribute('data-collapsed') === 'true';
            if (isCollapsed) {
                // Show all
                destPills.forEach(pill => pill.style.setProperty('display', 'flex', 'important'));
                toggleDestBtn.setAttribute('data-collapsed', 'false');
                toggleDestBtn.innerHTML = 'Show less <i class="fa-solid fa-chevron-up ms-1 small"></i>';
            } else {
                // Collapse back to visible limit
                destPills.forEach(function(pill, idx) {
                    if (idx >= visibleLimit) {
                        pill.style.setProperty('display', 'none', 'important');
                    }
                });
                toggleDestBtn.setAttribute('data-collapsed', 'true');
                toggleDestBtn.innerHTML = 'See all Destinations <i class="fa-solid fa-chevron-down ms-1 small"></i>';
            }
        });
    }

    // 2. Colleges search filtering
    var collegesSearch = document.getElementById('colleges-search-input');
    var collegeCards = document.querySelectorAll('.college-card-item');
    var emptyPlaceholder = document.getElementById('no-colleges-empty-placeholder');
    var matchesPlaceholder = document.getElementById('no-colleges-matches');

    if (collegesSearch) {
        collegesSearch.addEventListener('input', function() {
            var q = collegesSearch.value.toLowerCase().trim();
            var matchedCount = 0;

            collegeCards.forEach(function(card) {
                var name = card.getAttribute('data-name');
                if (name.includes(q)) {
                    card.style.display = 'block';
                    matchedCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (collegeCards.length > 0) {
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
