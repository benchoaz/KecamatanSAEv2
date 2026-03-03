#!/bin/bash
# Copy-Paste Commands for Google Cloud Shell Deployment
# This file contains all commands needed to deploy KECAMATAN-LAYANAN-WHATSAPP

# ============================================================================
# 🚀 QUICKEST START (Just Copy & Paste Everything Below)
# ============================================================================

# Step 1: Clone Repository
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP

# Step 2: Run Deployment
bash cloudshell-quickstart.sh

# That's it! Wait 5 minutes and your app is ready.
# ============================================================================


# ============================================================================
# 📋 MANUAL DEPLOYMENT (if you want step-by-step control)
# ============================================================================

# Clone the repo
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git
cd KECAMATAN-LAYANAN-WHATSAPP

# Create .env if it doesn't exist
[ ! -f .env ] && cp .env.example .env

# Start services
docker-compose down -v 2>/dev/null || true
sleep 2
docker-compose pull
sleep 2
docker-compose up -d

# Wait for MySQL
echo "Waiting for MySQL..."
for i in {1..30}; do
  if docker-compose exec -T db mysqladmin ping -u root -proot &>/dev/null; then
    echo "MySQL is ready!"
    break
  fi
  echo "Waiting... ($i/30)"
  sleep 2
done

# Run migrations
docker-compose exec -T app php artisan config:cache
docker-compose exec -T app php artisan migrate --force

# View status
docker-compose ps

# ============================================================================


# ============================================================================
# 🔧 USEFUL COMMANDS AFTER DEPLOYMENT
# ============================================================================

# View all services and their status
docker-compose ps

# View real-time logs (all services)
docker-compose logs -f

# View logs for specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f db
docker-compose logs -f n8n
docker-compose logs -f waha

# Restart all services
docker-compose restart

# Restart specific service
docker-compose restart app
docker-compose restart db

# Stop all services (data persists)
docker-compose stop

# Start services again
docker-compose start

# Stop and remove everything
docker-compose down

# Stop and remove everything + delete all data
docker-compose down -v

# ============================================================================


# ============================================================================
# 📊 DATABASE COMMANDS
# ============================================================================

# Run migrations
docker-compose exec app php artisan migrate

# Run seeds
docker-compose exec app php artisan db:seed

# Clear cache
docker-compose exec app php artisan cache:clear

# Clear config
docker-compose exec app php artisan config:clear

# Access database directly
docker-compose exec db mysql -u root -proot dashboard_kecamatan

# Backup database
docker-compose exec -T db mysqldump -u root -proot dashboard_kecamatan > backup.sql

# Restore database
docker-compose exec -T db mysql -u root -proot dashboard_kecamatan < backup.sql

# ============================================================================


# ============================================================================
# 🎯 APPLICATION COMMANDS
# ============================================================================

# Access application shell
docker-compose exec app bash

# Run Artisan tinker (Laravel REPL)
docker-compose exec app php artisan tinker

# Generate app key (if needed)
docker-compose exec app php artisan key:generate

# Publish config
docker-compose exec app php artisan config:cache

# Clear all caches
docker-compose exec app php artisan cache:clear && \
docker-compose exec app php artisan config:clear && \
docker-compose exec app php artisan view:clear

# Run tests
docker-compose exec app php artisan test

# ============================================================================


# ============================================================================
# 🐛 TROUBLESHOOTING COMMANDS
# ============================================================================

# Check if Docker is running
docker info

# View resource usage
docker stats

# Check disk space
df -h
docker system df

# View all networks
docker network ls

# View specific network
docker network inspect dashboard-kecamatan_app-network

# Check container details
docker inspect dashboard-kecamatan-app
docker inspect dashboard-kecamatan-nginx
docker inspect dashboard-kecamatan-db

# View stopped containers
docker ps -a

# View recently exited containers
docker ps -a --filter status=exited

# Check port usage
sudo netstat -tulpn | grep LISTEN
sudo lsof -i :8000

# Kill process using port
sudo kill -9 <PID>

# Clean up unused images
docker system prune -a

# ============================================================================


# ============================================================================
# 🔄 FULL RESTART (if something goes wrong)
# ============================================================================

# Stop everything and remove all containers
docker-compose down -v

# Clean up system
docker system prune -a

# Pull fresh images
docker-compose pull

# Start fresh
docker-compose up -d

# Check status
docker-compose ps

# View logs
docker-compose logs -f

# ============================================================================


# ============================================================================
# 📍 SERVICE URLS (After deployment)
# ============================================================================

# Main Application
# http://localhost:8000
# Or use Web Preview → Port 8000

# n8n Workflows
# http://localhost:5679

# WAHA WhatsApp API
# http://localhost:3000

# Database (from application)
# Host: db
# Port: 3306
# User: root
# Pass: root

# Database (from Cloud Shell host)
# Host: localhost
# Port: 3307
# User: root
# Pass: root

# ============================================================================


# ============================================================================
# 📄 DOCUMENTATION FILES
# ============================================================================

# Quick reference (one page)
cat CLOUDSHELL_QUICKREF.md

# Full deployment guide
cat CLOUD_SHELL_GUIDE.md

# Architecture and data flow
cat ARCHITECTURE.md

# Deployment complete summary
cat DEPLOYMENT_COMPLETE.md

# Start here overview
cat 00_START_HERE.md

# ============================================================================


# ============================================================================
# 🎓 TIPS & TRICKS
# ============================================================================

# Monitor deployment in real-time
watch 'docker-compose ps'

# See logs as they appear
docker-compose logs -f --tail=50

# Quick health check
docker-compose exec app php-fpm -t && echo "✅ PHP OK"
docker-compose exec db mysqladmin ping -u root -proot && echo "✅ MySQL OK"
docker-compose exec nginx nginx -t && echo "✅ Nginx OK"

# Total resource usage
echo "=== Container Stats ===" && docker stats --no-stream
echo "=== Disk Usage ===" && docker system df

# ============================================================================

# 🎉 All done! Your app is running in Google Cloud Shell.
# Access it at: http://localhost:8000
# Or use Web Preview → Port 8000
