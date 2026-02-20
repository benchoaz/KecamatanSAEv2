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
            'MENU_ADMIN' => $this->handleMenuAdmin($session, $messageLower),
            'MENU_EKONOMI' => $this->handleMenuEkonomi($session, $messageLower),
            'MENU_JASA' => $this->jasaHandler->search($message),
            'WAITING_COMPLAINT_MESSAGE' => $this->complaintHandler->handleMessage($session, $message),
            'WAITING_COMPLAINT_CONFIRM' => $this->complaintHandler->handleConfirmation($session, $message),
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
            return $this->syaratHandler->search(''); // Show category list
        }

        if ($this->isSelection($message, '2')) {
            return $this->statusHandler->handle($session->phone);
        }

        return [
            'success' => true,
            'intent' => 'invalid_selection',
            'reply' => "Pilihan tidak valid. Silakan pilih:\n1️⃣ *Syarat*\n2️⃣ *Status*\n\nAtau ketik *MENU* untuk kembali.",
            'state_update' => 'MENU_ADMIN',
        ];
    }

    /**
     * Handle Ekonomi Sub-menu
     */
    protected function handleMenuEkonomi(WhatsappSession $session, string $message): array
    {
        if ($this->isSelection($message, '1')) {
            return [
                'success' => true,
                'intent' => 'umkm_prompt',
                'reply' => "🛍️ *CARI UMKM*\n\nKetik nama produk atau usaha yang Anda cari.\nContoh: _madu_, _keripik_, _bakso_\n\nKetik *MENU* untuk kembali.",
                'state_update' => 'WAITING_UMKM_SEARCH',
            ];
        }

        if ($this->isSelection($message, '2')) {
            return $this->lokerHandler->search(''); // Show latest jobs
        }

        // Handle specific states if needed, or fallback
        if ($session->state === 'WAITING_UMKM_SEARCH') {
            return $this->umkmHandler->search($message);
        }

        return [
            'success' => true,
            'intent' => 'invalid_selection',
            'reply' => "Pilihan tidak valid. Silakan pilih:\n1️⃣ *UMKM*\n2️⃣ *Loker*\n\nAtau ketik *MENU* untuk kembali.",
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
            '1' => '1️⃣',
            '2' => '2️⃣',
            '3' => '3️⃣',
            '4' => '4️⃣',
            '5' => '5️⃣',
        ];

        return isset($emojis[$number]) && $message === $emojis[$number];
    }
}
