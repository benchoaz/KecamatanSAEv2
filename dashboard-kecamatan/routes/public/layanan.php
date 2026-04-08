<?php

use App\Http\Controllers\PublicServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Service Routes (Layanan Publik)
|--------------------------------------------------------------------------
|
| Route untuk halaman layanan publik - Menggunakan controller untuk
| performance yang lebih baik dengan caching
|
*/

// Main Layanan Portal
Route::get('/layanan', [PublicServiceController::class, 'trackingPage'])
    ->name('layanan');
Route::post('/layanan/check', [PublicServiceController::class, 'checkStatus'])
    ->name('public.tracking.check');

// Application Routes - KTP, KK, Akta, dll
Route::get('/layanan/apply/{type}', [App\Http\Controllers\Public\LayananController::class, 'showForm'])
    ->name('apply.form');

// Direct aliases for the chatbot/landing page links
Route::get('/ktp', fn() => redirect()->route('apply.form', 'ktp'))->name('apply.ktp');
Route::get('/kk', fn() => redirect()->route('apply.form', 'kk'))->name('apply.kk');
Route::get('/akta', fn() => redirect()->route('apply.form', 'akta'))->name('apply.akta');
Route::get('/sktm', fn() => redirect()->route('apply.form', 'sktm'))->name('apply.sktm');
Route::get('/domisili', fn() => redirect()->route('apply.form', 'domisili'))->name('apply.domisili');
Route::get('/nikah', fn() => redirect()->route('apply.form', 'nikah'))->name('apply.nikah');
Route::get('/bpjs', fn() => redirect()->route('apply.form', 'bpjs'))->name('apply.bpjs');

Route::post('/layanan/apply', [App\Http\Controllers\Public\LayananController::class, 'store'])
    ->name('apply.store');
