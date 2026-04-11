<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Desa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncTatadesaDemografi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'desa:sync-demografi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync demografi data from Tatadesa APIs for all 17 villages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai sinkronisasi data demografi dari Tatadesa...');

        $desas = Desa::all();

        foreach ($desas as $desa) {
            // Jika website kosong, buat format berdasarkan nama_desa, contoh: "Alas Nyiur" -> "alasnyiur.tatadesa.com"
            $websiteUrl = $desa->website;
            
            if (empty($websiteUrl)) {
                $subdomain = Str::slug(str_replace(' ', '', $desa->nama_desa), '');
                $websiteUrl = "{$subdomain}.tatadesa.com";
                
                // Simpan website
                $desa->website = $websiteUrl;
                $desa->save();
            }

            $this->info("Mengambil data untuk desa {$desa->nama_desa} ({$websiteUrl})...");

            try {
                // Fetch All Data Concurrent-ish
                $baseUrl = "https://{$websiteUrl}/api/v1/public";
                
                $responses = \Illuminate\Support\Facades\Http::pool(fn (\Illuminate\Http\Client\Pool $pool) => [
                    $pool->as('demografi')->timeout(10)->get("{$baseUrl}/penduduk/statistik"),
                    $pool->as('pendidikan')->timeout(10)->get("{$baseUrl}/penduduk/statistik/pendidikan"),
                    $pool->as('pekerjaan')->timeout(10)->get("{$baseUrl}/penduduk/statistik/pekerjaan"),
                    $pool->as('agama')->timeout(10)->get("{$baseUrl}/penduduk/statistik/agama"),
                    $pool->as('kesehatan')->timeout(10)->get("{$baseUrl}/health/statistik/yearly"),
                    $pool->as('desil')->timeout(10)->get("{$baseUrl}/desil/statistik"),
                ]);

                if ($responses['demografi']->successful()) {
                    $demografiData = $responses['demografi']->json('data');
                    if ($demografiData) {
                        $desa->jumlah_penduduk = $demografiData['totalPenduduk'] ?? $desa->jumlah_penduduk;
                        $desa->jumlah_laki_laki = $demografiData['jumlahLakiLaki'] ?? $desa->jumlah_laki_laki;
                        $desa->jumlah_perempuan = $demografiData['jumlahPerempuan'] ?? $desa->jumlah_perempuan;
                        $desa->jumlah_kk = $demografiData['totalKepalaKeluarga'] ?? $desa->jumlah_kk;
                    }
                }

                // Pendidikan
                if ($responses['pendidikan']->successful() && $responses['pendidikan']->json('data.pendidikan')) {
                    $desa->stat_pendidikan = $responses['pendidikan']->json('data.pendidikan');
                }
                
                // Pekerjaan
                if ($responses['pekerjaan']->successful() && $responses['pekerjaan']->json('data.pekerjaan')) {
                    $desa->stat_pekerjaan = $responses['pekerjaan']->json('data.pekerjaan');
                }
                
                // Agama
                if ($responses['agama']->successful() && $responses['agama']->json('data.agama')) {
                    $desa->stat_agama = $responses['agama']->json('data.agama');
                }
                
                // Kesehatan
                if ($responses['kesehatan']->successful() && $responses['kesehatan']->json('data')) {
                    // Cukup ambil data root nya seperti totalStunting, totalGiziNormal
                    $hData = $responses['kesehatan']->json('data');
                    $kesehatanSubset = [
                        'totalStunting' => $hData['totalStunting'] ?? 0,
                        'totalGiziNormal' => $hData['totalGiziNormal'] ?? 0,
                        'totalGiziBuruk' => $hData['totalGiziBuruk'] ?? 0,
                        'persentaseStunting' => $hData['persentaseStunting'] ?? 0,
                        'persentaseGiziNormal' => $hData['persentaseGiziNormal'] ?? 0
                    ];
                    $desa->stat_kesehatan = $kesehatanSubset;
                }
                
                // Desil Kesejahteraan
                if ($responses['desil']->successful() && $responses['desil']->json('data')) {
                    $desa->stat_desil = $responses['desil']->json('data');
                }

                $desa->save();
                $this->line("  <info>✓</info> Data berhasil diupdate: Penduduk dkk + Statistik Lengkap.");
            } catch (\Exception $e) {
                $this->line("  <error>✗</error> Error: " . $e->getMessage());
            }
            
            // Beri jeda 1 detik untuk menghindari rate limit
            sleep(1);
        }

        $this->info('Sinkronisasi selesai!');
    }
}
