<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * View Agregat untuk Real-time Reporting Ekonomi & Pembangunan
     * Berdasarkan analisis struktur database
     */
    public function up(): void
    {
        // Wrap each view in try-catch so missing tables won't fail the entire migration

        // ==================== VIEW EKONOMI ====================

        // Check which optional tables exist
        $hasUmkmLocal   = \Illuminate\Support\Facades\Schema::hasTable('umkm_locals');  // correct plural name
        $hasJobVacancy  = \Illuminate\Support\Facades\Schema::hasTable('job_vacancy');
        $hasWorkDir     = \Illuminate\Support\Facades\Schema::hasTable('work_directory');

        // Build dynamic view SQL based on available tables
        $umkmLocalJoin  = $hasUmkmLocal  ? "LEFT JOIN umkm_locals ul ON ul.desa_id = d.id" : "";
        $jobVacancyJoin = $hasJobVacancy ? "LEFT JOIN job_vacancy jv ON jv.desa_id = d.id" : "";
        $workDirJoin    = $hasWorkDir    ? "LEFT JOIN work_directory wd ON wd.desa_id = d.id" : "";
        $umkmLocalCol   = $hasUmkmLocal  ? "COUNT(DISTINCT ul.id)" : "0";
        $jobVacancyCol  = $hasJobVacancy ? "COUNT(DISTINCT jv.id)" : "0";
        $workDirCol     = $hasWorkDir    ? "COUNT(DISTINCT wd.id)" : "0";

        try {
        // View: Ringkasan Ekonomi per Desa
        DB::statement("
            CREATE OR REPLACE VIEW v_ekonomi_desa_summary AS
            SELECT 
                d.id AS desa_id,
                d.nama_desa,
                d.kode_desa,
                COUNT(DISTINCT u.id) AS total_umkm,
                COUNT(DISTINCT CASE WHEN u.status = 'aktif' THEN u.id END) AS umkm_aktif,
                COUNT(DISTINCT CASE WHEN u.status = 'pending' THEN u.id END) AS umkm_pending,
                {$umkmLocalCol} AS total_umkm_local,
                {$workDirCol} AS total_jasa,
                COUNT(DISTINCT l.id) AS total_lowongan,
                COUNT(DISTINCT CASE WHEN l.status = 'aktif' AND l.tanggal_tutup >= CURDATE() THEN l.id END) AS lowongan_aktif,
                {$jobVacancyCol} AS total_job_vacancy,
                (COUNT(DISTINCT u.id) * 10000000) AS estimasi_nilai_umkm
            FROM desa d
            LEFT JOIN umkm u ON u.desa_id = d.id
            {$umkmLocalJoin}
            {$workDirJoin}
            LEFT JOIN loker l ON l.desa_id = d.id
            {$jobVacancyJoin}
            GROUP BY d.id, d.nama_desa, d.kode_desa
        ");
        } catch (\Exception $e) { \Log::warning('Migration view v_ekonomi_desa_summary skipped: ' . $e->getMessage()); }

        try {
        // View: Ringkasan Ekonomi Kecamatan (Agregat)
        $umkmLocalKec = $hasUmkmLocal ? "LEFT JOIN umkm_locals ul ON 1=1" : "";
        DB::statement("
            CREATE OR REPLACE VIEW v_ekonomi_kecamatan_summary AS
            SELECT 
                'KECAMATAN' AS level,
                'ALL' AS kode,
                'Seluruh Desa' AS nama,
                COUNT(DISTINCT u.id) AS total_umkm,
                COUNT(DISTINCT CASE WHEN u.status = 'aktif' THEN u.id END) AS umkm_aktif,
                COUNT(DISTINCT CASE WHEN u.status = 'pending' THEN u.id END) AS umkm_pending,
                {$umkmLocalCol} AS total_umkm_local,
                {$workDirCol} AS total_jasa,
                COUNT(DISTINCT l.id) AS total_lowongan,
                COUNT(DISTINCT CASE WHEN l.status = 'aktif' AND l.tanggal_tutup >= CURDATE() THEN l.id END) AS lowongan_aktif,
                {$jobVacancyCol} AS total_job_vacancy,
                (COUNT(DISTINCT u.id) * 10000000) AS estimasi_nilai_umkm
            FROM umkm u
            {$umkmLocalKec}
            LEFT JOIN loker l ON 1=1
        ");
        } catch (\Exception $e) { \Log::warning('Migration view v_ekonomi_kecamatan_summary skipped: ' . $e->getMessage()); }

        // View: Kategori Bisnis/Usaha
        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_ekonomi_kategori_summary AS
            SELECT 
                u.jenis_usaha AS kategori,
                COUNT(*) AS jumlah,
                COUNT(CASE WHEN u.status = 'aktif' THEN 1 END) AS aktif,
                COUNT(CASE WHEN u.status = 'pending' THEN 1 END) AS pending,
                COUNT(CASE WHEN u.status = 'nonaktif' THEN 1 END) AS nonaktif
            FROM umkm u
            WHERE u.jenis_usaha IS NOT NULL
            GROUP BY u.jenis_usaha
            ORDER BY jumlah DESC
        ");
        } catch (\Exception $e) { \Log::warning('View v_ekonomi_kategori_summary skipped: ' . $e->getMessage()); }

        // ==================== VIEW PEMBANGUNAN ====================

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_pembangunan_desa_summary AS
            SELECT 
                d.id AS desa_id,
                d.nama_desa,
                d.kode_desa,
                COUNT(pd.id) AS total_proyek,
                COALESCE(SUM(pd.pagu_anggaran), 0) AS total_pagu,
                COALESCE(SUM(pd.realisasi_anggaran), 0) AS total_realisasi,
                MAX(pd.tanggal_mulai) AS proyek_terbaru_mulai,
                MIN(pd.tanggal_selesai) AS proyek_tercepat_selesai
            FROM desa d
            LEFT JOIN pembangunan_desa pd ON pd.desa_id = d.id
            GROUP BY d.id, d.nama_desa, d.kode_desa
        ");
        } catch (\Exception $e) { \Log::warning('View v_pembangunan_desa_summary skipped: ' . $e->getMessage()); }

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_pembangunan_anggaran_tahun AS
            SELECT 
                pd.tahun_anggaran AS tahun,
                COUNT(pd.id) AS jumlah_proyek,
                SUM(pd.pagu_anggaran) AS total_pagu,
                SUM(pd.realisasi_anggaran) AS total_realisasi
            FROM pembangunan_desa pd
            WHERE pd.tahun_anggaran IS NOT NULL
            GROUP BY pd.tahun_anggaran
            ORDER BY pd.tahun_anggaran DESC
        ");
        } catch (\Exception $e) { \Log::warning('View v_pembangunan_anggaran_tahun skipped: ' . $e->getMessage()); }

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_pembangunan_spj_status AS
            SELECT 
                pd.id AS pembangunan_id,
                pd.nama_kegatan,
                d.nama_desa,
                COUNT(pds.id) AS total_dokumen_wajib
            FROM pembangunan_desa pd
            LEFT JOIN pembangunan_dokumen_spj pds ON pds.pembangunan_desa_id = pd.id AND pds.is_wajib = 1
            LEFT JOIN desa d ON d.id = pd.desa_id
            GROUP BY pd.id, pd.nama_kegatan, d.nama_desa
        ");
        } catch (\Exception $e) { \Log::warning('View v_pembangunan_spj_status skipped: ' . $e->getMessage()); }

        // ==================== VIEW EKBANG/LAPORAN ====================

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_ekbang_submission_summary AS
            SELECT 
                d.id AS desa_id,
                d.nama_desa,
                m.kode_menu,
                m.nama_menu,
                COUNT(s.id) AS total_submission,
                MAX(s.created_at) AS submission_terakhir
            FROM desa d
            CROSS JOIN menu m
            LEFT JOIN submissions s ON s.desa_id = d.id AND s.menu_id = m.id
            WHERE m.kode_menu IN ('ekbang', 'pemerintah', 'kesra', 'pembangunan')
            GROUP BY d.id, d.nama_desa, m.kode_menu, m.nama_menu
        ");
        } catch (\Exception $e) { \Log::warning('View v_ekbang_submission_summary skipped: ' . $e->getMessage()); }

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_ekbang_aspek_summary AS
            SELECT 
                d.id AS desa_id,
                d.nama_desa,
                a.kode_aspek,
                a.nama_aspek,
                COUNT(s.id) AS total_laporan,
                MAX(s.created_at) AS laporan_terakhir
            FROM desa d
            CROSS JOIN aspek a
            LEFT JOIN submissions s ON s.desa_id = d.id AND s.aspek_id = a.id
            WHERE a.kode_aspek LIKE 'ekb_%' OR a.kode_aspek LIKE '%monev%'
            GROUP BY d.id, d.nama_desa, a.kode_aspek, a.nama_aspek
        ");
        } catch (\Exception $e) { \Log::warning('View v_ekbang_aspek_summary skipped: ' . $e->getMessage()); }

        // ==================== VIEW MUSRENBANG ====================

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_musrenbang_status AS
            SELECT 
                d.id AS desa_id,
                d.nama_desa,
                um.tahun,
                um.skala,
                um.status,
                COUNT(*) AS jumlah_usulan
            FROM desa d
            LEFT JOIN usulan_musrenbang um ON um.desa_id = d.id
            GROUP BY d.id, d.nama_desa, um.tahun, um.skala, um.status
        ");
        } catch (\Exception $e) { \Log::warning('View v_musrenbang_status skipped: ' . $e->getMessage()); }

        // ==================== VIEW BUKU TAMU ====================

        try {
        DB::statement("
            CREATE OR REPLACE VIEW v_buku_tamu_summary AS
            SELECT 
                d.id AS desa_id,
                d.nama_desa,
                COUNT(pk.id) AS total_pengunjung,
                COUNT(CASE WHEN DATE(pk.jam_datang) = CURDATE() THEN 1 END) AS hari_ini,
                pk.tujuan_bidang AS bidang,
                MAX(pk.jam_datang) AS kunjungan_terakhir
            FROM desa d
            LEFT JOIN pengunjung_kecamatan pk ON pk.desa_asal_id = d.id
            GROUP BY d.id, d.nama_desa, pk.tujuan_bidang
        ");
        } catch (\Exception $e) { \Log::warning('View v_buku_tamu_summary skipped: ' . $e->getMessage()); }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_ekonomi_desa_summary');
        DB::statement('DROP VIEW IF EXISTS v_ekonomi_kecamatan_summary');
        DB::statement('DROP VIEW IF EXISTS v_ekonomi_kategori_summary');
        DB::statement('DROP VIEW IF EXISTS v_pembangunan_desa_summary');
        DB::statement('DROP VIEW IF EXISTS v_pembangunan_anggaran_tahun');
        DB::statement('DROP VIEW IF EXISTS v_pembangunan_spj_status');
        DB::statement('DROP VIEW IF EXISTS v_ekbang_submission_summary');
        DB::statement('DROP VIEW IF EXISTS v_ekbang_aspek_summary');
        DB::statement('DROP VIEW IF EXISTS v_musrenbang_status');
        DB::statement('DROP VIEW IF EXISTS v_buku_tamu_summary');
    }
};
