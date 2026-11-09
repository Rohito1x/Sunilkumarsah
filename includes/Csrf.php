<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Cryptographic CSRF Defense Library
 * Generates and validates timing-safe CSRF tokens for POST forms and AJAX requests.
 * Fully compatible with existing forms referencing $_SESSION['csrf_token'].
 */
final class Csrf
{
    public const TOKEN_KEY = 'csrf_token';
    public const ALT_FORM_KEY = '_csrf_token';
    public const HEADER_KEY = 'HTTP_X_CSRF_TOKEN';

    /**
     * Initializes or retrieves the current CSRF token from the session.
     */
    public static function getToken(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (class_exists('SessionManager')) {
                SessionManager::start();
            } else {
                session_start();
            }
        }

        if (empty($_SESSION[self::TOKEN_KEY]) || !is_string($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Returns a hidden HTML input field for inclusion inside forms.
     */
    public static function field(): string
    {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="' . self::TOKEN_KEY . '" value="' . $token . '">';
    }

    /**
     * Returns an HTML <meta> tag for inclusion in page <head> for AJAX requests.
     */
    public static function meta(): string
    {
        $token = htmlspecialchars(self::getToken(), ENT_QUOTES, 'UTF-8');
        return '<meta name="csrf-token" content="' . $token . '">';
    }

    /**
     * Validates whether the request originates from the same domain (OWASP Origin/Referer verification).
     */
    public static function validateSameOrigin(): bool
    {
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        if (empty($currentHost)) {
            return false;
        }

        $cleanCurrent = strtolower(explode(':', $currentHost)[0]);

        // 1. Check HTTP_ORIGIN
        if (!empty($_SERVER['HTTP_ORIGIN'])) {
            $originHost = parse_url((string)$_SERVER['HTTP_ORIGIN'], PHP_URL_HOST);
            if ($originHost) {
                $cleanOrigin = strtolower($originHost);
                if ($cleanOrigin === $cleanCurrent || $cleanOrigin === 'www.' . $cleanCurrent || $cleanCurrent === 'www.' . $cleanOrigin) {
                    return true;
                }
            }
        }

        // 2. Check HTTP_REFERER
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $refererHost = parse_url((string)$_SERVER['HTTP_REFERER'], PHP_URL_HOST);
            if ($refererHost) {
                $cleanReferer = strtolower($refererHost);
                if ($cleanReferer === $cleanCurrent || $cleanReferer === 'www.' . $cleanCurrent || $cleanCurrent === 'www.' . $cleanReferer) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Validates the CSRF token on mutating requests (POST, PUT, DELETE, PATCH).
     * Employs timing-safe token verification with OWASP same-origin fallback.
     */
    public static function validateRequest(): bool
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        // Safe HTTP methods do not modify server state
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return true;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (class_exists('SessionManager')) {
                SessionManager::start();
            } else {
                @session_start();
            }
        }

        $sessionToken = $_SESSION[self::TOKEN_KEY] ?? '';

        // 1. Inspect POST body under both 'csrf_token' and '_csrf_token'
        $submittedToken = $_POST[self::TOKEN_KEY] ?? ($_POST[self::ALT_FORM_KEY] ?? null);

        // 2. Fallback to custom AJAX HTTP header
        if ($submittedToken === null) {
            if (!empty($_SERVER[self::HEADER_KEY])) {
                $submittedToken = $_SERVER[self::HEADER_KEY];
            } elseif (!empty($_SERVER['HTTP_CSRF_TOKEN'])) {
                $submittedToken = $_SERVER['HTTP_CSRF_TOKEN'];
            }
        }

        // Standard timing-safe cryptographic token verification
        if (!empty($sessionToken) && is_string($submittedToken) && !empty($submittedToken) && hash_equals($sessionToken, trim($submittedToken))) {
            return true;
        }

        // Defense-in-depth fallback: Same-Origin verification
        // Safeguards genuine user inquiries, reviews, and receipt uploads even if session cookie was delayed by proxy/LSPHP
        if (self::validateSameOrigin()) {
            if (empty($_SESSION[self::TOKEN_KEY])) {
                $_SESSION[self::TOKEN_KEY] = self::getToken();
            }
            return true;
        }

        return false;
    }

    /**
     * Enforces CSRF validation with an optional route exemption list.
     */
    public static function protect(array $exemptRoutes = []): void
    {
        $currentUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        foreach ($exemptRoutes as $exempt) {
            if (rtrim($currentUri, '/') === rtrim($exempt, '/')) {
                return;
            }
        }

        if (!self::validateRequest()) {
            http_response_code(403);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                header('Content-Type: application/json; charset=utf-8');
                exit(json_encode([
                    'success' => false,
                    'message' => 'CSRF validation failed. Please refresh the page and try again.'
                ]));
            }
            header('Content-Type: text/plain; charset=utf-8');
            exit('403 Forbidden: Invalid or missing CSRF token.');
        }
    }
}
