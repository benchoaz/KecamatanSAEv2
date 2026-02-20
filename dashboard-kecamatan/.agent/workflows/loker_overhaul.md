# Implementation Plan - Overhaul Loker Module

Rebuilding the Job Vacancy (Loker) and Dashboard management module to be simple, consistent with UMKM, and ready for multi-channel integration (WA/n8n).

## 1. Database & Model Overhaul
- Update `lokers` migration to include:
    - `internal_notes` for admin.
    - Standardized `status` values: `menunggu_verifikasi`, `aktif`, `nonaktif`.
- Update `Loker` model:
    - Add `status` constants.
    - Ensure `fillable` includes all necessary fields.
    - Maintain `uuid` and `manage_token` for decoupled management.

## 2. Public Facing (Warga) - Simplified
- **URL**: `/loker`
- **Controller**: `PublicLokerController`
- **Views**:
    - `public/loker/index.blade.php`: Clean, mobile-first card list. Large action buttons (Call/WA).
    - `public/loker/create.blade.php`: Ultra-simple form. No login. Fast entry.
- **Workflow**:
    - User fills form (Title, Category, WhatsApp, Time, etc.).
    - Submission creates `Loker` (status: `menunggu_verifikasi`).
    - Submission creates entry in unified `PublicService` (category: `loker`, source: `web_form`).

## 3. Dashboard Management (Admin)
- **URL**: `kecamatan/layanan/loker`
- **Controller**: `Kecamatan\LayananPublikController`
- **Views**:
    - `kecamatan/layanan/loker/index.blade.php`: Comprehensive table with status badges and source tracking.
    - `kecamatan/layanan/loker/form.blade.php`: Unified form for Create/Edit. Includes `internal_notes` and `is_sensitive` toggle.
- **Features**:
    - Filtering by status/desa.
    - Direct activation/deactivation.

## 4. Integration & Inclusivity
- **Inbox**: Full integration with `PublicService`. Admin sees new Loker submissions in the main "Inbox Terpadu".
- **WA/n8n**: Schema designed to be queryable by n8n bots.
- **Inclusivity**: Large fonts, intuitive colors, and Voice Guide support.

## 5. Technical Steps
1. Refine `lokers` migration and run/simulate.
2. Update `Loker` model.
3. Implement `PublicLokerController` logic.
4. Implement `LayananPublikController` loker methods.
5. Create/Update Blade templates with Premium Dashboard Design.
