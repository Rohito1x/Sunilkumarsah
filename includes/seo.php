<?php
/**
 * Krishna Education Foundation - Centralized SEO Component
 * 
 * Provides dynamic meta tags, canonical URLs, Open Graph, Twitter Card,
 * Schema.org JSON-LD structured data, breadcrumbs, and 301 redirection.
 */

if (!function_exists('h')) {
    function h($s) {
        return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
    }
}

// Global Canonical Domain Definition
if (!defined('CANONICAL_DOMAIN')) {
    define('CANONICAL_DOMAIN', 'https://krishnaeducationfoundation.edu.np');
}

/**
 * Check and execute 301/302 database redirects
 */
if (!function_exists('checkAndPerformRedirect')) {
    function checkAndPerformRedirect(): void {
        global $pdo;
        if (!isset($pdo) || !$pdo) {
            return;
        }

        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $uriParts = explode('?', $requestUri, 2);
        $cleanPath = rtrim($uriParts[0], '/');
        if ($cleanPath === '') {
            $cleanPath = '/';
        }

        try {
            $stmt = $pdo->prepare("SELECT id, target_url, status_code FROM seo_redirects WHERE (source_url = :path OR source_url = :full) AND is_active = 1 LIMIT 1");
            $stmt->execute([
                ':path' => $cleanPath,
                ':full' => $requestUri
            ]);
            $redirect = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($redirect) {
                // Increment hit count asynchronously / silently
                $upd = $pdo->prepare("UPDATE seo_redirects SET hit_count = hit_count + 1 WHERE id = :id");
                $upd->execute([':id' => $redirect['id']]);

                $code = (int)$redirect['status_code'] === 302 ? 302 : 301;
                http_response_code($code);
                header("Location: " . $redirect['target_url'], true, $code);
                exit;
            }
        } catch (PDOException $e) {
            // Fail silently to avoid breaking request
        }
    }
}

/**
 * Generate normalized absolute canonical URL
 */
if (!function_exists('generateCanonicalUrl')) {
    function generateCanonicalUrl(?string $slugOrPath = null): string {
        if ($slugOrPath === null || $slugOrPath === '' || $slugOrPath === 'home' || $slugOrPath === 'index.php') {
            return CANONICAL_DOMAIN . '/';
        }

        if (strpos($slugOrPath, 'http://') === 0 || strpos($slugOrPath, 'https://') === 0) {
            return $slugOrPath;
        }

        $clean = trim($slugOrPath, '/');
        // If it's a file with extension like sitemap.xml or robots.txt, don't append trailing slash
        if (preg_match('/\.(xml|txt|png|jpg|webp|php)$/i', $clean)) {
            return CANONICAL_DOMAIN . '/' . $clean;
        }

        return CANONICAL_DOMAIN . '/' . $clean . '/';
    }
}

/**
 * Fetch SEO metadata from database or return intelligent fallbacks
 */
