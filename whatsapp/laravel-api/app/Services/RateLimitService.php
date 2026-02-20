<?php

namespace App\Services;

/**
 * Rate Limiting Service for WhatsApp Bot
 * 
 * Limits requests per phone number to prevent spam
 */
class RateLimitService
{
    private string $cachePath;

    /**
     * Default rate limit configuration
     */
    public const DEFAULT_MAX_REQUESTS = 10;    // requests per window
    public const DEFAULT_WINDOW_SECONDS = 60;  // 1 minute window

    public function __construct()
    {
        $this->cachePath = __DIR__ . '/../../storage/framework/rate_limits';
        $this->ensureDirectoryExists();
    }

    /**
     * Check if identifier is rate limited
     */
    public function isRateLimited(
        string $identifier,
        int $maxRequests = self::DEFAULT_MAX_REQUESTS,
        int $window = self::DEFAULT_WINDOW_SECONDS
    ): bool {
        $key = $this->generateKey($identifier);
        $file = $this->cachePath . '/' . $key . '.json';

        if (!file_exists($file)) {
            return false;
        }

        $data = $this->readCacheFile($file);

        // Reset if window expired
        if (time() - $data['start_time'] > $window) {
            $this->clearCache($identifier);
            return false;
        }

        return $data['count'] >= $maxRequests;
    }

    /**
     * Increment request count for identifier
     */
    public function increment(string $identifier): void
    {
        $key = $this->generateKey($identifier);
        $file = $this->cachePath . '/' . $key . '.json';

        if (file_exists($file)) {
            $data = $this->readCacheFile($file);

            // Check if within window
            if (time() - $data['start_time'] <= self::DEFAULT_WINDOW_SECONDS) {
                $data['count']++;
                $data['last_request'] = time();
            } else {
                // Reset window
                $data = $this->createNewEntry();
            }
        } else {
            $data = $this->createNewEntry();
        }

        $this->writeCacheFile($file, $data);
    }

    /**
     * Get seconds until rate limit resets
     */
    public function getRetryAfter(string $identifier): int
    {
        $key = $this->generateKey($identifier);
        $file = $this->cachePath . '/' . $key . '.json';

        if (!file_exists($file)) {
            return 0;
        }

        $data = $this->readCacheFile($file);
        return max(0, self::DEFAULT_WINDOW_SECONDS - (time() - $data['start_time']));
    }

    /**
     * Get current request count for identifier
     */
    public function getCurrentCount(string $identifier): int
    {
        $key = $this->generateKey($identifier);
        $file = $this->cachePath . '/' . $key . '.json';

        if (!file_exists($file)) {
            return 0;
        }

        $data = $this->readCacheFile($file);

        // Reset if window expired
        if (time() - $data['start_time'] > self::DEFAULT_WINDOW_SECONDS) {
            return 0;
        }

        return $data['count'];
    }

    /**
     * Clear rate limit cache for identifier
     */
    public function clearCache(string $identifier): void
    {
        $key = $this->generateKey($identifier);
        $file = $this->cachePath . '/' . $key . '.json';

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Clear all rate limit caches
     */
    public function clearAll(): void
    {
        $files = glob($this->cachePath . '/*.json');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Get statistics for monitoring
     */
    public function getStats(): array
    {
        $files = glob($this->cachePath . '/*.json');
        $stats = [
            'total_tracked' => count($files),
            'active_limited' => 0,
            'top_requesters' => []
        ];

        foreach ($files as $file) {
            $data = $this->readCacheFile($file);
            if ($data['count'] >= self::DEFAULT_MAX_REQUESTS) {
                $stats['active_limited']++;
            }
            $stats['top_requesters'][] = [
                'key' => basename($file, '.json'),
                'count' => $data['count'],
                'first_request' => date('Y-m-d H:i:s', $data['start_time']),
                'last_request' => date('Y-m-d H:i:s', $data['last_request'] ?? $data['start_time'])
            ];
        }

        // Sort by count descending
        usort($stats['top_requesters'], fn($a, $b) => $b['count'] <=> $a['count']);
        $stats['top_requesters'] = array_slice($stats['top_requesters'], 0, 10);

        return $stats;
    }

    /**
     * Generate cache key from identifier
     */
    private function generateKey(string $identifier): string
    {
        // Normalize phone number
        $identifier = preg_replace('/[^0-9]/', '', $identifier);
        return md5($identifier);
    }

    /**
     * Create new cache entry
     */
    private function createNewEntry(): array
    {
        return [
            'count' => 1,
            'start_time' => time(),
            'last_request' => time()
        ];
    }

    /**
     * Read cache file
     */
    private function readCacheFile(string $file): array
    {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        return $data ?? $this->createNewEntry();
    }

    /**
     * Write cache file
     */
    private function writeCacheFile(string $file, array $data): void
    {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Ensure cache directory exists
     */
    private function ensureDirectoryExists(): void
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }
}
