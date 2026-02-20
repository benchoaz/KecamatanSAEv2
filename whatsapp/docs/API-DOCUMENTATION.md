# WhatsApp Automation System v4 - API Documentation

## Overview

WhatsApp Automation System v4 provides a modular, secure, and scalable architecture for handling WhatsApp-based public services for Kecamatan Besuk.

## Architecture

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   WhatsApp      │────▶│   WAHA API      │────▶│   n8n Router    │
│   Users         │     │   (Docker)      │     │   (Workflow)    │
└─────────────────┘     └─────────────────┘     └────────┬────────┘
                                                         │
                        ┌────────────────────────────────┼────────────────────────────────┐
                        │                                │                                │
                        ▼                                ▼                                ▼
                ┌───────────────┐               ┌───────────────┐               ┌───────────────┐
                │  Laravel API  │               │  Dashboard    │               │  External     │
                │  Gateway      │               │  Kecamatan    │               │  Services     │
                └───────┬───────┘               └───────────────┘               └───────────────┘
                        │
        ┌───────────────┼───────────────┬───────────────┬───────────────┐
        │               │               │               │               │
        ▼               ▼               ▼               ▼               ▼
   ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐    ┌─────────┐
   │ Rate    │    │ Phone   │    │ Owner   │    │ Complaint│   │ Input   │
   │ Limit   │    │ Mask    │    │ PIN     │    │ Service │    │ Sanitize│
   │ Service │    │ Service │    │ Service │    │         │    │ Service │
   └─────────┘    └─────────┘    └─────────┘    └─────────┘    └─────────┘
```

## Base URL

```
Production: https://api.kecamatan-besuk.go.id
Development: http://localhost:8001
```

## Authentication

All API endpoints require Bearer token authentication:

```http
Authorization: Bearer YOUR_API_TOKEN
```

## Rate Limiting

- **Limit**: 10 requests per minute per phone number
- **Headers**: 
  - `X-RateLimit-Limit`: Maximum requests per minute
  - `X-RateLimit-Remaining`: Remaining requests in current window
  - `X-RateLimit-Reset`: Unix timestamp when the rate limit resets

---

## Endpoints

### Health Check

```http
GET /api/health
```

**Response:**
```json
{
  "status": "healthy",
  "service": "whatsapp-api-gateway",
  "version": "4.0.0",
  "timestamp": "2026-02-13T12:00:00Z"
}
```

---

### Owner Endpoints

#### Request New PIN

Request a new PIN for owner verification. The PIN will be sent via WhatsApp.

```http
POST /api/owner/request-pin
```

**Request Body:**
```json
{
  "phone": "6281234567890"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "PIN baru berhasil dibuat",
  "data": {
    "pin": "123456",
    "phone": "6281234567890",
    "listings": [
      {"id": 1, "type": "umkm"},
      {"id": 2, "type": "jasa"}
    ]
  }
}
```

**Error Response (404):**
```json
{
  "success": false,
  "message": "Nomor ini tidak terdaftar sebagai owner UMKM/Jasa"
}
```

---

#### Verify PIN

Verify owner PIN for authentication.

```http
POST /api/owner/verify-pin
```

**Request Body:**
```json
{
  "phone": "6281234567890",
  "pin": "123456"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "PIN verified successfully",
  "data": {
    "owner_phone": "6281234567890",
    "listings": [
      {"id": 1, "type": "umkm"},
      {"id": 2, "type": "jasa"}
    ]
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "PIN salah"
}
```

---

#### Toggle Listing Visibility

Open or close a listing. Requires PIN verification.

```http
POST /api/owner/toggle-listing
```

**Request Body:**
```json
{
  "phone": "6281234567890",
  "pin": "123456",
  "listing_id": 1,
  "listing_type": "umkm",
  "action": "close"
}
```

**Parameters:**
| Field | Type | Description |
|-------|------|-------------|
| phone | string | Owner's WhatsApp number |
| pin | string | 6-digit PIN |
| listing_id | integer | ID of the listing |
| listing_type | string | "umkm" or "jasa" |
| action | string | "open" or "close" |

**Success Response (200):**
```json
{
  "success": true,
  "message": "Lapak berhasil ditutup",
  "data": {
    "id": 1,
    "type": "umkm",
    "is_listed": false
  }
}
```

---

#### Get Owner Listings

Get all listings for a phone number.

```http
GET /api/owner/listings?phone=6281234567890
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "owner_phone": "6281234567890",
    "listings": [
      {
        "id": 1,
        "type": "umkm",
        "name": "Toko Buah Segar",
        "product": "Buah-buahan",
        "is_listed": true,
        "is_active": true
      }
    ]
  }
}
```

---

### Complaint Endpoints

#### Store Pending Complaint

Store a complaint that awaits user confirmation.

```http
POST /api/complaint/pending
```

**Request Body:**
```json
{
  "phone": "6281234567890",
  "complaint_type": "pelayanan",
  "description": "Pelayanan lambat",
  "category": "kecamatan"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Pengaduan menunggu konfirmasi",
  "data": {
    "pending_id": "abc123",
    "expires_in": 300
  }
}
```

---

#### Confirm Complaint

Confirm and create the pending complaint.

```http
POST /api/complaint/confirm
```

**Request Body:**
```json
{
  "phone": "6281234567890",
  "pending_id": "abc123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Pengaduan berhasil dikirim",
  "data": {
    "ticket_number": "PGK-2026021301"
  }
}
```

---

#### Cancel Pending Complaint

Cancel a pending complaint.

```http
POST /api/complaint/cancel
```

**Request Body:**
```json
{
  "phone": "6281234567890",
  "pending_id": "abc123"
}
```

---

### Search Endpoints

#### Search UMKM

```http
GET /api/umkm/search?q=kerupuk
```

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Kerupuk Ibu Ani",
      "product": "Kerupuk Udang",
      "price": "Rp 15.000/bungkus",
      "contact_wa": "6281234567890",
      "description": "Kerupuk udang segar",
      "is_listed": true
    }
  ]
}
```

