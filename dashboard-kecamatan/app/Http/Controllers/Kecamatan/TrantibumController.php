<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\TrantibumKejadian;
use App\Models\TrantibumRelawan;
use Illuminate\Http\Request;

class TrantibumController extends Controller
{
    public function index()
    {
        // Dashboard Stats
        $stats = [
            'total_kejadian' => TrantibumKejadian::count(),
            'kejadian_bulan_ini' => TrantibumKejadian::whereMonth('waktu_kejadian', now()->month)->count(),
            'total_relawan' => TrantibumRelawan::where('status_aktif', true)->count(),
            'desa_terdampak' => TrantibumKejadian::distinct('desa_id')->count('desa_id'),
        ];

        // Recent Incidents
        $recent_incidents = TrantibumKejadian::with('desa')
            ->latest('waktu_kejadian')
            ->take(5)
            ->get();

        // Incidents by Category Chart Data
        $categories = TrantibumKejadian::selectRaw('kategori, count(*) as count')
            ->groupBy('kategori')
            ->pluck('count', 'kategori');

        return view('kecamatan.trantibum.index', compact('stats', 'recent_incidents', 'categories'));
    }

    public function kejadian(Request $request)
    {
        $query = TrantibumKejadian::with('desa')->latest('waktu_kejadian');

        if ($request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->desa_id) {
            $query->where('desa_id', $request->desa_id);
        }

        $kejadians = $query->paginate(10);

        return view('kecamatan.trantibum.kejadian', compact('kejadians'));
    }

    public function relawan(Request $request)
    {
        $query = TrantibumRelawan::with('desa')->where('status_aktif', true);

        if ($request->desa_id) {
            $query->where('desa_id', $request->desa_id);
        }

        $relawans = $query->latest()->paginate(12);

        return view('kecamatan.trantibum.relawan', compact('relawans'));
    }
}
