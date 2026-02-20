# =====================================================
# WAHA Webhook Diagnostic Script
# =====================================================
# This script diagnoses why WAHA webhook is not triggering
# when a message is sent to the WhatsApp bot

$ErrorActionPreference = "Continue"

# Configuration
$WAHA_URL = "http://localhost:3099"
$WAHA_API_KEY = "62a72516dd1b418499d9dd22075ccfa0"
$N8N_CONTAINER = "n8n-kecamatan"
$WAHA_CONTAINER = "waha-kecamatan"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WAHA Webhook Diagnostic Tool" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check if WAHA container is running
Write-Host "[1/8] Checking WAHA container status..." -ForegroundColor Yellow
$wahaStatus = docker ps --filter "name=$WAHA_CONTAINER" --format "{{.Status}}"
if ($wahaStatus) {
    Write-Host "  ✓ WAHA container is running: $wahaStatus" -ForegroundColor Green
}
else {
    Write-Host "  ✗ WAHA container is NOT running!" -ForegroundColor Red
    Write-Host "  Run: docker-compose up -d waha" -ForegroundColor White
    exit 1
}

# Step 2: Check if n8n container is running
Write-Host "[2/8] Checking n8n container status..." -ForegroundColor Yellow
$n8nStatus = docker ps --filter "name=$N8N_CONTAINER" --format "{{.Status}}"
if ($n8nStatus) {
    Write-Host "  ✓ n8n container is running: $n8nStatus" -ForegroundColor Green
}
else {
    Write-Host "  ✗ n8n container is NOT running!" -ForegroundColor Red
    Write-Host "  Run: docker-compose up -d n8n" -ForegroundColor White
    exit 1
}

# Step 3: Check WAHA session status
Write-Host "[3/8] Checking WAHA session status..." -ForegroundColor Yellow
try {
    $headers = @{ "X-Api-Key" = $WAHA_API_KEY }
    $sessions = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Get -Headers $headers -ErrorAction SilentlyContinue
    if ($sessions) {
        $defaultSession = $sessions | Where-Object { $_.name -eq "default" }
        if ($defaultSession) {
            Write-Host "  ✓ Default session found" -ForegroundColor Green
            Write-Host "    Status: $($defaultSession.status)" -ForegroundColor White
            if ($defaultSession.status -ne "WORKING" -and $defaultSession.status -ne "CONNECTED") {
                Write-Host "  ⚠ Session is not connected! Status: $($defaultSession.status)" -ForegroundColor Yellow
                Write-Host "  Please scan QR code: $WAHA_URL/api/sessions/default/qr" -ForegroundColor White
            }
        }
        else {
            Write-Host "  ✗ No 'default' session found!" -ForegroundColor Red
        }
    }
}
catch {
    Write-Host "  ✗ Failed to check sessions: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 4: Check registered webhooks in WAHA
Write-Host "[4/8] Checking registered webhooks in WAHA..." -ForegroundColor Yellow
try {
    $webhooks = Invoke-RestMethod -Uri "$WAHA_URL/api/webhooks" -Method Get -Headers $headers -ErrorAction SilentlyContinue
    if ($webhooks -and $webhooks.Count -gt 0) {
        Write-Host "  Found $($webhooks.Count) webhook(s):" -ForegroundColor Green
        foreach ($wh in $webhooks) {
            Write-Host "    - ID: $($wh.id)" -ForegroundColor White
            Write-Host "      URL: $($wh.url)" -ForegroundColor White
            Write-Host "      Events: $($wh.events -join ', ')" -ForegroundColor White
        }
        
        # Check if webhook URL is correct
        $correctUrl = $webhooks | Where-Object { 
            $_.url -like "*n8n*" -or $_.url -like "*whatsapp-webhook*"
        }
        if (-not $correctUrl) {
            Write-Host "  ⚠ No webhook pointing to n8n found!" -ForegroundColor Yellow
            Write-Host "  Expected URL like: http://n8n-kecamatan:5678/webhook/whatsapp-webhook" -ForegroundColor White
        }
    }
    else {
        Write-Host "  ✗ NO webhooks registered in WAHA!" -ForegroundColor Red
        Write-Host "  This is the likely cause of the issue!" -ForegroundColor Yellow
    }
}
catch {
    Write-Host "  ✗ Failed to check webhooks: $($_.Exception.Message)" -ForegroundColor Red
}

# Step 5: Check n8n workflow status
Write-Host "[5/8] Checking n8n workflow status..." -ForegroundColor Yellow
try {
    $n8nWorkflows = docker exec $N8N_CONTAINER n8n list:workflow 2>&1
    if ($n8nWorkflows) {
        Write-Host "  Workflows found:" -ForegroundColor Green
        Write-Host $n8nWorkflows -ForegroundColor White
    }
}
catch {
    Write-Host "  Could not list workflows via CLI, checking via API..." -ForegroundColor Yellow
}

# Step 6: Test connectivity from WAHA to n8n
Write-Host "[6/8] Testing connectivity from WAHA to n8n..." -ForegroundColor Yellow
$testConnectivity = docker exec $WAHA_CONTAINER sh -c "wget -q -O- http://$N8N_CONTAINER`:5678/healthz 2>&1" 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "  ✓ WAHA can reach n8n" -ForegroundColor Green
}
else {
    Write-Host "  ✗ WAHA CANNOT reach n8n!" -ForegroundColor Red
    Write-Host "  Error: $testConnectivity" -ForegroundColor White
    Write-Host "  Check if both containers are on the same Docker network" -ForegroundColor Yellow
}

