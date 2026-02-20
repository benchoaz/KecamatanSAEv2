# WhatsApp Bot Integration - Comprehensive Test Plan

## Test Plan Overview
This test plan verifies the refactor implementation of the WhatsApp bot integration, ensuring all audit findings have been resolved. The dashboard is now the single source of truth for all business logic, with n8n simplified to a 4-node router.

## 1. Test Environment Setup

### 1.1 Prerequisites
- Test WhatsApp number: `6282231203765`
- n8n workflow configured to use simplified router
- Dashboard API endpoint accessible

### 1.2 Database Seeding
```bash
# Run all seeders
cd dashboard-kecamatan
php artisan migrate:fresh --seed
```

### 1.3 Environment Variables
```env
# Dashboard environment variables
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=kecamatan_besuk
DB_USERNAME=root
DB_PASSWORD=

# WhatsApp integration
WHATSAPP_API_URL=http://localhost:3000
WHATSAPP_API_KEY=your-api-key
```

### 1.4 Verification
```bash
# Check if database has seeded data
php artisan tinker
>>> PelayananFaq::count() >= 4
>>> UmkmLocal::count() >= 3
>>> Loker::count() >= 2
>>> PublicService::where('phone', '6282231203765')->count() >= 1
```

---

## 2. Core Functionality Tests

### 2.1 Menu Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| MENU-001 | Test MENU command shows all options | 1. Send "MENU" <br>2. Wait for response | Response includes 5 options: STATUS, SYARAT, UMKM & JASA, LOKER, PENGADUAN | | |
| MENU-002 | Test SYARAT menu is explicit option | 1. Send "MENU" <br>2. Check menu options | SYARAT is listed as an explicit menu option | | |
| MENU-003 | Test MENU command from active session | 1. Send "PENGADUAN" <br>2. Send "MENU" | Returns to main menu, clears active session | | |

### 2.2 SYARAT/FAQ Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| SYARAT-001 | Test SYARAT command without query | 1. Send "SYARAT" <br>2. Wait for response | Returns list of available FAQ categories or instructions | | |
| SYARAT-002 | Test "syarat ktp" query | 1. Send "syarat ktp" <br>2. Wait for response | Returns KTP requirements with answer and keywords | | Seeded data exists |
| SYARAT-003 | Test "kk baru" query | 1. Send "kk baru" <br>2. Wait for response | Returns KK Baru requirements | | Seeded data exists |
| SYARAT-004 | Test "akta lahir" query | 1. Send "akta lahir" <br>2. Wait for response | Returns Akta Lahir requirements | | Seeded data exists |
| SYARAT-005 | Test "pindah domisili" query | 1. Send "pindah domisili" <br>2. Wait for response | Returns Pindah Domisili requirements | | Seeded data exists |
| SYARAT-006 | Test FAQ search with partial keywords | 1. Send "syarat kt" <br>2. Wait for response | Returns KTP-related results | | Fuzzy search test |
| SYARAT-007 | Test SYARAT command from menu | 1. Send "MENU" <br>2. Send "SYARAT" <br>3. Send "ktp" | Returns KTP requirements | | Session management test |

### 2.3 UMKM Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| UMKM-001 | Test "umkm" command (list all) | 1. Send "umkm" <br>2. Wait for response | Returns list of all UMKM with contact_wa | | Seeded data exists |
| UMKM-002 | Test "umkm kerupuk" search | 1. Send "umkm kerupuk" <br>2. Wait for response | Returns kerupuk-related UMKM | | Seeded data exists |
| UMKM-003 | Test "madu" search | 1. Send "madu" <br>2. Wait for response | Returns honey-related UMKM | | Seeded data exists |
| UMKM-004 | Test UMKM with invalid query | 1. Send "umkm abc123" <br>2. Wait for response | Returns no results message | | No matching data |
| UMKM-005 | Test UMKM results have valid WA numbers | 1. Send "umkm" <br>2. Check contact_wa format | All results have valid WhatsApp numbers (starts with 62) | | |

