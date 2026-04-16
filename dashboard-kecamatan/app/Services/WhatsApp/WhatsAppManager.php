<?php

namespace App\Services\WhatsApp;

use App\Models\WahaN8nSetting;
use App\Services\WhatsApp\Contracts\WhatsAppProviderInterface;
use App\Services\WhatsApp\Providers\FonnteProvider;
use App\Services\WhatsApp\Providers\GenericHttpProvider;
use App\Services\WhatsApp\Providers\UltraMsgProvider;
use App\Services\WhatsApp\Providers\WahaProvider;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp Manager / Factory
 *
 * Usage (anywhere in the app):
 *   WhatsAppManager::driver()->sendMessage($phone, $message);
 *   WhatsAppManager::driver()->checkConnection();
 *   WhatsAppManager::make('fonnte')->sendMessage(...);
 */
class WhatsAppManager
{
    /**
     * Build the provider for the currently-active setting.
     */
    public static function driver(): WhatsAppProviderInterface
    {
        $settings = WahaN8nSetting::getSettings();

        if (! $settings) {
            // No settings saved yet → fall back to WAHA using .env values
            return new WahaProvider(
                config('services.waha.url', 'http://localhost:3000'),
                config('services.waha.api_key'),
                config('services.waha.session', 'default'),
            );
        }

        return self::make($settings->active_provider ?? 'waha', $settings);
    }

    /**
     * Build a specific provider (regardless of active setting).
     *
     * @param  string  $type  'waha' | 'fonnte' | 'ultramsg' | 'generic_http'
     * @param  WahaN8nSetting|null  $settings  Loaded settings (avoids extra DB query)
     */
    public static function make(string $type, ?WahaN8nSetting $settings = null): WhatsAppProviderInterface
    {
        $settings = $settings ?? WahaN8nSetting::getSettings();

        return match ($type) {
            'fonnte'       => self::buildFonnte($settings),
            'ultramsg'     => self::buildUltraMsg($settings),
            'generic_http' => self::buildGenericHttp($settings),
            default        => self::buildWaha($settings),   // 'waha' + any unknown → WAHA
        };
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Private builders
    // ──────────────────────────────────────────────────────────────────────────

    private static function buildWaha(?WahaN8nSetting $settings): WahaProvider
    {
        return new WahaProvider(
            $settings?->waha_api_url  ?? config('services.waha.url', 'http://localhost:3000'),
            $settings?->waha_api_key  ?? config('services.waha.api_key'),
            $settings?->waha_session_name ?? config('services.waha.session', 'default'),
        );
    }

    private static function buildFonnte(?WahaN8nSetting $settings): FonnteProvider
    {
        return new FonnteProvider(
            $settings?->fonnte_token  ?? '',
            $settings?->fonnte_device ?? null,
        );
    }

    private static function buildUltraMsg(?WahaN8nSetting $settings): UltraMsgProvider
    {
        return new UltraMsgProvider(
            $settings?->ultramsg_instance_id ?? '',
            $settings?->ultramsg_token       ?? '',
        );
    }

    private static function buildGenericHttp(?WahaN8nSetting $settings): GenericHttpProvider
    {
        $rawHeaders = $settings?->generic_http_headers ?? [];
        $headers    = is_array($rawHeaders) ? $rawHeaders : [];

        $rawExtra = $settings?->generic_http_extra_body ?? [];
        $extra    = is_array($rawExtra) ? $rawExtra : [];

        return new GenericHttpProvider(
            $settings?->generic_http_url          ?? '',
            $headers,
            $settings?->generic_http_phone_field   ?? 'target',
            $settings?->generic_http_message_field ?? 'message',
            $extra,
        );
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Convenience helpers
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * List of all supported providers for the UI dropdown.
     */
    public static function supportedProviders(): array
    {
        return [
            'waha'         => 'WAHA (Self-hosted)',
            'fonnte'       => 'Fonnte',
            'ultramsg'     => 'UltraMsg',
            'generic_http' => 'Generic HTTP (custom)',
        ];
    }

    /**
     * Quick-send helper — use the active driver.
     */
    public static function send(string $phone, string $message): array
    {
        try {
            return self::driver()->sendMessage($phone, $message);
        } catch (\Exception $e) {
            Log::error('[WhatsAppManager] Unexpected error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
