<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenMiddleware
{
    /**
     * Handle an incoming request for API authentication.
     * 
     * Supports both:
     * - Database-stored tokens (with abilities and expiration)
     * - Legacy env-based token (WHATSAPP_API_TOKEN)
     * 
     * @param Request $request
     * @param Closure $next
     * @param string|null $ability Required ability for this endpoint
     */
    public function handle(Request $request, Closure $next, ?string $ability = null): Response
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. No API token provided.'
            ], 401);
        }

        // First, try database token validation
        $apiToken = $this->validateDatabaseToken($token);

        if ($apiToken) {
            // Check if token has required ability
            if ($ability && !$apiToken->can($ability)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Forbidden. Token lacks required ability: ' . $ability
                ], 403);
            }

            // Update last used timestamp
            $apiToken->markAsUsed();

            // Add token to request for controllers
            $request->attributes->set('api_token', $apiToken);

            return $next($request);
        }

        // Fallback to legacy env-based token
        $envToken = env('WHATSAPP_API_TOKEN');
        if (!empty($envToken) && $token === $envToken) {
            // Legacy token works but has no ability restrictions
            return $next($request);
        }

        // Log unauthorized attempt
        \Log::warning('Unauthorized API access attempt', [
            'ip' => $request->ip(),
            'path' => $request->path(),
            'token_prefix' => substr($token, 0, 8) . '...'
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized. Invalid or revoked API token.'
        ], 401);
    }

    /**
     * Validate a token against the database.
     */
    private function validateDatabaseToken(string $plainToken): ?ApiToken
    {
        $hashedToken = ApiToken::hashToken($plainToken);

        $apiToken = ApiToken::where('token', $hashedToken)->first();

        if (!$apiToken) {
            return null;
        }

        // Check if token is valid (not revoked, not expired)
        if (!$apiToken->isValid()) {
            return null;
        }

        return $apiToken;
    }
}
