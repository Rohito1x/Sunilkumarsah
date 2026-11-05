<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$guide = null;
$rawId = $_GET['id'] ?? null;
$rawSlug = $_GET['slug'] ?? null;

if ($rawId !== null) {
    if (!ctype_digit((string)$rawId) || (int)$rawId <= 0) {
        http_response_code(404);
        require_once __DIR__ . '/404.php';
        exit;
    }
    $id = (int)$rawId;
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM guides WHERE id = :id AND status = 'published' LIMIT 1");
            $stmt->execute([':id' => $id]);
            $guide = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Guide detail fetch DB error: " . $e->getMessage());
        }
    }
} elseif (!empty($rawSlug)) {
    $slug = trim((string)$rawSlug);
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM guides WHERE slug = :slug AND status = 'published' LIMIT 1");
            $stmt->execute([':slug' => $slug]);
            $guide = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Guide detail fetch DB error: " . $e->getMessage());
        }
    }
}

if (!$guide) {
    http_response_code(404);
    require_once __DIR__ . '/404.php';
    exit;
}

// Pass dynamic SEO metadata and Article Schema
$pageSeo = getSeoData('guide', $guide);
$pageSeoExtra = ['guide' => $guide];

require_once __DIR__ . '/includes/header.php';

// Fetch other recent guides for sidebar list
$recentGuides = [];
if (isset($pdo)) {
    try {
        $stmtRecent = $pdo->prepare("SELECT id, title, slug, publish_date FROM guides WHERE status = 'published' AND id != ? ORDER BY publish_date DESC, id DESC LIMIT 5");
        $stmtRecent->execute([$guide['id']]);
        $recentGuides = $stmtRecent->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Fallback
    }
}

$guideImg = !empty($guide['image_path']) ? ( (strpos($guide['image_path'], 'http') === 0) ? $guide['image_path'] : $basePath . $guide['image_path'] ) : 'https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=800&auto=format&fit=crop&q=60';
$formattedDate = !empty($guide['publish_date']) ? date('F d, Y', strtotime($guide['publish_date'])) : 'Recent Post';
$articleUrl = generateCanonicalUrl('blog/' . ($guide['slug'] ?? 'post-' . $guide['id']));
$encodedUrl = urlencode($articleUrl);
$encodedTitle = urlencode($guide['title']);
?>

<!-- Breadcrumbs Section -->
<section class="breadcrumb-section py-3 bg-light border-bottom">
    <div class="container">
        <?= generateBreadcrumbs([
            'Home' => 'index.php',
            'Blog' => 'guides.php',
            $guide['title'] => 'blog/' . ($guide['slug'] ?? $guide['id'])
        ]) ?>
    </div>
</section>

