# NGINX Gateway untuk Kecamatan Besuk

Reverse proxy gateway sebagai single entry point untuk semua service (Dashboard, WhatsApp, n8n).

## 🏗️ Arsitektur

```
Browser/Client
      ↓
NGINX Gateway (Port 80)
      ↓
┌──────────────┬───────────────┬──────────────┐
│ Dashboard    │ WhatsApp API  │ n8n          │
│ (Laravel)    │ (Gateway)     │ (Automation) │
│ :80          │ :8001         │ :5678        │
└──────────────┴───────────────┴──────────────┘
```

## 📋 Prerequisites

1. **Docker & Docker Compose** sudah terinstall
2. **Existing services** sudah berjalan:
   - dashboard-kecamatan
   - whatsapp automation stack (WAHA, n8n, whatsapp-api)

## 🚀 Setup (PERTAMA KALI)

### 1. Buat External Network

```bash
docker network create gateway-net
```

> **PENTING**: Network ini akan digunakan untuk komunikasi antar container.

### 2. Hubungkan Existing Services ke Network

Tambahkan `gateway-net` ke compose file existing services:

#### Dashboard Kecamatan
Edit `dashboard-kecamatan/docker-compose.yml`:
```yaml
services:
  nginx:
    # ... existing config ...
    networks:
      - default
      - gateway-net

networks:
  gateway-net:
    external: true
```

#### WhatsApp Stack
Edit `whatsapp/docker-compose.yml`:
```yaml
services:
  whatsapp-api:
    # ... existing config ...
    networks:
      - whatsapp-network
      - gateway-net

  n8n:
    # ... existing config ...
    networks:
      - whatsapp-network
      - gateway-net

networks:
  gateway-net:
    external: true
```

### 3. Restart Existing Services

```bash
# Dashboard
cd d:\Projectku\dashboard-kecamatan
docker-compose down
docker-compose up -d

# WhatsApp
cd d:\Projectku\whatsapp
docker-compose down
docker-compose up -d
```

### 4. Start NGINX Gateway

```bash
cd d:\Projectku\gateway-nginx
docker-compose up -d
```

## ✅ Verifikasi

### 1. Check Container Status

```bash
docker ps | grep nginx-gateway
```

Expected: Container running

### 2. Test Health Check

```bash
curl http://localhost/health
```

Expected: `OK - NGINX GATEWAY`

### 3. Test Dashboard

```bash
curl -I http://localhost/
```

Expected: HTTP 200, redirects to dashboard

### 4. Test WhatsApp API

```bash
curl http://localhost/api/whatsapp/health
```

Expected: JSON response from whatsapp-api

### 5. Test n8n

Open browser: `http://localhost/n8n/`

Expected: n8n UI loads

## 🔀 Routing Table

| URL Path | Target Service | Internal Address | Purpose |
|----------|---------------|------------------|---------|
| `/` | Dashboard Kecamatan | `dashboard-nginx:80` | Main Laravel app |
| `/api/whatsapp/*` | WhatsApp API | `whatsapp-api:8001` | WhatsApp webhook & API |
| `/n8n/*` | n8n | `n8n:5678` | Workflow automation |
| `/health` | NGINX | - | Gateway health check |

## 🛠️ Troubleshooting

### Gateway tidak bisa connect ke service

**Problem**: 502 Bad Gateway

**Solution**:
1. Cek nama container benar:
   ```bash
   docker ps --format "{{.Names}}"
   ```
2. Pastikan semua service sudah join `gateway-net`:
   ```bash
   docker network inspect gateway-net
   ```
3. Edit `nginx/conf.d/default.conf` jika nama container beda

### Port 80 sudah dipakai

**Problem**: Port conflict

**Solution**:
1. Stop service yang pakai port 80
2. Atau ubah port di `docker-compose.yml`:
   ```yaml
   ports:
     - "8080:80"  # Akses via localhost:8080
   ```

### n8n websocket tidak konek

**Problem**: Workflow tidak update real-time

**Solution**:
Sudah dikonfigurasi dengan:
```nginx
proxy_set_header Upgrade $http_upgrade;
proxy_set_header Connection "upgrade";
```

Jika masih issue, restart nginx:
```bash
docker-compose restart
```

## 📊 Logs

### View NGINX Logs

```bash
docker logs -f nginx-gateway
```

### View Access Log

```bash
docker exec nginx-gateway tail -f /var/log/nginx/access.log
```

### View Error Log

```bash
docker exec nginx-gateway tail -f /var/log/nginx/error.log
```

## 🔐 Security Features

✅ **Headers Added**:
- `X-Real-IP` - Client IP address
- `X-Forwarded-For` - Proxy chain
- `X-Forwarded-Proto` - HTTP/HTTPS protocol
- `Host` - Original host header

✅ **Limits**:
- Max upload: 10MB (`client_max_body_size`)
- Server tokens hidden (`server_tokens off`)

✅ **Not Exposed**:
- WAHA (port 3001) - Internal only
- MySQL database - Not accessible
- Internal service ports (8000, 8001, 5678)

## 🚀 Production Deployment

### Add SSL/TLS (Let's Encrypt)

1. Install certbot in gateway container or use certbot docker
2. Get certificate:
   ```bash
   certbot certonly --webroot -w /var/www/html -d yourdomain.com
   ```
3. Update `default.conf`:
   ```nginx
   server {
       listen 443 ssl http2;
       ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
       ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
   }
   ```
4. Add HTTP to HTTPS redirect:
   ```nginx
   server {
       listen 80;
       return 301 https://$host$request_uri;
   }
   ```

### Enable Rate Limiting

Add to `nginx.conf`:
```nginx
http {
    limit_req_zone $binary_remote_addr zone=general:10m rate=10r/s;
    
    # In server block
    limit_req zone=general burst=20 nodelay;
}
```

## 🔄 Update & Maintenance

### Reload Configuration (no downtime)

```bash
docker exec nginx-gateway nginx -s reload
```

### Update nginx.conf

```bash
# Edit file
nano nginx/nginx.conf

# Test config
docker exec nginx-gateway nginx -t

# Reload
docker exec nginx-gateway nginx -s reload
```

### Restart Gateway

```bash
docker-compose restart
```

## 📝 Configuration Files

- **[docker-compose.yml](file:///d:/Projectku/gateway-nginx/docker-compose.yml)** - Main compose file
- **[nginx/nginx.conf](file:///d:/Projectku/gateway-nginx/nginx/nginx.conf)** - Core nginx config
- **[nginx/conf.d/default.conf](file:///d:/Projectku/gateway-nginx/nginx/conf.d/default.conf)** - Routing rules

## 🎯 Benefits

✅ **Single Entry Point** - All services via port 80
✅ **Clean URLs** - Path-based routing
✅ **Security** - Headers, limits, hidden internal ports
✅ **Scalability** - Easy to add new services
✅ **Production Ready** - SSL-ready, logging enabled
✅ **No Breaking Changes** - Existing services untouched

## 📞 Support

Jika ada masalah:
1. Check logs (see Logs section)
2. Verify network: `docker network inspect gateway-net`
3. Test config: `docker exec nginx-gateway nginx -t`
4. Restart: `docker-compose restart`
