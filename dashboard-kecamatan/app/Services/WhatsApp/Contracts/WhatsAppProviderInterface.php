<?php

namespace App\Services\WhatsApp\Contracts;

interface WhatsAppProviderInterface
{
    /**
     * Send a WhatsApp message.
     *
     * @param  string  $phone  Recipient phone number (any format — will be normalised inside)
     * @param  string  $message  Message body
     * @return array{success: bool, message: string, data?: mixed}
     */
    public function sendMessage(string $phone, string $message): array;

    /**
     * Test the connection / credentials for this provider.
     *
     * @return array{success: bool, message: string, status: string, data?: mixed}
     */
    public function checkConnection(): array;

    /**
     * Human-readable name shown in the dashboard (e.g. "WAHA", "Fonnte").
     */
    public function getName(): string;

    /**
     * Machine-readable key used in the database (e.g. "waha", "fonnte").
     */
    public function getProviderType(): string;
}
