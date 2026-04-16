<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaProvider implements WhatsAppProviderInterface
{
    protected string $apiUrl;
    protected ?string $apiKey;
    protected string $session;

    public function __construct(string $apiUrl, ?string $apiKey = null, string $session = 'default')
    {
        $this->apiUrl  = rtrim($apiUrl, '/');
        $this->apiKey  = $apiKey;
        $this->session = $session ?: 'default';
    }

    public function getName(): string
    {
        return 'WAHA (Self-hosted)';
    }

    public function getProviderType(): string
    {
        return 'waha';
    }

    public function sendMessage(string $phone, string $message): array
    {
        try {
            $phone = $this->normalizePhone($phone);

            $response = Http::timeout(30)
                ->withHeaders($this->headers())
                ->post("{$this->apiUrl}/api/sendText", [
                    'session' => $this->session,
                    'chatId'  => "{$phone}@c.us",
                    'text'    => $message,
                ]);

            if ($response->successful()) {
                Log::info('[WAHA] Message sent', ['phone' => $phone]);
                return ['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $response->json()];
            }

            Log::error('[WAHA] Send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'Gagal kirim: ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('[WAHA] Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function checkConnection(): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->headers())
                ->get("{$this->apiUrl}/api/sessions/{$this->session}");

            if ($response->successful()) {
                $data   = $response->json();
                $status = $data['status'] ?? 'UNKNOWN';
                $ok     = in_array($status, ['WORKING', 'CONNECTED', 'ONLINE']);

                return [
                    'success' => $ok,
                    'message' => $ok ? 'WAHA terhubung' : "Status: {$status}",
                    'status'  => $status,
                    'data'    => $data,
                ];
            }

            return ['success' => false, 'message' => 'HTTP ' . $response->status(), 'status' => 'error'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'status' => 'error'];
        }
    }

    // ──────────────────────────────────────────────────────────────────────────

    protected function headers(): array
    {
        $h = ['Accept' => 'application/json', 'Content-Type' => 'application/json'];
        if ($this->apiKey) {
            $h['X-Api-Key'] = $this->apiKey;
        }
        return $h;
    }

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
