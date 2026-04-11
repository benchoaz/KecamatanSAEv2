<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Desa;
use App\Models\Berita;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ScrapeDesaNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:desa-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape news from TataDesa APIs for registered villages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting TataDesa News Aggregation...');

        $desas = Desa::whereNotNull('tatadesa_domain')
            ->where('tatadesa_domain', '!=', '')
            ->get();

        if ($desas->isEmpty()) {
            $this->warn('No villages found with a configured tatadesa_domain.');
            return;
        }

        foreach ($desas as $desa) {
            $domain = trim($desa->tatadesa_domain);
            
            // Clean up domain if user copy-pasted http
            $domain = preg_replace('#^https?://#', '', $domain);
            $domain = rtrim($domain, '/');

            $apiUrl = "https://{$domain}/api/v1/pub?status=PUBLISHED&type=NEWS&page=1&limit=12";
            $this->info("Fetching news from {$desa->nama_desa} ({$apiUrl})");

            try {
                $response = Http::timeout(10)->get($apiUrl);

                if (!$response->successful()) {
                    $this->error("Failed to fetch from {$domain}. Status: " . $response->status());
                    continue;
                }

                $json = $response->json();
                
                // Determine format (sometimes wrapped in data.data, sometimes just data)
                $articles = [];
                if (isset($json['data']['data']) && is_array($json['data']['data'])) {
                    $articles = $json['data']['data'];
                } elseif (isset($json['data']) && is_array($json['data'])) {
                    $articles = $json['data'];
                }

                if (empty($articles)) {
                    $this->warn("No articles found for {$desa->nama_desa}");
                    continue;
                }

                $count = 0;
                foreach ($articles as $item) {
                    if (empty($item['id'])) continue;

                    // Prevent duplicates
                    $existing = Berita::where('external_id', $item['id'])->first();
                    if ($existing) continue;

                    // Extract logic securely
                    $title = $item['title'] ?? 'Berita Desa';
                    $slug = Str::slug($desa->nama_desa) . '-' . ($item['slug'] ?? Str::slug($title) . '-' . Str::random(5));
                    
                    // Thumbnail extraction logic (handle array or string)
                    $thumbnailUrl = null;
                    if (!empty($item['thumbnail'])) {
                        $thumbnailUrl = is_string($item['thumbnail']) ? $item['thumbnail'] : null;
                    } elseif (!empty($item['media']) && is_array($item['media']) && isset($item['media'][0]['url'])) {
                        $thumbnailUrl = $item['media'][0]['url'];
                    }

                    // Save directly as external URL for now to save user filesystem space, 
                    // or could use a fallback image. We map directly to external_url.
                    $ringkasan = $item['shortDescription'] ?? Str::limit(strip_tags($item['content'] ?? ''), 150);
                    $konten = $item['content'] ?? $ringkasan;
                    
                    // Create clickbait headline
                    $clickbait_words = ['TERKINI', 'BARU', 'INFO DESA', 'KABAR DESA'];
                    $clickbait = $clickbait_words[array_rand($clickbait_words)] . ': ' . Str::limit($title, 50);

                    Berita::create([
                        'desa_id' => $desa->id,
                        'scope' => 'desa',
                        'source_type' => 'scraped',
                        'external_id' => $item['id'],
                        'external_url' => "https://{$domain}/news/" . ($item['slug'] ?? ''),
                        'external_source' => 'Website Desa ' . $desa->nama_desa,
                        'judul' => $title,
                        'slug' => Str::limit($slug, 150),
                        'ringkasan' => $ringkasan,
                        'konten' => $konten,
                        'kategori' => 'Berita Desa',
                        'thumbnail' => $thumbnailUrl, // Use external asset link
                        'status' => 'published',
                        'view_count' => 0,
                        'author_id' => 1, // System admin
                        'published_at' => !empty($item['createdAt']) ? Carbon::parse($item['createdAt']) : now(),
                        'clickbait_headline' => $clickbait,
                        'priority_level' => 3 
                    ]);
                    $count++;
                }

                $this->info("Successfully imported {$count} new articles for {$desa->nama_desa}.");

            } catch (\Exception $e) {
                $this->error("Exception for {$domain}: " . $e->getMessage());
                Log::error("ScrapeDesaNews Error [{$domain}]: " . $e->getMessage());
            }
        }

        $this->info('TataDesa News Aggregation Completed!');
    }
}
