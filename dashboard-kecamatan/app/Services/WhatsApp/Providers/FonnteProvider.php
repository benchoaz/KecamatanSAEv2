<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fonnte — popular Indonesian WhatsApp gateway.
 * Docs: https://fonnte.com/docs
 */
class FonnteProvider implements WhatsAppProviderInterface
{
    protected const SEND_URL  = 'https://api.fonnte.com/send';
    protected const CHECK_URL = 'https://api.fonnte.com/device';

    protected string $token;
    protected ?string $deviceId;

    public function __construct(string $token, ?string $deviceId = null)
    {
        $this->token    = $token;
        $this->deviceId = $deviceId;
    }

    public function getName(): string
    {
        return 'Fonnte';
    }

    public function getProviderType(): string
    {
        return 'fonnte';
    }

    public function sendMessage(string $phone, string $message): array
    {
        try {
            $phone = $this->normalizePhone($phone);

            $body = [
                'target'      => $phone,
                'message'     => $message,
                'countryCode' => '62',
            ];

            if ($this->deviceId) {
                $body['device'] = $this->deviceId;
            }

            $response = Http::timeout(30)
                ->withHeaders(['Authorization' => $this->token])
                ->post(self::SEND_URL, $body);

            if ($response->successful()) {
                $data = $response->json();
                // Fonnte returns {"status":true/false,"detail":"..."}
                $ok = (bool) ($data['status'] ?? false);
                if ($ok) {
                    Log::info('[Fonnte] Message sent', ['phone' => $phone]);
                    return ['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $data];
                }
                return ['success' => false, 'message' => $data['detail'] ?? 'Gagal kirim'];
            }

            Log::error('[Fonnte] Send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'HTTP ' . $response->status() . ': ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('[Fonnte] Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function checkConnection(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders(['Authorization' => $this->token])
                ->post(self::CHECK_URL, []);

            if ($response->successful()) {
                $data = $response->json();
                $ok   = (bool) ($data['status'] ?? false);
                return [
                    'success' => $ok,
                    'message' => $ok ? 'Fonnte terhubung' : ($data['detail'] ?? 'Tidak terhubung'),
                    'status'  => $ok ? 'connected' : 'error',
                    'data'    => $data,
                ];
            }

            return ['success' => false, 'message' => 'HTTP ' . $response->status(), 'status' => 'error'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 'error'];
        }
    }

    // ──────────────────────────────────────────────────────────────────────────

    protected function normalizePhone(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            return '62' . substr($clean, 1);
        }
        if (!str_starts_with($clean, '62')) {
            return '62' . $clean;
        }
        return $clean;
    }
}
