<#
.SYNOPSIS
    Test script for WhatsApp bot webhook with n8n
.DESCRIPTION
    This script tests the n8n health check and sends a test message to the WhatsApp webhook
.PARAMETER webhookUrl
    The n8n webhook URL (default: http://localhost:5678/webhook/webhook-test/whatsapp-bot)
#>

param(
    [string]$webhookUrl = "http://localhost:5678/webhook/webhook-test/whatsapp-bot"
)

# Colors for output
$Green = "Green"
$Red = "Red"
$Yellow = "Yellow"
$Cyan = "Cyan"
$White = "White"

function Write-Header {
    param([string]$Title)
    Write-Host ""
    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor $Cyan
    Write-Host "  $Title" -ForegroundColor $Cyan
    Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor $Cyan
    Write-Host ""
}

function Write-Section {
    param([string]$Title)
    Write-Host ""
    Write-Host "─── $Title ───" -ForegroundColor $Yellow
    Write-Host ""
}

# ============================================================================
# STEP 1: Test n8n Health Check
# ============================================================================
Write-Header "WhatsApp Bot Webhook Test Script"

Write-Section "STEP 1: Testing n8n Health Check"

$healthUrl = "http://localhost:5678/healthz"
Write-Host "  Testing n8n health at: $healthUrl" -ForegroundColor $White

try {
    $healthResponse = Invoke-RestMethod -Uri $healthUrl -Method Get -TimeoutSec 10 -ErrorAction Stop
    Write-Host "  ✓ n8n is RUNNING" -ForegroundColor $Green
    Write-Host "  Response: $($healthResponse | ConvertTo-Json -Compress)" -ForegroundColor $Green
}
catch {
    Write-Host "  ✗ n8n is NOT RESPONDING" -ForegroundColor $Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor $Red
    Write-Host ""
    Write-Host "  Possible fixes:" -ForegroundColor $Yellow
    Write-Host "  1. Check if n8n is running: Get-Process -Name n8n" -ForegroundColor $White
    Write-Host "  2. Start n8n: n8n start" -ForegroundColor $White
    Write-Host "  3. Check n8n port: netstat -ano | findstr :5678" -ForegroundColor $White
    Write-Host ""
    Write-Host "  Also check n8n UI: http://localhost:5678" -ForegroundColor $Cyan
    exit 1
}

# ============================================================================
# STEP 2: Test Webhook with Personal Message
# ============================================================================
Write-Section "STEP 2: Testing WhatsApp Webhook with Personal Message"

# Test payload for personal message
$payload = @{
    event   = "message"
    session = "default"
    payload = @{
        id         = "test_message_001"
        timestamp  = [int](Get-Date -UFormat %s)
        from       = "6281234567890@c.us"
        fromMe     = $false
        body       = "/help"
        hasMedia   = $false
        to         = "6282231203765@c.us"
        notifyName = "Test User"
    }
    engine  = "WEBJS"
} | ConvertTo-Json -Compress

Write-Host "  Webhook URL: $webhookUrl" -ForegroundColor $White
Write-Host ""
Write-Host "  Test Payload:" -ForegroundColor $Yellow
Write-Host $payload -ForegroundColor $White
Write-Host ""

Write-Host "  Sending POST request..." -ForegroundColor $White

try {
    $webhookResponse = Invoke-RestMethod -Uri $webhookUrl -Method Post -Body $payload `
        -ContentType "application/json" -TimeoutSec 30 -ErrorAction Stop

    Write-Host ""
    Write-Host "  ✓ Webhook request SUCCESSFUL" -ForegroundColor $Green
    Write-Host "  Response:" -ForegroundColor $Green
    Write-Host ($webhookResponse | ConvertTo-Json -Compress) -ForegroundColor $Green
}
catch {
    Write-Host ""
    Write-Host "  ✗ Webhook request FAILED" -ForegroundColor $Red
    Write-Host "  Error: $($_.Exception.Message)" -ForegroundColor $Red

    # Check for specific error types
    if ($_.Exception.Response) {
        $statusCode = [int]$_.Exception.Response.StatusCode
        Write-Host "  HTTP Status Code: $statusCode" -ForegroundColor $Red

        if ($statusCode -eq 404) {
            Write-Host ""
            Write-Host "  Possible causes:" -ForegroundColor $Yellow
            Write-Host "  1. Webhook endpoint does not exist" -ForegroundColor $White
            Write-Host "  2. Workflow is not active/published" -ForegroundColor $White
            Write-Host "  3. Wrong webhook path" -ForegroundColor $White
        }
        elseif ($statusCode -eq 500) {
            Write-Host ""
            Write-Host "  Possible causes:" -ForegroundColor $Yellow
            Write-Host "  1. Workflow has an error" -ForegroundColor $White
            Write-Host "  2. Database connection failed" -ForegroundColor $White
            Write-Host "  3. Missing required node configuration" -ForegroundColor $White
        }
    }
}

# ============================================================================
# STEP 3: Instructions for Checking n8n Executions
# ============================================================================
Write-Section "STEP 3: Instructions for Checking n8n Executions"

Write-Host "  To check n8n workflow executions:" -ForegroundColor $White
Write-Host ""
Write-Host "  1. Open n8n UI:" -ForegroundColor $Cyan
Write-Host "     http://localhost:5678" -ForegroundColor $White
Write-Host ""
Write-Host "  2. Navigate to:" -ForegroundColor $Cyan
Write-Host "     - Click on your 'webhook-test' workflow" -ForegroundColor $White
Write-Host "     - Click 'Executions' tab" -ForegroundColor $White
Write-Host "     - View recent executions" -ForegroundColor $White
Write-Host ""
Write-Host "  3. Check execution details:" -ForegroundColor $Cyan
Write-Host "     - Click on any execution to see step-by-step" -ForegroundColor $White
Write-Host "     - Look for red error indicators" -ForegroundColor $White
Write-Host "     - Check input/output data at each node" -ForegroundColor $White
Write-Host ""
Write-Host "  4. Enable manual execution:" -ForegroundColor $Cyan
Write-Host "     - Go to workflow settings" -ForegroundColor $White
Write-Host "     - Make sure workflow is 'Active'" -ForegroundColor $White
Write-Host "     - Check 'Save manual executions' is enabled" -ForegroundColor $White
Write-Host ""
Write-Host "  5. View workflow error logs:" -ForegroundColor $Cyan
Write-Host "     - Settings > Error Workflow" -ForegroundColor $White
Write-Host "     - Check if there's an error workflow attached" -ForegroundColor $White

# ============================================================================
# STEP 4: Common Issues Summary
# ============================================================================
Write-Section "STEP 4: Common Issues Checklist"

Write-Host "  □ n8n is running and accessible?" -ForegroundColor $White
Write-Host "  □ WhatsApp workflow is published/active?" -ForegroundColor $White
Write-Host "  □ Webhook URL is correct?" -ForegroundColor $White
Write-Host "  □ fromMe flag is correctly handled?" -ForegroundColor $White
Write-Host "  □ Response node is configured?" -ForegroundColor $White
Write-Host "  □ No red error nodes in workflow?" -ForegroundColor $White
Write-Host ""
Write-Host "  Debug commands:" -ForegroundColor $Cyan
Write-Host "  - Check n8n processes: Get-Process -Name node,n8n" -ForegroundColor $White
Write-Host "  - Check port 5678: Test-NetConnection -ComputerName localhost -Port 5678" -ForegroundColor $White
Write-Host "  - View n8n logs: Check console output where n8n is running" -ForegroundColor $White

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor $Cyan
Write-Host "  Test Complete" -ForegroundColor $Cyan
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor $Cyan
Write-Host ""
