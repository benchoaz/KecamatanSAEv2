<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Models\UmkmLocal;
use App\Models\Loker;

class OwnerHandler
{
    /**
     * Initiate owner toggle flow (request PIN)
     */
    public function initiate(string $phone): array
    {
        // Clean phone for lookup
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Check if phone owns any UMKM/JASA
        $umkm = UmkmLocal::where('contact_wa', 'LIKE', "%{$cleanPhone}%")->first();

        // Check if phone owns any LOKER
        $loker = Loker::where('contact_wa', 'LIKE', "%{$cleanPhone}%")->first();

        if (!$umkm && !$loker) {
            return [
                'success' => true,
                'intent' => 'owner_not_found',
                'reply' => "Nomor Anda tidak terdaftar sebagai pemilik UMKM, Penyedia Jasa, atau Pemasang Loker di sistem kami.\n\n" .
                    "Pastikan nomor WhatsApp ini ({$phone}) sesuai dengan yang terdaftar di database.",
                'state_update' => null,
            ];
        }

        // Store detected items for PIN verification
        $session = WhatsappSession::getOrCreate($phone);
        if ($umkm) {
            $session->setTempValue('owner_umkm_id', $umkm->id);
        }
        if ($loker) {
            $session->setTempValue('owner_loker_id', $loker->id);
        }

        return [
            'success' => true,
            'intent' => 'owner_request_pin',
            'reply' => "🔐 *KELOLA DATA ANDA*\n\n" .
                "Ditemukan data terdaftar untuk nomor ini.\n" .
                "Silakan masukkan PIN Anda untuk melanjutkan.\n\n" .
                "Ketik *BATAL* untuk membatalkan.",
            'state_update' => 'WAITING_OWNER_PIN',
        ];
    }

    /**
     * Handle PIN input
     */
    public function handlePin(WhatsappSession $session, string $message): array
    {
        if (strtolower($message) === 'batal') {
            $session->clear();
            return [
                'success' => true,
                'intent' => 'owner_cancelled',
                'reply' => "Dibatalkan. Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        $umkmId = $session->getTempValue('owner_umkm_id');
        $lokerId = $session->getTempValue('owner_loker_id');

        $umkm = $umkmId ? UmkmLocal::where('id', $umkmId)->where('owner_pin', $message)->first() : null;
        $loker = $lokerId ? Loker::where('id', $lokerId)->where('owner_pin', $message)->first() : null;

        if (!$umkm && !$loker) {
            return [
                'success' => true,
                'intent' => 'owner_pin_invalid',
                'reply' => "❌ PIN salah. Silakan coba lagi atau ketik *BATAL*.",
                'state_update' => 'WAITING_OWNER_PIN',
            ];
        }

        $session->updateState('WAITING_OWNER_ACTION');

        $reply = "✅ PIN benar!\n\n";
        $reply .= "Silakan pilih data yang ingin dikelola:\n";

        if ($umkm) {
            $status = $umkm->is_active ? '✅ AKTIF' : '❌ NONAKTIF';
            $reply .= "• UMKM/JASA: *{$umkm->name}* ({$status})\n";
            $session->setTempValue('target_type', 'umkm');
        }

        if ($loker) {
            $status = ($loker->status === 'aktif') ? '✅ AKTIF' : '❌ NONAKTIF';
            $reply .= "• LOKER: *{$loker->title}* ({$status})\n";
            // If both exist, we might need a selection step. 
            // For now, prioritize the one they typed if ambiguous, or handle both as a toggle.
            // Simplified: Toggle whatever is available.
            if (!$umkm)
                $session->setTempValue('target_type', 'loker');
        }

        $reply .= "\nPilih aksi:\n";
        $reply .= "1️⃣ Ketik *AKTIF* untuk mengaktifkan\n";
        $reply .= "2️⃣ Ketik *NONAKTIF* untuk mematikan (tidak bisa dicari)\n";
        $reply .= "3️⃣ Ketik *BATAL* untuk keluar";

        return [
            'success' => true,
            'intent' => 'owner_pin_valid',
            'reply' => $reply,
            'state_update' => 'WAITING_OWNER_ACTION',
        ];
    }

    /**
     * Handle toggle action
     */
    public function handleAction(WhatsappSession $session, string $message): array
    {
        $messageLower = strtolower(trim($message));

        if ($messageLower === 'batal') {
            $session->clear();
            return [
                'success' => true,
                'intent' => 'owner_cancelled',
                'reply' => "Dibatalkan. Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        $umkmId = $session->getTempValue('owner_umkm_id');
        $lokerId = $session->getTempValue('owner_loker_id');

        $action = ($messageLower === 'aktif');
        $newStatus = $action ? 'AKTIF' : 'NONAKTIF';

        $summary = "✅ *STATUS DIPERBARUI*\n\n";

        if ($umkmId) {
            $umkm = UmkmLocal::find($umkmId);
            if ($umkm) {
                $umkm->update(['is_active' => $action, 'last_toggle_at' => now()]);
                $summary .= "📍 *{$umkm->name}* -> *{$newStatus}*\n";
            }
        }

        if ($lokerId) {
            $loker = Loker::find($lokerId);
            if ($loker) {
                $lokerStatus = $action ? 'aktif' : 'nonaktif';
                $loker->update(['status' => $lokerStatus, 'last_toggle_at' => now()]);
                $summary .= "💼 *{$loker->title}* -> *{$newStatus}*\n";
            }
        }

        $summary .= "\nData telah diperbarui dan perubahan langsung berlaku di pencarian warga.";

        $session->clear();

        return [
            'success' => true,
            'intent' => 'owner_toggled',
            'reply' => $summary,
            'state_update' => null,
        ];
    }

    /**
     * Error response
     */
    protected function errorResponse(): array
    {
        return [
            'success' => false,
            'intent' => 'error',
            'reply' => "Terjadi kesalahan sistem. Silakan coba lagi nanti.",
            'state_update' => null,
        ];
    }
}
