# Container Names Reference

Update this if container names change in docker-compose files.

## Dashboard Stack
- **Container**: `dashboard-kecamatan-nginx`
- **Used in**: `default.conf` → `proxy_pass http://dashboard-kecamatan-nginx:80`

## WhatsApp Stack
- **Container**: `whatsapp-api-gateway`
- **Used in**: `default.conf` → `proxy_pass http://whatsapp-api-gateway:8001`

- **Container**: `n8n-kecamatan`
- **Used in**: `default.conf` → `proxy_pass http://n8n-kecamatan:5678`

- **Container**: `waha-kecamatan` (NOT exposed via gateway)

## Verify Container Names

```bash
docker ps --format "table {{.Names}}\t{{.Ports}}"
```

Expected:
- dashboard-kecamatan-nginx (8000->80)
- whatsapp-api-gateway (8001->8001)
- n8n-kecamatan (5678->5678)
- waha-kecamatan (3001->3000)
