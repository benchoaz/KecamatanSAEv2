<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Indeks untuk optimasi query Ekonomi & Pembangunan
     * Berdasarkan analisis struktur database
     */
    public function up(): void
    {
        try {
            // ==================== INDEKS EKONOMI ====================

            // Tabel umkm
            if (Schema::hasTable('umkm') && Schema::hasColumn('umkm', 'desa_id') && Schema::hasColumn('umkm', 'status')) {
                Schema::table('umkm', function (Blueprint $table) {
                    $table->index(['desa_id', 'status'], 'idx_umkm_desa_status');
                    $table->index(['jenis_usaha', 'status'], 'idx_umkm_jenis_status');
                    $table->index('created_at', 'idx_umkm_created_at');
                });
            }

            // Tabel umkm_local
            if (Schema::hasTable('umkm_local') && Schema::hasColumn('umkm_local', 'desa_id') && Schema::hasColumn('umkm_local', 'status')) {
                Schema::table('umkm_local', function (Blueprint $table) {
                    $table->index(['desa_id', 'status'], 'idx_umkm_local_desa_status');
                    $table->index('kategori', 'idx_umkm_local_kategori');
                });
            }

            // Tabel work_directory
            if (Schema::hasTable('work_directory') && Schema::hasColumn('work_directory', 'job_category')) {
                Schema::table('work_directory', function (Blueprint $table) {
                    $table->index(['job_category', 'status'], 'idx_workdir_category_status');
                    $table->index('service_area', 'idx_workdir_service_area');
                    $table->index('price', 'idx_workdir_price');
                });
            }

            // Tabel loker
            if (Schema::hasTable('loker') && Schema::hasColumn('loker', 'desa_id')) {
                Schema::table('loker', function (Blueprint $table) {
                    $table->index(['desa_id', 'status'], 'idx_loker_desa_status');
                    $table->index('tanggal_tutup', 'idx_loker_tgl_tutup');
                    $table->index(['jenis', 'status'], 'idx_loker_jenis_status');
                });
            }

            // Tabel job_vacancy
            if (Schema::hasTable('job_vacancy') && Schema::hasColumn('job_vacancy', 'status')) {
                Schema::table('job_vacancy', function (Blueprint $table) {
                    $table->index(['status', 'created_at'], 'idx_jobvacancy_status_created');
                });
            }

            // ==================== INDEKS PEMBANGUNAN ====================

            // Tabel pembangunan_desa
            if (Schema::hasTable('pembangunan_desa') && Schema::hasColumn('pembangunan_desa', 'desa_id')) {
                Schema::table('pembangunan_desa', function (Blueprint $table) {
                    $table->index(['desa_id', 'tahun_anggaran'], 'idx_pembangunan_desa_tahun');
                    $table->index(['status', 'progress_fisik'], 'idx_pembangunan_status_progress');
                    $table->index(['tanggal_mulai', 'tanggal_selesai'], 'idx_pembangunan_tgl_range');
                    $table->index('progress_keuangan', 'idx_pembangunan_progress_keu');
                });
            }

            // Tabel pembangunan_dokumen_spj
            if (Schema::hasTable('pembangunan_dokumen_spj') && Schema::hasColumn('pembangunan_dokumen_spj', 'pembangunan_desa_id')) {
                Schema::table('pembangunan_dokumen_spj', function (Blueprint $table) {
                    $table->index(['pembangunan_desa_id', 'status'], 'idx_spj_desa_status');
                    $table->index('status', 'idx_spj_status');
                });
            }

            // Tabel pembangunan_logbook
            if (Schema::hasTable('pembangunan_logbook') && Schema::hasColumn('pembangunan_logbook', 'pembangunan_desa_id')) {
                Schema::table('pembangunan_logbook', function (Blueprint $table) {
                    $table->index(['pembangunan_desa_id', 'tanggal'], 'idx_logbook_desa_tgl');
                });
            }

            // ==================== INDEKS MASTER DATA ====================

            // Tabel master_kegiatans
            if (Schema::hasTable('master_kegiatans') && Schema::hasColumn('master_kegiatans', 'tahun')) {
                Schema::table('master_kegiatans', function (Blueprint $table) {
                    $table->index(['tahun', 'jenis_kegatan'], 'idx_mkegiatans_tahun_jenis');
                    $table->index('sumber_dana', 'idx_mkegiatans_sumber_dana');
                });
            }

            // Tabel master_komponen_belanja
            if (Schema::hasTable('master_komponen_belanja') && Schema::hasColumn('master_komponen_belanja', 'kategori')) {
                Schema::table('master_komponen_belanja', function (Blueprint $table) {
                    $table->index(['kategori', 'is_active'], 'idx_mkomponen_kategori_active');
                });
            }

            // Tabel master_bidang
            if (Schema::hasTable('master_bidang') && Schema::hasColumn('master_bidang', 'kode_bidang')) {
                Schema::table('master_bidang', function (Blueprint $table) {
                    $table->index('kode_bidang', 'idx_mbidang_kode');
                });
            }

            // Tabel master_sub_bidang
            if (Schema::hasTable('master_sub_bidang') && Schema::hasColumn('master_sub_bidang', 'master_bidang_id')) {
                Schema::table('master_sub_bidang', function (Blueprint $table) {
                    $table->index(['master_bidang_id', 'kode_sub_bidang'], 'idx_msubbidang_master_kode');
                });
            }

            // ==================== INDEKS SUBMISSION (EKBANG) ====================

            // Tabel submissions
            if (Schema::hasTable('submissions') && Schema::hasColumn('submissions', 'desa_id')) {
                Schema::table('submissions', function (Blueprint $table) {
                    $table->index(['desa_id', 'menu_id', 'created_at'], 'idx_submission_desa_menu_tgl');
                    $table->index(['menu_id', 'status'], 'idx_submission_menu_status');
                    $table->index(['aspek_id', 'status'], 'idx_submission_aspek_status');
                });
            }

            // Tabel usulan_musrenbang
            if (Schema::hasTable('usulan_musrenbang') && Schema::hasColumn('usulan_musrenbang', 'desa_id')) {
                Schema::table('usulan_musrenbang', function (Blueprint $table) {
                    $table->index(['desa_id', 'status', 'skala'], 'idx_musrenbang_desa_status_skala');
                    $table->index('tahun', 'idx_musrenbang_tahun');
                });
            }

            // ==================== INDEKS PENGUNJUNG ====================
            if (Schema::hasTable('pengunjung_kecamatan') && Schema::hasColumn('pengunjung_kecamatan', 'desa_asal_id')) {
                Schema::table('pengunjung_kecamatan', function (Blueprint $table) {
                    $table->index(['desa_asal_id', 'status'], 'idx_pengunjung_desa_status');
                    $table->index(['jam_datang', 'status'], 'idx_pengunjung_jam_status');
                });
            }
        } catch (\Exception $e) {
            // Ignore index errors if column or table not ready
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Hapus indeks (fallback only - tidak semua bisa dihapus)
        Schema::table('umkm', function (Blueprint $table) {
            $table->dropIndex('idx_umkm_desa_status');
            $table->dropIndex('idx_umkm_jenis_status');
            $table->dropIndex('idx_umkm_created_at');
        });

        Schema::table('umkm_local', function (Blueprint $table) {
            $table->dropIndex('idx_umkm_local_desa_status');
            $table->dropIndex('idx_umkm_local_kategori');
        });

        Schema::table('work_directory', function (Blueprint $table) {
            $table->dropIndex('idx_workdir_category_status');
            $table->dropIndex('idx_workdir_service_area');
            $table->dropIndex('idx_workdir_price');
        });

        Schema::table('loker', function (Blueprint $table) {
            $table->dropIndex('idx_loker_desa_status');
            $table->dropIndex('idx_loker_tgl_tutup');
            $table->dropIndex('idx_loker_jenis_status');
        });

        Schema::table('pembangunan_desa', function (Blueprint $table) {
            $table->dropIndex('idx_pembangunan_desa_tahun');
            $table->dropIndex('idx_pembangunan_status_progress');
            $table->dropIndex('idx_pembangunan_tgl_range');
            $table->dropIndex('idx_pembangunan_progress_keu');
        });

        Schema::table('pembangunan_dokumen_spj', function (Blueprint $table) {
            $table->dropIndex('idx_spj_desa_status');
            $table->dropIndex('idx_spj_status');
        });

        Schema::table('pembangunan_logbook', function (Blueprint $table) {
            $table->dropIndex('idx_logbook_desa_tgl');
        });

        Schema::table('master_kegiatans', function (Blueprint $table) {
            $table->dropIndex('idx_mkegiatans_tahun_jenis');
            $table->dropIndex('idx_mkegiatans_sumber_dana');
        });

        Schema::table('master_komponen_belanja', function (Blueprint $table) {
            $table->dropIndex('idx_mkomponen_kategori_active');
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropIndex('idx_submission_desa_menu_tgl');
            $table->dropIndex('idx_submission_menu_status');
            $table->dropIndex('idx_submission_aspek_status');
        });

        Schema::table('usulan_musrenbang', function (Blueprint $table) {
            $table->dropIndex('idx_musrenbang_desa_status_skala');
            $table->dropIndex('idx_musrenbang_tahun');
        });

        Schema::table('pengunjung_kecamatan', function (Blueprint $table) {
            $table->dropIndex('idx_pengunjung_desa_status');
            $table->dropIndex('idx_pengunjung_jam_status');
        });
    }
};
