<?php

namespace App\Services\WhatsApp\Providers;

use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Generic HTTP Provider — works with ANY REST WhatsApp gateway.
 *
 * Admin configures:
 *   - Endpoint URL (POST)
 *   - Auth headers (JSON: {"Authorization":"Bearer xxx"})
 *   - Phone field name  (e.g. "target", "to", "phone")
 *   - Message field name (e.g. "message", "body", "text")
 *   - Extra body fields (JSON: {"countryCode":"62"})
 */
class GenericHttpProvider implements WhatsAppProviderInterface
{
    protected string  $url;
    protected array   $extraHeaders;
    protected string  $phoneField;
    protected string  $messageField;
    protected array   $extraBody;

    public function __construct(
        string $url,
        array  $extraHeaders  = [],
        string $phoneField    = 'target',
        string $messageField  = 'message',
        array  $extraBody     = []
    ) {
        $this->url           = rtrim($url, '/');
        $this->extraHeaders  = $extraHeaders;
        $this->phoneField    = $phoneField ?: 'target';
        $this->messageField  = $messageField ?: 'message';
        $this->extraBody     = $extraBody;
    }

    public function getName(): string
    {
        return 'Generic HTTP';
    }

    public function getProviderType(): string
    {
        return 'generic_http';
    }

    public function sendMessage(string $phone, string $message): array
    {
        try {
            $phone = $this->normalizePhone($phone);

            $body = array_merge($this->extraBody, [
                $this->phoneField   => $phone,
                $this->messageField => $message,
            ]);

            $response = Http::timeout(30)
                ->withHeaders(array_merge(
                    ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
                    $this->extraHeaders
                ))
                ->post($this->url, $body);

            if ($response->successful()) {
                Log::info('[GenericHTTP] Message sent', ['phone' => $phone, 'url' => $this->url]);
                return ['success' => true, 'message' => 'Pesan berhasil dikirim', 'data' => $response->json()];
            }

            Log::error('[GenericHTTP] Send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return ['success' => false, 'message' => 'HTTP ' . $response->status() . ': ' . $response->body()];
        } catch (\Exception $e) {
            Log::error('[GenericHTTP] Exception', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function checkConnection(): array
    {
        // Generic provider has no standard health endpoint.
        // We do a dry-run HEAD/GET to the configured URL to see if it's alive.
        try {
            $response = Http::timeout(8)
                ->withHeaders(array_merge(
                    ['Accept' => 'application/json'],
                    $this->extraHeaders
                ))
                ->get($this->url);

            // Any response (even 4xx) means the server is reachable.
            $alive = $response->status() < 500;

            return [
                'success' => $alive,
                'message' => $alive
                    ? "Server merespons (HTTP {$response->status()})"
                    : "Server error (HTTP {$response->status()})",
                'status'  => $alive ? 'reachable' : 'error',
            ];
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
