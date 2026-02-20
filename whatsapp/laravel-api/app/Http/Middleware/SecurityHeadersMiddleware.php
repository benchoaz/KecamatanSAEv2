<?php

namespace App\Http\Middleware;

/**
 * Security Headers Middleware
 * 
 * Adds security headers to all API responses
 */
class SecurityHeadersMiddleware
{
    /**
     * Handle the request
     */
    public function handle($request, $next)
    {
        $response = $next($request);

        // Add security headers
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->header('Content-Security-Policy', "default-src 'self'");

        // Remove server signature
        if (function_exists('header_remove')) {
            header_remove('X-Powered-By');
            header_remove('Server');
        }

        return $response;
    }
}
