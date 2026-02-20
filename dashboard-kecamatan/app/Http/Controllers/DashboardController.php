<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Entry point for dashboard.
     * Redirects to the appropriate domain dashboard.
     */
    public function index()
    {
        if (auth()->user()->desa_id !== null) {
            return redirect()->route('desa.dashboard');
        }
        return redirect()->route('kecamatan.dashboard');
    }

    /**
     * Forwarding stats and chart calls to Kecamatan dashboard if needed,
     * or handle them here if they are shared.
     */
    public function stats()
    {
        $user = auth()->user();
        if ($user->desa_id) {
            // If Desa has specific stats AJAX, call it here. 
            // For now, Desa stats are loaded directly in the index view.
            return response()->json(['status' => 'success', 'domain' => 'desa']);
        }

        return app(\App\Http\Controllers\Kecamatan\DashboardController::class)->stats();
    }

    public function chartData(Request $request)
    {
        $user = auth()->user();
        if ($user->desa_id) {
            return response()->json(['status' => 'success', 'domain' => 'desa', 'data' => []]);
        }

        return app(\App\Http\Controllers\Kecamatan\DashboardController::class)->chartData($request);
    }
}
