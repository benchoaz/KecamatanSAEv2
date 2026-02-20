<?php

namespace App\Services;

use App\Models\MasterDokumen;
use App\Models\MasterKegiatan;
use Illuminate\Support\Str;

class SpjRuleEngine
{
    /**
     * Menyiapkan daftar dokumen SPJ yang disarankan berdasarkan data kegiatan.
     * 
     * @param array|MasterKegiatan $kegiatan
     * @param array $selectedComponents (opsional, dari input form)
     * @return \Illuminate\Support\Collection
     */
    public function getRecommendedDocuments($kegiatan, array $selectedComponents = [])
    {
        $requiredDocCodes = [];
        $isModel = $kegiatan instanceof MasterKegiatan;

        $nama = $isModel ? $kegiatan->nama_kegiatan : ($kegiatan['nama_kegiatan'] ?? '');
        $jenis = $isModel ? $kegiatan->jenis_kegiatan : ($kegiatan['jenis_kegiatan'] ?? '');
        $pagu = $isModel ? 0 : ($kegiatan['pagu_anggaran'] ?? 0); // Jika model, belum ada pagu real

        // 1. Rule: Jika ada Honor -> Tanda Terima Honor (DOC.06)
        if ($this->hasHonorComponent($kegiatan, $selectedComponents)) {
            $requiredDocCodes[] = 'DOC.06';
        }

        // 2. Rule: Jika ada Peserta/Orang -> Daftar Hadir (DOC.01)
        if ($this->hasParticipants($kegiatan, $selectedComponents)) {
            $requiredDocCodes[] = 'DOC.01';
        }

        // 3. Rule: Jika ada unsur Rapat/Musyawarah -> Notulen (DOC.02)
        if ($this->isMeetingActivity($nama)) {
            $requiredDocCodes[] = 'DOC.02';
            $requiredDocCodes[] = 'DOC.04'; // Undangan biasanya sepaket dengan rapat
        }

        // 4. Rule: Jika jenis kegiatanya Musdes -> BA Musdes (DOC.14)
        if ($jenis === 'musdes') {
            $requiredDocCodes[] = 'DOC.14';
        }

        // 5. Rule: Jika fisik -> Dokumen Fisik (RAB, Gambar, Lap, BAST)
        if ($jenis === 'fisik') {
            $requiredDocCodes[] = 'DOC.08'; // RAB
            $requiredDocCodes[] = 'DOC.09'; // Gambar
            $requiredDocCodes[] = 'DOC.10'; // Progres
            $requiredDocCodes[] = 'DOC.11'; // BAST
        }

        // 6. Rule: Jika ada Belanja Anggaran -> Kwitansi (DOC.05)
        if ($pagu > 0 || $jenis === 'fisik' || $jenis === 'non_fisik') {
            $requiredDocCodes[] = 'DOC.05';
        }

        // 7. Selalu butuh dokumentasi
        $requiredDocCodes[] = 'DOC.03'; // Foto Dokumentasi

        return MasterDokumen::whereIn('kode_dokumen', array_unique($requiredDocCodes))
            ->active()
            ->orderBy('urutan')
            ->get();
    }

    /**
     * Cek apakah ada komponen honor dalam kegiatan.
     */
    private function hasHonorComponent($kegiatan, array $selectedComponents)
    {
        if (!empty($selectedComponents)) {
            // Cek dari input yang dipilih di form
            return \App\Models\MasterKomponenBelanja::whereIn('id', $selectedComponents)
                ->where('kategori', 'honor')
                ->exists();
        }

        if ($kegiatan instanceof MasterKegiatan) {
            return $kegiatan->komponenBelanjas()->where('kategori', 'honor')->exists();
        }

        return false;
    }

    /**
     * Cek apakah kegiatan melibatkan peserta/orang.
     */
    private function hasParticipants($kegiatan, array $selectedComponents)
    {
        $satuanParticipants = ['orang', 'peserta', 'kpm', 'oh', 'ok'];

        if ($kegiatan instanceof MasterKegiatan) {
            if (in_array(strtolower($kegiatan->satuan_default), $satuanParticipants)) {
                return true;
            }
        }

        // Cek label satuan di komponen belanja
        if (!empty($selectedComponents)) {
            return \App\Models\MasterKomponenBelanja::whereIn('id', $selectedComponents)
                ->whereIn('satuan', $satuanParticipants)
                ->exists();
        }

        return false;
    }

    /**
     * Cek apakah nama kegiatan mengandung unsur rapat.
     */
    private function isMeetingActivity($nama)
    {
        $keywords = ['rapat', 'musyawarah', 'musdes', 'musrenbang', 'sosialisasi', 'pelatihan', 'bimtek'];
        return Str::contains(strtolower($nama), $keywords);
    }
}
