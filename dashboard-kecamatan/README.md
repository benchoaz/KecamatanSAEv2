# 🏛️ Dashboard Kecamatan SAE
### Sistem Administrasi Terpadu untuk Masyarakat

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=flat&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat&logo=php)](https://php.net)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=flat&logo=docker)](https://docker.com)
[![WhatsApp](https://img.shields.io/badge/WhatsApp-Bot-25D366?style=flat&logo=whatsapp)](https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP)

---

## 🌐 Landing Page

![Landing Page Hero](docs/screenshots/landing-hero.png)

![WhatsApp Bot](docs/screenshots/landing-whatsapp.png)

---

## 📋 Tentang Aplikasi

**Dashboard Kecamatan SAE** adalah platform digital terintegrasi untuk Kecamatan Besuk, Kabupaten Probolinggo. Sistem ini menghubungkan masyarakat dengan layanan pemerintahan kecamatan secara digital melalui web dan WhatsApp Bot.

### ✨ Fitur Utama

| Modul | Deskripsi |
|---|---|
| 🏠 **Landing Page** | Portal publik dengan info layanan, berita, dan kontak WhatsApp bot |
| 📋 **Pelayanan Publik** | Pengajuan & tracking layanan administrasi online |
| 🤖 **WhatsApp Bot** | Bot otomatis via WAHA + n8n untuk layanan 24 jam |
| 🏘️ **Pemerintahan Desa** | Monitoring aparatur, dokumen, dan perencanaan desa |
| 🏗️ **Pembangunan** | Monitoring proyek pembangunan desa |
| 🛡️ **Trantibum** | Laporan kejadian keamanan dan ketertiban |
| 🛒 **UMKM** | Direktori UMKM lokal dan etalase produk |
| 💼 **Loker** | Informasi lowongan kerja warga |
| 📰 **Berita** | Publikasi berita dan pengumuman kecamatan |
| 🗺️ **Geospasial** | Peta wilayah desa berbasis GeoJSON |

---

## 🏗️ Arsitektur Sistem

```
┌─────────────────────────────────────────────────────┐
│                   Docker Stack                       │
│                                                     │
│  ┌──────────┐  ┌──────────┐  ┌──────────────────┐  │
│  │  Nginx   │  │  Laravel │  │     MySQL 8.0    │  │
│  │ :8000    │→ │  PHP-FPM │→ │    :3307         │  │
│  └──────────┘  └──────────┘  └──────────────────┘  │
│                                                     │
│  ┌──────────────────┐  ┌──────────────────────────┐ │
│  │  WAHA (WhatsApp) │  │  n8n (Workflow)          │ │
│  │  :3099           │  │  :5678                   │ │
│  └──────────────────┘  └──────────────────────────┘ │
└─────────────────────────────────────────────────────┘
```

---

## 🚀 Instalasi dengan Docker

### Prasyarat
- Docker & Docker Compose
- Git

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP/dashboard-kecamatan

# 2. Copy environment file
cp .env.example .env

# 3. Jalankan Docker
docker-compose up -d

# 4. Install dependencies
docker exec dashboard-kecamatan-app composer install

# 5. Generate app key
docker exec dashboard-kecamatan-app php artisan key:generate

# 6. Jalankan migration
docker exec dashboard-kecamatan-app php artisan migrate --force

# 7. Jalankan seeder
docker exec dashboard-kecamatan-app php artisan db:seed --force

# 8. Storage link
docker exec dashboard-kecamatan-app php artisan storage:link
```

Akses aplikasi di: **http://localhost:8000**

---

## ⚙️ Konfigurasi Environment

Edit file `.env` sesuai kebutuhan:

```env
# Database
DB_HOST=db
DB_DATABASE=dashboard_kecamatan
DB_USERNAME=user
DB_PASSWORD=root

# WAHA WhatsApp API
WAHA_API_URL=http://waha-kecamatan:3000
WAHA_API_KEY=your_waha_api_key
WAHA_SESSION=default

# n8n Workflow
N8N_REPLY_WEBHOOK_URL=http://n8n-kecamatan:5678/webhook/whatsapp-besuk

# WhatsApp API Token (untuk n8n)
WHATSAPP_API_TOKEN=your_token_here
```

---

## 🤖 WhatsApp Bot

Bot WhatsApp terintegrasi dengan **WAHA** (WhatsApp HTTP API) dan **n8n** untuk workflow automation.

**Alur:**
```
Pesan WA → WAHA → n8n Webhook → Dashboard API → Response → WAHA → Pesan WA
```

**Konfigurasi nomor bot** dapat diubah langsung dari dashboard admin:
> Menu: **Pengaturan → Monitoring WhatsApp Bot**

---

## 👥 Akun Default

| Role | Username | Password |
|---|---|---|
| Super Admin | `superadmin` | `password` |
| Operator Kecamatan | `admin_kec` | `password` |
| Operator Desa | `admin_desa` | `password` |

---

## 🛠️ Tech Stack

- **Backend**: Laravel 11, PHP 8.2
- **Frontend**: Bootstrap 5, Tailwind CSS, Alpine.js
- **Database**: MySQL 8.0
- **Cache**: File/Database
- **WhatsApp**: WAHA (devlikeapro/waha)
- **Automation**: n8n
- **Server**: Nginx + PHP-FPM
- **Container**: Docker Compose

---

## 📁 Struktur Direktori

```
dashboard-kecamatan/
├── app/
│   ├── Http/Controllers/
│   │   ├── Kecamatan/          # Controller modul kecamatan
│   │   └── Api/                # API endpoints untuk WhatsApp bot
│   ├── Models/                 # Eloquent models
│   └── Services/
│       └── WhatsApp/           # Handler WhatsApp bot
├── database/
│   ├── migrations/             # Database migrations
│   └── seeders/                # Data seeder
├── docker/
│   └── php/                    # PHP Docker config
├── resources/views/
│   ├── kecamatan/              # Views dashboard kecamatan
│   ├── public/                 # Views halaman publik
│   └── landing.blade.php       # Landing page
└── routes/
    ├── web.php                 # Web routes
    ├── kecamatan.php           # Routes dashboard kecamatan
    └── api.php                 # API routes
```

---

## 📄 Lisensi

Project ini dikembangkan untuk **Kecamatan Besuk, Kabupaten Probolinggo**.

---

<p align="center">
  Dibuat dengan ❤️ untuk pelayanan masyarakat Kecamatan Besuk
</p>
