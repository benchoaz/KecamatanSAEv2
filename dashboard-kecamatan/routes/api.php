<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\Desa\SubmissionController;

Route::apiResource('submissions', SubmissionController::class);
Route::post('submissions/{id}/status', [SubmissionController::class, 'changeStatus']);

// SPJ Template & Draft Routes
Route::get('/spj-template/{id}/download', [\App\Http\Controllers\SpjTemplateController::class, 'downloadDraft'])->name('spj.download');
Route::delete('/spj-template/{id}', [\App\Http\Controllers\SpjTemplateController::class, 'destroy'])->name('spj.destroy');

// Real-time Assistant Routes (SAE Helper)
Route::post('/assistant/predict-docs', [\App\Http\Controllers\Desa\PembangunanController::class, 'predictDocs']);
Route::post('/assistant/estimate-tax', [\App\Http\Controllers\Desa\PembangunanController::class, 'estimateTax']);

// WhatsApp Integration API
Route::post('/inbox/whatsapp', [\App\Http\Controllers\PublicServiceController::class, 'storeFromWhatsapp'])
    ->middleware('api.token')
    ->name('api.inbox.whatsapp');

// WhatsApp FAQ Search API (for bot integration)
Route::get('/faq/search', [\App\Http\Controllers\PublicServiceController::class, 'faqSearch'])
    ->name('api.faq.search');

// WhatsApp Status Check API (for bot integration)
Route::get('/status/check', [\App\Http\Controllers\PublicServiceController::class, 'checkStatus'])
    ->name('api.status.check');

// WhatsApp Reply API (for dashboard to send replies)
Route::post('/reply/send', [\App\Http\Controllers\WhatsAppReplyController::class, 'send'])
    ->middleware('api.token')
    ->name('api.reply.send');

// WhatsApp Bulk Reply API (for announcements)
Route::post('/reply/bulk', [\App\Http\Controllers\WhatsAppReplyController::class, 'sendBulk'])
    ->middleware('api.token')
    ->name('api.reply.bulk');

// WhatsApp Test Connection API
Route::post('/reply/test', [\App\Http\Controllers\WhatsAppReplyController::class, 'testConnection'])
    ->middleware('api.token')
    ->name('api.reply.test');

// WhatsApp Service Status API
Route::get('/reply/status', [\App\Http\Controllers\WhatsAppReplyController::class, 'getStatus'])
    ->name('api.reply.status');

// External API for WhatsApp Bot (UMKM & Jasa)
// Rate limited: 60 requests per minute
Route::prefix('v1/external')->middleware(['api.token', 'throttle:60,1'])->group(function () {
    Route::get('/umkm/search', [\App\Http\Controllers\ExternalApiController::class, 'searchUmkm']);
    Route::get('/jasa/search', [\App\Http\Controllers\ExternalApiController::class, 'searchJasa']);

    Route::get('/faq/search', [\App\Http\Controllers\ExternalApiController::class, 'searchFaq']);
    Route::get('/config', [\App\Http\Controllers\ExternalApiController::class, 'getConfig']);

    // Owner endpoints (require PIN verification)
    Route::post('/owner/verify-pin', [\App\Http\Controllers\ExternalApiController::class, 'verifyOwnerPin']);
    Route::get('/owner/listings', [\App\Http\Controllers\ExternalApiController::class, 'getOwnerListings']);
    Route::post('/owner/toggle-listing', [\App\Http\Controllers\ExternalApiController::class, 'toggleListing']);
});

// WhatsApp Bot API (Dashboard as Brain)
// All business logic handled in dashboard, n8n only routes
use App\Http\Controllers\Api\WhatsappController;

Route::prefix('whatsapp')->middleware(['api.token', 'throttle:60,1'])->group(function () {
    // Main entry point for n8n
    Route::post('/handle', [WhatsappController::class, 'handle'])
        ->name('api.whatsapp.handle');

    // Health check endpoint (no auth required)
    Route::get('/health', [WhatsappController::class, 'health'])
        ->withoutMiddleware(['api.token'])
        ->name('api.whatsapp.health');
});

// Global health check for n8n (compatibility alias)
Route::get('/health', [WhatsappController::class, 'health'])->name('api.health');








