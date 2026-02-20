<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Models\PublicService;
use Illuminate\Support\Str;

class ComplaintHandler
{
    /**
     * Initiate complaint submission flow
     */
    public function initiate(string $phone): array
    {
        return [
            'success' => true,
            'intent' => 'complaint_initiate',
            'reply' => "📢 *PENGADUAN MASYARAKAT*\n\n" .
                "Silakan sampaikan keluhan/pengaduan Anda terkait layanan Kecamatan Besuk.\n" .
                "Tulis pengaduan Anda dalam satu pesan (maks 1000 karakter).\n\n" .
                "Ketik *BATAL* untuk membatalkan.",
            'state_update' => 'WAITING_COMPLAINT_MESSAGE',
        ];
    }

    /**
     * Handle complaint message and ask for confirmation
     */
    public function handleMessage(WhatsappSession $session, string $message): array
    {
        $messageLower = strtolower(trim($message));

        if ($messageLower === 'batal') {
            $session->clear();
            return [
                'success' => true,
                'intent' => 'complaint_cancelled',
                'reply' => "Pengaduan dibatalkan. Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        if (strlen($message) > 1000) {
            return [
                'success' => true,
                'intent' => 'complaint_too_long',
                'reply' => "⚠️ Maaf, pengaduan Anda terlalu panjang (maks 1000 karakter).\n\nSilakan ringkas pengaduan Anda dan kirim kembali, atau ketik *BATAL*.",
                'state_update' => 'WAITING_COMPLAINT_MESSAGE',
            ];
        }

        // Store complaint temporarily
        $session->setTempValue('complaint_message', $message);
        $session->updateState('WAITING_COMPLAINT_CONFIRM');

        $preview = Str::limit($message, 150);

        return [
            'success' => true,
            'intent' => 'complaint_confirm',
            'reply' => "📝 *KONFIRMASI PENGADUAN*\n\n" .
                "Isi Laporan:\n" .
                "_{$preview}_\n\n" .
                "Apakah Anda yakin ingin mengirim laporan ini?\n\n" .
                "Balas *YA* untuk mengirim atau *BATAL* untuk membatalkan.",
            'state_update' => 'WAITING_COMPLAINT_CONFIRM',
        ];
    }

    /**
     * Handle confirmation response
     */
    public function handleConfirmation(WhatsappSession $session, string $message): array
    {
        $messageLower = strtolower(trim($message));

        if ($messageLower === 'ya' || $messageLower === 'y' || $messageLower === 'yes') {
            // Create complaint record using PublicService model
            $complaintMessage = $session->getTempValue('complaint_message');

            $service = PublicService::create([
                'uuid' => (string) Str::uuid(),
                'category' => 'pengaduan', // Category from PublicServiceController constant logic
                'source' => 'whatsapp_bot',
                'whatsapp' => $session->phone,
                'nama_pemohon' => 'Warga (WhatsApp)',
                'uraian' => $complaintMessage,
                'jenis_layanan' => 'Pengaduan Umum',
                'status' => 'menunggu_verifikasi',
                'ip_address' => request()->ip() ?? '127.0.0.1',
            ]);

            $session->clear();

            return [
                'success' => true,
                'intent' => 'complaint_submitted',
                'reply' => "✅ *PENGADUAN TERKIRIM*\n\n" .
                    "Terima kasih, laporan Anda telah kami terima dengan ID:\n" .
                    "*{$service->uuid}*\n\n" .
                    "Petugas kami akan segera menindaklanjuti. Anda dapat mengecek status laporan kapan saja dengan mengetik *STATUS*.",
                'state_update' => null,
            ];
        }

        // Cancel complaint if anything else
        $session->clear();

        return [
            'success' => true,
            'intent' => 'complaint_cancelled',
            'reply' => "Pengaduan dibatalkan. Ketik *MENU* untuk kembali.",
            'state_update' => null,
        ];
    }
}
