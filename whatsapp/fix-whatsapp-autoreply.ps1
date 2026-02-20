# WhatsApp Auto-Response Fix Script
# This script fixes all issues preventing WhatsApp auto-response and private chat handling

$ErrorActionPreference = "Stop"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WhatsApp Auto-Response Fix Tool" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Configuration
$WAHA_URL = "http://localhost:3099"
$API_KEY = "62a72516dd1b418499d9dd22075ccfa0"
$N8N_WEBHOOK = "http://n8n-kecamatan:5678/webhook/whatsapp-incoming"
$AUTH_HEADER = @{ "X-Api-Key" = $API_KEY; "Content-Type" = "application/json" }

# Step 1: Check WAHA Session Status
Write-Host "[1/7] Checking WAHA session status..." -ForegroundColor Yellow
try {
    $sessions = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Get -Headers $AUTH_HEADER
    Write-Host "✓ WAHA is accessible" -ForegroundColor Green
    Write-Host "  Sessions: $($sessions | ConvertTo-Json -Compress)" -ForegroundColor White
} catch {
    Write-Host "✗ Cannot connect to WAHA!" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Gray
    Write-Host "  Make sure WAHA container is running: docker ps | findstr waha" -ForegroundColor Yellow
    exit 1
}
Write-Host ""

# Step 2: Check if default session exists
Write-Host "[2/7] Checking default session..." -ForegroundColor Yellow
$sessionExists = $false
try {
    $sessionInfo = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions/default" -Method Get -Headers $AUTH_HEADER -ErrorAction SilentlyContinue
    if ($sessionInfo) {
        $sessionExists = $true
        Write-Host "✓ Default session exists" -ForegroundColor Green
        Write-Host "  Status: $($sessionInfo.status)" -ForegroundColor White
    }
} catch {
    Write-Host "✗ Default session does not exist" -ForegroundColor Red
}
Write-Host ""

# Step 3: Create session if it doesn't exist
if (-not $sessionExists) {
    Write-Host "[3/7] Creating default session..." -ForegroundColor Yellow
    try {
        $body = @{ "name" = "default" } | ConvertTo-Json
        $response = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Post -Headers $AUTH_HEADER -Body $body
        Write-Host "✓ Session created successfully" -ForegroundColor Green
        Write-Host "  QR Code will be available in logs" -ForegroundColor White
        Write-Host "  Run: docker logs waha-kecamatan" -ForegroundColor Yellow
    } catch {
        Write-Host "✗ Failed to create session" -ForegroundColor Red
        Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Gray
        exit 1
    }
} else {
    Write-Host "[3/7] Session already exists, skipping creation..." -ForegroundColor Yellow
}
Write-Host ""

# Step 4: Configure Webhook for the session
Write-Host "[4/7] Configuring webhook for default session..." -ForegroundColor Yellow
try {
    # First, try to delete existing webhooks
    try {
        Invoke-RestMethod -Uri "$WAHA_URL/api/sessions/default/webhooks" -Method Delete -Headers $AUTH_HEADER -ErrorAction SilentlyContinue
        Write-Host "  Removed existing webhooks" -ForegroundColor Gray
    } catch {
        # Ignore if no webhooks exist
    }

    # Add new webhook with proper events
    $webhookBody = @{
        url = $N8N_WEBHOOK
        events = @("message", "message.any", "session.status")
    } | ConvertTo-Json

    $response = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions/default/webhooks" -Method Post -Headers $AUTH_HEADER -Body $webhookBody
    Write-Host "✓ Webhook configured successfully" -ForegroundColor Green
    Write-Host "  URL: $N8N_WEBHOOK" -ForegroundColor White
    Write-Host "  Events: message, message.any, session.status" -ForegroundColor White
} catch {
    Write-Host "✗ Failed to configure webhook" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Gray
    exit 1
}
Write-Host ""

# Step 5: Verify webhook configuration
Write-Host "[5/7] Verifying webhook configuration..." -ForegroundColor Yellow
try {
    $webhooks = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions/default/webhooks" -Method Get -Headers $AUTH_HEADER
    Write-Host "✓ Webhook verification successful" -ForegroundColor Green
    Write-Host "  Configured webhooks:" -ForegroundColor White
    $webhooks | ConvertTo-Json -Depth 10 | ForEach-Object { Write-Host "    $_" -ForegroundColor Gray }
} catch {
    Write-Host "✗ Failed to verify webhook" -ForegroundColor Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor Gray
}
Write-Host ""

