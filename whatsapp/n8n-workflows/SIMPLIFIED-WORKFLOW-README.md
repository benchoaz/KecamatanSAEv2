# WhatsApp Bot - Simplified Workflow Documentation

## Overview
This is a drastically simplified n8n workflow that follows the "Dashboard as Brain" architecture. The workflow has only **5 nodes** compared to the previous 30+ node complex workflows.

## Architecture Principle
- **n8n**: Router only (no business logic)
- **Dashboard**: Contains ALL business logic, state management, and decision-making
- **WAHA**: WhatsApp message transport only

## Workflow Nodes

### 1. WAHA Webhook
- **Type**: Webhook Trigger
- **Purpose**: Receives incoming WhatsApp messages from WAHA
- **Configuration**: 
  - Path: `/whatsapp-webhook`
  - Method: POST

### 2. Anti-Loop Filter
- **Type**: IF condition
- **Purpose**: Prevents bot from responding to its own messages
- **Logic**: Filter out messages where `fromMe === true`
- **Critical**: This prevents infinite loops

### 3. Call Dashboard API
- **Type**: HTTP Request
- **Purpose**: Send message to Laravel Dashboard for processing
- **Endpoint**: `POST /api/whatsapp/handle`
- **Headers**: `Authorization: Bearer {WHATSAPP_API_TOKEN}`
- **Body**:
  ```json
  {
    "phone": "62822xxxx",
    "message": "user message"
  }
  ```
- **Response**: 
  ```json
  {
    "success": true,
    "reply": "Bot response text",
    "state_update": null
  }
  ```

### 4. Send to WhatsApp
- **Type**: HTTP Request
- **Purpose**: Send bot response back to user via WAHA
- **Endpoint**: `POST /api/sendText` (WAHA endpoint)
- **Body**:
  ```json
  {
    "session": "default",
    "chatId": "62822xxxx@c.us",
    "text": "{{ reply from dashboard }}"
  }
  ```

### 5. Respond to Webhook
- **Type**: Respond to Webhook
- **Purpose**: Close the webhook request
- **Configuration**: Return all incoming items

## Environment Variables Required

```env
DASHBOARD_URL=http://your-dashboard-url
WAHA_URL=http://your-waha-url
WHATSAPP_API_TOKEN=your-secure-token
```

## What This Workflow Does NOT Do

❌ Intent detection (handled by Dashboard)
❌ State management (handled by Dashboard)
❌ Business logic (handled by Dashboard)
❌ Database queries (handled by Dashboard)
❌ Search filtering (handled by Dashboard)
❌ Status checking (handled by Dashboard)
❌ Complaint validation (handled by Dashboard)
❌ Owner verification (handled by Dashboard)

## What This Workflow DOES

✅ Receive messages from WAHA
✅ Filter out bot's own messages
✅ Forward to Dashboard
✅ Send Dashboard's response back to user

## Comparison with Old Workflow

| Aspect | Old Workflow | New Workflow |
|--------|--------------|--------------|
| **Nodes** | 30+ nodes | 5 nodes |
| **Complexity** | High | Very Low |
| **Logic Location** | n8n | Dashboard |
| **State Management** | n8n variables | Database |
| **Maintenance** | Hard | Easy |
| **Debugging** | Complex | Simple |
| **Scalability** | Poor | Excellent |

## Testing

### Test with cURL (simulate WAHA webhook)

```bash
curl -X POST http://your-n8n-url/webhook/whatsapp-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "from": "6282299887766@c.us",
    "body": "menu",
    "fromMe": false,
    "timestamp": 1676543210
  }'
```

### Expected Flow

1. n8n receives webhook
2. Checks `fromMe !== true` ✓
3. Calls Dashboard: `POST /api/whatsapp/handle`
4. Dashboard responds with menu message
5. n8n sends response to WAHA
6. WAHA sends to user

## Migration from Old Workflow

1. **Backup** existing workflows
2. **Import** this new workflow to n8n
3. **Configure** environment variables
4. **Test** with sample messages
5. **Deactivate** old workflows
6. **Activate** new workflow
7. **Monitor** logs

## Troubleshooting

### Bot not responding
1. Check n8n execution logs
2. Verify `DASHBOARD_URL` is accessible from n8n
3. Check API token is valid
4. Test `/api/whatsapp/health` endpoint

### Bot responding to itself (loop)
1. Verify anti-loop filter is active
2. Check `fromMe` field in WAHA webhook payload

### Slow responses
1. Check Dashboard response time
2. Optimize database queries in handlers
3. Add caching if needed

## Monitoring

Check these metrics:
- **Total messages/day**: Track in `whatsapp_logs` table
- **Success rate**: Check `success` column in logs
- **Response time**: Monitor Dashboard API performance
- **Active sessions**: Count in `whatsapp_sessions` table

## Security Notes

- ✅ Bearer token authentication
- ✅ Rate limiting (60 req/min)
- ✅ Anti-loop protection
- ✅ No sensitive data in n8n
- ✅ All validation in Dashboard
