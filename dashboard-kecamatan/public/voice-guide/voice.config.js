/**
 * voice.config.js
 * Konfigurasi utama untuk Voice Guide
 */

window.VoiceConfig = {
    // Global Config
    appName: 'Sistem Informasi Kecamatan',
    regionName: window.APP_WILAYAH_NAMA || 'Kecamatan',

    // Intents (Maksud pengguna)
    INTENT: {
        WELCOME: 'WELCOME',
        STOP: 'STOP',
        NAVIGATE_BERITA: 'NAV_BERITA',
        NAVIGATE_LAYANAN: 'NAV_LAYANAN',
        NAVIGATE_HOME: 'NAV_HOME',
        NAVIGATE_WILAYAH: 'NAV_WILAYAH', // Maps to "Region"
        NAVIGATE_PROFILE: 'NAV_PROFILE',
        NAVIGATE_INFO: 'NAV_INFO',
        NAVIGATE_PENGADUAN: 'NAV_COMPLAINT',
        NAVIGATE_KERJA: 'NAV_KERJA',
        READ_NEWS_ITEM: 'READ_NEWS_ITEM',
        READ_NEWS_DETAIL: 'READ_NEWS_DETAIL', // [NEW] Contextual read
        SEARCH_NEWS: 'SEARCH_NEWS', // [NEW] Multi-page search
        LOGIN: 'LOGIN',
        INFO_HOURS: 'INFO_HOURS',
        FAQ_SEARCH: 'FAQ_SEARCH', // [NEW] for general questions
        UNKNOWN: 'UNKNOWN'
    },

    // Keyword Mapping (Simple regex/includes matching)
    keywords: [
        { intent: 'STOP', words: ['matikan', 'stop', 'berhenti', 'nonaktifkan', 'diam'] },
        { intent: 'NAVIGATE_BERITA', words: ['berita', 'warta', 'kabar', 'informasi'] },
        { intent: 'NAVIGATE_LAYANAN', words: ['layanan', 'pelayanan', 'admin', 'surat', 'ktp', 'kk', 'akta', 'domisili'] },
        { intent: 'NAVIGATE_HOME', words: ['beranda', 'home', 'depan', 'utama', 'awal'] },
        { intent: 'NAVIGATE_WILAYAH', words: ['wilayah', 'pariwisata', 'wisata', 'umkm', 'potensi'] },
        { intent: 'NAVIGATE_KERJA', words: ['kerja', 'jasa', 'loker', 'lowongan', 'tukang', 'direktori'] },
        { intent: 'NAVIGATE_INFO', words: ['info', 'informasi', 'pengumuman', 'hari ini'] },
        { intent: 'NAVIGATE_PENGADUAN', words: ['pengaduan', 'lapor', 'aspirasi', 'keluhan'] },
        { intent: 'LOGIN', words: ['masuk', 'login', 'operator', 'admin'] },
        { intent: 'FAQ_SEARCH', words: ['bantuan', 'cari', 'tanya', 'bagaimana', 'syarat', 'persyaratan'] },
    ],

    // Sentences
    messages: {
        welcome: (region) => `Selamat datang di ${region}. Berikut adalah menu yang tersedia pada halaman ini.`,
        menuItem: (item) => `Menu ${item}.`,
        instruction: "Silakan pilih menu yang Anda inginkan, atau katakan keperluan Anda.",
        stopConfirmation: "Baik, suara panduan dinonaktifkan.",
        navigateHome: "Kembali ke halaman utama.",
        navigateLogin: "Mengarahkan ke halaman Login Petugas.",
        infoHours: "Kami buka Senin sampai Kamis pukul 08.00 sampai 15.30, dan Jumat pukul 08.00 sampai 14.30 WIB.",
        fallbackTitle: "Panduan Suara Aktif",
        fallbackText: "Audio tidak tersedia di perangkat ini. Silakan gunakan menu visual."
    }
};

console.log('[VoiceGuide] Config Loaded');
