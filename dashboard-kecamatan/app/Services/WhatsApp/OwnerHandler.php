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
        $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
        
        return [
            'success' => true,
            'intent' => 'owner_portal_link',
            'reply' => "🔐 *Akses Pusat Kendali Warga*\n\n" .
                "Sistem keamanan PIN manual telah kami **hapus** untuk mempermudah Anda.\n" .
                "Kini, Anda dapat memperbarui status Jasa maupun mengelola produk UMKM secara langsung dengan sistem aman tanpa sandi.\n\n" .
                "Silakan klik Portal Warga di bawah ini:\n" .
                "🌐 {$baseUrl}/portal-warga/masuk\n\n" .
                "Ketik MENU untuk kembali.",
            'state_update' => null,
        ];
    }

    /**
     * Handle Forgot PIN - Redirect to PIN-less portal
     */
    public function handleForgotOwnerPin(string $phone): array
    {
        $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));

        return [
            'success' => true,
            'intent' => 'owner_lupa_pin',
            'reply' => "🔐 *Portal Warga Tanpa Sandi*\n\n" .
                "Sistem kami sekarang tidak lagi menggunakan PIN untuk akses manajemen.\n\n" .
                "Anda dapat mengelola data secara aman melalui tautan pribadi di bawah ini:\n" .
                "🌐 {$baseUrl}/portal-warga/masuk\n\n" .
                "Ketik MENU untuk kembali.",
            'state_update' => null,
        ];
    }

    /**
     * Quick toggle holiday status for all assets owned by this phone
     */
    public function toggleHolidayStatus(string $phone, string $action): array
    {
        $action = strtolower($action);
        $isHoliday = ($action === 'libur');
        $newStatusLabel = $isHoliday ? 'DILIBURKAN 🔴' : 'DIBUKA KEMBALI 🟢';

        // Normalize phone for searching
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        $basePhone = $cleanPhone;
        if (str_starts_with($cleanPhone, '62')) {
            $basePhone = substr($cleanPhone, 2);
        } elseif (str_starts_with($cleanPhone, '0')) {
            $basePhone = substr($cleanPhone, 1);
        }
        $likeClause = '%' . ltrim($basePhone, '0') . '%';

        // Update all related assets
        $umkmCount = \App\Models\Umkm::where('no_wa', 'like', $likeClause)->update(['is_on_holiday' => $isHoliday]);
        $jasaCount = \App\Models\WorkDirectory::where('contact_phone', 'like', $likeClause)->update(['is_on_holiday' => $isHoliday]);
        $localCount = \App\Models\UmkmLocal::where('contact_wa', 'like', $likeClause)->update(['is_on_holiday' => $isHoliday]);

        $total = $umkmCount + $jasaCount + $localCount;

        if ($total === 0) {
            return [
                'success' => true,
                'intent' => 'owner_not_found',
                'reply' => "Maaf, nomor WhatsApp Anda (+{$phone}) belum terdaftar sebagai pemilik UMKM atau Jasa di database kami.\n\nSilakan daftar terlebih dahulu di menu *KELOLA PROFIL*.",
                'state_update' => null,
            ];
        }

        $reply = "✅ *STATUS BERHASIL DIUBAH*\n\n";
        $reply .= "Seluruh layanan/toko Anda telah {$newStatusLabel}.\n";
        
        if ($isHoliday) {
            $reply .= "\nStatus [LIBUR] akan tampil di hasil pencarian warga agar mereka tahu Anda sedang tidak melayani.";
        } else {
            $reply .= "\nStatus [Lagi Buka] akan tampil kembali di hasil pencarian warga.";
        }

        $reply .= "\n\nKetik *MENU* untuk kembali.";

        return [
            'success' => true,
            'intent' => 'owner_holiday_toggled',
            'reply' => $reply,
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
