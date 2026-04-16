<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * UltraMsg — cloud WhatsApp gateway.
 * Docs: https://ultramsg.com/api
 */
class UltraMsgProvider implements WhatsAppProviderInterface
{
    protected string $instanceId;
    protected string $token;

    public function __construct(string $instanceId, string $token)
    {
        $this->instanceId = $instanceId;
        $this->token      = $token;
    }

    public function getName(): string
    {
        return 'UltraMsg';
    }

    public function getProviderType(): string
    {
        return 'ultramsg';
    }

    public function sendMessage(string $phone, string $message): array
    {
        try {
            $phone = $this->normalizePhone($phone);

            $response = Http::timeout(30)
                ->asForm()
                ->post("https://api.ultramsg.com/{$this->instanceId}/messages/chat", [
                    'token'   => $this->token,
                    'to'      => $phone,
                    'body'    => $message,
                    'msgtype' => 'text',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                // UltraMsg returns {"sent":"true",...} or {"error":"..."}
                $ok = isset($data['sent']) && $data['sent'] === 'true';
                if ($ok) {
                    Log::info('[UltraMsg] Message sent', ['phone' => $phone]);
                    return ['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $data];
                }
                return ['success' => false, 'message' => $data['error'] ?? 'Gagal kirim pesan'];
            }

            Log::error('[UltraMsg] Send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'HTTP ' . $response->status() . ': ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('[UltraMsg] Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function checkConnection(): array
    {
        try {
            $response = Http::timeout(10)
                ->get("https://api.ultramsg.com/{$this->instanceId}/instance/status", [
                    'token' => $this->token,
                ]);

            if ($response->successful()) {
                $data   = $response->json();
                $status = $data['status']['accountStatus']['status'] ?? 'unknown';
                $ok     = $status === 'authenticated';

                return [
                    'success' => $ok,
                    'message' => $ok ? 'UltraMsg terhubung' : "Status: {$status}",
                    'status'  => $ok ? 'connected' : 'disconnected',
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
            return '+62' . substr($clean, 1);
        }
        if (str_starts_with($clean, '62')) {
            return '+' . $clean;
        }
        return '+62' . $clean;
    }
}
