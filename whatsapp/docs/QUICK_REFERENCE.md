# WhatsApp Bot Pelayanan - Quick Reference

## Bot Commands

| Command | Deskripsi | Contoh |
|---------|-----------|--------|
| `/help` | Tampilkan bantuan dan daftar perintah | `/help` |
| `/status {uuid}` | Cek status laporan berdasarkan UUID | `/status 550e8400-e29b-41d4-a716-446655440000` |
| `/faq {keyword}` | Cari informasi FAQ | `/faq jam pelayanan` |

## Fitur Bot

### 1. FAQ Otomatis
Bot akan otomatis mencari jawaban FAQ sebelum menyimpan pesan ke inbox.

**Contoh:**
```
Warga: Jam berapa kantor kecamatan buka?
Bot: 📋 Jawaban Otomatis:

✅ Jawaban:
Jam pelayanan kantor kecamatan:

Senin - Kamis: 08:00 - 15:00
Jumat: 08:00 - 11:00
Sabtu - Minggu: Libur

💡 Apakah jawaban ini membantu?
• Ketik "YA" jika sudah cukup
• Ketik "LANJUT" jika ingin melaporkan resmi ke petugas
```

### 2. Emergency Detection
Bot mendeteksi kata kunci darurat dan memberikan respon prioritas.

**Kategori Emergency:**
- **Criminal**: maling, pencurian, perampokan, kejahatan → Polisi 110
- **Health**: pingsan, sesak napas, kecelakaan → Medis 119
- **Conflict**: keributan, tawuran, konflik → Aparat keamanan
- **Disaster**: banjir, longsor, kebakaran → Damkar 112

### 3. Status Tracking
Warga dapat mengecek status laporan kapan saja.

**Contoh:**
```
Warga: /status 550e8400-e29b-41d4-a716-446655440000
Bot: 📊 Status Laporan:

🆔 ID: 550e8400-e29b-41d4-a716-446655440000
📂 Layanan: Pengaduan Umum
📊 Status: Selesai

📝 Respon Petugas:
Jalan telah diperbaiki oleh Dinas PU. Terima kasih atas laporannya.
```

### 4. Auto-Reply
Bot mengirim konfirmasi otomatis saat laporan diterima.

**Contoh:**
```
Bot: ✅ Laporan Diterima

🆔 ID: 550e8400-e29b-41d4-a716-446655440000
📂 Kategori: pengaduan
📊 Status: Menunggu Verifikasi

💡 Cek status kapan saja dengan ketik: /status 550e8400-e29b-41d4-a716-446655440000
```

### 5. Status Update Notification
Warga menerima notifikasi otomatis saat status laporan diupdate.

**Contoh:**
```
Bot: ✅ Update Status Laporan

🆔 ID: 550e8400-e29b-41d4-a716-446655440000
📂 Layanan: Pengaduan Umum
📊 Status: Selesai
📅 Update: 11 Feb 2026, 14:00

📝 Respon Petugas:
Jalan telah diperbaiki oleh Dinas PU. Terima kasih atas laporannya.

💡 Ketik /status 550e8400-e29b-41d4-a716-446655440000 untuk cek status kapan saja.
```

## Kategori Laporan

| Kategori | Deskripsi | Keywords |
|----------|-----------|----------|
| `pengaduan` | Pengaduan Umum | lapor, keluhan, rusak, aduan, komplain, masalah |
| `pelayanan` | Pelayanan Administrasi | surat, ktp, kk, administrasi, layanan, akta, nikah, cerai, domisili |
| `umkm` | UMKM Rakyat | usaha, jualan, produk, umkm, dagang, bisnis, toko |
| `loker` | Lowongan Kerja | lowongan, kerja, loker, pekerjaan, lamaran, vacancy |

## API Endpoints

| Endpoint | Method | Deskripsi |
|----------|--------|-----------|
| `/api/faq/search` | GET | Search FAQ untuk bot |
| `/api/status/check` | GET | Cek status via WhatsApp |
| `/api/reply/send` | POST | Kirim balasan ke WhatsApp |
| `/api/reply/bulk` | POST | Kirim balasan bulk |
| `/api/reply/test` | POST | Test koneksi WhatsApp |
| `/api/reply/status` | GET | Cek status service |

## Troubleshooting

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

## Tips Penggunaan

1. **Gunakan command dengan benar**: Pastikan format command sesuai
2. **Simpan UUID**: Simpan UUID laporan untuk tracking
3. **Cek FAQ dulu**: Cek FAQ sebelum melaporkan untuk respon lebih cepat
4. **Gunakan kata kunci yang jelas**: Gunakan kata kunci spesifik untuk FAQ

## Contoh Skenario

### Skenario 1: Bertanya Jam Pelayanan
```
Warga: Jam berapa kantor kecamatan buka?
Bot: [Jawaban FAQ otomatis]
```

### Skenario 2: Melaporkan Masalah
```
Warga: Jalan di desa saya rusak parah
Bot: [Konfirmasi penerimaan laporan dengan UUID]
```

### Skenario 3: Cek Status
```
Warga: /status 550e8400-e29b-41d4-a716-446655440000
Bot: [Status laporan]
```

### Skenario 4: Petugas Update Status
```
Petugas: Update status di Dashboard → Selesai
Bot: [Notifikasi update status ke warga]
```

## Support

Untuk bantuan lebih lanjut:
- Dokumentasi: `whatsapp/docs/IMPLEMENTATION_GUIDE.md`
- Konfigurasi: `whatsapp/docs/ENVIRONMENT_CONFIGURATION.md`
- Analisis: `whatsapp/docs/ANALISIS_WHATSAPP_BOT_PELAYANAN.md`
