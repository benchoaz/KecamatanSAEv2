<?php

namespace Database\Seeders;

use App\Models\Desa\DesaSubmission;
use App\Models\Desa\DesaSubmissionDetail;
use App\Models\Desa\DesaSubmissionFile;
use App\Models\Desa\DesaSubmissionNote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MusdesTestSeeder extends Seeder
{
    /**
     * Seed test data for Musdes workflow.
     * Run dengan: php artisan db:seed --class=MusdesTestSeeder
     */
    public function run(): void
    {
        // Cari user operator desa pertama
        $operatorDesa = User::whereHas('role', function ($q) {
            $q->where('nama_role', 'operator_desa');
        })->first();

        if (!$operatorDesa || !$operatorDesa->desa_id) {
            $this->command->error('Tidak ada user operator_desa dengan desa_id. Buat user dulu!');
            return;
        }

        $desaId = $operatorDesa->desa_id;
        $userId = $operatorDesa->id;

        $this->command->info("Creating test Musdes for Desa ID: {$desaId}");

        // 1. Musdes Draft (Belum Lengkap)
        $draft = DesaSubmission::create([
            'id' => Str::uuid(),
            'desa_id' => $desaId,
            'modul' => 'musdes',
            'judul' => 'Musdes Perencanaan RKP 2026',
            'periode' => '2026',
            'status' => 'draft',
            'created_by' => $userId,
        ]);

        $draft->details()->createMany([
            ['field_key' => 'tanggal_pelaksanaan', 'field_value' => '2026-02-15'],
            ['field_key' => 'lokasi', 'field_value' => 'Balai Desa Suka Maju'],
            ['field_key' => 'jenis_musdes', 'field_value' => 'Musdes RKP Desa'],
            ['field_key' => 'keterangan', 'field_value' => 'Pembahasan rencana kerja tahun anggaran 2026'],
        ]);

        // 2. Musdes Submitted (Sudah Lengkap & Dikirim)
        $submitted = DesaSubmission::create([
            'id' => Str::uuid(),
            'desa_id' => $desaId,
            'modul' => 'musdes',
            'judul' => 'Musdes APBDes 2026',
            'periode' => '2026',
            'status' => 'submitted',
            'submitted_at' => now()->subDays(2),
            'created_by' => $userId,
        ]);

        $submitted->details()->createMany([
            ['field_key' => 'tanggal_pelaksanaan', 'field_value' => '2026-01-10'],
            ['field_key' => 'lokasi', 'field_value' => 'Balai Desa'],
            ['field_key' => 'jenis_musdes', 'field_value' => 'Musdes APBDes'],
            ['field_key' => 'keterangan', 'field_value' => 'Penetapan APBDes tahun 2026'],
        ]);

        // File dummy (hanya metadata, bukan file fisik)
        $submitted->files()->createMany([
            ['file_type' => 'berita_acara', 'file_path' => 'musdes/dummy/ba.pdf'],
            ['file_type' => 'daftar_hadir', 'file_path' => 'musdes/dummy/absen.jpg'],
            ['file_type' => 'foto_kegiatan', 'file_path' => 'musdes/dummy/foto1.jpg'],
        ]);

        // 3. Musdes Returned (Dikembalikan untuk Perbaikan)
        $returned = DesaSubmission::create([
            'id' => Str::uuid(),
            'desa_id' => $desaId,
            'modul' => 'musdes',
            'judul' => 'Musdes Khusus Pembangunan Jalan',
            'periode' => '2025',
            'status' => 'returned',
            'submitted_at' => now()->subDays(5),
            'created_by' => $userId,
        ]);

        $returned->details()->createMany([
            ['field_key' => 'tanggal_pelaksanaan', 'field_value' => '2025-12-20'],
            ['field_key' => 'lokasi', 'field_value' => 'Kantor Desa'],
            ['field_key' => 'jenis_musdes', 'field_value' => 'Musdes Khusus'],
            ['field_key' => 'keterangan', 'field_value' => 'Pembahasan alokasi dana pembangunan jalan desa'],
        ]);

        // Catatan pengembalian
        $returned->notes()->create([
            'note' => 'Berita acara belum ditandatangani oleh Kepala Desa. Mohon dilengkapi dan upload ulang.',
            'created_by' => 1, // Asumsi ID admin kecamatan
        ]);

        $this->command->info('✓ Created 3 test Musdes records:');
        $this->command->info('  - Draft: ' . $draft->judul);
        $this->command->info('  - Submitted: ' . $submitted->judul);
        $this->command->info('  - Returned: ' . $returned->judul);
    }
}
