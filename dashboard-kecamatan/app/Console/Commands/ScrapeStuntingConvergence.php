<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Desa;

class ScrapeStuntingConvergence extends Command
{
    protected $signature = 'scrape:stunting-convergence';
    protected $description = 'Scrape stunting convergence data from Kemendagri Konvergensi Portal';

    protected $baseUrl = 'https://konvergensi.bangda.kemendagri.go.id';

    public function handle()
    {
        $this->info('Starting Comprehensive Stunting Convergence Scraping...');

        $jar = new \GuzzleHttp\Cookie\CookieJar();

        try {
            // Step 1: Establish session
            $this->line('  Establishing session...');
            Http::withOptions(['verify' => false, 'cookies' => $jar, 'allow_redirects' => true])
                ->get($this->baseUrl . '/kecamatan');

            // Step 2: Authenticate
            $this->line('  Logging in...');
            $loginRes = Http::withOptions([
                'verify' => false,
                'cookies' => $jar,
                'allow_redirects' => false,
            ])->asForm()->post($this->baseUrl . '/index.php/loginkec', [
                'username' => 'besuk3513.21',
                'password' => 'nusantara25',
            ]);

            $this->info('Login successful.');

            // Step 3: Scrape Action Status
            $this->line('  Fetching action convergence status...');
            $pantauData = $this->scrapePantau($jar);

            // Step 4: Scrape all master & semantic endpoints
            $baseEndpoints = ['sasarankec', 'sasaran2kec', 'sasaran3kec', 'pendukungkec'];
            $masterEndpoints = ['master1kec', 'master2kec', 'master3kec'];

            $villageStore = [];

            // 4a. Scrape semantic endpoints (Data Sasaran & Pendukung)
            $baseEndpoints = [
                'sasarankec'   => 'Data Sasaran 1',
                'sasaran2kec'  => 'Data Sasaran 2', 
                'sasaran3kec'  => 'Data Sasaran 3',
                'pendukungkec' => 'Data Pendukung'
            ];
            
            foreach ($baseEndpoints as $slug => $label) {
                $this->line("  Scraping semantic endpoint: {$label} ({$slug})...");
                $data = $this->scrapeMaster($jar, $slug);
                $this->mergeToStore($villageStore, $data);
            }

            // 4b. Scrape master endpoints (with togglable sub-forms)
            $masterEndpoints = ['master1kec', 'master2kec', 'master3kec'];
            foreach ($masterEndpoints as $slug) {
                $this->line("  Scraping master endpoint with subforms: {$slug}...");
                
                // Fetch page to discover forms
                $r = Http::withOptions(['verify' => false, 'cookies' => $jar])->get($this->baseUrl . '/index.php/' . $slug);
                preg_match_all('/<option[^>]*value=[\"\'](.*?)[\"\'][^>]*>(.*?)<\/option>/si', $r->body(), $options);
                
                $formIds = array_filter(array_unique($options[1] ?? []));
                
                if (empty($formIds)) {
                    $data = $this->scrapeMaster($jar, $slug);
                    $this->mergeToStore($villageStore, $data);
                    continue;
                }

                foreach ($formIds as $fid) {
                    $this->line("    -> Toggling form: {$fid}");
                    Http::withOptions(['verify' => false, 'cookies' => $jar, 'allow_redirects' => true])
                        ->asForm()->post($this->baseUrl . '/index.php/bukaverifikasikec', [
                            'idform' => $fid,
                            'btnbuka' => '1'
                        ]);
                    
                    // Scrape results
                    $data = $this->scrapeMaster($jar, $slug);
                    $this->mergeToStore($villageStore, $data);
                }
            }

            // Step 5: Merge and persist
            $convergenceData = [
                'scraped_at' => now()->toIso8601String(),
                'source' => 'Portal Konvergensi Stunting Kemendagri',
                'aksi_konvergensi' => $pantauData,
                'villages' => array_values($villageStore),
                'summary' => $this->buildSummary($pantauData),
                'capaian_per_desa' => array_map(fn($v) => [
                    'nama_desa' => $v['nama_desa'],
                    'status' => $v['status'],
                    'indikator' => $v['indicators']
                ], array_values($villageStore))
            ];

            \Illuminate\Support\Facades\Storage::put(
                'convergence_data.json',
                json_encode($convergenceData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            );

            $this->info('Data convergence berhasil disimpan: ' . count($villageStore) . ' desa diproses.');

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            Log::error('ScrapeStuntingConvergence Error: ' . $e->getMessage());
        }

        return 0;
    }

    private function mergeToStore(array &$store, array $newData): void
    {
        foreach ($newData as $entry) {
            $name = $this->normalizeName($entry['nama_desa']);
            if (empty($name)) continue;

            if (!isset($store[$name])) {
                $store[$name] = [
                    'nama_desa' => $name,
                    'status'    => '',
                    'indicators' => []
                ];
            }
            
            if (isset($entry['indikator'])) {
                foreach ($entry['indikator'] as $k => $v) {
                    // We only update if the new value is non-zero, or if we don't have the key yet
                    if (!isset($store[$name]['indicators'][$k]) || $v != 0) {
                        $store[$name]['indicators'][$k] = $v;
                    }
                }
            }
            
            if (!empty($entry['status']) && empty($store[$name]['status'])) {
                $store[$name]['status'] = $entry['status'];
            }
        }
    }

    private function normalizeName($name): string
    {
        $name = trim(strip_tags(html_entity_decode($name)));
        $name = preg_replace('/^Desa\s+/i', '', $name);
        return trim($name);
    }

    private function scrapePantau(\GuzzleHttp\Cookie\CookieJar $jar): array
    {
        $r = Http::withOptions(['verify' => false, 'cookies' => $jar])
            ->get($this->baseUrl . '/index.php/pantauaksikec');

        $data = [];
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $r->body(), $trs);

        foreach ($trs[1] as $tr) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $tr, $tds);
            $cells = array_values(array_map(fn($c) => trim(strip_tags($c)), $tds[1]));

