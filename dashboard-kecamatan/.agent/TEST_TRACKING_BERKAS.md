# Test Plan: Tracking Berkas

## 📋 Overview
Dokumen ini berisi panduan lengkap untuk melakukan testing fitur tracking berkas pada sistem Dashboard Kecamatan.

## 🎯 Fitur yang Ditest
- Halaman tracking berkas publik
- Pencarian berkas menggunakan nomor WhatsApp atau UUID
- Tampilan status berkas (Pending, Proses, Selesai)
- Download hasil berkas (Digital Completion)
- Informasi pickup berkas (Physical Completion)

## 🔗 URL & Routes
- **Halaman Tracking**: `http://localhost:8000/lacak-berkas`
- **Route Name**: `public.tracking`
- **API Endpoint**: `POST /lacak-berkas/cek` (route: `public.tracking.check`)

---

## 📝 Test Cases

### Test Case 1: Akses Halaman Tracking
**Tujuan**: Memastikan halaman tracking dapat diakses dengan baik

**Langkah**:
1. Buka browser
2. Navigasi ke `http://localhost:8000/lacak-berkas`
3. Periksa tampilan halaman

**Expected Result**:
- ✅ Halaman berhasil dimuat
- ✅ Terdapat form input dengan label "Masukkan Nomor WhatsApp atau ID Berkas"
- ✅ Terdapat tombol "Cek Status Sekarang"
- ✅ Design menggunakan gradient teal/blue yang modern
- ✅ Icon search-location ditampilkan di header

---

### Test Case 2: Pencarian dengan Nomor WhatsApp (Data Ditemukan)
**Tujuan**: Memastikan sistem dapat menemukan berkas menggunakan nomor WhatsApp

**Pre-requisite**:
- Harus ada data berkas di database dengan nomor WhatsApp tertentu
- Contoh: `628123456789`

**Langkah**:
1. Buka halaman tracking
2. Input nomor WhatsApp yang valid (contoh: `628123456789`)
3. Klik tombol "Cek Status Sekarang"
4. Tunggu response

**Expected Result**:
- ✅ Muncul card "Detail Status Berkas" dengan header hijau teal
- ✅ Menampilkan badge status (Selesai/Proses/Pending) dengan warna yang sesuai
- ✅ Menampilkan informasi:
  - ID Berkas (UUID)
  - Jenis Layanan
  - Tanggal Pengajuan
  - Tanggapan (jika ada)
- ✅ Tidak ada error message

---

### Test Case 3: Pencarian dengan UUID (Data Ditemukan)
**Tujuan**: Memastikan sistem dapat menemukan berkas menggunakan UUID

**Pre-requisite**:
- Harus ada data berkas di database
- Dapatkan UUID dari database atau dari hasil pengajuan sebelumnya

**Langkah**:
1. Buka halaman tracking
2. Input UUID yang valid (contoh: `550e8400-e29b-41d4-a716-446655440000`)
3. Klik tombol "Cek Status Sekarang"
4. Tunggu response

**Expected Result**:
- ✅ Sama seperti Test Case 2
- ✅ Data berkas yang sesuai dengan UUID ditampilkan

---

### Test Case 4: Pencarian dengan Data Tidak Ditemukan
**Tujuan**: Memastikan error handling untuk data yang tidak ada

**Langkah**:
1. Buka halaman tracking
2. Input nomor WhatsApp atau UUID yang tidak ada di database (contoh: `621111111111`)
3. Klik tombol "Cek Status Sekarang"

**Expected Result**:
- ✅ Muncul alert merah dengan icon exclamation-circle
- ✅ Menampilkan pesan: "Berkas Tidak Ditemukan"
- ✅ Menampilkan detail error: "Berkas tidak ditemukan. Pastikan nomor WA atau ID berkas sudah benar."
- ✅ Card result tidak ditampilkan

---

### Test Case 5: Digital Completion - Download PDF
**Tujuan**: Memastikan fitur download PDF untuk berkas yang sudah selesai (digital)

**Pre-requisite**:
- Harus ada berkas dengan:
  - `completion_type` = 'digital'
  - `result_file_path` terisi
  - `status` = 'Selesai'

**Langkah**:
1. Buka halaman tracking
2. Input identifier berkas yang sudah selesai (digital)
3. Klik tombol "Cek Status Sekarang"
4. Periksa tampilan hasil
5. Klik tombol "Download Hasil (PDF)"

