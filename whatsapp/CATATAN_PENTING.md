# ⚠️ CATATAN PENTING - Baca Sebelum Deploy

## 🔸 1. Webhook URL Consistency

**WAJIB DIPAHAMI**: 

Komunikasi **antar container** dalam satu docker-compose network:
```bash
# ✅ BENAR - gunakan nama container
http://n8n-kecamatan:5678/webhook/whatsapp-incoming
```

Komunikasi **dari container ke host** (Laravel API → Dashboard):
```bash
# ✅ BENAR - gunakan host.docker.internal
http://host.docker.internal:8080/api/inbox/whatsapp
```

**JANGAN** campur keduanya:
```bash
# ❌ SALAH - jangan gunakan localhost dari dalam container
http://localhost:5678/...

# ❌ SALAH - jangan gunakan host.docker.internal untuk antar container
http://host.docker.internal:5678/...
```

## 🔸 2. FAQ Auto-Reply (Fase Berikutnya)

**Kondisi Saat Ini**:
- Semua pesan WhatsApp → masuk inbox
- Admin harus handle SEMUA pertanyaan, termasuk FAQ sederhana

**Rekomendasi Optimasi** (kurangi beban inbox 60-70%):

```
Pesan WhatsApp masuk
   ↓
Cek FAQ dashboard API (/api/faq-search?q=...)
   ↓
   ├─ FAQ ditemukan → Balas otomatis via WAHA → DONE (tidak buat tiket)
   └─ FAQ tidak ditemukan → Buat PublicService → Masuk inbox
```

**Implementasi** (nanti, tidak sekarang):
1. Tambah node di n8n workflow sebelum "Send to WhatsApp API"
2. HTTP Request ke `http://host.docker.internal:8080/api/faq-search`
3. Jika `found: true` → Kirim balasan via WAHA, stop workflow
4. Jika `found: false` → Lanjut ke flow existing

**Tidak perlu ubah kode Laravel atau Dashboard!** Cukup edit n8n workflow.

## 🔸 3. Container Names Reference

Sesuai `docker-compose.yml`:
- WAHA: `waha-kecamatan`
- n8n: `n8n-kecamatan`
- Laravel API: `whatsapp-api-gateway`

Gunakan nama ini untuk komunikasi antar container.

## 🔸 4. Token Security

**WAJIB ganti** token default sebelum production:

```bash
# Generate token aman
openssl rand -base64 32
```

Update di 3 file .env dengan token yang SAMA:
1. `whatsapp/.env`
2. `whatsapp/laravel-api/.env`
3. `dashboard-kecamatan/.env`

## 📌 Tujuan Catatan Ini

Memastikan sistem awet 1-3 tahun tanpa refactoring besar:
- ✅ Konsistensi networking
- ✅ Skalabilitas (FAQ auto-reply siap ditambahkan)
- ✅ Keterbacaan konfigurasi
- ✅ Keamanan token
