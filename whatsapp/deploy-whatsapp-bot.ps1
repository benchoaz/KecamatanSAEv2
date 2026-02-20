# WhatsApp Bot Pelayanan Deployment Script (PowerShell)
# Script ini membantu proses deployment WhatsApp Bot Pelayanan

$ErrorActionPreference = "Stop"

Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "WhatsApp Bot Pelayanan Deployment Script" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""

# Get project directory
$ProjectDir = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
Write-Host "Project directory: $ProjectDir" -ForegroundColor Gray

# Check if .env files exist
Write-Host "Checking environment files..." -ForegroundColor Gray

$DashboardEnv = "$ProjectDir\dashboard-kecamatan\.env"
$WhatsAppEnv = "$ProjectDir\whatsapp\laravel-api\.env"

if (-not (Test-Path $DashboardEnv)) {
    Write-Host "✗ File dashboard-kecamatan\.env tidak ditemukan!" -ForegroundColor Red
    exit 1
}
Write-Host "✓ dashboard-kecamatan\.env ditemukan" -ForegroundColor Green

if (-not (Test-Path $WhatsAppEnv)) {
    Write-Host "✗ File whatsapp\laravel-api\.env tidak ditemukan!" -ForegroundColor Red
    exit 1
}
Write-Host "✓ whatsapp\laravel-api\.env ditemukan" -ForegroundColor Green

# Generate API Token if not set
Write-Host "Checking API Token configuration..." -ForegroundColor Gray

$DashboardContent = Get-Content $DashboardEnv -Raw
$WhatsAppContent = Get-Content $WhatsAppEnv -Raw

if ($DashboardContent -match "DASHBOARD_API_TOKEN=(YOUR_GENERATED_TOKEN_HERE|change_this)" -or 
    $WhatsAppContent -match "DASHBOARD_API_TOKEN=(YOUR_GENERATED_TOKEN_HERE|change_this)") {
    
    Write-Host "⚠ API Token belum di-set. Generating new token..." -ForegroundColor Yellow
    
    # Generate random token
    $Bytes = New-Object byte[] 32
    $Rng = [System.Security.Cryptography.RNGCryptoServiceProvider]::Create()
    $Rng.GetBytes($Bytes)
    $ApiToken = [System.Convert]::ToBase64String($Bytes) -replace '[/+=]', ''
    
    # Update dashboard-kecamatan .env
    if ($DashboardContent -match "DASHBOARD_API_TOKEN=") {
        $DashboardContent = $DashboardContent -replace "DASHBOARD_API_TOKEN=.*", "DASHBOARD_API_TOKEN=$ApiToken"
    }
    else {
        $DashboardContent += "`nDASHBOARD_API_TOKEN=$ApiToken"
    }
    Set-Content -Path $DashboardEnv -Value $DashboardContent -NoNewline
    
    # Update whatsapp-laravel-api .env
    if ($WhatsAppContent -match "DASHBOARD_API_TOKEN=") {
        $WhatsAppContent = $WhatsAppContent -replace "DASHBOARD_API_TOKEN=.*", "DASHBOARD_API_TOKEN=$ApiToken"
    }
    else {
        $WhatsAppContent += "`nDASHBOARD_API_TOKEN=$ApiToken"
    }
    Set-Content -Path $WhatsAppEnv -Value $WhatsAppContent -NoNewline
    
    Write-Host "✓ API Token generated: $ApiToken" -ForegroundColor Green
    Write-Host "⚠ Simpan token ini dengan aman: $ApiToken" -ForegroundColor Yellow
}
else {
    Write-Host "✓ API Token sudah di-set" -ForegroundColor Green
}

# Check N8N_REPLY_WEBHOOK_URL
Write-Host "Checking N8N_REPLY_WEBHOOK_URL configuration..." -ForegroundColor Gray

$DashboardContent = Get-Content $DashboardEnv -Raw
if ($DashboardContent -notmatch "N8N_REPLY_WEBHOOK_URL=") {
    Write-Host "⚠ N8N_REPLY_WEBHOOK_URL belum di-set. Adding default value..." -ForegroundColor Yellow
    Add-Content -Path $DashboardEnv -Value "N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply"
    Write-Host "✓ N8N_REPLY_WEBHOOK_URL added" -ForegroundColor Green
}
else {
    Write-Host "✓ N8N_REPLY_WEBHOOK_URL sudah di-set" -ForegroundColor Green
}

