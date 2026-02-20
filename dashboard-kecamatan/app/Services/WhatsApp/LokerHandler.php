<?php

namespace App\Services\WhatsApp;

use App\Models\Loker;

class LokerHandler
{
    /**
     * Search job vacancies
     */
    public function search(string $query): array
    {
        // Search active job vacancies (job_category is usually used to distinguish if there are modules)
        // In this schema, job_category seems to be the main differentiator for Loker
        $queryBuilder = Loker::where('status', Loker::STATUS_AKTIF)
            ->where('is_verified', true)
            ->where('is_flagged', false);

        // Add search filter if query is provided
        if (!empty($query)) {
            $queryBuilder->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }

        $lokers = $queryBuilder->latest()->limit(5)->get();

        if ($lokers->isEmpty()) {
            $message = empty($query)
                ? "Saat ini tidak ada lowongan kerja yang tersedia."
                : "Tidak ditemukan lowongan kerja dengan kata kunci \"*{$query}*\".";

            return [
                'success' => true,
                'intent' => 'loker',
                'reply' => $message . "\n\nKetik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        return [
            'success' => true,
            'intent' => 'loker',
            'reply' => $this->formatResults($lokers, $query),
            'state_update' => null,
        ];
    }

    /**
     * Format search results
     */
    protected function formatResults($lokers, string $query): string
    {
        $message = "💼 *LOWONGAN KERJA*\n";

        if (!empty($query)) {
            $message .= "Kata kunci: \"*{$query}*\"\n";
        }

        $message .= "Ditemukan {$lokers->count()} lowongan:\n\n";

        foreach ($lokers as $index => $loker) {
            $num = $index + 1;
            $message .= "{$num}. *{$loker->title}*\n";

            if ($loker->contact_wa) {
                $message .= "   📞 {$loker->contact_wa}\n";
            }

            if ($loker->work_time) {
                $message .= "   ⏰ Waktu Kerja: {$loker->work_time}\n";
            }

            if ($loker->created_at) {
                $message .= "   📅 Diposting: {$loker->created_at->format('d/m/Y')}\n";
            }

            $message .= "\n";
        }

        if ($lokers->count() === 5) {
            $message .= "💡 _Menampilkan 5 lowongan terbaru._";
        }

        return $message;
    }
}
