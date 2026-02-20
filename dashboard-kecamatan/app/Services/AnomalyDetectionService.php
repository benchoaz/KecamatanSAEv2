<?php

namespace App\Services;

use App\Models\PembangunanDesa;
use App\Models\BltDesa;
use Carbon\Carbon;

class AnomalyDetectionService
{
    /**
     * Deteksi anomali pada laporan pembangunan.
     * 
     * @param PembangunanDesa $item
     * @return array
     */
    public function detectPembangunanAnomalies(PembangunanDesa $item)
    {
        $alerts = [];

        // 1. Ketidakseimbangan Anggaran vs Fisik
        if ($item->pagu_anggaran > 0) {
            $realizationPct = ($item->realisasi_anggaran / $item->pagu_anggaran) * 100;
            $progressPct = (int) str_replace('%', '', $item->progres_fisik);

            if ($realizationPct > 80 && $progressPct < 50) {
                $alerts[] = [
                    'type' => 'danger',
                    'message' => "Realisasi anggaran tinggi ($realizationPct%), namun progres fisik masih rendah ($progressPct%).",
                    'code' => 'budget_progress_mismatch'
                ];
            }
        }

        // 2. Progres Tinggi Tanpa Bukti (High Progress, Low Evidence)
        $progressPct = (int) str_replace('%', '', $item->progres_fisik);
        if ($progressPct > 75 && (empty($item->rab_file) || empty($item->gambar_rencana_file))) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "Progres sudah $progressPct%, namun dokumen RAB atau Gambar Rencana belum lengkap.",
                'code' => 'missing_docs'
            ];
        }

        // 3. Stagnasi Status
        if ($item->status_kegiatan === 'Sedang Berjalan') {
            $lastUpdate = Carbon::parse($item->updated_at);
            if ($lastUpdate->diffInDays(now()) > 30) {
                $alerts[] = [
                    'type' => 'info',
                    'message' => "Laporan tidak ada pembaruan status selama lebih dari 30 hari.",
                    'code' => 'stagnant_report'
                ];
            }
        }

        return $alerts;
    }

    /**
     * Deteksi anomali pada laporan BLT.
     * 
     * @param BltDesa $item
     * @return array
     */
    public function detectBltAnomalies(BltDesa $item)
    {
        $alerts = [];

        // 1. BLT Mismatch (Kalkulasi KPM vs Total Dana)
        // Standar BLT adalah Rp 300.000 per KPM per bulan. 
        // Ini asumsikan laporan adalah akumulasi atau per periode.
        // Kita gunakan toleransi tipis atau logika sbg indikasi.
        if ($item->kpm_terealisasi > 0) {
            $expectedMin = $item->kpm_terealisasi * 300000;
            if (abs((float) $item->total_dana_tersalurkan - $expectedMin) > 50000) { // Toleransi Rp 50k
                $alerts[] = [
                    'type' => 'warning',
                    'message' => "Dana tersalurkan (Rp " . number_format((float) $item->total_dana_tersalurkan, 0, ',', '.') . ") tidak sebanding dengan standar BLT untuk " . $item->kpm_terealisasi . " KPM.",
                    'code' => 'blt_amount_mismatch'
                ];
            }
        }

        // 2. KPM Melebihi Kuota
        if ($item->kpm_terealisasi > $item->jumlah_kpm) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "Jumlah KPM terealisasi melebihi kuota yang direncanakan.",
                'code' => 'kpm_over_quota'
            ];
        }

        return $alerts;
    }
}
