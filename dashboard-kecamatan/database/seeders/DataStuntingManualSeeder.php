<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desa;

class DataStuntingManualSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'Kecik' => ['stunting' => 27, 'totalBalita' => 200],
            'Bago' => ['stunting' => 18, 'totalBalita' => 150],
            'Alasnyiur' => ['stunting' => 12, 'totalBalita' => 100],
            'Sindetanyar' => ['stunting' => 18, 'totalBalita' => 110, 'giziNormal' => 90],
            'Sindetlami' => ['stunting' => 13, 'totalBalita' => 255, 'giziNormal' => 240],
            'Sumurdalam' => ['stunting' => 26, 'totalBalita' => 148, 'giziNormal' => 120],
            'Besuk Kidul' => ['stunting' => 17, 'totalBalita' => 176, 'giziNormal' => 155],
            'Besuk Agung' => ['stunting' => 17, 'totalBalita' => 184, 'giziNormal' => 165],
            'Randujalak' => ['stunting' => 14, 'totalBalita' => 142, 'giziNormal' => 125],
            'Alastengah' => ['stunting' => 62, 'totalBalita' => 394, 'giziNormal' => 320],
            'Alaskandang' => ['stunting' => 39, 'totalBalita' => 315, 'giziNormal' => 270],
            'Alassumurlor' => ['stunting' => 17, 'totalBalita' => 161, 'giziNormal' => 140],
            'Sumberan' => ['stunting' => 12, 'totalBalita' => 197, 'giziNormal' => 180],
            'Matekan' => ['stunting' => 25, 'totalBalita' => 482], // Extrapolated from image footnote
        ];

        foreach ($data as $nama => $stats) {
            $desa = Desa::where('nama_desa', 'LIKE', '%' . str_replace(' ', '%', $nama) . '%')->first();
            if ($desa) {
                // Determine missing values
                $totalBalita = $stats['totalBalita'];
                $stunting = $stats['stunting'];
                $giziNormal = $stats['giziNormal'] ?? ($totalBalita - $stunting);
                $giziBuruk = $totalBalita - $giziNormal - $stunting;
                if ($giziBuruk < 0) $giziBuruk = 0;

                $desa->stat_kesehatan = json_encode([
                    'totalBalita' => $totalBalita,
                    'totalStunting' => $stunting,
                    'totalGiziNormal' => $giziNormal,
                    'totalGiziBuruk' => $giziBuruk,
                    'totalGiziKurang' => 0,
                    'totalBumil' => rand(15, 50),
                    'totalBumilResti' => rand(0, 5)
                ]);
                $desa->save();
            }
        }
        
        echo "Data kesehatan stunting berhasil di-seed!\n";
    }
}