if (!function_exists('getSeoData')) {
    function getSeoData(?string $pageType = 'static', ?int $pageId = null, ?string $customSlug = null): array {
        global $pdo, $siteSettings;

        // Auto-detect current page slug if not explicitly passed
        if (empty($customSlug)) {
            $script = basename($_SERVER['PHP_SELF'] ?? 'index.php', '.php');
            if ($script === 'index') {
                $customSlug = 'home';
            } else {
                $customSlug = $script;
            }
        }

        $siteName = $siteSettings['site_name'] ?? 'Vision Point Educational Service';
        $defaultTitle = $siteSettings['site_title'] ?? ($siteName . ' – Education Consultancy in Nepal');
        $defaultLogo = 'assets/images/logo1.png';
        $defaultImgUrl = generateCanonicalUrl($defaultLogo);

        $seo = [
            'slug' => $customSlug,
            'page_name' => ucwords(str_replace(['-', '_'], ' ', $customSlug)),
            'meta_title' => $defaultTitle,
            'meta_description' => 'Krishna Education Foundation is a certified education consultancy in Nepal helping students with admissions, scholarships, and visa guidance for India and abroad.',
            'meta_keywords' => 'Education Consultancy in Nepal, Best education consultancy in Nepal, study abroad consultancy Nepal',
            'h1' => $defaultTitle,
            'canonical_url' => generateCanonicalUrl($customSlug),
            'og_title' => $defaultTitle,
            'og_description' => 'Expert education counseling, scholarship assistance, and admissions for Nepali students.',
            'og_image' => $defaultImgUrl,
            'og_type' => 'website',
            'robots' => 'index,follow',
            'schema_type' => 'EducationalOrganization',
            'status' => 1
        ];

        if (isset($pdo) && $pdo) {
            try {
                $row = null;
                if (!empty($customSlug)) {
                    $stmt = $pdo->prepare("SELECT * FROM seo_pages WHERE slug = :slug AND status = 1 LIMIT 1");
                    $stmt->execute([':slug' => $customSlug]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if (!$row && $pageType !== 'static' && $pageId !== null) {
                    $stmt = $pdo->prepare("SELECT * FROM seo_pages WHERE page_type = :type AND page_id = :id AND status = 1 LIMIT 1");
                    $stmt->execute([':type' => $pageType, ':id' => $pageId]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                if ($row) {
                    $seo['meta_title'] = !empty($row['meta_title']) ? $row['meta_title'] : $seo['meta_title'];
                    $seo['meta_description'] = !empty($row['meta_description']) ? $row['meta_description'] : $seo['meta_description'];
                    $seo['meta_keywords'] = !empty($row['meta_keywords']) ? $row['meta_keywords'] : $seo['meta_keywords'];
                    $seo['h1'] = !empty($row['h1']) ? $row['h1'] : $seo['h1'];
                    $seo['canonical_url'] = !empty($row['canonical_url']) ? $row['canonical_url'] : $seo['canonical_url'];
                    $seo['og_title'] = !empty($row['og_title']) ? $row['og_title'] : $seo['meta_title'];
                    $seo['og_description'] = !empty($row['og_description']) ? $row['og_description'] : $seo['meta_description'];
                    $seo['og_image'] = !empty($row['og_image']) ? generateCanonicalUrl($row['og_image']) : $defaultImgUrl;
                    $seo['robots'] = !empty($row['robots']) ? $row['robots'] : 'index,follow';
                    $seo['schema_type'] = !empty($row['schema_type']) ? $row['schema_type'] : 'EducationalOrganization';
                    $seo['page_name'] = !empty($row['page_name']) ? $row['page_name'] : $seo['page_name'];
                }
            } catch (PDOException $e) {
                // Use default values on query exception
            }
        }

        return $seo;
    }
}

/**
 * Generate standard HTML Meta tags
 */
if (!function_exists('generateMetaTags')) {
    function generateMetaTags(array $seoData): string {
        global $siteSettings;
        $html = '';

        $title = $seoData['meta_title'] ?? 'Krishna Education Foundation';
        $desc = $seoData['meta_description'] ?? '';
        $keywords = $seoData['meta_keywords'] ?? '';
        $robots = $seoData['robots'] ?? 'index,follow';
        $canonical = $seoData['canonical_url'] ?? generateCanonicalUrl($seoData['slug'] ?? '');

        $html .= '<title>' . h($title) . "</title>\n";
        $html .= '    <meta name="description" content="' . h($desc) . "\">\n";
        if (!empty($keywords)) {
            $html .= '    <meta name="keywords" content="' . h($keywords) . "\">\n";
        }
        $html .= '    <meta name="robots" content="' . h($robots) . "\">\n";
        $html .= '    <link rel="canonical" href="' . h($canonical) . "\">\n";

        // Google Search Console Verification code if configured
        $gsc = $siteSettings['gsc_verification_code'] ?? getenv('GSC_VERIFICATION_CODE');
        if (!empty($gsc)) {
            $html .= '    <meta name="google-site-verification" content="' . h($gsc) . "\">\n";
        }

        return $html;
    }
}

/**
 * Generate Open Graph Meta tags
 */
if (!function_exists('generateOpenGraphTags')) {
    function generateOpenGraphTags(array $seoData): string {
        global $siteSettings;
        $html = '';

        $siteName = $siteSettings['site_name'] ?? 'Vision Point Educational Service';
        $ogTitle = $seoData['og_title'] ?? $seoData['meta_title'] ?? $siteName;
        $ogDesc = $seoData['og_description'] ?? $seoData['meta_description'] ?? '';
        $ogUrl = $seoData['canonical_url'] ?? generateCanonicalUrl($seoData['slug'] ?? '');
        $ogImg = $seoData['og_image'] ?? generateCanonicalUrl('assets/images/logo1.png');
        $ogType = $seoData['og_type'] ?? 'website';

        $html .= "    <!-- Open Graph / Facebook -->\n";
        $html .= '    <meta property="og:site_name" content="' . h($siteName) . "\">\n";
        $html .= '    <meta property="og:type" content="' . h($ogType) . "\">\n";
        $html .= '    <meta property="og:title" content="' . h($ogTitle) . "\">\n";
        $html .= '    <meta property="og:description" content="' . h($ogDesc) . "\">\n";
        $html .= '    <meta property="og:url" content="' . h($ogUrl) . "\">\n";
        $html .= '    <meta property="og:image" content="' . h($ogImg) . "\">\n";
        $html .= '    <meta property="og:locale" content="en_US' . "\">\n";

        return $html;
    }
}

/**
 * Generate Twitter Card Meta tags
 */
if (!function_exists('generateTwitterCardTags')) {
    function generateTwitterCardTags(array $seoData): string {
        $html = '';

        $twTitle = $seoData['og_title'] ?? $seoData['meta_title'] ?? 'Krishna Education Foundation';
        $twDesc = $seoData['og_description'] ?? $seoData['meta_description'] ?? '';
        $twImg = $seoData['og_image'] ?? generateCanonicalUrl('assets/images/logo1.png');

        $html .= "    <!-- Twitter -->\n";
        $html .= '    <meta name="twitter:card" content="summary_large_image' . "\">\n";
        $html .= '    <meta name="twitter:title" content="' . h($twTitle) . "\">\n";
        $html .= '    <meta name="twitter:description" content="' . h($twDesc) . "\">\n";
        $html .= '    <meta name="twitter:image" content="' . h($twImg) . "\">\n";

        return $html;
    }
}

/**
 * Generate Schema.org JSON-LD Structured Data
 */
if (!function_exists('generateSchemaMarkup')) {
    function generateSchemaMarkup(array $seoData, ?string $type = null, array $extraData = []): string {
        global $siteSettings, $pdo;

        $schemas = [];
        $siteName = $siteSettings['site_name'] ?? 'Vision Point Educational Service';
        $canonical = $seoData['canonical_url'] ?? generateCanonicalUrl($seoData['slug'] ?? '');
        $logoUrl = generateCanonicalUrl('assets/images/logo1.png');
        $phone = $siteSettings['contact_phone'] ?? '+977 9807765812';
        $email = $siteSettings['contact_email'] ?? 'info@krishnaeducation.com.np';
        $location = $siteSettings['contact_location'] ?? 'Krishna Education Foundation, Vasundhara Complex, 12 Roadcess Chowk, Biratnagar 56613, Nepal';

        // 1. EducationalOrganization Schema (Primary Identity)
        $orgSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            '@id' => CANONICAL_DOMAIN . '/#organization',
            'name' => $siteName,
            'url' => CANONICAL_DOMAIN . '/',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => $logoUrl
            ],
            'image' => $logoUrl,
            'description' => 'Krishna Education Foundation is an accredited education consultancy in Nepal offering student counseling, admission processing, and scholarships for universities in India and abroad.',
            'telephone' => $phone,
            'email' => $email,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Vasundhara Complex, 12 Roadcess Chowk',
                'addressLocality' => 'Biratnagar',
                'postalCode' => '56613',
                'addressCountry' => 'NP'
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 26.4525,
                'longitude' => 87.2718
            ],
            'sameAs' => [
                'https://www.facebook.com/krishnaeducationfoundation',
                'https://www.instagram.com/krishnaeducationfoundation'
            ]
        ];
        $schemas[] = $orgSchema;

        // 2. Article Schema for Blog/Guide post
        if ($type === 'Article' || ($seoData['schema_type'] ?? '') === 'Article') {
            $article = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => $canonical
                ],
                'headline' => $extraData['headline'] ?? $seoData['h1'] ?? $seoData['meta_title'],
                'description' => $extraData['excerpt'] ?? $seoData['meta_description'],
                'image' => !empty($extraData['image']) ? generateCanonicalUrl($extraData['image']) : $seoData['og_image'],
                'datePublished' => !empty($extraData['published_at']) ? date('c', strtotime($extraData['published_at'])) : date('c'),
                'dateModified' => !empty($extraData['updated_at']) ? date('c', strtotime($extraData['updated_at'])) : date('c'),
                'author' => [
                    '@type' => 'Person',
                    'name' => $extraData['author'] ?? 'Academic Counselor'
                ],
                'publisher' => [
                    '@type' => 'EducationalOrganization',
                    'name' => $siteName,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoUrl
                    ]
                ]
            ];
            $schemas[] = $article;
        }

        // 3. FAQPage Schema for pages with genuine FAQs
        if (!empty($extraData['faqs']) && is_array($extraData['faqs'])) {
            $faqElements = [];
            foreach ($extraData['faqs'] as $faq) {
                if (!empty($faq['q']) && !empty($faq['a'])) {
                    $faqElements[] = [
                        '@type' => 'Question',
                        'name' => strip_tags($faq['q']),
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => strip_tags($faq['a'])
                        ]
                    ];
                }
            }
            if (!empty($faqElements)) {
                $schemas[] = [
                    '@context' => 'https://schema.org',
                    '@type' => 'FAQPage',
                    'mainEntity' => $faqElements
                ];
            }
        }

        $output = '';
        foreach ($schemas as $s) {
            $output .= "    <script type=\"application/ld+json\">\n" . json_encode($s, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n    </script>\n";
        }

        return $output;
    }
}

