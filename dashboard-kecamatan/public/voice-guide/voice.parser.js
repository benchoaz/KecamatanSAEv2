/**
 * voice.parser.js
 * Natural Language Processing (Heuristic & Normalize)
 * Menggunakan Normalizer & IntentRules untuk pemahaman lebih baik.
 */

window.VoiceParser = (function () {
    const Normalizer = window.VoiceNormalizer;
    const IntentRules = window.VoiceIntentRules;
    const Config = window.VoiceConfig;

    function parse(text) {
        if (!text) return { intent: Config.INTENT.UNKNOWN, original: "" };

        // 1. Normalize
        const normalized = Normalizer.clean(text);
        console.log(`[VoiceParser] Original: "${text}" -> Normalized: "${normalized}"`);

        // 2. Intent Heuristics (Rules & Scoring)
        const ruleMatch = IntentRules.findMatch(normalized);

        // 3. News/Item Specific Logic (Variable Extraction)
        // Cek apakah user ingin membaca berita/item spesifik: "baca umkm keripik" or "baca berita banjir"
        const topic = extractTopic(normalized);
        const targetLink = findTargetLink(topic);

        // --- CONTEXTUAL CHECK: Are we on a detail page? ---
        const isDetailPage = document.querySelector('article') !== null || window.location.pathname.includes('/umkm/');
        const isContextualRequest = ['ini', 'itu', 'artikel', 'isi', 'detail', 'selengkapnya', 'produk'].includes(topic);

        // DECISION LOGIC

        // A. CONTEXTUAL: Jika di halaman artikel/produk & minta baca "ini" atau "isi"
        if (isDetailPage && (isContextualRequest || !topic) && ruleMatch && ruleMatch.originalIntent === "NAV_NEWS") {
            console.log(`[VoiceParser] 📄 Contextual Detail requested.`);
            return {
                intent: Config.INTENT.READ_NEWS_DETAIL,
                original: text
            };
        }

        // B. DIRECT FOUND: Judul berita/item ditemukan di halaman ini
        if (targetLink) {
            console.log(`[VoiceParser] 🎯 Target Link FOUND: "${targetLink.innerText}"`);
            return {
                intent: Config.INTENT.READ_NEWS_ITEM,
                payload: targetLink,
                original: text
            };
        }

        // C. NOT FOUND BUT HAS TOPIC: Intent News + Topic -> SEARCH_NEWS
        if (ruleMatch && ruleMatch.originalIntent === "NAV_NEWS" && topic && topic.length > 3) {
            console.log(`[VoiceParser] 🔍 Topic "${topic}" NOT FOUND on this page. Escalating to SEARCH_NEWS.`);
            return {
                intent: Config.INTENT.SEARCH_NEWS,
                payload: { keyword: topic }, // Pass keyword for next page
                original: text
            };
        }

        // D. GENERIC MATCH: "Baca berita" (Tanpa topik spesifik atau topik terlalu pendek)
        if (ruleMatch) {
            console.log(`[VoiceParser] Match Rule: ${ruleMatch.originalIntent} (Score: ${ruleMatch.score})`);
            return {
                intent: ruleMatch.intent,
                original: text,
                matched: ruleMatch.originalIntent
            };
        }

        // 4. Fallback: Search Specific News directly (Legacy fallback check)
        // Removed validation here because Step A covers it.

        // 5. Unknown -> FAQ Search fallback default (di Actions)
        return {
            intent: Config.INTENT.UNKNOWN,
            original: text,
            normalized: normalized
        };
    }

    // Helper: Extract variable topic from sentence
    function extractTopic(normalizedText) {
        // Remove known Intent Keywords to isolate the "Subject"
        return normalizedText
            .replace(/\bread\b/g, '')     // Key: read
            .replace(/\bnews\b/g, '')     // Key: news
            .replace(/\bprofile\b/g, '')  // Key: profile
            .replace(/\bcontact\b/g, '')  // Key: contact
            .replace(/\bmenu\b/g, '')     // Key: menu
            .replace(/\bservice\b/g, '')  // Key: service
            .replace(/\bposition\b/g, '') // Key: position
            .replace(/\bcontextual\b/g, '')
            .trim();
    }

    function findTargetLink(cleanText) {
        console.log('[VoiceParser] findTargetLink for:', cleanText);

        if (!cleanText || cleanText.length < 3) return null;

        // 1. Check NEWS (Standard anchor)
        const newsLinks = document.querySelectorAll('#berita h3 a');
        for (let link of newsLinks) {
            if (link.innerText.toLowerCase().includes(cleanText)) return link;
        }

        // 2. Check UMKM (Card matching)
        const umkmTitles = document.querySelectorAll('#umkm h4');
        for (let title of umkmTitles) {
            if (title.innerText.toLowerCase().includes(cleanText)) {
                // Return the "Detail" button inside this card
                const card = title.closest('.group');
                const detailBtn = card ? card.querySelector('a[href*="/umkm/"]') : null;
                if (detailBtn) return detailBtn;
            }
        }

        // 3. Check LOKER (Card matching)
        const jobTitles = document.querySelectorAll('#umkm h4'); // Fixed selector context if in same section
        for (let title of jobTitles) {
            if (title.innerText.toLowerCase().includes(cleanText)) {
                const card = title.closest('.group');
                const waLink = card ? card.querySelector('a[href*="wa.me"]') : null;
                if (waLink) return waLink;
            }
        }

        // 4. Check LAYANAN (Modal trigger)
        const serviceTitles = document.querySelectorAll('#layanan h3');
        for (let title of serviceTitles) {
            if (title.innerText.toLowerCase().includes(cleanText)) {
                const card = title.closest('.card');
                const actionBtn = card ? card.querySelector('button') : null;
                if (actionBtn) return actionBtn;
            }
        }

        console.log('[VoiceParser] ⏸️ No target match found.');
        return null;
    }

    return {
        parse: parse
    };
})();

console.log('[VoiceGuide] Parser Module Loaded (Enhanced NLU)');
