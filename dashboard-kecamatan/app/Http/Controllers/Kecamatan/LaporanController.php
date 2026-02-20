<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\Desa;
use App\Models\DokumenDesa;
use App\Models\PerencanaanDesa;
use App\Models\Submission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index()
    {
        $year = request('year', date('Y'));
        $desaId = request('desa_id');

        $desas = Desa::orderBy('nama_desa')->get();

        // Stats for Rekap Umum
        $totalDesa = $desas->count();
        $totalSubmissions = Submission::whereYear('created_at', $year)
            ->when($desaId, fn($q) => $q->where('desa_id', $desaId))
            ->count();

        $statusCounts = Submission::whereYear('created_at', $year)
            ->when($desaId, fn($q) => $q->where('desa_id', $desaId))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('kecamatan.laporan.index', compact(
            'desas',
            'totalDesa',
            'totalSubmissions',
            'statusCounts',
            'year',
            'desaId'
        ));
    }

    public function ekbang(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $desaId = $request->get('desa_id');
        $triwulan = $request->get('triwulan');

        // Logic for Ekbang reports (aggregate)
        // This would typically involve querying submissions and results
        // For demonstration, we'll fetch related submissions
        $reports = Submission::where('menu_id', 2) // Assuming 2 is Ekbang
            ->whereYear('created_at', $year)
            ->when($desaId, fn($q) => $q->where('desa_id', $desaId))
            ->with(['desa', 'aspek'])
            ->latest()
            ->get();

        return view('kecamatan.laporan.ekbang', compact('reports', 'year', 'desaId', 'triwulan'));
    }

    public function pemerintahan(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $desaId = $request->get('desa_id');

        // Administrative documents status
        $desas = Desa::withCount([
            'dokumens as rpjmdes_exists' => function ($q) use ($year) {
                $q->where('tipe_dokumen', 'RPJMDes');
            }
        ])
            ->withCount([
                'dokumens as rkpdes_exists' => function ($q) use ($year) {
                    $q->where('tipe_dokumen', 'RKPDes')->where('tahun', $year);
                }
            ])
            ->withCount([
                'dokumens as lkpj_exists' => function ($q) use ($year) {
                    $q->where('tipe_dokumen', 'LKPJ')->where('tahun', $year);
                }
            ])
            ->withCount([
                'dokumens as lppd_exists' => function ($q) use ($year) {
                    $q->where('tipe_dokumen', 'LPPD')->where('tahun', $year);
                }
            ])
            ->when($desaId, fn($q) => $q->where('id', $desaId))
            ->orderBy('nama_desa')
            ->get();

        return view('kecamatan.laporan.pemerintahan', compact('desas', 'year', 'desaId'));
    }

    public function kesra(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $desaId = $request->get('desa_id');

        $reports = Submission::where('menu_id', 3) // Assuming 3 is Kesra
            ->whereYear('created_at', $year)
            ->when($desaId, fn($q) => $q->where('desa_id', $desaId))
            ->with(['desa', 'aspek'])
            ->get();

        return view('kecamatan.laporan.kesra', compact('reports', 'year', 'desaId'));
    }

    public function trantibum(Request $request)
    {
        $year = $request->get('year', date('Y'));
        $desaId = $request->get('desa_id');

        $reports = Submission::where('menu_id', 4) // Assuming 4 is Trantibum
            ->whereYear('created_at', $year)
            ->when($desaId, fn($q) => $q->where('desa_id', $desaId))
            ->with(['desa', 'aspek'])
            ->get();

        return view('kecamatan.laporan.trantibum', compact('reports', 'year', 'desaId'));
    }

    public function export(Request $request)
    {
        // Handle PDF/Excel export logic
        return view('kecamatan.laporan.export');
    }
}