### 2.4 JASA Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| JASA-001 | Test "jasa" command (list all) | 1. Send "jasa" <br>2. Wait for response | Returns list of all services | | Seeded data exists |
| JASA-002 | Test "jasa ac" search | 1. Send "jasa ac" <br>2. Wait for response | Returns AC repair services | | Seeded data exists |
| JASA-003 | Test "piket" search | 1. Send "jasa piket" <br>2. Wait for response | Returns security services | | Seeded data exists |
| JASA-004 | Test JASA distinct from UMKM | 1. Send "jasa" <br>2. Send "umkm" <br>3. Compare results | Results are distinct (no overlapping items) | | |
| JASA-005 | Test JASA with invalid query | 1. Send "jasa xyz" <br>2. Wait for response | Returns no results message | | |

### 2.5 LOKER Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| LOKER-001 | Test "loker" command (list active) | 1. Send "loker" <br>2. Wait for response | Returns list of active jobs (status='aktif') | | Seeded data exists |
| LOKER-002 | Test "loker sopir" search | 1. Send "loker sopir" <br>2. Wait for response | Returns driver job listings | | Seeded data exists |
| LOKER-003 | Test "admin" search | 1. Send "loker admin" <br>2. Wait for response | Returns admin job listings | | Seeded data exists |
| LOKER-004 | Test LOKER status filter | 1. Check database for inactive jobs <br>2. Send "loker" <br>3. Verify no inactive jobs | Only active jobs (status='aktif') are returned | | |

### 2.6 STATUS Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| STATUS-001 | Test STATUS with test number | 1. Send "STATUS" <br>2. Wait for response | Returns service records for '6282231203765' | | Seeded data exists |
| STATUS-002 | Test STATUS with explicit number | 1. Send "STATUS 6282231203765" <br>2. Wait for response | Returns service records | | |
| STATUS-003 | Test STATUS with invalid number | 1. Send "STATUS 1234567890" <br>2. Wait for response | Returns "no records found" message | | |
| STATUS-004 | Test STATUS from menu | 1. Send "MENU" <br>2. Send "STATUS" | Returns service records | | |

### 2.7 PENGADUAN Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| PENGADUAN-001 | Test complete pengaduan flow | 1. Send "PENGADUAN" <br>2. Send "Keluhan tentang jalan rusak" <br>3. Wait for response | 1. Bot asks for complaint <br>2. Bot confirms complaint received | | Session management test |
| PENGADUAN-002 | Test pengaduan with empty message | 1. Send "PENGADUAN" <br>2. Send " " (empty message) <br>3. Wait for response | Bot prompts for valid complaint | | |
| PENGADUAN-003 | Test canceling pengaduan | 1. Send "PENGADUAN" <br>2. Send "Batal" <br>3. Wait for response | Returns to main menu, clears session | | |

---

## 3. Edge Case Tests

### 3.1 Invalid Input Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| EDGE-001 | Test random text input | 1. Send "abc123xyz" <br>2. Wait for response | Returns "unknown intent" message with menu option | | |
| EDGE-002 | Test special characters | 1. Send "!@#$%^&*()" <br>2. Wait for response | Returns "unknown intent" message | | |
| EDGE-003 | Test image input | 1. Send image <br>2. Wait for response | Returns appropriate response for media input | | Media handling test |
| EDGE-004 | Test video input | 1. Send video <br>2. Wait for response | Returns appropriate response for media input | | |
| EDGE-005 | Test very long message | 1. Send message with 200+ characters <br>2. Wait for response | Handles message correctly without errors | | |

