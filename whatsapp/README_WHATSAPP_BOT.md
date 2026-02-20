# WhatsApp Bot Pelayanan - Dashboard Kecamatan Integration

Integrasi WhatsApp sebagai bot pelayanan yang terhubung dengan dashboard-kecamatan untuk memberikan layanan otomatis kepada warga.

## 📋 Ringkasan

WhatsApp Bot Pelayanan adalah sistem otomatis yang memungkinkan warga untuk:
- Mengirim laporan dan pengaduan via WhatsApp
- Mendapatkan jawaban otomatis dari FAQ
- Melacak status laporan secara real-time
- Menerima notifikasi update status

## 🏗️ Arsitektur

```
┌─────────────┐      ┌──────────────────────────────────────┐
│   WAHA      │◄─────│            n8n Workflow               │
│ (WhatsApp   │─────>│  ┌────────────────────────────────┐  │
│  API)       │      │  │ 1. Receive Message             │  │
└─────────────┘      │  │ 2. Extract & Normalize         │  │
                     │  │ 3. Check Command (status/help) │  │
                     │  │ 4. FAQ Lookup                  │  │
                     │  │ 5. Category Classification     │  │
                     │  │ 6. Send to Dashboard API       │  │
                     │  │ 7. Send Auto-Reply             │  │
                     │  └────────────────────────────────┘  │
                     └──────────────────────────────────────┘
                                   │
                                   ▼
                     ┌──────────────────────────────────────┐
                     │         Dashboard-Kecamatan          │
                     │  ┌────────────────────────────────┐  │
                     │  │ API: /api/inbox/whatsapp       │  │
                     │  │ API: /api/faq/search            │  │
                     │  │ API: /api/status/check          │  │
                     │  │ API: /api/reply/send            │  │
                     │  └────────────────────────────────┘  │
                     └──────────────────────────────────────┘
```

## ✨ Fitur Utama

### 1. FAQ Otomatis
- Bot mencari jawaban FAQ sebelum menyimpan ke inbox
- Emergency detection untuk situasi darurat
- Synonym mapping untuk pencarian lebih akurat

### 2. Command System
| Command | Deskripsi |
|---------|-----------|
| `/help` | Tampilkan bantuan |
| `/status {uuid}` | Cek status laporan |
| `/faq {keyword}` | Cari informasi FAQ |

### 3. Auto-Reply
- Konfirmasi penerimaan laporan
- Update status otomatis
- Notifikasi penyelesaian

### 4. Bidirectional Communication
- Dashboard dapat mengirim balasan ke WhatsApp
- Notifikasi status update otomatis

## 📁 Struktur File

```
whatsapp/
├── docs/
│   ├── ANALISIS_WHATSAPP_BOT_PELAYANAN.md    # Analisis lengkap
│   ├── ENVIRONMENT_CONFIGURATION.md           # Konfigurasi environment
│   ├── IMPLEMENTATION_GUIDE.md                # Panduan implementasi
│   └── QUICK_REFERENCE.md                     # Quick reference
├── n8n-workflows/
│   ├── whatsapp-service-bot.json              # Workflow bot utama
│   └── dashboard-to-whatsapp-reply.json       # Workflow reply
├── deploy-whatsapp-bot.sh                     # Script deployment (Linux/Mac)
└── deploy-whatsapp-bot.ps1                    # Script deployment (Windows)

dashboard-kecamatan/
├── app/Http/Controllers/
│   ├── WhatsAppReplyController.php            # Controller reply WhatsApp (BARU)
│   └── Kecamatan/
│       └── PelayananController.php            # Controller pelayanan (UPDATE)
└── routes/
    └── api.php                                # API routes (UPDATE)

whatsapp/laravel-api/
├── app/Http/Controllers/
│   └── WebhookController.php                  # Controller webhook (UPDATE)
├── app/Services/
│   └── DashboardApiService.php                # Service API dashboard (UPDATE)
└── routes/
    └── api.php                                # API routes (UPDATE)
```

## 🚀 Quick Start

### 1. Setup Environment

Generate API token:
```bash
openssl rand -base64 32
```

Update `.env` files:

**dashboard-kecamatan/.env:**
```bash
N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply
DASHBOARD_API_TOKEN=YOUR_GENERATED_TOKEN
```

**whatsapp/laravel-api/.env:**
```bash
DASHBOARD_API_URL=http://dashboard-kecamatan:8000
DASHBOARD_API_TOKEN=YOUR_GENERATED_TOKEN
```

