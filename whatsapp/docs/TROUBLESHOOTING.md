# Troubleshooting WhatsApp Bot - Tidak Ada Respon

Jika bot tidak merespon ketik `/help`, ikuti langkah troubleshooting berikut:

---

## Checklist Troubleshooting

### 1. Cek n8n Workflow

**Langkah:**
1. Buka n8n UI: `http://localhost:5678`
2. Cek apakah workflow "WhatsApp Service Bot" sudah di-import
3. Cek apakah workflow sudah **aktif** (toggle switch di pojok kanan atas)
4. Cek apakah webhook URL sudah terdaftar

**Cara Cek:**
- Klik workflow "WhatsApp Service Bot"
- Lihat node "Webhook WAHA"
- Copy "Production URL" atau "Test URL"
- URL harus seperti: `http://n8n:5678/webhook/whatsapp-bot`

**Jika belum aktif:**
- Klik toggle switch untuk mengaktifkan workflow

---

### 2. Cek WAHA Webhook

**Langkah:**
1. Buka WAHA UI: `http://localhost:3099/dashboard/workers`
2. Cek apakah session "default" aktif
3. Cek apakah webhook sudah terdaftar

**Cara Cek via API:**
```bash
# Cek sessions
curl http://localhost:3099/api/sessions

# Cek webhooks
curl http://localhost:3099/api/webhooks
```

**Expected Response:**
```json
[
  {
    "url": "http://n8n:5678/webhook/whatsapp-bot",
    "events": ["messages.upsert"],
    "session": "default"
  }
]
```

**Jika webhook belum terdaftar:**
```bash
curl -X POST http://localhost:3099/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://n8n:5678/webhook/whatsapp-bot",
    "events": ["messages.upsert"],
    "session": "default"
  }'
```

---

### 3. Cek Services Berjalan

**Cek semua services:**
```bash
# Cek Docker containers
docker ps | grep -E "dashboard|whatsapp|n8n|waha"

# Expected output:
# - dashboard-kecamatan-app-1
# - whatsapp-laravel-api-1
# - n8n-1
# - waha-1
```

**Jika ada service yang tidak berjalan:**
```bash
# Restart service
cd dashboard-kecamatan
docker-compose restart

cd ../whatsapp
docker-compose restart laravel-api

# Atau restart n8n dan waha
docker-compose restart n8n waha
```

---

### 4. Cek n8n Executions

**Langkah:**
1. Buka n8n UI: `http://localhost:5678`
2. Klik "Executions" di menu sidebar
3. Lihat apakah ada eksekusi workflow terbaru
4. Klik eksekusi untuk melihat detail

**Cek Error:**
- Jika ada error, lihat node mana yang gagal
- Cek error message untuk troubleshooting

---

### 5. Cek Logs

**Cek Dashboard Logs:**
```bash
docker logs dashboard-kecamatan-app-1 --tail 50 -f
```

**Cek WhatsApp API Logs:**
```bash
docker logs whatsapp-laravel-api-1 --tail 50 -f
```

**Cek n8n Logs:**
```bash
docker logs n8n-1 --tail 50 -f
```

**Cek WAHA Logs:**
```bash
docker logs waha-1 --tail 50 -f
```

---

### 6. Test API Connections

**Test Dashboard API:**
```bash
curl http://localhost:8080/api/health

# Expected response:
# {"status":"healthy","service":"dashboard-kecamatan","timestamp":"..."}
```

**Test WhatsApp API Gateway:**
```bash
curl http://localhost:8001/api/health

# Expected response:
# {"status":"healthy","service":"whatsapp-api-gateway","timestamp":"..."}
```

**Test WhatsApp Reply Status:**
```bash
curl -X GET http://localhost:8080/api/reply/status \
  -H "Authorization: Bearer ENon4M92iOxQAF9Rl/nFIjWDub9E2887uh2guKtITp4="

# Expected response:
# {"success":true,"service":"whatsapp-reply","configured":true,...}
```

---

### 7. Test n8n Webhook Directly

**Test Webhook URL:**
```bash
curl -X POST http://localhost:5678/webhook/whatsapp-bot \
  -H "Content-Type: application/json" \
  -d '{
    "body": {
      "from": "6281234567890",
      "text": "/help",
      "notifyName": "Test User"
    }
  }'
```

**Expected Response:**
```json
{
  "status": "success",
  "message": "Message processed"
}
```

**Jika error:**
- Cek n8n executions untuk detail error
- Cek n8n logs

---

### 8. Test WAHA Send Text Directly

