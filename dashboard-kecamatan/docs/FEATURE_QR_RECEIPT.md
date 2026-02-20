# 📱 QR Code + PDF Receipt System

## 🎯 **Feature Overview**

Sistem struk pengajuan digital dengan QR Code untuk tracking status berkas secara real-time.

---

## ✨ **Key Features:**

### 1. **Auto-Generated PDF Receipt**
- ✅ Download otomatis setelah submit pengajuan
- 📄 PDF dengan design premium & professional
- 🏢 Include logo & kontak kecamatan
- 📋 Nomor pengajuan unik (UUID)
- 📅 Timestamp lengkap

### 2. **QR Code Integration**
- 🔲 QR Code embedded di struk PDF
- 📱 Scan → Langsung ke tracking page
- ⚡ Auto-fill & auto-submit status
- 🔒 Secure (UUID-based)

### 3. **Multi-Access Tracking**
Masyarakat bisa track via:
- Scan QR Code
- Input nomor WA manual
- Input UUID manual
- Link langsung dari SweetAlert

### 4. **Admin Tools**
- 📄 Download struk kapan saja
- 🔲 Generate QR Code ulang
- 👁️ Preview receipt sebelum download
- 📊 Analytics (planned)

---

## 🚀 **User Flow:**

### **Untuk Masyarakat:**
```
1. Submit pengajuan di landing page
     ↓
2. SweetAlert muncul dengan 2 button:
   - Download Struk (PDF)
   - Lacak Status
     ↓
3. Download PDF → Simpan
     ↓
4. Scan QR Code kapan saja → Lihat status real-time
```

### **Untuk Admin:**
```
1. Buka inbox → Detail pengaduan
     ↓
2. Klik "Struk" → Download PDF
     ↓
3. Klik "QR" → Lihat QR Code only
     ↓
4. Share ke pemohon via WA/Email
```

---

## 📐 **Technical Stack:**

| Component | Library | License |
|-----------|---------|---------|
| QR Code | simple-qrcode | MIT (Free) |
| PDF Generator | laravel-dompdf | MIT (Free) |
| JS Alert | SweetAlert2 | MIT (Free) |
| Icons | FontAwesome | Free |

**💰 Total Cost: FREE!**

---

## 🗺️ **Routes:**

```php
// Public Routes
GET  /struk/{uuid}         → Download PDF Receipt
GET  /struk/{uuid}/preview → Preview Receipt (HTML)
GET  /qr/{uuid}            → QR Code Image (PNG)

// Tracking Routes (existing)
GET  /lacak-berkas         → Tracking Page
POST /lacak-berkas/cek     → Check Status API
```

---

## 📊 **Database:**

**No additional tables needed!** Uses existing `public_services` table.

QR Code & Receipt generated on-the-fly from existing data.

---

## 🎨 **Receipt Design:**

### **Header:**
- Logo Kecamatan (if available)
- Nama Kecamatan
- "Struk Pengajuan Layanan Publik"

### **Body:**
- **Nomor Pengajuan** (large, highlighted)
- **Info Layanan:**
  - Jenis layanan
  - Tanggal pengajuan
  - Status (badge berwarna)
- **Data Pemohon:**
  - Nama
  - NIK
  - WhatsApp
  - Desa
- **QR Code Section:**
  - QR Code (200x200 px)
  - Instruksi scan
  - Tracking URL

### **Footer:**
- Alamat kantor
- Kontak (phone, email)
- Jam layanan

---

## 🔄 **Future Enhancements:**

### **Phase 2: WhatsApp Integration**
- Auto-send receipt via WA
- Notifikasi status change
- Template pesan

### **Phase 3: Analytics Dashboard**
- Total receipts generated
- Scan rate analytics
- Popular services chart
- Avg completion time

### **Phase 4: Mobile App**
- Native QR scanner
- Push notifications
- Offline mode

---

## 💡 **Benefits:**

### **For Citizens:**
✅ Professional experience
✅ Easy status tracking
✅ No need remember ID
✅ Scan anywhere, anytime
✅ Digital proof of submission

### **For Admin:**
✅ Reduce phone inquiries
✅ Modern & credible image
✅ Easy record sharing
✅ Automated documentation
✅ Better service reputation

### **For Kecamatan:**
✅ Cost: $0 (fully free)
✅ Modern public service
✅ Reduced manual work
✅ Better accountability
✅ Higher citizen satisfaction

---

## 📝 **Example Use Cases:**

### Case 1: Busy Citizen
```
Pak Budi submit KTP request → Download struk → 
Simpan di Google Drive → Busy 1 minggu → 
Scan QR → Lihat status "Selesai" → Download hasil
```

### Case 2: Elder Citizen
```
Bu Siti submit bantuan sosial → Admin print struk for her → 
Keluarga scan QR → Track status → 
Pick up dokumen saat ready
```

### Case 3: Office Worker
```
Submit 5 requests different services → 
All receipts in one folder → 
Scan specific QR when needed → 
Efficient!
```

---

## 🛠️ **Installation:**

See [INSTALL_PACKAGES.md](./INSTALL_PACKAGES.md) for detailed setup instructions.

**Quick start:**
```bash
docker-compose exec app composer require simplesoftwareio/simple-qrcode
docker-compose exec app composer require barryvdh/laravel-dompdf
docker-compose exec app php artisan migrate
```

---

## 📞 **Support:**

Jika ada kendala:
1. Check [INSTALL_PACKAGES.md](./INSTALL_PACKAGES.md) → Troubleshooting section
2. Check `storage/logs/laravel.log` untuk error details
3. Test di tinker: `QrCode::generate('test')`

---

**Built with ❤️ for better public service**
