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
    protected LokerHandler $lokerHandler;
    protected JasaHandler $jasaHandler;

    public function __construct(
        IntentHandler $intentHandler,
        ComplaintHandler $complaintHandler,
        OwnerHandler $ownerHandler,
        StatusHandler $statusHandler,
        SyaratHandler $syaratHandler,
        UmkmHandler $umkmHandler,
        LokerHandler $lokerHandler,
        JasaHandler $jasaHandler
    ) {
        $this->intentHandler = $intentHandler;
        $this->complaintHandler = $complaintHandler;
        $this->ownerHandler = $ownerHandler;
        $this->statusHandler = $statusHandler;
        $this->syaratHandler = $syaratHandler;
        $this->umkmHandler = $umkmHandler;
        $this->lokerHandler = $lokerHandler;
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
            str_starts_with($messageLower, 'syarat') ||
            str_starts_with($messageLower, 'umkm') ||
            str_starts_with($messageLower, 'jasa') ||
            str_starts_with($messageLower, 'loker') ||
            str_starts_with($messageLower, 'pengaduan') ||
            str_starts_with($messageLower, 'cek') ||
            str_starts_with($messageLower, 'status')
        ) {
            \Log::info('StateHandler Keyword Override Triggered', ['message' => $message]);
            $session->clear();
            return $this->intentHandler->handle($session->phone, $message);
        }

        return match ($session->state) {
            'ADM_SUBMENU', 'MENU_ADMIN' => $this->handleMenuAdmin($session, $messageLower),
            'MENU_EKONOMI' => $this->handleMenuEkonomi($session, $messageLower),
            'MENU_JASA' => $this->jasaHandler->search($message),
            'WAITING_UMKM_SEARCH' => $this->umkmHandler->search($message),
            'WAITING_LOKER_SEARCH' => $this->lokerHandler->search($message),
            // New simplified complaint flow - form link
            'WAITING_COMPLAINT_NAME' => $this->complaintHandler->handleName($session, $message),
            'WAITING_COMPLAINT_WA' => $this->complaintHandler->handleWhatsApp($session, $message),
            'WAITING_COMPLAINT_CATEGORY' => $this->complaintHandler->handleCategory($session, $message),
            'WAITING_OWNER_PIN' => $this->ownerHandler->handlePin($session, $message),
            'WAITING_OWNER_ACTION' => $this->ownerHandler->handleAction($session, $message),
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
                'reply' => "Cek Status Berkas\n\nSilakan masukkan PIN Lacak (6 angka) untuk melihat status berkas Anda.\n\nAtau ketik STATUS untuk melihat semua berkas Anda.\n\nKetik LUPA PIN jika Anda lupa PIN Lacak Anda.",
                'state_update' => 'WAITING_STATUS_PIN',
            ];
        }

        if ($this->isSelection($message, '2')) {
            // Show layanan/syarat link
            $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
            $layananUrl = $baseUrl . '/#layanan';
            return [
                'success' => true,
                'intent' => 'syarat_link',
                'reply' => "LAYANAN KECAMATAN\n\n" .
                    "Silakan pilih layanan yang dibutuhkan:\n\n" .
                    "- syarat ktp - Pembuatan KTP\n" .
                    "- syarat kk - Pembuatan KK\n" .
                    "- syarat akta - Akta Kelahiran\n" .
                    "- syarat sktm - SKTM\n" .
                    "- syarat domisili - Surat Domisili\n\n" .
                    "Ajukan Secara Online:\n" .
                    "{$layananUrl}\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => 'ADM_SUBMENU',
            ];
        }

        if ($this->isSelection($message, '3') || $message === 'menu' || $message === 'kembali') {
            // Go back to main menu
            $regionName = strtoupper(appProfile()->region_name ?? 'BESUK');
            $menu = "MENU LAYANAN KECAMATAN {$regionName}\n\n";
            $menu .= "Silakan pilih layanan (Ketik angka):\n\n";
            $menu .= "1. Administrasi - Syarat & Status Berkas\n";
            $menu .= "2. Loker & UMKM - Kerja & Produk Desa\n";
            $menu .= "3. Jasa - Cari Tukang/Servis\n";
            $menu .= "4. Pengaduan - Aspirasi Warga\n";
            $menu .= "5. Kelola Data - Aktif/Nonaktifkan Data Anda\n\n";
            $menu .= "Ketik MENU kapan saja untuk kembali.";
            return [
                'success' => true,
                'intent' => 'menu',
                'reply' => $menu,
                'state_update' => null,
            ];
        }

        // Check for lupa pin
        if (
            str_contains($message, 'lupa') ||
            str_contains($message, 'pin') ||
            str_contains($message, 'forgot')
        ) {
            return $this->statusHandler->handleForgotPin($session->phone);
        }

        return [
            'success' => true,
            'intent' => 'invalid_selection',
            'reply' => "Pilihan tidak valid. Silakan pilih:\n1. STATUS - Lacak Berkas\n2. SYARAT - Persyaratan Layanan\n3. MENU - Kembali\n\nAtau ketik LUPA PIN jika lupa PIN Lacak.",
            'state_update' => 'ADM_SUBMENU',
        ];
    }

    /**
     * Handle Ekonomi Sub-menu
     */
    protected function handleMenuEkonomi(WhatsappSession $session, string $message): array
    {
        if ($this->isSelection($message, '1')) {
            // Show links to Loker & Toko/Etalase
            $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
            return [
                'success' => true,
                'intent' => 'loker_etalase_link',
                'reply' => "LOKER & TOKO\n\n" .
                    "Pilih kategori:\n\n" .
                    "1. Loker - Lowongan Kerja\n" .
                    "   {$baseUrl}/loker\n\n" .
                    "2. Toko/Etalase - Produk Warga\n" .
                    "   {$baseUrl}/etalase\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        if ($this->isSelection($message, '2')) {
            // Show Loker link
            $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
            return [
                'success' => true,
                'intent' => 'loker_link',
                'reply' => "LOWONGAN KERJA (LOKER)\n\n" .
                    "Cari lowongan kerja warga:\n\n" .
                    "{$baseUrl}/loker\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        // Handle specific states if needed, or fallback
        if ($session->state === 'WAITING_UMKM_SEARCH') {
            return $this->umkmHandler->search($message);
        }

        return [
            'success' => true,
            'intent' => 'invalid_selection',
            'reply' => "Pilihan tidak valid. Silakan pilih:\n1. UMKM\n2. Loker\n\nAtau ketik *MENU* untuk kembali.",
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