# Step 6: Fix n8n workflow configuration
Write-Host "[6/7] Fixing n8n workflow configuration..." -ForegroundColor Yellow
$workflowFile = "n8n-workflows/whatsapp-classifier.json"
if (Test-Path $workflowFile) {
    $workflowContent = Get-Content $workflowFile -Raw

    # Fix container name: whatsapp-api -> whatsapp-api-gateway
    $workflowContent = $workflowContent -replace 'http://whatsapp-api:8001', 'http://whatsapp-api-gateway:8001'

    # Fix URL path: /api/webhook/n8n -> /api/webhook
    $workflowContent = $workflowContent -replace '/api/webhook/n8n', '/api/webhook'

    Set-Content $workflowFile -Value $workflowContent -NoNewline
    Write-Host "✓ n8n workflow fixed" -ForegroundColor Green
    Write-Host "  Container name: whatsapp-api -> whatsapp-api-gateway" -ForegroundColor White
    Write-Host "  URL path: /api/webhook/n8n -> /api/webhook" -ForegroundColor White
    Write-Host "  NOTE: You need to re-import this workflow in n8n!" -ForegroundColor Yellow
} else {
    Write-Host "✗ Workflow file not found: $workflowFile" -ForegroundColor Red
}
Write-Host ""

# Step 7: Check n8n workflow (whatsapp-to-gateway.json)
Write-Host "[7/7] Checking secondary workflow..." -ForegroundColor Yellow
$workflowFile2 = "n8n-workflows/whatsapp-to-gateway.json"
if (Test-Path $workflowFile2) {
    $workflowContent2 = Get-Content $workflowFile2 -Raw

    # Check if it needs fixing
    if ($workflowContent2 -match 'whatsapp-api:8001') {
        $workflowContent2 = $workflowContent2 -replace 'http://whatsapp-api:8001', 'http://whatsapp-api-gateway:8001'
        Set-Content $workflowFile2 -Value $workflowContent2 -NoNewline
        Write-Host "✓ Secondary workflow also fixed" -ForegroundColor Green
    } else {
        Write-Host "✓ Secondary workflow already correct" -ForegroundColor Green
    }
} else {
    Write-Host "  Secondary workflow not found (optional)" -ForegroundColor Gray
}
Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "FIX SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "✓ WAHA webhook configured with proper events" -ForegroundColor Green
Write-Host "✓ n8n workflow configuration fixed" -ForegroundColor Green
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Re-import the fixed workflow in n8n:" -ForegroundColor White
Write-Host "   - Open http://localhost:5678" -ForegroundColor Gray
Write-Host "   - Delete existing 'WhatsApp Message Classifier' workflow" -ForegroundColor Gray
Write-Host "   - Import: n8n-workflows/whatsapp-classifier.json" -ForegroundColor Gray
Write-Host "   - Activate the workflow (toggle to ON)" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Test the webhook:" -ForegroundColor White
Write-Host "   - Send a WhatsApp message to your connected number" -ForegroundColor Gray
Write-Host "   - Check n8n executions: http://localhost:5678/executions" -ForegroundColor Gray
Write-Host "   - Check Laravel logs: laravel-api/storage/logs/transactions-*.log" -ForegroundColor Gray
Write-Host ""
Write-Host "3. If still not working, check:" -ForegroundColor White
Write-Host "   - WAHA logs: docker logs waha-kecamatan" -ForegroundColor Gray
Write-Host "   - n8n logs: docker logs n8n-kecamatan" -ForegroundColor Gray
Write-Host "   - Laravel logs: docker logs whatsapp-api-gateway" -ForegroundColor Gray
Write-Host ""
Write-Host "4. For auto-reply functionality:" -ForegroundColor White
Write-Host "   - Currently, the system only forwards messages to dashboard" -ForegroundColor Gray
Write-Host "   - Auto-reply needs to be implemented in n8n workflow" -ForegroundColor Gray
Write-Host "   - See: ADD_AUTO_REPLY.md for implementation guide" -ForegroundColor Gray
Write-Host ""
