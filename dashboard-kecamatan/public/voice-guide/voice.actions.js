/**
 * voice.actions.js
 * Handler Maksud (Intents) & Eksekusi UI
 */

window.VoiceActions = (function () {

    function execute(parseResult) {
        const Config = window.VoiceConfig;
        const Intent = Config.INTENT;
        const Speech = window.VoiceSpeech;

        console.log("Executing Intent:", parseResult.intent);

        switch (parseResult.intent) {
            case Intent.STOP:
                Speech.speak(Config.messages.stopConfirmation);
                window.VoiceState.setActive(false);
                break;

            case Intent.WELCOME:
                console.log('[VoiceActions] WELCOME intent received, calling playWelcomeSequence...');
                playWelcomeSequence();
                break;

            case Intent.NAVIGATE_HOME:
                Speech.speak(Config.messages.navigateHome);
                if (window.location.pathname !== '/') {
                    setTimeout(() => window.location.assign('/'), 100);
                } else {
                    navigateTo('top');
                }
                break;

            case Intent.NAVIGATE_BERITA: navigateTo('berita'); readTargetSummary(); break;
            case Intent.NAVIGATE_LAYANAN: navigateTo('layanan'); readServices(); break;
            case Intent.NAVIGATE_WILAYAH:
                Speech.speak("Membuka halaman peta dan potensi wilayah.");
                setTimeout(() => window.location.assign('/wilayah'), 500);
                break;
            case Intent.NAVIGATE_KERJA:
                Speech.speak("Membuka direktori kerja dan jasa warga.");
                setTimeout(() => window.location.assign('/kerja'), 500);
                break;
            case Intent.NAVIGATE_INFO: navigateTo('info-hari-ini'); Speech.speak("Menampilkan informasi hari ini."); break;
            case Intent.NAVIGATE_PENGADUAN: navigateTo('pengaduan'); Speech.speak("Menampilkan layanan pengaduan warga."); break;
            case Intent.NAVIGATE_PROFILE: navigateTo('profil'); Speech.speak("Menampilkan profil kecamatan."); break;

            case Intent.SEARCH_NEWS:
                {
                    const keyword = parseResult.payload.keyword;
                    const isNewsPage = window.location.pathname.includes('/berita') && !window.location.pathname.includes('/berita/');

                    if (isNewsPage) {
                        Speech.speak(`Mohon maaf, saya tidak menemukan berita tentang ${keyword} di halaman ini.`);
                    } else {
                        Speech.speak(`Berita tentang ${keyword} tidak ada di halaman utama. Saya akan mencarinya di daftar berita.`);

                        // Set Pending Action
                        window.VoiceState.setPendingAction('SEARCH_NEWS', { keyword: keyword });

                        // Navigate
                        setTimeout(() => {
                            window.location.assign('/berita');
                        }, 1000);
                    }
                }
                break;

            case Intent.LOGIN:
                Speech.speak(Config.messages.navigateLogin);
                setTimeout(() => {
                    window.location.assign('/login');
                }, 0);
                break;

            case Intent.INFO_HOURS:
                Speech.speak(Config.messages.infoHours);
                break;

            case Intent.READ_NEWS_ITEM:
                {
                    const target = parseResult.payload;
                    const tagName = target.tagName.toLowerCase();

                    if (tagName === 'button') {
                        const title = target.innerText;
                        window.VoiceSpeech.speak(`Membuka: ${title}`);
                        target.click();
                        break;
                    }

                    const url = target.getAttribute('href');
                    const titleForDetail = target.innerText;

                    if (!url || url === '#' || url === 'javascript:void(0)') {
                        window.VoiceSpeech.speak("Maaf, aksi ini tidak memiliki tujuan yang valid.");
                        break;
                    }

                    window.VoiceState.setPendingAction('READ_DETAIL', { title: titleForDetail });
                    window.VoiceSpeech.speak(`Membuka: ${titleForDetail}`);
                    setTimeout(() => window.location.assign(url), 100);
                }
                break;

            case Intent.READ_NEWS_DETAIL:
                // Generalized Detail Reading
                readGenericDetail();
                break;

            case Intent.NAVIGATE_PROFILE:
                navigateTo('profil');
                window.VoiceSpeech.speak("Menampilkan profil kecamatan dan struktur organisasi.");
                break;

            case Intent.FAQ_SEARCH:
                {
                    const originalText = parseResult.original || "";
                    if (originalText) {
                        window.VoiceSpeech.speak("Sedang mencari informasi...", () => {
                            const modal = document.getElementById('publicServiceModal');
                            if (modal && typeof modal.showModal === 'function') modal.showModal();

                            const botInput = document.getElementById('botQuery');
                            const botForm = document.getElementById('publicFaqForm');
                            if (botInput && botForm) {
                                botInput.value = originalText;
                                botForm.dispatchEvent(new Event('submit'));
                            }
                        });
                    }
                }
                break;

            default:
                // Unknown command? Maybe just echo or ignore.
                // Speech.speak("Maaf, saya tidak mengerti.");
                break;
        }
    }

    // --- Auto-Run Check (Called on Init) ---
    function checkAutoRun() {
        const pending = window.VoiceState.getPendingAction();

        if (!pending) return;

        console.log("[VoiceActions] Found Pending Action:", pending.action);

        // CASE 1: READ DETAIL (From Landing match -> Navigate -> Read)
        if (pending.action === 'READ_DETAIL') {
            // Clear immediately
            window.VoiceState.setPendingAction(null);

            // Wait a bit for DOM
            setTimeout(() => {
                const title = pending.payload.title;
                readNewsDetailContent(title);
            }, 1000);
        }

        // CASE 2: SEARCH NEWS (From Landing fail -> Navigate Index -> Search Here)
        if (pending.action === 'SEARCH_NEWS') {
            // Clear immediately
            window.VoiceState.setPendingAction(null);

            setTimeout(() => {
                const keyword = pending.payload.keyword;
                console.log(`[VoiceActions] Executing Pending Search for: "${keyword}"`);

                // Search in /berita list (h3 a, .post-title a)
                const links = document.querySelectorAll('h3 a, .post-title a, .card-title a');
                let foundLink = null;

                for (let link of links) {
                    if (link.innerText.toLowerCase().includes(keyword.toLowerCase())) {
                        foundLink = link;
                        break;
                    }
                }

                if (foundLink) {
                    window.VoiceSpeech.speak(`Saya menemukan berita tentang ${keyword}. Membuka berita...`);

                    // Set Next Pending Action (Read Detail)
                    window.VoiceState.setPendingAction('READ_DETAIL', { title: foundLink.innerText });

                    setTimeout(() => {
                        window.location.assign(foundLink.href);
                    }, 1000);

                } else {
                    window.VoiceSpeech.speak(`Mohon maaf, saya juga tidak menemukan berita tentang ${keyword} di daftar berita. Apakah Anda ingin mendengar berita terbaru?`);
                }

            }, 1000);
        }
    }

    // --- Helpers ---

    function readGenericDetail(knownTitle) {
        // 1. Find Title
        let titleEl = document.querySelector('h1') || document.querySelector('.post-title') || document.querySelector('article h2') || document.querySelector('.card-title');
        let titleText = knownTitle || (titleEl ? titleEl.innerText : "Informasi");

        // 2. Find Content (Article/Product Body)
        let contentEl = document.querySelector('article') || document.querySelector('.article-content') || document.querySelector('.prose') || document.querySelector('.description');

        if (!contentEl) {
            window.VoiceSpeech.speak(`Saya sudah membuka detail ${titleText}, silakan baca pada layar.`);
            return;
        }

        // 3. Extract Paragraphs
        let paragraphs = Array.from(contentEl.querySelectorAll('p, li')).map(p => p.innerText.trim()).filter(t => t.length > 15);

        if (paragraphs.length === 0) {
            const rawText = contentEl.innerText;
            const lines = rawText.split('\n').map(l => l.trim()).filter(l => l.length > 20);
            paragraphs = lines.length > 1 ? lines : [rawText];
        }

        // 4. Speak Sequence
        let sentences = [`Menampilkan detail: ${titleText}.`];
        sentences = sentences.concat(paragraphs.slice(0, 8)); // Limit for brevity
        sentences.push("Sekian informasi ini. Katakan 'kembali' untuk ke menu utama.");

        window.VoiceSpeech.speakSequence(sentences);
    }

    function navigateTo(id) {
        if (id === 'top') {
            window.scrollTo({ top: 0, behavior: 'smooth' });
            // 🔊 AUDIO FEEDBACK: Announce return to top
            setTimeout(() => {
                window.VoiceSpeech.speak("Kembali ke menu utama");
            }, 500);
        } else {
            const el = document.getElementById(id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                el.classList.add('bg-blue-50', 'transition-colors', 'duration-1000');
                setTimeout(() => el.classList.remove('bg-blue-50'), 2000);

                // 🔊 AUDIO FEEDBACK: Announce successful navigation
                setTimeout(() => {
                    const sectionName = getSectionName(id);
                    window.VoiceSpeech.speak(`Menampilkan ${sectionName}`);
                }, 800);
            } else {
                // 🔊 AUDIO FEEDBACK: Section not found
                window.VoiceSpeech.speak("Maaf, bagian tersebut tidak ditemukan");
            }
        }
    }

    // Helper to get friendly section names
    function getSectionName(id) {
        const names = {
            'berita': 'berita dan informasi',
            'layanan': 'layanan publik',
            'umkm': 'UMKM dan lowongan kerja',
            'info-hari-ini': 'informasi hari ini',
            'pengaduan': 'layanan pengaduan',
            'profil': 'profil kecamatan'
        };
        return names[id] || id;
    }

    function playWelcomeSequence() {
        const Config = window.VoiceConfig;
        const Speech = window.VoiceSpeech;
        const region = Config.regionName;
        const menus = getReadableMenus();

        const sentences = [];
        sentences.push(Config.messages.welcome(region));
        menus.forEach(m => sentences.push(Config.messages.menuItem(m)));
        sentences.push(Config.messages.instruction);

        Speech.speakSequence(sentences, () => {
            window.VoiceState.setPlayedWelcome(true);
        });
    }

    function getReadableMenus() {
        const containers = document.querySelectorAll('.navbar-center, .menu, nav');
        const texts = new Set();
        containers.forEach(container => {
            const links = container.querySelectorAll('a');
            links.forEach(el => {
                const text = el.innerText.trim();
                if (text && text.toLowerCase() !== 'masuk' && text.length > 2) texts.add(text);
            });
        });
        return Array.from(texts);
    }

    function readTargetSummary() {
        // Detect current section or default to latest news
        const newsItems = document.querySelectorAll('#berita h3 a, #umkm h4');
        let text = "Berikut update terbaru: ";
        if (newsItems.length === 0) text += "Belum ada informasi tersaji.";

        newsItems.forEach((item, idx) => {
            if (idx < 3) text += `${item.innerText}. `;
        });

        window.VoiceSpeech.speak(text);
    }

    function readServices() {
        const items = document.querySelectorAll('#layanan h3');
        let text = "Layanan kami meliputi: ";
        items.forEach(i => text += `${i.innerText}, `);
        window.VoiceSpeech.speak(text);
    }

    return {
        execute: execute,
        checkAutoRun: checkAutoRun
    };
})();

console.log('[VoiceGuide] Actions Module Loaded');
