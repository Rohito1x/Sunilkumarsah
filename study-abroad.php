<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'study-abroad');
require_once __DIR__ . '/includes/header.php';

// Fetch study-abroad page content
$pageData = null;
if (isset($pdo)) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = 'study-abroad' LIMIT 1");
        $stmt->execute();
        $pageData = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fallbacks
$pageTitle = $pageData['title'] ?? 'Study Abroad';
$heroTitle = $pageData['hero_title'] ?? 'Study Abroad';
$heroSubtitle = $pageData['hero_subtitle'] ?? 'Launch your global career by securing admissions in top universities and colleges across the world. Discover high-quality learning, multicultural campuses, and immense post-study work opportunities.';
$benefits = [];
$destinations = [];
$steps = [];

if ($pageData && !empty($pageData['content'])) {
    $content = json_decode($pageData['content'], true);
    $benefits = $content['benefits'] ?? [];
    $destinations = $content['destinations'] ?? [];
    $steps = $content['steps'] ?? [];
} else {
    // Defaults
    $benefits = [
        ['title' => 'Academic Excellence', 'icon' => 'fa-graduation-cap', 'text' => 'Gain access to advanced curriculums, cutting-edge labs, and highly distinguished faculty members driving innovation.'],
        ['title' => 'Global Employability', 'icon' => 'fa-briefcase', 'text' => 'Foreign degrees hold massive weight with multinational corporations, equipping you for top salaries and career paths.'],
        ['title' => 'Cross-Cultural Exposure', 'icon' => 'fa-globe', 'text' => 'Build lifelong connections with peers from all continents, developing a truly global outlook and independent life skills.'],
        ['title' => 'Language & Growth', 'icon' => 'fa-language', 'text' => 'Polish your language fluency while gaining standard adaptability traits highly sought in competitive industries.']
    ];
    $destinations = [
        [
            'country' => 'United States of America',
            'code' => 'USA',
            'flag' => 'fa-flag-usa',
            'img' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=600&auto=format&fit=crop&q=60',
            'desc' => 'Home to Ivy League universities and world-renowned research facilities offering top-tier STEM and business majors.',
            'benefits' => 'OPT work opportunities, top-ranked institutions, diverse cultural landscape.'
        ],
        [
            'country' => 'United Kingdom',
            'code' => 'UK',
            'flag' => 'fa-landmark',
            'img' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=600&auto=format&fit=crop&q=60',
            'desc' => 'Deep academic history with accelerated degree paths (3-year Bachelor, 1-year Master) recognized globally.',
            'benefits' => '2-year post-study work visa (Graduate Route), fast-track graduation, rich culture.'
        ],
        [
            'country' => 'Canada',
            'code' => 'CA',
            'flag' => 'fa-leaf',
            'img' => 'https://images.unsplash.com/photo-1507608869274-d3177c8bb4c7?w=600&auto=format&fit=crop&q=60',
            'desc' => 'Highly welcoming policies, stunning natural environments, and premium quality of life with affordable living.',
            'benefits' => 'Post-Graduation Work Permit (PGWP), pathways to Permanent Residency (PR), safe campus life.'
        ],
        [
            'country' => 'Australia',
            'code' => 'AU',
            'flag' => 'fa-austral-sign',
            'img' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=600&auto=format&fit=crop&q=60',
            'desc' => 'Vibrant cities, world-class teaching standards, and extensive student support with top career prospects.',
            'benefits' => 'Extended post-study work options, sunshine & great beaches, strong economy.'
        ]
    ];
    $steps = [
        ['title' => 'Step 1: Consultation & Target Setting', 'text' => 'Evaluate academic credentials and map them to preferred countries, courses, and financial budgets.'],
        ['title' => 'Step 2: English Proficiency Tests', 'text' => 'Enroll in test prep classes (IELTS/PTE/TOEFL) and secure competitive bands needed for visa requirements.'],
        ['title' => 'Step 3: Document Compilation & Application', 'text' => 'Prepare Statements of Purpose (SOP), letters of recommendation, and submit applications to shortlist universities.'],
        ['title' => 'Step 4: Visa Processing & Flight', 'text' => 'Compile financial documentations, clear visa interviews, attend pre-departure orientations, and fly!']
    ];
}
?>

