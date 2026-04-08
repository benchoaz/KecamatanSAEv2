<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PublicService;
use App\Models\WahaN8nSetting;

trait HasWhatsAppNotifications
{
    /**
     * Send WhatsApp notification via n8n (preferred) or WAHA direct
     */
    protected function sendWaNotification($model, $type = 'status_update'): bool
    {
        try {
            $phone = $this->normalizePhone($model->whatsapp ?? $model->contact_wa ?? $model->no_wa);
            if (!$phone) return false;

            $message = $this->buildWaMessage($model, $type);
            
            // 1. PRIORITIZE n8n Webhook (As requested: "Otomatis lewat n8n")
            $n8nWebhook = config('services.n8n.reply_webhook_url', env('N8N_REPLY_WEBHOOK_URL'));
            
            if ($n8nWebhook) {
                $response = Http::timeout(10)->post($n8nWebhook, [
                    'phone' => $phone,
                    'message' => $message,
                    'type' => $type,
                    'category' => $model->category ?? 'service',
                    'service_id' => $model->id,
                    'uuid' => $model->uuid ?? $model->manage_token ?? $model->id,
                    'tracking_code' => $model->tracking_code ?? null,
                ]);

                if ($response->successful()) {
                    Log::info("WhatsApp Notification sent via n8n", ['phone' => $phone, 'type' => $type]);
                    return true;
                }
                
                Log::warning("n8n Webhook failed, trying fallback", ['status' => $response->status()]);
            }

            // 2. FALLBACK: WAHA Direct
            $wahaSettings = WahaN8nSetting::getSettings();
            if ($wahaSettings && $wahaSettings->waha_api_url) {
                $headers = ['Content-Type' => 'application/json'];
                if ($wahaSettings->waha_api_key) {
                    $headers['X-Api-Key'] = $wahaSettings->waha_api_key;
                }
                
                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                $response = Http::withHeaders($headers)
                    ->timeout(10)
                    ->post(rtrim($wahaSettings->waha_api_url, '/') . '/api/sendText', [
                        'session' => $wahaSettings->waha_session_name ?? 'default',
                        'chatId' => $cleanPhone . '@c.us',
                        'text' => $message,
                    ]);
                
                return $response->successful();
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp notification", [
                'error' => $e->getMessage(),
                'model_id' => $model->id ?? 'unknown'
            ]);
            return false;
        }
    }

    /**
     * Normalize phone number to 62 prefix
     */
    protected function normalizePhone($phone): ?string
    {
        if (!$phone) return null;
        
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            return '+62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '62')) {
            return '+' . $clean;
        }
        
        return '+' . $clean;
    }

    /**
     * Build standard messages based on type
     */
    protected function buildWaMessage($model, $type): string
    {
        $regionName = strtoupper(appProfile()->region_name ?? 'KECAMATAN');

        // WA Reply: send petugas's custom text directly
        if ($type === 'wa_reply') {
            $msg = "💬 *Jawaban Resmi Kecamatan {$regionName}*\n\n";
            $msg .= $model->public_response ?? '(Tidak ada pesan)';
            $msg .= "\n\n";
            $msg .= "🆔 ID Permohonan: `" . ($model->tracking_code ?? $model->uuid) . "`\n";
            $msg .= "📅 " . now()->format('d M Y, H:i') . " WIB\n\n";
            $msg .= "Ketik *STATUS* untuk melihat progres terkini.\n";
            $msg .= "_Pesan otomatis dari Layanan Digital {$regionName}_";
            return $msg;
        }

        if ($type === 'submission') {
            $msg = "📝 *Konfirmasi Pendaftaran*\n\n";
            $msg .= "Terima kasih, permohonan Anda telah kami terima.\n\n";
            $msg .= "📌 *ID Lacak:* `{$model->tracking_code}`\n";
            $msg .= "📂 Layanan: " . ($model->jenis_layanan ?? 'Pelayanan Berkas') . "\n";
            $msg .= "👤 Pemohon: {$model->nama_pemohon}\n";
            $msg .= "📅 Tanggal: " . now()->format('d M Y, H:i') . "\n\n";
            $msg .= "Gunakan ID Lacak di atas untuk mengecek status permohonan Anda di website kami atau ketik *STATUS* di chat ini.\n\n";
            $msg .= "_Pesan otomatis dari Layanan Digital {$regionName}_";
            return $msg;
        }

        // Default: Status Update (Copied & Refined from PelayananController)
        $statusLabel = $model->status_label ?? $model->status;
        $statusEmoji = match ($model->status) {
            PublicService::STATUS_MENUNGGU => '⏳',
            PublicService::STATUS_DIPROSES => '🔄',
            PublicService::STATUS_SELESAI => '✅',
            PublicService::STATUS_DITOLAK => '❌',
            default => '📋'
        };

        $idDisplay = $model->uuid ?? $model->tracking_code ?? $model->id;
        $trackingToken = $model->tracking_code ?? $model->uuid;

        $msg = "{$statusEmoji} *Update Status Layanan*\n\n";
        $msg .= "🆔 ID: `{$idDisplay}`\n";
        $msg .= "📂 Layanan: " . ($model->jenis_layanan ?? 'Pelayanan') . "\n";
        $msg .= "📊 Status: *{$statusLabel}*\n";
        $msg .= "📅 Update: " . now()->format('d M Y, H:i') . "\n\n";

        if (!empty($model->public_response)) {
            $msg .= "📝 *Respon Petugas:*\n{$model->public_response}\n\n";
        }

        if ($model->status === PublicService::STATUS_SELESAI) {
            if ($model->completion_type === 'digital' && $model->result_file_path) {
                $msg .= "📎 *Dokumen PDF Anda sudah siap:*\n";
                $msg .= asset('storage/' . $model->result_file_path) . "\n\n";
            } elseif ($model->completion_type === 'physical') {
                $msg .= "📍 *Dokumen Siap Diambil di Kantor:*\n";
                if ($model->ready_at) $msg .= "⏰ Waktu: " . $model->ready_at->format('d M Y, H:i') . "\n";
                if ($model->pickup_person) $msg .= "👤 Petugas: {$model->pickup_person}\n";
                $msg .= "\n";
            }
        }

        $trackingUrl = url('/layanan?q=' . $trackingToken);
        $msg .= "🌐 *Cek Detail Online:*\n{$trackingUrl}\n\n";
        $msg .= "💡 Ketik *STATUS* untuk cek progres via WhatsApp.";
        
        return $msg;
    }
}
