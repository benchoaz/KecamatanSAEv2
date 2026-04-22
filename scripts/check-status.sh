#!/bin/bash

# ============================================================
# SILAP System - Quick Debug & Status Checker
# ============================================================

echo "📊 SILAP System Status Report"
echo "--------------------------------"

# 1. Container Status
echo "📦 1. Docker Containers:"
docker compose ps
echo ""

# 2. Database Connection Test
echo "🗄️ 2. Database Connectivity:"
if docker exec kecamatan-app php artisan db:show > /dev/null 2>&1; then
    echo "✅ Database connection: SUCCESS"
else
    echo "❌ Database connection: FAILED"
    docker logs kecamatan-db --tail 20
fi
echo ""

# 3. Redis Connection Test
echo "🧠 3. Redis Connectivity:"
if docker exec kecamatan-redis redis-cli ping | grep -q "PONG"; then
    echo "✅ Redis connection: SUCCESS"
else
    echo "❌ Redis connection: FAILED"
fi
echo ""

# 4. Traefik / Network Check
echo "🌐 4. Network & Traefik:"
if docker ps | grep -q "traefik-gateway"; then
    echo "✅ Traefik Gateway is running."
else
    echo "❌ Traefik Gateway is DOWN."
fi
echo ""

# 5. Laravel Logs (Last 10 lines)
echo "📜 5. Recent Laravel Logs:"
docker exec kecamatan-app tail -n 10 storage/logs/laravel.log 2>/dev/null || echo "No logs found."
echo ""

# 6. Disk Usage
echo "💾 6. Disk Usage:"
df -h . | awk 'NR==1 || NR==2'
echo ""

echo "--------------------------------"
echo "💡 Tip: Use 'docker compose logs -f [service_name]' for real-time logs."
