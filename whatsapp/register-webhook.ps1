# Register Webhook WAHA to n8n
$wahaUrl = "http://localhost:3099"
$apiKey = "62a72516dd1b418499d9dd22075ccfa0"
# Correct n8n webhook path from workflow: whatsapp-bot
$webhookUrl = "http://n8n-kecamatan:5678/webhook/whatsapp-bot"

Write-Host "Registering WAHA Webhook..."

# Using raw JSON to avoid PowerShell ConvertTo-Json array flattening issues
$jsonBody = @"
{
    "name": "default",
    "config": {
        "webhooks": [
            {
                "url": "$webhookUrl",
                "events": ["message", "message.any", "messages.upsert"]
            }
        ]
    }
}
"@

$headers = @{
    "X-Api-Key"    = $apiKey
    "Content-Type" = "application/json"
}

try {
    $response = Invoke-RestMethod -Uri "$wahaUrl/api/sessions" -Method Post -Headers $headers -Body $jsonBody
    Write-Host "Success! Session configuration updated."
    $response | ConvertTo-Json | Write-Host
}
catch {
    Write-Host "Failed: $_"
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $reader.BaseStream.Position = 0
        $body = $reader.ReadToEnd()
        Write-Host "Response Body: $body"
    }
}
