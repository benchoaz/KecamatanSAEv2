# WhatsApp Auto-Response Issues and Solutions

## Problem Summary

Your WhatsApp system is not responding automatically and appears to only capture group messages. This document explains the root causes and provides solutions.

---

## Root Causes Identified

### 1. Container Name Mismatch in n8n Workflow

**Location**: [`n8n-workflows/whatsapp-classifier.json`](n8n-workflows/whatsapp-classifier.json:120)

**Issue**: The n8n workflow tries to send data to `http://whatsapp-api:8001/api/webhook/n8n`

**Problem**: The actual container name in [`docker-compose.yml`](docker-compose.yml:48) is `whatsapp-api-gateway`, not `whatsapp-api`

**Impact**: n8n cannot reach the Laravel API, so messages are never forwarded to the dashboard.

---

### 2. URL Path Mismatch

**Location**: [`n8n-workflows/whatsapp-classifier.json`](n8n-workflows/whatsapp-classifier.json:120)

**Issue**: The n8n workflow sends to `/api/webhook/n8n`

**Problem**: The actual route in [`laravel-api/routes/api.php`](laravel-api/routes/api.php:19) is `/api/webhook`

**Impact**: Even if the container name was correct, the path is wrong.

---

### 3. WAHA Webhook Configuration Issue

**Location**: [`setup-webhook.ps1`](setup-webhook.ps1:9)

**Issue**: The script uses a session creation approach with webhooks:
```powershell
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
}
```

**Problem**: If the session already exists, this won't update the webhooks. The correct approach for existing sessions is:
```bash
POST /api/sessions/{sessionName}/webhooks
```

**Impact**: Webhooks may not be properly configured, causing messages to not be sent to n8n.

---

### 4. No Auto-Reply Implementation

**Current Behavior**: The system only **forwards** messages to the dashboard but **never sends any reply back** to the WhatsApp sender.

**Architecture Flow**:
```
WhatsApp Message → WAHA → n8n → Laravel API → Dashboard
```

**Missing**: There's no path back to send a reply:
```
Dashboard → Laravel API → WAHA → WhatsApp Reply
```

**Impact**: Users receive no acknowledgment or response, making it seem like the system is not working.

---

### 5. WAHA Event Types

**Current Events**: `["message", "message.any"]`

**Issue**: While `message.any` should capture all message types (private, group, broadcast), there might be:
- Configuration issues with how WAHA handles different message types
- Missing events for specific message scenarios

**Impact**: Some messages (especially private chats) might not trigger webhooks.

---

### 6. Dashboard API Token Not Configured

**Location**: [`laravel-api/.env`](laravel-api/.env:7)

**Issue**: The token is still set to:
```
DASHBOARD_API_TOKEN=your_secure_random_token_here_change_this
```

**Problem**: This needs to match the token in the dashboard's `.env` file.

**Impact**: Laravel API cannot authenticate with the dashboard, causing 401 errors.

---

## Solutions

### Solution 1: Run the Fix Script

Run the provided fix script to automatically fix the configuration issues:

```powershell
cd d:\Projectku\whatsapp
.\fix-whatsapp-autoreply.ps1
```

This script will:
1. Check WAHA session status
2. Create session if it doesn't exist
3. Configure webhook with proper events
4. Fix n8n workflow configuration (container name and URL path)
5. Verify webhook configuration

---

### Solution 2: Re-import n8n Workflow

After running the fix script, you need to re-import the workflow in n8n:

1. Open n8n: http://localhost:5678
2. Delete the existing "WhatsApp Message Classifier" workflow
3. Import the fixed workflow: `n8n-workflows/whatsapp-classifier.json`
4. Activate the workflow (toggle to ON)

---

### Solution 3: Configure Dashboard API Token

Edit [`laravel-api/.env`](laravel-api/.env:7) and set the correct token:

```env
DASHBOARD_API_TOKEN=your_actual_token_here
```

This token must match the `WHATSAPP_API_TOKEN` in your dashboard's `.env` file.

Then restart the Laravel API container:

```powershell
docker-compose restart whatsapp-api
```

---

### Solution 4: Implement Auto-Reply (See Below)

To enable automatic responses, you need to add auto-reply functionality to the n8n workflow.

---

## Implementing Auto-Reply

### Option 1: Simple Acknowledgment Auto-Reply

Add a simple acknowledgment message after processing:

**n8n Workflow Modification**:

After the "Send to WhatsApp API" node, add a new node:

1. **HTTP Request Node** (Send Auto-Reply)
   - Method: POST
   - URL: `http://waha-kecamatan:3000/api/sendText`
   - Headers:
     - `X-Api-Key`: `62a72516dd1b418499d9dd22075ccfa0`
   - Body:
     ```json
     {
       "session": "default",
       "chatId": "={{$json.phone}}@c.us",
       "text": "Terima kasih! Pesan Anda telah diterima dan sedang diproses."
     }
     ```

---

### Option 2: FAQ-Based Auto-Reply

Implement FAQ matching to answer common questions automatically:

