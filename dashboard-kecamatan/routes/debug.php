<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/check-auth', function () {
    $user = Auth::user();
    return response()->json([
        'id' => $user->id ?? 'null',
        'name' => $user->nama_lengkap ?? 'null',
        'role_manual' => $user->role->nama_role ?? 'No Role Logic',
        'desa_id' => $user->desa_id ?? 'null',
        'is_operator_desa_check' => ($user && $user->desa_id) ? 'YES' : 'NO',
    ]);
});
