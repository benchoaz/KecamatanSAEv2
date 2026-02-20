<?php

namespace App\Services\WhatsApp;

use App\Models\PelayananFaq;

class SyaratHandler
{
    /**
     * Search for requirements/syarat based on query
     */
    public function search(string $query): array
    {
        $query = trim(strtolower($query));

        // If empty query, show available categories
        if (empty($query)) {
            return $this->getCategoriesList();
        }

        // Search FAQ for matching keywords
        $faq = $this->findMatchingFaq($query);

        if ($faq) {
            return [
                'success' => true,
                'intent' => 'syarat',
                'reply' => $this->formatFaqAnswer($faq),
                'state_update' => null,
            ];
        }

        // No match found - show suggestions
        return [
            'success' => true,
            'intent' => 'syarat_not_found',
            'reply' => $this->getNotFoundMessage($query),
            'state_update' => null,
        ];
    }

    /**
     * Find matching FAQ from database
     */
    protected function findMatchingFaq(string $query): ?PelayananFaq
    {
        // Direct keyword match
        $faq = PelayananFaq::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(keywords) LIKE ?', ["%{$query}%"])
                    ->orWhereRaw('LOWER(question) LIKE ?', ["%{$query}%"]);
            })
            ->orderBy('priority', 'desc')
            ->first();

        if ($faq) {
            return $faq;
        }

        // Fuzzy match - check each keyword
        $faqs = PelayananFaq::where('is_active', true)->get();

        foreach ($faqs as $faq) {
            $keywords = explode(',', strtolower($faq->keywords));
            foreach ($keywords as $keyword) {
                $keyword = trim($keyword);
                if (str_contains($query, $keyword) || str_contains($keyword, $query)) {
                    return $faq;
                }
            }
        }

        return null;
    }

    /**
     * Format FAQ answer for WhatsApp
     */
    protected function formatFaqAnswer(PelayananFaq $faq): string
    {
        $reply = "📋 *{$faq->question}*\n\n";
        $reply .= $faq->answer;
        $reply .= "\n\n";
        $reply .= "💡 Ketik *SYARAT* untuk melihat daftar layanan lainnya.\n";
        $reply .= " Ketik *MENU* untuk kembali ke menu utama.";

        return $reply;
    }

    /**
     * Get list of available categories
     */
    protected function getCategoriesList(): string
    {
        $reply = "📋 *SYARAT LAYANAN KECAMATAN*\n\n";
        $reply .= "Silakan ketik layanan yang Anda butuhkan:\n\n";

        // Get all active FAQs grouped by category
        $faqs = PelayananFaq::where('is_active', true)
            ->where('category', '!=', 'Darurat')
            ->orderBy('category')
            ->orderBy('priority', 'desc')
            ->get();

        $grouped = $faqs->groupBy('category');

        foreach ($grouped as $category => $items) {
            $reply .= "*{$category}:*\n";
            foreach ($items as $faq) {
                $keywords = explode(',', $faq->keywords);
                $mainKeyword = trim($keywords[0] ?? '');
                $reply .= "• SYARAT {$mainKeyword}\n";
            }
            $reply .= "\n";
        }

        $reply .= "Contoh: *syarat kk*, *syarat ktp*, *syarat domisili*\n\n";
        $reply .= "Ketik *MENU* untuk kembali ke menu utama.";

        return $reply;
    }

    /**
     * Get not found message with suggestions
     */
    protected function getNotFoundMessage(string $query): string
    {
        $reply = "❌ Maaf, tidak ditemukan informasi syarat untuk \"{$query}\".\n\n";
        $reply .= "Silakan coba kata kunci lain seperti:\n";
        $reply .= "• SYARAT KTP\n";
        $reply .= "• SYARAT KK\n";
        $reply .= "• SYARAT AKTA\n";
        $reply .= "• SYARAT DOMISILI\n\n";
        $reply .= "Ketik *SYARAT* untuk melihat daftar lengkap.\n";
        $reply .= "Ketik *MENU* untuk kembali ke menu utama.";

        return $reply;
    }
}
