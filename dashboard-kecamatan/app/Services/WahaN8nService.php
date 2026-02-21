<?php

namespace App\Services;

use App\Models\WahaN8nSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WahaN8nService
{
    protected ?WahaN8nSetting $settings;
    protected string $cacheKey = 'waha_n8n_settings';

    public function __construct()
    {
        $this->settings = $this->getSettings();
    }

    /**
     * Get settings from cache or database
     */
    public function getSettings(): ?WahaN8nSetting
    {
        return Cache::remember($this->cacheKey, 3600, function () {
            return WahaN8nSetting::first();
        });
    }

    /**
     * Clear settings cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        $this->settings = $this->getSettings();
    }

    /**
     * Check if bot is operational
     */
    public function isBotOperational(): bool
    {
        return $this->settings?->isBotOperational() ?? false;
    }

    /**
     * Check WAHA connection
     */
    public function checkWahaConnection(): array
    {
        if (!$this->settings || !$this->settings->waha_api_url) {
            return [
                'success' => false,
                'message' => 'WAHA API URL tidak dikonfigurasi',
                'status' => 'not_configured',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->get("{$this->settings->waha_api_url}/api/sessions/{$this->settings->waha_session_name}");

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? 'UNKNOWN';

                $isConnected = in_array($status, ['WORKING', 'CONNECTED', 'ONLINE']);

                $this->settings->update([
                    'is_waha_connected' => $isConnected,
                    'bot_status' => $isConnected ? 'connected' : ($status === 'SCAN_QR_CODE' ? 'qr_required' : 'disconnected'),
                    'last_connection_check' => now(),
                    'connection_details' => $data,
                ]);

                return [
                    'success' => true,
                    'message' => $isConnected ? 'WAHA terhubung' : "Status: {$status}",
                    'status' => $status,
                    'data' => $data,
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghubungi WAHA: ' . $response->status(),
                'status' => 'error',
            ];
        } catch (\Exception $e) {
            Log::error('WAHA connection check failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    /**
     * Check n8n connection
     */
    public function checkN8nConnection(): array
    {
        if (!$this->settings || !$this->settings->n8n_api_url) {
            return [
                'success' => false,
                'message' => 'n8n API URL tidak dikonfigurasi',
                'status' => 'not_configured',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getN8nHeaders())
                ->get("{$this->settings->n8n_api_url}/api/v1/workflows");

            if ($response->successful()) {
                $data = $response->json();

                $this->settings->update([
                    'is_n8n_connected' => true,
                    'last_connection_check' => now(),
                ]);

                return [
                    'success' => true,
                    'message' => 'n8n terhubung',
                    'status' => 'connected',
                    'workflow_count' => count($data['data'] ?? []),
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal menghubungi n8n: ' . $response->status(),
                'status' => 'error',
            ];
        } catch (\Exception $e) {
            Log::error('n8n connection check failed', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    /**
     * Send WhatsApp message via WAHA
     */
    public function sendMessage(string $phone, string $message): array
    {
        if (!$this->settings || !$this->settings->is_waha_connected) {
            return [
                'success' => false,
                'message' => 'WAHA tidak terhubung',
            ];
        }

        try {
            // Clean phone number
            $phone = preg_replace('/[^0-9]/', '', $phone);

            $response = Http::timeout(30)
                ->withHeaders($this->getWahaHeaders())
                ->post("{$this->settings->waha_api_url}/api/sendText", [
                    'session' => $this->settings->waha_session_name,
                    'chatId' => "{$phone}@c.us",
                    'text' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent', ['phone' => $phone]);

                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim',
                    'data' => $response->json(),
                ];
            }

            Log::error('Failed to send WhatsApp message', [
                'phone' => $phone,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal mengirim pesan: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Exception sending WhatsApp message', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Trigger n8n webhook
     */
    public function triggerN8nWebhook(array $data): array
    {
        if (!$this->settings || !$this->settings->n8n_webhook_url) {
            return [
                'success' => false,
                'message' => 'n8n webhook URL tidak dikonfigurasi',
            ];
        }

        try {
            $response = Http::timeout(30)
                ->post($this->settings->n8n_webhook_url, $data);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Webhook berhasil dipicu',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memicu webhook: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Exception triggering n8n webhook', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get QR code for WhatsApp connection
     */
    public function getQrCode(): ?array
    {
        if (!$this->settings || !$this->settings->waha_api_url) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->get("{$this->settings->waha_api_url}/api/sessions/{$this->settings->waha_session_name}/qr");

            if ($response->successful()) {
                $data = $response->json();

                $this->settings->update([
                    'qr_code' => $data['qr'] ?? null,
                    'bot_status' => 'qr_required',
                ]);

                return $data;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get QR code', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Start WAHA session
     */
    public function startSession(): array
    {
        if (!$this->settings || !$this->settings->waha_api_url) {
            return [
                'success' => false,
                'message' => 'WAHA API URL tidak dikonfigurasi',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->post("{$this->settings->waha_api_url}/api/sessions/start", [
                    'session' => $this->settings->waha_session_name,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Session berhasil dimulai',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal memulai session: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to start WAHA session', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Logout from WhatsApp session
     */
    public function logoutSession(): array
    {
        if (!$this->settings || !$this->settings->waha_api_url) {
            return [
                'success' => false,
                'message' => 'WAHA API URL tidak dikonfigurasi',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->post("{$this->settings->waha_api_url}/api/sessions/{$this->settings->waha_session_name}/logout");

            if ($response->successful()) {
                $this->settings->update([
                    'is_waha_connected' => false,
                    'bot_status' => 'disconnected',
                    'qr_code' => null,
                ]);

                return [
                    'success' => true,
                    'message' => 'Session berhasil logout',
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal logout: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to logout WAHA session', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get WAHA API headers
     */
    protected function getWahaHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->settings && $this->settings->waha_api_key) {
            $headers['X-Api-Key'] = $this->settings->waha_api_key;
        }

        return $headers;
    }

    /**
     * Get n8n API headers
     */
    protected function getN8nHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->settings && $this->settings->n8n_api_key) {
            $headers['X-N8N-API-KEY'] = $this->settings->n8n_api_key;
        }

        return $headers;
    }

    /**
     * Get bot number
     */
    public function getBotNumber(): ?string
    {
        return $this->settings?->bot_number;
    }

    /**
     * Get formatted bot number
     */
    public function getFormattedBotNumber(): ?string
    {
        return $this->settings?->getFormattedBotNumber();
    }

    /**
     * Get WhatsApp link for bot
     */
    public function getWhatsappLink(string $text = "Halo, saya butuh informasi."): string
    {
        return $this->settings?->getWhatsappLink($text) ?? '#';
    }

    /**
     * Get bot status
     */
    public function getBotStatus(): string
    {
        return $this->settings?->bot_status ?? 'disconnected';
    }

    /**
     * Get connection status summary
     */
    public function getConnectionSummary(): array
    {
        return [
            'waha_connected' => $this->settings?->is_waha_connected ?? false,
            'n8n_connected' => $this->settings?->is_n8n_connected ?? false,
            'bot_enabled' => $this->settings?->bot_enabled ?? false,
            'bot_status' => $this->settings?->bot_status ?? 'disconnected',
            'bot_operational' => $this->isBotOperational(),
            'last_check' => $this->settings?->last_connection_check?->diffForHumans(),
        ];
    }
}