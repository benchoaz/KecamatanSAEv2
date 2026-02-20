# Panduan Deployment Manual - WhatsApp Bot Pelayanan

Panduan langkah-demi-langkah untuk deployment manual WhatsApp Bot Pelayanan.

---

## Prasyarat

Sebelum memulai, pastikan:
- [ ] Docker dan Docker Compose terinstall
- [ ] Akses ke server/dashboard-kecamatan
- [ ] n8n sudah terinstall dan berjalan
- [ ] WAHA (WhatsApp API) sudah terinstall dan berjalan
- [ ] Akses database dashboard-kecamatan

---

## Langkah 1: Generate API Token

### 1.1 Generate Token

Pilih salah satu metode berikut:

**Metode 1: OpenSSL (Linux/Mac)**
```bash
openssl rand -base64 32
```

**Metode 2: PHP**
```bash
php -r "echo bin2hex(random_bytes(32));"
```

**Metode 3: Node.js**
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('base64'))"
```

**Contoh output:**
```
aB3xY9zK2mN4pQ7rS1tU5vW8xY0zA3bC6dE9fG2hJ5kM8nP1qR4sT7uV0wY3zA6b
```

### 1.2 Simpan Token

Simpan token yang dihasilkan, akan digunakan di beberapa tempat:
- dashboard-kecamatan/.env
- whatsapp/laravel-api/.env
- n8n environment variables

---

## Langkah 2: Update Environment Variables

### 2.1 Update dashboard-kecamatan/.env

Buka file `dashboard-kecamatan/.env` dan tambahkan/edit baris berikut:

```bash
# WhatsApp Integration
N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply
DASHBOARD_API_TOKEN=aB3xY9zK2mN4pQ7rS1tU5vW8xY0zA3bC6dE9fG2hJ5kM8nP1qR4sT7uV0wY3zA6b
```

### 2.2 Update whatsapp/laravel-api/.env

Buka file `whatsapp/laravel-api/.env` dan tambahkan/edit baris berikut:

```bash
# Dashboard API Configuration
DASHBOARD_API_URL=http://dashboard-kecamatan:8000
DASHBOARD_API_TOKEN=aB3xY9zK2mN4pQ7rS1tU5vW8xY0zA3bC6dE9fG2hJ5kM8nP1qR4sT7uV0wY3zA6b

# App Configuration
APP_DEBUG=false
APP_ENV=production
```

### 2.3 Update n8n Environment Variables

**Via n8n UI:**
1. Buka n8n UI: `http://localhost:5678`
2. Klik "Settings" → "Environment Variables"
3. Tambahkan:
   - `DASHBOARD_API_TOKEN=aB3xY9zK2mN4pQ7rS1tU5vW8xY0zA3bC6dE9fG2hJ5kM8nP1qR4sT7uV0wY3zA6b`
   - `WAHA_URL=http://waha:3000`

**Via .env (jika n8n dijalankan dengan Docker):**
```bash
DASHBOARD_API_TOKEN=aB3xY9zK2mN4pQ7rS1tU5vW8xY0zA3bC6dE9fG2hJ5kM8nP1qR4sT7uV0wY3zA6b
WAHA_URL=http://waha:3000
```

---

## Langkah 3: Copy File ke Server

### 3.1 Copy Dashboard-Kecamatan Files

Copy file-file berikut ke server:

```bash
# WhatsAppReplyController (BARU)
cp dashboard-kecamatan/app/Http/Controllers/WhatsAppReplyController.php \
   /path/to/server/dashboard-kecamatan/app/Http/Controllers/

# PelayananController (UPDATE - backup dulu)
cp /path/to/server/dashboard-kecamatan/app/Http/Controllers/Kecamatan/PelayananController.php \
   /path/to/server/dashboard-kecamatan/app/Http/Controllers/Kecamatan/PelayananController.php.backup

cp dashboard-kecamatan/app/Http/Controllers/Kecamatan/PelayananController.php \
   /path/to/server/dashboard-kecamatan/app/Http/Controllers/Kecamatan/

# api.php (UPDATE - backup dulu)
cp /path/to/server/dashboard-kecamatan/routes/api.php \
   /path/to/server/dashboard-kecamatan/routes/api.php.backup

cp dashboard-kecamatan/routes/api.php \
   /path/to/server/dashboard-kecamatan/routes/
```

### 3.2 Copy WhatsApp Laravel API Files