**Test WAHA API:**
```bash
curl -X POST http://localhost:3099/api/sendText \
  -H "Content-Type: application/json" \
  -d '{
    "chatId": "6281234567890@c.us",
    "text": "Test message from WAHA",
    "session": "default"
  }'
```

**Expected Response:**
```json
{
  "key": {
    "remoteJid": "6281234567890@c.us",
    "fromMe": true,
    "id": "..."
  },
  "message": {...}
}
```

**Jika error:**
- Cek WAHA session aktif
- Cek WAHA logs

---

### 9. Cek Environment Variables

**Cek dashboard-kecamatan/.env:**
```bash
cat dashboard-kecamatan/.env | grep -E "WHATSAPP|N8N"
```

**Expected:**
```
WHATSAPP_API_TOKEN=ENon4M92iOxQAF9Rl/nFIjWDub9E2887uh2guKtITp4=
N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply
```

**Cek whatsapp/laravel-api/.env:**
```bash
cat whatsapp/laravel-api/.env | grep -E "DASHBOARD"
```

**Expected:**
```
DASHBOARD_API_URL=http://host.docker.internal:8080
DASHBOARD_API_TOKEN=ENon4M92iOxQAF9Rl/nFIjWDub9E2887uh2guKtITp4=
```

---

### 10. Restart Services

**Restart semua services:**
```bash
# Restart dashboard-kecamatan
cd dashboard-kecamatan
docker-compose restart

# Restart whatsapp
cd ../whatsapp
docker-compose restart laravel-api

# Restart n8n
docker-compose restart n8n

# Restart waha
docker-compose restart waha

# Tunggu 10-30 detik
sleep 30
```

---

## Common Issues & Solutions

### Issue 1: n8n Workflow Tidak Aktif

**Symptoms:** Tidak ada eksekusi di n8n executions

**Solution:**
1. Buka n8n UI
2. Klik workflow "WhatsApp Service Bot"
3. Klik toggle switch di pojok kanan atas untuk mengaktifkan

---

### Issue 2: WAHA Webhook Tidak Terdaftar

**Symptoms:** Pesan masuk tapi tidak diteruskan ke n8n

**Solution:**
```bash
curl -X POST http://localhost:3099/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://n8n:5678/webhook/whatsapp-bot",
    "events": ["messages.upsert"],
    "session": "default"
  }'
```

---

### Issue 3: WAHA Session Tidak Aktif

**Symptoms:** Error "Session not found" di WAHA

**Solution:**
1. Buka WAHA UI: `http://localhost:3099/dashboard/workers`
2. Cek apakah session "default" aktif
3. Jika tidak, buat session baru atau scan QR code

---

### Issue 4: Network Issue (Docker)

**Symptoms:** n8n tidak bisa mengakses WAHA atau sebaliknya

**Solution:**
1. Pastikan semua services di network yang sama
2. Cek docker network:
```bash
docker network inspect app-network
```

---

### Issue 5: API Token Mismatch

**Symptoms:** Error 401/403 Unauthorized

**Solution:**
1. Pastikan `DASHBOARD_API_TOKEN` sama di semua service
2. Restart semua service setelah mengubah token

---

## Quick Fix Script

Jalankan script berikut untuk quick check:

```bash
#!/bin/bash

echo "=== Checking Services ==="
docker ps | grep -E "dashboard|whatsapp|n8n|waha"

echo ""
echo "=== Checking WAHA Sessions ==="
curl -s http://localhost:3099/api/sessions | jq .

echo ""
echo "=== Checking WAHA Webhooks ==="
curl -s http://localhost:3099/api/webhooks | jq .

echo ""
echo "=== Checking Dashboard API ==="
curl -s http://localhost:8080/api/health | jq .

echo ""
echo "=== Checking WhatsApp API ==="
curl -s http://localhost:8001/api/health | jq .

echo ""
echo "=== Checking WhatsApp Reply Status ==="
curl -s -X GET http://localhost:8080/api/reply/status \
  -H "Authorization: Bearer ENon4M92iOxQAF9Rl/nFIjWDub9E2887uh2guKtITp4=" | jq .
```

---

## Next Steps

Setelah troubleshooting:

1. **Jika semua checks pass:**
   - Kirim pesan test ke WhatsApp bot
   - Cek n8n executions untuk melihat eksekusi
   - Cek logs untuk detail

2. **Jika masih tidak berfungsi:**
   - Kirim error message dari logs
   - Kirim screenshot n8n executions
   - Kirim screenshot WAHA dashboard

---

## Support

Jika masih mengalami masalah:
- Cek logs: `docker logs <container-name> --tail 100`
- Cek n8n executions: `http://localhost:5678/executions`
- Cek WAHA dashboard: `http://localhost:3099/dashboard/workers`
