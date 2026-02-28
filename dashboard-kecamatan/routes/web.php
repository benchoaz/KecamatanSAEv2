<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
// DISABLED: Debug route removed for security - 2026-02-22
// require __DIR__ . '/debug.php';

use App\Http\Controllers\AuthController;

use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Public Landing Page
Route::get('/', [\App\Http\Controllers\LandingController::class, 'index']);
Route::get('/wilayah', [\App\Http\Controllers\LandingController::class, 'wilayah'])->name('landing.wilayah');

// Public Economy Hub (Unidentified UMKM & Jasa)
Route::get('/ekonomi', [\App\Http\Controllers\EconomyController::class, 'index'])->name('economy.index');

// Pendaftaran Pekerjaan & Jasa (HARUS di sebelum route /ekonomi/{id})
Route::get('/ekonomi/daftar', [\App\Http\Controllers\EconomyController::class, 'create'])->name('economy.create');
Route::post('/ekonomi/daftar', [\App\Http\Controllers\EconomyController::class, 'store'])->name('economy.store');

// Detail Pekerjaan & Jasa
Route::get('/ekonomi/{id}', [\App\Http\Controllers\EconomyController::class, 'show'])->name('economy.show');

// Redirects for backward compatibility
Route::get('/umkm', function () {
    return redirect()->route('economy.index', ['tab' => 'produk']);
})->name('public.umkm.index');
Route::get('/kerja', function () {
    return redirect()->route('economy.index', ['tab' => 'jasa']);
});

// Owner Dashboard (UMKM/Jasa/Loker)
Route::prefix('owner')->name('owner.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\OwnerAuthController::class, 'login'])->name('login');
    Route::post('/login', [\App\Http\Controllers\OwnerAuthController::class, 'authenticate'])->name('authenticate');
    Route::get('/logout', [\App\Http\Controllers\OwnerAuthController::class, 'logout'])->name('logout');

    // Forgot PIN
    Route::get('/lupa-pin', [\App\Http\Controllers\OwnerAuthController::class, 'forgotPin'])->name('forgot_pin');
    Route::post('/lupa-pin', [\App\Http\Controllers\OwnerAuthController::class, 'requestPinReset'])->name('request_pin_reset');

    // Protected routes
    Route::middleware(['web'])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\OwnerAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/reset-pin', [\App\Http\Controllers\OwnerAuthController::class, 'resetPin'])->name('reset_pin');
        Route::post('/toggle-umkm', [\App\Http\Controllers\OwnerAuthController::class, 'toggleUmkm'])->name('toggle_umkm');
        Route::post('/toggle-loker', [\App\Http\Controllers\OwnerAuthController::class, 'toggleLoker'])->name('toggle_loker');
    });
});

Route::get('/umkm/{id}', [\App\Http\Controllers\PublicUmkmController::class, 'show'])->name('public.umkm.show');

// UMKM Rakyat (Self-Service)
Route::prefix('umkm-rakyat')->name('umkm_rakyat.')->group(function () {
    Route::get('/', [\App\Http\Controllers\UmkmRakyatController::class, 'index'])->name('index');
    Route::get('/daftar', [\App\Http\Controllers\UmkmRakyatController::class, 'create'])->name('create');
    Route::post('/daftar', [\App\Http\Controllers\UmkmRakyatController::class, 'store'])->name('store');

    // Login
    Route::get('/masuk', [\App\Http\Controllers\UmkmRakyatController::class, 'login'])->name('login');
    Route::post('/masuk', [\App\Http\Controllers\UmkmRakyatController::class, 'sendAccessLink'])->name('login.post');

    Route::get('/verifikasi/{id}', [\App\Http\Controllers\UmkmRakyatController::class, 'verifyStep'])->name('verify_step');
    Route::post('/verifikasi/{id}', [\App\Http\Controllers\UmkmRakyatController::class, 'processVerify'])->name('process_verify');
    Route::get('/produk', [\App\Http\Controllers\UmkmRakyatController::class, 'allProducts'])->name('products');
    Route::get('/terdekat', [\App\Http\Controllers\UmkmRakyatController::class, 'nearby'])->name('nearby');
    Route::get('/etalase/{slug}', [\App\Http\Controllers\UmkmRakyatController::class, 'show'])->name('show');

    // UMKM Dashboard (Seller Center)
    Route::get('/manage/{token}', [\App\Http\Controllers\UmkmRakyatController::class, 'manage'])->name('manage');
    Route::get('/manage/{token}/produk', [\App\Http\Controllers\UmkmRakyatController::class, 'manageProducts'])->name('manage.products');
    Route::get('/manage/{token}/pengaturan', [\App\Http\Controllers\UmkmRakyatController::class, 'manageSettings'])->name('manage.settings');
    Route::post('/manage/{token}/pengaturan', [\App\Http\Controllers\UmkmRakyatController::class, 'updateSettings'])->name('settings.update');

    Route::post('/manage/{token}/produk', [\App\Http\Controllers\UmkmRakyatController::class, 'storeProduct'])->name('product.store');
    Route::delete('/manage/{token}/produk/{productId}', [\App\Http\Controllers\UmkmRakyatController::class, 'deleteProduct'])->name('product.delete');
});

