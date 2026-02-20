# N8N Workflow Configuration Guide

## Critical Issue: API URL Corrections

All sub-workflows (wf-umkm, wf-jasa, wf-loker, wf-status, wf-faq) currently use INCORRECT API URLs.

### Current vs. Correct URLs

| Workflow | Current URL (❌ WRONG) | Correct URL (✅ RIGHT) |
|----------|----------------------|----------------------|
| wf-umkm | http://whatsapp-api:8001/api/umkm/search | http://whatsapp-api:8001/api/v1/external/umkm/search |
| wf-jasa | http://whatsapp-api:8001/api/jasa/search | http://whatsapp-api:8001/api/v1/external/jasa/search |
| wf-loker | http://whatsapp-api:8001/api/loker/search | http://whatsapp-api:8001/api/v1/external/loker/search |
| wf-status | http://whatsapp-api:8001/api/status/check | ✅ CORRECT |
| wf-faq | http://whatsapp-api:8001/api/faq/search | http://whatsapp-api:8001/api/v1/external/faq/search |

## Solution Options

### Option A: Update Workflows (Recommended)
Update the JSON files before importing to n8n. I can create corrected versions.

### Option B: Add Laravel API Gateway Routes
Add the old routes as aliases in `whatsapp/laravel-api/routes/api.php` that proxy to dashboard:

```php
// Proxy routes for backward compatibility
Route::get('/api/umkm/search', function(Request $request) {
    return app(\GuzzleHttp\Client::class)->get(
        'http://dashboard-kecamatan/api/v1/external/umkm/search',
        ['query' => $request->query()]
    );
});
// ... same for jasa, loker, faq
```

### Option C: Update Dashboard Routes
Change dashboard routes from `/api/v1/external/*` to `/api/*` (NOT recommended - breaks API versioning)

## Recommendation

**Use Option A** - Let me create corrected workflow files with proper URLs. This is cleanest solution.

Shall I proceed with creating corrected workflow files?
