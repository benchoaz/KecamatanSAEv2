<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\AppProfile;
use App\Models\WahaN8nSetting;
use App\Services\WhatsApp\N8nWorkflowGenerator;
use App\Services\WhatsApp\WhatsAppManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WahaN8nController extends Controller
{
    // ──────────────────────────────────────────────────────────────────────────
    // Bot Number / Enable-Disable (halaman utama — tidak berubah)
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $settings  = WahaN8nSetting::getSettings() ?? new WahaN8nSetting();
        $providers = WhatsAppManager::supportedProviders();

        return view('kecamatan.settings.waha-n8n', compact('settings', 'providers'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'bot_number'  => 'nullable|string|max:20',
            'bot_enabled' => 'nullable|boolean',
        ]);

        $validated['bot_enabled'] = $request->has('bot_enabled') ? true : false;

        if (!empty($validated['bot_number'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['bot_number']);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }
            $validated['bot_number'] = $phone;
        }

        $settings = WahaN8nSetting::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            $settings = WahaN8nSetting::create($validated);
        }

        if (!empty($validated['bot_number'])) {
            AppProfile::query()->update(['whatsapp_bot_number' => $validated['bot_number']]);
            Cache::forget('app_profile_global');
        }

        WahaN8nSetting::clearCache();

        return redirect()
            ->route('kecamatan.settings.waha-n8n.index')
            ->with('success', 'Pengaturan bot berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Provider Settings
    // ──────────────────────────────────────────────────────────────────────────

    public function providerSettings()
    {
        $settings  = WahaN8nSetting::getSettings() ?? new WahaN8nSetting();
        $providers = WhatsAppManager::supportedProviders();

        return view('kecamatan.settings.whatsapp-provider', compact('settings', 'providers'));
    }

    public function updateProvider(Request $request)
    {
        $validated = $request->validate([
            'active_provider'            => 'required|in:waha,fonnte,ultramsg,generic_http',

            // WAHA fields
            'waha_api_url'               => 'nullable|url|max:255',
            'waha_api_key'               => 'nullable|string|max:255',
            'waha_session_name'          => 'nullable|string|max:100',

            // Fonnte
            'fonnte_token'               => 'nullable|string|max:255',
            'fonnte_device'              => 'nullable|string|max:100',

            // UltraMsg
            'ultramsg_instance_id'       => 'nullable|string|max:100',
            'ultramsg_token'             => 'nullable|string|max:255',

            // Generic HTTP
            'generic_http_url'           => 'nullable|url|max:500',
            'generic_http_headers_raw'   => 'nullable|string',   // JSON textarea input
            'generic_http_phone_field'   => 'nullable|string|max:100',
            'generic_http_message_field' => 'nullable|string|max:100',
            'generic_http_extra_raw'     => 'nullable|string',   // JSON textarea input

            // n8n
            'n8n_api_url'                => 'nullable|url|max:255',
            'n8n_api_key'                => 'nullable|string|max:255',
            'n8n_webhook_url'            => 'nullable|url|max:255',
        ]);

        // Parse JSON textarea fields
        $headers = [];
        if (!empty($validated['generic_http_headers_raw'])) {
            $decoded = json_decode($validated['generic_http_headers_raw'], true);
            $headers = is_array($decoded) ? $decoded : [];
        }

        $extraBody = [];
        if (!empty($validated['generic_http_extra_raw'])) {
            $decoded = json_decode($validated['generic_http_extra_raw'], true);
            $extraBody = is_array($decoded) ? $decoded : [];
        }

        $data = [
            'active_provider'            => $validated['active_provider'],
            'waha_api_url'               => $validated['waha_api_url'] ?? null,
            'waha_api_key'               => $validated['waha_api_key'] ?? null,
            'waha_session_name'          => $validated['waha_session_name'] ?? 'default',
            'fonnte_token'               => $validated['fonnte_token'] ?? null,
            'fonnte_device'              => $validated['fonnte_device'] ?? null,
            'ultramsg_instance_id'       => $validated['ultramsg_instance_id'] ?? null,
            'ultramsg_token'             => $validated['ultramsg_token'] ?? null,
            'generic_http_url'           => $validated['generic_http_url'] ?? null,
            'generic_http_headers'       => $headers,
            'generic_http_phone_field'   => $validated['generic_http_phone_field'] ?? 'target',
            'generic_http_message_field' => $validated['generic_http_message_field'] ?? 'message',
            'generic_http_extra_body'    => $extraBody,
            'n8n_api_url'                => $validated['n8n_api_url'] ?? null,
            'n8n_api_key'                => $validated['n8n_api_key'] ?? null,
            'n8n_webhook_url'            => $validated['n8n_webhook_url'] ?? null,
        ];

        $settings = WahaN8nSetting::first();
        if ($settings) {
            $settings->update($data);
        } else {
            WahaN8nSetting::create($data);
        }

        WahaN8nSetting::clearCache();

        return redirect()
            ->route('kecamatan.settings.waha-n8n.provider')
            ->with('success', 'Konfigurasi provider WhatsApp berhasil disimpan.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Test Connection (AJAX)
    // ──────────────────────────────────────────────────────────────────────────

    public function testProvider(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:waha,fonnte,ultramsg,generic_http',
        ]);

        $settings = WahaN8nSetting::getSettings();
        $provider = WhatsAppManager::make($request->provider, $settings);
        $result   = $provider->checkConnection();

        return response()->json([
            'success'      => $result['success'],
            'message'      => $result['message'],
            'status'       => $result['status'] ?? 'unknown',
            'provider_name'=> $provider->getName(),
            'data'         => $result['data'] ?? null,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // Download n8n Workflow JSON
    // ──────────────────────────────────────────────────────────────────────────

    public function downloadN8nWorkflow()
    {
        $settings = WahaN8nSetting::getSettings() ?? new WahaN8nSetting();
        $generator = new N8nWorkflowGenerator($settings);
        $json      = $generator->toJson();

        $provider  = $settings->getActiveProvider();
        $filename  = "kecamatan-whatsapp-bot-{$provider}-" . date('Ymd') . '.json';

        return response($json, 200, [
            'Content-Type'        => 'application/json',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
