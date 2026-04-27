#!/bin/bash
# ============================================================
# SILAP — Files/Media Backup Script
# Usage:
#   ./scripts/backup/backup-files.sh
#
# Pastikan 'rclone' sudah terkonfigurasi dengan nama remote 'gdrive'.
# ============================================================
set -euo pipefail

# ── Konfigurasi ────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(cd "$SCRIPT_DIR/../.." && pwd)"

SOURCE_DIR="$ROOT_DIR/app/storage/app/public"
# Ambil alamat folder Google Drive dari Dasbor Admin (ModuleSetting) via Docker
echo "🔍 Mengambil pengaturan direktori Google Drive dari sistem..."
# Fallback ke default jika perintah gagal
DB_CLOUD_PATH=$(docker exec kecamatan-app php artisan setting:get backup gdrive_path --default="gdrive:backup/kecamatan-files/" 2>/dev/null || echo "gdrive:backup/kecamatan-files/")

# Hilangkan spasi berlebih
DEST_CLOUD=$(echo "$DB_CLOUD_PATH" | xargs)
LOG_FILE="$ROOT_DIR/storage/logs/rclone-files.log"

echo ""
echo "📂 SILAP Media Files Backup"
echo "========================"
echo "📅 Waktu  : $(date '+%Y-%m-%d %H:%M:%S')"
echo "📁 Sumber : $SOURCE_DIR"
echo "☁️  Target : $DEST_CLOUD"
echo ""

# ── Step 1: Validasi Folder Sumber ─────────────────────────
if [ ! -d "$SOURCE_DIR" ]; then
    echo "❌ Folder sumber tidak ditemukan: $SOURCE_DIR"
    exit 1
fi

# ── Step 2: Validasi Rclone ────────────────────────────────
if ! command -v rclone &> /dev/null; then
    echo "❌ Aplikasi 'rclone' tidak ditemukan."
    echo "   Silakan install dengan: curl https://rclone.org/install.sh | sudo bash"
    exit 1
fi

# ── Step 3: Proses Sinkronisasi (Upload) ───────────────────
echo "⏳ Memulai sinkronisasi file ke Google Drive..."
echo "   (Hanya meng-upload file baru atau yang berubah)"

# Buat folder log jika belum ada
mkdir -p "$(dirname "$LOG_FILE")"

if rclone sync "$SOURCE_DIR" "$DEST_CLOUD" \
    --progress \
    --log-file="$LOG_FILE" \
    --log-level INFO \
    --transfers 4 \
    --checkers 8 \
    --retries 3; then
    
    echo ""
    echo "✅ Sinkronisasi file gambar/PDF berhasil!"
    echo "   Log tersimpan di: $LOG_FILE"
else
    echo ""
    echo "❌ Sinkronisasi gagal! Silakan cek log: $LOG_FILE"
    exit 1
fi

echo ""
