# =====================================================
# WAHA Webhook Fix Script
# =====================================================
# This script fixes the WAHA webhook configuration
# to point to the correct n8n endpoint

$ErrorActionPreference = "Stop"

# Configuration
$WAHA_URL = "http://localhost:3099"
$WAHA_API_KEY = "62a72516dd1b418499d9dd22075ccfa0"
$N8N_WEBHOOK_URL = "http://n8n-kecamatan:5678/webhook/whatsapp-webhook"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WAHA Webhook Fix Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Headers for API calls
$headers = @{
    "Content-Type" = "application/json"
    "X-Api-Key"    = $WAHA_API_KEY
}

# Step 1: Check current webhooks
Write-Host "[1/4] Checking current webhooks..." -ForegroundColor Yellow
try {
    $currentWebhooks = Invoke-RestMethod -Uri "$WAHA_URL/api/webhooks" -Method Get -Headers $headers -ErrorAction SilentlyContinue
    if ($currentWebhooks -and $currentWebhooks.Count -gt 0) {
        Write-Host "  Found $($currentWebhooks.Count) existing webhook(s):" -ForegroundColor White
        foreach ($wh in $currentWebhooks) {
            Write-Host "    - ID: $($wh.id), URL: $($wh.url)" -ForegroundColor Gray
        }
    }
    else {
        Write-Host "  No existing webhooks found" -ForegroundColor White
    }
}
catch {
    Write-Host "  Warning: Could not check existing webhooks: $($_.Exception.Message)" -ForegroundColor Yellow
}

# Step 2: Delete all existing webhooks
Write-Host "[2/4] Removing old webhooks..." -ForegroundColor Yellow
try {
    # Try to delete all webhooks at once
    $deleteResult = Invoke-RestMethod -Uri "$WAHA_URL/api/webhooks" -Method Delete -Headers $headers -ErrorAction SilentlyContinue
    Write-Host "  ✓ Deleted all webhooks" -ForegroundColor Green
}
catch {
    # If bulk delete fails, try one by one
    if ($currentWebhooks -and $currentWebhooks.Count -gt 0) {
        foreach ($wh in $currentWebhooks) {
            try {
                Invoke-RestMethod -Uri "$WAHA_URL/api/webhooks/$($wh.id)" -Method Delete -Headers $headers -ErrorAction SilentlyContinue
                Write-Host "  ✓ Deleted webhook: $($wh.id)" -ForegroundColor Green
            }
            catch {
                Write-Host "  ⚠ Could not delete webhook $($wh.id): $($_.Exception.Message)" -ForegroundColor Yellow
            }
        }
    }
}

# Step 3: Register new webhook
Write-Host "[3/4] Registering new webhook..." -ForegroundColor Yellow
$body = @{
    url    = $N8N_WEBHOOK_URL
    events = @("message")
} | ConvertTo-Json

try {
    $newWebhook = Invoke-RestMethod -Uri "$WAHA_URL/api/webhooks" -Method Post -Headers $headers -Body $body -ErrorAction Stop
    Write-Host "  ✓ Webhook registered successfully!" -ForegroundColor Green
    Write-Host "    URL: $N8N_WEBHOOK_URL" -ForegroundColor White
    Write-Host "    Events: message" -ForegroundColor White
    if ($newWebhook.id) {
        Write-Host "    ID: $($newWebhook.id)" -ForegroundColor White
    }
}
catch {
    Write-Host "  ✗ Failed to register webhook: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "  Response: $($_.Exception.Response)" -ForegroundColor Red
    exit 1
}

# Step 4: Verify webhook registration
Write-Host "[4/4] Verifying webhook registration..." -ForegroundColor Yellow
Start-Sleep -Seconds 2
try {
    $verifyWebhooks = Invoke-RestMethod -Uri "$WAHA_URL/api/webhooks" -Method Get -Headers $headers -ErrorAction Stop
    $found = $false
    foreach ($wh in $verifyWebhooks) {
        if ($wh.url -eq $N8N_WEBHOOK_URL) {
            $found = $true
            Write-Host "  ✓ Webhook verified!" -ForegroundColor Green
            Write-Host "    ID: $($wh.id)" -ForegroundColor White
            Write-Host "    URL: $($wh.url)" -ForegroundColor White
            Write-Host "    Events: $($wh.events -join ', ')" -ForegroundColor White
        }
    }
    if (-not $found) {
        Write-Host "  ✗ Webhook not found in verification!" -ForegroundColor Red
        exit 1
    }
}
catch {
    Write-Host "  ✗ Failed to verify webhook: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WEBHOOK FIX COMPLETE!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor White
Write-Host "1. Make sure n8n workflow is ACTIVE (toggle switch in n8n UI)" -ForegroundColor White
Write-Host "2. Make sure WhatsApp session is connected (scan QR if needed)" -ForegroundColor White
Write-Host "3. Test by sending a message to the WhatsApp bot" -ForegroundColor White
Write-Host ""
Write-Host "To check WhatsApp session:" -ForegroundColor White
Write-Host "  curl -H `"X-Api-Key: $WAHA_API_KEY`" $WAHA_URL/api/sessions" -ForegroundColor Cyan
Write-Host ""
Write-Host "To get QR code for scanning:" -ForegroundColor White
Write-Host "  Open: $WAHA_URL/api/sessions/default/qr" -ForegroundColor Cyan
Write-Host ""
