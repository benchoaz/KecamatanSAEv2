# 🚀 Quick Start Guide: Testing Tracking Berkas

## Langkah-langkah Testing

### 1️⃣ Persiapan Environment

Pastikan Laravel server berjalan:

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

---

### 2️⃣ Prepare Test Data

#### Opsi A: Menggunakan SQL Script (Recommended)

1. Buka database client Anda (phpMyAdmin, DBeaver, atau command line)

2. Jalankan script SQL:
   ```bash
   # Jika menggunakan MySQL command line
   mysql -u root -p dashboard_kecamatan < .agent/scripts/prepare_test_data.sql
   ```

   Atau copy-paste isi file `.agent/scripts/prepare_test_data.sql` ke database client Anda.

3. Verifikasi data sudah masuk:
   ```sql
   SELECT uuid, whatsapp, jenis_layanan, status, completion_type 
   FROM public_services 
   WHERE whatsapp LIKE '6281%';
   ```

#### Opsi B: Menggunakan Data Existing

Jika Anda sudah punya data berkas di database, skip step ini dan gunakan nomor WhatsApp atau UUID yang sudah ada.

---

### 3️⃣ Manual Testing (Browser)

#### Test Case 1: Digital Completion

1. Buka browser: `http://localhost:8000/lacak-berkas`
2. Input: `628111111111`
3. Klik "Cek Status Sekarang"
4. **Expected**: 
   - Status: Selesai
   - Muncul tombol "Download Hasil (PDF)"
   - Tombol berwarna merah gradient

#### Test Case 2: Physical Completion

1. Input: `628222222222`
2. Klik "Cek Status Sekarang"
3. **Expected**:
   - Status: Selesai
   - Muncul card kuning "Ambil di Kantor Kecamatan"
   - Ada info: Siap Diambil, Hubungi, Catatan

#### Test Case 3: Sedang Diproses

1. Input: `628333333333`
2. Klik "Cek Status Sekarang"
3. **Expected**:
   - Status: Sedang Diproses
   - Badge berwarna orange
   - Ada tanggapan publik

#### Test Case 4: Menunggu Klarifikasi

1. Input: `628444444444`
2. Klik "Cek Status Sekarang"
3. **Expected**:
   - Status: Menunggu Klarifikasi
   - Badge berwarna abu-abu
   - Ada tanggapan publik

#### Test Case 5: Data Not Found

1. Input: `621111111111` (nomor yang tidak ada)
2. Klik "Cek Status Sekarang"
3. **Expected**:
   - Muncul alert merah
   - Pesan: "Berkas Tidak Ditemukan"

---

### 4️⃣ Automated Testing (PHP Script)

Jalankan automated test script:

```bash
php .agent/scripts/quick_test_tracking.php
```

Script ini akan:
- ✅ Otomatis mengambil CSRF token
- ✅ Test 5 scenario berbeda
- ✅ Validasi response
- ✅ Menampilkan summary hasil test

**Expected Output:**
```
╔══════════════════════════════════════════════════════════════╗
║       QUICK TEST: Tracking Berkas Feature                   ║
╚══════════════════════════════════════════════════════════════╝

🔐 Getting CSRF token...
✅ CSRF token obtained

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Test #1: Digital Completion
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Identifier: 628111111111
✅ Status: Selesai
✅ UUID: 550e8400-e29b-41d4-a716-446655440001
✅ Jenis Layanan: Surat Keterangan Usaha
✅ Completion Type: digital
✅ Download URL: http://localhost:8000/storage/...

🎉 TEST PASSED

...

╔══════════════════════════════════════════════════════════════╗
║                      TEST SUMMARY                            ║
╚══════════════════════════════════════════════════════════════╝
Total Tests: 5
✅ Passed: 5
❌ Failed: 0

🎊 ALL TESTS PASSED! 🎊
```

---

### 5️⃣ Testing Checklist

Gunakan checklist ini saat manual testing:

#### UI/UX Testing
- [ ] Halaman loading dengan cepat (< 2 detik)
- [ ] Form input terlihat jelas dan mudah digunakan
- [ ] Placeholder text informatif
- [ ] Icon ditampilkan dengan benar (FontAwesome)
- [ ] Gradient colors terlihat smooth
- [ ] Responsive di mobile (test dengan resize browser)

#### Functional Testing
- [ ] Pencarian dengan WhatsApp berhasil
- [ ] Pencarian dengan UUID berhasil
- [ ] Error handling untuk data tidak ditemukan
- [ ] Badge status menampilkan warna yang benar
- [ ] Digital completion: tombol download muncul
- [ ] Physical completion: info pickup muncul
- [ ] Public response ditampilkan jika ada