```bash
# WebhookController (UPDATE - backup dulu)
cp /path/to/server/whatsapp/laravel-api/app/Http/Controllers/WebhookController.php \
   /path/to/server/whatsapp/laravel-api/app/Http/Controllers/WebhookController.php.backup

cp whatsapp/laravel-api/app/Http/Controllers/WebhookController.php \
   /path/to/server/whatsapp/laravel-api/app/Http/Controllers/

# DashboardApiService (UPDATE - backup dulu)
cp /path/to/server/whatsapp/laravel-api/app/Services/DashboardApiService.php \
   /path/to/server/whatsapp/laravel-api/app/Services/DashboardApiService.php.backup

cp whatsapp/laravel-api/app/Services/DashboardApiService.php \
   /path/to/server/whatsapp/laravel-api/app/Services/

# api.php (UPDATE - backup dulu)
cp /path/to/server/whatsapp/laravel-api/routes/api.php \
   /path/to/server/whatsapp/laravel-api/routes/api.php.backup

cp whatsapp/laravel-api/routes/api.php \
   /path/to/server/whatsapp/laravel-api/routes/
```

### 3.3 Copy n8n Workflows

```bash
# Copy workflow files
cp whatsapp/n8n-workflows/whatsapp-service-bot.json \
   /path/to/n8n/workflows/

cp whatsapp/n8n-workflows/dashboard-to-whatsapp-reply.json \
   /path/to/n8n/workflows/
```

---

## Langkah 4: Restart Services

### 4.1 Restart Dashboard-Kecamatan

```bash
cd /path/to/server/dashboard-kecamatan
docker-compose restart

# Atau restart specific service
docker-compose restart app
```

### 4.2 Restart WhatsApp Laravel API

```bash
cd /path/to/server/whatsapp
docker-compose restart laravel-api

# Atau restart specific service
docker-compose restart whatsapp-api-gateway
```

### 4.3 Restart n8n (jika perlu)

```bash
cd /path/to/n8n
docker-compose restart n8n
```

### 4.4 Verify Services Running

```bash
# Cek semua services
docker ps | grep -E "dashboard|whatsapp|n8n|waha"

# Cek logs
docker logs dashboard-kecamatan-app-1 --tail 50
docker logs whatsapp-laravel-api-1 --tail 50
docker logs n8n-1 --tail 50
docker logs waha-1 --tail 50
```

---

## Langkah 5: Import n8n Workflows

### 5.1 Import WhatsApp Service Bot Workflow

1. Buka n8n UI: `http://localhost:5678`
2. Login jika diperlukan
3. Klik "Import from File" (ikon file di pojok kanan atas)
4. Pilih file: `whatsapp/n8n-workflows/whatsapp-service-bot.json`
5. Klik "Import"
6. Simpan workflow dengan nama: "WhatsApp Service Bot"
7. Aktifkan workflow (toggle switch di pojok kanan atas)

### 5.2 Import Dashboard to WhatsApp Reply Workflow

1. Klik "Import from File"
2. Pilih file: `whatsapp/n8n-workflows/dashboard-to-whatsapp-reply.json`
3. Klik "Import"
4. Simpan workflow dengan nama: "Dashboard to WhatsApp Reply"
5. Aktifkan workflow

### 5.3 Configure WAHA Credentials

Untuk node "Send WhatsApp Reply" di kedua workflow:

1. Klik node "Send WhatsApp Reply"
2. Pilih "Credentials" → "Create New"
3. Pilih "Header Auth"
4. Masukkan:
   - Name: `Authorization`
   - Value: `Bearer YOUR_WAHA_SESSION_TOKEN`
5. Klik "Save"
6. Pilih credentials yang baru dibuat

**Cara mendapatkan WAHA Session Token:**

```bash
# Via WAHA API
curl http://waha:3000/api/sessions

# Atau via WAHA UI
# Buka WAHA UI → Sessions → Copy session token
```

### 5.4 Get Webhook URLs

Setelah workflow diaktifkan, catat webhook URL:

1. Klik node "Webhook WAHA" di "WhatsApp Service Bot" workflow
2. Copy "Production URL" atau "Test URL"
3. URL akan seperti: `http://n8n:5678/webhook/whatsapp-bot`

---

## Langkah 6: Setup WAHA Webhook

### 6.1 Via WAHA API

```bash
curl -X POST http://waha:3000/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://n8n:5678/webhook/whatsapp-bot",
    "events": ["messages.upsert"],
    "session": "default"
  }'
```

### 6.2 Via WAHA UI

1. Buka WAHA UI: `http://localhost:3000`
2. Login jika diperlukan
3. Pilih session yang aktif
4. Klik "Webhooks" tab
5. Klik "Add Webhook"
6. Masukkan:
   - URL: `http://n8n:5678/webhook/whatsapp-bot`
   - Events: Centang "messages.upsert"
7. Klik "Save"

### 6.3 Verify Webhook

```bash
# Cek webhook yang terdaftar
curl http://waha:3000/api/webhooks

# Expected response:
# [
#   {
#     "url": "http://n8n:5678/webhook/whatsapp-bot",
#     "events": ["messages.upsert"],
#     "session": "default"
#   }
# ]
```