---

#### Search Jasa

```http
GET /api/jasa/search?q=tukang
```

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Jasa Tukang Bangunan",
      "product": "Renovasi Rumah",
      "price": "Negotiable",
      "contact_wa": "6281234567890",
      "description": "Tukang profesional"
    }
  ]
}
```

---

#### Search Loker

```http
GET /api/loker/search?q=sopir
```

**Success Response (200):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Sopir Box",
      "job_category": "Transportasi",
      "contact_wa": "6281234567890",
      "work_time": "Full Time",
      "description": "Butuh sopir box pengalaman 2 tahun"
    }
  ]
}
```

---

### Status Check

```http
GET /api/status/check?identifier=081234567890
```

**Success Response (200):**
```json
{
  "success": true,
  "data": {
    "ticket": "PGK-2026021301",
    "status": "Diproses",
    "created_at": "2026-02-13",
    "updated_at": "2026-02-14"
  }
}
```

---

## WhatsApp Commands

Users can send these commands via WhatsApp:

| Command | Description |
|---------|-------------|
| `MENU` | Show main menu |
| `STATUS <nomor>` | Check document status |
| `UMKM <keyword>` | Search UMKM products |
| `JASA <keyword>` | Search services |
| `LOKER <keyword>` | Search job vacancies |
| `SYARAT <layanan>` | Get requirements info |
| `TUTUP LAPAK` | Close listing (owner) |
| `BUKA LAPAK` | Open listing (owner) |
| `PIN BARU` | Request new PIN (owner) |

---

## Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Invalid JSON or missing parameters |
| 401 | Unauthorized - Invalid or missing PIN |
| 403 | Forbidden - No access to the resource |
| 404 | Not Found - Resource not found |
| 422 | Validation Error - Invalid input data |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error |
| 502 | Bad Gateway - Dashboard API error |

---

## Phone Number Format

All phone numbers should be in international format:
- ✅ `6281234567890`
- ✅ `081234567890` (will be converted)
- ✅ `+6281234567890` (will be converted)

---

## Security Features

1. **Rate Limiting**: 10 requests/minute per phone
2. **Phone Masking**: Phone numbers are masked in public responses
3. **PIN Verification**: 6-digit PIN required for owner actions
4. **Confirmation Flow**: Complaints require explicit confirmation
5. **Input Sanitization**: All inputs are validated and sanitized
6. **Security Headers**: All responses include security headers

---

## n8n Workflows

The system uses modular n8n workflows:

| Workflow | Purpose |
|----------|---------|
| `whatsapp-router.json` | Main router with intent detection |
| `wf-menu.json` | Menu display |
| `wf-status.json` | Status checking |
| `wf-faq.json` | FAQ search |
| `wf-umkm.json` | UMKM search |
| `wf-jasa.json` | Jasa search |
| `wf-loker.json` | Loker search |
| `wf-owner-toggle.json` | Owner listing toggle |
| `wf-pin-request.json` | PIN request |
| `wf-complaint.json` | Complaint handling |

---

## Deployment

### Docker Compose

```yaml
version: '3.8'
services:
  waha:
    image: devlikeapro/waha:latest
    ports:
      - "3000:3000"
    environment:
      - WHATSAPP_HOOK_URL=http://n8n:5678/webhook/whatsapp-layanan

  n8n:
    image: n8nio/n8n:latest
    ports:
      - "5678:5678"
    volumes:
      - ./n8n-workflows:/home/node/.n8n

  laravel-api:
    build: ./laravel-api
    ports:
      - "8001:80"
    environment:
      - DASHBOARD_API_URL=http://dashboard:80
      - DASHBOARD_API_TOKEN=your-token
```

---

## Changelog

### v4.0.0 (2026-02-13)
- Modular n8n workflow architecture
- PIN-based owner verification
- Confirmation-based complaints
- Rate limiting per phone number
- Phone number masking
- Input sanitization
- Comprehensive logging
