# WhatsApp Automation System - Kecamatan Besuk

Sistem otomasi WhatsApp yang terintegrasi dengan Dashboard Kecamatan untuk mengelola pesan masuk dari warga dan mengklasifikasikannya ke dalam 4 kategori layanan.

## 🏗️ Arsitektur

```
Warga WhatsApp
   ↓
WAHA (WhatsApp HTTP API)
   ↓
n8n (Klasifikasi Pesan)
   ↓
Laravel API Gateway
   ↓
Dashboard Kecamatan (Inbox)
```

## 📋 Prasyarat

- Docker & Docker Compose
- Nomor WhatsApp untuk bot (bisa nomor pribadi atau bisnis)
- Dashboard Kecamatan sudah berjalan di `http://localhost:8080`

## 🚀 Quick Start

### 1. Konfigurasi Environment

Edit file `.env` dan `.env` di folder `laravel-api`:

```bash
# File: .env (root)
DASHBOARD_API_URL=http://host.docker.internal:8080
DASHBOARD_API_TOKEN=ganti_dengan_token_rahasia_anda

# File: laravel-api/.env
DASHBOARD_API_URL=http://host.docker.internal:8080
DASHBOARD_API_TOKEN=ganti_dengan_token_rahasia_anda
```

**PENTING**: Gunakan token yang sama di kedua file!

### 2. Tambahkan Token ke Dashboard

Edit file `.env` di `d:\Projectku\dashboard-kecamatan`:

```env
WHATSAPP_API_TOKEN=ganti_dengan_token_rahasia_anda
```

Token ini harus **sama persis** dengan yang di WhatsApp stack!

### 3. Install Dependencies Laravel API

```bash
cd laravel-api
composer install
cd ..
```

### 4. Jalankan Container

```bash
docker-compose up -d
```

Tunggu semua container berjalan:
- ✅ **WAHA**: `http://localhost:3001`
- ✅ **n8n**: `http://localhost:5678`
- ✅ **WhatsApp API**: `http://localhost:8001`

### 5. Hubungkan WhatsApp

1. **Buka WAHA API docs**: `http://localhost:3001`
2. **Create Session**:
   - POST `/api/sessions`
   - Body: `{"name": "default"}`
   - Response akan berisi QR code text
3. **Scan QR Code**:
   - Jika `WAHA_PRINT_QR=true`, QR akan tercetak di console
   - Atau gunakan QR text untuk generate QR code
   - Scan dengan WhatsApp mobile (Linked Devices)
4. **Verifikasi Status**:
   - GET `/api/sessions/default`
   - Status harus "WORKING"

### 6. Setup n8n Workflow

1. **Buka n8n**: `http://localhost:5678`
2. **Import Workflow**:
   - Workflows → Import from File
   - Pilih `n8n-workflows/whatsapp-classifier.json`
3. **Activate Workflow**: Toggle switch ke ON (hijau)
4. **Salin Webhook URL** dari node "Webhook WAHA"
   - Contoh: `http://localhost:5678/webhook/whatsapp-incoming`

### 7. Konfigurasi WAHA Webhook

Kirim request untuk set webhook WAHA:

> [!IMPORTANT]
> **Container Networking**: WAHA dan n8n berada di network yang sama (`whatsapp-network`), jadi gunakan **nama container** `n8n-kecamatan`, BUKAN `localhost` atau `host.docker.internal`.

```bash
curl -X POST http://localhost:3001/api/sessions/default/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://n8n-kecamatan:5678/webhook/whatsapp-incoming",
    "events": ["message"]
  }'
```

## ✅ Testing

### Test 1: Health Check

```bash
curl http://localhost:8001/api/health
```

Expected: `{"success": true, "status": "healthy"}`

### Test 2: Kirim Pesan WhatsApp

Kirim pesan ke nomor WhatsApp yang terhubung dengan WAHA:
- **Pengaduan**: "Saya mau lapor jalan rusak"
- **Pelayanan**: "Bagaimana syarat buat KTP?"
- **UMKM**: "Saya ingin daftar UMKM"
- **Loker**: "Ada lowongan kerja?"

### Test 3: Cek Inbox Dashboard

1. Login ke Dashboard Kecamatan: `http://localhost:8080`
2. Menu: **Pelayanan** → **Inbox**
3. Filter by source: **WhatsApp**
4. Verifikasi pesan muncul dengan kategori yang benar

## 📂 Struktur Folder

```
whatsapp/
├── docker-compose.yml          # Konfigurasi container
├── .env                        # Environment variables
├── .gitignore
├── README.md                   # File ini
├── laravel-api/
│   ├── Dockerfile
│   ├── composer.json
│   ├── .env
│   ├── public/
│   │   └── index.php           # Entry point API
│   ├── app/
│   │   ├── Http/
│   │   │   └── Controllers/
│   │   │       ├── HealthController.php
│   │   │       └── WebhookController.php
│   │   └── Services/
│   │       └── DashboardApiService.php
│   └── storage/
│       └── logs/               # Transaction logs
└── n8n-workflows/
    ├── whatsapp-classifier.json
    └── README.md
```

## 🔀 Alur Data

### Pesan Masuk

