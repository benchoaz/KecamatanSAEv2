#!/bin/bash
# =============================================================
# UPDATE SCRIPT - Dashboard Kecamatan
# Jalankan di VPS untuk menarik update terbaru dari GitHub
# Cara pakai: bash update-vps.sh
# =============================================================

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}🔄 Sedang memperbarui aplikasi...${NC}"

# 1. Pull latest code
echo -e "${YELLOW}→ Menarik kode terbaru dari GitHub...${NC}"
git pull origin main

# 2. Update containers if needed
echo -e "${YELLOW}→ Membangun ulang container (jika ada perubahan Docker)...${NC}"
docker compose build app
docker compose up -d

# 3. Laravel Updates
echo -e "${YELLOW}→ Menjalankan migrasi database & bersihkan cache...${NC}"
docker compose exec -T app php artisan migrate --force
docker compose exec -T app php artisan config:cache
docker compose exec -T app php artisan route:cache
docker compose exec -T app php artisan view:cache

echo -e "${GREEN}✅ Update Berhasil! Aplikasi Anda kini sudah di versi terbaru.${NC}"
echo -e "${BLUE}Cek status: docker compose ps${NC}"
