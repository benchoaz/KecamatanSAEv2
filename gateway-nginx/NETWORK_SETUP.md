# ⚠️ CATATAN PENTING - Setup Network

## 🔸 1. Buat External Network (SEKALI SAJA)

Sebelum start gateway pertama kali, buat network:

```bash
docker network create gateway-net
```

Cek network sudah dibuat:

```bash
docker network ls | grep gateway-net
```

## 🔸 2. Hubungkan Container Existing

### Dashboard Kecamatan

Edit `dashboard-kecamatan/docker-compose.yml`:

```yaml
services:
  nginx:
    # ... existing config ...
    container_name: dashboard-nginx  # PENTING: pastikan nama ini
    networks:
      - default
      - gateway-net

networks:
  default:
  gateway-net:
    external: true
```

Restart:
```bash
cd d:\Projectku\dashboard-kecamatan
docker-compose down
docker-compose up -d
```

### WhatsApp Stack

Edit `whatsapp/docker-compose.yml`:

```yaml
services:
  whatsapp-api:
    container_name: whatsapp-api  # PENTING: pastikan nama ini
    # ... existing config ...
    networks:
      - whatsapp-network
      - gateway-net

  n8n:
    container_name: n8n  # PENTING: pastikan nama ini
    # ... existing config ...
    networks:
      - whatsapp-network
      - gateway-net

networks:
  whatsapp-network:
    driver: bridge
  gateway-net:
    external: true
```

Restart:
```bash
cd d:\Projectku\whatsapp
docker-compose down
docker-compose up -d
```

## 🔸 3. Verifikasi Network

Check semua container sudah join `gateway-net`:

```bash
docker network inspect gateway-net
```

Expected containers:
- `dashboard-nginx`
- `whatsapp-api`
- `n8n`
- `nginx-gateway` (setelah start gateway)

## 🔸 4. Troubleshooting

### Container tidak bisa komunikasi

**Problem**: 502 Bad Gateway

**Check**:
```bash
# List containers in gateway-net
docker network inspect gateway-net --format '{{range .Containers}}{{.Name}} {{end}}'

# Check container names
docker ps --format "table {{.Names}}\t{{.Networks}}"
```

**Fix**: Update nama container di `nginx/conf.d/default.conf` sesuai dengan nama actual.

### Network sudah ada tapi connection refused

**Problem**: External network exist but can't connect

**Fix**:
```bash
# Remove and recreate network
docker network rm gateway-net
docker network create gateway-net

# Restart all services
cd d:\Projectku\dashboard-kecamatan && docker-compose restart
cd d:\Projectku\whatsapp && docker-compose restart
cd d:\Projectku\gateway-nginx && docker-compose restart
```

## 🔸 5. Container Names Reference

Pastikan nama container di `default.conf` sesuai:

| Service | Expected Container Name | Used in nginx config |
|---------|------------------------|----------------------|
| Dashboard NGINX | `dashboard-nginx` | `http://dashboard-nginx:80` |
| WhatsApp API | `whatsapp-api` | `http://whatsapp-api:8001` |
| n8n | `n8n` | `http://n8n:5678` |

Jika nama beda, edit `nginx/conf.d/default.conf`.