<!-- Full-width Dark Blue Header Banner -->
<div class="abroad-hero-banner">
    <div class="container">
        <div class="abroad-breadcrumb">
            <a href="<?= h($basePath) ?>index.php"><i class="fa-solid fa-house"></i></a> &gt; Study Abroad
        </div>
        <h1 class="abroad-title">Study Abroad</h1>
    </div>
</div>

<div class="container py-4 animate-fade-in">
    <!-- Country Cards Grid -->
    <div class="country-card-grid">
        <?php foreach ($destinations as $dest): ?>
            <?php 
                // Determine appropriate display code/label
                $countryLabel = $dest['code'] ?? $dest['country'];
                if ($countryLabel === 'USA') {
                    $countryLabel = 'US';
                }
            ?>
            <a href="study-abroad-detail.php?code=<?= h($dest['code'] ?? 'USA') ?>" class="country-card-item text-decoration-none">
                <img src="<?= h($dest['img']) ?>" class="country-card-img" alt="<?= h($dest['country']) ?>">
                <div class="country-card-overlay">
                    <h3 class="country-card-name"><?= h($countryLabel) ?></h3>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- About Section -->
    <div class="about-block-wrap">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="about-block-title">About <span><?= h($siteSettings['site_name'] ?? 'Vision Point') ?></span></h2>
                <p class="about-block-text">
                    At <?= h($siteSettings['site_name'] ?? 'Vision Point Educational Service') ?>, we specialize in guiding students and professionals through the complex world of international education. Our mission is to make studying abroad an achievable and rewarding experience by offering expert advice, tailored services, and comprehensive support throughout your academic journey.
                </p>
            </div>
            <div class="col-md-4 about-block-logo-col text-center">
                <?php
                $logoPath = !empty($siteSettings['site_logo']) ? $siteSettings['site_logo'] : 'assets/images/logo1.png';
                $logoUrl = (strpos($logoPath, 'http') === 0) ? $logoPath : $basePath . $logoPath;
                if (file_exists(__DIR__ . '/' . $logoPath) || (strpos($logoPath, 'http') === 0)): ?>
                    <img src="<?= h($logoUrl) ?>" alt="Vision Point Logo" class="img-fluid rounded shadow-sm" style="max-height: 100px; width: auto; object-fit: contain; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border: 1px solid rgba(0,0,0,0.05);">
                <?php else: ?>
                    <div class="about-block-logo-box">
                        <div class="about-block-logo-text">VP</div>
                        <div class="about-block-logo-sub">Vision Point</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Orange Newsletter Banner -->
    <div class="orange-newsletter-section">
        <div class="orange-newsletter-waves"></div>
        <div class="newsletter-title-wrap">
            <h2 class="newsletter-title">Subscribe to our Newsletter!</h2>
            <div class="newsletter-title-underline"></div>
        </div>
        <div class="newsletter-form-wrap">
            <form id="subscribe_form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <input type="hidden" name="action" value="subscribe">
                <div class="newsletter-input-group">
                    <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address" required>
                    <button type="submit" id="submit_subscribe_form" class="newsletter-submit-btn">Subscribe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Simple script to handle form pre-fill for target country -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle country pre-fill on modal trigger
    const countryCards = document.querySelectorAll('.country-card-item');
    countryCards.forEach(card => {
        card.addEventListener('click', function() {
            const countryName = this.getAttribute('data-country');
            const countrySelect = document.querySelector('select[name="interested_country"]');
            if (countrySelect) {
                let found = false;
                for (let option of countrySelect.options) {
                    if (option.value.toLowerCase() === countryName.toLowerCase() || 
                        option.text.toLowerCase() === countryName.toLowerCase()) {
                        countrySelect.value = option.value;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    // Try exact mappings
                    if (countryName.toLowerCase() === 'united states of america' || countryName.toLowerCase() === 'usa') {
                        countrySelect.value = 'US';
                    } else if (countryName.toLowerCase() === 'united kingdom' || countryName.toLowerCase() === 'uk') {
                        countrySelect.value = 'UK';
                    } else if (countryName.toLowerCase() === 'canada') {
                        countrySelect.value = 'Canada';
                    } else if (countryName.toLowerCase() === 'australia') {
                        countrySelect.value = 'Australia';
                    }
                }
            }
        });
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
