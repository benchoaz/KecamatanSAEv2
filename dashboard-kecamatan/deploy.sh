#!/bin/bash
# =============================================================
# DEPLOY SCRIPT - Dashboard Kecamatan
# Cocok untuk VPS fresh (Ubuntu/Debian/CentOS)
# Cara pakai: bash deploy.sh
# =============================================================

set -e

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}"
echo "╔══════════════════════════════════════════════════╗"
echo "║     DASHBOARD KECAMATAN - DEPLOY SCRIPT          ║"
echo "╚══════════════════════════════════════════════════╝"
echo -e "${NC}"

# ──────────────────────────────────────────────────────────
# 1. Cek & Install Docker
# ──────────────────────────────────────────────────────────
install_docker() {
    echo -e "${YELLOW}[1/6] Mengecek Docker...${NC}"
    if ! command -v docker &>/dev/null; then
        echo -e "${YELLOW}Docker belum terinstall. Menginstall otomatis...${NC}"
        if command -v apt-get &>/dev/null; then
            apt-get update -qq
            apt-get install -y -qq ca-certificates curl gnupg
            install -m 0755 -d /etc/apt/keyrings
            curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /etc/apt/keyrings/docker.gpg
            chmod a+r /etc/apt/keyrings/docker.gpg
            echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | tee /etc/apt/sources.list.d/docker.list >/dev/null
            apt-get update -qq
            apt-get install -y -qq docker-ce docker-ce-cli containerd.io docker-compose-plugin
        elif command -v yum &>/dev/null; then
            yum install -y -q yum-utils
            yum-config-manager --add-repo https://download.docker.com/linux/centos/docker-ce.repo
            yum install -y -q docker-ce docker-ce-cli containerd.io docker-compose-plugin
        else
            curl -fsSL https://get.docker.com -o get-docker.sh && sh get-docker.sh
        fi
        systemctl start docker
        systemctl enable docker
        echo -e "${GREEN}✔ Docker berhasil diinstall!${NC}"
    else
        DOCKER_VER=$(docker --version)
        echo -e "${GREEN}✔ Docker sudah ada: $DOCKER_VER${NC}"
    fi

    # Cek docker compose (plugin atau standalone)
    if docker compose version &>/dev/null 2>&1; then
        COMPOSE_CMD="docker compose"
    elif command -v docker-compose &>/dev/null; then
        COMPOSE_CMD="docker-compose"
    else
        echo -e "${YELLOW}Menginstall Docker Compose plugin...${NC}"
        apt-get install -y -qq docker-compose-plugin 2>/dev/null || \
        curl -SL "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose && \
        chmod +x /usr/local/bin/docker-compose
        COMPOSE_CMD="docker-compose"
    fi
    echo -e "${GREEN}✔ Docker Compose: $COMPOSE_CMD${NC}"
}

# ──────────────────────────────────────────────────────────
# 2. Setup file .env
# ──────────────────────────────────────────────────────────
setup_env() {
    echo -e "${YELLOW}[2/6] Setup environment...${NC}"
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            cp .env.example .env
            echo -e "${GREEN}✔ .env dibuat dari .env.example${NC}"
            echo -e "${YELLOW}⚠ Harap edit .env sesuai kebutuhan (APP_URL, DB_PASSWORD, dll.)${NC}"
        else
            echo -e "${RED}✘ File .env tidak ditemukan dan .env.example juga tidak ada!${NC}"
            exit 1
        fi
    else
        echo -e "${GREEN}✔ .env sudah ada${NC}"
    fi
}

# ──────────────────────────────────────────────────────────
# 3. Setup storage symlink & permissions
# ──────────────────────────────────────────────────────────
setup_storage() {
    echo -e "${YELLOW}[3/6] Setup direktori & permissions...${NC}"
    mkdir -p storage/framework/{sessions,views,cache}
    mkdir -p storage/logs
    mkdir -p bootstrap/cache
    chmod -R 775 storage bootstrap
    echo -e "${GREEN}✔ Direktori storage & bootstrap siap${NC}"
}

