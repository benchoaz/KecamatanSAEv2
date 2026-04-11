<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Berita;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;

class ScrapeKecamatanNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:kecamatan-news';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrape news directly from Kecamatan Besuk official website';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Kecamatan News Aggregation...');

        $targetUrl = 'https://besuk.probolinggokab.go.id/berita';
        $baseUrl = 'https://besuk.probolinggokab.go.id';

        try {
            // Non-verifying SSL for local/governmental websites sometimes is needed
            $response = Http::withOptions(['verify' => false])->timeout(15)->get($targetUrl);

            if (!$response->successful()) {
                $this->error("Failed to fetch from {$targetUrl}. Status: " . $response->status());
                return;
            }

            $html = $response->body();

            // Suppress DOM parsing warnings due to malformed HTML
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            libxml_clear_errors();

            $xpath = new DOMXPath($dom);
            
            // Find all news cards
            $cards = $xpath->query("//div[contains(@class, 'minimal-card')]");

            if ($cards->length === 0) {
                $this->warn("No articles found on the target website.");
                return;
            }

            $count = 0;
            foreach ($cards as $card) {
                // Extract Thumbnail
                $imgNode = $xpath->query(".//img[contains(@class, 'img-thumb')]", $card)->item(0);
                $thumbnail = $imgNode ? $imgNode->getAttribute('src') : null;
                if ($thumbnail && !Str::startsWith($thumbnail, 'http')) {
                    $thumbnail = rtrim($baseUrl, '/') . '/' . ltrim($thumbnail, '/');
                }

                // Extract Title and Link
                $titleNode = $xpath->query(".//div[contains(@class, 'card-content')]/h6/a", $card)->item(0);
                if (!$titleNode) continue;
                
                $title = trim($titleNode->textContent);
                $link = $titleNode->getAttribute('href');
                if ($link && !Str::startsWith($link, 'http')) {
                    $link = rtrim($baseUrl, '/') . '/' . ltrim($link, '/');
                }

                // Extract Summary
                $summaryNode = $xpath->query(".//div[contains(@class, 'card-content')]/p[contains(@class, 'text-truncate-3')]", $card)->item(0);
                $summary = $summaryNode ? trim(strip_tags($summaryNode->textContent)) : '';
                
                // Clean up string
                $title = html_entity_decode($title);
                $summary = html_entity_decode($summary);

                if (empty($title) || empty($link)) continue;

                // Identify completely unique external ID based on slug of the URL 
                $externalId = md5($link);

                // Prevent duplicates
                $existing = Berita::where('external_id', $externalId)->first();
                if ($existing) continue;

                $slug = Str::slug($title) . '-' . Str::random(5);

                $clickbait_words = ['TERKINI', 'INFO PENTING', 'INFO KECAMATAN', 'KABAR KECAMATAN'];
                $clickbait = $clickbait_words[array_rand($clickbait_words)] . ': ' . Str::limit($title, 50);

                Berita::create([
                    'desa_id' => null, // null means Kecamatan level
                    'scope' => 'kecamatan',
                    'source_type' => 'scraped',
                    'external_id' => $externalId,
                    'external_url' => $link,
                    'external_source' => 'Website Resmi Kecamatan',
                    'judul' => $title,
                    'slug' => Str::limit($slug, 150),
                    'ringkasan' => Str::limit($summary, 150),
                    'konten' => $summary . "\n\n*(Sumber: " . $link . ")*", 
                    'kategori' => 'Berita Kecamatan',
                    'thumbnail' => $thumbnail,
                    'status' => 'published',
                    'view_count' => rand(10, 50), // give it some initial views
                    'author_id' => 1,
                    'published_at' => now(), // We use now() as backup since date is buried inside summary text in html snippet
                    'clickbait_headline' => $clickbait,
                    'priority_level' => 4 // slightly higher priority than desa
                ]);
                
                $count++;
            }

            $this->info("Successfully imported {$count} new articles for Kecamatan.");

        } catch (\Exception $e) {
            $this->error("Exception while scraping: " . $e->getMessage());
            Log::error("ScrapeKecamatanNews Error: " . $e->getMessage());
        }
    }
}
