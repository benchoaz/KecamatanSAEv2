/**
 * voice.speech.js
 * Wrapper untuk Web Speech API (Synthesis)
 */

window.VoiceSpeech = (function () {
    let synth = window.speechSynthesis;
    let voices = [];

    function loadVoices() {
        if (!synth) return;
        voices = synth.getVoices();
    }

    if (synth) {
        if (synth.onvoiceschanged !== undefined) {
            synth.onvoiceschanged = loadVoices;
        }
        loadVoices();
    }

    // Visual Feedback (Toast/Fallback)
    function showFallback() {
        // Simple Dispatch event, UI handled in init/actions
        document.dispatchEvent(new CustomEvent('voice-error-tts'));
    }

    // Create Utterance with best Indonesian voice
    function createUtterance(text) {
        const utterance = new SpeechSynthesisUtterance(text);

        // Priority: exact ID -> partial ID -> default
        const voice = voices.find(v => v.lang === 'id-ID') ||
            voices.find(v => v.lang.startsWith('id')) ||
            voices[0];

        if (voice) {
            utterance.voice = voice;
            utterance.lang = voice.lang; // Ensure lang matches voice
        } else {
            utterance.lang = 'id-ID'; // Fallback lang
        }

        utterance.rate = 1.0;
        return utterance;
    }

    return {
        speak: (text, onEndCallback) => {
            if (!window.VoiceState.isActive() && !text.includes('nonaktif')) return; // Guard
            if (!synth) { showFallback(); return; }

            // Cancel current speech
            synth.cancel();

            const utterance = createUtterance(text);

            utterance.onstart = () => {
                window.VoiceState.setSpeaking(true);
            };

            utterance.onend = () => {
                window.VoiceState.setSpeaking(false);
                if (onEndCallback) onEndCallback();
            };

            utterance.onerror = (e) => {
                console.error("TTS Error:", e);
                window.VoiceState.setSpeaking(false);
                if (e.error !== 'interrupted' && e.error !== 'canceled') {
                    showFallback();
                }
            };

            synth.speak(utterance);
        },

        stop: () => {
            if (synth) synth.cancel();
            window.VoiceState.setSpeaking(false);
        },

        // Speak list of sentences sequentially
        speakSequence: (sentences, onComplete) => {
            if (!sentences || sentences.length === 0) {
                if (onComplete) onComplete();
                return;
            }

            // Simple recursion to handle sequence
            const playNext = (index) => {
                if (index >= sentences.length) {
                    if (onComplete) onComplete();
                    return;
                }

                if (!window.VoiceState.isActive()) return; // Stop if deactivated mid-sequence

                const utterance = createUtterance(sentences[index]);

                utterance.onend = () => playNext(index + 1);
                utterance.onerror = () => playNext(index + 1); // Skip error items

                synth.speak(utterance);
            };

            synth.cancel();
            playNext(0);
        }
    };
})();

console.log('[VoiceGuide] Speech Module Loaded');
