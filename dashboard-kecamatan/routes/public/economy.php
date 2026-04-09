<?php

use App\Http\Controllers\EconomyController;
use App\Http\Controllers\UmkmRakyatController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Economy Routes (UMKM, Jasa, Loker)
|--------------------------------------------------------------------------
|
| Route untuk halaman ekonomi - UMKM Rakyat, Lowongan Kerja, dan Jasa
|
*/

// Economy Hub (Main)
Route::get('/ekonomi', [EconomyController::class, 'index'])
    ->name('economy.index');

// Pendaftaran - HARUS di sebelum route /ekonomi/{id}
Route::get('/ekonomi/daftar', [EconomyController::class, 'create'])
    ->name('economy.create');
Route::post('/ekonomi/daftar', [EconomyController::class, 'store'])
    ->name('economy.store');

// Kelola Jasa Mandiri (Akses menggunakan Super Dasbor Warga)
Route::get('/ekonomi/login', function() {
    return redirect()->route('portal_warga.login');
})->name('economy.login');
Route::post('/ekonomi/login', [EconomyController::class, 'authenticate'])
    ->name('economy.authenticate');
Route::get('/ekonomi/manage/{id}', [EconomyController::class, 'manage'])
    ->name('economy.manage');
Route::post('/ekonomi/manage/{id}', [EconomyController::class, 'update'])
    ->name('economy.update');

// Detail Produk UMKM (harus sebelum /{id} agar tidak bentrok)
Route::get('/ekonomi/produk/{id}', [EconomyController::class, 'showProduk'])
    ->name('economy.produk.show');

// Detail Jasa/WorkDirectory
Route::get('/ekonomi/{id}', [EconomyController::class, 'show'])
    ->name('economy.show');

// Redirects for backward compatibility
Route::get('/umkm', function () {
    return redirect()->route('economy.index', ['tab' => 'produk']);
})->name('public.umkm.index');

Route::get('/kerja', function () {
    return redirect()->route('economy.index', ['tab' => 'jasa']);
});

// ==========================================
// UMKM Rakyat (Seller Center)
// ==========================================

Route::prefix('umkm-rakyat')->name('umkm_rakyat.')->group(function () {
    // Publik Katalog
    Route::get('/', [UmkmRakyatController::class, 'index'])->name('index');
    Route::get('/terdekat', [UmkmRakyatController::class, 'nearby'])->name('nearby');
    Route::get('/produk', [UmkmRakyatController::class, 'allProducts'])->name('all_products');
    
    // Pendaftaran & Verifikasi Mandiri
    Route::get('/daftar', [UmkmRakyatController::class, 'create'])->name('create');
    Route::post('/daftar', [UmkmRakyatController::class, 'store'])->name('store');
    Route::get('/{id}/verifikasi', [UmkmRakyatController::class, 'verifyStep'])->name('verify_step');
    Route::post('/{id}/verifikasi', [UmkmRakyatController::class, 'processVerify'])->name('process_verify');

    // Login Owner (Redirect ke Super Dasbor Warga)
    Route::get('/login', function() {
        return redirect()->route('portal_warga.login');
    })->name('login');
    // POST request untuk backward compatibility jika ada yg hardcode (opsional, better die/redirect)
    Route::post('/login/request', function() {
        return redirect()->route('portal_warga.login');
    })->name('send_access');

    // Dasbor Pemilik UMKM (Menggunakan Token)
    Route::prefix('{token}/manage')->name('manage')->group(function () {
        Route::get('/', [UmkmRakyatController::class, 'manage']);
        
        // Produk
        Route::get('/products', [UmkmRakyatController::class, 'manageProducts'])->name('.products');
        Route::post('/products', [UmkmRakyatController::class, 'storeProduct'])->name('.product.store');
        Route::patch('/products/{productId}/toggle', [UmkmRakyatController::class, 'toggleProductAvailability'])->name('.product.toggle');
        Route::delete('/products/{productId}', [UmkmRakyatController::class, 'deleteProduct'])->name('.product.delete');
        
        // Toko/Settings
        Route::get('/settings', [UmkmRakyatController::class, 'manageSettings'])->name('.settings');
        Route::post('/settings', [UmkmRakyatController::class, 'updateSettings'])->name('.settings.update');
    });

    // Profil Toko & Etalase Publik (Tergantung slug)
    Route::get('/toko/{slug}', [UmkmRakyatController::class, 'show'])->name('show');
});
