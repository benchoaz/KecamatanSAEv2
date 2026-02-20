<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;

class IntentHandler
{
    protected StatusHandler $statusHandler;
    protected SyaratHandler $syaratHandler;
    protected UmkmHandler $umkmHandler;
    protected JasaHandler $jasaHandler;
    protected LokerHandler $lokerHandler;
    protected ComplaintHandler $complaintHandler;
    protected OwnerHandler $ownerHandler;

    public function __construct(
        StatusHandler $statusHandler,
        SyaratHandler $syaratHandler,
        UmkmHandler $umkmHandler,
        JasaHandler $jasaHandler,
        LokerHandler $lokerHandler,
        ComplaintHandler $complaintHandler,
        OwnerHandler $ownerHandler
    ) {
        $this->statusHandler = $statusHandler;
        $this->syaratHandler = $syaratHandler;
        $this->umkmHandler = $umkmHandler;
        $this->jasaHandler = $jasaHandler;
        $this->lokerHandler = $lokerHandler;
        $this->complaintHandler = $complaintHandler;
        $this->ownerHandler = $ownerHandler;
    }

    /**
     * Handle incoming message and detect intent
     */
    public function handle(string $phone, string $message): array
    {
        $messageLower = strtolower($message);

        // Menu intent
        if ($this->matchesIntent($messageLower, ['menu', 'help', 'bantuan'])) {
            return $this->menuIntent();
        }

        // Status check intent
        if ($this->matchesIntent($messageLower, ['status', 'cek', 'lacak'])) {
            return $this->statusHandler->handle($phone);
        }

        // SYARAT (requirements) intent - NEW!
        if (str_starts_with($messageLower, 'syarat') || $this->matchesIntent($messageLower, ['persyaratan', 'ketentuan'])) {
            $query = str_replace(['syarat', 'persyaratan', 'ketentuan'], '', $messageLower);
            $query = trim($query);
            return $this->syaratHandler->search($query);
        }

        // UMKM search intent
        if (str_starts_with($messageLower, 'umkm')) {
            $query = trim(substr($message, 4));
            return $this->umkmHandler->search($query);
        }

        // JASA search intent
        if (str_starts_with($messageLower, 'jasa')) {
            $query = trim(substr($message, 4));
            return $this->jasaHandler->search($query);
        }

        // LOKER search intent
        if ($this->matchesIntent($messageLower, ['loker', 'lowongan', 'kerja'])) {
            $query = str_replace(['loker', 'lowongan', 'kerja'], '', $messageLower);
            $query = trim($query);
            return $this->lokerHandler->search($query);
        }

        // Complaint submission intent
        if ($this->matchesIntent($messageLower, ['pengaduan', 'lapor', 'aduan', 'complaint'])) {
            return $this->complaintHandler->initiate($phone);
        }

        // Owner toggle intent
        if ($this->matchesIntent($messageLower, ['toggle', 'aktif', 'nonaktif', 'on', 'off'])) {
            return $this->ownerHandler->initiate($phone);
        }

        // Unknown intent
        return [
            'success' => true,
            'intent' => 'unknown',
            'reply' => $this->getUnknownIntentMessage(),
            'state_update' => null,
        ];
    }

    /**
     * Check if message matches any of the intent keywords
     */
    protected function matchesIntent(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($message, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Menu intent response
     */
    protected function menuIntent(): array
    {
        $menu = "🏛️ *MENU LAYANAN KECAMATAN BESUK*\n\n";
        $menu .= "Silakan pilih layanan yang Anda butuhkan:\n\n";
        $menu .= "1️⃣ *STATUS* - Cek status berkas layanan\n";
        $menu .= "2️⃣ *SYARAT* - Informasi persyaratan layanan\n";
        $menu .= "   Contoh: _syarat kk_, _syarat ktp_\n\n";
        $menu .= "3️⃣ *UMKM [kata kunci]* - Cari produk UMKM\n";
        $menu .= "   Contoh: _umkm bakso_\n\n";
        $menu .= "4️⃣ *JASA [kata kunci]* - Cari penyedia jasa\n";
        $menu .= "   Contoh: _jasa tukang_\n\n";
        $menu .= "5️⃣ *LOKER* - Lihat lowongan kerja\n";
        $menu .= "6️⃣ *PENGADUAN* - Sampaikan keluhan/aduan\n";
        $menu .= "7️⃣ *TOGGLE* - Kelola status lapak/jasa Anda\n\n";
        $menu .= "Ketik *MENU* kapan saja untuk kembali ke menu ini.";

        // Clear any active session
        WhatsappSession::where('phone', request()->input('phone'))
            ->update(['state' => null, 'temp_data' => null]);

        return [
            'success' => true,
            'intent' => 'menu',
            'reply' => $menu,
            'state_update' => null,
        ];
    }

    /**
     * Unknown intent message
     */
    protected function getUnknownIntentMessage(): string
    {
        return "Maaf, saya tidak mengerti pesan Anda. Ketik *MENU* untuk melihat daftar layanan yang tersedia.";
    }
}
