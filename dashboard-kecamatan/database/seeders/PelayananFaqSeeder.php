<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PelayananFaq;

class PelayananFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'Adminduk',
                'module' => 'pelayanan',
                'question' => 'Syarat Pembuatan KTP Baru',
                'answer' => "📋 Syarat KTP Baru:\n1. Akta Lahir (asli)\n2. KK (asli + salinan)\n3. Surat Keterangan Kelurahan\n4. Pas Foto 4x6 (4 lembar)\n5. Biaya admin Rp 10.000",
                'keywords' => 'ktp baru, buat ktp, syarat ktp',
                'priority' => 1,
                'is_active' => true
            ],
            [
                'category' => 'Adminduk',
                'module' => 'pelayanan',
                'question' => 'Syarat Pembuatan KK Baru',
                'answer' => "📋 Syarat KK Baru:\n1. Akta Lahir (asli)\n2. KTP Ayah/Ibu (asli)\n3. Surat Keterangan Kelurahan\n4. Pas Foto keluarga (2 lembar)\n5. Biaya admin Rp 15.000",
                'keywords' => 'kk baru, buat kk, syarat kk',
                'priority' => 1,
                'is_active' => true
            ],
            [
                'category' => 'Adminduk',
                'module' => 'pelayanan',
                'question' => 'Syarat Akta Lahir',
                'answer' => "📋 Syarat Akta Lahir:\n1. Surat Keterangan Lahir dari Rumah Sakit\n2. KTP Ayah (asli)\n3. KTP Ibu (asli)\n4. Pas Foto Ayah/Ibu (1 lembar)\n5. Biaya admin Rp 5.000",
                'keywords' => 'akta lahir, syarat akta',
                'priority' => 1,
                'is_active' => true
            ],
            [
                'category' => 'Adminduk',
                'module' => 'pelayanan',
                'question' => 'Syarat Surat Pindah Domisili',
                'answer' => "📋 Syarat Pindah Domisili:\n1. SK Keluar dari Desa Asal\n2. KK (asli)\n3. KTP (asli)\n4. Surat Keterangan Tempat Tinggal Baru\n5. Pas Foto 4x6 (2 lembar)",
                'keywords' => 'pindah domisili, surat pindah, syarat pindah',
                'priority' => 1,
                'is_active' => true
            ],
            [
                'category' => 'Umum',
                'module' => 'pelayanan',
                'question' => 'Jam Layanan Kantor',
                'answer' => "🕐 Jam Layanan:\nSenin - Jumat: 08:00 - 16:00\nSabtu: 08:00 - 12:00\nMinggu: Tutup",
                'keywords' => 'jam kerja, jam kantor',
                'priority' => 0,
                'is_active' => true
            ],
            [
                'category' => 'Darurat',
                'module' => 'pelayanan',
                'question' => 'Nomor Darurat',
                'answer' => "🚨 Nomor Darurat:\n- Polisi: 110\n- Ambulans: 118\n- Pemadam: 113\n- Posko Camat: 082231203765",
                'keywords' => 'darurat, nomor darurat',
                'priority' => 2,
                'is_active' => true
            ],
            // Additional existing categories for completeness
            [
                'category' => 'Pemerintahan',
                'module' => 'pelayanan',
                'keywords' => 'desa, kades, aparat desa, konflik desa, perangkat desa',
                'question' => 'Ada masalah dengan aparat Desa, ke mana saya harus melapor?',
                'answer' => "Masalah terkait kinerja atau administrasi Desa dapat dikonsultasikan melalui Seksi Pemerintahan di Kecamatan. Kami akan melakukan mediasi atau pembinaan terhadap Pemerintah Desa terkait sesuai kewenangan Camat sebagai pembina wilayah.",
                'priority' => 1,
                'is_active' => true
            ],
            [
                'category' => 'Pembangunan',
                'module' => 'pelayanan',
                'keywords' => 'umkm, modal, izin usaha, ibp, nib',
                'question' => 'Ingin mengurus izin usaha kecil (UMKM) atau mencari bantuan modal.',
                'answer' => "Untuk pelaku usaha mikro, Anda dapat mengurus **NIB (Nomor Induk Berusaha)** secara mandiri melalui sistem OSS atau meminta bantuan pendampingan di Seksi Ekonomi & Pembangunan Kecamatan. Untuk bantuan modal, kami sering mengadakan sosialisasi program KUR dari perbankan atau pelatihan keterampilan UMKM.",
                'priority' => 1,
                'is_active' => true
            ]
        ];

        foreach ($faqs as $faq) {
            PelayananFaq::updateOrCreate(
                ['question' => $faq['question']],
                $faq
            );
        }
    }
}
