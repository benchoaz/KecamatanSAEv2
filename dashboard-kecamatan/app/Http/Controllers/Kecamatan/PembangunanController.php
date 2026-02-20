<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\PembangunanDesa;
use Illuminate\Http\Request;

class PembangunanController extends Controller
{
    public function index(Request $request)
    {
        // Stats
        $stats = [
            'total_proyek' => PembangunanDesa::count(),
            'total_anggaran' => PembangunanDesa::sum('pagu_anggaran'),
            'proyek_selesai' => PembangunanDesa::where('status_kegiatan', 'Selesai')->count(),
            'proyek_berjalan' => PembangunanDesa::whereIn('status_kegiatan', ['Sedang Berjalan', 'Persiapan'])->count(),
        ];

        // Project List
        $query = PembangunanDesa::with([
            'desa',
            'logbooks' => function ($q) {
                $q->latest();
            }
        ]);

        if ($request->desa_id) {
            $query->where('desa_id', $request->desa_id);
        }

        if ($request->status) {
            $query->where('status_kegiatan', $request->status);
        }

        $projects = $query->latest()->paginate(10);

        return view('kecamatan.pembangunan.index', compact('stats', 'projects'));
    }

    public function show($id)
    {
        $project = PembangunanDesa::with(['desa', 'logbooks.createdBy'])->findOrFail($id);
        return view('kecamatan.pembangunan.show', compact('project'));
    }
}
