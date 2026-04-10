<?php

namespace App\Services\WhatsApp;

use App\Models\WhatsappSession;

class IntentHandler
{
    protected StatusHandler $statusHandler;
    protected SyaratHandler $syaratHandler;
    protected UmkmHandler $umkmHandler;
    protected JasaHandler $jasaHandler;

    protected ComplaintHandler $complaintHandler;
    protected OwnerHandler $ownerHandler;
    protected \App\Services\FaqSearchService $faqSearchService;

    public function __construct(
        StatusHandler $statusHandler,
        SyaratHandler $syaratHandler,
        UmkmHandler $umkmHandler,
        JasaHandler $jasaHandler,
        ComplaintHandler $complaintHandler,
        OwnerHandler $ownerHandler,
        \App\Services\FaqSearchService $faqSearchService
    ) {
        $this->statusHandler = $statusHandler;
        $this->syaratHandler = $syaratHandler;
        $this->umkmHandler = $umkmHandler;
        $this->jasaHandler = $jasaHandler;
        $this->complaintHandler = $complaintHandler;
        $this->ownerHandler = $ownerHandler;
        $this->faqSearchService = $faqSearchService;
    }

    /**
     * Handle incoming message and detect intent
     */
    public function handle(string $phone, string $message): array
    {
        $messageLower = strtolower(trim($message));

        // Menu intent
        if ($this->matchesIntent($messageLower, ['menu', 'help', 'bantuan'])) {
            return [
                'success' => true,
                'intent' => 'menu',
                'reply' => $this->getMainMenu()['reply'],
                'state_update' => null,
            ];
        }

        // --- NUMERIC SELECTION (Top Level) ---
        if ($this->isSelection($messageLower, '1')) {
            // Show submenu for Administrasi
            return [
                'success' => true,
                'intent' => 'administrasi_submenu',
                'reply' => $this->getAdministrasiSubmenu()['reply'],
                'state_update' => 'ADM_SUBMENU',
            ];
        }

        if ($this->isSelection($messageLower, '2')) {
            // Option 2: Langsung ke Etalase Produk UMKM
            $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
            return [
                'success' => true,
                'intent' => 'umkm_produk',
                'reply' => "🛍️ *PRODUK UMKM LOKAL*\n\n" .
                    "Temukan produk olahan dan kerajinan tangan karya warga sekitar:\n\n" .
                    "👉 {$baseUrl}/ekonomi?tab=produk\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        if ($this->isSelection($messageLower, '3')) {
            // Option 3: Langsung ke Direktori Jasa
            $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://localhost'));
            return [
                'success' => true,
                'intent' => 'jasa',
                'reply' => "🔧 *CARI JASA & TENAGA AHLI*\n\n" .
                    "Temukan tukang, ART, ojek, dan tenaga harian di sekitar Anda:\n\n" .
                    "👉 {$baseUrl}/ekonomi?tab=jasa\n\n" .
                    "Atau ketik jenis jasa yang Anda cari:\n" .
                    "Contoh: *jasa tukang*, *jasa ojek*\n\n" .
                    "Ketik *MENU* atau *0* untuk kembali.",
                'state_update' => 'MENU_JASA',
            ];
        }

        if ($this->isSelection($messageLower, '4')) {
            // Option 4: Pengaduan - aspirasi warga
            return $this->complaintHandler->initiate($phone, 'pengaduan');
        }

        if ($this->isSelection($messageLower, '5')) {
            // Option 5: Kelola Data - aktif/nonaktifkan data
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

        // LUPA PIN (Forgot PIN) intent
        if ($this->matchesIntent($messageLower, ['lupa pin', 'lupin', 'forgot pin', 'lupa', 'forget'])) {
            return $this->statusHandler->handleForgotPin($phone);
        }

        // SYARAT (requirements) intent - with link when no specific query
        if (
            str_starts_with($messageLower, 'syarat') ||
            str_starts_with($messageLower, 'buat') ||
            $this->matchesIntent($messageLower, ['persyaratan', 'ketentuan'])
        ) {
            // Extract query - remove "syarat" or "buat" prefix
            $query = str_replace(['syarat', 'persyaratan', 'ketentuan', 'buat'], '', $messageLower);
            $query = trim($query);

            // If just "syarat" or "buat" without query, show link to layanan page
            if (empty($query) || strlen($query) < 2) {
                return $this->getLayananLink();
            }
            return $this->syaratHandler->search($query);
        }

        // UMK search intent - with link when no query
        if ($this->matchesIntent($messageLower, ['umkm', 'umk', 'produk', 'etalase'])) {
            // Extract query from message (remove keyword)
            $query = str_replace(['umkm', 'umk', 'produk', 'etalase'], '', $messageLower);
            $query = trim($query);
            // If just keyword without query, show link
            if (empty($query) || strlen($query) < 2) {
                return $this->getUmkmLink();
            }
            return $this->umkmHandler->search($query);
        }

        // JASA search intent
        if (str_starts_with($messageLower, 'jasa')) {
            $query = trim(substr($message, 4));
            return $this->jasaHandler->search($query);
        }

        // LOKER keyword - redirect ke Jasa/Direktori karena Loker sudah dihapus
        if ($this->matchesIntent($messageLower, ['loker', 'lowongan', 'kerja'])) {
            $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
            return [
                'success' => true,
                'intent' => 'jasa_link',
                'reply' => "🔧 *Direktori Jasa & Tenaga Ahli*\n\n" .
                    "Temukan tukang, tenaga harian, dan penyedia jasa lokal:\n" .
                    "{$baseUrl}/ekonomi?tab=jasa\n\n" .
                    "Ketik *MENU* untuk kembali.",
                'state_update' => null,
            ];
        }

        // Complaint submission intent
        if ($this->matchesIntent($messageLower, ['pengaduan', 'lapor', 'aduan', 'complaint'])) {
            return $this->complaintHandler->initiate($phone);
        }

        // --- STATE BASED HANDLING ---
        $session = WhatsappSession::where('phone', $phone)->first();

        // Administrasi Submenu state
        if ($session && $session->state === 'ADM_SUBMENU') {
            if ($this->isSelection($messageLower, '1') || $messageLower === 'status' || $messageLower === 'cek status') {
                return $this->statusHandler->handle($phone, 'STATUS');
            }
            if ($this->isSelection($messageLower, '2') || $messageLower === 'syarat') {
                return $this->getLayananLink();
            }
            if ($this->isSelection($messageLower, '3') || $messageLower === 'menu' || $messageLower === 'kembali') {
                return $this->getMainMenu();
            }
            // If user types something else, show submenu again
            return $this->getAdministrasiSubmenu();
        }

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

        // Quick Holiday Toggles (Masyarakat Friendly)
        if ($messageLower === 'libur' || $messageLower === 'buka') {
            return $this->ownerHandler->toggleHolidayStatus($phone, $messageLower);
        }

        // --- FAQ NATURAL LANGUAGE FALLBACK ---
        // Try searching FAQs before giving up
        $faqData = $this->faqSearchService->search($messageLower);
        if ($faqData['found']) {
            if (isset($faqData['multiple']) && $faqData['multiple']) {
                $reply = "Ditemukan beberapa topik yang mungkin relevan:\n\n";
                foreach ($faqData['results'] as $i => $res) {
                    $num = $i + 1;
                    $reply .= "{$num}. SYARAT " . strtoupper($res['question']) . "\n";
                }
                $reply .= "\nKetik kata kunci yang lebih spesifik atau pilih dari menu.";
                return [
                    'success' => true,
                    'intent' => 'faq_suggestions',
                    'reply' => $reply,
                    'state_update' => null,
                ];
            }

            $top = $faqData['results'][0];
            return [
                'success' => true,
                'intent' => 'faq_match',
                'reply' => "✅ *{$top['question']}*\n\n{$top['answer']}\n\nKetik *MENU* untuk kembali.",
                'state_update' => null,
            ];
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
     * 
     */
    protected function matchesIntent(string $message, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_starts_with($message, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 
     */
    protected function getMainMenu(): array
    {
        $regionName = strtoupper(appProfile()->region_name ?? 'BESUK');
        $baseUrl = config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev');

        $menu = "MENU LAYANAN KECAMATAN {$regionName}\n\n";
        $menu .= "Silakan pilih layanan (Ketik angka):\n\n";
        $menu .= "1. ADMINISTRASI - Cek Syarat dan Status Berkas\n";
        $menu .= "2. PRODUK UMKM - Belanja Produk & Olahan Warga Lokal\n";
        $menu .= "3. CARI JASA - Tukang, ART, Ojek, Tenaga Harian\n";
        $menu .= "4. PENGADUAN - Aspirasi dan Laporan Warga\n";
        $menu .= "5. KELOLA PROFIL - Kelola Data Jasa / Toko UMKM Anda\n\n";
        $menu .= "Ketik MENU kapan saja untuk kembali.";

        return [
            'success' => true,
            'intent' => 'menu',
            'reply' => $menu,
            'state_update' => null,
        ];
    }

    /**
     * 
     */
    protected function menuIntent(): array
    {
        return $this->getMainMenu();
    }

    /**
     * 
     */
    protected function isSelection(string $message, string $number): bool
    {
        $message = trim($message);

        // Pure numeric match
        if ($message === $number)
            return true;

        // Emoji match mapping
        $emojis = [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
        ];

        return isset($emojis[$number]) && isset($emojis[$message]) && $emojis[$message] === $emojis[$number];
    }

    /**
     * 
     */
    protected function getUnknownIntentMessage(): string
    {
        return "🙏 *Mohon maaf*, saya belum mengenali pesan tersebut.\n\n" .
            "Agar dapat kami layani dengan baik, silakan pilih nomor layanan (1-5) atau ketik *MENU* untuk melihat daftar layanan utama kami.\n\n" .
            "Terima kasih atas pengertiannya! 😊";
    }

    /**
     * 
     */
    protected function getUmkmLink(): array
    {
        $baseUrl = config('app.url');
        $umkmUrl = $baseUrl . '/ekonomi?tab=produk';

        return [
            'success' => true,
            'intent' => 'umkm_link',
            'reply' => "🛍️ *ETALASE PRODUK UMKM*\n\n" .
                "Lihat semua produk pilihan warga {$this->getRegionName()} di:\n\n" .
                "👉 {$umkmUrl}\n\n" .
                "Anda juga bisa ketik nama produk yang dicari.\n" .
                "Contoh: *umkm bakso*\n\n" .
                "Ketik *MENU* atau *0* untuk kembali.",
            'state_update' => 'WAITING_UMKM_SEARCH',
        ];
    }

    /**
     * 
     */
    protected function getLokerLink(): array
    {
        $baseUrl = config('app.url');
        $lokerUrl = $baseUrl . '/loker';
        $daftarUrl = $baseUrl . '/loker/pasang';

        return [
            'success' => true,
            'intent' => 'loker_link',
            'reply' => "LOWONGAN KERJA\n\n" .
                "Lihat info lowongan kerja:\n" .
                "{$lokerUrl}\n\n" .
                "Pasang lowongan kerja:\n" .
                "{$daftarUrl}\n\n" .
                "Anda juga bisa ketik kata kunci.\n" .
                "Contoh: *loker tukang*\n" .
                "Ketik *MENU* untuk kembali.",
            'state_update' => null,
        ];
    }

    /**
     * 
     */
    public function getLayananLink(): array
    {
        $baseUrl = env('PUBLIC_BASE_URL', config('app.url', 'https://babette-nonslanderous-randi.ngrok-free.dev'));
        $layananUrl = $baseUrl . '/#layanan';

        return [
            'success' => true,
            'intent' => 'syarat_link',
            'reply' => "🏛️ *LAYANAN ADMINISTRASI*\n\n" .
                "Silakan pilih layanan yang Anda butuhkan:\n\n" .
                "- syarat ktp - Pembuatan KTP\n" .
                "- syarat kk - Pembuatan KK\n" .
                "- syarat akta - Akta Kelahiran\n" .
                "- syarat domisili - Surat Domisili\n\n" .
                "Ajukan Secara Online:\n"
                . "👉 {$layananUrl}\n\n" .
                "Ketik *MENU* atau *0* untuk kembali.",
            'state_update' => 'ADM_SUBMENU', 
        ];
    }

    /**
     * 
     */
    protected function getRegionName(): string
    {
        $profile = appProfile();
        return $profile->region_name ?? 'Kecamatan';
    }

    /**
     * 
     */
    public function getAdministrasiSubmenu(): array
    {
        $reply = "🏛️ *MENU ADMINISTRASI*\n\n";
        $reply .= "Silakan pilih layanan yang diinginkan:\n\n";
        $reply .= "1. *STATUS* - Lacak Berkas Anda\n";
        $reply .= "2. *SYARAT* - Syarat & Ajukan Online\n";
        $reply .= "3. *MENU* - Kembali ke Menu Utama\n\n";
        $reply .= "Ketik angka *1*, *2*, atau *3*.\n";
        $reply .= "Atau ketik *MENU* kapan saja.";

        return [
            'success' => true,
            'intent' => 'administrasi_submenu',
            'reply' => $reply,
            'state_update' => 'ADM_SUBMENU',
        ];
    }
}
