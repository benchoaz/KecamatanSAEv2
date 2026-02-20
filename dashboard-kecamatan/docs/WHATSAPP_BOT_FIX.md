# WhatsApp Bot Integration Fix - FINAL

## Tanggal: 19 Februari 2026

---

## Masalah Utama yang Ditemukan

### Root Cause: WAHA Mengirim Berbagai Event Types

WAHA tidak hanya mengirim event `message`, tapi juga:
- `message.any`
- `session.status`
- `qr`
- `ready`

**Masalah:** Workflow memproses SEMUA event, padahal hanya `message` yang memiliki `payload.from` (chatId).

Saat event bukan `message`:
- `payload.from` = undefined
- `chatId` = kosong
- WAHA crash dengan error Puppeteer

---

## Solusi FINAL

### Workflow Baru: [`whatsapp-bot-stable.n8n`](../../whatsapp-bot-stable.n8n)

```
WAHA Webhook
    ↓
Extract Message Only (filter event === 'message')
    ↓
Call Dashboard API
    ↓
Prepare Response
    ↓
ChatId Valid? (IF node - guard sebelum sendText)
    ↓                    ↓
Send to WhatsApp    Respond Error
    ↓
Respond OK
```

### Code Node "Extract Message Only" (FINAL STABLE VERSION)

```javascript
// FINAL STABLE VERSION - Extract only message events
const raw = items[0].json;

// Hanya proses event message
if (raw.event !== 'message') {
  console.log('Skipping non-message event:', raw.event);
  return [];
}

// Ambil payload
const payload = raw.payload || {};

// Extract data
const chatId = payload.from || '';
const message = (payload.body || '').trim();
const fromMe = payload.fromMe || false;

// VALIDASI WAJIB
if (!chatId || !chatId.includes('@c.us')) {
  console.log('INVALID CHATID:', chatId);
  return [];
}

// Skip messages from me (bot)
if (fromMe === true) {
  console.log('Skipping message from me');
  return [];
}

console.log('Valid message:', { chatId, message });

return [
  {
    json: {
      chatId,
      message,
      fromMe
    }
  }
];
```

---

## Perubahan yang Dilakukan

### 1. docker-compose.yml
- ✅ Menghapus `gateway-net` (external network)
- ✅ Semua container hanya menggunakan `app-network`
- ✅ Menambahkan `healthcheck`

### 2. n8n Workflow
- ✅ Filter hanya event `message`
- ✅ Validasi `chatId` harus mengandung `@c.us`
- ✅ Skip `fromMe === true`
- ✅ Guard dengan IF node sebelum sendText

### 3. Nginx Config
- ✅ Fix `fastcgi_pass` ke `dashboard-kecamatan-app:9000`
- ✅ Tambah health check endpoint `/health`

---

## Langkah Implementasi

### Step 1: Import Workflow Baru
1. Buka n8n: http://localhost:5679
2. Klik "Import from File"
3. Pilih file: `whatsapp-bot-stable.n8n`
4. **DEACTIVATE workflow lama**
5. **ACTIVATE workflow baru**

### Step 2: Verifikasi WAHA Session
```bash
curl http://waha-kecamatan:3000/api/sessions
```
Harus return `status: WORKING`

### Step 3: Test
Kirim "MENU" ke WhatsApp bot

---

## Checklist

- [ ] Workflow baru sudah di-import
- [ ] Workflow lama sudah di-deactivate
- [ ] WAHA session status = WORKING
- [ ] Test kirim "MENU" berhasil

---

## Best Practice untuk WAHA WebJS

1. **Selalu filter event:**
   ```javascript
   if (raw.event !== 'message') return [];
   ```

2. **Validasi chatId:**
   ```javascript
   if (!chatId || !chatId.includes('@c.us')) return [];
   ```

3. **Guard sebelum sendText:**
   - Gunakan IF node untuk cek `chatId is not empty`
   - Jika FALSE → STOP, jangan kirim ke WAHA

---

## File yang Relevan

| File | Deskripsi |
|------|-----------|
| [`whatsapp-bot-stable.n8n`](../../whatsapp-bot-stable.n8n) | Workflow n8n FINAL |
| [`docker-compose.yml`](../docker-compose.yml) | Docker config |
| [`docker/nginx/conf.d/default.conf`](../docker/nginx/conf.d/default.conf) | Nginx config |
