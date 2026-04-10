<?php

namespace App\Services\WhatsApp;

use App\Models\PublicService;
use Illuminate\Support\Facades\Cache;

class StatusHandler
{
    /**
     * Cache TTL in seconds (5 minutes)
     */
    private const CACHE_TTL = 300;

    /**
     * Handle status check request
     */
    public function handle($phone, ?string $query = null): array
    {
        // Normalize phone to string
        if (!is_string($phone)) {
            if (is_array($phone)) {
                $phone = reset($phone);
            }
            $phone = (string) $phone;
        }
        // If a specific query (PIN or UUID) is provided
        if ($query) {
            $queryLower = strtolower(trim($query));
            if ($queryLower === 'lupa' || $queryLower === 'forgot' || str_contains($queryLower, 'pin')) {
                return $this->handleForgotPin($phone);
            }

            // Try cache first for PIN/UUID lookup
            $cacheKey = $this->getCacheKey('pin', $query);
            $cached = Cache::get($cacheKey);

            if ($cached) {
                return $cached;
            }

            $service = PublicService::where('tracking_code', $query)
                ->orWhere('uuid', $query)
                ->first();

            if ($service) {
                $result = [
                    'success' => true,
                    'intent' => 'status',
                    'reply' => $this->formatSingleStatus($service),
                    'state_update' => 'ADM_SUBMENU',
                ];

                // Cache the result
                Cache::put($cacheKey, $result, self::CACHE_TTL);

                return $result;
            }
        }

        // Clean phone number (remove +, spaces, etc.)
        $cleanPhone = $this->normalizePhone($phone);

        // Try cache for phone number lookup
        $cacheKey = $this->getCacheKey('phone', $cleanPhone);
        $cached = Cache::get($cacheKey);

        if ($cached) {
            return $cached;
        }

        // Search for public services by phone number (multiple matching strategies)
        $services = $this->findByPhone($cleanPhone);

        if ($services->isEmpty()) {
            $result = [
                'success' => true,
                'intent' => 'status',
                'reply' => $this->formatNotFound($phone),
                'state_update' => 'ADM_SUBMENU',
            ];

            // Cache negative result for shorter time (2 minutes)
            Cache::put($cacheKey, $result, 120);

            return $result;
        }

        if ($services->count() === 1) {
            $service = $services->first();
            $result = [
                'success' => true,
                'intent' => 'status',
                'reply' => $this->formatSingleStatus($service),
                'state_update' => 'ADM_SUBMENU',
            ];
        } else {
            // Multiple services found
            $result = [
                'success' => true,
                'intent' => 'status',
                'reply' => $this->formatMultipleStatus($services),
                'state_update' => 'ADM_SUBMENU',
            ];
        }

        // Cache the result
        Cache::put($cacheKey, $result, self::CACHE_TTL);

        return $result;
    }

    /**
     * Handle forgot PIN (LUPA PIN) request
     */
    public function handleForgotPin($phone): array
    {
        // Normalize phone to string
        if (!is_string($phone)) {
            if (is_array($phone)) {
                $phone = reset($phone);
            }
            $phone = (string) $phone;
        }

        $cleanPhone = $this->normalizePhone($phone);

        // Find all services by phone number
        $services = $this->findByPhone($cleanPhone);

        if ($services->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'lupa_pin',
                'reply' => $this->formatNoServicesForForgotPin($phone),
                'state_update' => 'ADM_SUBMENU',
            ];
        }

