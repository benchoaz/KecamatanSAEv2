#!/bin/bash
set -e

# ============================================================
# SILAP System Setup & Deployment Script (Anti-Error Version)
# ============================================================

echo "🏛️  SILAP System - Robust Setup Wizard"
echo "----------------------------------------"

# 1. Pre-flight Checks
chmod +x scripts/pre-flight.sh
./scripts/pre-flight.sh

# 2. Setup Environment (Idempotent)
echo "📝 Preparing environment files..."
[ ! -f .env ] && cp .env.example .env && echo "✅ Root .env created."
[ ! -f app/.env ] && cp app/.env.example app/.env && echo "✅ App .env created."

# 3. Safe Pull & Build
echo "🏗️  Pulling images sequentially (safe mode)..."
services=("traefik:v2.10" "postgres:17-alpine" "redis:7-alpine" "nginx:alpine" "node:18-alpine" "php:8.1-fpm-alpine")
for img in "${services[@]}"; do
    docker pull $img || echo "⚠️ Warning: Failed to pull $img, will try building."
done

echo "🏗️  Building system components..."
docker compose build --pull

# 4. Start System with Retry
echo "🚀 Starting services..."
MAX_RETRIES=3
COUNT=0
until docker compose up -d || [ $COUNT -eq $MAX_RETRIES ]; do
    echo "⚠️ Failed to start. Retrying ($((++COUNT))/$MAX_RETRIES)..."
    sleep 5
done

if [ $COUNT -eq $MAX_RETRIES ]; then
    echo "❌ ERROR: System failed to start after $MAX_RETRIES retries."
    exit 1
fi

# 5. Initialize App (Laravel Hardening)
echo "⚙️  Initializing Laravel Core..."
# Fix permissions first
docker exec kecamatan-app chown -R www-data:www-data storage bootstrap/cache
docker exec kecamatan-app chmod -R 775 storage bootstrap/cache

# Generate key if empty
APP_KEY=$(grep APP_KEY app/.env | cut -d '=' -f2)
if [ -z "$APP_KEY" ]; then
    docker exec kecamatan-app php artisan key:generate --ansi
fi

# Optimize & Migrate
docker exec kecamatan-app php artisan optimize:clear
docker exec kecamatan-app php artisan migrate --force

# 6. Cleanup
echo "🧹 Cleaning up old images and cache..."
docker system prune -f --volumes || true

echo "----------------------------------------"
echo "✅ DEPLOYMENT SUCCESSFUL!"
echo "🌐 Your system is running and hardened."
echo "👉 Use './scripts/check-status.sh' to verify."
