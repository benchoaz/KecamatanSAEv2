<?php

namespace App\Http\Middleware;

use App\Services\RateLimitService;
use Closure;

/**
 * Rate Limiting Middleware for WhatsApp Bot API
 * 
 * Limits requests per phone number to prevent spam
 * Default: 10 requests per minute
 */
class RateLimitMiddleware
{
    /**
     * Rate limit configuration
     */
    private const MAX_REQUESTS = 10;     // requests per window
    private const WINDOW_SECONDS = 60;   // 1 minute window

    /**
     * Handle an incoming request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get identifier (phone number, chatId, or IP)
        $identifier = $this->getIdentifier($request);

        $rateLimitService = new RateLimitService();

        // Check if rate limited
        if ($rateLimitService->isRateLimited($identifier, self::MAX_REQUESTS, self::WINDOW_SECONDS)) {
            $retryAfter = $rateLimitService->getRetryAfter($identifier);

            return response()->json([
                'success' => false,
                'error' => 'rate_limited',
                'message' => 'Terlalu banyak permintaan. Silakan coba lagi dalam ' . $retryAfter . ' detik.',
                'retry_after' => $retryAfter,
                'limit' => self::MAX_REQUESTS,
                'window' => self::WINDOW_SECONDS
            ], 429, [
                'Retry-After' => $retryAfter,
                'X-RateLimit-Limit' => self::MAX_REQUESTS,
                'X-RateLimit-Remaining' => 0,
                'X-RateLimit-Reset' => time() + $retryAfter
            ]);
        }

        // Increment request count
        $rateLimitService->increment($identifier);

        // Add rate limit headers to response
        $response = $next($request);

        $remaining = max(0, self::MAX_REQUESTS - $rateLimitService->getCurrentCount($identifier));

        $response->headers->set('X-RateLimit-Limit', self::MAX_REQUESTS);
        $response->headers->set('X-RateLimit-Remaining', $remaining);
        $response->headers->set('X-RateLimit-Reset', time() + self::WINDOW_SECONDS);

        return $response;
    }

    /**
     * Get identifier for rate limiting
     * Priority: phone > chatId > IP address
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    private function getIdentifier($request): string
    {
        // Try to get from JSON body
        $phone = $request->input('phone')
            ?? $request->input('chatId')
            ?? $request->input('from')
            ?? $request->input('identifier');

        if ($phone) {
            // Normalize phone number
            $phone = preg_replace('/[^0-9]/', '', $phone);
            return $phone;
        }

        // Fallback to IP address
        return $request->ip() ?? 'unknown';
    }
}
