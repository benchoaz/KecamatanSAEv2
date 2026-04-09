<?php

namespace App\Services;

use App\Models\PembangunanDesa;
use App\Models\Umkm;

use App\Models\UsulanMusrenbang;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Early Warning System (EWS) untuk Ekonomi & Pembangunan
 * 
 * Mendeteksi anomali dan mengirim notifikasi untuk:
 * - Pembangunan: Serapan anggaran, progress fisik, SPJ
 * - Ekonomi: UMKM tidak aktif, lowongan expired
 * - Musrenbang: Usulan melampaui deadline
 */
class EconomyEarlyWarningService
{
    /**
     * Check all warning conditions and return alerts
     */
    public function checkAll(): array
    {
        return [
            'pembangunan' => $this->checkPembangunan(),
            'ekonomi' => $this->checkEkonomi(),
            'musrenbang' => $this->checkMusrenbang(),
            'laporan' => $this->checkLaporan(),
        ];
    }

    /**
     * Check pembangunan/anomaly conditions
     */
    public function checkPembangunan(): array
    {
        $alerts = [];
        $currentYear = date('Y');

        // 1. Proyek dengan progress fisik mandek (>30 hari tidak berubah)
        $stagnantProjects = PembangunanDesa::where('status', 'executing')
            ->whereYear('tanggal_mulai', '<=', $currentYear)
            ->get()
            ->filter(function ($project) {
                $lastUpdate = $project->updated_at;
                $daysSinceUpdate = now()->diffInDays($lastUpdate);
                return $daysSinceUpdate > 30 && $project->progress_fisik < 100;
            });

        if ($stagnantProjects->isNotEmpty()) {
            $alerts[] = [
                'level' => 'critical',
                'type' => 'progress_stagnant',
                'title' => 'Proyek Progress Mandek',
                'message' => "{$stagnantProjects->count()} proyek tidak mengalami kemajuan >30 hari",
                'data' => $stagnantProjects->map(fn($p) => [
                    'id' => $p->id,
                    'nama' => $p->nama_kegatan,
                    'desa' => $p->desa->nama_desa ?? 'N/A',
                    'progress' => $p->progress_fisik,
                    'hari_tanpa_update' => now()->diffInDays($p->updated_at)
                ])
            ];
        }

        // 2. Anomali: Keuangan mendahului fisik (>20%)
        $financialAnomalies = PembangunanDesa::where('status', 'executing')
            ->whereRaw('progress_keuangan > (progress_fisik + 20)')
            ->get();

        if ($financialAnomalies->isNotEmpty()) {
            $alerts[] = [
                'level' => 'warning',
                'type' => 'finance_anomaly',
                'title' => 'Anomali Keuangan',
                'message' => "{$financialAnomalies->count()} proyek: keuangan mendahului fisik",
                'data' => $financialAnomalies->map(fn($p) => [
                    'id' => $p->id,
                    'nama' => $p->nama_kegatan,
                    'progress_fisik' => $p->progress_fisik,
                    'progress_keuangan' => $p->progress_keuangan
                ])
            ];
        }

        // 3. Serapan anggaran lambat (progress_keuangan < expected based on time)
        $projects = PembangunanDesa::where('status', 'executing')
            ->whereYear('tanggal_mulai', $currentYear)
            ->get();

        foreach ($projects as $project) {
            $totalDays = $project->tanggal_mulai->diffInDays($project->tanggal_selesai ?? now()->addMonths(6));
            $daysPassed = $project->tanggal_mulai->diffInDays(now());
            $expectedProgress = $totalDays > 0 ? ($daysPassed / $totalDays) * 100 : 0;

            if ($expectedProgress > 10 && $project->progress_keuangan < ($expectedProgress * 0.7)) {
                $alerts[] = [
                    'level' => 'warning',
                    'type' => 'slow_absorption',
                    'title' => 'Serapan Anggaran Lambat',
                    'message' => "Proyek {$project->nama_kegatan}: serapan < 70% dari seharusnya",
                    'data' => [
                        'id' => $project->id,
                        'nama' => $project->nama_kegatan,
                        'expected' => round($expectedProgress, 1),
                        'actual' => $project->progress_keuangan
                    ]
                ];
            }
        }

        // 4. SPJ tidak lengkap (>7 hari sejak proyek selesai)
        $incompleteSpj = DB::table('pembangunan_desa as pd')
            ->leftJoin('pembangunan_dokumen_spj as pds', 'pd.id', '=', 'pds.pembangunan_desa_id')
            ->where('pd.status', 'completed')
            ->where('pd.tanggal_selesai', '<=', now()->subDays(7))
            ->whereNull('pds.id')
            ->orWhere('pds.status', 'pending')
            ->select('pd.id', 'pd.nama_kegatan', 'pd.tanggal_selesai', 'pd.desa_id')
            ->get();

        if ($incompleteSpj->isNotEmpty()) {
            $alerts[] = [
                'level' => 'critical',
                'type' => 'spj_incomplete',
                'title' => 'SPJ Belum Lengkap',
                'message' => "{$incompleteSpj->count()} proyek selesai >7 hari belum ada SPJ",
                'data' => $incompleteSpj
            ];
        }

        return $alerts;
    }

