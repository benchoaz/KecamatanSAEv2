<#
.SYNOPSIS
    Test script for WhatsApp WAHA webhook endpoint
.DESCRIPTION
    Sends a test payload to the WhatsApp webhook URL and displays the response
.PARAMETER WebhookUrl
    The webhook URL to test (default: http://localhost:8080/webhook/webhook-test/whatsapp-bot)
#>

param(
    [string]$WebhookUrl = "http://localhost:8080/webhook/webhook-test/whatsapp-bot"
)

# Colors for output
$Green = "Green"
$Red = "Red"
$Yellow = "Yellow"
$Cyan = "Cyan"

Write-Host "`n========================================" -ForegroundColor $Cyan
Write-Host "  WhatsApp WAHA Webhook Test Script" -ForegroundColor $Cyan
Write-Host "========================================" -ForegroundColor $Cyan
Write-Host ""

# Display webhook URL
Write-Host "Target Webhook URL:" -ForegroundColor $Yellow
Write-Host "  $WebhookUrl`n" -ForegroundColor $Green

# Test payload
$payload = @{
    event   = "message"
    session = "default"
    payload = @{
        id         = "test_$(Get-Random -Minimum 1000 -Maximum 9999)"
        timestamp  = [int](Get-Date -UFormat "%s")
        from       = "6281234567890@c.us"
        fromMe     = $false
        body       = "/help"
        hasMedia   = $false
        to         = "6282231203765@c.us"
        notifyName = "Test User"
    }
} | ConvertTo-Json -Depth 10

Write-Host "Test Payload:" -ForegroundColor $Yellow
Write-Host $payload -ForegroundColor $Cyan
Write-Host ""

# Check if curl is available, otherwise use PowerShell Invoke-RestMethod
$useCurl = $false
try {
    $curlVersion = curl --version 2>&1
    if ($LASTEXITCODE -eq 0) {
        $useCurl = $true
    }
}
catch {
    $useCurl = $false
}

Write-Host "Sending request..." -ForegroundColor $Yellow

$response = $null
$statusCode = $null
$errorMessage = $null

try {
    if ($useCurl) {
        # Use curl for request
        $responseBody = curl -X POST `
            -H "Content-Type: application/json" `
            -d $payload `
            $WebhookUrl `
            --max-time 30 `
            2>&1
        $curlExitCode = $LASTEXITCODE
        
        if ($curlExitCode -eq 0) {
            $response = $responseBody
            Write-Host "`nResponse received successfully!" -ForegroundColor $Green
        }
        else {
            $errorMessage = $responseBody
            Write-Host "`ncurl error (exit code: $curlExitCode)" -ForegroundColor $Red
        }
    }
    else {
        # Use PowerShell Invoke-RestMethod
        $webRequest = Invoke-WebRequest -Uri $WebhookUrl `
            -Method Post `
            -Body $payload `
            -ContentType "application/json" `
            -TimeoutSec 30 `
            -ErrorAction Stop
        
        $statusCode = $webRequest.StatusCode
        $response = $webRequest.Content
        
        Write-Host "`nResponse received successfully!" -ForegroundColor $Green
        Write-Host "Status Code: $statusCode" -ForegroundColor $Green
    }
}
catch {
    $errorMessage = $_.Exception.Message
    Write-Host "`nError occurred:" -ForegroundColor $Red
    Write-Host "  $($_.Exception.Message)" -ForegroundColor $Red
    
    if ($_.Exception.Response) {
        $statusCode = [int]$_.Exception.Response.StatusCode
        Write-Host "  Status Code: $statusCode" -ForegroundColor $Red
    }
}

# Display response
Write-Host "`n----------------------------------------" -ForegroundColor $Cyan
Write-Host "Response:" -ForegroundColor $Yellow
Write-Host "----------------------------------------" -ForegroundColor $Cyan

if ($response) {
    try {
        $formattedResponse = $response | ConvertFrom-Json | Format-List | Out-String
        Write-Host $formattedResponse -ForegroundColor $Green
    }
    catch {
        Write-Host $response -ForegroundColor $Green
    }
}
elseif ($errorMessage) {
    Write-Host $errorMessage -ForegroundColor $Red
}

Write-Host ""

# Troubleshooting section
Write-Host "========================================" -ForegroundColor $Cyan
Write-Host "  Troubleshooting Guide" -ForegroundColor $Cyan
Write-Host "========================================" -ForegroundColor $Cyan
Write-Host ""

if (-not $response) {
    Write-Host "The webhook did not respond. Check the following:" -ForegroundColor $Yellow
    Write-Host ""
    Write-Host "1. Is n8n running?" -ForegroundColor White
    Write-Host "   - Check if n8n container/service is running"
    Write-Host "   - Command: docker ps | grep n8n" -ForegroundColor Gray
    Write-Host ""
    Write-Host "2. Is the reverse proxy (nginx) running?" -ForegroundColor White
    Write-Host "   - Check if nginx is running on port 8080"
    Write-Host ""
    Write-Host "3. Is the webhook URL correct?" -ForegroundColor White
    Write-Host "   - Current: $WebhookUrl" -ForegroundColor Gray
    Write-Host "   - Verify the URL path matches your n8n webhook"
    Write-Host ""
    Write-Host "4. Check n8n logs for errors:" -ForegroundColor White
    Write-Host "   - Command: docker logs <n8n-container-name>" -ForegroundColor Gray
    Write-Host ""
    Write-Host "5. Test if the server is reachable:" -ForegroundColor White
    Write-Host "   - Command: curl http://localhost:8080" -ForegroundColor Gray
}

Write-Host "6. Verify the webhook is properly configured in n8n:" -ForegroundColor White
Write-Host "   - Check that the webhook node exists and is active"
Write-Host "   - Verify the HTTP Method is set to POST"
Write-Host ""
