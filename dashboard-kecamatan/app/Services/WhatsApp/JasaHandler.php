<?php

namespace App\Services\WhatsApp;

use App\Models\UmkmLocal;

class JasaHandler
{
    /**
     * Search JASA providers by query
     */
    public function search(string $query): array
    {
        if (empty($query)) {
            return [
                'success' => true,
                'intent' => 'jasa',
                'reply' => "Silakan masukkan kata kunci pencarian.\nContoh: *jasa tukang*",
                'state_update' => null,
            ];
        }

        // Search active JASA from UmkmLocal (based on module constant)
        $jasaList = UmkmLocal::where('module', UmkmLocal::MODULE_JASA)
            ->where('is_active', true)
            ->where('is_verified', true)
            ->where('is_flagged', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('product', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get();

        if ($jasaList->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'jasa',
                'reply' => "😔 *Maaf*, tidak ditemukan penyedia jasa dengan kata kunci \"*{$query}*\".\n\n" .
                    "Silakan coba kata kunci lain atau ketik *MENU* atau *0* untuk kembali.",
                'state_update' => 'MENU_JASA',
            ];
        }

        return [
            'success' => true,
            'intent' => 'jasa',
            'reply' => $this->formatResults($jasaList, $query),
            'state_update' => null,
        ];
    }

    /**
     * Format search results
     */
    protected function formatResults($jasaList, string $query): string
    {
        $message = "HASIL PENCARIAN JASA\n";
        $message .= "Kata kunci: \"*{$query}*\"\n";
        $message .= "Ditemukan {$jasaList->count()} penyedia jasa:\n\n";

        foreach ($jasaList as $index => $jasa) {
            $num = $index + 1;
            $opStatus = $jasa->operational_status;
            $statusPrefix = $opStatus['is_open'] ? "🟢" : "🔴";

            $message .= "{$num}. {$statusPrefix} *{$jasa->name}* [{$opStatus['label']}]\n";
            $message .= "   {$jasa->address}\n";

            if ($jasa->operating_hours) {
                $message .= "   🕒 Jam: {$jasa->operating_hours}\n";
            }

            if ($jasa->contact_wa) {
                $message .= "   📱 WA: {$jasa->contact_wa}\n";
            }

            if ($jasa->product) {
                $message .= "   🔧 {$jasa->product}\n";
            }

            $message .= "\n";
        }

        if ($jasaList->count() === 5) {
            $message .= "_Menampilkan 5 hasil teratas._";
        }

        return $message;
    }
}