    /**
     * Check ekonomi/anomaly conditions
     */
    public function checkEkonomi(): array
    {
        $alerts = [];

        // 1. UMKM tidak aktif tinggi (>30%)
        $totalUmkm = Umkm::count();
        $nonaktifUmkm = Umkm::where('status', 'nonaktif')->count();

        if ($totalUmkm > 0 && ($nonaktifUmkm / $totalUmkm) > 0.3) {
            $alerts[] = [
                'level' => 'info',
                'type' => 'umkm_inactive_high',
                'title' => 'UMKM Tidak Aktif Tinggi',
                'message' => round(($nonaktifUmkm / $totalUmkm) * 100, 1) . "% UMKM tidak aktif",
                'data' => [
                    'total' => $totalUmkm,
                    'nonaktif' => $nonaktifUmkm,
                    'persentase' => round(($nonaktifUmkm / $totalUmkm) * 100, 1)
                ]
            ];
        }



        // 3. UMKM pending verification (>14 hari)
        $pendingUmkm = Umkm::where('status', 'pending')
            ->where('created_at', '<=', now()->subDays(14))
            ->get();

        if ($pendingUmkm->isNotEmpty()) {
            $alerts[] = [
                'level' => 'warning',
                'type' => 'umkm_pending_long',
                'title' => 'UMKM Menunggu Verifikasi',
                'message' => "{$pendingUmkm->count()} UMKM pending >14 hari",
                'data' => $pendingUmkm->map(fn($u) => [
                    'id' => $u->id,
                    'nama_usaha' => $u->nama_usaha,
                    'hari_pending' => now()->diffInDays($u->created_at)
                ])
            ];
        }

        return $alerts;
    }

    /**
     * Check musrenbang conditions
     */
    public function checkMusrenbang(): array
    {
        $alerts = [];
        $currentYear = date('Y');

        // 1. Usulan Musrenbang belum diverifikasi (>30 hari)
        $unverified = UsulanMusrenbang::where('status', 'usulan')
            ->whereYear('created_at', '<=', $currentYear - 1)
            ->get();

        if ($unverified->isNotEmpty()) {
            $alerts[] = [
                'level' => 'warning',
                'type' => 'musrenbang_unverified',
                'title' => 'Usulan Musrenbang Belum Diverifikasi',
                'message' => "{$unverified->count()} usulan dari tahun lalu belum diverifikasi",
                'data' => $unverified->map(fn($m) => [
                    'id' => $m->id,
                    'nama_usulan' => $m->nama_usulan,
                    'tahun' => $m->tahun,
                    'hari_menunggu' => now()->diffInDays($m->created_at)
                ])
            ];
        }

        return $alerts;
    }

    /**
     * Check laporan/ekbang submission conditions
     */
    public function checkLaporan(): array
    {
        $alerts = [];
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // 1. Laporan bulan lalu belum submitted
        $lastMonth = now()->subMonth();
        $missingReports = DB::table('desa as d')
            ->leftJoin('submissions as s', function ($join) use ($lastMonth) {
                $join->on('d.id', '=', 's.desa_id')
                    ->whereYear('s.created_at', '=', $lastMonth->year)
                    ->whereMonth('s.created_at', '=', $lastMonth->month);
            })
            ->whereNull('s.id')
            ->select('d.id', 'd.nama_desa')
            ->get();

        if ($missingReports->isNotEmpty()) {
            $alerts[] = [
                'level' => 'warning',
                'type' => 'report_missing',
                'title' => 'Laporan Bulan Lalu Belum Ada',
                'message' => "{$missingReports->count()} desa belum submit laporan bulan " . $lastMonth->format('F Y'),
                'data' => $missingReports
            ];
        }

        return $alerts;
    }

    /**
     * Get summary counts for dashboard
     */
    public function getSummary(): array
    {
        $allAlerts = $this->checkAll();

        $critical = 0;
        $warning = 0;
        $info = 0;

        foreach ($allAlerts as $category) {
            foreach ($category as $alert) {
                match ($alert['level']) {
                    'critical' => $critical++,
                    'warning' => $warning++,
                    'info' => $info++,
                    default => null
                };
            }
        }

        return [
            'total' => $critical + $warning + $info,
            'critical' => $critical,
            'warning' => $warning,
            'info' => $info,
            'has_alerts' => ($critical + $warning + $info) > 0
        ];
    }
}