### 3.2 Session Management Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| EDGE-006 | Test session timeout (30 mins) | 1. Send "PENGADUAN" <br>2. Wait 30 minutes <br>3. Send complaint <br>4. Wait for response | Session expired, returns to menu | | |
| EDGE-007 | Test multiple consecutive commands | 1. Send "MENU" <br>2. Send "STATUS" <br>3. Send "MENU" <br>4. Send "SYARAT" | Each command executed correctly | | |
| EDGE-008 | Test overlapping commands | 1. Send "PENGADUAN" <br>2. Send "MENU" <br>3. Verify session | Session cleared, menu displayed | | |

---

## 4. Error Handling Tests

### 4.1 Connection Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| ERROR-001 | Test network failure | 1. Stop dashboard API <br>2. Send message <br>3. Wait for response | n8n retries or returns error message | | |
| ERROR-002 | Test n8n connection failure | 1. Stop n8n service <br>2. Send message <br>3. Check system behavior | Appropriate error handling | | |
| ERROR-003 | Test database connection error | 1. Stop database <br>2. Send message <br>3. Wait for response | User-friendly error message | | |

### 4.2 Data Error Tests

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| ERROR-004 | Test empty database | 1. Truncate all tables <br>2. Send "MENU" <br>3. Send "UMKM" <br>4. Check responses | Handles empty database gracefully | | |
| ERROR-005 | Test invalid data formats | 1. Corrupt some database records <br>2. Test various commands <br>3. Check responses | Handles corrupt data without crashing | | |

---

## 5. n8n Integration Tests

### 5.1 Router Functionality

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| N8N-001 | Test API call from n8n | 1. Send message via WhatsApp <br>2. Check n8n execution log <br>3. Verify API call to dashboard | n8n correctly routes to dashboard API | | |
| N8N-002 | Test anti-loop filter | 1. Send same message 3 times <br>2. Check responses | Subsequent messages handled appropriately | | |
| N8N-003 | Test response formatting | 1. Send "MENU" <br>2. Check n8n response processing <br>3. Verify WhatsApp message | Response correctly formatted for WhatsApp | | |

---

## 6. Regression Tests

### 6.1 Audit Fixes Verification

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| REGRESS-001 | Test state loss in pengaduan flow | 1. Send "PENGADUAN" <br>2. Send complaint message <br>3. Verify response | Complaint properly recorded (no state loss) | | |
| REGRESS-002 | Test routing consistency | 1. Send "cek status berkas" in pengaduan session <br>2. Verify response | Responds to complaint flow, not status check | | |
| REGRESS-003 | Test fallback loops | 1. Send invalid input 5 times <br>2. Check responses | No repeated fallback loops | | |

### 6.2 Menu Options Verification

| Test ID | Test Description | Steps to Execute | Expected Result | Pass/Fail | Notes |
|---------|-----------------|------------------|-----------------|-----------|-------|
| REGRESS-004 | Test all menu options exist | 1. Send "MENU" <br>2. Check all 5 options | MENU shows: STATUS, SYARAT, UMKM & JASA, LOKER, PENGADUAN | | |
| REGRESS-005 | Test menu accessibility | 1. Test from various states <br>2. Verify menu command works | "MENU" command works from any state | | |

---

## Test Execution Notes

### Test Data Requirements
- All seeders must be run successfully
- Test number `6282231203765` must have at least 1 service record
- At least 4 FAQ items must exist (KTP, KK, Akta Lahir, Pindah Domisili)
- At least 3 UMKM and 2 JASA records must exist
- At least 2 active LOKER records must exist

### Test Execution Order
1. Environment setup and verification
2. Core functionality tests (menu, SYARAT, UMKM, JASA, LOKER, STATUS, PENGADUAN)
3. Edge case tests
4. Error handling tests
5. n8n integration tests
6. Regression tests

### Test Tools
- WhatsApp client for testing
- Browser DevTools for API inspection
- n8n workflow execution logs
- Laravel Telescope or Debugbar for debugging
- MySQL client for database verification

---

## Success Criteria
- All tests pass
- No state loss in any conversation flow
- Responses are timely and relevant
- Error messages are user-friendly
- Database operations are correctly handled
- n8n integration works as simplified router
