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
     * Determines whether the current request is conducted over HTTPS.
     */
    public static function isHttps(): bool
    {
        return (
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
            || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            || (!empty($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false)
        );
    }

    /**
     * Calculates the normalized cookie domain. Returns leading dot domain for real domains,
     * or empty string for localhost and IP addresses.
     */
    public static function getCookieDomain(): string
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $host = preg_replace('/:\d+$/', '', $host);
        if (!empty($host) && !filter_var($host, FILTER_VALIDATE_IP) && $host !== 'localhost') {
            $domainBase = preg_replace('/^www\./i', '', $host);
            if (strpos($domainBase, '.') !== false) {
                return '.' . $domainBase;
            }
        }
        return '';
    }

    /**
     * Initializes a hardened PHP session with resilient cookie parameters.
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::restoreAuthFromCookie();
            return;
        }

        // Resilient session save path: check server default, then local project directory, then sys temp
        $currentSavePath = @session_save_path();
        if (empty($currentSavePath) || !@is_dir($currentSavePath) || !@is_writable($currentSavePath)) {
            $localSessions = dirname(__DIR__) . '/sessions';
            if (@is_dir($localSessions) && @is_writable($localSessions)) {
                @session_save_path($localSessions);
            } else {
                $sysTemp = sys_get_temp_dir();
                if (!empty($sysTemp) && @is_dir($sysTemp) && @is_writable($sysTemp)) {
                    @session_save_path($sysTemp);
                }
            }
        }

        if (!headers_sent()) {
            @ini_set('session.use_only_cookies', '1');
            @ini_set('session.use_trans_sid', '0');

            $domain = self::getCookieDomain();
            $cookieParams = [
                'lifetime' => 86400 * 7,      // 7 days
                'path'     => '/',
                'secure'   => self::isHttps(),
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            if (!empty($domain)) {
                $cookieParams['domain'] = $domain;
            }
            session_set_cookie_params($cookieParams);
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        // Automatically restore authenticated session from signed cookie if session file was dropped
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

        // Explicitly set the session cookie header so LiteSpeed doesn't lose the cookie on 302 redirect
        if (!headers_sent()) {
            $sessId = session_id();
            if (!empty($sessId)) {
                $domain = self::getCookieDomain();
                $options = [
                    'expires'  => time() + (86400 * 7),
                    'path'     => '/',
                    'secure'   => self::isHttps(),
                    'httponly' => true,
                    'samesite' => 'Lax'
                ];
                if (!empty($domain)) {
                    $options['domain'] = $domain;
                }
                setcookie(session_name(), $sessId, $options);
            }
        }

        // Issue signed cryptographic auth cookie as persistent backup
        self::setAuthCookie($userId, $userEmail);
    }

    /**
     * Sets a cryptographically signed HMAC auth cookie as an unbreakable fallback.
     * Uses dot-delimited hex encoding (userId.expires.hexEmail.signature) to ensure
     * zero special characters, avoiding all cookie urlencoding/decoding discrepancies.
     */
    public static function setAuthCookie(int $userId, string $userEmail): void
    {
        $expires = time() + (86400 * 7); // 7 days
        $cleanEmail = strtolower(trim($userEmail));
        $hexEmail = bin2hex($cleanEmail);
        $payload = $userId . '.' . $expires . '.' . $hexEmail;
        $sig = hash_hmac('sha256', $payload, self::AUTH_SECRET);
        $token = $payload . '.' . $sig;

        if (!headers_sent()) {
            $domain = self::getCookieDomain();
            $options = [
                'expires'  => $expires,
                'path'     => '/',
                'secure'   => self::isHttps(),
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            if (!empty($domain)) {
                $options['domain'] = $domain;
            }
            setcookie(self::AUTH_COOKIE_NAME, $token, $options);
        }
    }

    /**
     * Restores an active admin session from the signed auth cookie if $_SESSION was dropped.
     * Supports both the robust dot-hex format and the legacy base64 format.
     */
    public static function restoreAuthFromCookie(): bool
    {
        if (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] > 0) {
            return true;
        }

        if (empty($_COOKIE[self::AUTH_COOKIE_NAME])) {
            return false;
        }

        $token = trim((string)$_COOKIE[self::AUTH_COOKIE_NAME]);

        // 1. Primary: Dot-hex format (userId.expires.hexEmail.signature)
        if (strpos($token, '.') !== false) {
            $parts = explode('.', $token);
            if (count($parts) === 4) {
                list($userId, $expires, $hexEmail, $sig) = $parts;
                if ((int)$expires >= time()) {
                    $payload = $userId . '.' . $expires . '.' . $hexEmail;
                    $expectedSig = hash_hmac('sha256', $payload, self::AUTH_SECRET);

                    if (hash_equals($expectedSig, $sig)) {
                        $userEmail = @hex2bin($hexEmail);
                        if ($userEmail !== false && !empty($userEmail)) {
                            $_SESSION['admin_id'] = (int)$userId;
                            $_SESSION['admin_email'] = $userEmail;
                            $_SESSION[self::LAST_ACTIVITY_KEY] = time();
                            return true;
                        }
                    }
                }
            }
        }

        // 2. Fallback: Legacy base64 pipe format (restores + from spaces)
        $normalized = str_replace(' ', '+', $token);
        $raw = base64_decode($normalized, true);
        if ($raw) {
            $parts = explode('|', $raw);
            if (count($parts) === 4) {
                list($userId, $userEmail, $expires, $sig) = $parts;
                if ((int)$expires >= time()) {
                    $payload = $userId . '|' . $userEmail . '|' . $expires;
                    $expectedSig = hash_hmac('sha256', $payload, self::AUTH_SECRET);

                    if (hash_equals($expectedSig, $sig)) {
                        $_SESSION['admin_id'] = (int)$userId;
                        $_SESSION['admin_email'] = $userEmail;
                        $_SESSION[self::LAST_ACTIVITY_KEY] = time();
                        // Automatically upgrade to modern dot-hex token
                        self::setAuthCookie((int)$userId, $userEmail);
                        return true;
                    }
                }
            }
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

        $domain = self::getCookieDomain();
        $isHttps = self::isHttps();

        if (ini_get('session.use_cookies')) {
            $options = [
                'expires'  => time() - 86400,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            if (!empty($domain)) {
                $options['domain'] = $domain;
            }
            setcookie(session_name(), '', $options);
            setcookie(session_name(), '', time() - 86400, '/');
        }

        if (!headers_sent()) {
            $options = [
                'expires'  => time() - 86400,
                'path'     => '/',
                'secure'   => $isHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ];
            if (!empty($domain)) {
                $options['domain'] = $domain;
            }
            setcookie(self::AUTH_COOKIE_NAME, '', $options);
            setcookie(self::AUTH_COOKIE_NAME, '', time() - 86400, '/');
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
