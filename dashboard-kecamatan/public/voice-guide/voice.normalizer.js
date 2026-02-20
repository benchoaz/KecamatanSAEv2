/**
 * voice.normalizer.js
 * Modul pembersih teks - PURE TEXT PROCESSING
 * Adaptasi fungsi normalizeHumanSpeech dari request user
 * ke dalam format IIFE untuk kompatibilitas existing loader.
 */

window.VoiceNormalizer = (function () {
    const Lexicon = window.VoiceLexicon._raw; // Akses raw data structure

    /* ===============================
       NORMALIZER – HUMAN SPEECH
       =============================== */
    function normalizeHumanSpeech(input) {
        if (!input || typeof input !== 'string') {
            return {
                raw: '',
                normalized: '',
                tokens: []
            };
        }

        let text = input.toLowerCase();

        /* -------------------------------
           1. HAPUS TANDA BACA & SIMBOL
           ------------------------------- */
        // Note: \p{L} support mungkin terbatas di beberapa browser lama, 
        // tapi modern browser sudah support unicode property escapes.
        // Jika error, kita fallback ke regex aman.
        try {
            text = text.replace(/[^\p{L}\p{N}\s]/gu, ' ');
        } catch (e) {
            text = text.replace(/[^a-z0-9\s]/g, ' '); // Fallback
        }

        /* -------------------------------
           2. PERBAIKI SALAH UCAP UMUM
           ------------------------------- */
        Object.entries(Lexicon.corrections).forEach(([wrong, correct]) => {
            const re = new RegExp(`\\b${wrong}\\b`, 'g');
            text = text.replace(re, correct);
        });

        /* -------------------------------
           3. HAPUS KATA SOPAN / FILLER
           ------------------------------- */
        Lexicon.fillers.forEach(filler => {
            const re = new RegExp(`\\b${filler}\\b`, 'g');
            text = text.replace(re, ' ');
        });

        /* -------------------------------
           4. NORMALISASI KATA KERJA
           (stemming ringan manual)
           ------------------------------- */
        text = text
            .replace(/\bmembacakan\b/g, 'baca')
            .replace(/\bbacakan\b/g, 'baca')
            .replace(/\bbacain\b/g, 'baca')
            .replace(/\bmembaca\b/g, 'baca')
            .replace(/\bdengarkan\b/g, 'dengar')
            .replace(/\bdengerin\b/g, 'dengar')
            .replace(/\bmelihat\b/g, 'lihat');

        /* -------------------------------
           5. HAPUS KATA GANDA
           ------------------------------- */
        text = text.replace(/\b(\w+)\s+\1\b/g, '$1');

        /* -------------------------------
           6. RAPALKAN SPASI
           ------------------------------- */
        text = text.replace(/\s+/g, ' ').trim();

        /* -------------------------------
           7. TOKENISASI
           ------------------------------- */
        const tokens = text.split(' ').filter(Boolean);

        return {
            raw: input,
            normalized: text,
            tokens
        };
    }

    // Expose as 'clean' to match interface used by Parser, returning only string
    // Or prefer exposing the full object for smarter parsing?
    // Let's expose both.

    return {
        // Alias 'clean' untuk kompatibilitas code lama (return string only)
        clean: (text) => normalizeHumanSpeech(text).normalized,

        // Fungsi utama full power
        normalizeHumanSpeech: normalizeHumanSpeech
    };
})();