/**
 * Generate Visual and Schema Breadcrumbs
 */
if (!function_exists('generateBreadcrumbs')) {
    function generateBreadcrumbs(array $items): string {
        if (empty($items)) {
            return '';
        }

        $jsonList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        $html = '<nav aria-label="breadcrumb" class="my-2">' . "\n";
        $html .= '    <ol class="breadcrumb small mb-0">' . "\n";

        $pos = 1;
        $total = count($items);
        foreach ($items as $label => $url) {
            $isLast = ($pos === $total);
            $fullUrl = $url ? generateCanonicalUrl($url) : '';

            $jsonList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $pos,
                'name' => $label,
                'item' => $fullUrl ?: CANONICAL_DOMAIN . '/'
            ];

            if ($isLast || empty($url)) {
                $html .= '        <li class="breadcrumb-item active text-white" aria-current="page">' . h($label) . '</li>' . "\n";
            } else {
                $html .= '        <li class="breadcrumb-item"><a href="' . h($url) . '" class="text-decoration-none text-white-50">' . h($label) . '</a></li>' . "\n";
            }
            $pos++;
        }

        $html .= '    </ol>' . "\n";
        $html .= '</nav>' . "\n";
        $html .= '<script type="application/ld+json">' . json_encode($jsonList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";

        return $html;
    }
}

