<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
// use App\Http\Controllers\Public\LayananController;
use App\Http\Controllers\PublicServiceController;
use App\Http\Controllers\EconomyController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root Landing Page
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::get('/test-ping', function() { return 'pong'; });
// Laporan Demografi & Statistik (Separate Pages)
Route::prefix('statistik')->group(function () {
    Route::get('/', [LandingController::class, 'statistik'])->name('landing.statistik.index');
    Route::get('/pendidikan', [LandingController::class, 'statPendidikan'])->name('landing.statistik.pendidikan');
    Route::get('/pekerjaan', [LandingController::class, 'statPekerjaan'])->name('landing.statistik.pekerjaan');
    Route::get('/agama', [LandingController::class, 'statAgama'])->name('landing.statistik.agama');
    Route::get('/kesehatan', [LandingController::class, 'statKesehatan'])->name('landing.statistik.kesehatan');
    Route::get('/kesejahteraan', [LandingController::class, 'statKesejahteraan'])->name('landing.statistik.kesejahteraan');
});
Route::get('/berita', [\App\Http\Controllers\PublicBeritaController::class, 'index'])->name('public.berita.index');
Route::get('/berita/{slug}', [\App\Http\Controllers\PublicBeritaController::class, 'show'])->name('public.berita.show');
// Public Service & Economy Routes
require __DIR__ . '/public/layanan.php';
require __DIR__ . '/public/economy.php';
require __DIR__ . '/public/warga.php';

// Route Aliases for Landing Page compatibility
Route::get('/tracking', [PublicServiceController::class, 'trackingPage'])->name('public.tracking');
Route::post('/service/submit', [PublicServiceController::class, 'submit'])->name('public.service.submit');


// Receipt Routes
Route::get('/receipt/{uuid}/download', [\App\Http\Controllers\ReceiptController::class, 'generateReceipt'])->name('receipt.download');
Route::get('/receipt/{uuid}/preview', [\App\Http\Controllers\ReceiptController::class, 'previewReceipt'])->name('receipt.preview');

// Auth Routes
Route::get('/login', [AuthController::class, 'login'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard & Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $user = auth()->user();

        // Kecamatan Level Roles
        if ($user->hasRole('Super Admin') ||
            $user->hasRole('Operator Kecamatan') ||
            $user->isAdminPelayanan()) {
            return redirect()->route('kecamatan.dashboard');
        }

        // Desa Level Roles
        if ($user->hasRole('Operator Desa') || $user->desa_id) {
            return redirect()->route('desa.dashboard');
        }

        // Fallback for other logged-in users (e.g., Warga)
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke dashboard khusus.');
    })->name('dashboard');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [\App\Http\Controllers\ProfileController::class, 'editPassword'])->name('profile.password.edit');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password.update');
});

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
