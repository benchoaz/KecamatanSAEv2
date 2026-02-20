# Review Arsitektur & Implementasi Domain Desa
**Project**: Dashboard Kecamatan - Refactoring Modul Desa
**Tanggal**: 28 Januari 2026

## 1. Latar Belakang & Tujuan
Tujuan utama refactoring ini adalah memisahkan secara tegas antara **Domain Kecamatan** (sebagai supervisor/verifikator) dan **Domain Desa** (sebagai entitas pelapor mandiri). 
Prinsip utama: **"Desa = Pelaksana/Input, Kecamatan = Monitor/Pembina"**.
Sistem harus **Audit-Safe**, artinya data yang sudah dikirim tidak boleh diubah sembarangan tanpa jejak.

## 2. Arsitektur Database (Final)
Kami telah menerapkan skema tabel baru yang terpisah dari tabel legacy untuk fleksibilitas dan keamanan audit.

### A. Tabel Utama: `desa_submissions`
Tabel ini bertindak sebagai "amplop" universal untuk semua jenis laporan (Perencanaan, Pembangunan, Keuangan, dll).
*   **PK**: UUID (untuk keamanan ID).
*   **Desa Isolation**: `desa_id` wajib ada & di-index.
*   **Workflow**: Menggunakan kolom `status` (ENUM: draft, submitted, returned, completed).
*   **Audit**: `submitted_at`, `completed_at`, `created_by`.

### B. Tabel Anak (Relasi)
Menggunakan pendekatan semistructured untuk detail agar tidak perlu sering mengubah skema database saat formulir berubah.
1.  **`desa_submission_details`**: Menyimpan data key-value (EAV-like) untuk isi formulir yang dinamis.
2.  **`desa_submission_files`**: Menyimpan referensi file bukti dukung (Foto 0%, 50%, 100%, BA Musdes, dll).
3.  **`desa_submission_notes`**: Menyimpan riwayat catatan pembinaan dari kecamatan jika laporan dikembalikan (Returned).

## 3. Workflow Operasional (State Machine)
Sistem menggunakan alur status yang linear dan ketat (hard-coded logic di Model):

1.  **DRAFT**:
    *   Satu-satunya status di mana Operator Desa bisa mengedit data/file.
    *   Data belum terlihat oleh Kecamatan (bersifat lokal).
2.  **SUBMITTED**:
    *   Data terkunci (Read Only bagi Desa).
    *   Kecamatan menerima notifikasi dan mulai memeriksa.
3.  **RETURNED**:
    *   Jika Kecamatan menemukan kekurangan, status diubah ke Returned dengan **Catatan Wajib**.
    *   Pintu edit terbuka kembali untuk Desa (kembali bisa edit & upload).
4.  **COMPLETED**:
    *   Laporan diterima dan dinyatakan selesai secara administratif.
    *   Data terkunci permanen sebagai arsip.

## 4. Implementasi Teknis Saat Ini

### Migration
*   Status: **DONE**
*   File: `2026_01_28_035045_create_desa_operational_tables.php` sukses dijalankan.

### Models
*   `DesaSubmission`: Menggunakan **Global Scope** (`DesaScope`) untuk isolasi data otomatis. Hanya data desa user yang login yang bisa diakses.
*   Method `isEditable()`: Sentralisasi logika ketersediaan form edit.
*   Relasi ke `details`, `files`, `notes` sudah didefinisikan.

### Controller
*   `SubmissionController`: Sudah mengimplementasikan method dasar (`store`, `submit`, `edit`) dengan proteksi `isEditable()`.
*   Menggunakan Transaction (`DB::beginTransaction`) untuk menjamin integritas data saat pembuatan submission.

## 5. Rekomendasi Selanjutnya (Untuk AI Berikutnya)
Langkah selanjutnya fokus pada **Frontend & UX**:
1.  **Buat View Blade**: `desa/submissions/create.blade.php` dan `edit.blade.php`.
2.  **Dynamic Form**: Implementasikan logika di View untuk me-render input form berdasarkan `modul` yang dipilih (karena tabel backendnya generik).
3.  **File Upload Handler**: Implementasikan endpoint AJAX untuk upload file progress bar (menggunakan `desa_submission_files`).
4.  **Dashboard Widget**: Tampilkan status "Perlu Perbaikan" (Returned) di dashboard utama agar operator *aware*.

---
*Dokumen ini dibuat otomatis oleh Antigravity System Architect.*
