# Panduan Implementasi WhatsApp Bot Pelayanan

## Ringkasan

Dokumen ini berisi panduan langkah-demi-langkah untuk mengimplementasikan WhatsApp Bot Pelayanan yang terintegrasi dengan dashboard-kecamatan.

## Prasyarat

- Docker dan Docker Compose terinstall
- Akses ke server/dashboard-kecamatan
- n8n sudah terinstall dan berjalan
- WAHA (WhatsApp API) sudah terinstall dan berjalan
- Akses database dashboard-kecamatan

## Langkah 1: Setup Environment Variables

### 1.1 Generate API Token

Generate API token yang kuat:

```bash
# Linux/Mac
openssl rand -base64 32

# Atau menggunakan PHP
php -r "echo bin2hex(random_bytes(32));"
```

Simpan token ini, akan digunakan di beberapa tempat.

### 1.2 Update Dashboard-Kecamatan .env

Edit file `dashboard-kecamatan/.env`:

```bash
# Tambahkan baris berikut
N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply
WHATSAPP_API_TOKEN=YOUR_GENERATED_TOKEN_HERE
```

### 1.3 Update WhatsApp Laravel API .env

Edit file `whatsapp/laravel-api/.env`:

```bash
# Pastikan konfigurasi berikut ada
DASHBOARD_API_URL=http://dashboard-kecamatan:8000
DASHBOARD_API_TOKEN=YOUR_GENERATED_TOKEN_HERE
```

### 1.4 Update N8N Environment Variables

Di konfigurasi n8n (via UI atau .env):

```bash
DASHBOARD_API_TOKEN=YOUR_GENERATED_TOKEN_HERE
WAHA_URL=http://waha:3000
```

## Langkah 2: Deploy Kode Baru

### 2.1 Copy File ke Server

Copy file-file berikut ke server:

```
dashboard-kecamatan/
├── app/Http/Controllers/
│   └── WhatsAppReplyController.php (NEW)
├── app/Http/Controllers/Kecamatan/
│   └── PelayananController.php (UPDATED)
└── routes/
    └── api.php (UPDATED)

whatsapp/laravel-api/
├── app/Http/Controllers/
│   └── WebhookController.php (UPDATED)
├── app/Services/
│   └── DashboardApiService.php (UPDATED)
└── routes/
    └── api.php (UPDATED)

whatsapp/n8n-workflows/
├── whatsapp-service-bot.json (NEW)
└── dashboard-to-whatsapp-reply.json (NEW)
```

### 2.2 Restart Services

```bash
# Restart dashboard-kecamatan
cd dashboard-kecamatan
docker-compose restart

# Restart whatsapp-api-gateway
cd ../whatsapp
docker-compose restart laravel-api

# Restart n8n (jika perlu)
docker-compose restart n8n
```

## Langkah 3: Import n8n Workflows

### 3.1 Import WhatsApp Service Bot Workflow

1. Buka n8n UI: `http://your-n8n-url:5678`
2. Klik "Import from File"
3. Pilih file `whatsapp/n8n-workflows/whatsapp-service-bot.json`
4. Simpan workflow dengan nama: "WhatsApp Service Bot"
5. Aktifkan workflow (toggle switch)

### 3.2 Import Dashboard to WhatsApp Reply Workflow

1. Klik "Import from File"
2. Pilih file `whatsapp/n8n-workflows/dashboard-to-whatsapp-reply.json`
3. Simpan workflow dengan nama: "Dashboard to WhatsApp Reply"
4. Aktifkan workflow

### 3.3 Configure WAHA Credentials

Untuk node "Send WhatsApp Reply", konfigurasi WAHA credentials:

1. Klik node "Send WhatsApp Reply"
2. Pilih "Credentials" → "Create New"
3. Pilih "Header Auth"
4. Masukkan:
   - Name: `Authorization`
   - Value: `Bearer YOUR_WAHA_SESSION_TOKEN`
5. Simpan credentials

## Langkah 4: Setup WAHA Webhook

### 4.1 Get n8n Webhook URL

Setelah workflow diaktifkan, n8n akan memberikan webhook URL:

```
http://your-n8n-url:5678/webhook/whatsapp-bot
```

### 4.2 Configure WAHA Webhook

```bash
# Via WAHA API
curl -X POST http://waha:3000/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://n8n:5678/webhook/whatsapp-bot",
    "events": ["messages.upsert"],
    "session": "default"
  }'
```

Atau via WAHA UI:
1. Buka WAHA UI: `http://waha-url:3000`
2. Pilih session
3. Go to "Webhooks"
4. Add new webhook dengan URL n8n

## Langkah 5: Test Integration

### 5.1 Test Dashboard API Connection

```bash
curl -X GET http://dashboard-kecamatan:8000/api/reply/status \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

Expected response:
```json
{
  "success": true,
  "service": "whatsapp-reply",
  "configured": true
}
```

### 5.2 Test FAQ Search

```bash
curl -X GET "http://whatsapp-api-gateway:8001/api/faq/search?q=jam%20pelayanan" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### 5.3 Test WhatsApp Reply

```bash
curl -X POST http://dashboard-kecamatan:8000/api/reply/send \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "6281234567890",
    "message": "🧪 Test pesan dari Dashboard Kecamatan. Jika Anda menerima pesan ini, koneksi WhatsApp berhasil!",
    "type": "manual_reply"
  }'
```

### 5.4 Test WhatsApp Bot Commands

Kirim pesan ke nomor WhatsApp bot:

