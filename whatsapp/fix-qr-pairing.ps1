# WAHA QR Code Pairing Fix Script
# Usage: .\fix-qr-pairing.ps1 [-ClearSessions] [-StartNewSession] [-RecreateContainer] [-ShowQR]

param(
    [Parameter(Mandatory = $false)]
    [switch]$ClearSessions,

    [Parameter(Mandatory = $false)]
    [switch]$StartNewSession,

    [Parameter(Mandatory = $false)]
    [switch]$RecreateContainer,

    [Parameter(Mandatory = $false)]
    [switch]$ShowQR
)

$ErrorActionPreference = "Stop"
$WAHA_CONTAINER = "waha-kecamatan"
$WAHA_URL = "http://localhost:3099"
$API_KEY = "62a72516dd1b418499d9dd22075ccfa0"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WAHA QR Code Pairing Fix Tool" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

if ($ClearSessions) {
    Write-Host "[Clear Sessions] Stopping WAHA container..." -ForegroundColor Yellow
    docker-compose stop waha
    
    Write-Host "[Clear Sessions] Removing session volume..." -ForegroundColor Yellow
    docker volume rm whatsapp_waha_sessions -f 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "  Volume removed successfully" -ForegroundColor Green
    }
    else {
        Write-Host "  Volume may not exist or already removed" -ForegroundColor Yellow
    }
    
    Write-Host "[Clear Sessions] Starting WAHA container..." -ForegroundColor Yellow
    docker-compose up -d waha
    
    Write-Host "[Clear Sessions] Waiting for container to initialize..." -ForegroundColor Yellow
    Start-Sleep -Seconds 15
    
    Write-Host ""
    Write-Host "Sessions cleared! Container restarted." -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "1. Run: .\fix-qr-pairing.ps1 -StartNewSession" -ForegroundColor White
    Write-Host "2. Or access dashboard: http://localhost:3099 (admin/admin123)" -ForegroundColor White
    Write-Host ""
}

if ($StartNewSession) {
    Write-Host "[Start Session] Creating new WhatsApp session..." -ForegroundColor Yellow
    
    $headers = @{ 
        "X-Api-Key"    = $API_KEY
        "Content-Type" = "application/json"
    }
    
    $body = @{
        name   = "default"
        config = @{
            proxy = $null
            noweb = @{
                store = @{
                    enabled  = $true
                    fullSync = $false
                }
            }
        }
    } | ConvertTo-Json -Depth 10
    
    try {
        Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Post -Headers $headers -Body $body -ErrorAction Stop | Out-Null
        Write-Host ""
        Write-Host "Session created successfully!" -ForegroundColor Green
        Write-Host ""
        Write-Host "QR Code should now be available. Check:" -ForegroundColor Cyan
        Write-Host "1. Dashboard: http://localhost:3099" -ForegroundColor White
        Write-Host "2. Console logs: docker logs waha-kecamatan -f" -ForegroundColor White
        Write-Host ""
        Write-Host "Scan the QR code with WhatsApp within 60 seconds!" -ForegroundColor Yellow
        Write-Host ""
        
        # Wait and show QR from logs
        Write-Host "Fetching QR code from logs..." -ForegroundColor Yellow
        Start-Sleep -Seconds 5
        docker logs $WAHA_CONTAINER --tail 100 2>&1 | Select-String -Pattern "█|QR"
        
    }
    catch {
        Write-Host ""
        Write-Host "Failed to create session: $($_.Exception.Message)" -ForegroundColor Red
        Write-Host ""
        if ($_.ErrorDetails.Message) {
            Write-Host "Response: $($_.ErrorDetails.Message)" -ForegroundColor Gray
        }
        Write-Host ""
        Write-Host "Try running: .\fix-qr-pairing.ps1 -ClearSessions first" -ForegroundColor Yellow
    }
    Write-Host ""
}

if ($RecreateContainer) {
    Write-Host "[Recreate] Stopping and removing container..." -ForegroundColor Yellow
    docker-compose down waha
    
    Write-Host "[Recreate] Pulling latest WAHA image..." -ForegroundColor Yellow
    docker-compose pull waha
    
    Write-Host "[Recreate] Creating new container..." -ForegroundColor Yellow
    docker-compose up -d waha
    
    Write-Host "[Recreate] Waiting for container to start..." -ForegroundColor Yellow
    Start-Sleep -Seconds 15
    
    Write-Host ""
    Write-Host "Container recreated!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next: Run .\fix-qr-pairing.ps1 -StartNewSession" -ForegroundColor Cyan
    Write-Host ""
}

if ($ShowQR) {
    Write-Host "[Show QR] Streaming container logs (QR code will appear here)..." -ForegroundColor Yellow
    Write-Host "Press Ctrl+C to stop" -ForegroundColor Gray
    Write-Host ""
    docker logs -f $WAHA_CONTAINER 2>&1
}

if (-not ($ClearSessions -or $StartNewSession -or $RecreateContainer -or $ShowQR)) {
    Write-Host "Usage:" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  .\fix-qr-pairing.ps1 -ClearSessions      # Clear all session data" -ForegroundColor White
    Write-Host "  .\fix-qr-pairing.ps1 -StartNewSession    # Start new pairing session" -ForegroundColor White
    Write-Host "  .\fix-qr-pairing.ps1 -RecreateContainer  # Recreate WAHA container" -ForegroundColor White
    Write-Host "  .\fix-qr-pairing.ps1 -ShowQR             # Show QR in console" -ForegroundColor White
    Write-Host ""
    Write-Host "Typical workflow:" -ForegroundColor Cyan
    Write-Host "  1. Run diagnostic:  .\diagnose-qr-pairing.ps1" -ForegroundColor Gray
    Write-Host "  2. Clear sessions:  .\fix-qr-pairing.ps1 -ClearSessions" -ForegroundColor Gray
    Write-Host "  3. Start session:   .\fix-qr-pairing.ps1 -StartNewSession" -ForegroundColor Gray
    Write-Host "  4. Scan QR code in WhatsApp app" -ForegroundColor Gray
    Write-Host ""
}
