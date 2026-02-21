<?php

namespace App\Http\Controllers\Kecamatan;

use App\Http\Controllers\Controller;
use App\Models\AppProfile;
use App\Models\WahaN8nSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WahaN8nController extends Controller
{
    /**
     * Display WAHA/n8n management page
     */
    public function index()
    {
        $settings = WahaN8nSetting::getSettings() ?? new WahaN8nSetting();

        return view('kecamatan.settings.waha-n8n', compact('settings'));
    }

    /**
     * Update WAHA/n8n settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'bot_number' => 'nullable|string|max:20',
            'bot_enabled' => 'nullable|boolean',
        ]);

        // Normalize bot_enabled (checkbox sends nothing when unchecked)
        $validated['bot_enabled'] = $request->has('bot_enabled') ? true : false;

        // Normalize phone number: accept 08xxx or 628xxx, store as 628xxx for wa.me links
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

        // Sync bot_number to app_profiles.whatsapp_bot_number (used by landing page)
        if (!empty($validated['bot_number'])) {
            AppProfile::query()->update(['whatsapp_bot_number' => $validated['bot_number']]);
            Cache::forget('app_profile_global');
        }

        // Clear WAHA settings cache
        WahaN8nSetting::clearCache();

        return redirect()
            ->route('kecamatan.settings.waha-n8n.index')
            ->with('success', 'Pengaturan bot berhasil disimpan.');
    }

    /**
     * Check WAHA connection
     */
    public function checkWaha()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan WAHA belum dikonfigurasi.',
            ], 404);
        }

        $result = $settings->checkWahaConnection();

        return response()->json($result);
    }

    /**
     * Check n8n connection
     */
    public function checkN8n()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan n8n belum dikonfigurasi.',
            ], 404);
        }

        $result = $settings->checkN8nConnection();

        return response()->json($result);
    }

    /**
     * Check all connections
     */
    public function checkAll()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan belum dikonfigurasi.',
                'waha' => null,
                'n8n' => null,
            ], 404);
        }

        $wahaResult = $settings->checkWahaConnection();
        $n8nResult = $settings->checkN8nConnection();

        return response()->json([
            'success' => true,
            'message' => 'Pengecekan selesai',
            'waha' => $wahaResult,
            'n8n' => $n8nResult,
            'settings' => $settings->fresh(),
        ]);
    }

    /**
     * Get QR code for WhatsApp connection
     */
    public function getQrCode()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan WAHA belum dikonfigurasi.',
            ], 404);
        }

        $qr = $settings->getQrCode();

        if ($qr) {
            return response()->json([
                'success' => true,
                'qr' => $qr,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal mendapatkan QR code. Pastikan session sudah dimulai.',
        ]);
    }

    /**
     * Start WAHA session
     */
    public function startSession()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan WAHA belum dikonfigurasi.',
            ], 404);
        }

        $result = $settings->startSession();

        return response()->json($result);
    }

    /**
     * Logout from WhatsApp session
     */
    public function logoutSession()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan WAHA belum dikonfigurasi.',
            ], 404);
        }

        $result = $settings->logoutSession();

        return response()->json($result);
    }

    /**
     * Get connection status
     */
    public function status()
    {
        $settings = WahaN8nSetting::getSettings();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'configured' => false,
                'message' => 'Pengaturan belum dikonfigurasi.',
            ]);
        }

        return response()->json([
            'success' => true,
            'configured' => true,
            'waha_connected' => $settings->is_waha_connected,
            'n8n_connected' => $settings->is_n8n_connected,
            'bot_enabled' => $settings->bot_enabled,
            'bot_status' => $settings->bot_status,
            'bot_operational' => $settings->isBotOperational(),
            'last_check' => $settings->last_connection_check?->diffForHumans(),
        ]);
    }

    /**
     * Test sending a message via WAHA
     */
    public function testMessage(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $settings = WahaN8nSetting::getSettings();

        if (!$settings || !$settings->is_waha_connected) {
            return response()->json([
                'success' => false,
                'message' => 'WAHA tidak terhubung.',
            ], 400);
        }

        try {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);

            $response = \Illuminate\Support\Facades\Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'X-Api-Key' => $settings->waha_api_key,
                ])
                ->post("{$settings->waha_api_url}/api/sendText", [
                    'session' => $settings->waha_session_name,
                    'chatId' => "{$phone}@c.us",
                    'text' => $validated['message'],
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim.',
                    'data' => $response->json(),
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim pesan: ' . $response->body(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}