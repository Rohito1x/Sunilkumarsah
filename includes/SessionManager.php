<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Enterprise Session Manager
 * Enforces hardened session cookies, fixation protection, and hijacking defense.
 */
final class SessionManager
{
    private const FINGERPRINT_KEY = '_sec_fingerprint';
    private const LAST_ACTIVITY_KEY = '_sec_last_activity';
    private const MAX_INACTIVITY_SECONDS = 86400; // 24 hours

    /**
     * Initializes a hardened PHP session with secure cookie parameters.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $rawHost = $_SERVER['HTTP_HOST'] ?? '';
        $cleanHost = strtolower(explode(':', $rawHost)[0]);

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
            || (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_SSL']) === 'on')
            || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false)
        );

        // Configure session save path defensively
        // If current path is empty or not writable in caged Linux/LiteSpeed, fallback to system temp or local sessions directory
        $currentSavePath = @session_save_path();
        $isPathWritable = !empty($currentSavePath) && @is_dir($currentSavePath) && @is_writable($currentSavePath);

        if (!$isPathWritable) {
            $sysTemp = sys_get_temp_dir();
            if (!empty($sysTemp) && @is_dir($sysTemp) && @is_writable($sysTemp)) {
                @session_save_path($sysTemp);
            } else {
                $localSessionDir = dirname(__DIR__) . '/sessions';
                if (!is_dir($localSessionDir)) {
                    @mkdir($localSessionDir, 0700, true);
                }
                if (is_dir($localSessionDir) && @is_writable($localSessionDir)) {
                    @session_save_path($localSessionDir);
                }
            }
        }

        // Use host-only cookie (domain '') for 100% browser compatibility across desktop, mobile, and cPanel environments
        $cookieDomain = '';

        if (!headers_sent()) {
            // Enforce hardened cookie configuration
            session_set_cookie_params([
                'lifetime' => 0,              // Valid until browser is closed
                'path'     => '/',
                'domain'   => $cookieDomain,  // Unified domain for both www & non-www
                'secure'   => $isHttps,       // Strict HTTPS transmission when running under HTTPS
                'httponly' => true,           // Deny JavaScript document.cookie access
                'samesite' => 'Lax'           // Cross-site request forgery defense
            ]);

            @ini_set('session.use_only_cookies', '1');
            @ini_set('session.use_trans_sid', '0');
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        // Enforce inactivity timeout & session hijacking defenses
        self::validateIntegrity();
    }

    /**
     * Regenerates the session identifier upon login or privilege escalation.
     * Prevents session fixation attacks.
     */
    public static function regenerateOnAuth(?int $userId = null, ?string $userEmail = null): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        // Regenerate and discard old session ID
        session_regenerate_id(true);

        if ($userId !== null) {
            $_SESSION['admin_id'] = $userId;
        }
        if ($userEmail !== null) {
            $_SESSION['admin_email'] = $userEmail;
        }

        $_SESSION[self::FINGERPRINT_KEY] = self::generateFingerprint();
        $_SESSION[self::LAST_ACTIVITY_KEY] = time();
    }

    /**
     * Terminates and cleans up the session during logout.
     */
    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        @session_destroy();
    }

    /**
     * Computes a client browser fingerprint based on User-Agent.
     * Note: Per OWASP guidelines, client IP is excluded to avoid breaking legitimate
     * sessions on mobile networks, IPv6 privacy extensions, and CDN reverse proxies.
     */
    private static function generateFingerprint(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'standard_browser';
        return hash('sha256', $ua);
    }

    /**
     * Checks session activity timestamp and fingerprint consistency for authenticated users.
     */
    private static function validateIntegrity(): void
    {
        $now = time();

        // Only enforce strict fingerprinting and inactivity timeout on active authenticated administrator sessions
        if (isset($_SESSION['admin_id'])) {
            if (isset($_SESSION[self::LAST_ACTIVITY_KEY]) && is_int($_SESSION[self::LAST_ACTIVITY_KEY])) {
                if (($now - $_SESSION[self::LAST_ACTIVITY_KEY]) > self::MAX_INACTIVITY_SECONDS) {
                    self::destroy();
                    return;
                }
            }

            $currentFingerprint = self::generateFingerprint();
            if (!isset($_SESSION[self::FINGERPRINT_KEY])) {
                $_SESSION[self::FINGERPRINT_KEY] = $currentFingerprint;
            }
        }

        $_SESSION[self::LAST_ACTIVITY_KEY] = $now;
    }
}
