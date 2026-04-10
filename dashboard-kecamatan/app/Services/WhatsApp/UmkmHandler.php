<?php

namespace App\Services\WhatsApp;

use App\Models\UmkmLocal;

class UmkmHandler
{
    /**
     * Search UMKM by query
     */
    public function search(string $query): array
    {
        if (empty($query)) {
            return [
                'success' => true,
                'intent' => 'umkm',
                'reply' => "Silakan masukkan kata kunci pencarian.\nContoh: *umkm bakso*",
                'state_update' => null,
            ];
        }

        // Search verified and active UMKM only
        $umkms = UmkmLocal::where('module', UmkmLocal::MODULE_UMKM)
            ->where('is_verified', true)
            ->where('is_active', true)
            ->where('is_flagged', false)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('product', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->limit(5)
            ->get();

        if ($umkms->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'umkm',
                'reply' => "😔 *Maaf*, tidak ditemukan UMKM dengan kata kunci \"*{$query}*\".\n\n" .
                    "Silakan coba kata kunci lain atau ketik *MENU* atau *0* untuk kembali.",
                'state_update' => 'WAITING_UMKM_SEARCH',
            ];
        }

        return [
            'success' => true,
            'intent' => 'umkm',
            'reply' => $this->formatResults($umkms, $query),
            'state_update' => null,
        ];
    }

    /**
     * Format search results
     */
    protected function formatResults($umkms, string $query): string
    {
        $message = "HASIL PENCARIAN UMKM\n";
        $message .= "Kata kunci: \"*{$query}*\"\n";
        $message .= "Ditemukan {$umkms->count()} UMKM:\n\n";

        foreach ($umkms as $index => $umkm) {
            $num = $index + 1;
            $opStatus = $umkm->operational_status;
            $statusPrefix = $opStatus['is_open'] ? "🟢" : "🔴";
            
            $message .= "{$num}. {$statusPrefix} *{$umkm->name}* [{$opStatus['label']}]\n";
            $message .= "   {$umkm->address}\n";

            if ($umkm->operating_hours) {
                $message .= "   🕒 Jam: {$umkm->operating_hours}\n";
            }

            if ($umkm->contact_wa) {
                $message .= "   📱 WA: {$umkm->contact_wa}\n";
            }

            if ($umkm->product) {
                $message .= "   🛍️ {$umkm->product}\n";
            }

            $message .= "\n";
        }

        if ($umkms->count() === 5) {
            $message .= "_Menampilkan 5 hasil teratas._";
        }

        return $message;
    }
}
