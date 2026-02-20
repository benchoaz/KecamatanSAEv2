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
        // Check if phone owns any UMKM or JASA (UmkmLocal model handles both)
        $owner = UmkmLocal::where('contact_wa', 'LIKE', "%{$phone}%")->first();

        if (!$owner) {
            return [
                'success' => true,
                'intent' => 'owner_not_found',
                'reply' => "Nomor Anda tidak terdaftar sebagai pemilik UMKM atau penyedia jasa di sistem kami.\n\n" .
                    "Pastikan nomor WhatsApp ini sama dengan yang didaftarkan saat registrasi.",
                'state_update' => null,
            ];
        }

        return [
            'success' => true,
            'intent' => 'owner_request_pin',
            'reply' => "🔐 *KELOLA LAPAK/JASA*\n\n" .
                "Untuk keamanan, silakan masukkan PIN Anda.\n\n" .
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

        $phone = $session->phone;

        // Verify PIN
        $owner = UmkmLocal::where('contact_wa', 'LIKE', "%{$phone}%")
            ->where('owner_pin', $message)
            ->first();

        if (!$owner) {
            return [
                'success' => true,
                'intent' => 'owner_pin_invalid',
                'reply' => "❌ PIN salah. Silakan coba lagi atau ketik *BATAL*.",
                'state_update' => 'WAITING_OWNER_PIN',
            ];
        }

        // Store owner info in session
        $session->setTempValue('owner_id', $owner->id);
        $session->setTempValue('owner_name', $owner->name);

        $currentStatus = $owner->is_active ? 'AKTIF' : 'NONAKTIF';

        $session->updateState('WAITING_OWNER_ACTION');

        return [
            'success' => true,
            'intent' => 'owner_pin_valid',
            'reply' => "✅ PIN benar!\n\n" .
                "📍 *{$owner->name}*\n" .
                "Status saat ini: *{$currentStatus}*\n\n" .
                "Pilih aksi:\n" .
                "1️⃣ Ketik *AKTIF* untuk mengaktifkan lapak\n" .
                "2️⃣ Ketik *NONAKTIF* untuk menonaktifkan lapak\n" .
                "3️⃣ Ketik *BATAL* untuk keluar",
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

        $ownerId = $session->getTempValue('owner_id');
        $owner = UmkmLocal::find($ownerId);

        if (!$owner) {
            $session->clear();
            return $this->errorResponse();
        }

        if ($messageLower === 'aktif') {
            $owner->update([
                'is_active' => true,
                'last_toggle_at' => now(),
            ]);
            $newStatus = 'AKTIF';
        } elseif ($messageLower === 'nonaktif') {
            $owner->update([
                'is_active' => false,
                'last_toggle_at' => now(),
            ]);
            $newStatus = 'NONAKTIF';
        } else {
            return [
                'success' => true,
                'intent' => 'owner_invalid_action',
                'reply' => "Pilihan tidak valid. Ketik *AKTIF*, *NONAKTIF*, atau *BATAL*.",
                'state_update' => 'WAITING_OWNER_ACTION',
            ];
        }

        $session->clear();

        return [
            'success' => true,
            'intent' => 'owner_toggled',
            'reply' => "✅ *STATUS DIPERBARUI*\n\n" .
                "📍 *{$owner->name}*\n" .
                "Status baru: *{$newStatus}*\n\n" .
                "Perubahan telah disimpan dan segera tayang di aplikasi web.",
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
