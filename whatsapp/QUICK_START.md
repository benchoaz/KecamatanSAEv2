# WhatsApp Automation - Quick Reference

## 🚀 Start System

```bash
cd d:\Projectku\whatsapp
docker-compose up -d
```

## ✅ Check Status

```bash
# All containers
docker-compose ps

# Health check
curl http://localhost:3001    # WAHA
curl http://localhost:5678    # n8n  
curl http://localhost:8001/api/health    # Laravel API
```

## 📱 Connect WhatsApp

1. GET `http://localhost:3001/api/sessions/default/qr` → Scan with WhatsApp
2. Or check docker logs: `docker logs waha-kecamatan`

## 🔄 Restart Services

```bash
docker-compose restart
```

## 📊 View Logs

```bash
# WAHA logs
docker logs -f waha-kecamatan

# n8n logs
docker logs -f n8n-kecamatan

# Laravel API logs
cat laravel-api/storage/logs/transactions-*.log
```

## 🛑 Stop System

```bash
docker-compose down
```

## 🔧 Rebuild (if changed code)

```bash
docker-compose up -d --build
```
