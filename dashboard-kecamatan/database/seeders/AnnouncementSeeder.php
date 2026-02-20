<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        if (!$admin)
            return;

        Announcement::create([
            'title' => 'Layanan Normal',
            'content' => 'Pelayanan Kecamatan tetap berjalan normal selama jam kerja (08.00 - 15.30 WIB).',
            'target_type' => 'public',
            'display_mode' => 'ticker',
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'priority' => 'normal',
            'is_active' => true,
            'created_by' => $admin->id
        ]);

        Announcement::create([
            'title' => 'Musdes APBDes 2026',
            'content' => 'Seluruh desa agar segera melaksanakan Musdes APBDes Tahun Anggaran 2026 paling lambat akhir bulan ini.',
            'target_type' => 'all_desa',
            'display_mode' => 'ticker',
            'start_date' => now(),
            'end_date' => now()->addMonths(1),
            'priority' => 'important',
            'is_active' => true,
            'created_by' => $admin->id
        ]);
    }
}