1. Warga kirim WhatsApp → WAHA terima
2. WAHA kirim webhook → n8n
3. n8n klasifikasi berdasarkan keyword:
   - **Pengaduan**: keluhan, lapor, masalah, rusak
   - **Pelayanan**: surat, ktp, kk, administrasi
   - **UMKM**: usaha, jualan, produk, umkm
   - **Loker**: lowongan, kerja, loker, pekerjaan
4. n8n kirim ke Laravel API Gateway
5. Laravel validasi dan transform data
6. Laravel kirim ke Dashboard API
7. Dashboard simpan di tabel `public_services`

### Field Mapping

| WhatsApp | PublicService Table |
|----------|---------------------|
| Phone | `whatsapp` |
| Sender name | `nama_pemohon` |
| Message | `uraian` |
| Category | `category` (pengaduan/pelayanan/umkm/loker) |
| - | `source` = 'whatsapp' |
| - | `status` = 'menunggu_verifikasi' |

## 🛠️ Troubleshooting

### Container Tidak Bisa Communicate

**Problem**: Laravel API tidak bisa kirim data ke dashboard

**Solution**:
- Pastikan `DASHBOARD_API_URL=http://host.docker.internal:8080`
- Jangan gunakan `localhost` (dalam container ini tidak benar)

### Token Authentication Gagal

**Problem**: 401 Unauthorized dari dashboard

**Solution**:
1. Cek token di 3 file `.env` **harus sama persis**:
   - `whatsapp/.env`
   - `whatsapp/laravel-api/.env`
   - `dashboard-kecamatan/.env`
2. Restart dashboard setelah ubah `.env`

### QR Code Tidak Muncul

**Problem**: Tidak ada QR code untuk scan

**Solution**:
```bash
docker logs waha-kecamatan
```
QR code akan tercetak di logs jika `WAHA_PRINT_QR=true`

### Pesan Tidak Masuk ke Dashboard

**Problem**: WhatsApp terima pesan tapi inbox kosong

**Debug Steps**:
1. **Cek n8n execution**:
   - Buka `http://localhost:5678/executions`
   - Lihat apakah ada execution (hijau = sukses, merah = error)
2. **Cek Laravel API logs**:
   ```bash
   cat laravel-api/storage/logs/transactions-*.log
   ```
3. **Cek Dashboard Laravel logs**:
   ```bash
   tail -f dashboard-kecamatan/storage/logs/laravel.log
   ```

### Klasifikasi Salah

**Problem**: Pesan masuk ke kategori yang salah

**Solution**:
- Edit keyword di n8n workflow
- Buka workflow → Edit node "Classify Message"
- Tambah/ubah regex pattern

## 🔐 Security Notes

1. **Ganti Token Default**: Jangan gunakan `your_secure_random_token_here`
2. **Generate Token Aman**:
   ```bash
   openssl rand -base64 32
   ```
3. **Simpan Token di .env**: Jangan commit token ke git

## 📊 Logs & Monitoring

### Transaction Logs (Laravel API)
Path: `laravel-api/storage/logs/transactions-YYYY-MM-DD.log`

Berisi:
- Request dari n8n
- Data yang ditransform
- Response dari dashboard

### Error Logs (Laravel API)
Path: `laravel-api/storage/logs/api-errors-YYYY-MM-DD.log`

Berisi error komunikasi dengan dashboard

### Dashboard Logs
Path: `dashboard-kecamatan/storage/logs/laravel.log`

Berisi log dari `storeFromWhatsapp` method

## 🚧 Future Enhancements

### AI Classification (Opsional)

Ganti keyword matching dengan AI (OpenAI, Claude, Local LLM):

1. Buat account OpenAI / Anthropic
2. Edit n8n workflow
3. Ganti node "Classify Message" dengan "HTTP Request" ke AI API
4. Parse response AI untuk dapat kategori

### FAQ Auto-Reply (PRIORITAS TINGGI - Kurangi Beban Inbox 60-70%)

**Problem**: Semua pesan saat ini masuk ke inbox, termasuk pertanyaan FAQ yang bisa dijawab otomatis.

**Solution**: Sebelum buat `PublicService`, cek FAQ dashboard dulu:

```
Pesan WA masuk
   ↓
Cari di FAQ dashboard (via API)
   ↓
   ├─ FAQ ditemukan → Balas langsung via WAHA → SELESAI (tidak buat tiket)
   └─ FAQ tidak ditemukan → Buat PublicService → Masuk inbox
```

**Implementation**:
1. Tambah endpoint di dashboard: `GET /api/faq-search?q={query}` (sudah ada!)
2. Tambah node di n8n workflow:
   - HTTP Request ke dashboard FAQ API
   - If FAQ found → HTTP Request ke WAHA send message
   - If FAQ not found → Continue ke Laravel API (existing flow)
3. Tidak perlu ubah arsitektur!

**Impact**: 
- Admin hanya handle pertanyaan kompleks
- FAQ dijawab instant ke warga
- Inbox lebih fokus dan terkelola

### Auto Reply

Admin bisa balas pesan melalui dashboard, dan balasan terkirim otomatis ke WhatsApp warga melalui WAHA API.

### Multi-Device

Tambah multiple WhatsApp session untuk desa yang berbeda-beda.

## 📞 Support

Jika ada masalah:
1. Cek logs (lihat section Troubleshooting)
2. Restart containers: `docker-compose restart`
3. Rebuild containers: `docker-compose up -d --build`

## 📝 License

Internal use - Kecamatan Besuk
