<?php

use App\Http\Controllers\WargaPortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Portal Warga Routes
|--------------------------------------------------------------------------
|
| Route untuk Pusat Kendali Warga (Super Dashboard)
|
*/

Route::prefix('portal-warga')->name('portal_warga.')->group(function () {
    Route::get('/masuk', [WargaPortalController::class, 'login'])->name('login');
    Route::post('/masuk/request', [WargaPortalController::class, 'requestAccess'])->name('request');
    Route::get('/verify/{phone}', [WargaPortalController::class, 'verify'])->name('verify');
    Route::get('/dashboard', [WargaPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/keluar', [WargaPortalController::class, 'logout'])->name('logout');
    
    // Operational & Holiday Management
    Route::post('/status-update', [WargaPortalController::class, 'updateOperationalStatus'])->name('status_update');
    Route::post('/update-name', [WargaPortalController::class, 'updateName'])->name('update_name');

    // Bridge for Jasa (Using Session)
    Route::get('/jasa/{id}/bridge', [WargaPortalController::class, 'bridgeJasa'])->name('bridge.jasa');
});
