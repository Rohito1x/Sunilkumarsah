<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Master Security Bootstrap
 * Coordinates session security, HTTP response headers, XSS prevention, rate limiting, and CSRF defense.
 */

// 1. Require core modules
require_once __DIR__ . '/SessionManager.php';
require_once __DIR__ . '/Escaper.php';
require_once __DIR__ . '/RateLimiter.php';
require_once __DIR__ . '/Csrf.php';
require_once __DIR__ . '/SecureUploader.php';

// 2. Initialize Hardened Session
SessionManager::start();

// Synchronize legacy $_SESSION['csrf_token'] with the cryptographic token
$_SESSION[Csrf::TOKEN_KEY] = Csrf::getToken();

// 3. Fallback Security Headers (active if web server module headers are not configured)
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );

    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Content Security Policy tailored for Vision Point dependencies
    $cspDirectives = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://www.googletagmanager.com https://www.google-analytics.com",
        "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com",
        "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:",
        "img-src 'self' data: https: blob:",
        "connect-src 'self' https://www.google-analytics.com https://*.google-analytics.com https://www.googletagmanager.com",
        "frame-src 'self' https://www.google.com https://maps.google.com https://www.youtube.com",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'"
    ];
    header('Content-Security-Policy: ' . implode('; ', $cspDirectives));
}

// 4. Global Anti-DoS / Crawl Throttling (150 requests per minute per IP)
// Bypass for local CLI executions
if (php_sapi_name() !== 'cli') {
    $limiter = new RateLimiter();
    $clientIp = RateLimiter::getClientIp();
    $throttleKey = 'global_' . $clientIp;
    $rateCheck = $limiter->check($throttleKey, 150, 60);

    if (!$rateCheck['allowed']) {
        http_response_code(429);
        header('Retry-After: ' . $rateCheck['retry_after']);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
            header('Content-Type: application/json; charset=utf-8');
            exit(json_encode([
                'success' => false,
                'message' => 'Too many requests. Please slow down and try again shortly.',
                'retry_after' => $rateCheck['retry_after']
            ]));
        }
        header('Content-Type: text/plain; charset=utf-8');
        exit('429 Too Many Requests: Request limit reached. Please wait ' . $rateCheck['retry_after'] . ' seconds.');
    }
}

/**
 * Defensive Database Helper: Whitelist sorting parameters for dynamic queries.
 * Eliminates SQL injection in ORDER BY clauses where parameters cannot be bound.
 *
 * @param string|null $sortBy User-supplied column name
 * @param array $allowedColumns Map of valid column keys to actual SQL column identifiers
 * @param string $defaultColumn Fallback SQL column identifier
 * @param string $direction 'ASC' or 'DESC'
 * @return string Safe SQL fragment (e.g., "`created_at` DESC")
 */
if (!function_exists('safeOrderBy')) {
    function safeOrderBy(?string $sortBy, array $allowedColumns, string $defaultColumn, string $direction = 'ASC'): string {
        $column = $allowedColumns[$sortBy ?? ''] ?? $defaultColumn;
        $cleanDir = strtoupper(trim($direction)) === 'DESC' ? 'DESC' : 'ASC';
        return "{$column} {$cleanDir}";
    }
}
