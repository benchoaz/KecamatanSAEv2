# WAHA Debug Script v2
$WAHA_URL = "http://localhost:3099"
$AUTH_HEADER = @{ "X-Api-Key" = "62a72516dd1b418499d9dd22075ccfa0" }

Write-Host "--- Checking WAHA Sessions ---"
try {
    $sessions = Invoke-RestMethod -Uri "$WAHA_URL/api/sessions" -Method Get -Headers $AUTH_HEADER
    Write-Host "Sessions found:"
    $sessions | ConvertTo-Json
}
catch {
    Write-Host "Error getting sessions: $_"
}

Write-Host "`n--- Testing Webhook Endpoints ---"
$test_endpoints = @(
    "/api/webhooks",
    "/api/sessions/default/webhooks"
)

foreach ($ep in $test_endpoints) {
    Write-Host "Testing GET $ep ..."
    try {
        $res = Invoke-RestMethod -Uri "$WAHA_URL$ep" -Method Get -Headers $AUTH_HEADER
        Write-Host "SUCCESS: Endpoint $ep exists."
    }
    catch {
        Write-Host "FAILED: Endpoint $ep - $($_.Exception.Message)"
    }
}
