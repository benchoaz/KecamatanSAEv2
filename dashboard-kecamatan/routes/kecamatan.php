<?php

use App\Http\Controllers\Kecamatan\DashboardController;
use App\Http\Controllers\Kecamatan\EkbangController;
use App\Http\Controllers\Kecamatan\KesraController;
use App\Http\Controllers\Kecamatan\PemerintahanController;
use App\Http\Controllers\Kecamatan\TrantibumController;
use App\Http\Controllers\Kecamatan\VerifikasiController;
use App\Http\Controllers\Kecamatan\LaporanController;
use App\Http\Controllers\Kecamatan\UserManagementController;
use App\Http\Controllers\Kecamatan\PembangunanController;
use App\Http\Controllers\Kecamatan\ReferenceDataController;
use App\Http\Controllers\Master\DesaMasterController;
use App\Http\Controllers\ApplicationProfileController;
use App\Http\Controllers\Kecamatan\WahaN8nController;
use App\Http\Controllers\Kecamatan\PelayananController;
use App\Http\Controllers\Kecamatan\AnnouncementController;
use App\Http\Controllers\Kecamatan\LayananPublikController;
use App\Http\Controllers\Kecamatan\BeritaController;
use App\Http\Controllers\Pemerintahan\AparaturController; // Keep for now or move
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Operator Kecamatan,Super Admin,pelayanan_admin,Admin Pelayanan'])->prefix('kecamatan')->name('kecamatan.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // System Settings - Allow all authenticated users in this group
    Route::get('/settings/profile', [ApplicationProfileController::class, 'index'])->name('settings.profile');
    Route::put('/settings/profile', [ApplicationProfileController::class, 'update'])->name('settings.profile.update');

    // Standardized Pelayanan Module
    Route::prefix('pelayanan')->name('pelayanan.')->group(function () {
        Route::get('/inbox', [PelayananController::class, 'inbox'])->name('inbox');
        Route::get('/pengaduan', [PelayananController::class, 'pengaduanIndex'])->name('pengaduan');
        Route::get('/pengaduan/{id}', [PelayananController::class, 'pengaduanShow'])->name('pengaduan.show');
        Route::put('/pengaduan/{id}', [PelayananController::class, 'pengaduanUpdateStatus'])->name('pengaduan.update-status');
        Route::put('/pengaduan/{id}/sender', [PelayananController::class, 'pengaduanUpdateSender'])->name('pengaduan.update-sender');
        Route::get('/statistics', [PelayananController::class, 'statistics'])->name('statistics');

        Route::prefix('visitor')->name('visitor.')->group(function () {
            Route::get('/', [PelayananController::class, 'visitorIndex'])->name('index');
            Route::post('/', [PelayananController::class, 'visitorStore'])->name('store');
            Route::put('/{id}', [PelayananController::class, 'visitorUpdate'])->name('update');
        });

        Route::prefix('faq')->name('faq.')->group(function () {
            Route::get('/', [PelayananController::class, 'faqIndex'])->name('index');
            Route::post('/', [PelayananController::class, 'faqStore'])->name('store');
            Route::put('/{id}', [PelayananController::class, 'faqUpdate'])->name('update');
        });

        Route::prefix('layanan')->name('layanan.')->group(function () {
            Route::get('/', [PelayananController::class, 'layananIndex'])->name('index');
            Route::get('/create', [PelayananController::class, 'layananCreate'])->name('create');
            Route::post('/', [PelayananController::class, 'layananStore'])->name('store');
            Route::get('/{id}/edit', [PelayananController::class, 'layananEdit'])->name('edit');
            Route::put('/{id}', [PelayananController::class, 'layananUpdate'])->name('update');
            Route::delete('/{id}', [PelayananController::class, 'layananDestroy'])->name('destroy');
        });

        // Pelayanan Detail & Status Update: Catch-all ID routes moved to end 
        // to avoid hijacking static prefixes like /visitor, /faq, /layanan
        Route::get('/{id}', [PelayananController::class, 'show'])->name('show');
        Route::put('/{id}', [PelayananController::class, 'updateStatus'])->name('update-status');
    });

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // UMKM & Jasa (Layanan Publik)
    Route::prefix('umkm')->name('umkm.')->group(function () {
        Route::get('/', [LayananPublikController::class, 'umkmIndex'])->name('index');
        Route::get('/create', [LayananPublikController::class, 'umkmCreate'])->name('create');
        Route::post('/', [LayananPublikController::class, 'umkmStore'])->name('store');
        Route::get('/{id}/handover', [LayananPublikController::class, 'umkmHandover'])->name('handover');
        Route::get('/{id}/edit', [LayananPublikController::class, 'umkmEdit'])->name('edit');
        Route::put('/{id}', [LayananPublikController::class, 'umkmUpdate'])->name('update');
        Route::delete('/{id}', [LayananPublikController::class, 'umkmDestroy'])->name('destroy');
        Route::post('/{id}/reset-akses', [LayananPublikController::class, 'resetAkses'])->name('reset-akses');
        Route::post('/{id}/toggle-verify', [LayananPublikController::class, 'umkmToggleVerify'])->name('toggle-verify');
    });

    // Jasa Management (Parallel to UMKM)
    Route::prefix('jasa')->name('jasa.')->group(function () {
        Route::get('/create', [LayananPublikController::class, 'jasaCreate'])->name('create');
        Route::post('/', [LayananPublikController::class, 'jasaStore'])->name('store');
        Route::get('/{id}/handover', [LayananPublikController::class, 'jasaHandover'])->name('handover');
        Route::get('/{id}/edit', [LayananPublikController::class, 'jasaEdit'])->name('edit');
        Route::put('/{id}', [LayananPublikController::class, 'jasaUpdate'])->name('update');
        Route::delete('/{id}', [LayananPublikController::class, 'jasaDestroy'])->name('destroy');
        Route::post('/{id}/reset-akses', [LayananPublikController::class, 'jasaResetAkses'])->name('reset-akses');
        Route::post('/{id}/toggle-verify', [LayananPublikController::class, 'jasaToggleVerify'])->name('toggle-verify');
    });



    // Restrict other routes to Super Admin & Operator only
    Route::middleware(['role:Operator Kecamatan,Super Admin'])->group(function () {
        // Verification & Approval
        Route::prefix('verifikasi')->name('verifikasi.')->group(function () {
            Route::get('/', [VerifikasiController::class, 'index'])->name('index');
            Route::get('/{uuid}', [VerifikasiController::class, 'show'])->name('show');
            Route::post('/{id}/process', [VerifikasiController::class, 'process'])->name('process');
        });

        // Secure File Routes for Kecamatan
        Route::prefix('file')->name('file.')->group(function () {
            Route::get('/personil/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'personil'])->name('personil');
            Route::get('/personil-foto/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'personilFoto'])->name('personil-foto');
            Route::get('/lembaga/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'lembaga'])->name('lembaga');
            Route::get('/dokumen/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'dokumen'])->name('dokumen');
            Route::get('/perencanaan/ba/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'perencanaanBa'])->name('perencanaan-ba');
            Route::get('/perencanaan/absensi/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'perencanaanAbsensi'])->name('perencanaan-absensi');
            Route::get('/perencanaan/foto/{id}', [\App\Http\Controllers\Kecamatan\FileController::class, 'perencanaanFoto'])->name('perencanaan-foto');
        });

        // Pemerintahan (Monitoring Side)
        Route::prefix('pemerintahan')->name('pemerintahan.')->group(function () {
            Route::get('/', [PemerintahanController::class, 'index'])->name('index');
            Route::get('/export-audit', [PemerintahanController::class, 'exportAudit'])->name('export');


            // Administrative Governance Modules (Detailed Monitoring)
            Route::prefix('detail')->name('detail.')->group(function () {
                Route::get('/personil', [PemerintahanController::class, 'personilIndex'])->name('personil.index');
                Route::post('/personil', [PemerintahanController::class, 'personilStore'])->name('personil.store');
                Route::post('/personil/{id}/verify', [PemerintahanController::class, 'personilVerify'])->name('personil.verify');

                Route::get('/bpd', [PemerintahanController::class, 'bpdIndex'])->name('bpd.index');
                Route::post('/bpd', [PemerintahanController::class, 'personilStore'])->name('bpd.store'); // Reuse store for now

                Route::get('/lembaga', [PemerintahanController::class, 'lembagaIndex'])->name('lembaga.index');
                Route::post('/lembaga', [PemerintahanController::class, 'lembagaStore'])->name('lembaga.store');
                Route::post('/lembaga/{id}/verify', [PemerintahanController::class, 'lembagaVerify'])->name('lembaga.verify');
                Route::get('/perencanaan', [PemerintahanController::class, 'perencanaanIndex'])->name('perencanaan.index');
                Route::post('/perencanaan', [PemerintahanController::class, 'perencanaanStore'])->name('perencanaan.store');
                Route::get('/perencanaan/{id}', [PemerintahanController::class, 'perencanaanShow'])->name('perencanaan.show');
                Route::post('/perencanaan/{id}/verify', [PemerintahanController::class, 'perencanaanVerify'])->name('perencanaan.verify');
                Route::get('/laporan', [PemerintahanController::class, 'laporanIndex'])->name('laporan.index');
                Route::post('/laporan/{id}/verify', [PemerintahanController::class, 'laporanVerify'])->name('laporan.verify');
                Route::get('/inventaris', [PemerintahanController::class, 'inventarisIndex'])->name('inventaris.index');
                Route::post('/inventaris', [PemerintahanController::class, 'inventarisStore'])->name('inventaris.store');
                Route::get('/dokumen', [PemerintahanController::class, 'dokumenIndex'])->name('dokumen.index');
                Route::post('/dokumen', [PemerintahanController::class, 'dokumenStore'])->name('dokumen.store');
                Route::get('/peraturan', [PemerintahanController::class, 'peraturanIndex'])->name('peraturan.index');
            });

            // Sub-Modul: Data Kepala Desa & Perangkat
            Route::resource('aparatur', AparaturController::class);
            Route::post('aparatur/{id}/verify', [AparaturController::class, 'verify'])->name('aparatur.verify');
        });

        // System Settings
        Route::get('/settings/profile', [ApplicationProfileController::class, 'index'])->name('settings.profile');
        Route::put('/settings/profile', [ApplicationProfileController::class, 'update'])->name('settings.profile.update');
        Route::get('/settings/features', [ApplicationProfileController::class, 'features'])->name('settings.features');
        Route::post('/settings/features/toggle', [ApplicationProfileController::class, 'toggleFeature'])->name('settings.profile.toggle-feature');

        Route::get('/settings/geospasial', [\App\Http\Controllers\Kecamatan\GeospasialWilayahController::class, 'index'])->name('settings.geospasial');
        Route::post('/settings/geospasial/upload', [\App\Http\Controllers\Kecamatan\GeospasialWilayahController::class, 'upload'])->name('settings.geospasial.upload');

        // API Token Management (Super Admin only)
        Route::prefix('settings/api-tokens')->name('settings.api-tokens.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ApiTokenController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\ApiTokenController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\ApiTokenController::class, 'store'])->name('store');
            Route::get('/{apiToken}', [\App\Http\Controllers\ApiTokenController::class, 'show'])->name('show');
            Route::put('/{apiToken}/revoke', [\App\Http\Controllers\ApiTokenController::class, 'revoke'])->name('revoke');
            Route::delete('/{apiToken}', [\App\Http\Controllers\ApiTokenController::class, 'destroy'])->name('destroy');
        });

        // WAHA & n8n Management - Simplified
        Route::prefix('settings/waha-n8n')->name('settings.waha-n8n.')->group(function () {
            Route::get('/', [WahaN8nController::class, 'index'])->name('index');
            Route::put('/', [WahaN8nController::class, 'update'])->name('update');
        });

        // Ekbang (Monitoring Side)
        Route::middleware(['menu.toggle:ekbang'])->prefix('ekbang')->name('ekbang.')->group(function () {
            Route::get('/', [EkbangController::class, 'index'])->name('index');
            Route::get('/export-audit', [EkbangController::class, 'exportAudit'])->name('export');
            Route::get('/dana-desa', [EkbangController::class, 'danaDesa'])->name('dana-desa.index');
            Route::get('/fisik', [EkbangController::class, 'fisik'])->name('fisik.index');
            Route::get('/realisasi', [EkbangController::class, 'realisasi'])->name('realisasi.index');
            Route::get('/kepatuhan', [EkbangController::class, 'kepatuhan'])->name('kepatuhan.index');
            Route::get('/audit', [EkbangController::class, 'audit'])->name('audit.index');
        });

        // Pembangunan & BLT (Monitoring Side)
        Route::middleware(['menu.toggle:ekbang'])->prefix('pembangunan')->name('pembangunan.')->group(function () {
            Route::get('/', [PembangunanController::class, 'index'])->name('index');
            Route::get('/{id}/detail', [PembangunanController::class, 'show'])->name('show');
            Route::get('/blt', [PembangunanController::class, 'bltIndex'])->name('blt.index');
            Route::post('/{id}/monitoring/{type}', [PembangunanController::class, 'updateMonitoring'])->name('update-monitoring');

            // Reference Data (SSH & SBU)
            Route::prefix('referensi')->name('referensi.')->group(function () {
                Route::prefix('ssh')->name('ssh.')->group(function () {
                    Route::get('/', [ReferenceDataController::class, 'sshIndex'])->name('index');
                    Route::post('/', [ReferenceDataController::class, 'sshStore'])->name('store');
                    Route::put('/{id}', [ReferenceDataController::class, 'sshUpdate'])->name('update');
                    Route::delete('/{id}', [ReferenceDataController::class, 'sshDestroy'])->name('destroy');
                });
                Route::prefix('sbu')->name('sbu.')->group(function () {
                    Route::get('/', [ReferenceDataController::class, 'sbuIndex'])->name('index');
                    Route::post('/', [ReferenceDataController::class, 'sbuStore'])->name('store');
                    Route::put('/{id}', [ReferenceDataController::class, 'sbuUpdate'])->name('update');
                    Route::delete('/{id}', [ReferenceDataController::class, 'sbuDestroy'])->name('destroy');
                });
            });
        });

        // Kesejahteraan Rakyat
        Route::prefix('kesra')->name('kesra.')->group(function () {
            Route::get('/', [KesraController::class, 'index'])->name('index');
            Route::get('/export-audit', [KesraController::class, 'exportAudit'])->name('export');
            Route::get('/bansos', [KesraController::class, 'bansosIndex'])->name('bansos.index');
            Route::get('/pendidikan', [KesraController::class, 'pendidikanIndex'])->name('pendidikan.index');
            Route::get('/kesehatan', [KesraController::class, 'kesehatanIndex'])->name('kesehatan.index');
            Route::get('/sosial-budaya', [KesraController::class, 'sosialBudayaIndex'])->name('sosial-budaya.index');
            Route::get('/rekomendasi', [KesraController::class, 'rekomendasiIndex'])->name('rekomendasi.index');
            Route::post('/process/{id}', [KesraController::class, 'process'])->name('process');
        });

        // Trantibum Module - Isolated with role-based access
        Route::middleware(['module.role:trantibum'])->prefix('trantibum')->name('trantibum.')->group(function () {
            Route::get('/', [TrantibumController::class, 'index'])->name('index');
            Route::get('/kejadian', [TrantibumController::class, 'kejadian'])->name('kejadian');
            Route::get('/relawan', [TrantibumController::class, 'relawan'])->name('relawan');
            Route::get('/tagana', [TrantibumController::class, 'taganaIndex'])->name('tagana.index');
            Route::get('/emergency', [TrantibumController::class, 'emergencyIndex'])->name('emergency.index');
            Route::get('/export-audit', [TrantibumController::class, 'exportAudit'])->name('export');
            Route::get('/{id}', [TrantibumController::class, 'show'])->name('show');
        });

        // Modul Laporan (Monev & Rekap Administratif)
        Route::prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/', [LaporanController::class, 'index'])->name('index');
            Route::get('/ekbang', [LaporanController::class, 'ekbang'])->name('ekbang');
            // Rename or keep consistency with user's structure
            Route::get('/pemerintahan', [LaporanController::class, 'pemerintahan'])->name('pemerintahan');
            Route::get('/kesra', [LaporanController::class, 'kesra'])->name('kesra');
            Route::get('/trantibum', [LaporanController::class, 'trantibum'])->name('trantibum');
            Route::get('/pelayanan', [LaporanController::class, 'pelayanan'])->name('pelayanan');
            Route::get('/export', [LaporanController::class, 'export'])->name('export');
        });

        // Modul User Management
        Route::resource('users', UserManagementController::class);

        // Modul Audit Logs
        Route::prefix('audit-logs')->name('audit-logs.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Kecamatan\AuditLogController::class, 'index'])->name('index');
            Route::get('/export', [\App\Http\Controllers\Kecamatan\AuditLogController::class, 'export'])->name('export');
            Route::get('/stats', [\App\Http\Controllers\Kecamatan\AuditLogController::class, 'stats'])->name('stats');
            Route::get('/{auditLog}', [\App\Http\Controllers\Kecamatan\AuditLogController::class, 'show'])->name('show');
        });

        // Modul Master Data
        Route::prefix('master')->name('master.')->group(function () {
            Route::resource('desa', DesaMasterController::class)->except(['show']);
        });

        // Modul Berita & Informasi (Kecamatan Internal CRUD)
        Route::prefix('berita')->name('berita.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/toggle-status', [\App\Http\Controllers\Kecamatan\BeritaController::class, 'toggleStatus'])->name('toggle-status');

            // Sub-Modul: Banner Iklan
            Route::resource('banners', \App\Http\Controllers\Kecamatan\NewsBannerController::class)->except(['show']);
            Route::patch('banners/{id}/toggle-status', [\App\Http\Controllers\Kecamatan\NewsBannerController::class, 'toggleStatus'])->name('banners.toggle-status');
        });
    });
});
