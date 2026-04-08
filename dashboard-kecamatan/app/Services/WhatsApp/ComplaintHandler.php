<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;
use App\Models\PublicService;
use App\Models\AppProfile;
use Illuminate\Support\Str;

class ComplaintHandler
{
    /**
     * Get complaint form URL from AppProfile
     */
    protected function getComplaintFormUrl(?string $name = null, ?string $phone = null, ?string $category = null): string
    {
        $profile = app(AppProfile::class);
        $baseUrl = $profile->public_base_url ?? $profile->app_url ?? config('app.url', 'https://localhost');
        $url = rtrim($baseUrl, '/') . '/#pengaduan';

        // Add pre-filled parameters if provided
        $params = [];
        if ($name) {
            $params['nama'] = urlencode($name);
        }
        if ($phone) {
            $params['no_hp'] = urlencode($phone);
        }
        if ($category) {
            $params['kategori'] = urlencode($category);
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    /**
     * Initiate complaint flow - ask for name
     */
    public function initiate(string $phone, string $category = 'pengaduan'): array
    {
        $session = WhatsappSession::getOrCreate($phone);
        $session->setTempValue('submission_category', $category);

        return [
            'success' => true,
            'intent' => 'complaint_initiate',
            'reply' => "📢 *PENGADUAN MASYARAKAT*\n\n" .
                "Terima kasih ingin menyampaikan aspirasi.\n\n" .
                "Siapa nama lengkap Anda?\n" .
                "(Ketik nama Anda, atau ketik TIDAK untuk membatalkan)",
            'state_update' => 'WAITING_COMPLAINT_NAME',
        ];
    }

    /**
     * Handle name input - ask for WhatsApp number
     */
    public function handleName(WhatsappSession $session, string $message): array
    {
        $messageTrim = trim($message);
        $messageLower = strtolower($messageTrim);

        // Check for cancel
        if (in_array($messageLower, ['tidak', 'batal', 'cancel', 'no'])) {
            $session->clear();
            return [
                'success' => true,
                'intent' => 'complaint_cancelled',
                'reply' => "Pengaduan dibatalkan.\n" .
                    "Ketik *MENU* untuk kembali ke menu utama.",
                'state_update' => null,
            ];
        }

        // Store name
        $session->setTempValue('complaint_name', $messageTrim);

        // Get user's current phone number
        $userPhone = $session->phone;

        return [
            'success' => true,
            'intent' => 'complaint_name_received',
            'reply' => "Terima kasih *{$messageTrim}*.\n\n" .
                "Nomor WhatsApp yang dapat kami hubungi: *{$userPhone}*\n\n" .
                "Apakah nomor ini benar untuk dihubungi?\n\n" .
                "Ketik *YA* jika benar, atau ketik nomor WhatsApp lain yang ingin dihubungi.",
            'state_update' => 'WAITING_COMPLAINT_WA',
        ];
    }

    /**
     * Handle WhatsApp number input - ask for Category
     */
    public function handleWhatsApp(WhatsappSession $session, string $message): array
    {
        $messageTrim = trim($message);
        $messageLower = strtolower($messageTrim);

        // If user confirms with YA
        if (in_array($messageLower, ['ya', 'y', 'yes', 'benar', 'ok', 'oke', 'siap'])) {
            $waNumber = $session->phone; // Use current session phone
        } else {
            // User provided different WhatsApp number
            $waNumber = preg_replace('/[^0-9]/', '', $messageTrim);
            // Add country code if not present
            if (!str_starts_with($waNumber, '62')) {
                $waNumber = '62' . ltrim($waNumber, '0');
            }
        }

        // Store WhatsApp number
        $session->setTempValue('complaint_wa', $waNumber);

        return [
            'success' => true,
            'intent' => 'complaint_wa_received',
            'reply' => "Baik, nomor WhatsApp *{$waNumber}* telah dicatat.\n\n" .
                "Pilih kategori pengaduan Anda:\n" .
                "1️⃣ Pengaduan (Layanan Tidak Memadai)\n" .
                "2️⃣ Aspirasi (Saran & Masukan)\n" .
                "3️⃣ Permintaan (Butuh Layanan Khusus)\n\n" .
                "Ketik angka *1*, *2*, atau *3*:",
            'state_update' => 'WAITING_COMPLAINT_CATEGORY',
        ];
    }

    /**
     * Handle Category input - send form link with disclaimer
     */
    public function handleCategory(WhatsappSession $session, string $message): array
    {
        $input = trim($message);
        $category = 'Pengaduan'; // default
        
        if ($input === '1') {
            $category = 'Pengaduan';
        } elseif ($input === '2') {
            $category = 'Aspirasi';
        } elseif ($input === '3') {
            $category = 'Permintaan';
        } elseif (is_numeric($input)) {
            return [
                'success' => true,
                'intent' => 'complaint_category_invalid',
                'reply' => "Pilihan tidak valid. Silakan ketik angka *1*, *2*, atau *3* sesuai kategori.",
                'state_update' => 'WAITING_COMPLAINT_CATEGORY',
            ];
        } else {
            // Fallback for non-number text
            $category = $input;
        }

        // Store Category
        $session->setTempValue('complaint_category', $category);

        // Retrieve earlier data
        $name = $session->getTempValue('complaint_name');
        $waNumber = $session->getTempValue('complaint_wa');

        // Get form URL with all pre-filled parameters
        $formUrl = $this->getComplaintFormUrl($name, $waNumber, $category);

        // Clear session after providing link
        $session->clear();

        $reply = "✅ *Data Diterima!*\n\n" .
            "Nama: *{$name}*\n" .
            "WhatsApp: *{$waNumber}*\n" .
            "Kategori: *{$category}*\n\n" .
            "━━━━━━━━━━━━━━━━━━━━\n\n" .
            "📝 *ISI FORM PENGADUAN*:\n{$formUrl}\n\n" .
            "━━━━━━━━━━━━━━━━━━━━\n\n" .
            "⚠️ *PERINGATAN & DISCLAIMER*:\n\n" .
            "1. Informasi yang Anda berikan akan diverifikasi oleh petugas.\n\n" .
            "2. Dilarang menyebarkan informasi bohong (hoax), fitnah, atau tuduhan tanpa bukti.\n\n" .
            "3. Setiap laporan palsu/hoax adalah pelanggaran hukum dan dapat dipidana.\n\n" .
            "4. Kami akan menindaklanjuti laporan Anda setelah verifikasi selesai.\n\n" .
            "5. Jangan mudah percaya dengan informasi yang belum terverifikasi.\n\n" .
            "━━━━━━━━━━━━━━━━━━━━\n\n" .
            "Terima kasih atas partisipasi Anda membangun {@region}.\n" .
            "Ketik *MENU* untuk kembali.";

        // Replace placeholder with region name
        $profile = app(AppProfile::class);
        $regionName = $profile->region_name ?? 'kecamatan kami';
        $reply = str_replace('{@region}', $regionName, $reply);

        return [
            'success' => true,
            'intent' => 'complaint_link_sent',
            'reply' => $reply,
            'state_update' => null,
        ];
    }

}
