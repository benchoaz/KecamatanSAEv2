# Debug WhatsApp Bot
# Jalankan: .\debug-whatsapp-bot.ps1

Write-Host "=== Debug WhatsApp Bot ===" -ForegroundColor Cyan
Write-Host ""

# 1. Cek WAHA
Write-Host "1. Cek WAHA Status..." -ForegroundColor Yellow
try {
    $wahaStatus = Invoke-RestMethod -Uri "http://localhost:3099/api/sessions" -Headers @{"X-Api-Key" = "62a72516dd1b418499d9dd22075ccfa0" }
    Write-Host "   [OK] WAHA berjalan" -ForegroundColor Green
    Write-Host "   Sessions: $($wahaStatus | ConvertTo-Json -Compress)" -ForegroundColor Gray
}
catch {
    Write-Host "   [FAIL] WAHA tidak merespon: $_" -ForegroundColor Red
    exit 1
}

# 2. Cek Webhooks
Write-Host ""
Write-Host "2. Cek Webhooks Terdaftar..." -ForegroundColor Yellow
try {
    # Check session config instead of global webhooks if global returns 404
    $session = Invoke-RestMethod -Uri "http://localhost:3099/api/sessions/default" -Headers @{"X-Api-Key" = "62a72516dd1b418499d9dd22075ccfa0" }
    if ($session.config.webhooks.Count -eq 0) {
        Write-Host "   [FAIL] Tidak ada webhook terdaftar di session 'default'!" -ForegroundColor Red
        Write-Host "   Jalankan: .\setup-webhook-waha.ps1" -ForegroundColor Yellow
    }
    else {
        Write-Host "   [OK] Webhooks terdaftar di session 'default':" -ForegroundColor Green
        foreach ($webhook in $session.config.webhooks) {
            Write-Host "     - $($webhook.url)" -ForegroundColor Gray
            Write-Host "       Events: $($webhook.events -join ', ')" -ForegroundColor Gray
        }
    }
}
catch {
    Write-Host "   [FAIL] Gagal cek webhooks: $_" -ForegroundColor Red
}

# 3. Cek n8n
Write-Host ""
Write-Host "3. Cek n8n Status..." -ForegroundColor Yellow
try {
    Invoke-RestMethod -Uri "http://localhost:5678/healthz" | Out-Null
    Write-Host "   [OK] n8n berjalan" -ForegroundColor Green
}
catch {
    Write-Host "   [FAIL] n8n tidak merespon: $_" -ForegroundColor Red
    exit 1
}

# 4. Cek whatsapp-api
Write-Host ""
Write-Host "4. Cek whatsapp-api Status..." -ForegroundColor Yellow
try {
    Invoke-WebRequest -Uri "http://localhost:8001/api/health" -UseBasicParsing | Out-Null
    Write-Host "   [OK] whatsapp-api berjalan" -ForegroundColor Green
}
catch {
    Write-Host "   [FAIL] whatsapp-api tidak merespon: $_" -ForegroundColor Red
}

# 5. Cek dashboard-kecamatan
Write-Host ""
Write-Host "5. Cek dashboard-kecamatan Status..." -ForegroundColor Yellow
try {
    $dashboardStatus = Invoke-WebRequest -Uri "http://localhost:8000/api/health" -UseBasicParsing -ErrorAction SilentlyContinue
    if ($dashboardStatus.StatusCode -eq 200) {
        Write-Host "   [OK] dashboard-kecamatan berjalan (port 8000)" -ForegroundColor Green
    }
    else {
        throw "Status Code: $($dashboardStatus.StatusCode)"
    }
}
catch {
    try {
        $dashboardStatus = Invoke-WebRequest -Uri "http://localhost:8080/api/health" -UseBasicParsing -ErrorAction SilentlyContinue
        if ($dashboardStatus.StatusCode -eq 200) {
            Write-Host "   [OK] dashboard-kecamatan berjalan (port 8080)" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "   [FAIL] dashboard-kecamatan tidak merespon" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "=== Debug Selesai ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Langkah selanjutnya:" -ForegroundColor Yellow
Write-Host "1. Buka n8n: http://localhost:5678" -ForegroundColor Gray
Write-Host "2. Cek Executions untuk melihat log" -ForegroundColor Gray
Write-Host "3. Pastikan workflow aktif (toggle ON)" -ForegroundColor Gray
Write-Host "4. Kirim pesan ke WhatsApp dan cek Executions" -ForegroundColor Gray
