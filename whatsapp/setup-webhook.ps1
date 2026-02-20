# WAHA Webhook Setup Script (PowerShell version)
#
# Architecture:
# - Port 8080: nginx gateway (dashboard-kecamatan)
# - /webhook/*: nginx forwards to n8n (n8n-kecamatan:5678)
# - WAHA calls: http://gateway-nginx:80/webhook/whatsapp-incoming

$WAHA_URL = "http://localhost:3099"
$N8N_WEBHOOK = "http://gateway-nginx:80/webhook/whatsapp-incoming"
$API_KEY = "62a72516dd1b418499d9dd22075ccfa0"

Write-Host "Registering WAHA Webhook via Session Config..."
Write-Host "Target N8N Webhook: $N8N_WEBHOOK"

$body = @{
    name   = "default"
    config = @{
        webhooks = @(
            @{
                url    = $N8N_WEBHOOK
                events = @("message", "message.any")
            }
        )
    }
} | ConvertTo-Json -Depth 10

$AUTH_HEADER = @{
    "X-Api-Key"    = $API_KEY
    "Content-Type" = "application/json"
}

Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Post -Headers $AUTH_HEADER -Body $body

Write-Host "`nWebhook setup request sent."
