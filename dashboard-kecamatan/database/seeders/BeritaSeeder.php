<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Berita;
use App\Models\User;
use Illuminate\Support\Str;

class BeritaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'Operator Kecamatan')->orWhere('nama_role', 'Super Admin');
        })->first();

        if (!$admin) {
            $this->command->info('Tidak ditemukan user admin untuk author berita. Skipping seeder.');
            return;
        }

        $beritas = [
            [
                'judul' => 'Camat Tinjau Langsung Proyek Jalan Desa Sukamaju',
                'kategori' => 'Pembangunan',
                'konten' => "Camat beserta jajaran Muspika melakukan peninjauan langsung terhadap progres pembangunan jalan rabat beton di Desa Sukamaju. \n\nKegiatan ini bertujuan untuk memastikan kualitas bangunan sesuai dengan RAB dan spesifikasi yang telah ditentukan. \"Kami ingin memastikan dana desa digunakan sebaik-baiknya untuk kemaslahatan warga,\" ujar Camat di sela-sela kunjungan. \n\nPembangunan jalan sepanjang 500 meter ini diharapkan dapat memperlancar akses ekonomi warga, terutama dalam mengangkut hasil bumi ke pasar kecamatan.",
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'view_count' => 154
            ],
            [
                'judul' => 'Penyaluran BLT-DD Tahap I Tahun 2026 Berjalan Lancar',
                'kategori' => 'Sosial',
                'konten' => "Penyaluran Bantuan Langsung Tunai Dana Desa (BLT-DD) Tahap I untuk tahun anggaran 2026 telah dilaksanakan serentak di 5 desa wilayah kecamatan hari ini.\n\nSebanyak 150 Keluarga Penerima Manfaat (KPM) menerima bantuan tunai sebesar Rp 300.000 per bulan. Penyaluran dilakukan di balai desa masing-masing dengan tetap mematuhi protokol kesehatan dan ketertiban.\n\nKepala Seksi Pemberdayaan Masyarakat Desa (PMD) Kecamatan mengingatkan agar bantuan ini digunakan untuk kebutuhan pokok sehari-hari.",
                'status' => 'published',
                'published_at' => now()->subDay(),
                'view_count' => 89
            ],
            [
                'judul' => 'Jadwal Layanan Perekaman E-KTP Keliling',
                'kategori' => 'Pemerintahan',
                'konten' => "Untuk mendekatkan pelayanan kepada masyarakat, Kecamatan akan menggelar layanan jemput bola perekaman E-KTP bagi pemula dan lansia.\n\nJadwal pelaksanaan:\n- Senin: Desa A\n- Selasa: Desa B\n- Rabu: Desa C\n\nWarga diharapkan membawa fotokopi Kartu Keluarga (KK). Layanan ini GRATIS tidak dipungut biaya apapun.",
                'status' => 'draft',
                'published_at' => null,
                'view_count' => 0
            ],
            [
                'judul' => 'Pelatihan UMKM: Digital Marketing untuk Produk Lokal',
                'kategori' => 'Ekonomi',
                'konten' => "Menggandeng Dinas Koperasi dan UKM, Kecamatan menyelenggarakan pelatihan Digital Marketing bagi pelaku UMKM se-kecamatan.\n\nPelatihan ini difokuskan pada penggunaan media sosial dan marketplace untuk memperluas jangkauan pasar produk unggulan desa. \"Potensi produk kita luar biasa, hanya perlu sentuhan kemasan dan pemasaran yang tepat,\" ungkap narasumber.",
                'status' => 'published',
                'published_at' => now()->subHours(5),
                'view_count' => 45
            ]
        ];

        foreach ($beritas as $data) {
            Berita::create([
                'judul' => $data['judul'],
                'slug' => Str::slug($data['judul']),
                'ringkasan' => Str::limit($data['konten'], 150),
                'konten' => $data['konten'],
                'kategori' => $data['kategori'],
                'status' => $data['status'],
                'thumbnail' => null, // Bisa diisi path dummy jika ada
                'view_count' => $data['view_count'],
                'author_id' => $admin->id,
                'published_at' => $data['published_at'],
            ]);
        }
    }
}
