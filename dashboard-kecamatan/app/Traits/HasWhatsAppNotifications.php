<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PublicService;
use App\Models\WahaN8nSetting;

trait HasWhatsAppNotifications
{
    /**
     * Send WhatsApp notification via active WA provider (primary) or n8n webhook (fallback).
     *
     * Provider priority:
     *   1. WhatsAppManager::driver()    — uses whichever provider admin set in dashboard
     *                                     (WAHA / Fonnte / UltraMsg / Generic HTTP)
     *   2. n8n webhook (if configured)  — fallback jika provider utama gagal
     *
     * Dengan urutan ini, admin bisa ganti provider kapan saja dari dashboard
     * tanpa perlu mengubah workflow n8n.
     */
    protected function sendWaNotification($model, $type = 'status_update'): bool
    {
        try {
            $phone = $this->normalizePhone($model->whatsapp ?? $model->contact_wa ?? $model->no_wa);
            if (!$phone) {
                Log::warning("WhatsApp Notification skipped: no phone number", [
                    'model_id' => $model->id ?? 'unknown',
                    'type'     => $type,
                ]);
                return false;
            }

            $message = $this->buildWaMessage($model, $type);

            // 1. PRIMARY: Active WhatsApp Provider (WAHA / Fonnte / UltraMsg / Generic HTTP)
            //    Langsung kirim ke provider yang dipilih admin di dashboard
            try {
                $provider = \App\Services\WhatsApp\WhatsAppManager::driver();
                $providerType = $provider->getProviderType();
                $result = $provider->sendMessage($phone, $message);

                if ($result['success'] ?? false) {
                    Log::info("WhatsApp Notification sent via active provider", [
                        'provider' => $providerType,
                        'phone'    => $phone,
                        'type'     => $type,
                    ]);
                    return true;
                }

                Log::warning("WhatsApp provider [{$providerType}] failed, trying n8n fallback", [
                    'error' => $result['message'] ?? 'Unknown error',
                    'phone' => $phone,
                ]);
            } catch (\Exception $providerErr) {
                Log::warning("WhatsApp provider threw exception, trying n8n fallback", [
                    'error' => $providerErr->getMessage(),
                ]);
            }

            // 2. FALLBACK: n8n Webhook (jika provider utama gagal)
            $n8nWebhook = config('services.n8n.reply_webhook_url', env('N8N_REPLY_WEBHOOK_URL'));

            if ($n8nWebhook) {
                $payload = [
                    'phone'         => $phone,
                    'chatId'        => str_replace('+', '', $phone) . '@c.us',
                    'message'       => $message,
                    'replyText'     => $message,
                    'reply'         => $message,
                    'type'          => $type,
                    'category'      => $model->category ?? 'service',
                    'service_id'    => $model->id,
                    'uuid'          => $model->uuid ?? $model->manage_token ?? $model->id,
                    'tracking_code' => $model->tracking_code ?? null,
                ];

                $response = Http::timeout(10)->post($n8nWebhook, $payload);

                if ($response->successful()) {
                    Log::info("WhatsApp Notification sent via n8n fallback", [
                        'phone' => $phone,
                        'type'  => $type,
                    ]);
                    return true;
                }

                Log::warning("n8n Webhook fallback also failed", ['status' => $response->status()]);
            }

            Log::error("WhatsApp Notification FAILED: all methods exhausted", [
                'phone' => $phone,
                'type'  => $type,
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("Failed to send WhatsApp notification", [
                'error'    => $e->getMessage(),
                'model_id' => $model->id ?? 'unknown',
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
            if (in_array($model->category ?? '', [PublicService::CATEGORY_UMKM, PublicService::CATEGORY_PEKERJAAN])) {
                $workDir = \App\Models\WorkDirectory::where('contact_phone', $model->whatsapp)
                    ->where('display_name', $model->nama_pemohon)
                    ->latest()
                    ->first();
                    
                $jenisLapakan = ($model->category == PublicService::CATEGORY_UMKM) ? 'Lapak UMKM' : 'Profil Jasa';
                $msg .= "🌟 *Selamat! {$jenisLapakan} Anda diverifikasi.*\n\n";
                
                if ($workDir) {
                    $msg .= "✅ Usaha/Jasa Anda kini sudah **otomatis tampil** dan dapat dicari di halaman Direktori Ekonomi website Kecamatan.\n\n";
                    $msg .= "🌐 *Lihat tampilan lapak publik Anda disini:*\n";
                    $msg .= route('economy.show', $workDir->id) . "\n\n";
                }
            } elseif ($model->completion_type === 'digital' && $model->result_file_path) {
                $msg .= "📎 *Dokumen PDF Anda sudah siap:*\n";
                $msg .= asset('storage/' . $model->result_file_path) . "\n\n";
            } elseif ($model->completion_type === 'physical') {
                $msg .= "📍 *Dokumen Siap Diambil di Kantor:*\n";
                if ($model->ready_at) $msg .= "⏰ Waktu: " . $model->ready_at->format('d M Y, H:i') . "\n";
                if ($model->pickup_person) $msg .= "👤 Petugas: {$model->pickup_person}\n";
                $msg .= "\n";
            }
        }

        if (!in_array($model->category ?? '', [PublicService::CATEGORY_UMKM, PublicService::CATEGORY_PEKERJAAN])) {
            $trackingUrl = url('/layanan?q=' . $trackingToken);
            $msg .= "🌐 *Cek Detail Online:*\n{$trackingUrl}\n\n";
            $msg .= "💡 Ketik *STATUS* untuk cek progres via WhatsApp.";
        }
        
        return $msg;
    }
}
