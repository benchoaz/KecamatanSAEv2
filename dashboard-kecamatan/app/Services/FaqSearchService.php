<?php

namespace App\Services;

use App\Models\PelayananFaq;
use Illuminate\Support\Collection;

class FaqSearchService
{
    /**
     * Search for FAQs based on query with weighted scoring
     * 
     * @param string $query
     * @return array Result with 'found', 'multiple', 'results', and 'is_emergency'
     */
    public function search(string $query): array
    {
        $query = strtolower(trim($query));
        if (empty($query)) {
            return ['found' => false, 'results' => []];
        }

        // 1. Synonyms Pre-processing
        $synonyms = [
            'jam layanan' => 'jam pelayanan',
            'buka jam' => 'jam pelayanan',
            'tutup jam' => 'jam pelayanan',
            'jadwal' => 'jam',
            'syarat' => 'persyaratan',
            'bikin' => 'buat',
            'ngurus' => 'buat',
            'daftar' => 'buat',
            'pendaftaran' => 'buat',
            'biaya' => 'gratis',
            'bayar' => 'gratis'
        ];

        $originalQuery = $query;
        foreach ($synonyms as $from => $to) {
            if (str_contains($query, $from)) {
                $query = str_replace($from, $to, $query);
            }
        }

        // 2. Emergency/Darurat Priority (Database)
        $emergencyFaqs = PelayananFaq::where('is_active', true)
            ->where('category', 'Darurat')
            ->get();

        foreach ($emergencyFaqs as $faq) {
            if ($this->isMatch($faq, $query, $originalQuery)) {
                return [
                    'found' => true,
                    'is_emergency' => true,
                    'results' => [['question' => $faq->question, 'answer' => $faq->answer]]
                ];
            }
        }

        // 3. Fallback Hardcoded Emergency (Health, Criminal, etc.)
        // [Logic moved to this service for consistency if needed, but keeping it simple for now]

        // 4. Weighted Scoring Match
        $matches = collect();

        // 4a. Match FAQ
        $activeFaqs = \App\Models\PelayananFaq::where('is_active', true)->get();
        foreach ($activeFaqs as $faq) {
            $score = 0;
            $lowTitle = strtolower($faq->question);
            $keywords = explode(',', strtolower($faq->keywords));

            // Weight 1: Exact Title Match
            if ($lowTitle == $query || $lowTitle == $originalQuery) $score += 100;

            // Weight 2: Title Contains
            if (str_contains($lowTitle, $query) || str_contains($lowTitle, $originalQuery)) $score += 50;

            // Weight 3: Keyword Match
            foreach ($keywords as $kw) {
                $trimmed = trim($kw);
                if (empty($trimmed)) continue;

                if (preg_match('/\b' . preg_quote($trimmed, '/') . '\b/i', $query) || 
                    preg_match('/\b' . preg_quote($trimmed, '/') . '\b/i', $originalQuery)) {
                    $score += 80;
                } elseif (str_contains($query, $trimmed) || str_contains($originalQuery, $trimmed)) {
                    $score += 30;
                }
            }

            if ($score > 0) {
                $matches->push([
                    'id' => 'faq_' . $faq->id,
                    'is_emergency' => $faq->category === 'Darurat',
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'score' => $score
                ]);
            }
        }

        // 4b. Match Master Layanan
        $masterLayanan = \App\Models\MasterLayanan::where('is_active', true)->get();
        foreach ($masterLayanan as $layanan) {
            $score = 0;
            $lowTitle = strtolower($layanan->nama_layanan);

            if ($lowTitle == $query || $lowTitle == $originalQuery) $score += 100;
            if (str_contains($lowTitle, $query) || str_contains($lowTitle, $originalQuery)) $score += 50;

            // Boost score if user asks for 'syarat' or 'dokumen'
            if (str_contains($query, 'syarat') || str_contains($query, 'persyaratan') || str_contains($query, 'dokumen') || str_contains($query, 'buat')) {
                if ($score > 0) $score += 40;
            }

            if ($score > 0) {
                $reqsText = is_array($layanan->attachment_requirements) 
                    ? implode("\n- ", $layanan->attachment_requirements)
                    : $layanan->attachment_requirements;

                $answer = "**SOP Layanan: {$layanan->nama_layanan}**\n";
                $answer .= "⏱️ Waktu Pelayanan: {$layanan->estimasi_waktu}\n\n";
                if (!empty($layanan->deskripsi_syarat)) {
                    $answer .= strip_tags(html_entity_decode($layanan->deskripsi_syarat)) . "\n\n";
                }
                if (!empty($reqsText)) {
                    $answer .= "**Persyaratan Dokumen:**\n- " . $reqsText;
                } else {
                    $answer .= "Sampaikan detail permohonan Anda ke petugas untuk syarat spesifik.";
                }

                $matches->push([
                    'id' => 'master_' . $layanan->id,
                    'is_emergency' => false,
                    'question' => "Syarat " . $layanan->nama_layanan,
                    'answer' => trim($answer),
                    'score' => $score
                ]);
            }
        }

        $sortedMatches = $matches->sortByDesc('score')->values();

        if ($sortedMatches->isNotEmpty()) {
            $topMatch = $sortedMatches->first();
            
            // If top match is very strong, return it directly
            if ($topMatch['score'] >= 80) {
                return [
                    'found' => true,
                    'multiple' => false,
                    'is_emergency' => $topMatch['is_emergency'],
                    'results' => [['question' => $topMatch['question'], 'answer' => $topMatch['answer']]]
                ];
            }

            // Otherwise, return top 3 as suggestions
            $suggestions = $sortedMatches->take(3)->map(function($m) {
                return [
                    'id' => $m['id'],
                    'question' => $m['question'],
                    'answer' => $m['answer']
                ];
            })->toArray();

            return [
                'found' => true,
                'multiple' => true,
                'results' => $suggestions
            ];
        }

        return [
            'found' => false,
            'answer' => "Maaf, saya tidak menemukan jawaban pasti. Coba kata kunci lain atau bagikan keperluan Anda pada petugas."
        ];
    }

    /**
     * Check if a specific FAQ matches the query (Simplified for priority checks)
     */
    protected function isMatch(\App\Models\PelayananFaq $faq, string $query, string $originalQuery): bool
    {
        $keywords = explode(',', strtolower($faq->keywords));
        foreach ($keywords as $kw) {
            $trimmed = trim($kw);
            if ($trimmed !== '' && (preg_match('/\b' . preg_quote($trimmed, '/') . '\b/i', $query) || preg_match('/\b' . preg_quote($trimmed, '/') . '\b/i', $originalQuery))) {
                return true;
            }
        }
        return false;
    }
}
