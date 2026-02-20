<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ComplaintController;

/*
|--------------------------------------------------------------------------
| API Routes - WhatsApp Bot Gateway
|--------------------------------------------------------------------------
|
| These routes handle the WhatsApp bot API gateway for the
| Kecamatan Besuk public services.
|
*/

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => 'whatsapp-api-gateway',
        'version' => '4.0.0',
        'timestamp' => now()
    ]);
});

// User endpoint (requires auth)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| WhatsApp Bot Endpoints
|--------------------------------------------------------------------------
*/

// Main webhook for n8n integration
Route::post('/webhook', [WebhookController::class, 'handleN8nWebhook']);

// Bot configuration
Route::get('/config', [WebhookController::class, 'getConfig']);

// Conversation history
Route::post('/history/add', [WebhookController::class, 'logMessage']);

// Reply sending
Route::post('/reply/send', [WebhookController::class, 'sendReply']);

/*
|--------------------------------------------------------------------------
| Search Endpoints (Public Data)
|--------------------------------------------------------------------------
*/

// FAQ search
Route::get('/faq/search', [WebhookController::class, 'searchFaq']);

// UMKM search (with security filters)
Route::get('/umkm/search', [WebhookController::class, 'searchUmkm']);

// Jasa/Services search (NEW)
Route::get('/jasa/search', [WebhookController::class, 'searchJasa']);

// Loker/Job vacancies search
Route::get('/loker/search', [WebhookController::class, 'searchLoker']);

// Status check (document tracking)
Route::get('/status/check', [WebhookController::class, 'checkStatus']);

// Berkas check (NEW - alias for status check)
Route::get('/cek-berkas', [WebhookController::class, 'checkStatus']);

/*
|--------------------------------------------------------------------------
| Owner Endpoints (PIN Protected)
|--------------------------------------------------------------------------
*/

// Verify owner PIN
Route::post('/owner/verify-pin', [OwnerController::class, 'verifyPin']);

// Toggle listing visibility (requires PIN verification)
Route::post('/owner/toggle-listing', [OwnerController::class, 'toggleListing']);

// Get owner's listings
Route::get('/owner/listings', [OwnerController::class, 'getListings']);

// Request new PIN (for owners who forgot their PIN)
Route::post('/owner/request-pin', [OwnerController::class, 'requestNewPin']);

// Generate PIN for new listing (called by dashboard when approving)
Route::post('/owner/generate-pin', [OwnerController::class, 'generatePinForListing']);

/*
|--------------------------------------------------------------------------
| Complaint Endpoints (Confirmation Based)
|--------------------------------------------------------------------------
*/

// Store pending complaint (awaits confirmation)
Route::post('/complaint/pending', [ComplaintController::class, 'storePending']);

// Confirm and create complaint
Route::post('/complaint/confirm', [ComplaintController::class, 'confirm']);

// Cancel pending complaint
Route::post('/complaint/cancel', [ComplaintController::class, 'cancel']);

// Check pending complaint status
Route::get('/complaint/pending', [ComplaintController::class, 'checkPending']);


/*
|--------------------------------------------------------------------------
| Rate Limit Endpoints
|--------------------------------------------------------------------------
*/

// Check rate limit for a phone number (used by n8n router)
Route::post('/rate-limit/check', function (Request $request) {
    require_once app_path('Services/RateLimitService.php');
    $service = new \App\Services\RateLimitService();

    $phone = $request->input('phone', $request->ip());
    $maxRequests = 10; // requests per minute
    $window = 60; // seconds

    $isRateLimited = $service->isRateLimited($phone, $maxRequests, $window);

    if ($isRateLimited) {
        return response()->json([
            'success' => false,
            'rate_limited' => true,
            'retry_after' => $service->getRetryAfter($phone),
            'message' => 'Too many requests. Please try again later.'
        ], 429);
    }

    // Increment counter
    $service->increment($phone);

    return response()->json([
        'success' => true,
        'rate_limited' => false,
        'message' => 'Request allowed'
    ]);
});

// Rate limit stats (Admin Only)
Route::get('/rate-limit/stats', function () {
    require_once app_path('Services/RateLimitService.php');
    $service = new \App\Services\RateLimitService();
    return response()->json([
        'success' => true,
        'data' => $service->getStats()
    ]);
});

Route::post('/rate-limit/clear', function () {
    require_once app_path('Services/RateLimitService.php');
    $service = new \App\Services\RateLimitService();
    $service->clearAll();
    return response()->json([
        'success' => true,
        'message' => 'Rate limit cache cleared'
    ]);
});

