/**
 * voice.recognition.js
 * Wrapper untuk WebkitSpeechRecognition
 */

window.VoiceRecognition = (function () {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    let recognition = null;
    let restartTimer = null;

    function init() {
        if (!SpeechRecognition) {
            console.warn("Browser does not support SpeechRecognition");
            return false;
        }

        recognition = new SpeechRecognition();
        recognition.lang = 'id-ID';
        recognition.interimResults = false;
        recognition.maxAlternatives = 1;

        recognition.onstart = () => {
            window.VoiceState.setListening(true);
            document.dispatchEvent(new CustomEvent('voice-listen-start'));
        };

        recognition.onend = () => {
            window.VoiceState.setListening(false);
            document.dispatchEvent(new CustomEvent('voice-listen-end'));

            // Auto-restart if Continuous Mode is ON and NOT Speaking
            if (window.VoiceState.isContinuous() && !window.VoiceState.isSpeaking()) {
                clearTimeout(restartTimer);
                restartTimer = setTimeout(() => {
                    try { recognition.start(); } catch (e) { }
                }, 500);
            }
        };

        recognition.onresult = (event) => {
            const transcript = event.results[0][0].transcript;
            console.log("Heard:", transcript);

            // Dispatch to Init/Controller to handle parsing
            document.dispatchEvent(new CustomEvent('voice-command', { detail: { text: transcript } }));
        };

        recognition.onerror = (event) => {
            console.log("Recognition Error:", event.error);
            if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                window.VoiceState.setActive(false); // Force stop if permission denied
            }
        };

        return true;
    }

    return {
        init: init,
        start: () => {
            if (recognition) {
                try { recognition.start(); } catch (e) { }
            }
        },
        stop: () => {
            if (recognition) {
                try { recognition.stop(); } catch (e) { }
            }
        },
        abort: () => {
            if (recognition) recognition.abort();
        }
    };
})();

console.log('[VoiceGuide] Recognition Module Loaded');