# Step 7: Test webhook endpoint directly
Write-Host "[7/8] Testing n8n webhook endpoint..." -ForegroundColor Yellow
try {
    $testBody = @{
        from   = "6281234567890@c.us"
        body   = "TEST MESSAGE"
        fromMe = $false
    } | ConvertTo-Json
    
    $testResult = Invoke-RestMethod -Uri "http://localhost:5678/webhook/whatsapp-webhook" `
        -Method Post `
        -ContentType "application/json" `
        -Body $testBody `
        -ErrorAction SilentlyContinue
    
    if ($testResult) {
        Write-Host "  ✓ n8n webhook endpoint is responding" -ForegroundColor Green
        Write-Host "  Response: $($testResult | ConvertTo-Json -Compress)" -ForegroundColor White
    }
}
catch {
    Write-Host "  ⚠ Could not test webhook endpoint: $($_.Exception.Message)" -ForegroundColor Yellow
}

# Step 8: Check recent WAHA logs
Write-Host "[8/8] Checking recent WAHA logs for webhook activity..." -ForegroundColor Yellow
$logs = docker logs $WAHA_CONTAINER --tail 30 2>&1
$webhookLogs = $logs | Select-String -Pattern "webhook|hook|POST|send" -Context 0, 1
if ($webhookLogs) {
    Write-Host "  Recent webhook-related logs:" -ForegroundColor White
    Write-Host $webhookLogs -ForegroundColor Gray
}
else {
    Write-Host "  No recent webhook activity found in logs" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "DIAGNOSIS SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# Provide fix recommendations
Write-Host ""
Write-Host "RECOMMENDED FIXES:" -ForegroundColor Green
Write-Host ""
Write-Host "1. If no webhooks registered, run this command to register:" -ForegroundColor White
Write-Host @"
   curl -X POST $WAHA_URL/api/webhooks `
     -H "Content-Type: application/json" `
     -H "X-Api-Key: $WAHA_API_KEY" `
     -d '{
       "url": "http://n8n-kecamatan:5678/webhook/whatsapp-webhook",
       "events": ["message"]
     }'
"@ -ForegroundColor Cyan
Write-Host ""
Write-Host "2. If webhook URL is incorrect, delete and recreate:" -ForegroundColor White
Write-Host "   curl -X DELETE `"$WAHA_URL/api/webhooks`" -H `"X-Api-Key: $WAHA_API_KEY`"" -ForegroundColor Cyan
Write-Host ""
Write-Host "3. Verify n8n workflow is ACTIVE (not just saved)" -ForegroundColor White
Write-Host "   Open n8n UI at: http://localhost:5678" -ForegroundColor Cyan
Write-Host ""
Write-Host "4. Check if WhatsApp session is connected:" -ForegroundColor White
Write-Host "   Open: $WAHA_URL/api/sessions/default/qr" -ForegroundColor Cyan
Write-Host ""
