#!/bin/bash

# Script untuk hapus semua webhooks lama dari WAHA

echo "=== Menghapus semua webhooks lama dari WAHA ==="
echo ""

WAHA_URL="http://localhost:3099"

# Step 1: Cek webhooks yang ada
echo "1. Memeriksa webhooks yang ada..."
curl -s "$WAHA_URL/api/webhooks" | jq .
echo ""

# Step 2: Hapus semua webhooks
echo "2. Menghapus semua webhooks..."

# Dapatkan list webhook IDs
WEBHOOK_IDS=$(curl -s "$WAHA_URL/api/webhooks" | jq -r '.[] | .id')

if [ -z "$WEBHOOK_IDS" ]; then
    echo "Tidak ada webhooks ditemukan."
else
    for ID in $WEBHOOK_IDS; do
        echo "Menghapus webhook: $ID"
        curl -s -X DELETE "$WAHA_URL/api/webhooks/$ID"
        echo "✓ Webhook $ID dihapus"
    done
fi

echo ""
echo "3. Verifikasi - Webhooks setelah dihapus..."
curl -s "$WAHA_URL/api/webhooks" | jq .

echo ""
echo "=== Selesai ==="
echo ""
echo "Langkah selanjutnya:"
echo "1. Setup webhook baru dengan URL nginx gateway:"
echo "   curl -X POST $WAHA_URL/api/webhooks \\"
echo "     -H 'Content-Type: application/json' \\"
echo "     -d '{"
echo '       "url": "http://gateway-nginx:80/webhook/whatsapp-incoming",'
echo '       "events": ["message"],'
echo '       "session": "default"'
echo '     }'
echo "'"
echo ""
echo "2. Restart nginx gateway:"
echo "   docker restart gateway-nginx"
echo ""
echo "3. Verifikasi:"
echo "   curl $WAHA_URL/api/webhooks"