        return [
            'success' => true,
            'intent' => 'lupa_pin',
            'reply' => $this->formatForgotPinResponse($services),
            'state_update' => 'ADM_SUBMENU',
        ];
    }

    /**
     * Normalize phone number to standard format
     */
    protected function normalizePhone($phone): string
    {
        // Handle if phone is an array or object
        if (!is_string($phone)) {
            if (is_array($phone)) {
                $phone = reset($phone); // Get first element
            }
            $phone = (string) $phone;
        }

        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 62, keep it (Indonesia format with country code)
        // If starts with 0, convert to 62
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        return $clean;
    }

    /**
     * Find services by phone number with multiple matching strategies
     */
    protected function findByPhone(string $phone): \Illuminate\Database\Eloquent\Collection
    {
        if (empty($phone)) {
            return collect();
        }

        // Try exact match first
        $services = PublicService::where('whatsapp', $phone)
            ->latest()
            ->get();

        if ($services->isNotEmpty()) {
            return $services;
        }

        // Try suffix match (last 10 digits) - now using indexed column
        if (strlen($phone) >= 10) {
            $suffix = substr($phone, -10);
            $services = PublicService::where('whatsapp_suffix', $suffix)
                ->latest()
                ->get();

            if ($services->isNotEmpty()) {
                return $services;
            }
        }

        // Fallback: LIKE search (slower)
        if (strlen($phone) >= 10) {
            $suffix = substr($phone, -10);
            $services = PublicService::where('whatsapp', 'LIKE', "%{$suffix}")
                ->latest()
                ->get();
        }

        return $services ?? collect();
    }

    /**
     * Generate cache key
     */
    protected function getCacheKey(string $type, string $identifier): string
    {
        return "whatsapp_status:{$type}:" . md5($identifier);
    }

    /**
     * Format single service status
     */
    protected function formatSingleStatus(PublicService $service): string
    {
        $statusLabel = $this->getStatusBadge($service->status);
        $baseUrl = config('app.url', 'https://localhost');
        $trackingUrl = rtrim($baseUrl, '/') . '/layanan?q=' . $service->tracking_code;

        $msg = "📂 *INFORMASI BERKAS ANDA*\n\n";
        $msg .= "Nama: *{$service->nama_pemohon}*\n";
        $msg .= "Layanan: {$service->jenis_layanan}\n";
        $msg .= "━━━━━━━━━━━━━━━━━\n";
        $msg .= "🔑 *PIN Lacak:* `{$service->tracking_code}`\n";
        $msg .= "📊 *Status:* {$statusLabel}\n";
        $msg .= "📅 *Tanggal:* {$service->created_at->format('d/m/Y')}\n";
        $msg .= "━━━━━━━━━━━━━━━━━\n";

        $response = $service->effective_public_response;
        if ($response) {
            $msg .= "\n💬 *Tanggapan Petugas:*\n_{$response}_\n";
        }

        if ($service->completion_type === 'digital' && $service->result_file_path) {
            $msg .= "\n✨ *Dokumen Selesai:* Silakan cek di website atau hubungi admin.\n";
        }

        $msg .= "\n🔗 *Cek Detail Lengkap:*\n{$trackingUrl}\n\n";
        $msg .= "_Ketik MENU untuk kembali._";

        return $msg;
    }

    /**
     * Format multiple services status
     */
    protected function formatMultipleStatus($services): string
    {
        $baseUrl = config('app.url', 'https://localhost');
        
        $msg = "📂 *DAFTAR BERKAS LAYANAN ANDA*\n\n";
        $msg .= "Halo! Kami menemukan *{$services->count()}* berkas yang terdaftar dengan nomor ini:\n\n";

        foreach ($services as $index => $service) {
            $num = $index + 1;
            $statusLabel = $this->getStatusBadge($service->status);
            
            $msg .= "{$num}. *{$service->jenis_layanan}*\n";
            $msg .= "   📌 PIN: `{$service->tracking_code}`\n";
            $msg .= "   📊 Status: {$statusLabel}\n";
            $msg .= "   📅 " . $service->created_at->format('d/m/Y') . "\n\n";
        }

        $msg .= "━━━━━━━━━━━━━━━━━\n\n";
        $msg .= "💡 *Tips:* Ketik langsung **PIN Lacak** (6 angka) untuk melihat detail lengkap, atau kunjungi portal kami:\n";
        $msg .= rtrim($baseUrl, '/') . "/layanan\n\n";
        $msg .= "_Ketik MENU untuk kembali._";

        return $msg;
    }

    /**
     * Get status badge with emoji
     */
    protected function getStatusBadge(string $status): string
    {
        return match (strtolower($status)) {
            'pending', 'menunggu_verifikasi' => '⏳ Menunggu Antrean',
            'diproses' => '⚙️ Sedang Dikerjakan',
            'selesai' => '✅ Selesai & Siap Diambil',
            'ditolak' => '❌ Perlu Perbaikan / Ditolak',
            default => '📋 ' . ucfirst($status),
        };
    }

    /**
     * Format response when service not found
     */
    protected function formatNotFound(string $phone): string
    {
        return "❌ *Berkas Tidak Ditemukan*\n\n" .
            "Kami tidak menemukan berkas layanan yang terdaftar dengan nomor *{$phone}*.\n\n" .
            "💡 *Saran Aktif:*\n" .
            "- Ketik langsung **PIN Lacak** (6 angka) jika ada.\n" .
            "- Ketik **MENU** untuk melihat opsi lain.\n\n" .
            "━━━━━━━━━━━━━━━━━\n" .
            "Butuh bantuan? Silakan hubungi petugas kecamatan.";
    }

    /**
     * Format response for forgot PIN - no services found
     */
    protected function formatNoServicesForForgotPin(string $phone): string
    {
        return "⚠️ *Nomor Tidak Terdaftar*\n\n" .
            "Maaf, nomor *{$phone}* belum terdaftar memiliki layanan aktif di sistem kami.\n\n" .
            "💡 *Langkah Selanjutnya:*\n" .
            "- Silakan ajukan layanan baru di website.\n" .
            "- Atau hubungi petugas jika ini adalah kesalahan.\n\n" .
            "━━━━━━━━━━━━━━━━━\n" .
            "Ketik *MENU* untuk kembali";
    }

    /**
     * Format response for forgot PIN - services found
     */
    protected function formatForgotPinResponse($services): string
    {
        $reply = "🔑 *PENCARIAN PIN LACAK*\n\n";
        $reply .= "Ditemukan *{$services->count()}* PIN yang terdaftar pada nomor Anda:\n\n";

        foreach ($services as $service) {
            $statusLabel = $this->getStatusBadge($service->status);
            $reply .= "📌 *{$service->jenis_layanan}*\n";
            $reply .= "   PIN: `{$service->tracking_code}`\n";
            $reply .= "   Status: {$statusLabel}\n\n";
        }

        $reply .= "━━━━━━━━━━━━━━━━━\n";
        $reply .= "💡 *Tips:* Ketik PIN di atas untuk melihat detail lengkap.\n\n" .
            "Ketik *MENU* untuk kembali.";

        return $reply;
    }
}
