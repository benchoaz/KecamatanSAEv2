<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Models\PublicService;
use Illuminate\Support\Str;

class ComplaintHandler
{
    /**
     * Initiate submission flow
     */
    public function initiate(string $phone, string $category = 'pengaduan'): array
    {
        $session = WhatsappSession::where('phone', $phone)->first();
        if ($session) {
            $session->setTempValue('submission_category', $category);
        }

        $isPelayanan = $category === 'pelayanan';
        $title = $isPelayanan ? "📄 *PERMOHONAN LAYANAN*" : "📢 *PENGADUAN MASYARAKAT*";
        $instruction = $isPelayanan
            ? "Silakan sampaikan permohonan layanan/berkas Anda (misal: pengurusan KTP, Domisili, dll)."
            : "Silakan sampaikan keluhan/pengaduan Anda terkait layanan Kecamatan Besuk.";

        return [
            'success' => true,
            'intent' => 'complaint_initiate',
            'reply' => $title . "\n\n" .
                $instruction . "\n" .
                "Tulis pesan Anda dalam satu pesan (maks 1000 karakter).\n\n" .
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

            $category = $session->getTempValue('submission_category') ?: 'pengaduan';
            $defaultService = $category === 'pelayanan' ? 'Permohonan Layanan/Berkas' : 'Layanan Pengaduan/Administrasi';

            $service = PublicService::create([
                'uuid' => (string) Str::uuid(),
                'category' => $category,
                'source' => 'whatsapp_bot',
                'whatsapp' => $session->phone,
                'nama_pemohon' => 'Warga (WhatsApp)',
                'uraian' => $complaintMessage,
                'jenis_layanan' => $defaultService,
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
                    "Serta *PIN Lacak: {$service->tracking_code}*\n\n" .
                    "Petugas kami akan segera menindaklanjuti. Anda dapat mengecek status laporan kapan saja dengan mengetik *STATUS* atau langsung masukkan PIN Lacak Anda.",
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
