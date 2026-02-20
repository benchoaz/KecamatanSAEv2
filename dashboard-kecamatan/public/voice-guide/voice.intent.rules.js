/**
 * voice.intent.rules.js
 * Logika penentuan maksud (Intent) berdasarkan skor dan aturan.
 * Menggantikan simple keyword matching dengan sistem bobot.
 */

window.VoiceIntentRules = (function () {
    const Config = window.VoiceConfig;
    // Pastikan Config sudah load. Jika belum, kita inject nanti via init.
    // Tapi karena struktur load berurutan, aman.

    // Definisi Rules
    // Setiap Intent punya 'triggers' (kata kunci)
    // weight: Bobot kata (semakin unik semakin tinggi)
    // required: Kata yang HARUS ada (opsional)

    // --- VERB-FIRST DOMAIN RESOLUTION CONFIG (STRICTER) ---

    // CRITICAL: Include English canonical forms (post-normalization)
    const VERBS_NEWS = ['baca', 'bacakan', 'bacain', 'buka', 'berita', 'info', 'kabar', 'kegiatan', 'tentang', 'terkait', 'mengenai', 'read', 'news', 'isi', 'detail', 'selengkapnya', 'umkm', 'loker', 'lowongan', 'produk', 'jualan', 'dagangan'];
    const VERBS_SERVICE = ['syarat', 'cara', 'prosedur', 'biaya', 'urus', 'pengurusan', 'daftar', 'bikin', 'membuat', 'buat', 'ajukan', 'pengajuan'];
    const VERBS_COMPLAINT = ['lapor', 'adukan', 'keluhan', 'komplain'];

    // Nouns that are ambiguous (can be news or service context)
    const NOUNS_AMBIGUOUS = ['umkm', 'ktp', 'kk', 'akta', 'surat', 'nikah', 'cerai', 'pindah', 'domisili', 'ijin', 'izin', 'blt', 'dd', 'bantuan', 'region'];

    const RULES = [
        {
            intent: "STOP_GUIDE",
            triggers: ["stop", "audio", "matikan", "berhenti", "diam"],
            minScore: 1
        },
        {
            intent: "NAV_HOME",
            triggers: ["back", "home", "beranda", "depan", "utama"],
            minScore: 1
        },
        {
            intent: "NAV_NEWS",
            triggers: ["news", "read", "berita", "kabar", "detail", "isi", "umkm", "loker", "produk"], // Broadened triggers
            minScore: 1
        },
        {
            intent: "NAV_SERVICES",
            triggers: ["service"],
            minScore: 1
        },
        {
            intent: "NAV_PROFILE",
            triggers: ["profile", "profil", "tentang"],
            minScore: 1
        },
        {
            intent: "NAV_REGION",
            triggers: ["region", "wilayah", "wisata", "potensi"],
            minScore: 1
        },
        {
            intent: "NAV_INFO",
            triggers: ["info", "informasi", "pengumuman", "hari ini"],
            minScore: 1
        },
        {
            intent: "NAV_COMPLAINT",
            triggers: ["pengaduan", "lapor", "aspirasi", "keluhan"],
            minScore: 1
        },
        {
            intent: "NAV_LOGIN",
            triggers: ["login", "masuk", "admin", "operator"],
            minScore: 1
        },
        {
            intent: "INFO_HOURS",
            triggers: ["hours", "jam", "buka", "tutup", "jadwal"],
            minScore: 1
        },
        {
            intent: "FAQ_SEARCH",
            triggers: ["contact", "tanya", "apa", "syarat"],
            minScore: 1
        }
    ];

    function evaluate(normalizedText) {
        console.log(`[IntentRules] Evaluating: "${normalizedText}"`);
        const tokens = normalizedText.split(' ');

        // --- 1. PRIORITY: VERB CHECK ---

        // A. NEWS VERBS (Highest Priority)
        const hasNewsVerb = tokens.some(t => VERBS_NEWS.includes(t));
        if (hasNewsVerb) {
            const hasStrongServiceVerb = tokens.some(t => ['syarat', 'cara', 'prosedur', 'biaya', 'urus', 'mengurus'].includes(t));
            if (!hasStrongServiceVerb) {
                console.log('[IntentRules] 📰 News Verb Detected -> Force NAV_NEWS');
                return { intent: mapInternalIntentToConfig("NAV_NEWS"), score: 10, originalIntent: "NAV_NEWS" };
            }
        }

        // B. SERVICE VERBS
        const hasServiceVerb = tokens.some(t => VERBS_SERVICE.includes(t));
        if (hasServiceVerb) {
            return { intent: mapInternalIntentToConfig("FAQ_SEARCH"), score: 10, originalIntent: "FAQ_SEARCH" };
        }

        // C. COMPLAINT VERBS
        const hasComplaintVerb = tokens.some(t => VERBS_COMPLAINT.includes(t));
        if (hasComplaintVerb) {
            return { intent: mapInternalIntentToConfig("NAV_COMPLAINT"), score: 10, originalIntent: "NAV_COMPLAINT" };
        }

        // --- 2. AMBIGUOUS NOUN CHECK ---
        const hasAmbiguousNoun = tokens.some(t => NOUNS_AMBIGUOUS.includes(t));
        if (hasAmbiguousNoun) {
            return { intent: mapInternalIntentToConfig("NAV_NEWS"), score: 5, originalIntent: "NAV_NEWS" };
        }

        // --- 3. FALLBACK: ORIGINAL SCORING LOGIC ---
        let bestMatch = null;
        let highestScore = 0;

        for (const rule of RULES) {
            let score = 0;
            tokens.forEach(token => { if (rule.triggers.includes(token)) score += 1; });
            if (score >= rule.minScore && score > highestScore) {
                highestScore = score;
                bestMatch = {
                    intent: mapInternalIntentToConfig(rule.intent),
                    score: score,
                    originalIntent: rule.intent
                };
            }
        }
        return bestMatch;
    }

    function mapInternalIntentToConfig(ruleIntent) {
        const C = window.VoiceConfig.INTENT;
        const MAPPING = {
            "STOP_GUIDE": C.STOP,
            "NAV_HOME": C.NAVIGATE_HOME,
            "NAV_NEWS": C.NAVIGATE_BERITA,
            "NAV_SERVICES": C.NAVIGATE_LAYANAN,
            "NAV_INFO": C.NAVIGATE_INFO,
            "NAV_COMPLAINT": C.NAVIGATE_PENGADUAN,
            "NAV_PROFILE": C.NAVIGATE_PROFILE,
            "NAV_REGION": C.NAVIGATE_WILAYAH,
            "NAV_LOGIN": C.LOGIN,
            "INFO_HOURS": C.INFO_HOURS,
            "FAQ_SEARCH": C.FAQ_SEARCH
        };
        return MAPPING[ruleIntent] || C.UNKNOWN;
    }

    return {
        findMatch: evaluate
    };
})();
