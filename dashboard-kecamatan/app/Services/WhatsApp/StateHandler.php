<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;

class StateHandler
{
    protected IntentHandler $intentHandler;
    protected ComplaintHandler $complaintHandler;
    protected OwnerHandler $ownerHandler;
    protected StatusHandler $statusHandler;
    protected SyaratHandler $syaratHandler;
    protected UmkmHandler $umkmHandler;

    protected JasaHandler $jasaHandler;

    public function __construct(
        IntentHandler $intentHandler,
        ComplaintHandler $complaintHandler,
        OwnerHandler $ownerHandler,
        StatusHandler $statusHandler,
        SyaratHandler $syaratHandler,
        UmkmHandler $umkmHandler,
        JasaHandler $jasaHandler
    ) {
        $this->intentHandler = $intentHandler;
        $this->complaintHandler = $complaintHandler;
        $this->ownerHandler = $ownerHandler;
        $this->statusHandler = $statusHandler;
        $this->syaratHandler = $syaratHandler;
        $this->umkmHandler = $umkmHandler;
        $this->jasaHandler = $jasaHandler;
    }

    /**
     * Handle message based on current session state
     */
    public function handle(WhatsappSession $session, string $message): array
    {
        $messageLower = strtolower(trim($message));

        // Global commands & Keyword Overrides
        if (
            $messageLower === 'menu' ||
            $messageLower === 'kembali' ||
            $messageLower === '0' ||
            str_starts_with($messageLower, 'syarat') ||
            str_starts_with($messageLower, 'umkm') ||
            str_starts_with($messageLower, 'jasa') ||
            str_starts_with($messageLower, 'pengaduan') ||
            str_starts_with($messageLower, 'cek') ||
            str_starts_with($messageLower, 'status') ||
            preg_match('/^[0-9]{6}$/', $messageLower)
        ) {
            \Log::info('StateHandler Keyword Override Triggered', ['message' => $message]);
            
            // If it's a numeric override for "Back" (0) or "KEMBALI", ensure we go to menu
            if ($messageLower === '0' || $messageLower === 'kembali') {
                $message = 'menu';
            }

            $session->clear();
            return $this->intentHandler->handle($session->phone, $message);
        }

        return match ($session->state) {
            'ADM_SUBMENU', 'MENU_ADMIN' => $this->handleMenuAdmin($session, $messageLower),
            'MENU_EKONOMI' => $this->handleMenuEkonomi($session, $messageLower),
            'MENU_JASA' => $this->jasaHandler->search($message),
            'WAITING_UMKM_SEARCH' => $this->umkmHandler->search($message),
            // New simplified complaint flow - form link
            'WAITING_COMPLAINT_NAME' => $this->complaintHandler->handleName($session, $message),
            'WAITING_COMPLAINT_WA' => $this->complaintHandler->handleWhatsApp($session, $message),
            'WAITING_COMPLAINT_CATEGORY' => $this->complaintHandler->handleCategory($session, $message),
            'WAITING_STATUS_PIN' => $this->statusHandler->handle($session->phone, $message),
            default => [
                'success' => true,
                'intent' => 'state_expired',
                'reply' => 'Sesi Anda telah berakhir. Ketik *MENU* untuk memulai lagi.',
                'state_update' => null,
            ],
        };
    }

    /**
     * Handle Administrasi Sub-menu
     */
    protected function handleMenuAdmin(WhatsappSession $session, string $message): array
    {
        if ($this->isSelection($message, '1')) {
            // Show status check
            return [
                'success' => true,
                'intent' => 'status',
                'reply' => "🏛️ *CEK STATUS BERKAS*\n\n" .
                    "Silakan masukkan **PIN Lacak** (6 angka) untuk melihat perkembangan berkas Anda.\n\n" .
                    "💡 *Tips:* Jika Anda lupa PIN, silakan ketik **LUPA**.\n\n" .
                    "Ketik **MENU** atau **0** untuk kembali.",
                'state_update' => 'WAITING_STATUS_PIN',
            ];
        }

        if ($this->isSelection($message, '2')) {
            // Show layanan/syarat link
            return $this->intentHandler->getLayananLink();
        }

        if ($this->isSelection($message, '3') || $message === 'menu' || $message === 'kembali' || $message === '0') {
            $session->clear();
            return $this->intentHandler->handle($session->phone, 'menu');
        }

        // Check for lupa pin
        if (
            str_contains($message, 'lupa') ||
            str_contains($message, 'forgot')
        ) {
            return $this->statusHandler->handleForgotPin($session->phone);
        }

        return [
            'success' => true,
            'intent' => 'invalid_selection',
            'reply' => "⚠️ *Pilihan tidak valid.*\n\n" .
                "Silakan pilih:\n" .
                "1. STATUS - Lacak Berkas\n" .
                "2. SYARAT - Persyaratan Layanan\n" .
                "3. MENU - Kembali ke Menu Utama\n\n" .
                "Ketik angka *1*, *2*, atau *3*.",
            'state_update' => 'ADM_SUBMENU',
        ];
    }

    /**
     * Handle Ekonomi Sub-menu
     */
    protected function handleMenuEkonomi(WhatsappSession $session, string $message): array
    {
        $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://localhost'));

        if ($this->isSelection($message, '1')) {
            return [
                'success' => true,
                'intent' => 'umkm_link',
                'reply' => "🛒 *Etalase Produk UMKM*\n\n" .
                    "Temukan produk unggulan karya warga sekitar:\n" .
                    "{$baseUrl}/ekonomi?tab=produk\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        if ($this->isSelection($message, '2')) {
            return [
                'success' => true,
                'intent' => 'jasa_link',
                'reply' => "🔧 *Direktori Jasa & Tenaga Ahli*\n\n" .
                    "Temukan tukang, tenaga harian, dan penyedia jasa:\n" .
                    "{$baseUrl}/ekonomi?tab=jasa\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        if ($this->isSelection($message, '3') || $message === 'menu' || $message === 'kembali') {
            $session->clear();
            return $this->intentHandler->handle($session->phone, 'menu');
        }

        return [
            'success' => true,
            'intent' => 'invalid_selection',
            'reply' => "⚠️ *Pilihan tidak valid.*\n\n" .
                "Silakan pilih nomor di bawah ini:\n" .
                "1. Produk UMKM\n" .
                "2. Jasa & Tenaga Ahli\n" .
                "3. MENU - Kembali ke Menu Utama\n\n" .
                "Atau ketik *MENU* kapan saja.",
            'state_update' => 'MENU_EKONOMI',
        ];
    }

    /**
     * Map basic numeric strings to technical emojis often sent by WA
     */
    protected function isSelection(string $message, string $number): bool
    {
        $message = trim($message);

        // Pure numeric match
        if ($message === $number)
            return true;

        // Emoji match mapping
        $emojis = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
        ];

        return isset($emojis[$number]) && $message === $emojis[$number];
    }
}
