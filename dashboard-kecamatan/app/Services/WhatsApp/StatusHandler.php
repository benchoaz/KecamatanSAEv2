<?php

namespace App\Services\WhatsApp;

use App\Models\PublicService;

class StatusHandler
{
    /**
     * Handle status check request
     */
    public function handle(string $phone): array
    {
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
                'reply' => "Tidak ditemukan berkas layanan yang terdaftar dengan nomor {$phone}.\n\nPastikan nomor Anda sudah terdaftar saat pengajuan layanan.",
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
        $status .= "ID Tracking: {$service->uuid}\n";
        $status .= "Status: " . $this->getStatusBadge($service->status) . "\n";
        $status .= "Tanggal: {$service->created_at->format('d/m/Y')}\n";

        if ($service->public_response) {
            $status .= "\n📝 *Tanggapan Petugas:*\n{$service->public_response}";
        }

        if ($service->completion_type === 'digital' && $service->result_file_path) {
            $status .= "\n\n📄 *Dokumen Selesai:* Silakan cek di tracking web atau hubungi admin.";
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
            $status .= "   Status: " . $this->getStatusBadge($service->status) . "\n";
            $status .= "   Tanggal: {$service->created_at->format('d/m/Y')}\n\n";
        }

        $status .= "💡 _Gunakan ID Tracking untuk mendapatkan detail lebih lengkap via website._";

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
