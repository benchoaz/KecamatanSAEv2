#!/bin/bash
# ============================================================
# SILAP — Database Backup Script
# Usage:
#   ./scripts/backup/backup-db.sh              → backup lokal
#   ./scripts/backup/backup-db.sh --offsite    → backup + upload ke cloud
#   ./scripts/backup/backup-db.sh --pre-deploy → backup sebelum deploy (tag khusus)
#
# Setup crontab (di VPS):
#   0 2 * * *   /path/to/project/scripts/backup/backup-db.sh >> /var/log/kecamatan-backup.log 2>&1
#   0 3 * * 0   /path/to/project/scripts/backup/backup-db.sh --offsite >> /var/log/kecamatan-backup.log 2>&1
# ============================================================
set -euo pipefail

# ── Konfigurasi ────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

# Load .env dari root project
if [ -f "$ROOT_DIR/.env" ]; then
    # shellcheck disable=SC2046
    export $(grep -v '^#' "$ROOT_DIR/.env" | grep -v '^$' | xargs)
fi

BACKUP_DIR="${BACKUP_DIR:-/opt/backups/kecamatan-db}"
DB_CONTAINER="kecamatan-db"
DB_NAME="${DB_DATABASE:-dashboard_kecamatan}"
DB_USER="${DB_USERNAME:-user}"
MAX_LOCAL_DAYS=7          # Simpan backup lokal selama N hari
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Tentukan tag nama file berdasarkan argumen
TAG="daily"
OFFSITE=false
for arg in "$@"; do
    case $arg in
        --pre-deploy) TAG="pre-deploy" ;;
        --offsite)    OFFSITE=true ;;
        --manual)     TAG="manual" ;;
    esac
done

BACKUP_FILE="$BACKUP_DIR/${TIMESTAMP}_${TAG}_kecamatan.sql.gz"

# ── Header ─────────────────────────────────────────────────
echo ""
echo "💾 SILAP Database Backup"
echo "========================"
echo "📅 Waktu  : $(date '+%Y-%m-%d %H:%M:%S')"
echo "📂 Target : $BACKUP_FILE"
echo "🏷️  Tag    : $TAG"
echo ""

# ── Step 1: Validasi ───────────────────────────────────────
if ! docker inspect "$DB_CONTAINER" > /dev/null 2>&1; then
    echo "❌ Container '$DB_CONTAINER' tidak ditemukan!"
    echo "   Pastikan: docker compose up -d db"
    exit 1
fi

DB_STATUS=$(docker inspect "$DB_CONTAINER" --format='{{.State.Status}}')
if [ "$DB_STATUS" != "running" ]; then
    echo "❌ Container '$DB_CONTAINER' tidak running (status: $DB_STATUS)"
    exit 1
fi

# ── Step 2: Buat direktori backup ──────────────────────────
mkdir -p "$BACKUP_DIR"

# ── Step 3: Dump & compress ────────────────────────────────
echo "⏳ Dumping database '$DB_NAME'..."

if docker exec "$DB_CONTAINER" \
    pg_dump -U "$DB_USER" -d "$DB_NAME" \
    --no-password \
    --format=plain \
    --no-owner \
    --no-acl \
    | gzip -9 > "$BACKUP_FILE"; then

    SIZE=$(du -sh "$BACKUP_FILE" | cut -f1)
    echo "✅ Backup berhasil: $BACKUP_FILE ($SIZE)"
else
    echo "❌ Backup gagal! File yang rusak dihapus."
    rm -f "$BACKUP_FILE"
    exit 1
fi

# ── Step 4: Verifikasi file tidak kosong ───────────────────
if [ ! -s "$BACKUP_FILE" ]; then
    echo "❌ File backup kosong! Ada masalah dengan pg_dump."
    rm -f "$BACKUP_FILE"
    exit 1
fi

# ── Step 5: Rotate backup lama ─────────────────────────────
echo ""
echo "🧹 Menghapus backup > $MAX_LOCAL_DAYS hari..."
DELETED=$(find "$BACKUP_DIR" -name "*.sql.gz" -mtime +"$MAX_LOCAL_DAYS" -print -delete 2>/dev/null | wc -l)
echo "   Dihapus: $DELETED file"

# ── Step 6: Upload ke cloud (opsional) ─────────────────────
if [ "$OFFSITE" = true ]; then
    echo ""
    echo "☁️  Upload ke cloud storage..."
    if command -v rclone &> /dev/null; then
        # Ambil pengaturan folder dari Dashboard via artisan
        DB_CLOUD_PATH=$(docker exec kecamatan-app php artisan setting:get backup gdrive_path --default="gdrive:backup/" 2>/dev/null || echo "gdrive:backup/")
        DEST_CLOUD=$(echo "$DB_CLOUD_PATH" | xargs)
        
        # Tambahkan subfolder /db agar rapi
        TARGET_GDRIVE="${DEST_CLOUD%/}/db/"

        if rclone copy "$BACKUP_FILE" "$TARGET_GDRIVE" \
            --log-level INFO \
            --transfers 1 \
            --retries 3; then
            echo "✅ Upload ke Google Drive berhasil"
        else
            echo "⚠️  Upload gagal, backup tetap tersimpan lokal"
        fi
    else
        echo "⚠️  rclone tidak terpasang. Install: https://rclone.org/install/"
        echo "   Kemudian konfigurasi: rclone config"
    fi
fi

# ── Ringkasan ──────────────────────────────────────────────
echo ""
echo "📊 Ringkasan Backup:"
echo "─────────────────────────────────────────────"
echo "   File terbaru : $BACKUP_FILE"
echo "   Ukuran       : $SIZE"
TOTAL_FILES=$(ls -1 "$BACKUP_DIR"/*.sql.gz 2>/dev/null | wc -l)
TOTAL_SIZE=$(du -sh "$BACKUP_DIR" 2>/dev/null | cut -f1)
echo "   Total file   : $TOTAL_FILES backup"
echo "   Storage pakai: $TOTAL_SIZE"
echo "─────────────────────────────────────────────"
echo ""
echo "💡 Restore dengan:"
echo "   zcat $BACKUP_FILE | \\"
echo "     docker exec -i $DB_CONTAINER psql -U $DB_USER -d $DB_NAME"
echo ""
