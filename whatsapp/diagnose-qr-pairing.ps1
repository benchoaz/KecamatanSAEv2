# WAHA QR Code Pairing Diagnostic Script
# Run this script to diagnose QR code pairing issues

$ErrorActionPreference = "Continue"
$WAHA_CONTAINER = "waha-kecamatan"
$WAHA_URL = "http://localhost:3099"
$API_KEY = "62a72516dd1b418499d9dd22075ccfa0"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WAHA QR Code Pairing Diagnostic" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check container status
Write-Host "[1/7] Checking WAHA container status..." -ForegroundColor Yellow
$containerStatus = docker ps --filter "name=$WAHA_CONTAINER" --format "{{.Status}}"
if ($containerStatus) {
    Write-Host "  Container running: $containerStatus" -ForegroundColor Green
}
else {
    Write-Host "  Container NOT running!" -ForegroundColor Red
    Write-Host "  Starting container..." -ForegroundColor Yellow
    docker-compose up -d waha
    Start-Sleep -Seconds 10
}
Write-Host ""

# Step 2: Check for QR code in logs
Write-Host "[2/7] Checking for QR code in container logs..." -ForegroundColor Yellow
$qrLogs = docker logs $WAHA_CONTAINER --tail 200 2>&1 | Select-String -Pattern "QR|qr|scan"
if ($qrLogs) {
    Write-Host "  QR code related logs found:" -ForegroundColor Green
    $qrLogs | Select-Object -First 10 | ForEach-Object { Write-Host "    $_" -ForegroundColor White }
}
else {
    Write-Host "  No QR code logs found" -ForegroundColor Yellow
}
Write-Host ""

# Step 3: Check existing sessions
Write-Host "[3/7] Checking existing WAHA sessions..." -ForegroundColor Yellow
$headers = @{ "X-Api-Key" = $API_KEY }
try {
    $sessions = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Get -Headers $headers -ErrorAction Stop
    if ($sessions.Count -eq 0) {
        Write-Host "  No existing sessions (ready for new pairing)" -ForegroundColor Green
    }
    else {
        Write-Host "  Found $($sessions.Count) existing session(s):" -ForegroundColor Yellow
        $sessions | ForEach-Object {
            Write-Host "    - Name: $($_.name), Status: $($_.status)" -ForegroundColor White
        }
    }
}
catch {
    Write-Host "  Failed to check sessions: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Step 4: Check session volume
Write-Host "[4/7] Checking waha_sessions volume..." -ForegroundColor Yellow
$volumeData = docker run --rm -v whatsapp_waha_sessions:/data alpine ls -la /data 2>&1
if ($volumeData -and $volumeData -notmatch "total 0") {
    Write-Host "  Volume contents:" -ForegroundColor Green
    $volumeData | ForEach-Object { Write-Host "    $_" -ForegroundColor White }
}
else {
    Write-Host "  Volume is empty (ready for new session)" -ForegroundColor Green
}
Write-Host ""

# Step 5: Test dashboard access
Write-Host "[5/7] Testing dashboard access..." -ForegroundColor Yellow
$basicAuth = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes("admin:admin123"))
$authHeaders = @{ "Authorization" = "Basic $basicAuth" }
try {
    $response = Invoke-WebRequest -Uri "$WAHA_URL/" -Method Get -Headers $authHeaders -ErrorAction Stop -TimeoutSec 10
    Write-Host "  Dashboard accessible (Status: $($response.StatusCode))" -ForegroundColor Green
    Write-Host "  Dashboard URL: http://localhost:3099" -ForegroundColor Cyan
}
catch {
    Write-Host "  Dashboard access failed: $($_.Exception.Message)" -ForegroundColor Red
}
Write-Host ""

# Step 6: Check container health
Write-Host "[6/7] Checking container health..." -ForegroundColor Yellow
$containerInfo = docker inspect $WAHA_CONTAINER 2>$null | ConvertFrom-Json
if ($containerInfo) {
    $restartCount = $containerInfo[0].RestartCount
    Write-Host "  Restart count: $restartCount" -ForegroundColor White
    if ($restartCount -gt 3) {
        Write-Host "  WARNING: Container has restarted $restartCount times (may indicate issues)" -ForegroundColor Yellow
    }
}
else {
    Write-Host "  Could not inspect container" -ForegroundColor Yellow
}
Write-Host ""

# Step 7: Check network connectivity
Write-Host "[7/7] Checking network connectivity..." -ForegroundColor Yellow
try {
    docker exec $WAHA_CONTAINER wget -q --spider web.whatsapp.com 2>&1 | Out-Null
    Write-Host "  Can reach WhatsApp servers" -ForegroundColor Green
}
catch {
    Write-Host "  Cannot reach WhatsApp servers (may be network issue)" -ForegroundColor Yellow
}
Write-Host ""

# Summary
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "DIAGNOSTIC SUMMARY" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Recommended actions based on findings:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. If sessions exist but disconnected:" -ForegroundColor White
Write-Host "   .\fix-qr-pairing.ps1 -ClearSessions" -ForegroundColor Gray
Write-Host ""
Write-Host "2. If no QR in logs:" -ForegroundColor White
Write-Host "   .\fix-qr-pairing.ps1 -StartNewSession" -ForegroundColor Gray
Write-Host ""
Write-Host "3. If container unstable:" -ForegroundColor White
Write-Host "   .\fix-qr-pairing.ps1 -RecreateContainer" -ForegroundColor Gray
Write-Host ""
Write-Host "4. View QR in console:" -ForegroundColor White
Write-Host "   docker logs waha-kecamatan -f" -ForegroundColor Gray
Write-Host ""
Write-Host "5. Access dashboard:" -ForegroundColor White
Write-Host "   http://localhost:3099 (admin/admin123)" -ForegroundColor Gray
Write-Host ""
