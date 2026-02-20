<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasterLayanan;

class MasterLayananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nama_layanan' => 'Surat Keterangan',
                'deskripsi_syarat' => 'Fotokopi KTP, KK, & Surat Pengantar RT/RW.',
                'estimasi_waktu' => '15 Menit',
                'ikon' => 'fa-file-signature',
                'warna_bg' => 'bg-emerald-50',
                'warna_text' => 'text-emerald-600',
                'urutan' => 1
            ],
            [
                'nama_layanan' => 'SKTM',
                'deskripsi_syarat' => 'Fotokopi KK/KTP, Surat Pengantar Desa, & Bukti Penghasilan.',
                'estimasi_waktu' => '20 Menit',
                'ikon' => 'fa-hands-helping',
                'warna_bg' => 'bg-blue-50',
                'warna_text' => 'text-blue-600',
                'urutan' => 2
            ],
            [
                'nama_layanan' => 'Pengantar Nikah',
                'deskripsi_syarat' => 'Surat Pengantar Desa (N1-N4) & Fotokopi KK/KTP.',
                'estimasi_waktu' => '1 Jam',
                'ikon' => 'fa-heart',
                'warna_bg' => 'bg-rose-50',
                'warna_text' => 'text-rose-600',
                'urutan' => 3
            ],
            [
                'nama_layanan' => 'BPJS/Kesra',
                'deskripsi_syarat' => 'Fotokopi KK/KTP & Surat Rekomendasi Dinas Sosial.',
                'estimasi_waktu' => '15 Menit',
                'ikon' => 'fa-user-md',
                'warna_bg' => 'bg-teal-50',
                'warna_text' => 'text-teal-600',
                'urutan' => 4
            ],
            [
                'nama_layanan' => 'Adminduk',
                'deskripsi_syarat' => 'Formulir Pendaftaran & Dokumen Pendukung Lengkap.',
                'estimasi_waktu' => '2-5 Hari',
                'ikon' => 'fa-id-card',
                'warna_bg' => 'bg-indigo-50',
                'warna_text' => 'text-indigo-600',
                'urutan' => 5
            ],
            [
                'nama_layanan' => 'Layanan Lainnya',
                'deskripsi_syarat' => 'Hubungi petugas kami untuk detail persyaratan lainnya.',
                'estimasi_waktu' => 'Variatif',
                'ikon' => 'fa-ellipsis-h',
                'warna_bg' => 'bg-slate-50',
                'warna_text' => 'text-slate-600',
                'urutan' => 6
            ],
        ];

        foreach ($services as $service) {
            MasterLayanan::create($service);
        }
    }
}
