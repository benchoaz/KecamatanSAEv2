#!/bin/bash
# Quick start script for Cloud Shell - copy and paste this entire block into Cloud Shell terminal

set -e

echo "🚀 KECAMATAN-LAYANAN-WHATSAPP - Cloud Shell Quick Start"
echo "======================================================"
echo ""

# Step 1: Clone repo
echo "📥 Cloning repository..."
cd ~
git clone https://github.com/benchoaz/KECAMATAN-LAYANAN-WHATSAPP.git || (cd KECAMATAN-LAYANAN-WHATSAPP && git pull)
cd KECAMATAN-LAYANAN-WHATSAPP

# Step 2: Setup environment
echo "⚙️  Setting up environment..."
if [ ! -f .env ]; then
    if [ -f .env.example ]; then
        cp .env.example .env
    else
        echo "Creating default .env..."
        cat > .env << 'ENVFILE'
APP_NAME="Kecamatan SAE"
APP_ENV=local
APP_KEY=base64:W5WQ9EUDLKZCRaMX33HayaMsn7KCGMRkR6YP89Q8sWk=
APP_DEBUG=true
APP_URL=http://localhost:8000
PUBLIC_BASE_URL=http://localhost:8000

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=dashboard_kecamatan
DB_USERNAME=root
DB_PASSWORD=root

BROADCAST_DRIVER=log
CACHE_DRIVER=array
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=cookie
SESSION_LIFETIME=15

MEMCACHED_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

WHATSAPP_API_TOKEN=62a72516dd1b418499d9dd22075ccfa0
WAHA_API_URL=http://waha-kecamatan:3000
WAHA_API_KEY=waha_secret_key_2024
WAHA_SESSION=default
DASHBOARD_API_TOKEN=fJJCz33U8jkHIKXEhTpv91GZJz97VGPHmItYlvxPNUi8obg05BYsZCh5TmfAznma
N8N_REPLY_WEBHOOK_URL=http://dashboard-n8n:5678/webhook/whatsapp-primary
ENVFILE
    fi
fi

# Step 3: Check Docker
echo "🐳 Checking Docker..."
if ! docker info > /dev/null 2>&1; then
    echo "⚠️  Docker is not running. Please start Docker and run again."
    exit 1
fi

# Step 4: Start services
echo "🚀 Starting Docker services..."
echo "(This takes 3-5 minutes on first run)"
docker-compose down -v 2>/dev/null || true
sleep 2
docker-compose up -d 2>&1 | tail -5

# Step 5: Wait for services
echo "⏳ Waiting for services to be ready..."
sleep 15

MAX_WAIT=30
ATTEMPT=0

echo "  Checking MySQL..."
while [ $ATTEMPT -lt $MAX_WAIT ]; do
  if docker-compose exec -T db mysqladmin ping -u root -proot &>/dev/null 2>&1; then
    echo "  ✅ MySQL ready!"
    break
  fi
  ATTEMPT=$((ATTEMPT+1))
  echo "    Attempt $ATTEMPT/$MAX_WAIT..."
  sleep 2
done

if [ $ATTEMPT -eq $MAX_WAIT ]; then
  echo "  ⚠️  MySQL startup taking longer than expected. Continuing anyway..."
fi

# Step 6: Run migrations
echo "🔄 Running database migrations..."
docker-compose exec -T app php artisan config:cache 2>/dev/null || true
sleep 2
docker-compose exec -T app php artisan migrate --force 2>/dev/null || echo "  Migrations may already be applied"

# Step 7: Show status
echo ""
echo "✅ Deployment complete!"
echo ""
docker-compose ps
echo ""
echo "📱 Access your services:"
echo "  • Main App:  http://localhost:8000"
echo "  • n8n:       http://localhost:5679"
echo "  • WAHA:      http://localhost:3000"
echo ""
echo "🔗 To use Web Preview in Cloud Shell:"
echo "  1. Click the Web Preview button (top-right of editor)"
echo "  2. Select 'Preview on port 8000'"
echo ""
echo "📋 Useful commands:"
echo "  docker-compose ps          # See all services"
echo "  docker-compose logs -f     # View logs in real-time"
echo "  docker-compose logs -f app # View app logs"
echo "  docker-compose restart     # Restart services"
echo "  docker-compose stop        # Stop services"
echo ""