# Check if new files exist
Write-Host "Checking new implementation files..." -ForegroundColor Gray

$FilesToCheck = @(
    "$ProjectDir\dashboard-kecamatan\app\Http\Controllers\WhatsAppReplyController.php",
    "$ProjectDir\whatsapp\n8n-workflows\whatsapp-service-bot.json",
    "$ProjectDir\whatsapp\n8n-workflows\dashboard-to-whatsapp-reply.json"
)

foreach ($file in $FilesToCheck) {
    if (Test-Path $file) {
        Write-Host "✓ Found: $(Split-Path $file -Leaf)" -ForegroundColor Green
    }
    else {
        Write-Host "✗ Missing: $(Split-Path $file -Leaf)" -ForegroundColor Red
        exit 1
    }
}

# Ask for confirmation
Write-Host ""
Write-Host "⚠ Script akan melakukan hal berikut:" -ForegroundColor Yellow
Write-Host "  1. Restart dashboard-kecamatan service"
Write-Host "  2. Restart whatsapp-api-gateway service"
Write-Host "  3. Import n8n workflows (manual step required)"
Write-Host ""

$confirmation = Read-Host "Lanjutkan? (y/N)"
if ($confirmation -ne "y" -and $confirmation -ne "Y") {
    Write-Host "ℹ Deployment dibatalkan." -ForegroundColor Gray
    exit 0
}

# Restart services
Write-Host ""
Write-Host "Restarting services..." -ForegroundColor Gray

try {
    Push-Location "$ProjectDir\dashboard-kecamatan"
    docker-compose restart
    Write-Host "✓ dashboard-kecamatan restarted" -ForegroundColor Green
    Pop-Location
}
catch {
    Write-Host "✗ Gagal restart dashboard-kecamatan: $_" -ForegroundColor Red
    exit 1
}

try {
    Push-Location "$ProjectDir\whatsapp"
    docker-compose restart laravel-api
    Write-Host "✓ whatsapp-api-gateway restarted" -ForegroundColor Green
    Pop-Location
}
catch {
    Write-Host "✗ Gagal restart whatsapp-api-gateway: $_" -ForegroundColor Red
    exit 1
}

# Wait for services to be ready
Write-Host ""
Write-Host "Waiting for services to be ready..." -ForegroundColor Gray
Start-Sleep -Seconds 10

# Print next steps
Write-Host ""
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host "✓ Deployment selesai!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "ℹ Langkah selanjutnya:" -ForegroundColor Gray
Write-Host ""
Write-Host "1. Import n8n Workflows:"
Write-Host "   - Buka n8n UI: http://localhost:5678"
Write-Host "   - Import file: whatsapp\n8n-workflows\whatsapp-service-bot.json"
Write-Host "   - Import file: whatsapp\n8n-workflows\dashboard-to-whatsapp-reply.json"
Write-Host "   - Aktifkan kedua workflow"
Write-Host ""
Write-Host "2. Configure WAHA Webhook:"
Write-Host "   - Buka WAHA UI: http://localhost:3000"
Write-Host "   - Setup webhook ke: http://n8n:5678/webhook/whatsapp-bot"
Write-Host ""
Write-Host "3. Test Integration:"
Write-Host "   - Kirim pesan ke nomor WhatsApp bot"
Write-Host "   - Coba command: /help, /faq jam pelayanan, /status {uuid}"
Write-Host ""
Write-Host "4. Configure FAQ:"
Write-Host "   - Login ke dashboard-kecamatan"
Write-Host "   - Buka menu Pelayanan → FAQ"
Write-Host "   - Tambah FAQ entries"
Write-Host ""
Write-Host "Dokumentasi lengkap tersedia di:"
Write-Host "  - whatsapp\docs\IMPLEMENTATION_GUIDE.md"
Write-Host "  - whatsapp\docs\ENVIRONMENT_CONFIGURATION.md"
Write-Host "  - whatsapp\docs\ANALISIS_WHATSAPP_BOT_PELAYANAN.md"
Write-Host ""
