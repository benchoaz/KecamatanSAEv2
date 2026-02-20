# Cara Setup WAHA Webhook dengan Benar

## ⚠️ Masalah yang Ditemukan (SEBELUM PERBAIKAN)

URL webhook yang lama terkonfigurasi:
```
http://localhost:8080/webhook/webhook-test/whatsapp-bot
```

**Masalah:**
1. Menggunakan `/webhook/*` yang TIDAK dikonfigurasi di nginx
2. nginx tidak memiliki location `/webhook/*` - jadi request ke `/webhook/*` diteruskan ke dashboard-kecamatan, bukan ke n8n
3. Port 8080 adalah nginx gateway yang meneruskan ke dashboard-kecamatan

---

## ✅ Arsitektur yang Benar

```
┌─────────────────────────────────────────────────────────────┐
│                     docker-compose network                    │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│   WAHA Container          nginx Gateway           n8n       │
│   (waha)        →   (gateway-nginx:80)    →   (n8n:5678)   │
│                         :443/:80                              │
│                         Port 8080                            │
│                                                              │
│   WAHA call:                                            n8n  │
│   http://gateway-nginx:80/webhook/whatsapp-incoming ←─┘   │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

**nginx configuration:**
- `/` → dashboard-kecamatan (port 80)
- `/webhook/*` → n8n (port 5678, path `/webhook/*`)
- `/n8n/*` → n8n UI (port 5678)
- `/api/whatsapp/*` → whatsapp-api-gateway (port 8001)

---

## ✅ Solusi: Setup Ulang WAHA Webhook

### Langkah 1: Hapus Webhook Lama

```bash
# Hapus semua webhooks lama
curl -X DELETE http://localhost:3099/api/webhooks

# Atau hapus satu per satu
curl http://localhost:3099/api/webhooks
# Copy webhook ID dan hapus
curl -X DELETE http://localhost:3099/api/webhooks/{webhook_id}
```

### Langkah 2: Setup Webhook Baru dengan URL Benar

**PENTING:** Gunakan URL internal Docker network ke nginx gateway!

```bash
# Setup webhook dengan URL nginx gateway (yang forward ke n8n)
curl -X POST http://localhost:3099/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://gateway-nginx:80/webhook/whatsapp-incoming",
    "events": ["message"],
    "session": "default"
  }'
```

### Langkah 3: Verifikasi Webhook

```bash
# Cek webhook yang terdaftar
curl http://localhost:3099/api/webhooks

# Expected response:
# [
#   {
#     "id": "xxx",
#     "url": "http://gateway-nginx:80/webhook/whatsapp-incoming",
#     "events": ["message"],
#     "session": "default"
#   }
# ]
```

---

## 📝 Penjelasan URL

| URL | Keterangan |
|-----|------------|
| `http://gateway-nginx:80/webhook/whatsapp-incoming` | ✅ Benar - URL internal ke nginx gateway |
| `http://n8n:5678/webhook/whatsapp-incoming` | ⚠️ Tidak Langsung - bypass nginx |
| `http://localhost:8080/webhook/whatsapp-incoming` | ✅ Benar - dari host machine |
| `http://waha:3099/api/sendText` | ✅ Benar - URL internal Docker |

**Flow request:**
1. WAHA mengirim webhook ke `http://gateway-nginx:80/webhook/whatsapp-incoming`
2. nginx menerima request dan meneruskan ke `http://n8n:5678/webhook/whatsapp-incoming`
3. n8n menerima dan memproses webhook

---

## 🔄 Setup via WAHA UI

### Lewat WAHA Dashboard:

1. Buka WAHA Dashboard: `http://localhost:3099/dashboard/workers`
2. Pilih session "default"
3. Klik tab **"Webhooks"**
4. Klik **"Add Webhook"**
5. Isi form:
   - **URL**: `http://gateway-nginx:80/webhook/whatsapp-incoming`
   - **Events**: Centang `message`
6. Klik **"Save"**

### Lewat WAHA API:

```bash
# Setup webhook
curl -X POST http://localhost:3099/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://gateway-nginx:80/webhook/whatsapp-incoming",
    "events": ["message"],
    "session": "default"
  }'

# Verify
curl http://localhost:3099/api/webhooks
```

---

## 🧪 Test Webhook

### Test dengan curl (simulasi WAHA):

```bash
# Kirim test message ke webhook melalui nginx
curl -X POST http://localhost:8080/webhook/whatsapp-incoming \
  -H "Content-Type: application/json" \
  -d '{
    "from": "6281234567890@c.us",
    "text": "/help",
    "name": "Test User",
    "timestamp": "1707651234"
  }'

# Expected response:
# {"status": "success", "message": "Message processed"}
```

### Test konektivitas:

```bash
# Test dari container WAHA ke nginx
docker exec waha-1 curl -v http://gateway-nginx:80/health

# Test dari nginx ke n8n
docker exec gateway-nginx curl -v http://n8n:5678/healthz

# Test dari n8n ke WAHA
docker exec n8n-1 curl -X POST http://waha:3099/api/sessions
```

---

## 📋 Checklist Setup

- [ ] Hapus webhook lama: `curl -X DELETE http://localhost:3099/api/webhooks`
- [ ] Setup webhook baru dengan URL: `http://gateway-nginx:80/webhook/whatsapp-incoming`
- [ ] Pilih event: `message`
- [ ] Pilih session: `default`
- [ ] Verifikasi dengan `curl http://localhost:3099/api/webhooks`
- [ ] Restart nginx: `docker restart gateway-nginx`
- [ ] Test dengan mengirim pesan ke WhatsApp
- [ ] Cek n8n executions

---

## 🔍 Jika Masih Tidak Berfungsi

### Cek nginx logs:

```bash
docker logs gateway-nginx --tail 100 -f
```

### Cek n8n logs:

```bash
docker logs n8n-1 --tail 100 -f
```

### Cek WAHA logs:

```bash
docker logs waha-1 --tail 100 -f
```

### Test konektivitas:

```bash
# Test dari container n8n
docker exec n8n-1 curl http://gateway-nginx:80/health

# Test dari container waha
docker exec waha-1 curl http://gateway-nginx:80/health

# Test koneksi n8n ke WAHA
docker exec n8n-1 curl http://waha:3099/api/sessions
```

---

## 📞 Support

Jika masih mengalami masalah:
1. Kirim output dari `curl http://localhost:3099/api/webhooks`
2. Kirim screenshot n8n executions
3. Kirim output dari `docker logs gateway-nginx --tail 50`
