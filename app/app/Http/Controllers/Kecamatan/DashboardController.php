<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Desa;
use App\Models\PengunjungKecamatan;
use App\Models\Submission;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentUser = auth()->user();
        abort_unless($currentUser->desa_id === null, 403);

        $desas = Desa::all();
        $stats = [
            'jumlah_desa' => $desas->count(),
            'pengunjung_hari_ini' => PengunjungKecamatan::hariIni()->count(),
            'total_penduduk' => $desas->sum('jumlah_penduduk'),
            'surat_bulan_ini' => Submission::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'last_updated' => $desas->max('updated_at'),
        ];

        $activities = AuditLog::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($log) {
                return (object)[
                    'icon' => $this->getIconForAction($log->action),
                    'type' => $this->getTypeForAction($log->action),
                    'message' => ($log->user->name ?? 'System') . ' ' . $log->action . ' ' . $log->table_name,
                    'time' => $log->created_at->diffForHumans()
                ];
            });

        return view('kecamatan.dashboard.index', compact('stats', 'activities'));
    }

    private function getIconForAction($action)
    {
        return match ($action) {
            'create' => 'fa-plus-circle',
            'update' => 'fa-edit',
            'delete' => 'fa-trash',
            default => 'fa-info-circle',
        };
    }

    private function getTypeForAction($action)
    {
        return match ($action) {
            'create' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            default => 'primary',
        };
    }

    /**
     * Get dashboard statistics (for AJAX requests).
     */
    public function stats()
    {
        $desas = Desa::all();
        $totalPenduduk = $desas->sum('jumlah_penduduk');
        $suratBulanIni = Submission::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        return response()->json([
            'total_penduduk' => $totalPenduduk,
            'surat_bulan_ini' => $suratBulanIni,
            'jumlah_desa' => $desas->count(),
            'umkm_terdaftar' => \App\Models\UmkmLocal::count(),
            'trend' => [
                'penduduk' => '+0.0%', // Bisa dikalkulasi jika ada data historis
                'surat' => '+0.0%',
                'umkm' => '+0.0%'
            ]
        ]);
    }

    /**
     * Get chart data (for AJAX requests).
     */
    public function chartData(Request $request)
    {
        $period = $request->get('period', '7days');

        $serviceData = [
            'labels' => ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
            'datasets' => [
                [
                    'label' => 'Surat Masuk',
                    'data' => [12, 19, 15, 25, 22, 8, 5]
                ],
                [
                    'label' => 'Surat Keluar',
                    'data' => [8, 15, 12, 18, 20, 6, 3]
                ]
            ]
        ];

        $populationData = [
            'labels' => ['Sukamaju', 'Mekar Jaya', 'Harapan', 'Sejahtera', 'Makmur', 'Damai', 'Sentosa', 'Bahagia'],
            'data' => [2150, 1890, 1650, 1420, 1780, 1350, 1280, 1327]
        ];

        return response()->json([
            'service' => $serviceData,
            'population' => $populationData
        ]);
    }
}
