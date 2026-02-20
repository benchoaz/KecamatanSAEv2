<?php

namespace App\Http\Controllers\Desa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $desaId = $user->desa_id;
        $tahun = $request->input('tahun', date('Y'));

        abort_unless($desaId !== null, 403);

        // 1. Statistik Pemerintahan (Counts)
        $stats = [
            'perangkat' => \App\Models\PersonilDesa::where('desa_id', $desaId)->where('kategori', 'perangkat')->count(),
            'bpd' => \App\Models\PersonilDesa::where('desa_id', $desaId)->where('kategori', 'bpd')->count(),
            'lembaga' => \App\Models\LembagaDesa::where('desa_id', $desaId)->count(),
        ];

        // 2. Status Dokumen Perencanaan (Status Strings)
        // Musdes
        $musdesModel = \App\Models\Desa\DesaSubmission::where('desa_id', $desaId)
            ->where('modul', 'musdes')
            ->where('periode', $tahun)
            ->first();

        // Perencanaan
        $rpjmdesModel = \App\Models\PerencanaanDesa::where('desa_id', $desaId)
            ->where('tipe_dokumen', 'RPJMDes')
            ->latest()
            ->first();

        $rkpdesModel = \App\Models\PerencanaanDesa::where('desa_id', $desaId)
            ->where('tipe_dokumen', 'RKPDes')
            ->where('tahun', $tahun)
            ->first();

        $apbdesModel = \App\Models\PerencanaanDesa::where('desa_id', $desaId)
            ->where('tipe_dokumen', 'APBDes')
            ->where('tahun', $tahun)
            ->first();

        $perencanaan = [
            'musdes' => $this->getStatusLabel($musdesModel ? $musdesModel->status : null),
            'rpjmdes' => $this->getStatusLabel($rpjmdesModel ? $rpjmdesModel->status : null),
            'rkpdes' => $this->getStatusLabel($rkpdesModel ? $rkpdesModel->status : null),
            'apbdes' => $this->getStatusLabel($apbdesModel ? $apbdesModel->status : null),
        ];

        // 3. Statistik Pembangunan (Budget Absorption)
        $totalPagu = \App\Models\DesaPaguAnggaran::where('desa_id', $desaId)->where('tahun', $tahun)->sum('jumlah_pagu');

        $realisasiFisik = \App\Models\PembangunanDesa::where('desa_id', $desaId)->where('tahun_anggaran', $tahun)->sum('realisasi_anggaran');
        $realisasiBlt = \App\Models\BltDesa::where('desa_id', $desaId)->where('tahun_anggaran', $tahun)->sum('total_dana_tersalurkan');
        $totalRealisasi = $realisasiFisik + $realisasiBlt;

        $pembangunan = [
            'total_pagu' => $totalPagu,
            'total_realisasi' => $totalRealisasi,
            'persentase' => ($totalPagu > 0) ? round(($totalRealisasi / $totalPagu) * 100, 2) : 0,
        ];

        return view('desa.dashboard.index', compact('stats', 'perencanaan', 'pembangunan', 'user', 'tahun'));
    }

    private function getStatusLabel($status)
    {
        if (!$status)
            return 'Belum Ada';
        return match ($status) {
            'draft' => 'Draft',
            'dikirim' => 'Verifikasi',
            'diterima' => 'Selesai',
            'dikembalikan' => 'Revisi',
            'lengkap' => 'Lengkap',
            default => ucfirst($status)
        };
    }
}
