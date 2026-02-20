# Setup WAHA Webhook untuk n8n
# Jalankan: .\setup-webhook-waha.ps1

$wahaUrl = "http://localhost:3099"
$apiKey = "62a72516dd1b418499d9dd22075ccfa0"
$webhookUrl = "http://n8n-kecamatan:5678/webhook/whatsapp-bot"

Write-Host "=== Setup WAHA Webhook (Session-Based) ===" -ForegroundColor Cyan
Write-Host ""

# Konfigurasi session 'default' dengan webhook
Write-Host "1. Mengupdate konfigurasi session 'default'..." -ForegroundColor Yellow

$jsonBody = @"
{
    "name": "default",
    "config": {
        "webhooks": [
            {
                "url": "$webhookUrl",
                "events": ["message", "message.any", "messages.upsert"]
            }
        ]
    }
}
"@

$headers = @{
    "X-Api-Key"    = $apiKey
    "Content-Type" = "application/json"
}

try {
    Invoke-RestMethod -Uri "$wahaUrl/api/sessions" -Method Post -Headers $headers -Body $jsonBody
    Write-Host "   [OK] Konfigurasi session berhasil diupdate" -ForegroundColor Green
}
catch {
    Write-Host "   [FAIL] Gagal update session: $_" -ForegroundColor Red
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        Write-Host "   Response Body: $($reader.ReadToEnd())" -ForegroundColor Red
    }
    exit 1
}

# Verifikasi webhook
Write-Host ""
Write-Host "2. Verifikasi webhook via Session Info..." -ForegroundColor Yellow
try {
    $session = Invoke-RestMethod -Uri "$wahaUrl/api/sessions/default" -Headers @{"X-Api-Key" = $apiKey }
    Write-Host "   Status Session: $($session.status)" -ForegroundColor Gray
    
    $found = $false
    foreach ($webhook in $session.config.webhooks) {
        if ($webhook.url -like "*whatsapp-bot") {
            Write-Host "   [OK] $($webhook.url)" -ForegroundColor Green
            Write-Host "     Events: $($webhook.events -join ', ')" -ForegroundColor Gray
            $found = $true
        }
    }
    if (-not $found) {
        Write-Host "   [!] Webhook target tidak ditemukan di session config" -ForegroundColor Yellow
    }
}
catch {
    Write-Host "   [FAIL] Gagal verifikasi: $_" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Setup Selesai ===" -ForegroundColor Cyan
