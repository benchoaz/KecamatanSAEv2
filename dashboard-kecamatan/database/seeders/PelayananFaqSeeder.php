<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PelayananFaq;

class PelayananFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate first to avoid duplicates
        PelayananFaq::truncate();

        $faqs = [
            [
                'category' => 'Administrasi',
                'module' => 'pelayanan',
                'question' => 'Syarat Pembuatan KTP Elektronik (e-KTP)',
                'answer' => "📋 **SOP Layanan KTP-el:**\n\n1. Telah berusia 17 tahun atau sudah kawin/pernah kawin.\n2. Fotokopi Kartu Keluarga (KK).\n3. Fotokopi Akta Kelahiran.\n4. Datang langsung ke Kantor Kecamatan untuk perekaman Biometrik (Manik mata, sidik jari, pas foto).\n\n⏱️ **Estimasi:** 15 Menit (Perekaman)\n💰 **Biaya:** GRATIS (Sesuai UU No. 24/2013)",
                'keywords' => 'ktp,ktp baru,buat ktp,syarat ktp,kartu tanda penduduk,elektronik ktp,e-ktp,rekam ktp,bikin ktp,foto ktp',
                'priority' => 10,
                'is_active' => true
            ],
            [
                'category' => 'Administrasi',
                'module' => 'pelayanan',
                'question' => 'Syarat Pembuatan Kartu Keluarga (KK)',
                'answer' => "📋 **SOP Layanan Kartu Keluarga:**\n\n1. Kartu Keluarga Asli (Lama).\n2. Fotokopi Akta Nikah/Buku Nikah.\n3. Fotokopi Akta Kelahiran pembentuk keluarga baru.\n4. Surat Pengantar RT/RW.\n5. Mengisi Formulir F-1.01 di Kantor Desa/Kecamatan.\n\n💰 **Biaya:** GRATIS",
                'keywords' => 'kk,kk baru,buat kk,syarat kk,kartu keluarga,pindah kk,pecah kk,bikin kk',
                'priority' => 10,
                'is_active' => true
            ],
            [
                'category' => 'Administrasi',
                'module' => 'pelayanan',
                'question' => 'Syarat Akta Kelahiran & Kematian',
                'answer' => "👶 **Akta Kelahiran:** Surat kelahiran Dokter/Bidan, Fotokopi Buku Nikah, KK, KTP Tua.\n\n💀 **Akta Kematian:** Surat Kematian dari RS/Puskesmas/Desa, KK & KTP almarhum.\n\n💰 **Biaya:** GRATIS (Jika dilaporkan < 60 hari)",
                'keywords' => 'akta,akta lahir,syarat akta,kelahiran,kematian,meninggal,lahir,bikin akta,surat lahir',
                'priority' => 10,
                'is_active' => true
            ],
            [
                'category' => 'Administrasi',
                'module' => 'pelayanan',
                'question' => 'Syarat Pindah Datang & Keluar',
                'answer' => "🚚 **Pindah Keluar:** KK Asli, KTP Asli. Petugas akan menerbitkan SKPWNI.\n\n📍 **Pindah Datang:** Membawa Surat Pindah (SKPWNI) dari daerah asal & KK tujuan.\n\n⚠️ Pastikan data di Dispendukcapil sudah ter-update (online) sebelum mengurus di alamat baru.",
                'keywords' => 'pindah,keluar,datang,surat pindah,pindah domisili,pindah penduduk,skpwni,pindah alamat',
                'priority' => 8,
                'is_active' => true
            ],
            [
                'category' => 'Administrasi',
                'module' => 'pelayanan',
                'question' => 'Legalisir Dokumen Kependudukan',
                'answer' => "📄 **Legalisir Dokumen:**\n\nSesuai Permendagri 104/2019, dokumen kependudukan (KK, KTP, Akta) yang sudah menggunakan **Tanda Tangan Elektronik (barcode/QR Code) TIDAK PERLU dilegalisir lagi** karena keasliannya dapat dicek via scan QR.",
                'keywords' => 'legalisir,legalisir ktp,legalisir kk,tanda tangan,stempel,pengesahan',
                'priority' => 5,
                'is_active' => true
            ],
            [
                'category' => 'Ekonomi',
                'module' => 'pelayanan',
                'question' => 'Surat Keterangan Usaha (SKU) / NIB',
                'answer' => "🏪 **Izin Usaha Mikro (NIB):**\nSekarang pengurusan izin usaha dilakukan via Online (OSS.go.id). Jika butuh bantuan, silakan bawa KTP & HP ke Kantor Kecamatan seksi Ekonomi.\n\n🏢 **SKU:** Fotokopi KTP, KK, dan Surat Pengantar dari Desa tempat usaha berada.",
                'keywords' => 'usaha,ket usaha,sku,nib,izin dagang,warung,modal,umkm,oss',
                'priority' => 7,
                'is_active' => true
            ],
            [
                'category' => 'Umum',
                'module' => 'pelayanan',
                'question' => 'Jam Pelayanan & Operasional',
                'answer' => "🕐 **Jam Operasional Kantor:**\n\n- Senin - Kamis: 08:00 - 15:30 WIB\n- Jumat: 08:00 - 11:30 WIB\n- Sabtu & Minggu: LIBUR\n\n📍 Layanan pengaduan online aktif 24 jam via Website & WhatsApp Bot.",
                'keywords' => 'jam kerja,jam kantor,jam layanan,buka jam,tutup jam,sabtu buka,hari kerja',
                'priority' => 9,
                'is_active' => true
            ],
            [
                'category' => 'Darurat',
                'module' => 'pelayanan',
                'question' => 'Kontak Darurat Wilayah',
                'answer' => "🚨 **Emergency Hotlines:**\n\n- Call Center POLRI: 110\n- Damkar (Pemadam): 112\n- Ambulans/Medis: 119\n\n📞 **Kecamatan Command Center:**\nSilakan hubungi WhatsApp resmi di nomor yang tertera di Footer untuk bantuan darurat kewilayahan.",
                'keywords' => 'darurat,nomor darurat,nomer darurat,emergency,polisi,ambulan,pemadam,kebakaran,maling,begal',
                'priority' => 10,
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