---

## Langkah 7: Test API Connections

### 7.1 Test Dashboard API Health

```bash
curl http://dashboard-kecamatan:8000/api/health

# Expected response:
# {
#   "status": "healthy",
#   "service": "dashboard-kecamatan",
#   "timestamp": "2026-02-11T14:00:00.000Z"
# }
```

### 7.2 Test WhatsApp API Gateway Health

```bash
curl http://whatsapp-api-gateway:8001/api/health

# Expected response:
# {
#   "status": "healthy",
#   "service": "whatsapp-api-gateway",
#   "timestamp": "2026-02-11T14:00:00.000Z"
# }
```

### 7.3 Test WhatsApp Reply Status

```bash
curl -X GET http://dashboard-kecamatan:8000/api/reply/status \
  -H "Authorization: Bearer YOUR_API_TOKEN"

# Expected response:
# {
#   "success": true,
#   "service": "whatsapp-reply",
#   "configured": true,
#   "webhook_url": "***configured***",
#   "timestamp": "2026-02-11T14:00:00.000Z"
# }
```

### 7.4 Test FAQ Search

```bash
curl -X GET "http://whatsapp-api-gateway:8001/api/faq/search?q=jam%20pelayanan" \
  -H "Authorization: Bearer YOUR_API_TOKEN"

# Expected response:
# {
#   "success": true,
#   "data": {
#     "found": true,
#     "question": "Berapa jam pelayanan kantor kecamatan?",
#     "answer": "Jam pelayanan kantor kecamatan:\n\nSenin - Kamis: 08:00 - 15:00\nJumat: 08:00 - 11:00\nSabtu - Minggu: Libur"
#   }
# }
```

### 7.5 Test Status Check

```bash
curl -X GET "http://whatsapp-api-gateway:8001/api/status/check?identifier=YOUR_UUID_OR_PHONE" \
  -H "Authorization: Bearer YOUR_API_TOKEN"

# Expected response:
# {
#   "success": true,
#   "data": {
#     "found": true,
#     "uuid": "550e8400-e29b-41d4-a716-446655440000",
#     "jenis_layanan": "Pengaduan Umum",
#     "status": "selesai",
#     "status_label": "Selesai"
#   }
# }
```

---

## Langkah 8: Test WhatsApp Bot

### 8.1 Test Help Command

Kirim pesan ke nomor WhatsApp bot:
```
/help
```

**Expected Response:**
```
🤖 Bot Pelayanan Kecamatan

📋 Perintah yang tersedia:

📊 /status {uuid} - Cek status laporan
❓ /faq {keyword} - Cari informasi FAQ
ℹ️ /help - Tampilkan bantuan ini

📝 Contoh penggunaan:
• /status 550e8400-e29b-41d4-a716-446655440000
• /faq jam pelayanan
• /faq syarat ktp

💡 Tips: Kirim pesan langsung untuk melaporkan atau bertanya, bot akan otomatis mencari jawaban FAQ terlebih dahulu.
```

### 8.2 Test FAQ Command

Kirim pesan:
```
/faq jam pelayanan
```

**Expected Response:**
```
📋 Jawaban FAQ

❓ Pertanyaan:
Berapa jam pelayanan kantor kecamatan?

✅ Jawaban:
Jam pelayanan kantor kecamatan:

Senin - Kamis: 08:00 - 15:00
Jumat: 08:00 - 11:00
Sabtu - Minggu: Libur

💡 Apakah jawaban ini membantu?
• Ketik "YA" jika sudah cukup
• Ketik pertanyaan lain untuk informasi lainnya
```

### 8.3 Test Normal Message (FAQ Auto-Lookup)

Kirim pesan:
```
Jam berapa kantor kecamatan buka?
```

**Expected Response:**
```
📋 Jawaban Otomatis

✅ Jawaban:
Jam pelayanan kantor kecamatan:

Senin - Kamis: 08:00 - 15:00
Jumat: 08:00 - 11:00
Sabtu - Minggu: Libur

💡 Apakah jawaban ini membantu?
• Ketik "YA" jika sudah cukup
• Ketik "LANJUT" jika ingin melaporkan resmi ke petugas
```

### 8.4 Test Report Submission

Kirim pesan:
```
Jalan di desa saya rusak parah, tolong diperbaiki
```

**Expected Response:**
```
✅ Laporan Diterima

🆔 ID: 550e8400-e29b-41d4-a716-446655440000
📂 Kategori: pengaduan
📊 Status: Menunggu Verifikasi

💡 Cek status kapan saja dengan ketik: /status 550e8400-e29b-41d4-a716-446655440000
```

---

## Langkah 9: Verify Dashboard Integration

### 9.1 Check Inbox