            if (count($cells) >= 5) {
                $nomor = $cells[1] ?? '';
                $namaForm = $cells[2] ?? '';
                $status = $cells[4] ?? '';
                $catatan = $cells[5] ?? '';

                // Identify semester
                $semester = null;
                if (strpos($namaForm, 'Semester 1') !== false) $semester = 1;
                elseif (strpos($namaForm, 'Semester 2') !== false) $semester = 2;

                $data[] = [
                    'kelompok'   => $cells[0] ?? '',
                    'nomor'      => $nomor,
                    'nama_form'  => $namaForm,
                    'status'     => $status,
                    'catatan'    => $catatan,
                    'semester'   => $semester,
                    'is_approved' => stripos($status, 'approved') !== false,
                ];
            }
        }

        return $data;
    }

    private function scrapeMaster(\GuzzleHttp\Cookie\CookieJar $jar, string $endpoint): array
    {
        $r = Http::withOptions(['verify' => false, 'cookies' => $jar])
            ->get($this->baseUrl . '/index.php/' . $endpoint);

        $data = [];
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/si', $r->body(), $trs);

        // Extract headers
        preg_match_all('/<th[^>]*>(.*?)<\/th>/si', $r->body(), $ths);
        $headers = array_map(fn($h) => trim(strip_tags($h)), $ths[1]);

        foreach ($trs[1] as $tr) {
            preg_match_all('/<td[^>]*>(.*?)<\/td>/si', $tr, $tds);
            $cells = array_map(fn($c) => trim(strip_tags(html_entity_decode($c))), $tds[1]);

            if (count($cells) >= 2 && is_numeric(trim($cells[0] ?? ''))) {
                $entry = [
                    'no'         => (int) trim($cells[0]),
                    'nama_desa'  => trim($cells[1] ?? ''),
                    'status'     => trim($cells[count($cells) - 1] ?? ''),
                ];

                // Map remaining cells to header names
                for ($i = 2; $i < count($cells) - 1; $i++) {
                    $headerKey = isset($headers[$i]) ? trim($headers[$i]) : 'col_' . $i;
                    $entry['indikator'][$headerKey] = (float) str_replace(',', '', trim($cells[$i] ?? '0'));
                }

                $data[] = $entry;
            }
        }

        return $data;
    }

    private function buildSummary(array $pantauData): array
    {
        $kelompok = [];
        foreach ($pantauData as $row) {
            $k = $row['kelompok'];
            if (!isset($kelompok[$k])) {
                $kelompok[$k] = ['total' => 0, 'approved' => 0];
            }
            $kelompok[$k]['total']++;
            if ($row['is_approved']) $kelompok[$k]['approved']++;
        }

        $s1Forms = array_filter($pantauData, fn($r) => $r['semester'] === 1);
        $s2Forms = array_filter($pantauData, fn($r) => $r['semester'] === 2);

        return [
            'total_form'          => count($pantauData),
            'approved'            => count(array_filter($pantauData, fn($r) => $r['is_approved'])),
            'pending'             => count(array_filter($pantauData, fn($r) => !$r['is_approved'])),
            'semester_1_total'    => count($s1Forms),
            'semester_1_approved' => count(array_filter($s1Forms, fn($r) => $r['is_approved'])),
            'semester_2_total'    => count($s2Forms),
            'semester_2_approved' => count(array_filter($s2Forms, fn($r) => $r['is_approved'])),
            'per_kelompok'        => $kelompok,
        ];
    }
}
