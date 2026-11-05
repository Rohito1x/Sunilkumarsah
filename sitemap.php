<?php
/**
 * Vision Point Educational Service - Dynamic XML Sitemap Generator
 * 
 * Generates standards-compliant XML sitemap dynamically from database.
 * Supports public indexable URLs, lastmod timestamps, changefreq, and priorities.
 */

// Set proper XML content type and character encoding
header('Content-Type: application/xml; charset=utf-8');
header('X-Robots-Tag: noindex, follow');

require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$urls = [];

// 1. Fetch all indexable static / custom pages from `seo_pages`
if (isset($pdo) && $pdo) {
    try {
        $stmt = $pdo->query("SELECT slug, robots, updated_at FROM seo_pages WHERE status = 1 ORDER BY id ASC");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Exclude noindex pages
            if (strpos(strtolower($row['robots']), 'noindex') !== false) {
                continue;
            }

            $slug = $row['slug'];
            $loc = generateCanonicalUrl($slug === 'home' ? '' : $slug);
            $lastmod = !empty($row['updated_at']) ? date('Y-m-d', strtotime($row['updated_at'])) : date('Y-m-d');
            
            $priority = '0.8';
            $changefreq = 'weekly';

            if ($slug === 'home') {
                $priority = '1.0';
                $changefreq = 'daily';
            } elseif (in_array($slug, ['study-in-india', 'courses', 'universities', 'scholarships', 'services', 'about'], true)) {
                $priority = '0.9';
                $changefreq = 'weekly';
            }

            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => $changefreq,
                'priority' => $priority
            ];
        }
    } catch (PDOException $e) {
        // Fallback
    }

    // 2. Fetch all published Blog / Resource Guides
    try {
        $stmtG = $pdo->query("SELECT slug, publish_date, updated_at FROM guides WHERE status = 'published' ORDER BY id DESC");
        while ($row = $stmtG->fetch(PDO::FETCH_ASSOC)) {
            $loc = generateCanonicalUrl('blog/' . $row['slug']);
            $dateVal = !empty($row['updated_at']) ? $row['updated_at'] : (!empty($row['publish_date']) ? $row['publish_date'] : date('Y-m-d'));
            $lastmod = date('Y-m-d', strtotime($dateVal));

            $urls[$loc] = [
                'loc' => $loc,
                'lastmod' => $lastmod,
                'changefreq' => 'monthly',
                'priority' => '0.7'
            ];
        }
    } catch (PDOException $e) {
        // Fallback
    }
}

// Fallback essential URLs if database is empty
if (empty($urls)) {
    $fallbackList = [
        '' => ['priority' => '1.0', 'changefreq' => 'daily'],
        'about' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'services' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'study-in-india' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'study-in-australia' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'study-in-uk' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'study-in-usa' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'study-in-canada' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'universities' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'courses' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'scholarships' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'visa-guidance' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'ielts' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'pte' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'cee-preparation' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'ctevt-preparation' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'career-counselling' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'blog' => ['priority' => '0.8', 'changefreq' => 'weekly'],
        'contact' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'student-success-stories' => ['priority' => '0.7', 'changefreq' => 'weekly']
    ];

    $today = date('Y-m-d');
    foreach ($fallbackList as $slug => $meta) {
        $loc = generateCanonicalUrl($slug);
        $urls[$loc] = [
            'loc' => $loc,
            'lastmod' => $today,
            'changefreq' => $meta['changefreq'],
            'priority' => $meta['priority']
        ];
    }
}

// Generate XML Output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($urls as $item): ?>
    <url>
        <loc><?= h($item['loc']) ?></loc>
        <lastmod><?= h($item['lastmod']) ?></lastmod>
        <changefreq><?= h($item['changefreq']) ?></changefreq>
        <priority><?= h($item['priority']) ?></priority>
    </url>
<?php endforeach; ?>
</urlset>

