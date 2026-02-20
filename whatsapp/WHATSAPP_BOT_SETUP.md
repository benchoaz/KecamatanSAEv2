# WhatsApp Bot Setup Guide

## Overview

This guide explains how to set up the WhatsApp bot using n8n and WAHA (WhatsApp HTTP API).

## Prerequisites

- n8n running on `http://localhost:8080`
- WAHA running on `http://localhost:3099`
- WAHA authentication configured

## Step 1: Import the Workflow

### Option A: Import via n8n UI

1. Open n8n at `http://localhost:8080`
2. Click on **Settings** (gear icon) → **Import from file**
3. Select the file: `whatsapp/n8n-workflows/whatsapp-bot-final.json`
4. Click **Import**

### Option B: Copy-Paste Import

1. Open n8n at `http://localhost:8080`
2. Click **+ Add workflow** → **Import from JSON**
3. Copy the content from [`whatsapp-bot-final.json`](whatsapp/n8n-workflows/whatsapp-bot-final.json)
4. Paste into the import dialog
5. Click **Import**

## Step 2: Configure the Workflow

### Update HTTP Header Authentication (if needed)

1. Click on the **Send Reply** node
2. Scroll to **Authentication** section
3. Verify or create **Header Auth** credential with:
   - **Header**: `X-Api-Key` (or whatever WAHA expects)
   - **Value**: Your WAHA API key
4. Click **Save**

### Verify Webhook URL

The webhook URL will be:
```
http://localhost:8080/webhook/whatsapp-bot
```

**Important:** n8n automatically adds `/webhook/` prefix to the path configured in the Webhook node.

## Step 3: Activate the Workflow

1. In n8n, find the **WhatsApp Bot Final** workflow
2. Click the **Active** toggle (top-right corner)
3. Confirm activation when prompted
4. The toggle should turn **green** when active

## Step 4: Configure WAHA Webhook

### Using WAHA Dashboard

1. Open WAHA dashboard at `http://localhost:3099`
2. Go to **Settings** → **Webhooks**
3. Set **Webhook URL** to:
   ```
   http://localhost:8080/webhook/whatsapp-bot
   ```
4. Select **Events** to subscribe to:
   - ✅ `message`
   - ✅ `message.any`
5. Click **Save**

### Using WAHA API

```bash
curl -X POST http://localhost:3099/api/webhooks \
  -H "Content-Type: application/json" \
  -d '{
    "url": "http://localhost:8080/webhook/whatsapp-bot",
    "events": ["message", "message.any"]
  }'
```

### Using WAHA Plus Dashboard (Alternative)

If using WAHA Plus version:
1. Open `http://localhost:3099/dashboard/plus`
2. Go to **Settings** → **Webhooks**
3. Add webhook URL: `http://localhost:8080/webhook/whatsapp-bot`

## Step 5: Test the Integration

### Test 1: Verify Webhook is Reachable

```bash
curl -X POST http://localhost:8080/webhook/whatsapp-bot \
  -H "Content-Type: application/json" \
  -d '{
    "event": "message",
    "data": {
      "chatId": "1234567890@c.us",
      "sender": "1234567890@c.us",
      "text": {
        "body": "Hello, this is a test message"
      }
    }
  }'
```

**Expected Response:** `200 OK` (from Respond to Webhook node)

### Test 2: Send a Real WhatsApp Message

1. Make sure WAHA is connected to WhatsApp (check session status)
2. Send a message to the WhatsApp number
3. Check n8n for execution history
4. Verify the reply is sent back

### Test 3: Check n8n Executions

1. Open n8n at `http://localhost:8080`
2. Go to **Executions** (left sidebar)
3. Find the **WhatsApp Bot Final** workflow
4. Click on an execution to see detailed results
5. Check each node's output for debugging

## Workflow Nodes Overview

| Node | Purpose |
|------|---------|
| **Webhook** | Receives POST requests at `/webhook/whatsapp-bot` |
| **Extract Message** | Parses WAHA payload and extracts message text |
| **Send Reply** | Sends text reply back via WAHA API |
| **Respond to Webhook** | Returns 200 OK to WAHA |

## WAHA API Reference

### Send Text Message

```bash
curl -X POST http://localhost:3099/api/sendText \
  -H "Content-Type: application/json" \
  -d '{
    "chatId": "1234567890@c.us",
    "text": "Your reply message here"
  }'
```

### Check Session Status

```bash
curl http://localhost:3099/api/sessions
```

## Troubleshooting

### Issue: No executions in n8n

**Possible causes:**
1. Workflow not activated → Activate it
2. Wrong webhook URL in WAHA → Verify URL is exactly `http://localhost:8080/webhook/whatsapp-bot`
3. Network connectivity → Ensure n8n and WAHA can communicate
4. Firewall blocking → Check Windows Defender Firewall

### Issue: 404 from webhook

**Solution:**
- Verify the path is `whatsapp-bot` (no leading slash)
- Full URL: `http://localhost:8080/webhook/whatsapp-bot`

### Issue: WAHA not sending webhooks

**Solutions:**
1. Check WAHA session is connected: `http://localhost:3099/api/sessions`
2. Verify webhook is registered: `http://localhost:3099/api/webhooks`
3. Restart WAHA container if needed

### Issue: Message not extracted correctly

**Solution:**
- The Extract Message node has debugging logs
- Check n8n execution details for full payload
- Update the extraction logic based on your WAHA version

## Configuration Summary

| Component | Value |
|-----------|-------|
| n8n URL | `http://localhost:8080` |
| Webhook Path | `whatsapp-bot` |
| Full Webhook URL | `http://localhost:8080/webhook/whatsapp-bot` |
| WAHA API | `http://localhost:3099` |
| Send Text Endpoint | `http://localhost:3099/api/sendText` |

## File Location

- Workflow file: [`whatsapp/n8n-workflows/whatsapp-bot-final.json`](whatsapp/n8n-workflows/whatsapp-bot-final.json)
- Setup guide: [`whatsapp/WHATSAPP_BOT_SETUP.md`](whatsapp/WHATSAPP_BOT_SETUP.md)
