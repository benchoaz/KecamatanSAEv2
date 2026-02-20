# Konfigurasi Environment untuk WhatsApp Bot Pelayanan

## Dashboard-Kecamatan (.env)

Tambahkan konfigurasi berikut ke file `.env` di dashboard-kecamatan:

```bash
# WhatsApp Integration
N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply

# API Token untuk WhatsApp Integration
# Generate token yang kuat dan unik
WHATSAPP_API_TOKEN=your_strong_api_token_here_change_this
```

## WhatsApp Laravel API (.env)

Pastikan konfigurasi berikut ada di file `.env` di `whatsapp/laravel-api/`:

```bash
# Dashboard API Configuration
DASHBOARD_API_URL=http://dashboard-kecamatan:8000
DASHBOARD_API_TOKEN=your_strong_api_token_here_change_this

# App Configuration
APP_DEBUG=false
APP_ENV=production
```

## N8N Environment Variables

Tambahkan environment variables berikut di konfigurasi n8n:

```bash
# Dashboard API Token (sama dengan dashboard-kecamatan)
DASHBOARD_API_TOKEN=your_strong_api_token_here_change_this

# WAHA Configuration
WAHA_URL=http://waha:3000
```

## Docker Compose Configuration

Pastikan service berikut terhubung dengan benar di `docker-compose.yml`:

```yaml
services:
  dashboard-kecamatan:
    # ... existing config ...
    environment:
      - N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply
    networks:
      - app-network

  whatsapp-api-gateway:
    # ... existing config ...
    environment:
      - DASHBOARD_API_URL=http://dashboard-kecamatan:8000
      - DASHBOARD_API_TOKEN=${DASHBOARD_API_TOKEN}
    networks:
      - app-network

  n8n:
    # ... existing config ...
    environment:
      - DASHBOARD_API_TOKEN=${DASHBOARD_API_TOKEN}
      - WAHA_URL=http://waha:3000
    networks:
      - app-network

  waha:
    # ... existing config ...
    networks:
      - app-network

networks:
  app-network:
    driver: bridge
```

## API Token Generation

Generate API token yang kuat menggunakan salah satu metode berikut:

### Method 1: Using OpenSSL (Linux/Mac)
```bash
openssl rand -base64 32
```

### Method 2: Using PHP
```php
<?php
echo bin2hex(random_bytes(32));
?>
```

### Method 3: Using Node.js
```bash
node -e "console.log(require('crypto').randomBytes(32).toString('base64'))"
```

## Webhook URLs

Setelah deployment, pastikan webhook URL berikut dapat diakses:

| Service | Webhook URL | Purpose |
|---------|-------------|---------|
| n8n | `http://n8n:5678/webhook/whatsapp-bot` | Menerima pesan dari WAHA |
| n8n | `http://n8n:5678/webhook/dashboard-reply` | Menerima reply dari Dashboard |
| Laravel API | `http://whatsapp-api-gateway:8001/api/webhook` | Menerima data dari n8n |
| Laravel API | `http://whatsapp-api-gateway:8001/api/faq/search` | FAQ search endpoint |
| Laravel API | `http://whatsapp-api-gateway:8001/api/status/check` | Status check endpoint |
| Laravel API | `http://whatsapp-api-gateway:8001/api/reply/send` | Reply send endpoint |

## Testing Configuration

### 1. Test Dashboard API Connection

```bash
curl -X GET http://dashboard-kecamatan:8000/api/reply/status \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

Expected response:
```json
{
  "success": true,
  "service": "whatsapp-reply",
  "configured": true,
  "webhook_url": "***configured***",
  "timestamp": "2026-02-11T14:00:00.000Z"
}
```

### 2. Test FAQ Search

```bash
curl -X GET "http://whatsapp-api-gateway:8001/api/faq/search?q=jam%20pelayanan" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### 3. Test Status Check

```bash
curl -X GET "http://whatsapp-api-gateway:8001/api/status/check?identifier=YOUR_UUID_OR_PHONE" \
  -H "Authorization: Bearer YOUR_API_TOKEN"
```

### 4. Test WhatsApp Reply

```bash
curl -X POST http://dashboard-kecamatan:8000/api/reply/send \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "phone": "6281234567890",
    "message": "Test pesan dari Dashboard",
    "type": "manual_reply"
  }'
```

## Troubleshooting

### Issue: N8N_REPLY_WEBHOOK_URL not configured

**Error**: `N8N_REPLY_WEBHOOK_URL not configured`

**Solution**:
1. Pastikan `N8N_REPLY_WEBHOOK_URL` ada di `.env` dashboard-kecamatan
2. Restart container dashboard-kecamatan
3. Verifikasi dengan test connection endpoint

### Issue: Connection refused to n8n

**Error**: `Connection error: Unable to reach n8n service`

**Solution**:
1. Pastikan n8n service berjalan: `docker ps | grep n8n`
2. Pastikan network sama: `docker network inspect app-network`
3. Cek firewall rules

### Issue: API Token invalid

**Error**: `401 Unauthorized` atau `403 Forbidden`

**Solution**:
1. Pastikan `DASHBOARD_API_TOKEN` sama di semua service
2. Restart semua service setelah mengubah token
3. Cek header Authorization: `Bearer YOUR_TOKEN`

### Issue: WAHA not responding

**Error**: `Failed to send WhatsApp reply via n8n`

**Solution**:
1. Cek status WAHA: `curl http://waha:3000/api/sessions`
2. Pastikan WAHA session aktif
3. Restart WAHA jika perlu

## Security Best Practices

1. **API Token**: Gunakan token yang kuat (minimal 32 karakter)
2. **Environment Variables**: Jangan commit `.env` ke version control
3. **HTTPS**: Gunakan HTTPS untuk production
4. **Rate Limiting**: Implement rate limiting untuk API endpoints
5. **Logging**: Log semua request untuk audit trail
6. **IP Whitelist**: Batasi akses API ke IP tertentu jika memungkinkan

## Monitoring

Monitor service berikut untuk memastikan integrasi berjalan lancar:

| Service | Health Check | Log Location |
|---------|--------------|--------------|
| Dashboard-Kecamatan | `/api/health` | `storage/logs/laravel.log` |
| WhatsApp API Gateway | `/api/health` | `storage/logs/laravel.log` |
| n8n | `/healthz` | n8n UI → Executions |
| WAHA | `/api/sessions` | WAHA logs |

## Backup Configuration

Backup konfigurasi berikut secara berkala:

1. `.env` files (tanpa sensitive data)
2. n8n workflow exports
3. Database backup (termasuk `public_services` dan `pelayanan_faqs`)
4. API tokens (simpan di password manager)
