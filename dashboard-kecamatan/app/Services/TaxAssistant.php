<?php

namespace App\Services;

use App\Models\MasterKomponenBelanja;

class TaxAssistant
{
    /**
     * Memberikan estimasi pajak berdasarkan kategori komponen.
     * 
     * @param MasterKomponenBelanja $komponen
     * @param float $nilai
     * @return array|null
     */
    public function getTaxEstimation(MasterKomponenBelanja $komponen, $nilai)
    {
        if (!$komponen->objek_pajak) {
            return null;
        }

        $rate = $this->getRateByKategori($komponen->kategori);

        return [
            'kategori' => $komponen->kategori_label,
            'nilai_bruto' => $nilai,
            'estimasi_pajak' => $nilai * $rate,
            'rate_percent' => ($rate * 100),
            'teks_edukasi' => "Silakan sesuaikan dengan ketentuan daerah (Perda/Perbup terkait Pajak & Retribusi)."
        ];
    }

    /**
     * Mendapatkan rate pajak default berdasarkan kategori (Simulasi).
     */
    private function getRateByKategori($kategori)
    {
        return match ($kategori) {
            'konsumsi' => 0.10, // PB1 10%
            'jasa' => 0.02,     // PPh 23 2%
            'honor' => 0.05,    // PPh 21 simulasi 5%
            'barang' => 0.11,   // PPN 11%
            default => 0,
        };
    }
}
