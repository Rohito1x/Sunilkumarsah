<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'universities');
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/institutions.php';

?>

<div class="container py-5">
    <div class="section-intro text-center mb-5">
        <h1 class="fw-bold text-primary mb-3"><i class="fa-solid fa-building-columns text-warning me-2"></i>Top Notch Partner Institutions</h1>
        <p class="text-muted max-w-600 mx-auto">Discover and filter top universities and colleges in India. Select any college to apply directly for admission and scholarships.</p>
    </div>

    <!-- Search & Filter Controls -->
    <div class="card border border-light p-4 rounded-4 shadow-sm bg-light mb-5">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label fw-bold text-primary small">Search University</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" id="uni-search" class="form-control border-start-0" placeholder="Search by name...">
                </div>
            </div>
            <div class="col-12 col-md-8">
                <label class="form-label fw-bold text-primary small">Filter by Region</label>
                <div class="d-flex flex-wrap gap-2" id="region-filters">
                    <?php foreach ($regions as $index => $reg): ?>
                        <button class="btn btn-sm rounded-pill px-3 filter-btn <?= $index === 0 ? 'btn-primary' : 'btn-outline-primary bg-white' ?>" data-region="<?= h($reg) ?>">
                            <?= h($reg) ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Institutions Grid -->
    <div class="row g-4" id="institutions-list">
        <?php foreach ($institutions as $uni): ?>
            <?php 
                $uniImg = !empty($uni['img']) ? ( (strpos($uni['img'], 'http') === 0) ? $uni['img'] : $basePath . $uni['img'] ) : 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60';
                $searchText = strtolower(trim(($uni['name'] ?? '') . ' ' . ($uni['city'] ?? '') . ' ' . ($uni['region'] ?? '') . ' ' . ($uni['location_detail'] ?? '')));
            ?>
            <div class="col-12 col-md-6 col-lg-4 uni-card-item" data-name="<?= strtolower(h($uni['name'])) ?>" data-city="<?= strtolower(h($uni['city'] ?? '')) ?>" data-region="<?= h($uni['region']) ?>" data-search-text="<?= h($searchText) ?>">
                <div class="card h-100 border border-light shadow-sm d-flex flex-column justify-content-between rounded-3 overflow-hidden">
                    <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="text-decoration-none text-dark">
                        <div class="position-relative">
                            <img src="<?= h($uniImg) ?>" class="card-img-top object-fit-cover" style="height: 180px;" alt="<?= h($uni['name']) ?>">
                            <?php if (isset($uni['is_top']) && $uni['is_top'] == 1): ?>
                                <span class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow-sm d-flex align-items-center gap-1 py-2 px-3 fw-bold rounded-pill" style="z-index: 2; font-size: 11px; border: 1px solid rgba(255, 255, 255, 0.4);">
                                    <i class="fa-solid fa-star text-white"></i> Top Notch
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <span class="badge bg-light text-primary rounded-pill"><i class="fa-solid fa-map-marker me-1"></i><?= h($uni['city']) ?></span>
                                <i class="fa-solid fa-graduation-cap text-warning fs-4"></i>
                            </div>
                            <h5 class="fw-bold text-primary mb-0 card-title-text"><?= h($uni['name']) ?></h5>
                        </div>
                    </a>
                    <div class="p-4 pt-0 mt-2 d-flex gap-2">
                        <a href="<?= h($basePath) ?>college-detail.php?id=<?= $uni['id'] ?>" class="btn btn-outline-primary flex-grow-1 rounded-pill py-2 btn-sm fw-semibold text-center text-decoration-none">
                            View Details
                        </a>
                        <button class="btn btn-secondary flex-grow-1 rounded-pill py-2 btn-sm fw-semibold enroll-btn" data-college="<?= h($uni['name']) ?>" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                            Enroll / Apply
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- No Results Placeholder -->
    <div id="no-results" class="text-center py-5" style="display: none;">
        <div class="max-w-400 mx-auto">
            <i class="fa-solid fa-magnifying-glass-minus text-muted display-4 mb-3"></i>
            <h4 class="fw-bold text-primary mb-2">No Matches Found</h4>
            <p class="text-muted small">We couldn't find any institutions matching your search terms. Try checking your spelling or clearing filters to see all universities.</p>
            <button type="button" class="btn btn-primary btn-sm rounded-pill px-4 mt-3" id="btn-reset-filters">View All Universities</button>
        </div>
    </div>
