# Port 80 Permission Issue - SOLVED

## Problem

Windows restricts port 80 for security reasons. Error:
```
listen tcp 0.0.0.0:80: bind: An attempt was made to access a socket in a way forbidden by its access permissions.
```

## Solution

Gateway sekarang menggunakan **port 8080** instead of 80.

## New Access URLs

| Service | Old URL | New URL |
|---------|---------|---------|
| Health Check | `http://localhost/health` | `http://localhost:8080/health` |
| Dashboard | `http://localhost/` | `http://localhost:8080/` |
| WhatsApp API | `http://localhost/api/whatsapp` | `http://localhost:8080/api/whatsapp` |
| n8n | `http://localhost/n8n` | `http://localhost:8080/n8n` |

## Restart Gateway

```bash
cd d:\Projectku\gateway-nginx
docker-compose up -d
```

## Test

```bash
curl http://localhost:8080/health
```

Expected: `OK - NGINX GATEWAY`

## For Production (VPS)

Pada VPS Linux, port 80 bisa digunakan. Ubah kembali di `docker-compose.yml`:
```yaml
ports:
  - "80:80"
```
