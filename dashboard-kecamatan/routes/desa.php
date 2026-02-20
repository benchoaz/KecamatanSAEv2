<?php

use App\Http\Controllers\Desa\DashboardController;
use App\Http\Controllers\Desa\PembangunanController;
use App\Http\Controllers\Desa\BltController;
use App\Http\Controllers\Desa\PemerintahanController;
use App\Http\Controllers\Desa\SubmissionController;
use App\Http\Controllers\Desa\TrantibumController;
use App\Http\Controllers\Desa\TrantibumRelawanController;
use App\Http\Controllers\Desa\ProfileController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:Operator Desa,Super Admin'])->prefix('desa')->name('desa.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // User Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // === MODUL ADMINISTRASI DESA (NEW) ===
    Route::prefix('administrasi')->name('administrasi.')->group(function () {
        Route::get('/', [App\Http\Controllers\Desa\AdministrasiController::class, 'index'])->name('index');

        // Personil (Perangkat & BPD)
        Route::get('/personil', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilIndex'])->name('personil.index');
        Route::get('/personil/create', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilCreate'])->name('personil.create');
        Route::post('/personil', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilStore'])->name('personil.store');
        Route::get('/personil/{id}/edit', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilEdit'])->name('personil.edit');
        Route::put('/personil/{id}', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilUpdate'])->name('personil.update');
        Route::delete('/personil/{id}', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilDestroy'])->name('personil.destroy');
        Route::post('/personil/{id}/submit', [App\Http\Controllers\Desa\AdministrasiController::class, 'personilSubmit'])->name('personil.submit');

        // Lembaga Desa
        Route::get('/lembaga', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaIndex'])->name('lembaga.index');
        Route::get('/lembaga/create', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaCreate'])->name('lembaga.create');
        Route::post('/lembaga', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaStore'])->name('lembaga.store');
        Route::get('/lembaga/{id}/edit', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaEdit'])->name('lembaga.edit');
        Route::put('/lembaga/{id}', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaUpdate'])->name('lembaga.update');
        Route::delete('/lembaga/{id}', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaDestroy'])->name('lembaga.destroy');
        Route::post('/lembaga/{id}/submit', [App\Http\Controllers\Desa\AdministrasiController::class, 'lembagaSubmit'])->name('lembaga.submit');

        // Dokumen Desa (Perdes & Laporan)
        Route::get('/dokumen', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenIndex'])->name('dokumen.index');
        Route::get('/dokumen/create', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenCreate'])->name('dokumen.create');
        Route::post('/dokumen', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenStore'])->name('dokumen.store');
        Route::get('/dokumen/{id}/edit', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenEdit'])->name('dokumen.edit');
        Route::put('/dokumen/{id}', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenUpdate'])->name('dokumen.update');
        Route::delete('/dokumen/{id}', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenDestroy'])->name('dokumen.destroy');
        Route::post('/dokumen/{id}/submit', [App\Http\Controllers\Desa\AdministrasiController::class, 'dokumenSubmit'])->name('dokumen.submit');

        // Secure File Routes
        Route::get('/file/personil/{id}', [App\Http\Controllers\Desa\FileController::class, 'personil'])->name('file.personil');
        Route::get('/file/lembaga/{id}', [App\Http\Controllers\Desa\FileController::class, 'lembaga'])->name('file.lembaga');
        Route::get('/file/dokumen/{id}', [App\Http\Controllers\Desa\FileController::class, 'dokumen'])->name('file.dokumen');
    });

    // Web Submission Routes
    // Web Submission Routes (Unified for all modules)
    Route::prefix('submissions')->name('submissions.')->group(function () {
        Route::get('/', [SubmissionController::class, 'index'])->name('index');

        // Modules specific listings
        Route::get('/module/{module_slug}', [SubmissionController::class, 'moduleIndex'])->name('module');

        // CRUD Flow
        Route::get('/create/{module_slug}', [SubmissionController::class, 'create'])->name('create');
        Route::post('/{module_slug}', [SubmissionController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [SubmissionController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SubmissionController::class, 'update'])->name('update');
        Route::get('/{id}', [SubmissionController::class, 'show'])->name('show');

        // Workflow Actions
        Route::post('/{id}/submit', [SubmissionController::class, 'submit'])->name('submit');
        Route::post('/{id}/file', [SubmissionController::class, 'uploadFile'])->name('file.upload');
        Route::delete('/file/{file_id}', [SubmissionController::class, 'deleteFile'])->name('file.delete');

        // Ajax Helper
        Route::get('/helper/aspek/{menuId}', [SubmissionController::class, 'getAspek']);
        Route::get('/helper/indikator/{aspekId}', [SubmissionController::class, 'getIndikator']);
    });

    // Modul Khusus: Musyawarah Desa (Musdes)
    Route::prefix('musdes')->name('musdes.')->group(function () {
        Route::get('/', [App\Http\Controllers\Desa\MusdesController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Desa\MusdesController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Desa\MusdesController::class, 'store'])->name('store');

        Route::get('/{id}/edit', [App\Http\Controllers\Desa\MusdesController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Desa\MusdesController::class, 'update'])->name('update');
        Route::get('/{id}', [App\Http\Controllers\Desa\MusdesController::class, 'show'])->name('show');
        Route::delete('/{id}', [App\Http\Controllers\Desa\MusdesController::class, 'destroy'])->name('destroy');

        // Special Actions
        Route::post('/{id}/submit', [App\Http\Controllers\Desa\MusdesController::class, 'submit'])->name('submit');
        Route::post('/{id}/upload', [App\Http\Controllers\Desa\MusdesController::class, 'uploadFile'])->name('upload');
        Route::delete('/{id}/file/{fileId}', [App\Http\Controllers\Desa\MusdesController::class, 'deleteFile'])->name('deleteFile');
        Route::get('/templates/{type}', [App\Http\Controllers\Desa\MusdesController::class, 'downloadTemplate'])->name('template.download');
    });

    // Modul Pembangunan (Simplified & Faktual)
    Route::middleware(['menu.toggle:ekbang'])->prefix('pembangunan')->name('pembangunan.')->group(function () {
        Route::get('/', [PembangunanController::class, 'modernIndex'])->name('index');
        Route::get('/create', [PembangunanController::class, 'modernCreate'])->name('create_new');

        // Pembangunan Fisik (Legacy Compatibility)
        Route::get('/fisik', [PembangunanController::class, 'fisikIndex'])->name('fisik.index');
        Route::get('/fisik/create', [PembangunanController::class, 'fisikCreate'])->name('fisik.create');
        Route::post('/fisik', [PembangunanController::class, 'fisikStore'])->name('fisik.store');

        // Kegiatan Non-Fisik
        Route::get('/non-fisik', [PembangunanController::class, 'nonFisikIndex'])->name('non-fisik.index');
        Route::get('/non-fisik/create', [PembangunanController::class, 'nonFisikCreate'])->name('non-fisik.create');
        Route::post('/non-fisik', [PembangunanController::class, 'nonFisikStore'])->name('non-fisik.store');

        // Shared Actions
        // Pagu Anggaran (Anggaran Awal)
        Route::get('/pagu', [App\Http\Controllers\Desa\PaguAnggaranController::class, 'index'])->name('pagu.index');
        Route::post('/pagu', [App\Http\Controllers\Desa\PaguAnggaranController::class, 'store'])->name('pagu.store');

        // Bantuan Administrasi Kegiatan (Optional)
        Route::get('/administrasi/{id?}', [App\Http\Controllers\Desa\PembangunanController::class, 'administrasiIndex'])->name('administrasi.index');

        Route::get('/{id}/edit', [PembangunanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PembangunanController::class, 'update'])->name('update');
        Route::get('/{id}', [PembangunanController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [PembangunanController::class, 'submit'])->name('submit');
    });

    // Modul BLT Desa
    Route::middleware(['menu.toggle:ekbang'])->prefix('blt')->name('blt.')->group(function () {
        Route::get('/', [BltController::class, 'index'])->name('index');
        Route::get('/create', [BltController::class, 'create'])->name('create');
        Route::post('/', [BltController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BltController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BltController::class, 'update'])->name('update');
        Route::get('/{id}', [BltController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [BltController::class, 'submit'])->name('submit');
    });

    // Pemerintahan (Input Side)
    Route::prefix('pemerintahan')->name('pemerintahan.')->group(function () {
        Route::get('/', [PemerintahanController::class, 'index'])->name('index');
        Route::get('/export-audit', [PemerintahanController::class, 'exportAudit'])->name('export');

        Route::prefix('detail')->name('detail.')->group(function () {
            Route::get('/personil', [PemerintahanController::class, 'personilIndex'])->name('personil.index');
            Route::get('/bpd', [PemerintahanController::class, 'bpdIndex'])->name('bpd.index');
            Route::get('/lembaga', [PemerintahanController::class, 'lembagaIndex'])->name('lembaga.index');
            Route::get('/perencanaan', [\App\Http\Controllers\Desa\PerencanaanController::class, 'index'])->name('perencanaan.index');
            Route::get('/perencanaan/create', [\App\Http\Controllers\Desa\PerencanaanController::class, 'create'])->name('perencanaan.create');
            Route::post('/perencanaan', [\App\Http\Controllers\Desa\PerencanaanController::class, 'store'])->name('perencanaan.store');
            Route::get('/perencanaan/{id}', [\App\Http\Controllers\Desa\PerencanaanController::class, 'show'])->name('perencanaan.show');
            Route::post('/perencanaan/{id}/submit', [\App\Http\Controllers\Desa\PerencanaanController::class, 'submit'])->name('perencanaan.submit');
            Route::get('/laporan', [PemerintahanController::class, 'laporanIndex'])->name('laporan.index');
            Route::get('/inventaris', [PemerintahanController::class, 'inventarisIndex'])->name('inventaris.index');
            Route::get('/dokumen', [PemerintahanController::class, 'dokumenIndex'])->name('dokumen.index');

            Route::post('/personil', [PemerintahanController::class, 'personilStore'])->name('personil.store');
            Route::post('/inventaris', [PemerintahanController::class, 'inventarisStore'])->name('inventaris.store');
            Route::post('/lembaga', [PemerintahanController::class, 'lembagaStore'])->name('lembaga.store');
            Route::post('/dokumen', [PemerintahanController::class, 'dokumenStore'])->name('dokumen.store');
        });
    });

    // Kesra (Input Side)
    Route::prefix('kesra')->name('kesra.')->group(function () {
        Route::get('/', [App\Http\Controllers\Desa\KesraController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Desa\KesraController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Desa\KesraController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [App\Http\Controllers\Desa\KesraController::class, 'edit'])->name('edit');
        Route::put('/{id}', [App\Http\Controllers\Desa\KesraController::class, 'update'])->name('update');
        Route::get('/{id}', [App\Http\Controllers\Desa\KesraController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [App\Http\Controllers\Desa\KesraController::class, 'submit'])->name('submit');
    });

    // Trantibum (Input Side - UPDATED with specific Kejadian reports)
    Route::prefix('trantibum')->name('trantibum.')->group(function () {
        Route::get('/', [TrantibumController::class, 'index'])->name('index');

        // Specific Kejadian/Bencana Reporting (Phase 1)
        Route::prefix('kejadian')->name('kejadian.')->group(function () {
            Route::get('/', [App\Http\Controllers\Desa\TrantibumKejadianController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\Desa\TrantibumKejadianController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\Desa\TrantibumKejadianController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Desa\TrantibumKejadianController::class, 'show'])->name('show');
            Route::delete('/{id}', [App\Http\Controllers\Desa\TrantibumKejadianController::class, 'destroy'])->name('destroy');
        });

        // Relawan Tangguh Bencana
        Route::resource('relawan', TrantibumRelawanController::class);

        Route::get('/create', [TrantibumController::class, 'create'])->name('create');
        Route::post('/', [TrantibumController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [TrantibumController::class, 'edit'])->name('edit');
        Route::put('/{id}', [TrantibumController::class, 'update'])->name('update');
        Route::get('/{id}', [TrantibumController::class, 'show'])->name('show');
        Route::post('/{id}/submit', [TrantibumController::class, 'submit'])->name('submit');
    });

    // Modul Pembangunan Logbooks
    Route::middleware(['menu.toggle:ekbang'])->prefix('pembangunan-logbook')->name('pembangunan.logbook.')->group(function () {
        Route::get('/', [App\Http\Controllers\Desa\PembangunanLogbookController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Desa\PembangunanLogbookController::class, 'store'])->name('store');
        Route::delete('/{id}', [App\Http\Controllers\Desa\PembangunanLogbookController::class, 'destroy'])->name('destroy');
    });
});
