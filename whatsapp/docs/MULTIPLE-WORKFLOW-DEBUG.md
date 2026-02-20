# Debugging: Multiple Workflows Active

## Masalah
Ketik "Menu" di WhatsApp → Dapat response "CEK STATUS BERKAS" (salah!)

## Root Cause
Kemungkinan ada **MULTIPLE workflows aktif** yang sama-sama listen di webhook path `/whatsapp-layanan`

## Cara Debug

### Step 1: Cek Active Workflows di n8n
1. Buka n8n dashboard: http://localhost:5678
2. Lihat daftar workflows
3. Cek mana yang **Active** (toggle hijau)
4. Lihat apakah ada lebih dari 1 workflow WhatsApp yang aktif

### Step 2: Identify Workflow Yang Bentrok
Workflows yang mungkin aktif dan bentrok:
- `whatsapp-bot-final`
- `whatsapp-bot-simple`
- `whatsapp-classifier`
- `whatsapp-router` (yang lama)
- `whatsapp-router-fixed` (yang baru) ✅ **Harusnya yang ini saja**

### Step 3: Disable Workflows Lama
1. Klik workflow yang lama
2. Toggle "Active" jadi OFF (abu-abu)
3. Pastikan hanya `whatsapp-router-fixed` yang aktif

### Step 4: Test Lagi
Ketik "Menu" di WhatsApp → Harusnya dapat menu bot

## Alternative Solution: Ganti Webhook Path

Kalau masih error, saya bisa buat workflow dengan webhook path BARU:
- Path baru: `/whatsapp-v2` atau `/whatsapp-besuk-v2`
- Update WAHA webhook URL ke path baru
- Tidak akan bentrok dengan workflow lama
