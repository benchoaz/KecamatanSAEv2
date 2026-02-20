#!/bin/bash

# WAHA Webhook Setup Script
# This script registers the n8n webhook URL within the WAHA service.
#
# Architecture:
# - Port 8080: nginx gateway (dashboard-kecamatan)
# - /webhook/*: nginx forwards to n8n (n8n-kecamatan:5678)
# - WAHA calls: http://gateway-nginx:80/webhook/whatsapp-incoming

WAHA_URL="http://localhost:3010"
WEBHOOK_URL="http://gateway-nginx:80/webhook/whatsapp-incoming"

echo "Registering WAHA Webhook..."
echo "Target N8N Webhook: $WEBHOOK_URL"

curl -X POST "$WAHA_URL/api/sessions/default/webhooks" \
  -H "Content-Type: application/json" \
  -d "{
    \"url\": \"$WEBHOOK_URL\",
    \"events\": [\"message\"]
  }"

echo -e "\nWebhook setup request sent."