**Expected Result**:
- ✅ Muncul section "Berkas Sudah Selesai!" dengan icon check-circle hijau
- ✅ Terdapat tombol download dengan gradient merah
- ✅ Tombol memiliki icon file-pdf
- ✅ Klik tombol membuka PDF di tab baru
- ✅ PDF berhasil didownload/ditampilkan

---

### Test Case 6: Physical Completion - Pickup Info
**Tujuan**: Memastikan informasi pickup ditampilkan untuk berkas fisik

**Pre-requisite**:
- Harus ada berkas dengan:
  - `completion_type` = 'physical'
  - `ready_at`, `pickup_person`, `pickup_notes` terisi
  - `status` = 'Selesai'

**Langkah**:
1. Buka halaman tracking
2. Input identifier berkas yang sudah selesai (physical)
3. Klik tombol "Cek Status Sekarang"
4. Periksa tampilan hasil

**Expected Result**:
- ✅ Muncul card kuning (amber) dengan gradient
- ✅ Judul: "Ambil di Kantor Kecamatan" dengan icon building
- ✅ Menampilkan informasi:
  - Siap Diambil: [tanggal]
  - Hubungi: [nama petugas]
  - Catatan pickup (jika ada)
- ✅ Design menggunakan warna amber/kuning yang konsisten

---

### Test Case 7: Status Badge Colors
**Tujuan**: Memastikan badge status menampilkan warna yang benar

**Langkah**:
1. Test dengan berkas status "Selesai"
2. Test dengan berkas status "Sedang Diproses" atau "Dikoordinasikan"
3. Test dengan berkas status "Menunggu Klarifikasi"

