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
        $messageLower = strtolower(trim($message));

        // Menu intent
        if ($this->matchesIntent($messageLower, ['menu', 'help', 'bantuan'])) {
            return $this->menuIntent();
        }

        // --- NUMERIC SELECTION (Top Level) ---
        if ($this->isSelection($messageLower, '1')) {
            return [
                'success' => true,
                'intent' => 'menu_admin',
                'reply' => "📄 *LAYANAN ADMINISTRASI*\n\n" .
                    "Silakan pilih:\n" .
                    "1️⃣ *Syarat* - Info syarat pembuatan berkas\n" .
                    "2️⃣ *Status* - Cek status berkas Anda\n" .
                    "3️⃣ *Permohonan* - Kirim permohonan berkas baru\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => 'MENU_ADMIN',
            ];
        }

        if ($this->isSelection($messageLower, '2')) {
            return [
                'success' => true,
                'intent' => 'menu_ekonomi',
                'reply' => "💰 *LOKER & UMKM*\n\n" .
                    "Silakan pilih:\n" .
                    "1️⃣ *UMKM* - Cari produk unggulan desa\n" .
                    "2️⃣ *Loker* - Cari lowongan kerja\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => 'MENU_EKONOMI',
            ];
        }

        if ($this->isSelection($messageLower, '3')) {
            return [
                'success' => true,
                'intent' => 'jasa_prompt',
                'reply' => "🔧 *LAYANAN JASA*\n\n" .
                    "Ketik jasa yang Anda butuhkan.\n" .
                    "Contoh: _jasa tukang_, _jasa pijat_\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => 'MENU_JASA',
            ];
        }

        if ($this->isSelection($messageLower, '4')) {
            return $this->complaintHandler->initiate($phone, 'pengaduan');
        }

        if ($this->isSelection($messageLower, '5')) {
            return $this->ownerHandler->initiate($phone);
        }

        // --- KEYWORD FALLBACKS ---

        // Direct PIN check (6 digits)
        if (preg_match('/^[0-9]{6}$/', $messageLower)) {
            return $this->statusHandler->handle($phone, $messageLower);
        }

        // Status check intent
        if ($this->matchesIntent($messageLower, ['status', 'cek', 'lacak'])) {
            $query = trim(str_replace(['status', 'cek', 'lacak'], '', $messageLower));
            return $this->statusHandler->handle($phone, $query ?: null);
        }

        // SYARAT (requirements) intent
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

        // --- STATE BASED HANDLING ---
        $session = WhatsappSession::where('phone', $phone)->first();
        if ($session && $session->state === 'MENU_ADMIN') {
            if ($this->isSelection($messageLower, '1')) {
                return $this->syaratHandler->search(null);
            }
            if ($this->isSelection($messageLower, '2')) {
                return $this->statusHandler->handle($phone, null);
            }
            if ($this->isSelection($messageLower, '3')) {
                return $this->complaintHandler->initiate($phone, 'pelayanan');
            }
        }

        // Owner toggle intent
        if ($this->matchesIntent($messageLower, ['toggle', 'aktif', 'nonaktif', 'on', 'off', 'kelola'])) {
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
        $menu .= "Silakan pilih layanan (Ketik angka):\n\n";
        $menu .= "1️⃣ *Administrasi* (Syarat & Status Berkas)\n";
        $menu .= "2️⃣ *Loker & UMKM* (Kerja & Produk Desa)\n";
        $menu .= "3️⃣ *Jasa* (Cari Tukang/Servis)\n";
        $menu .= "4️⃣ *Pengaduan* (Aspirasi Warga)\n";
        $menu .= "5️⃣ *Kelola Data* (Aktif/Nonaktifkan Data Anda)\n\n";
        $menu .= "Ketik *MENU* kapan saja untuk kembali.";

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
     * Map basic numeric strings to technical emojis often sent by WA
     */
    protected function isSelection(string $message, string $number): bool
    {
        $message = trim($message);

        // Pure numeric match
        if ($message === $number)
            return true;

        // Emoji match mapping
        $emojis = [
            '1' => '1️⃣',
            '2' => '2️⃣',
            '3' => '3️⃣',
            '4' => '4️⃣',
            '5' => '5️⃣',
        ];

        return isset($emojis[$number]) && $message === $emojis[$number];
    }

    /**
     * Unknown intent message
     */
    protected function getUnknownIntentMessage(): string
    {
        return "Maaf, saya tidak mengerti pesan Anda. Ketik *MENU* untuk melihat daftar layanan (1-5).";
    }
}