### 2. Deploy

**Linux/Mac:**
```bash
cd whatsapp
chmod +x deploy-whatsapp-bot.sh
./deploy-whatsapp-bot.sh
```

**Windows:**
```powershell
cd whatsapp
.\deploy-whatsapp-bot.ps1
```

### 3. Import n8n Workflows

1. Buka n8n UI: `http://localhost:5678`
2. Import `whatsapp/n8n-workflows/whatsapp-service-bot.json`
3. Import `whatsapp/n8n-workflows/dashboard-to-whatsapp-reply.json`
4. Aktifkan kedua workflow

### 4. Setup WAHA Webhook

Configure WAHA webhook ke n8n:
```
http://n8n:5678/webhook/whatsapp-bot
```

### 5. Test

Kirim pesan ke nomor WhatsApp bot:
```
/help
```

## 📚 Dokumentasi

| Dokumen | Deskripsi |
|---------|-----------|
| [ANALISIS_WHATSAPP_BOT_PELAYANAN.md](docs/ANALISIS_WHATSAPP_BOT_PELAYANAN.md) | Analisis lengkap arsitektur dan usulan perbaikan |
| [ENVIRONMENT_CONFIGURATION.md](docs/ENVIRONMENT_CONFIGURATION.md) | Panduan konfigurasi environment variables |
| [IMPLEMENTATION_GUIDE.md](docs/IMPLEMENTATION_GUIDE.md) | Panduan implementasi langkah-demi-langkah |
| [QUICK_REFERENCE.md](docs/QUICK_REFERENCE.md) | Quick reference untuk bot commands dan fitur |

## 🔧 API Endpoints

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/faq/search` | GET | Search FAQ untuk bot |
| `/api/status/check` | GET | Cek status via WhatsApp |
| `/api/reply/send` | POST | Kirim balasan ke WhatsApp |
| `/api/reply/bulk` | POST | Kirim balasan bulk |
| `/api/reply/test` | POST | Test koneksi WhatsApp |
| `/api/reply/status` | GET | Cek status service |

## 📊 Kategori Laporan

| Kategori | Deskripsi | Keywords |
|----------|-----------|----------|
| `pengaduan` | Pengaduan Umum | lapor, keluhan, rusak, aduan, komplain, masalah |
| `pelayanan` | Pelayanan Administrasi | surat, ktp, kk, administrasi, layanan, akta, nikah, cerai, domisili |
| `umkm` | UMKM Rakyat | usaha, jualan, produk, umkm, dagang, bisnis, toko |
| `loker` | Lowongan Kerja | lowongan, kerja, loker, pekerjaan, lamaran, vacancy |

## 🛠️ Troubleshooting

### Bot tidak merespon
1. Cek n8n workflow aktif
2. Cek WAHA session aktif
3. Cek webhook terkonfigurasi

### FAQ tidak ditemukan
1. Cek FAQ aktif di dashboard
2. Cek keywords sesuai
3. Test FAQ search API

### Notifikasi tidak terkirim
1. Cek `N8N_REPLY_WEBHOOK_URL` di .env
2. Cek n8n "Dashboard to WhatsApp Reply" workflow aktif
3. Cek WAHA session aktif

## 🔐 Security

- Gunakan API token yang kuat (minimal 32 karakter)
- Jangan commit `.env` ke version control
- Gunakan HTTPS untuk production
- Implement rate limiting untuk API endpoints
- Log semua request untuk audit trail

## 📈 Monitoring

Monitor service berikut:

| Service | Health Check | Log Location |
|---------|--------------|--------------|
| Dashboard-Kecamatan | `/api/health` | `storage/logs/laravel.log` |
| WhatsApp API Gateway | `/api/health` | `storage/logs/laravel.log` |
| n8n | `/healthz` | n8n UI → Executions |
| WAHA | `/api/sessions` | Docker logs |

## 🤝 Support

Untuk bantuan lebih lanjut:
- Lihat dokumentasi di folder `docs/`
- Cek logs untuk error details
- Hubungi tim technical support

## 📝 Checklist Deployment

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
- [ ] Test bot commands
- [ ] Verify dashboard integration
- [ ] Configure FAQ entries
- [ ] Test status update notification

## 📄 License

Proprietary - Internal Use Only

## 👥 Contributors

- Kilo Code - Implementation & Architecture