#### Security Testing
- [ ] CSRF protection aktif
- [ ] Input sanitization (coba input `<script>alert('xss')</script>`)
- [ ] SQL injection prevention (coba input `' OR '1'='1`)
- [ ] Rate limiting (optional)

#### Performance Testing
- [ ] Response time < 2 detik
- [ ] No memory leak (test multiple searches)
- [ ] No JavaScript errors di console
- [ ] Network request efficient (check di DevTools)

---

### 6️⃣ Troubleshooting

#### Problem: Server tidak bisa diakses

```bash
# Check apakah server running
php artisan serve

# Jika port 8000 sudah digunakan, gunakan port lain
php artisan serve --port=8001
```

#### Problem: CSRF token mismatch

```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Refresh browser dengan Ctrl+Shift+R
```

#### Problem: Data test tidak ditemukan

```sql
-- Check apakah data sudah masuk
SELECT * FROM public_services WHERE whatsapp LIKE '6281%';

-- Jika kosong, run ulang prepare_test_data.sql
```

#### Problem: Download PDF error (404)

```bash
# Create symbolic link untuk storage
php artisan storage:link

# Check apakah file ada
ls -la storage/app/public_services/
```

#### Problem: JavaScript error di console

1. Buka DevTools (F12)
2. Tab Console
3. Screenshot error
4. Check apakah ada missing library (jQuery, Bootstrap, FontAwesome)

---

### 7️⃣ Cleanup After Testing

Setelah testing selesai, hapus data test:

```sql
-- Delete test data
DELETE FROM public_services 
WHERE whatsapp IN (
    '628111111111',
    '628222222222',
    '628333333333',
    '628444444444',
    '628555555555'
);

-- Verify
SELECT COUNT(*) FROM public_services WHERE whatsapp LIKE '6281%';
-- Should return 0
```

---

### 8️⃣ Report Issues

Jika menemukan bug, catat informasi berikut:

1. **Test Case**: Nomor test case yang gagal
2. **Steps to Reproduce**: Langkah-langkah untuk reproduce bug
3. **Expected Result**: Hasil yang diharapkan
4. **Actual Result**: Hasil yang sebenarnya terjadi
5. **Screenshot**: Screenshot error (jika ada)
6. **Console Log**: Error dari browser console (F12)
7. **Laravel Log**: Error dari `storage/logs/laravel.log`

Template:

```
BUG REPORT

Test Case: TC5 - Digital Completion
Steps:
1. Buka /lacak-berkas
2. Input: 628111111111
3. Klik "Cek Status Sekarang"

Expected: Tombol download muncul
Actual: Tombol tidak muncul, console error: "download_url is undefined"

Screenshot: [attach]
Console Log: [attach]
Laravel Log: [attach]
```

---

## 📊 Test Results

Setelah testing, isi form ini:

**Test Date**: _______________  
**Tester**: _______________  
**Environment**: Development / Staging / Production  

| Test Case | Status | Notes |
|-----------|--------|-------|
| Digital Completion | ⬜ Pass / ⬜ Fail | |
| Physical Completion | ⬜ Pass / ⬜ Fail | |
| Sedang Diproses | ⬜ Pass / ⬜ Fail | |
| Menunggu Klarifikasi | ⬜ Pass / ⬜ Fail | |
| Data Not Found | ⬜ Pass / ⬜ Fail | |
| Responsive Design | ⬜ Pass / ⬜ Fail | |
| Security | ⬜ Pass / ⬜ Fail | |
| Performance | ⬜ Pass / ⬜ Fail | |

**Overall Result**: ⬜ PASS / ⬜ FAIL

**Issues Found**:
1. 
2. 
3. 

---

## 🎯 Success Criteria

Testing dianggap **BERHASIL** jika:

✅ Semua test case PASS  
✅ Tidak ada critical bug  
✅ Response time < 2 detik  
✅ UI/UX sesuai design  
✅ Mobile responsive  
✅ No console errors  
✅ Security validation passed  

---

## 📞 Need Help?

Jika ada pertanyaan atau menemukan masalah:

1. Check dokumentasi di `.agent/TEST_TRACKING_BERKAS.md`
2. Check Laravel log: `storage/logs/laravel.log`
3. Check browser console (F12)
4. Review kode di:
   - Controller: `app/Http/Controllers/PublicServiceController.php`
   - View: `resources/views/public/tracking.blade.php`
   - Route: `routes/web.php`

---

**Happy Testing! 🚀**
