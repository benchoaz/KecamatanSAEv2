<?php

namespace App\Services\WhatsApp;

use App\Models\PublicService;

class StatusHandler
{
    /**
     * Handle status check request
     */
    public function handle(string $phone, ?string $query = null): array
    {
        // If a specific query (PIN or UUID) is provided
        if ($query) {
            $service = PublicService::where('tracking_code', $query)
                ->orWhere('uuid', $query)
                ->first();

            if ($service) {
                return [
                    'success' => true,
                    'intent' => 'status',
                    'reply' => $this->formatSingleStatus($service),
                    'state_update' => null,
                ];
            }
        }

        // Clean phone number (remove +, spaces, etc.)
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Search for public services by phone number
        $services = PublicService::where('whatsapp', 'LIKE', "%{$cleanPhone}%")
            ->latest()
            ->get();

        if ($services->isEmpty()) {
            return [
                'success' => true,
                'intent' => 'status',
                'reply' => "Tidak ditemukan berkas layanan yang terdaftar dengan nomor {$phone}.\n\nPastikan nomor Anda sudah terdaftar saat pengajuan layanan atau masukkan PIN Lacak Anda langsung.",
                'state_update' => null,
            ];
        }

        if ($services->count() === 1) {
            $service = $services->first();
            return [
                'success' => true,
                'intent' => 'status',
                'reply' => $this->formatSingleStatus($service),
                'state_update' => null,
            ];
        }

        // Multiple services found
        return [
            'success' => true,
            'intent' => 'status',
            'reply' => $this->formatMultipleStatus($services),
            'state_update' => null,
        ];
    }

    /**
     * Format single service status
     */
    protected function formatSingleStatus(PublicService $service): string
    {
        $status = "📋 *STATUS BERKAS LAYANAN*\n\n";
        $status .= "Jenis Layanan: {$service->jenis_layanan}\n";
        $status .= "ID: " . substr($service->uuid, 0, 8) . "...\n";
        $status .= "PIN Lacak: *{$service->tracking_code}*\n";
        $status .= "Status: " . $this->getStatusBadge($service->status) . "\n";
        $status .= "Tanggal: {$service->created_at->format('d/m/Y')}\n";

        $response = $service->effective_public_response;
        if ($response) {
            $status .= "\n📝 *Tanggapan Petugas:*\n{$response}";
        }

        if ($service->completion_type === 'digital' && $service->result_file_path) {
            $status .= "\n\n📄 *Dokumen Selesai:* Silakan cek di website atau hubungi admin.";
        }

        return $status;
    }

    /**
     * Format multiple services status
     */
    protected function formatMultipleStatus($services): string
    {
        $status = "📋 *DAFTAR BERKAS LAYANAN ANDA*\n\n";
        $status .= "Ditemukan {$services->count()} berkas:\n\n";

        foreach ($services as $index => $service) {
            $num = $index + 1;
            $status .= "{$num}. {$service->jenis_layanan}\n";
            $status .= "   ID: " . substr($service->uuid, 0, 8) . "...\n";
            $status .= "   PIN Lacak: *{$service->tracking_code}*\n";
            $status .= "   Status: " . $this->getStatusBadge($service->status) . "\n";
            $status .= "   Tanggal: {$service->created_at->format('d/m/Y')}\n\n";
        }

        $status .= "💡 _Gunakan PIN Lacak (6 angka) untuk melihat detail lebih lengkap di website atau ketik langsung PIN tersebut di sini._";

        return $status;
    }

    /**
     * Get status badge with emoji
     */
    protected function getStatusBadge(string $status): string
    {
        // statuses from PublicService model or controller logic
        return match (strtolower($status)) {
            'pending', 'menunggu_verifikasi' => '🟡 Menunggu Verifikasi',
            'diproses' => '🔵 Sedang Diproses',
            'selesai' => '✅ Selesai (Siap Diambil/Download)',
            'ditolak' => '🔴 Ditolak/Perlu Perbaikan',
            default => ucfirst($status),
        };
    }
}
