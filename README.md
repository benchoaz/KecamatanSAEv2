# 🏛️ Sistem Integrasi Layanan Publik Digital (SILAP) - Kecamatan Besuk

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![n8n](https://img.shields.io/badge/n8n-FF6D5A?style=for-the-badge&logo=n8n&logoColor=white)](https://n8n.io)
[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://www.docker.com)
[![WhatsApp API](https://img.shields.io/badge/WAHA-API-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://waha.devlikeapro.pro/)

> **"Mewujudkan Pelayanan Prima melalui Transformasi Digital yang Inklusif dan Akuntabel."**

Selamat datang di repositori resmi **SILAP (Sistem Integrasi Layanan Publik)** Kecamatan Besuk. Proyek ini merupakan inisiatif strategis untuk memodernisasi kanal komunikasi antara pemerintah kecamatan dan masyarakat melalui integrasi *WhatsApp Automation*, *n8n Orchestration*, dan *Centralized Dashboard Management*.

---

## 📝 Deskripsi Proyek

SILAP dirancang untuk menghilangkan hambatan administratif dan mempercepat respon layanan publik. Dengan memanfaatkan platform WhatsApp sebagai antarmuka utama, masyarakat dapat mengakses berbagai layanan tanpa perlu mengunduh aplikasi tambahan, sejalan dengan prinsip *Service at Your Fingertips*.

### Pilar Utama Sistem:
1. **Pemberdayaan Ekonomi (UMKM & Jasa)**: Digitalisasi data pelaku usaha lokal untuk meningkatkan visibilitas dan akses pasar.
2. **Transparansi Administrasi**: Fitur pelacakan status berkas secara *real-time*.
3. **Respon Cepat (Quick Response)**: Penanganan pengaduan masyarakat yang terintegrasi langsung ke sistem internal.
4. **Efisiensi Birokrasi**: Otomatisasi alur kerja rutin menggunakan *low-code orchestration*.

---

## 🏗️ Arsitektur Sistem (Technical Architecture)

Sistem ini menggunakan pendekatan **"Single Source of Truth"** di mana Dashboard Laravel bertindak sebagai "Otak" utama untuk seluruh logika bisnis dan penyimpanan data.

```mermaid
graph TD
    User((Warga Besuk)) <--> |WhatsApp Message| WAHA[WAHA API Gateway]
    WAHA <--> |Webhook| N8N{n8n Orchestrator}
    N8N <--> |API Request/Response| Dashboard[Laravel Dashboard API]
    Dashboard <--> DB[(PostgreSQL/MySQL Database)]
    
    subgraph "Logic Processing"
    Dashboard --> Intent[Intent Detection]
    Dashboard --> State[State Machine Management]
    Dashboard --> Auth[Owner Verification]
    end
```

### Komponen Teknis:
- **Core Engine**: Laravel 10+ (PHP 8.2) sebagai pengelola basis data dan API internal.
- **Workflow Orchestrator**: n8n untuk normalisasi pesan, *anti-looping*, dan penjadwalan.
- **WhatsApp Gateway**: WAHA (WhatsApp HTTP API) untuk konektivitas pesan yang stabil.
- **Infrastructure**: Containerized environment menggunakan Docker & Docker Compose.

---

## ✨ Fitur Unggulan

| Fitur | Deskripsi | Status |
| :--- | :--- | :--- |
| **Cek Status Berkas** | Monitoring progress permohonan surat/administrasi secara otomatis. | ✅ Produksi |
| **Direktori UMKM** | Pencarian produk lokal berdasarkan kata kunci cerdas. | ✅ Produksi |
| **Layanan Jasa Lokal** | Menghubungkan warga dengan penyedia jasa (tukang, teknisi, dll). | ✅ Produksi |
| **Portal Loker** | Informasi lowongan kerja terkini di wilayah Kecamatan Besuk. | ✅ Produksi |
| **Pengaduan Masyarakat** | Integrasi alur pengaduan formal dengan manajemen status. | ✅ Produksi |
| **Kontrol Owner** | Fitur khusus bagi pelaku usaha untuk mengelola status "Lapak" via WA. | ✅ Produksi |

---

## 🚀 Panduan Deployment (VPS & Server)

### Prasyarat (Prerequisites):
- Server Linux (Ubuntu/Debian direkomendasikan)
- Git terinstall
- Docker & Docker Compose (Versi terbaru)
- Nama domain yang sudah diarahkan ke IP Server (Opsional)

### Langkah Instalasi Pertama Kali:

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/benchoaz/KecamatanSAEversiKabupaten.git
   cd KecamatanSAEversiKabupaten
   ```

2. **Konfigurasi Environment**:
   Salin file `.env.example` ke `.env` pada subdirektori `dashboard-kecamatan` dan konfigurasi password database serta kredensial lainnya.
   ```bash
   cp dashboard-kecamatan/.env.example dashboard-kecamatan/.env
   ```

3. **Jalankan Instalasi dan Container**:
   Menjalankan *script* bawaan untuk memastikan instalasi awal berjalan mulus:
   ```bash
   docker compose up -d --build
   ```

4. **Konfigurasi Laravel Inti**:
   Jalankan migrasi database agar struktur siap digunakan:
   ```bash
   docker exec kecamatan-app php /var/www/artisan key:generate
   docker exec kecamatan-app php /var/www/artisan migrate --seed --force
   docker exec kecamatan-app php /var/www/artisan optimize:clear
   ```

5. **Pairing WhatsApp**:
   Akses dashboard WAHA (contoh `http://ip-vps:3000`) untuk memindai kode QR dan menghubungkan nomor WhatsApp layanan utama.

---

## 🔄 Cara Melakukan Pembaruan (Update) di VPS

Sistem aplikasi ini dirancang ter-containerization dengan kuat, sehingga saat ada fitur atau optimasi baru dari repository, Anda tidak akan mengalami kendala *caching* atau ketergantungan paket versi OS.

Cukup jalankan runtutan sintaks berikut secara berurutan di dalam folder `KecamatanSAEversiKabupaten`:

```bash
# 1. Menarik source-code terbaru dari GitHub
git pull origin main

# 2. Menata ulang kontainer tanpa downtime lama
docker compose up -d --build

# 3. Menyesuaikan modifikasi kolom basis data yang baru (Bila ada)
docker exec kecamatan-app php /var/www/artisan migrate --force

# 4. Membersihkan cache sistem agar perubahan UI/UX tersinkronisasi
docker exec kecamatan-app php /var/www/artisan optimize:clear
```

---

## 🛡️ Keamanan & Etika Layanan

Kami berkomitmen untuk menjaga kerahasiaan data masyarakat sesuai dengan kaidah **Sistem Pemerintahan Berbasis Elektronik (SPBE)** di Indonesia. Seluruh log interaksi dienkripsi dan hanya digunakan untuk kepentingan peningkatan kualitas layanan publik.

---

## 🤝 Kontribusi

Apresiasi tinggi bagi rekan-rekan pengembang yang ingin berkontribusi. Silakan ajukan *Pull Request* atau sampaikan isu terkait melalui kanal yang tersedia. Mari bersama-sama membangun teknologi yang bermanfaat bagi nusa dan bangsa.

**Hormat kami,**

**Tim Pengembang SILAP - Kecamatan Besuk**
*Untuk Masyarakat, Oleh Teknologi.*
