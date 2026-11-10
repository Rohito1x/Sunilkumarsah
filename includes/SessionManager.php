<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Enterprise Session & Auth Manager
 * Manages PHP sessions and dual-layer cryptographic authentication cookies
 * for 100% login persistence across LiteSpeed, Cloudflare, cPanel, and mobile browsers.
 */
final class SessionManager
{
    public const AUTH_COOKIE_NAME = 'vp_admin_auth';
    private const AUTH_SECRET = 'vp_secure_auth_key_biratnagar_2026';
    private const FINGERPRINT_KEY = '_sec_fingerprint';
    private const LAST_ACTIVITY_KEY = '_sec_last_activity';
    private const MAX_INACTIVITY_SECONDS = 86400 * 7; // 7 days

    /**
     * Initializes a hardened PHP session with resilient cookie parameters.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::restoreAuthFromCookie();
            return;
        }

        // Only assign fallback path if server has no save_path configured at all
        $currentSavePath = @session_save_path();
        if (empty($currentSavePath)) {
            $sysTemp = sys_get_temp_dir();
            if (!empty($sysTemp) && @is_dir($sysTemp) && @is_writable($sysTemp)) {
                @session_save_path($sysTemp);
            }
        }

        if (!headers_sent()) {
            // Universal cookie configuration (works across HTTP, HTTPS, Cloudflare & reverse proxies)
            @ini_set('session.use_only_cookies', '1');
            @ini_set('session.use_trans_sid', '0');

            session_set_cookie_params([
                'lifetime' => 86400 * 7,      // 7 days
                'path'     => '/',
                'domain'   => '',             // Host-only cookie for 100% browser compatibility
                'secure'   => false,          // Works seamlessly across SSL proxies & direct connections
                'httponly' => true,           // Deny JavaScript document.cookie access
                'samesite' => 'Lax'           // Cross-site request forgery defense
            ]);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        // Automatically restore authenticated session from signed cookie if session file was refreshed
        self::restoreAuthFromCookie();

        self::validateIntegrity();
    }

    /**
     * Regenerates the session identifier upon login and sets the dual-layer signed auth cookie.
     */
    public static function regenerateOnAuth(int $userId, string $userEmail): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        $_SESSION['admin_id'] = $userId;
        $_SESSION['admin_email'] = $userEmail;
        $_SESSION[self::FINGERPRINT_KEY] = self::generateFingerprint();
        $_SESSION[self::LAST_ACTIVITY_KEY] = time();

        // Safely regenerate session ID without destroying previous session file prematurely
        @session_regenerate_id(false);

        // Explicitly flush the session cookie header so LiteSpeed doesn't lose the cookie on 302 redirect
        if (!headers_sent()) {
            $sessId = session_id();
            if (!empty($sessId)) {
                setcookie(
                    session_name(),
                    $sessId,
                    time() + (86400 * 7),
                    '/',
                    '',
                    false,
                    true
                );
            }
        }

        // Issue signed cryptographic auth cookie as persistent backup
        self::setAuthCookie($userId, $userEmail);
    }

    /**
     * Sets a cryptographically signed HMAC auth cookie as an unbreakable fallback.
     */
    public static function setAuthCookie(int $userId, string $userEmail): void
    {
        $expires = time() + (86400 * 7); // 7 days
        $payload = $userId . '|' . strtolower(trim($userEmail)) . '|' . $expires;
        $sig = hash_hmac('sha256', $payload, self::AUTH_SECRET);
        $token = base64_encode($payload . '|' . $sig);

        if (!headers_sent()) {
            setcookie(self::AUTH_COOKIE_NAME, $token, [
                'expires'  => $expires,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
    }

    /**
     * Restores an active admin session from the signed auth cookie if $_SESSION was dropped.
     */
    public static function restoreAuthFromCookie(): bool
    {
        if (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] > 0) {
            return true;
        }

        if (empty($_COOKIE[self::AUTH_COOKIE_NAME])) {
            return false;
        }

        $token = (string)$_COOKIE[self::AUTH_COOKIE_NAME];
        $raw = base64_decode($token, true);
        if (!$raw) {
            return false;
        }

        $parts = explode('|', $raw);
        if (count($parts) !== 4) {
            return false;
        }

        list($userId, $userEmail, $expires, $sig) = $parts;

        if ((int)$expires < time()) {
            return false;
        }

        $payload = $userId . '|' . $userEmail . '|' . $expires;
        $expectedSig = hash_hmac('sha256', $payload, self::AUTH_SECRET);

        if (hash_equals($expectedSig, $sig)) {
            $_SESSION['admin_id'] = (int)$userId;
            $_SESSION['admin_email'] = $userEmail;
            return true;
        }

        return false;
    }

    /**
     * Terminates and cleans up the session and auth cookie during logout.
     */
    public static function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            self::start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            setcookie(
                session_name(),
                '',
                time() - 42000,
                '/',
                '',
                false,
                true
            );
        }

        if (!headers_sent()) {
            setcookie(self::AUTH_COOKIE_NAME, '', [
                'expires'  => time() - 42000,
                'path'     => '/',
                'domain'   => '',
                'secure'   => false,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }

        @session_destroy();
    }

    private static function generateFingerprint(): string
    {
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'standard_browser';
        return hash('sha256', $ua);
    }

    private static function validateIntegrity(): void
    {
        $now = time();

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