**n8n Workflow Modification**:

1. After "Classify Message" node, add a new node:
   - **HTTP Request** (Search FAQ)
   - Method: GET
   - URL: `http://host.docker.internal:8080/api/faq-search?q={{$json.message}}`

2. Add a **Switch** node:
   - If FAQ found → Send FAQ answer via WAHA → End
   - If FAQ not found → Continue to Laravel API

3. **HTTP Request** (Send FAQ Answer)
   - Method: POST
   - URL: `http://waha-kecamatan:3000/api/sendText`
   - Body:
     ```json
     {
       "session": "default",
       "chatId": "={{$json.phone}}@c.us",
       "text": "={{$json.faq_answer}}"
     }
     ```

---

### Option 3: Category-Based Auto-Reply

Send different auto-replies based on message category:

**n8n Workflow Modification**:

After "Set Category" node, add a **Switch** node with branches:

1. **Pengaduan** → Send: "Laporan Anda telah diterima. Kami akan segera menindaklanjuti."
2. **Pelayanan** → Send: "Permohonan layanan Anda sedang diproses. Mohon tunggu informasi selanjutnya."
3. **UMKM** → Send: "Terima kasih atas minat Anda terhadap program UMKM. Kami akan menghubungi Anda segera."
4. **Loker** → Send: "Informasi lowongan kerja telah kami terima. Kami akan memproses permintaan Anda."

---

## Testing the Fixes

### Test 1: Verify WAHA Webhook

```powershell
# Check webhook configuration
$headers = @{ "X-Api-Key" = "62a72516dd1b418499d9dd22075ccfa0" }
Invoke-RestMethod -Uri "http://localhost:3099/api/sessions/default/webhooks" -Method Get -Headers $headers
```

Expected output should show your webhook URL and events.

---

### Test 2: Send a Test Message

1. Send a WhatsApp message to your connected number
2. Check n8n executions: http://localhost:5678/executions
3. Check Laravel logs:
   ```powershell
   Get-Content laravel-api/storage/logs/transactions-*.log -Tail 50
   ```

---

### Test 3: Check Dashboard

1. Login to Dashboard: http://localhost:8080
2. Menu: Pelayanan → Inbox
3. Filter by source: WhatsApp
4. Verify your message appears

---

## Troubleshooting

### Issue: Messages Not Reaching n8n

**Check WAHA logs**:
```powershell
docker logs waha-kecamatan --tail 100
```

Look for webhook-related errors.

---

### Issue: n8n Not Receiving Webhooks

**Check n8n logs**:
```powershell
docker logs n8n-kecamatan --tail 100
```

**Verify webhook URL**:
- The webhook URL should be accessible from WAHA container
- Test: `docker exec waha-kecamatan curl http://n8n-kecamatan:5678/webhook/whatsapp-incoming`

---

### Issue: Laravel API Not Receiving Data

**Check Laravel logs**:
```powershell
docker logs whatsapp-api-gateway --tail 100
```

**Check transaction logs**:
```powershell
Get-Content laravel-api/storage/logs/transactions-*.log -Tail 50
```

---

### Issue: Dashboard Not Receiving Data

**Check dashboard logs**:
```powershell
docker logs dashboard-kecamatan --tail 100
```

**Verify token configuration**:
- Ensure `DASHBOARD_API_TOKEN` matches in both:
  - `whatsapp/.env`
  - `whatsapp/laravel-api/.env`
  - `dashboard-kecamatan/.env`

---

## WAHA Event Types Reference

| Event | Description |
|-------|-------------|
| `message` | New message received |
| `message.any` | Any message type (private, group, broadcast) |
| `message.create` | Message created |
| `message.edit` | Message edited |
| `message.revoke` | Message revoked/deleted |
| `session.status` | Session status changed |
| `contacts.upsert` | Contact updated |
| `chats.upsert` | Chat updated |
| `chats.delete` | Chat deleted |
| `chats.update` | Chat metadata updated |

For capturing all messages (private and group), use:
```json
["message", "message.any"]
```

---

## WAHA Chat ID Format

| Type | Format | Example |
|------|--------|---------|
| Private Chat | `{phone}@c.us` | `6281234567890@c.us` |
| Group Chat | `{group_id}@g.us` | `6281234567890-1234567890@g.us` |
| Broadcast | `{broadcast_id}@broadcast` | `1234567890@broadcast` |

When sending replies, use the correct format based on the message type.

---

## Next Steps

1. **Run the fix script**: `.\fix-whatsapp-autoreply.ps1`
2. **Re-import n8n workflow** with the fixed configuration
3. **Configure dashboard API token** in Laravel API
4. **Test with a real WhatsApp message**
5. **Implement auto-reply** (choose one of the options above)
6. **Monitor logs** to ensure everything is working

---

## Additional Resources

- WAHA Documentation: https://waha.devlike.pro/
- WAHA GitHub: https://github.com/devlikeapro/waha
- n8n Documentation: https://docs.n8n.io/
- Laravel Documentation: https://laravel.com/docs
