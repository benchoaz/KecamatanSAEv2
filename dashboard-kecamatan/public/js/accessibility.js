/**
 * ACCESSIBILITY CORE v3.0 - Javascript
 * Strictly Following Mandatory Technical Architecture
 */

(function () {
    // --- MANDATORY STATE ---
    const accessibilityState = {
        theme: 'default',
        contrast: 'normal',
        fontSize: 'normal',
        dyslexia: false,
        underlineLinks: false,
        tts: false
    };

    const ROOT = document.documentElement;

    // --- MANDATORY PERSISTENCE ---
    function saveState() {
        localStorage.setItem('a11y', JSON.stringify(accessibilityState));
        applyState();
    }

    function loadState() {
        const saved = localStorage.getItem('a11y');
        if (saved) {
            Object.assign(accessibilityState, JSON.parse(saved));
        }
        applyState();
    }

    // --- MANDATORY APPLY LOGIC ---
    function applyState() {
        // 1️⃣ MODE TAMPILAN
        ROOT.dataset.theme = accessibilityState.theme;

        // 2️⃣ UKURAN TEKS
        ROOT.dataset.font = accessibilityState.fontSize;

        // 3️⃣ OTHER TOGGLES
        ROOT.dataset.dyslexia = accessibilityState.dyslexia;
        ROOT.dataset.underlineLinks = accessibilityState.underlineLinks;

        // Update UI Active States
        renderActiveStates();
    }

    // --- MANDATORY ACTIONS ---
    window.setGrayMode = function () {
        accessibilityState.theme = accessibilityState.theme === 'gray' ? 'default' : 'gray';
        saveState();
    };

    window.setHighContrast = function () {
        accessibilityState.theme = accessibilityState.theme === 'high-contrast' ? 'default' : 'high-contrast';
        saveState();
    };

    window.setDarkMode = function () {
        accessibilityState.theme = accessibilityState.theme === 'dark' ? 'default' : 'dark';
        saveState();
    };

    window.setFontSize = function (size) {
        accessibilityState.fontSize = accessibilityState.fontSize === size ? 'normal' : size;
        saveState();
    };

    window.toggleDyslexia = function () {
        accessibilityState.dyslexia = !accessibilityState.dyslexia;
        saveState();
    };

    window.toggleUnderlineLinks = function () {
        accessibilityState.underlineLinks = !accessibilityState.underlineLinks;
        saveState();
    };

    window.toggleTTS = function () {
        accessibilityState.tts = !accessibilityState.tts;
        if (!accessibilityState.tts) {
            window.speechSynthesis.cancel();
        }
        saveState();
    };

    window.resetAccessibility = function () {
        if (!confirm('Reset semua pengaturan aksesibilitas?')) return;
        localStorage.removeItem('a11y');
        location.reload();
    };

    // --- MANDATORY TTS (SUARA) ---
    let speech;
    function speak(text) {
        if (!accessibilityState.tts) return;

        // COORDINATION: Skip if Voice Guide is active and speaking
        if (window.VoiceState && window.VoiceState.isSpeaking()) return;

        if (speech) window.speechSynthesis.cancel();

        speech = new SpeechSynthesisUtterance(text);
        speech.lang = 'id-ID';
        window.speechSynthesis.speak(speech);
    }

    // Aktif saat focus
    document.addEventListener('focusin', (e) => {
        if (!accessibilityState.tts) return;

        // Debounce to prevent chatter
        clearTimeout(window.a11yFocusTimer);
        window.a11yFocusTimer = setTimeout(() => {
            const label =
                e.target.getAttribute('aria-label') ||
                e.target.innerText ||
                e.target.placeholder ||
                e.target.value;

            if (label && label.trim().length > 0) speak(label.trim());
        }, 150);
    });

    // --- UI GENERATION (WCAG AUDIT READY) ---
    function createWidget() {
        const panel = document.createElement('div');
        panel.id = 'accessibility-panel';
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-modal', 'true');
        panel.setAttribute('aria-label', 'Menu Aksesibilitas');

        panel.innerHTML = `
      <div class="a11y-header">
        <h2 class="a11y-title"><i class="fas fa-universal-access"></i> Aksesibilitas</h2>
        <button id="a11y-close" class="a11y-close-btn" aria-label="Tutup Menu Aksesibilitas" tabindex="0">&times;</button>
      </div>
      
      <div class="a11y-body">
        <div class="a11y-group">
          <div class="a11y-group-title">Mode Tampilan</div>
          <div class="a11y-grid">
            <button class="a11y-btn" onclick="setGrayMode()" aria-label="Aktifkan mode abu-abu" tabindex="0">
              <i class="fas fa-filter"></i> Abu-abu
            </button>
            <button class="a11y-btn" onclick="setHighContrast()" aria-label="Aktifkan mode kontras tinggi" tabindex="0">
              <i class="fas fa-adjust"></i> Kontras ++
            </button>
            <button class="a11y-btn" onclick="setDarkMode()" aria-label="Aktifkan mode gelap" tabindex="0">
              <i class="fas fa-moon"></i> Mode Gelap
            </button>
            <button class="a11y-btn" onclick="toggleDyslexia()" aria-label="Aktifkan font disleksia" tabindex="0">
              <i class="fas fa-font"></i> Font Disleksia
            </button>
          </div>
        </div>

        <div class="a11y-group">
          <div class="a11y-group-title">Ukuran Teks</div>
          <div class="a11y-grid">
            <button class="a11y-btn" onclick="setFontSize('large')" aria-label="Teks besar" tabindex="0">
              <i class="fas fa-plus"></i> Besar
            </button>
            <button class="a11y-btn" onclick="setFontSize('xlarge')" aria-label="Teks ekstra besar" tabindex="0">
              <i class="fas fa-expand-arrows-alt"></i> Ekstra Besar
            </button>
          </div>
        </div>

        <div class="a11y-group">
          <div class="a11y-group-title">Bantuan Lain</div>
          <div class="a11y-grid">
            <button class="a11y-btn" onclick="toggleUnderlineLinks()" aria-label="Tampilkan garis bawah link" tabindex="0">
              <i class="fas fa-link"></i> Garis Link
            </button>
            <button class="a11y-btn" onclick="toggleTTS()" aria-label="Aktifkan pembantu suara" tabindex="0">
              <i class="fas fa-volume-up"></i> Suara (TTS)
            </button>
          </div>
        </div>
      </div>

      <div class="a11y-footer">
        <button class="a11y-reset-btn" onclick="resetAccessibility()" aria-label="Atur ulang semua pengaturan" tabindex="0">
          <i class="fas fa-undo"></i> Atur Ulang
        </button>
      </div>
    `;

        document.body.appendChild(panel);
        bindEvents();
    }

    function bindEvents() {
        const panel = document.getElementById('accessibility-panel');
        const toggle = document.getElementById('accessibility-toggle');
        const closeBtn = document.getElementById('a11y-close');

        if (toggle) {
            toggle.onclick = () => {
                panel.classList.toggle('active');
                if (panel.classList.contains('active')) closeBtn.focus();
            };
        }

        closeBtn.onclick = () => {
            panel.classList.remove('active');
            if (toggle) toggle.focus();
        };

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && panel.classList.contains('active')) {
                panel.classList.remove('active');
                if (toggle) toggle.focus();
            }
        });
    }

    function renderActiveStates() {
        const panel = document.getElementById('accessibility-panel');
        if (!panel) return;

        panel.querySelectorAll('.a11y-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.setAttribute('aria-pressed', 'false');
        });

        // Theme Buttons
        if (accessibilityState.theme !== 'default') {
            const btnMap = { 'gray': 0, 'high-contrast': 1, 'dark': 2 };
            const idx = btnMap[accessibilityState.theme];
            if (idx !== undefined) {
                const btn = panel.querySelectorAll('.a11y-group:nth-child(1) .a11y-btn')[idx];
                btn.classList.add('active');
                btn.setAttribute('aria-pressed', 'true');
            }
        }

        // Dyslexia
        if (accessibilityState.dyslexia) {
            const btn = panel.querySelectorAll('.a11y-group:nth-child(1) .a11y-btn')[3];
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
        }

        // Font Size
        if (accessibilityState.fontSize !== 'normal') {
            const idx = accessibilityState.fontSize === 'large' ? 0 : 1;
            const btn = panel.querySelectorAll('.a11y-group:nth-child(2) .a11y-btn')[idx];
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
        }

        // Other Toggles
        if (accessibilityState.underlineLinks) {
            const btn = panel.querySelectorAll('.a11y-group:nth-child(3) .a11y-btn')[0];
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
        }

        if (accessibilityState.tts) {
            const btn = panel.querySelectorAll('.a11y-group:nth-child(3) .a11y-btn')[1];
            btn.classList.add('active');
            btn.setAttribute('aria-pressed', 'true');
        }
    }

    // --- INITIALIZE ---
    document.addEventListener('DOMContentLoaded', () => {
        loadState();
        createWidget();
    });
})();
