<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;

class StateHandler
{
    protected ComplaintHandler $complaintHandler;
    protected OwnerHandler $ownerHandler;

    public function __construct(
        ComplaintHandler $complaintHandler,
        OwnerHandler $ownerHandler
    ) {
        $this->complaintHandler = $complaintHandler;
        $this->ownerHandler = $ownerHandler;
    }

    /**
     * Handle message based on current session state
     */
    public function handle(WhatsappSession $session, string $message): array
    {
        return match ($session->state) {
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
}
