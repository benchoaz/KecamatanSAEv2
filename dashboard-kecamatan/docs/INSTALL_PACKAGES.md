# 🚀 Installation Guide: QR Code + PDF Receipt System

## 📦 **Step 1: Install Required Packages**

Karena menggunakan Laravel + Docker, jalankan command berikut:

```bash
# Masuk ke container (sesuaikan nama service Docker Anda)
docker-compose exec app bash

# Atau langsung dari luar container:
docker-compose exec app composer require simplesoftwareio/simple-qrcode
docker-compose exec app composer require barryvdh/laravel-dompdf
```

## 🔧 **Step 2: Publish Configuration**

```bash
docker-compose exec app php artisan vendor:publish --provider="SimpleSoftwareIO\QrCode\QrCodeServiceProvider"
docker-compose exec app php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## 🗄️ **Step 3: Run Migrations**

```bash
docker-compose exec app php artisan migrate
```

## ✅ **Step 4: Test Installation**

Test QR Code generation:

```bash
docker-compose exec app php artisan tinker
```

Di tinker, run:

```php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
QrCode::size(300)->generate('test');
// Jika tidak error, berhasil!
exit
```

## 🎨 **Step 5: Clear Cache**

```bash
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan cache:clear
```

## 🧪 **Step 6: Testing Features**

### Test 1: Submit Pengajuan
1. Buka landing page: `http://localhost:8000`
2. Klik service card (misal: "Surat Keterangan")
3. Isi form dan submit
4. **Harus muncul SweetAlert dengan 2 button:**
   - ✅ Download Struk (PDF)
   - 🔍 Lacak Status

### Test 2: Download Receipt
1. Klik "Download Struk"
2. **PDF harus terdownload** dengan:
   - Logo kecamatan
   - Nomor pengajuan (UUID)
   - QR Code (scan ready)
   - Detail pengajuan
   - Kontak kecamatan

### Test 3: Scan QR Code
1. Download struk
2. Buka PDF di HP
3. Scan QR Code dengan camera HP
4. **Harus redirect** ke halaman tracking dengan status

### Test 4: Admin Access
1. Login sebagai admin
2. Buka inbox → Detail pengaduan
3. **Harus ada 2 button baru** di header:
   - 📄 Struk (download PDF)
   - 🔲 QR (lihat QR Code)

### Test 5: QR Code Direct
Access: `http://localhost:8000/qr/<uuid>`
**Harus tampil** QR Code image PNG

---

## 📋 **Files Created:**

✅ `app/Http/Controllers/ReceiptController.php`
✅ `resources/views/receipts/service-receipt.blade.php`
✅ `routes/web.php` (updated)
✅ `app/Http/Controllers/PublicServiceController.php` (updated)
✅ `resources/views/kecamatan/pelayanan/show.blade.php` (updated)
✅ `resources/views/landing.blade.php` (updated)

---

## 🐛 **Troubleshooting:**

### Error: "Class 'QrCode' not found"
**Solution:**
```bash
docker-compose exec app composer dump-autoload
docker-compose exec app php artisan config:clear
```

### Error: "Class 'PDF' not found"
**Solution:**
```bash
docker-compose exec app composer require barryvdh/laravel-dompdf
docker-compose exec app php artisan config:clear
```

### QR Code tidak muncul di PDF
**Solution:**
- Check apakah base64 QR code ter-generate
- Check apakah tag `<img>` di template benar
- Try clear view cache: `php artisan view:clear`

### PDF tampilan berantakan
**Solution:**
- DomPDF tidak support semua CSS
- Gunakan inline styles atau table-based layout
- Hindari flexbox complex

---

## 🎉 **Success Indicators:**

- ✅ Submit form → Download button muncul
- ✅ Download PDF → File terdownload dengan QR
- ✅ Scan QR → Redirect ke tracking page
- ✅ Admin inbox → Button Struk & QR ada
- ✅ No errors di laravel.log

**Jika semua test pass, DONE!** 🚀