1. **Test Help Command**:
   ```
   /help
   ```

2. **Test FAQ Command**:
   ```
   /faq jam pelayanan
   ```

3. **Test Status Command**:
   ```
   /status YOUR_UUID
   ```

4. **Test Normal Message**:
   ```
   Jam berapa kantor kecamatan buka?
   ```

## Langkah 6: Verify Dashboard Integration

### 6.1 Check Inbox

1. Login ke dashboard-kecamatan
2. Buka menu "Pelayanan" → "Inbox"
3. Pastikan pesan dari WhatsApp muncul

### 6.2 Test Status Update Notification

1. Buka salah satu laporan dari WhatsApp
2. Update status ke "Diproses" atau "Selesai"
3. Pastikan checkbox "Kirim notifikasi WhatsApp" tercentang
4. Simpan
5. Cek WhatsApp, notifikasi harus terkirim

## Langkah 7: Configure FAQ

### 7.1 Add FAQ Entries

1. Login ke dashboard-kecamatan
2. Buka menu "Pelayanan" → "FAQ"
3. Tambah FAQ baru dengan format:
   - Category: Pilih kategori (misal: Administrasi)
   - Keywords: Kata kunci dipisahkan koma (misal: jam, buka, pelayanan)
   - Question: Pertanyaan
   - Answer: Jawaban
   - Is Active: Centang

### 7.2 Test FAQ

Kirim pesan ke WhatsApp dengan kata kunci yang sama:
```
Jam berapa kantor buka?
```

Bot harus merespon dengan jawaban FAQ.

## Troubleshooting

### Issue: Webhook tidak menerima pesan

**Symptoms**: Pesan dikirim ke WhatsApp tapi tidak masuk ke dashboard

**Solutions**:
1. Cek n8n workflow aktif
2. Cek WAHA webhook terkonfigurasi
3. Cek n8n execution logs
4. Test webhook URL: `curl http://n8n:5678/webhook/whatsapp-bot`

### Issue: FAQ tidak ditemukan

**Symptoms**: Bot merespon "Informasi tidak ditemukan"

**Solutions**:
1. Cek FAQ aktif di dashboard
2. Cek keywords sesuai
3. Test FAQ search API langsung
4. Cek n8n FAQ lookup node

### Issue: Notifikasi status tidak terkirim

**Symptoms**: Status diupdate tapi tidak ada notifikasi WhatsApp

**Solutions**:
1. Cek `N8N_REPLY_WEBHOOK_URL` di .env
2. Cek n8n "Dashboard to WhatsApp Reply" workflow aktif
3. Cek WAHA session aktif
4. Cek logs dashboard-kecamatan

### Issue: Error 401/403 Unauthorized

**Symptoms**: API request gagal dengan 401/403

**Solutions**:
1. Pastikan `DASHBOARD_API_TOKEN` sama di semua service
2. Restart semua service setelah mengubah token
3. Cek header Authorization format: `Bearer YOUR_TOKEN`

## Monitoring

### Dashboard Monitoring

Monitor endpoint berikut:

```bash
# Dashboard health
curl http://dashboard-kecamatan:8000/api/health

# WhatsApp API health
curl http://whatsapp-api-gateway:8001/api/health

# WhatsApp reply status
curl http://dashboard-kecamatan:8000/api/reply/status \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### Log Monitoring

Cek logs di:

| Service | Log Location |
|---------|--------------|
| Dashboard-Kecamatan | `storage/logs/laravel.log` |
| WhatsApp API Gateway | `storage/logs/laravel.log` |
| n8n | n8n UI → Executions |
| WAHA | Docker logs: `docker logs waha` |

## Maintenance

### Daily

- Cek n8n workflow status
- Cek WAHA session status
- Monitor error logs

### Weekly

- Review FAQ entries
- Review inbox messages
- Test bot commands

### Monthly

- Update API token (security best practice)
- Review and optimize workflows
- Backup database and configurations

## Rollback Plan

Jika terjadi masalah, rollback dengan langkah berikut:

1. **Disable n8n Workflows**:
   - Nonaktifkan "WhatsApp Service Bot"
   - Nonaktifkan "Dashboard to WhatsApp Reply"

2. **Restore Previous Code**:
   ```bash
   git checkout previous-commit
   docker-compose restart
   ```

3. **Restore Previous .env**:
   ```bash
   # Restore .env backup
   docker-compose restart
   ```

4. **Verify**:
   - Test dashboard functionality
   - Test existing features

## Support

Untuk bantuan lebih lanjut:

1. Cek dokumentasi: `whatsapp/docs/ANALISIS_WHATSAPP_BOT_PELAYANAN.md`
2. Cek konfigurasi: `whatsapp/docs/ENVIRONMENT_CONFIGURATION.md`
3. Cek logs untuk error details
4. Hubungi tim technical support

## Checklist Deployment

- [ ] Generate API token
- [ ] Update dashboard-kecamatan .env
- [ ] Update whatsapp-laravel-api .env
- [ ] Update n8n environment variables
- [ ] Copy new files to server
- [ ] Restart all services
- [ ] Import n8n workflows
- [ ] Configure WAHA credentials
- [ ] Setup WAHA webhook
- [ ] Test API connections
- [ ] Test FAQ search
- [ ] Test WhatsApp reply
- [ ] Test bot commands
- [ ] Verify dashboard integration
- [ ] Configure FAQ entries
- [ ] Test status update notification
- [ ] Document deployment
- [ ] Train users