# ──────────────────────────────────────────────────────────
# 4. Build & Start containers
# ──────────────────────────────────────────────────────────
build_and_start() {
    echo -e "${YELLOW}[4/6] Build Docker image & start containers...${NC}"
    echo -e "${BLUE}(Proses ini bisa memakan waktu 5-10 menit untuk pertama kali)${NC}"

    $COMPOSE_CMD build --no-cache app
    $COMPOSE_CMD up -d

    echo -e "${GREEN}✔ Semua container berhasil dijalankan!${NC}"
}

# ──────────────────────────────────────────────────────────
# 5. Jalankan artisan commands
# ──────────────────────────────────────────────────────────
run_artisan() {
    echo -e "${YELLOW}[5/6] Setup Laravel (migrations, cache, ide-helper)...${NC}"

    # Tunggu DB siap
    echo -n "Menunggu database siap"
    for i in $(seq 1 30); do
        if $COMPOSE_CMD exec -T db mysqladmin ping -h localhost -u root -p"${DB_ROOT_PASSWORD:-root}" --silent 2>/dev/null; then
            echo -e " ${GREEN}OK${NC}"
            break
        fi
        echo -n "."
        sleep 2
    done

    APP="$COMPOSE_CMD exec -T app php artisan"

    echo -e "${BLUE}→ Installing Backend Dependencies (Composer)...${NC}"
    $COMPOSE_CMD exec -T app composer install --optimize-autoloader --no-dev --no-interaction

    echo -e "${BLUE}→ Compiling Frontend Assets (Node/Vite)...${NC}"
    docker run --rm -v $(pwd):/var/www -w /var/www node:18-alpine sh -c "npm install && npm run build" || echo -e "${YELLOW}⚠ Peringatan: Compile assets gagal atau skip.${NC}"

    echo -e "${BLUE}→ Generating app key (skip jika sudah ada)...${NC}"
    $APP key:generate --no-interaction 2>/dev/null || true

    echo -e "${BLUE}→ Running migrations...${NC}"
    $APP migrate --force --no-interaction

    echo -e "${BLUE}→ Linking storage...${NC}"
    $APP storage:link --no-interaction 2>/dev/null || true

    echo -e "${BLUE}→ Generating IDE helper...${NC}"
    $APP ide-helper:generate 2>/dev/null && \
    $APP ide-helper:models --nowrite 2>/dev/null || \
    echo -e "${YELLOW}⚠ IDE helper skip (tidak affect production)${NC}"

    echo -e "${BLUE}→ Caching config & routes...${NC}"
    $APP config:cache
    $APP route:cache
    $APP view:cache

    echo -e "${GREEN}✔ Laravel setup selesai!${NC}"
}

# ──────────────────────────────────────────────────────────
# 6. Status & info
# ──────────────────────────────────────────────────────────
show_status() {
    echo -e "${YELLOW}[6/6] Status deployment...${NC}"
    $COMPOSE_CMD ps

    APP_PORT=$(grep APP_PORT .env 2>/dev/null | cut -d= -f2 | tr -d '"' || echo "8084")
    APP_PORT=${APP_PORT:-8084}

    echo ""
    echo -e "${GREEN}╔══════════════════════════════════════════════════╗"
    echo -e "║           DEPLOYMENT BERHASIL! ✔                 ║"
    echo -e "╠══════════════════════════════════════════════════╣"
    echo -e "║  🌐 App      : http://$(hostname -I | awk '{print $1}'):${APP_PORT}      "
    echo -e "║  ⚙️  N8N      : http://$(hostname -I | awk '{print $1}'):8080      "
    echo -e "║  📱 WAHA     : http://127.0.0.1:3000            ║"
    echo -e "║  🗄️  MySQL    : 127.0.0.1:3308                  ║"
    echo -e "╚══════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${YELLOW}Perintah berguna:${NC}"
    echo -e "  $COMPOSE_CMD logs -f app     # Lihat log Laravel"
    echo -e "  $COMPOSE_CMD exec app php artisan ...  # Jalankan artisan"
    echo -e "  $COMPOSE_CMD restart         # Restart semua container"
    echo -e "  bash update.sh               # Update deployment"
}

# ──────────────────────────────────────────────────────────
# MAIN
# ──────────────────────────────────────────────────────────
install_docker
setup_env
setup_storage
build_and_start
run_artisan
show_status
