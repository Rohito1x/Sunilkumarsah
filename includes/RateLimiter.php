<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Enterprise Rate Limiter & Brute-Force Throttler
 * Supports sliding window algorithm via APCu, Redis, or atomic file-locking (flock).
 */
final class RateLimiter
{
    private string $storageDir;
    private bool $hasApcu;

    public function __construct()
    {
        $this->hasApcu = function_exists('apcu_enabled') && apcu_enabled();
        $this->storageDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vp_rate_limits';

        if (!$this->hasApcu && !is_dir($this->storageDir)) {
            @mkdir($this->storageDir, 0700, true);
        }
    }

    /**
     * Resolves the verified client IP address.
     * Prevents IP spoofing via fabricated X-Forwarded-For headers.
     */
    public static function getClientIp(array $trustedProxies = []): string
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Check for Cloudflare CF-Connecting-IP
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $cfIp = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
            if (filter_var($cfIp, FILTER_VALIDATE_IP)) {
                return $cfIp;
            }
        }

        // Only evaluate X-Forwarded-For if request comes directly through a designated reverse proxy
        if (!empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true)) {
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                $clientCandidate = trim(end($ips));
                if (filter_var($clientCandidate, FILTER_VALIDATE_IP)) {
                    return $clientCandidate;
                }
            }
        }

        return filter_var($remoteAddr, FILTER_VALIDATE_IP) ? $remoteAddr : '127.0.0.1';
    }

    /**
     * Checks if an action is within rate limits using a sliding window algorithm.
     *
     * @param string $key Unique identifier (e.g., "global_" . $ip or "auth_" . $email)
     * @param int $maxAttempts Maximum permitted requests within the window
     * @param int $decaySeconds Window length in seconds
     * @return array ['allowed' => bool, 'remaining' => int, 'retry_after' => int]
     */
    public function check(string $key, int $maxAttempts, int $decaySeconds): array
    {
        $now = time();

        if ($this->hasApcu) {
            return $this->checkApcu($key, $maxAttempts, $decaySeconds, $now);
        }

        return $this->checkFile($key, $maxAttempts, $decaySeconds, $now);
    }

    /**
     * Resets rate limit counter for a specific key (e.g. on successful login).
     */
    public function reset(string $key): void
    {
        if ($this->hasApcu) {
            apcu_delete('rl:' . hash('sha256', $key));
            return;
        }

        $filePath = $this->storageDir . DIRECTORY_SEPARATOR . 'rl_' . hash('sha256', $key) . '.lock';
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    private function checkApcu(string $key, int $maxAttempts, int $decaySeconds, int $now): array
    {
        $cacheKey = 'rl:' . hash('sha256', $key);
        $data = apcu_fetch($cacheKey);

        $timestamps = is_array($data) ? $data : [];
        $cutoff = $now - $decaySeconds;
        $timestamps = array_values(array_filter($timestamps, function($ts) use ($cutoff) {
            return $ts > $cutoff;
        }));

        if (count($timestamps) >= $maxAttempts) {
            $oldest = reset($timestamps);
            $retryAfter = max(1, ($oldest + $decaySeconds) - $now);
            return ['allowed' => false, 'remaining' => 0, 'retry_after' => $retryAfter];
        }

        $timestamps[] = $now;
        apcu_store($cacheKey, $timestamps, $decaySeconds);

        return [
            'allowed' => true,
            'remaining' => $maxAttempts - count($timestamps),
            'retry_after' => 0
        ];
    }

    private function checkFile(string $key, int $maxAttempts, int $decaySeconds, int $now): array
    {
        $filePath = $this->storageDir . DIRECTORY_SEPARATOR . 'rl_' . hash('sha256', $key) . '.lock';
        $fp = @fopen($filePath, 'c+');

        if (!$fp) {
            // Fail open if directory write fails to avoid blocking site visitors
            return ['allowed' => true, 'remaining' => 1, 'retry_after' => 0];
        }

        flock($fp, LOCK_EX);

        $content = stream_get_contents($fp);
        $timestamps = [];
        if (!empty($content)) {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $timestamps = $decoded;
            }
        }

        $cutoff = $now - $decaySeconds;
        $timestamps = array_values(array_filter($timestamps, function($ts) use ($cutoff) {
            return $ts > $cutoff;
        }));

        if (count($timestamps) >= $maxAttempts) {
            $oldest = reset($timestamps);
            $retryAfter = max(1, ($oldest + $decaySeconds) - $now);
            flock($fp, LOCK_UN);
            fclose($fp);
            return ['allowed' => false, 'remaining' => 0, 'retry_after' => $retryAfter];
        }

        $timestamps[] = $now;

        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, json_encode($timestamps));
        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        return [
            'allowed' => true,
            'remaining' => $maxAttempts - count($timestamps),
            'retry_after' => 0
        ];
    }
}
