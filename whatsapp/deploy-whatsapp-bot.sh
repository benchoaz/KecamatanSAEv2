#!/bin/bash

# WhatsApp Bot Pelayanan Deployment Script
# Script ini membantu proses deployment WhatsApp Bot Pelayanan

set -e

echo "=========================================="
echo "WhatsApp Bot Pelayanan Deployment Script"
echo "=========================================="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Functions
print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "ℹ $1"
}

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    print_error "Jangan jalankan script ini sebagai root!"
    exit 1
fi

# Check Docker
print_info "Checking Docker installation..."
if ! command -v docker &> /dev/null; then
    print_error "Docker tidak terinstall. Silakan install Docker terlebih dahulu."
    exit 1
fi
print_success "Docker terinstall"

# Check Docker Compose
print_info "Checking Docker Compose installation..."
if ! command -v docker-compose &> /dev/null; then
    print_error "Docker Compose tidak terinstall. Silakan install Docker Compose terlebih dahulu."
    exit 1
fi
print_success "Docker Compose terinstall"

# Get project directory
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
print_info "Project directory: $PROJECT_DIR"

# Check if .env files exist
print_info "Checking environment files..."

if [ ! -f "$PROJECT_DIR/dashboard-kecamatan/.env" ]; then
    print_error "File dashboard-kecamatan/.env tidak ditemukan!"
    exit 1
fi
print_success "dashboard-kecamatan/.env ditemukan"

if [ ! -f "$PROJECT_DIR/whatsapp/laravel-api/.env" ]; then
    print_error "File whatsapp/laravel-api/.env tidak ditemukan!"
    exit 1
fi
print_success "whatsapp/laravel-api/.env ditemukan"

# Generate API Token if not set
print_info "Checking API Token configuration..."

if grep -q "DASHBOARD_API_TOKEN=YOUR_GENERATED_TOKEN_HERE" "$PROJECT_DIR/dashboard-kecamatan/.env" 2>/dev/null || \
   grep -q "DASHBOARD_API_TOKEN=change_this" "$PROJECT_DIR/dashboard-kecamatan/.env" 2>/dev/null; then
    
    print_warning "API Token belum di-set. Generating new token..."
    API_TOKEN=$(openssl rand -base64 32 | tr -d '/+=')
    
    # Update dashboard-kecamatan .env
    if grep -q "DASHBOARD_API_TOKEN=" "$PROJECT_DIR/dashboard-kecamatan/.env"; then
        sed -i "s/DASHBOARD_API_TOKEN=.*/DASHBOARD_API_TOKEN=$API_TOKEN/" "$PROJECT_DIR/dashboard-kecamatan/.env"
    else
        echo "DASHBOARD_API_TOKEN=$API_TOKEN" >> "$PROJECT_DIR/dashboard-kecamatan/.env"
    fi
    
    # Update whatsapp-laravel-api .env
    if grep -q "DASHBOARD_API_TOKEN=" "$PROJECT_DIR/whatsapp/laravel-api/.env"; then
        sed -i "s/DASHBOARD_API_TOKEN=.*/DASHBOARD_API_TOKEN=$API_TOKEN/" "$PROJECT_DIR/whatsapp/laravel-api/.env"
    else
        echo "DASHBOARD_API_TOKEN=$API_TOKEN" >> "$PROJECT_DIR/whatsapp/laravel-api/.env"
    fi
    
    print_success "API Token generated: $API_TOKEN"
    print_warning "Simpan token ini dengan aman: $API_TOKEN"
else
    print_success "API Token sudah di-set"
fi

# Check N8N_REPLY_WEBHOOK_URL
print_info "Checking N8N_REPLY_WEBHOOK_URL configuration..."

if ! grep -q "N8N_REPLY_WEBHOOK_URL=" "$PROJECT_DIR/dashboard-kecamatan/.env"; then
    print_warning "N8N_REPLY_WEBHOOK_URL belum di-set. Adding default value..."
    echo "N8N_REPLY_WEBHOOK_URL=http://n8n:5678/webhook/dashboard-reply" >> "$PROJECT_DIR/dashboard-kecamatan/.env"
    print_success "N8N_REPLY_WEBHOOK_URL added"
