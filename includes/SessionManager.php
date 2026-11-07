<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Enterprise Session Manager
 * Enforces hardened session cookies, strict mode, fixation protection, and hijacking defense.
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

        $isHttps = (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        );

        // Enforce hardened cookie configuration
        session_set_cookie_params([
            'lifetime' => 0,              // Valid until browser is closed
            'path'     => '/',
            'domain'   => '',             // Default to host
            'secure'   => $isHttps,       // Strict HTTPS transmission
            'httponly' => true,           // Deny JavaScript document.cookie access
            'samesite' => 'Lax'           // Cross-site request forgery defense
        ]);

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        session_start();

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
     * Computes a privacy-respecting client fingerprint based on User-Agent and Class-C IP subnet.
     */
    private static function generateFingerprint(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown_ua';
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        
        // IPv4 subnet mask /24 to tolerate mobile IP carrier hopping
        $subnet = preg_replace('/(\d+)\.(\d+)\.(\d+)\.\d+/', '$1.$2.$3.0', $ip);

        return hash('sha256', $ua . '|' . $subnet);
    }

    /**
     * Checks session activity timestamp and fingerprint consistency.
     */
    private static function validateIntegrity(): void
    {
        $now = time();

        // Check inactivity timeout
        if (isset($_SESSION[self::LAST_ACTIVITY_KEY]) && is_int($_SESSION[self::LAST_ACTIVITY_KEY])) {
            if (($now - $_SESSION[self::LAST_ACTIVITY_KEY]) > self::MAX_INACTIVITY_SECONDS) {
                self::destroy();
                return;
            }
        }
        $_SESSION[self::LAST_ACTIVITY_KEY] = $now;

        // Verify fingerprint
        $currentFingerprint = self::generateFingerprint();
        if (!isset($_SESSION[self::FINGERPRINT_KEY])) {
            $_SESSION[self::FINGERPRINT_KEY] = $currentFingerprint;
            return;
        }

        if (!hash_equals($_SESSION[self::FINGERPRINT_KEY], $currentFingerprint)) {
            // Mismatch indicates possible hijacked session
            self::destroy();
        }
    }
}
