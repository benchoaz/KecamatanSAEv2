<?php
use Illuminate\Support\Facades\Route;

Route::get('/check-session', function () {
    return [
        'lifetime' => config('session.lifetime'),
        'driver' => config('session.driver'),
        'env_lifetime' => env('SESSION_LIFETIME'),
    ];
});