**Expected Result**:
- ✅ Status "Selesai" → Badge hijau (gradient #10b981 to #059669)
- ✅ Status "Proses/Dikoordinasikan" → Badge orange (gradient #f59e0b to #d97706)
- ✅ Status "Pending" → Badge abu-abu (gradient #6b7280 to #4b5563)

---

### Test Case 8: Form Validation
**Tujuan**: Memastikan validasi form berfungsi

**Langkah**:
1. Buka halaman tracking
2. Klik tombol "Cek Status Sekarang" tanpa mengisi input
3. Coba submit form kosong

**Expected Result**:
- ✅ Browser menampilkan pesan validasi HTML5 "Please fill out this field"
- ✅ Form tidak tersubmit

---

### Test Case 9: Multiple Requests
**Tujuan**: Memastikan sistem dapat handle multiple pencarian berturut-turut

**Langkah**:
1. Cari berkas pertama (contoh: WA 628111111111)
2. Tunggu hasil muncul
3. Cari berkas kedua (contoh: WA 628222222222)
4. Tunggu hasil muncul
5. Cari berkas yang tidak ada (contoh: WA 621111111111)

**Expected Result**:
- ✅ Setiap pencarian menghapus hasil sebelumnya
- ✅ Hasil baru ditampilkan dengan benar
- ✅ Error dan success state bergantian dengan benar
- ✅ Tidak ada memory leak atau bug UI

---

### Test Case 10: Responsive Design
**Tujuan**: Memastikan halaman responsive di berbagai ukuran layar

**Langkah**:
1. Buka halaman tracking
2. Resize browser ke ukuran mobile (375px)
3. Resize ke tablet (768px)
4. Resize ke desktop (1920px)
5. Test pencarian di setiap ukuran

**Expected Result**:
- ✅ Layout tetap rapi di semua ukuran
- ✅ Form tetap usable di mobile
- ✅ Card result tidak overflow
- ✅ Tombol download tetap accessible
- ✅ Text tetap readable

---

## 🗄️ Data Preparation

### Cara Membuat Test Data

#### 1. Berkas dengan Digital Completion
```sql
-- Cek berkas yang ada
SELECT id, uuid, whatsapp, jenis_layanan, status, completion_type 
FROM public_services 
LIMIT 5;

-- Update berkas untuk testing digital completion
UPDATE public_services 
SET 
    completion_type = 'digital',
    result_file_path = 'public_services/sample.pdf',
    status = 'Selesai',
    public_response = 'Berkas Anda telah selesai diproses. Silakan download hasil.'
WHERE id = [ID_BERKAS];
```

#### 2. Berkas dengan Physical Completion
```sql
UPDATE public_services 
SET 
    completion_type = 'physical',
    ready_at = NOW(),
    pickup_person = 'Bapak Ahmad (Loket 1)',
    pickup_notes = 'Mohon bawa KTP asli saat pengambilan',
    status = 'Selesai',
    public_response = 'Berkas Anda sudah siap diambil di kantor kecamatan.'
WHERE id = [ID_BERKAS];
```

#### 3. Berkas dengan Status Berbeda
```sql
-- Status: Menunggu Klarifikasi
UPDATE public_services SET status = 'Menunggu Klarifikasi' WHERE id = [ID];

-- Status: Sedang Diproses
UPDATE public_services SET status = 'Sedang Diproses' WHERE id = [ID];

-- Status: Dikoordinasikan ke Desa
UPDATE public_services SET status = 'Dikoordinasikan ke Desa' WHERE id = [ID];
```

---

## 🐛 Bug Checklist

Periksa hal-hal berikut saat testing:

- [ ] CSRF token error
- [ ] 404 error pada route
- [ ] JavaScript error di console
- [ ] Network error (timeout, 500)
- [ ] XSS vulnerability (coba input script tag)
- [ ] SQL injection (coba input `' OR '1'='1`)
- [ ] File download error (404, permission denied)
- [ ] Broken CSS/styling
- [ ] Missing icons (FontAwesome)
- [ ] Slow response time (> 3 detik)

---

## 📊 Test Results Template

### Test Execution Date: [TANGGAL]
### Tester: [NAMA]

| Test Case | Status | Notes |
|-----------|--------|-------|
| TC1: Akses Halaman | ⬜ Pass / ⬜ Fail | |
| TC2: Pencarian WA (Found) | ⬜ Pass / ⬜ Fail | |
| TC3: Pencarian UUID (Found) | ⬜ Pass / ⬜ Fail | |
| TC4: Data Not Found | ⬜ Pass / ⬜ Fail | |
| TC5: Digital Completion | ⬜ Pass / ⬜ Fail | |
| TC6: Physical Completion | ⬜ Pass / ⬜ Fail | |
| TC7: Status Badge Colors | ⬜ Pass / ⬜ Fail | |
| TC8: Form Validation | ⬜ Pass / ⬜ Fail | |
| TC9: Multiple Requests | ⬜ Pass / ⬜ Fail | |
| TC10: Responsive Design | ⬜ Pass / ⬜ Fail | |

### Overall Result: ⬜ PASS / ⬜ FAIL

### Issues Found:
1. 
2. 
3. 

---

## 🚀 Quick Start Testing

### Cara Tercepat untuk Test:

1. **Start Laravel Server**
   ```bash
   php artisan serve
   ```

2. **Buka Browser**
   ```
   http://localhost:8000/lacak-berkas
   ```

3. **Gunakan Data Test Ini** (sesuaikan dengan data di database Anda):
   - WhatsApp: `628123456789`
   - UUID: Lihat di database `SELECT uuid FROM public_services LIMIT 1`

4. **Periksa Console**
   - Buka Developer Tools (F12)
   - Tab Console: Periksa error JavaScript
   - Tab Network: Periksa request/response API

---

## 📞 Troubleshooting

### Problem: "Berkas tidak ditemukan" padahal data ada
**Solution**: 
- Periksa format nomor WhatsApp (harus sama persis dengan di database)
- Periksa apakah UUID benar (case-sensitive)
- Cek database: `SELECT * FROM public_services WHERE whatsapp = '628xxx'`

### Problem: Download PDF tidak berfungsi
**Solution**:
- Periksa apakah file ada di `storage/app/public_services/`
- Jalankan `php artisan storage:link`
- Periksa permission folder storage

### Problem: CSRF token mismatch
**Solution**:
- Clear browser cache
- Refresh halaman
- Periksa session configuration di `.env`

### Problem: 500 Internal Server Error
**Solution**:
- Cek `storage/logs/laravel.log`
- Periksa database connection
- Pastikan semua migration sudah dijalankan

---

## ✅ Acceptance Criteria

Fitur tracking berkas dianggap **PASS** jika:

1. ✅ Semua 10 test case PASS
2. ✅ Tidak ada critical bug
3. ✅ Response time < 2 detik
4. ✅ UI/UX sesuai design
5. ✅ Mobile responsive
6. ✅ Accessible (keyboard navigation works)
7. ✅ No console errors
8. ✅ Security validation passed

---

## 📝 Notes

- Test dilakukan pada environment: **Development**
- Browser yang ditest: Chrome, Firefox, Safari (optional)
- Device yang ditest: Desktop, Mobile (optional)
- Test data harus di-reset setelah testing selesai (optional)