else
    print_success "N8N_REPLY_WEBHOOK_URL sudah di-set"
fi

# Check if new files exist
print_info "Checking new implementation files..."

FILES_TO_CHECK=(
    "$PROJECT_DIR/dashboard-kecamatan/app/Http/Controllers/WhatsAppReplyController.php"
    "$PROJECT_DIR/whatsapp/n8n-workflows/whatsapp-service-bot.json"
    "$PROJECT_DIR/whatsapp/n8n-workflows/dashboard-to-whatsapp-reply.json"
)

for file in "${FILES_TO_CHECK[@]}"; do
    if [ -f "$file" ]; then
        print_success "Found: $(basename $file)"
    else
        print_error "Missing: $(basename $file)"
        exit 1
    fi
done

# Ask for confirmation
echo ""
print_warning "Script akan melakukan hal berikut:"
echo "  1. Restart dashboard-kecamatan service"
echo "  2. Restart whatsapp-api-gateway service"
echo "  3. Import n8n workflows (manual step required)"
echo ""
read -p "Lanjutkan? (y/N): " -n 1 -r
echo ""

if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    print_info "Deployment dibatalkan."
    exit 0
fi

# Restart services
echo ""
print_info "Restarting services..."

cd "$PROJECT_DIR/dashboard-kecamatan"
docker-compose restart
print_success "dashboard-kecamatan restarted"

cd "$PROJECT_DIR/whatsapp"
docker-compose restart laravel-api
print_success "whatsapp-api-gateway restarted"

# Wait for services to be ready
echo ""
print_info "Waiting for services to be ready..."
sleep 10

# Test API connections
echo ""
print_info "Testing API connections..."

# Test Dashboard API
DASHBOARD_URL="http://localhost:8000"
if curl -s -f "$DASHBOARD_URL/api/health" > /dev/null 2>&1; then
    print_success "Dashboard API is accessible"
else
    print_warning "Dashboard API might not be accessible from localhost. This is normal if running in Docker network."
fi

# Test WhatsApp API Gateway
WHATSAPP_API_URL="http://localhost:8001"
if curl -s -f "$WHATSAPP_API_URL/api/health" > /dev/null 2>&1; then
    print_success "WhatsApp API Gateway is accessible"
else
    print_warning "WhatsApp API Gateway might not be accessible from localhost. This is normal if running in Docker network."
fi

# Print next steps
echo ""
echo "=========================================="
print_success "Deployment selesai!"
echo "=========================================="
echo ""
print_info "Langkah selanjutnya:"
echo ""
echo "1. Import n8n Workflows:"
echo "   - Buka n8n UI: http://localhost:5678"
echo "   - Import file: whatsapp/n8n-workflows/whatsapp-service-bot.json"
echo "   - Import file: whatsapp/n8n-workflows/dashboard-to-whatsapp-reply.json"
echo "   - Aktifkan kedua workflow"
echo ""
echo "2. Configure WAHA Webhook:"
echo "   - Buka WAHA UI: http://localhost:3000"
echo "   - Setup webhook ke: http://n8n:5678/webhook/whatsapp-bot"
echo ""
echo "3. Test Integration:"
echo "   - Kirim pesan ke nomor WhatsApp bot"
echo "   - Coba command: /help, /faq jam pelayanan, /status {uuid}"
echo ""
echo "4. Configure FAQ:"
echo "   - Login ke dashboard-kecamatan"
echo "   - Buka menu Pelayanan → FAQ"
echo "   - Tambah FAQ entries"
echo ""
echo "Dokumentasi lengkap tersedia di:"
echo "  - whatsapp/docs/IMPLEMENTATION_GUIDE.md"
echo "  - whatsapp/docs/ENVIRONMENT_CONFIGURATION.md"
echo "  - whatsapp/docs/ANALISIS_WHATSAPP_BOT_PELAYANAN.md"
echo ""