</div>

<!-- Filter and Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('uni-search');
    var filterButtons = document.querySelectorAll('.filter-btn');
    var cardItems = document.querySelectorAll('.uni-card-item');
    var activeRegion = 'All';

    // Check URL query parameters for pre-filtering region and search term
    var urlParams = new URLSearchParams(window.location.search);
    var urlRegion = urlParams.get('region');
    if (urlRegion) {
        var targetBtn = document.querySelector(`.filter-btn[data-region="${urlRegion}"]`);
        if (targetBtn) {
            filterButtons.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
            filterButtons.forEach(b => b.classList.add('bg-white'));
            
            targetBtn.classList.replace('btn-outline-primary', 'btn-primary');
            targetBtn.classList.remove('bg-white');
            activeRegion = urlRegion;
        }
    }

    var urlSearch = urlParams.get('search');
    if (urlSearch) {
        searchInput.value = urlSearch;
    }
    
    // Initial card filtration
    filterCards();

    // Search Input Event
    searchInput.addEventListener('input', filterCards);

    // Region Filter Button Events
    filterButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterButtons.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
            filterButtons.forEach(b => b.classList.add('bg-white'));
            
            btn.classList.replace('btn-outline-primary', 'btn-primary');
            btn.classList.remove('bg-white');

            activeRegion = btn.getAttribute('data-region');
            filterCards();
        });
    });

    // Reset Filters Event
    var resetBtn = document.getElementById('btn-reset-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            searchInput.value = '';
            activeRegion = 'All';
            filterButtons.forEach(b => b.classList.replace('btn-primary', 'btn-outline-primary'));
            filterButtons.forEach(b => b.classList.add('bg-white'));
            var allBtn = document.querySelector('.filter-btn[data-region="All"]');
            if (allBtn) {
                allBtn.classList.replace('btn-outline-primary', 'btn-primary');
                allBtn.classList.remove('bg-white');
            }
            filterCards();
        });
    }

    function filterCards() {
        var query = searchInput.value.toLowerCase().trim();
        
        // Filter out generic search words (stop words) to prevent search failure on typos of common terms like "university"
        var queryWords = query.split(/\s+/).filter(function(w) {
            var stopWords = ['university', 'univ', 'college', 'colg', 'institute', 'inst', 'of', 'and', 'the', 'group', 'univeristy'];
            return w.length > 0 && !stopWords.includes(w);
        });
        
        // Fallback: if query was only stop words (e.g. "university"), match all words
        if (queryWords.length === 0 && query.length > 0) {
            queryWords = query.split(/\s+/).filter(w => w.length > 0);
        }

        var visibleCount = 0;

        cardItems.forEach(function(item) {
            var searchContent = item.getAttribute('data-search-text') || item.getAttribute('data-name') || '';
            var region = item.getAttribute('data-region');

            // Match if all filtered search words are found in the name, city, or region
            var matchesSearch = queryWords.length === 0 || queryWords.every(function(word) {
                return searchContent.includes(word);
            });
            var matchesRegion = (activeRegion === 'All' || region === activeRegion);

            if (matchesSearch && matchesRegion) {
                item.style.display = 'block';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Show/hide no results container
        var noResults = document.getElementById('no-results');
        if (visibleCount === 0) {
            noResults.style.display = 'block';
        } else {
            noResults.style.display = 'none';
        }
    }
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
