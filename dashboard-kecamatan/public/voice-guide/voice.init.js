/**
 * voice.init.js
 * Entry Point & Bootstrapper
 * Memuat semua modul dependensi secara berurutan dan menginisialisasi event.
 */

(function () {
    const BASE_PATH = '/voice-guide/';
    const MODULES = [
        'voice.config.js',
        'voice.lexicon.js',     // [NEW]
        'voice.normalizer.js',  // [NEW]
        'voice.intent.rules.js',// [NEW]
        'voice.state.js',
        'voice.speech.js',
        'voice.recognition.js',
        'voice.parser.js',
        'voice.actions.js'
    ];

    // Helper to load script safely
    function loadScript(src) {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = BASE_PATH + src;
            script.async = false; // Maintain order
            script.defer = true;
            script.onload = () => resolve(src);
            script.onerror = () => reject(src);
            document.head.appendChild(script);
        });
    }

    // Sequence Loader
    async function bootstrap() {
        try {
            for (const mod of MODULES) {
                await loadScript(mod);
            }
            console.log('[VoiceGuide] All modules loaded. Initializing...');
            initializeSystem();
        } catch (error) {
            console.error('[VoiceGuide] Failed to load module:', error);
        }
    }

    function initializeSystem() {
        // Quick Alias
        window.VoiceConfig = window.VoiceConfig || {}; // Should be loaded
        const State = window.VoiceState;
        const Recognition = window.VoiceRecognition;
        const Actions = window.VoiceActions;
        const Speech = window.VoiceSpeech;

        // 1. Init State
        State.init();

        // 2. Init Recognition
        const hasMic = Recognition.init();

        // 3. Bind UI Elements
        bindToggleBtn();

        // 4. Global Event Listeners

        // Handle recognized text
        document.addEventListener('voice-command', (e) => {
            const text = e.detail.text;
            const parseResult = window.VoiceParser.parse(text);
            Actions.execute(parseResult);
        });

        // Handle State Changes
        document.addEventListener('voice-state-change', (e) => {
            const active = e.detail.isActive;
            console.log('[VoiceInit] State change detected. Active:', active);
            updateVisuals(active);

            if (active) {
                console.log('[VoiceInit] Voice Guide ACTIVATED');

                // 🔊 AUDIO FEEDBACK: Announce activation
                Speech.speak("Pemandu suara aktif");

                // CRITICAL FIX: Force reset welcome flag EVERY time
                State.setPlayedWelcome(false);
                console.log('[VoiceInit] Welcome flag FORCED to false');

                Recognition.start();

                // SAFETY DELAY: Ensure SpeechSynthesis is ready and UI has settled
                console.log('[VoiceInit] Triggering WELCOME intent with safety delay...');
                setTimeout(() => {
                    if (State.isActive()) {
                        Actions.execute({ intent: window.VoiceConfig.INTENT.WELCOME });
                    }
                }, 1200); // Increased to allow activation announcement
            } else {
                console.log('[VoiceInit] Voice Guide DEACTIVATED');

                // 🔊 AUDIO FEEDBACK: Announce deactivation
                Speech.speak("Pemandu suara nonaktif");

                // Stop after announcement
                setTimeout(() => {
                    Recognition.stop();
                    Speech.stop();
                }, 1500);
            }
        });

        // Auto-Start Check
        if (State.isActive()) {
            console.log('[VoiceGuide] Auto-starting...');
            updateVisuals(true);
            Recognition.start();
            // Don't play welcome on reload, maybe just a beep or log
        }

        // 5. Check for Pending Actions (e.g. Read News Detail after nav)
        Actions.checkAutoRun();
    }

    function bindToggleBtn() {
        const btn = document.getElementById('btnVoiceGuideToggle') || document.getElementById('voice-guide-btn');
        if (btn) {
            console.log('[VoiceInit] Binding toggle button:', btn.id);

            // Define global handler if not exists
            window.activateVoiceGuide = window.activateVoiceGuide || function () {
                const newState = !window.VoiceState.isActive();
                console.log('[VoiceInit] Button clicked, setting active:', newState);
                window.VoiceState.setActive(newState);
            };

            // Ensure physical click works if it's a standard button without inline onclick
            if (!btn.onclick) {
                btn.addEventListener('click', window.activateVoiceGuide);
            }
        }
    }

    function updateVisuals(isActive) {
        const btn = document.getElementById('btnVoiceGuideToggle') || document.getElementById('voice-guide-btn');
        const ping = document.getElementById('voice-ping');
        const dot = document.getElementById('voice-dot');

        if (!btn) return;

        if (isActive) {
            // Handle Landing Page Button (Orange/Blue)
            if (btn.id === 'btnVoiceGuideToggle') {
                btn.classList.remove('bg-orange-600', 'hover:bg-orange-700', 'bg-blue-600', 'hover:bg-blue-700');
                btn.classList.add('bg-emerald-500', 'hover:bg-emerald-600', 'ring-4', 'ring-emerald-200');
            } else {
                // Detail Page Button
                btn.style.backgroundColor = '#10b981'; // emerald-500
                btn.classList.add('ring-4', 'ring-emerald-200');
            }
            if (ping) ping.classList.remove('hidden');
            if (dot) dot.classList.remove('hidden');
            if (dot) dot.classList.add('bg-emerald-400');
        } else {
            if (btn.id === 'btnVoiceGuideToggle') {
                btn.classList.remove('bg-emerald-500', 'hover:bg-emerald-600', 'ring-4', 'ring-emerald-200');
                btn.classList.add('bg-orange-600', 'hover:bg-orange-700');
            } else {
                // Detail Page Button
                btn.style.backgroundColor = '#f97316'; // orange-500 (Accessibility Orange)
                btn.classList.remove('ring-4', 'ring-emerald-200');
            }
            if (ping) ping.classList.add('hidden');
            if (dot) dot.classList.add('hidden');
        }
    }

    // Start Boot
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootstrap);
    } else {
        bootstrap();
    }

})();