1. Login ke dashboard-kecamatan
2. Buka menu "Pelayanan" → "Inbox"
3. Pastikan pesan dari WhatsApp muncul
4. Klik salah satu laporan untuk melihat detail

### 9.2 Test Status Update Notification

1. Buka salah satu laporan dari WhatsApp
2. Update status ke "Diproses"
3. Pastikan checkbox "Kirim notifikasi WhatsApp" tercentang
4. Klik "Simpan"
5. Cek WhatsApp, notifikasi harus terkirim

**Expected WhatsApp Notification:**
```
✅ Update Status Laporan

🆔 ID: 550e8400-e29b-41d4-a716-446655440000
📂 Layanan: Pengaduan Umum
📊 Status: Sedang Diproses
📅 Update: 11 Feb 2026, 14:00

💡 Ketik /status 550e8400-e29b-41d4-a716-446655440000 untuk cek status kapan saja.
```

---

## Langkah 10: Configure FAQ

### 10.1 Add FAQ Entries

1. Login ke dashboard-kecamatan
2. Buka menu "Pelayanan" → "FAQ"
3. Klik "Tambah FAQ"
4. Isi form:
   - **Category**: Pilih kategori (misal: Administrasi)
   - **Keywords**: Kata kunci dipisahkan koma (misal: jam, buka, pelayanan, operasional)
   - **Question**: Pertanyaan (misal: Berapa jam pelayanan kantor kecamatan?)
   - **Answer**: Jawaban (misal: Jam pelayanan kantor kecamatan: Senin - Kamis: 08:00 - 15:00, Jumat: 08:00 - 11:00, Sabtu - Minggu: Libur)
   - **Is Active**: Centang
5. Klik "Simpan"

### 10.2 Test FAQ

Kirim pesan ke WhatsApp dengan kata kunci yang sama:
```
Jam berapa kantor buka?
```

Bot harus merespon dengan jawaban FAQ yang baru ditambahkan.

---

## Langkah 11: Monitor and Troubleshoot

### 11.1 Check n8n Executions

1. Buka n8n UI
2. Klik "Executions"
3. Lihat log eksekusi workflow
4. Cek error jika ada

### 11.2 Check Logs

```bash
# Dashboard logs
docker logs dashboard-kecamatan-app-1 --tail 100 -f

# WhatsApp API logs
docker logs whatsapp-laravel-api-1 --tail 100 -f

# n8n logs
docker logs n8n-1 --tail 100 -f

# WAHA logs
docker logs waha-1 --tail 100 -f
```

### 11.3 Common Issues

**Issue: Webhook tidak menerima pesan**
```bash
# Cek n8n workflow aktif
curl http://n8n:5678/webhook/whatsapp-bot

# Cek WAHA webhook
curl http://waha:3000/api/webhooks
```

**Issue: FAQ tidak ditemukan**
```bash
# Test FAQ search API
curl -X GET "http://whatsapp-api-gateway:8001/api/faq/search?q=test" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

**Issue: Notifikasi tidak terkirim**
```bash
# Test reply API
curl -X POST http://dashboard-kecamatan:8000/api/reply/send \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "6281234567890",
    "message": "Test",
    "type": "manual_reply"
  }'
```

---

## Checklist Deployment

- [ ] Generate API token
- [ ] Update dashboard-kecamatan/.env
- [ ] Update whatsapp-laravel-api/.env
- [ ] Update n8n environment variables
- [ ] Copy WhatsAppReplyController.php
- [ ] Copy updated PelayananController.php
- [ ] Copy updated dashboard-kecamatan/routes/api.php
- [ ] Copy updated WebhookController.php
- [ ] Copy updated DashboardApiService.php
- [ ] Copy updated whatsapp-laravel-api/routes/api.php
- [ ] Copy n8n workflow files
- [ ] Restart dashboard-kecamatan service
- [ ] Restart whatsapp-api-gateway service
- [ ] Import WhatsApp Service Bot workflow
- [ ] Import Dashboard to WhatsApp Reply workflow
- [ ] Configure WAHA credentials
- [ ] Setup WAHA webhook
- [ ] Test Dashboard API health
- [ ] Test WhatsApp API Gateway health
- [ ] Test WhatsApp reply status
- [ ] Test FAQ search
- [ ] Test status check
- [ ] Test /help command
- [ ] Test /faq command
- [ ] Test normal message (FAQ auto-lookup)
- [ ] Test report submission
- [ ] Verify dashboard inbox
- [ ] Test status update notification
- [ ] Configure FAQ entries
- [ ] Test FAQ integration
- [ ] Monitor n8n executions
- [ ] Check logs

---

## Support

Untuk bantuan lebih lanjut:
- Dokumentasi: `whatsapp/docs/`
- Logs: Cek Docker logs untuk error details
- n8n Executions: Lihat log eksekusi workflow