// Public Service Portal
Route::get('/layanan', function (\Illuminate\Http\Request $request) {
    $jenis = $request->query('jenis');
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();

    $publicAnnouncements = \App\Models\Announcement::where('target_type', 'public')
        ->where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->orderBy('priority', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

    return view('layanan', compact(
        'jenis',
        'masterLayanan',
        'publicAnnouncements'
    ));
})->name('layanan');

// Clean Application Routes (for WhatsApp Bot)
Route::get('/ktp', function () {
    $jenis = 'ktp';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.ktp');

Route::get('/kk', function () {
    $jenis = 'kk';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.kk');

Route::get('/akta', function () {
    $jenis = 'akta';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.akta');

Route::get('/sktm', function () {
    $jenis = 'sktm';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.sktm');

Route::get('/domisili', function () {
    $jenis = 'domisili';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.domisili');

Route::get('/nikah', function () {
    $jenis = 'nikah';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.nikah');

Route::get('/bpjs', function () {
    $jenis = 'bpjs';
    $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->orderBy('urutan')->get();
    return view('layanan', compact('jenis', 'masterLayanan'));
})->name('apply.bpjs');

use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\ApplicationProfileController;
use App\Http\Controllers\Kecamatan\DesaMasterController; // Added for DesaMasterController
use App\Http\Controllers\Kecamatan\LayananPublikController;

Route::post('/public-service/submit', [PublicServiceController::class, 'submit'])->name('public.service.submit');
Route::get('/api/faq-search', [PublicServiceController::class, 'faqSearch'])->name('api.faq.search');
Route::get('/lacak-berkas', [PublicServiceController::class, 'trackingPage'])->name('public.tracking');
Route::post('/lacak-berkas/cek', [PublicServiceController::class, 'checkStatus'])->name('public.tracking.check');

// Receipt & QR Code Routes
use App\Http\Controllers\ReceiptController;
Route::get('/struk/{uuid}', [ReceiptController::class, 'generateReceipt'])->name('receipt.download');
Route::get('/struk/{uuid}/preview', [ReceiptController::class, 'previewReceipt'])->name('receipt.preview');
Route::get('/qr/{uuid}', [ReceiptController::class, 'generateQrCode'])->name('qr.generate');

// SEO Routes (Sitemap & Robots)
use App\Http\Controllers\SitemapController;
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

// Public Berita Routes (Read-Only)
Route::prefix('berita')->name('public.berita.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PublicBeritaController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\PublicBeritaController::class, 'show'])->name('show');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout']); // Fallback for GET logout errors

// Shared Dashboard Redirector
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Generic Auth-Required Routes
Route::middleware(['auth'])->group(function () {
    // Generic Auth-Required Routes
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');
    Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');

    // Secure File Route (Protected by Auth, but FileController should handle specific permissions)
    Route::get('/files/{uuid}/{filename}', [FileController::class, 'show'])->name('files.show');

    // Kecamatan Domain Routes
    Route::middleware(['role:Operator Kecamatan,Super Admin'])->group(function () {
        // System Settings
        Route::get('/kecamatan/settings/profile', [ApplicationProfileController::class, 'index'])->name('kecamatan.settings.profile');
        Route::put('/kecamatan/settings/profile', [ApplicationProfileController::class, 'update'])->name('kecamatan.settings.profile.update');

        // Pelayanan Domain
        Route::prefix('kecamatan/pelayanan')->name('kecamatan.pelayanan.')->group(function () {
            Route::get('/inbox', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'inbox'])->name('inbox');
            Route::get('/inbox/{id}', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'show'])->name('show');
            Route::put('/inbox/{id}/status', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'updateStatus'])->name('update-status');

            // Pengaduan WhatsApp - Dedicated Menu
            Route::get('/pengaduan', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'pengaduanIndex'])->name('pengaduan');
            Route::get('/pengaduan/{id}', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'pengaduanShow'])->name('pengaduan.show');
            Route::put('/pengaduan/{id}/status', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'pengaduanUpdateStatus'])->name('pengaduan.update-status');

            Route::get('/faq', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'faqIndex'])->name('faq.index');
            Route::post('/faq', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'faqStore'])->name('faq.store');
            Route::put('/faq/{id}', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'faqUpdate'])->name('faq.update');

            Route::get('/statistics', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'statistics'])->name('statistics');

            // Buku Tamu (Moved from Pemerintahan)
            Route::get('/visitor', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'visitorIndex'])->name('visitor.index');
            Route::post('/visitor', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'visitorStore'])->name('visitor.store');
            Route::patch('/visitor/{id}', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'visitorUpdate'])->name('visitor.update');

            // Master Layanan (Self Service)
            Route::get('/layanan', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'layananIndex'])->name('layanan.index');
            Route::get('/layanan/create', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'layananCreate'])->name('layanan.create');
            Route::post('/layanan', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'layananStore'])->name('layanan.store');
            Route::get('/layanan/{id}/edit', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'layananEdit'])->name('layanan.edit');
            Route::put('/layanan/{id}', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'layananUpdate'])->name('layanan.update');
            Route::delete('/layanan/{id}', [\App\Http\Controllers\Kecamatan\PelayananController::class, 'layananDestroy'])->name('layanan.destroy');
        });

        // Pengumuman Domain
        Route::prefix('kecamatan/announcements')->name('kecamatan.announcements.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Kecamatan\AnnouncementController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Kecamatan\AnnouncementController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Kecamatan\AnnouncementController::class, 'store'])->name('store');
            Route::get('/{announcement}/edit', [\App\Http\Controllers\Kecamatan\AnnouncementController::class, 'edit'])->name('edit');
            Route::put('/{announcement}', [\App\Http\Controllers\Kecamatan\AnnouncementController::class, 'update'])->name('update');
            Route::delete('/{announcement}', [\App\Http\Controllers\Kecamatan\AnnouncementController::class, 'destroy'])->name('destroy');
        });

        // Layanan Publik (UMKM & Loker) - Module Isolated with role-based access
        Route::prefix('kecamatan/layanan')->name('kecamatan.')->group(function () {
            // UMKM Module - Isolated
            Route::middleware(['module.role:umkm'])->prefix('umkm')->name('umkm.')->group(function () {
                Route::get('/', [LayananPublikController::class, 'umkmIndex'])->name('index');
                Route::get('/create', [LayananPublikController::class, 'umkmCreate'])->name('create');
                Route::post('/', [LayananPublikController::class, 'umkmStore'])->name('store');
                Route::get('/{id}/edit', [LayananPublikController::class, 'umkmEdit'])->name('edit');
                Route::get('/{id}/handover', [LayananPublikController::class, 'umkmHandover'])->name('handover');
                Route::post('/{id}/reset-akses', [LayananPublikController::class, 'resetAkses'])->name('reset_akses');
                Route::put('/{id}', [LayananPublikController::class, 'umkmUpdate'])->name('update');
                Route::delete('/{id}', [LayananPublikController::class, 'umkmDestroy'])->name('destroy');
            });

            // Loker Module - Isolated
            Route::middleware(['module.role:loker'])->prefix('loker')->name('loker.')->group(function () {
                Route::get('/', [LayananPublikController::class, 'lokerIndex'])->name('index');
                Route::get('/create', [LayananPublikController::class, 'lokerCreate'])->name('create');
                Route::post('/', [LayananPublikController::class, 'lokerStore'])->name('store');
                Route::get('/{id}/edit', [LayananPublikController::class, 'lokerEdit'])->name('edit');
                Route::put('/{id}', [LayananPublikController::class, 'lokerUpdate'])->name('update');
                Route::delete('/{id}', [LayananPublikController::class, 'lokerDestroy'])->name('destroy');
            });
        });
    });
});

// Public Loker (Direktori Kerja Warga)
Route::prefix('loker')->name('public.loker.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PublicLokerController::class, 'index'])->name('index');
    Route::get('/pasang', [\App\Http\Controllers\PublicLokerController::class, 'create'])->name('create');
    Route::post('/pasang', [\App\Http\Controllers\PublicLokerController::class, 'store'])->name('store');
});