<!-- Main content container -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Left main column -->
            <div class="col-lg-8">
                <!-- Title -->
                <h1 class="fw-bold text-primary mb-3 display-6" style="line-height: 1.3;"><?= h($guide['title']) ?></h1>
                
                <!-- Meta data -->
                <div class="d-flex flex-wrap align-items-center text-muted small mb-4 gap-3 border-bottom pb-3">
                    <div class="d-flex align-items-center gap-1">
                        <i class="fa-solid fa-calendar-days text-primary"></i>
                        <span>Published on: <strong><?= h($formattedDate) ?></strong></span>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <i class="fa-solid fa-user-tie text-primary"></i>
                        <span>Author: <strong><?= h($guide['meta_author'] ?? 'Vision Point Educational Service') ?></strong></span>
                    </div>
                </div>

                <!-- Main banner image -->
                <div class="rounded-4 overflow-hidden shadow-sm mb-4 border border-light-subtle" style="max-height: 420px;">
                    <img src="<?= h($guideImg) ?>" alt="<?= h($guide['title']) ?>" class="w-100 object-fit-cover" style="max-height: 420px;">
                </div>

                <!-- Article content -->
                <article class="text-muted fs-6 mb-5" style="line-height: 1.8;">
                    <?php if (!empty($guide['summary'])): ?>
                        <div class="lead fw-medium text-dark mb-4 p-3 bg-light rounded-3" style="border-left: 4px solid #0458A0; line-height: 1.7;">
                            <?= h($guide['summary']) ?>
                        </div>
                    <?php endif; ?>

                    <div class="blog-body-text">
                        <?php 
                        // If it contains tags, output safely; otherwise, render with nl2br
                        if (strpos($guide['content'] ?? '', '<p>') !== false || strpos($guide['content'] ?? '', '<div>') !== false) {
                            echo $guide['content'];
                        } else {
                            echo nl2br(h($guide['content'] ?? ''));
                        }
                        ?>
                    </div>
                </article>

                <!-- Social Share Buttons -->
                <div class="p-4 bg-light rounded-4 border d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <span class="fw-bold text-dark"><i class="fa-solid fa-share-nodes text-primary me-2"></i>Share this article:</span>
                    <div class="d-flex gap-2">
                        <a href="https://api.whatsapp.com/send?text=<?= $encodedTitle ?>%20<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-success rounded-pill px-3 fw-semibold">
                            <i class="fa-brands fa-whatsapp me-1"></i>WhatsApp
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-primary rounded-pill px-3 fw-semibold">
                            <i class="fa-brands fa-facebook-f me-1"></i>Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=<?= $encodedTitle ?>&url=<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-dark rounded-pill px-3 fw-semibold">
                            <i class="fa-brands fa-x-twitter me-1"></i>X (Twitter)
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encodedUrl ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-info text-white rounded-pill px-3 fw-semibold">
                            <i class="fa-brands fa-linkedin-in me-1"></i>LinkedIn
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right sidebar column -->
            <div class="col-lg-4">
                <!-- Apply / Counsel CTA card -->
                <div class="card border border-light rounded-4 shadow-sm p-4 text-center bg-light mb-4 sticky-top" style="top: 100px; z-index: 10;">
                    <h4 class="fw-bold text-primary mb-2">Need Expert Guidance?</h4>
                    <p class="text-muted small mb-4">Have questions about admissions, fees, or scholarship structures?</p>
                    
                    <button class="btn btn-warning text-dark w-100 py-2.5 fw-bold rounded-pill mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-college="Guide Page: <?= h($guide['title']) ?>">
                        <i class="fa-solid fa-paper-plane me-2"></i>Book Free Consultation
                    </button>
                    
                    <hr class="my-3">
                    
                    <div class="text-center">
                        <span class="text-muted small">Direct Helpline</span>
                        <div class="fw-bold text-primary mt-1">
                            <a href="tel:+9779807765812" class="text-decoration-none text-primary">+977 9807765812</a>
                        </div>
                    </div>
                </div>

                <!-- Recent Guides sidebar widget -->
                <?php if (!empty($recentGuides)): ?>
                    <div class="card border border-light rounded-4 shadow-sm p-4 bg-white mb-4">
                        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-newspaper text-warning me-2"></i>Recent Guides</h5>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                            <?php foreach ($recentGuides as $rg): 
                                $rgDate = !empty($rg['publish_date']) ? date('M d, Y', strtotime($rg['publish_date'])) : 'Recent';
                                $rgLink = !empty($rg['slug']) ? $basePath . 'blog/' . $rg['slug'] : $basePath . 'guide-detail.php?id=' . $rg['id'];
                            ?>
                                <li class="pb-2 border-bottom border-light-subtle last-border-none">
                                    <h6 class="fw-bold mb-1" style="font-size: 14px;">
                                        <a href="<?= h($rgLink) ?>" class="text-decoration-none text-primary hover-underline"><?= h($rg['title']) ?></a>
                                    </h6>
                                    <span class="text-muted small" style="font-size: 11px;"><i class="fa-solid fa-calendar me-1"></i><?= h($rgDate) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

