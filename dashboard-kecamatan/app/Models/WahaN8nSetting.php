<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WahaN8nSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        // WAHA
        'waha_api_url',
        'waha_api_key',
        'waha_session_name',
        'waha_webhook_url',

        // n8n
        'n8n_api_url',
        'n8n_api_key',
        'n8n_webhook_url',

        // Active provider selector
        'active_provider',

        // Fonnte
        'fonnte_token',
        'fonnte_device',

        // UltraMsg
        'ultramsg_instance_id',
        'ultramsg_token',

        // Generic HTTP
        'generic_http_url',
        'generic_http_headers',
        'generic_http_phone_field',
        'generic_http_message_field',
        'generic_http_extra_body',

        // Status & Bot
        'is_waha_connected',
        'is_n8n_connected',
        'last_connection_check',
        'connection_details',
        'bot_enabled',
        'bot_number',
        'bot_status',
        'qr_code',
    ];

    protected $casts = [
        'is_waha_connected'       => 'boolean',
        'is_n8n_connected'        => 'boolean',
        'bot_enabled'             => 'boolean',
        'connection_details'      => 'array',
        'last_connection_check'   => 'datetime',
        'generic_http_headers'    => 'array',
        'generic_http_extra_body' => 'array',
    ];

    /**
     * Cache key for settings
     */
    protected static string $cacheKey = 'waha_n8n_settings';

    /**
     * Get settings from cache or database
     */
    public static function getSettings(): ?self
    {
        return Cache::remember(self::$cacheKey, 3600, function () {
            return self::first();
        });
    }

    /**
     * Clear settings cache
     */
    public static function clearCache(): void
    {
        Cache::forget(self::$cacheKey);
    }

    /**
     * Check WAHA connection status
     */
    public function checkWahaConnection(): array
    {
        if (!$this->waha_api_url) {
            return [
                'success' => false,
                'message' => 'WAHA API URL tidak dikonfigurasi',
                'status' => 'not_configured',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->get("{$this->waha_api_url}/api/sessions/{$this->waha_session_name}");

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? 'UNKNOWN';

                $isConnected = in_array($status, ['WORKING', 'CONNECTED', 'ONLINE']);

                $this->update([
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
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    /**
     * Check n8n connection status
     */
    public function checkN8nConnection(): array
    {
        if (!$this->n8n_api_url) {
            return [
                'success' => false,
                'message' => 'n8n API URL tidak dikonfigurasi',
                'status' => 'not_configured',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getN8nHeaders())
                ->get("{$this->n8n_api_url}/api/v1/workflows");

            if ($response->successful()) {
                $data = $response->json();

                $this->update([
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
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'status' => 'error',
            ];
        }
    }

    /**
     * Get QR code for WhatsApp connection
     */
    public function getQrCode(): ?array
    {
        if (!$this->waha_api_url) {
            return null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->get("{$this->waha_api_url}/api/sessions/{$this->waha_session_name}/qr");

            if ($response->successful()) {
                $data = $response->json();

                // Store QR code
                $this->update([
                    'qr_code' => $data['qr'] ?? null,
                    'bot_status' => 'qr_required',
                ]);

                return $data;
            }
        } catch (\Exception $e) {
            // Log error
        }

        return null;
    }

    /**
     * Start WAHA session
     */
    public function startSession(): array
    {
        if (!$this->waha_api_url) {
            return [
                'success' => false,
                'message' => 'WAHA API URL tidak dikonfigurasi',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->post("{$this->waha_api_url}/api/sessions/start", [
                    'session' => $this->waha_session_name,
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
        if (!$this->waha_api_url) {
            return [
                'success' => false,
                'message' => 'WAHA API URL tidak dikonfigurasi',
            ];
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getWahaHeaders())
                ->post("{$this->waha_api_url}/api/sessions/{$this->waha_session_name}/logout");

            if ($response->successful()) {
                $this->update([
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

        if ($this->waha_api_key) {
            $headers['X-Api-Key'] = $this->waha_api_key;
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

        if ($this->n8n_api_key) {
            $headers['X-N8N-API-KEY'] = $this->n8n_api_key;
        }

        return $headers;
    }

    /**
     * Get formatted bot number
     */
    public function getFormattedBotNumber(): ?string
    {
        if (!$this->bot_number) {
            return null;
        }

        // Remove non-numeric characters
        return preg_replace('/[^0-9]/', '', $this->bot_number);
    }

    /**
     * Get WhatsApp link for bot
     */
    public function getWhatsappLink(string $text = "Halo, saya butuh informasi."): string
    {
        $number = $this->getFormattedBotNumber();
        if (!$number) {
            return '#';
        }

        return "https://wa.me/{$number}?text=" . urlencode($text);
    }

    /**
     * Check if bot is fully operational
     */
    public function isBotOperational(): bool
    {
        return $this->bot_enabled
            && $this->is_waha_connected
            && $this->bot_status === 'connected';
    }

    /**
     * Get the active provider key (default: 'waha')
     */
    public function getActiveProvider(): string
    {
        return $this->active_provider ?? 'waha';
    }

    /**
     * Get human-readable label for the active provider
     */
    public function getActiveProviderLabel(): string
    {
        return \App\Services\WhatsApp\WhatsAppManager::supportedProviders()[$this->getActiveProvider()] ?? 'WAHA';
    }

    /**
     * Whether the active provider is WAHA
     */
    public function isWahaActive(): bool
    {
        return $this->getActiveProvider() === 'waha';
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadge(): string
    {
        return match ($this->bot_status) {
            'connected' => '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Terhubung</span>',
            'qr_required' => '<span class="badge bg-warning"><i class="fas fa-qrcode me-1"></i>QR Diperlukan</span>',
            'disconnected' => '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Terputus</span>',
            default => '<span class="badge bg-secondary"><i class="fas fa-question-circle me-1"></i>Tidak Diketahui</span>',
        };
    }
}