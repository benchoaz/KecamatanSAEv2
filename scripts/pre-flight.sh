#!/bin/bash
set -e

# ============================================================
# Pre-flight Check Script for VPS Deployment
# ============================================================

echo "🔍 Starting pre-flight checks..."

# 1. Disk Space Check (Minimum 2GB)
FREE_DISK=$(df -k . | awk 'NR==2 {print $4}')
MIN_DISK=2097152 # 2GB in KB
if [ "$FREE_DISK" -lt "$MIN_DISK" ]; then
    echo "❌ ERROR: Not enough disk space. Need at least 2GB free."
    df -h .
    exit 1
else
    echo "✅ Disk space: OK ($(df -h . | awk 'NR==2 {print $4}') free)"
fi

# 2. RAM Check (Warning if < 1GB)
FREE_RAM=$(free -m | awk '/^Mem:/{print $7}')
if [ "$FREE_RAM" -lt 500 ]; then
    echo "⚠️  WARNING: Low RAM ($FREE_RAM MB). Deployment might be slow."
else
    echo "✅ RAM: OK ($FREE_RAM MB available)"
fi

# 3. Port Check (80 & 443)
for port in 80 443; do
    if lsof -Pi :$port -sTCP:LISTEN -t >/dev/null ; then
        echo "❌ ERROR: Port $port is already in use."
        exit 1
    fi
done
echo "✅ Ports 80/443: OK"

# 4. Check .env files
if [ ! -f .env ]; then
    echo "❌ ERROR: Root .env file missing. Run setup.sh first or create it."
    exit 1
fi

if [ ! -f app/.env ]; then
    echo "❌ ERROR: app/.env file missing."
    exit 1
fi

# 5. Check Docker & Docker Compose
if ! command -v docker &> /dev/null; then
    echo "❌ ERROR: Docker not installed."
    exit 1
fi

echo "🚀 All pre-flight checks passed!"
exit 0
