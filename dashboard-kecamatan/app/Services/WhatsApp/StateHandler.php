<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;

class StateHandler
{
    protected ComplaintHandler $complaintHandler;
    protected OwnerHandler $ownerHandler;
    protected StatusHandler $statusHandler;
    protected SyaratHandler $syaratHandler;
    protected UmkmHandler $umkmHandler;
    protected LokerHandler $lokerHandler;
    protected JasaHandler $jasaHandler;

    public function __construct(
        ComplaintHandler $complaintHandler,
        OwnerHandler $ownerHandler,
        StatusHandler $statusHandler,
        SyaratHandler $syaratHandler,
        UmkmHandler $umkmHandler,
        LokerHandler $lokerHandler,
        JasaHandler $jasaHandler
    ) {
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

        // Global commands
        if ($messageLower === 'menu') {
            $session->clear();
            return (new IntentHandler(
                $this->statusHandler,
                $this->syaratHandler,
                $this->umkmHandler,
                $this->jasaHandler,
                $this->lokerHandler,
                $this->complaintHandler,
                $this->ownerHandler
            ))->handle($session->phone, 'menu');
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
        if ($message === '1') {
            return $this->syaratHandler->search(''); // Show category list
        }

        if ($message === '2') {
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
        if ($message === '1') {
            return [
                'success' => true,
                'intent' => 'umkm_prompt',
                'reply' => "🛍️ *CARI UMKM*\n\nKetik nama produk atau usaha yang Anda cari.\nContoh: _madu_, _keripik_, _bakso_\n\nKetik *MENU* untuk kembali.",
                'state_update' => 'WAITING_UMKM_SEARCH',
            ];
        }

        if ($message === '2') {
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
}
