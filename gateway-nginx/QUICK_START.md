# Gateway NGINX - Quick Reference

## 🚀 Start Gateway

```bash
cd d:\Projectku\gateway-nginx
docker-compose up -d
```

## ✅ Check Status

```bash
docker ps | grep nginx-gateway
docker logs nginx-gateway
```

## 🧪 Test Endpoints

```bash
# Health check
curl http://localhost/health

# Dashboard
curl -I http://localhost/

# WhatsApp API
curl http://localhost/api/whatsapp/health

# n8n (browser)
http://localhost/n8n/
```

## 🔄 Reload Config

```bash
docker exec nginx-gateway nginx -t
docker exec nginx-gateway nginx -s reload
```

## 🛑 Stop Gateway

```bash
docker-compose down
```

## 📊 View Logs

```bash
# All logs
docker logs -f nginx-gateway

# Access log only
docker exec nginx-gateway tail -f /var/log/nginx/access.log

# Error log only
docker exec nginx-gateway tail -f /var/log/nginx/error.log
```

## 🔧 Troubleshooting

### 502 Bad Gateway
```bash
# Check network
docker network inspect gateway-net

# Verify container names
docker ps --format "{{.Names}}"
```

### Config Error
```bash
# Test config
docker exec nginx-gateway nginx -t

# Check syntax
cat nginx/conf.d/default.conf
```
