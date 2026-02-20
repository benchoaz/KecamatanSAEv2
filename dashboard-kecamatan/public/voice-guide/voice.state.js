/**
 * voice.state.js
 * Manajemen State Global untuk Voice Guide
 */

window.VoiceState = (function () {
    // State Internal
    let state = {
        isActive: false,
        isContinuous: false,
        isSpeaking: false,
        isListening: false,
        hasPlayedWelcome: false
    };

    // Load initial state from Storage
    function init() {
        const savedActive = localStorage.getItem('voiceGuideActive');
        if (savedActive === 'true') {
            state.isActive = true;
            state.isContinuous = true;
        }
    }

    // Public API
    return {
        init: init,

        isActive: () => state.isActive,
        isContinuous: () => state.isContinuous,

        setActive: (status) => {
            state.isActive = status;
            state.isContinuous = status;

            // CRITICAL FIX: Reset welcome flag when turning OFF
            if (!status) {
                state.hasPlayedWelcome = false;
            }

            localStorage.setItem('voiceGuideActive', status ? 'true' : 'false');

            // Dispatch event for other modules
            document.dispatchEvent(new CustomEvent('voice-state-change', { detail: { isActive: status } }));
        },

        setSpeaking: (status) => state.isSpeaking = status,
        isSpeaking: () => state.isSpeaking,

        setListening: (status) => state.isListening = status,
        isListening: () => state.isListening,

        setPlayedWelcome: (status) => state.hasPlayedWelcome = status,
        hasPlayedWelcome: () => state.hasPlayedWelcome,

        // Session Persistence for Navigation
        setPendingAction: (action, payload) => {
            try {
                if (action) {
                    sessionStorage.setItem('voicePendingAction', JSON.stringify({ action, payload }));
                } else {
                    sessionStorage.removeItem('voicePendingAction');
                }
            } catch (e) {
                console.warn('[VoiceState] sessionStorage failed', e);
            }
        },
        getPendingAction: () => {
            try {
                const data = sessionStorage.getItem('voicePendingAction');
                return data ? JSON.parse(data) : null;
            } catch (e) {
                console.warn('[VoiceState] getPendingAction failed', e);
                return null;
            }
        },
        clearPendingAction: () => {
            try {
                sessionStorage.removeItem('voicePendingAction');
            } catch (e) {
                console.warn('[VoiceState] clearPendingAction failed', e);
            }
        }
    };
})();

console.log('[VoiceGuide] State Module Loaded');
