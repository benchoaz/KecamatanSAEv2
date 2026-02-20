# n8n Workflow Configuration

## Import Workflow

1. Buka n8n di `http://localhost:5678`
2. Klik menu **Workflows** > **Import from File**
3. Pilih file `whatsapp-classifier.json`
4. Klik **Import**

## Konfigurasi Webhook

Setelah workflow di-import:

1. Buka node **"Webhook WAHA"**
2. Salin URL webhook yang ditampilkan (contoh: `http://localhost:5678/webhook/whatsapp-incoming`)
3. URL ini akan digunakan untuk konfigurasi WAHA

## Cara Kerja Workflow

Workflow ini terdiri dari 6 node:

1. **Webhook WAHA**: Menerima pesan dari WAHA
2. **Extract Message Data**: Mengekstrak data penting (phone, message, sender_name)
3. **Classify Message**: Klasifikasi pesan berdasarkan keyword
   - **Pengaduan**: keluhan, lapor, masalah, rusak, komplain, aduan
   - **Pelayanan**: surat, ktp, kk, administrasi, layanan, akta, nikah, cerai, domisili
   - **UMKM**: usaha, jualan, produk, umkm, dagang, bisnis, toko
   - **Loker**: lowongan, kerja, loker, pekerjaan, lamaran, vacancy
4. **Set Category**: Menentukan kategori final (default: pelayanan)
5. **Send to WhatsApp API**: Kirim ke Laravel API Gateway
6. **Respond Success**: Kirim response sukses ke WAHA

## Aktivasi Workflow

1. Klik tombol **Active** di kanan atas untuk mengaktifkan workflow
2. Pastikan status berubah menjadi **Active** (hijau)

## Testing Workflow

1. Gunakan "Test Workflow" di n8n untuk melihat hasil eksekusi
2. Kirim pesan WhatsApp test untuk memicu workflow
3. Lihat log eksekusi di tab **Executions**

## Customization

### Menambah Keyword Klasifikasi

Edit node **"Classify Message"** untuk menambah atau mengubah keyword:

```javascript
/(keyword1|keyword2|keyword3)/
```

### Menggunakan AI untuk Klasifikasi

Jika ingin menggunakan AI (OpenAI, Claude, dll), ganti node **"Classify Message"** dengan:
- Node **HTTP Request** ke OpenAI API
- Node **Code** untuk parsing response AI

Contoh akan ditambahkan di masa depan.
