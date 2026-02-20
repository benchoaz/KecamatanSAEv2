document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------------------
    // Sidebar Toggle Logic
    // -------------------------------------------------------------------------
    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const body = document.body;

    function toggleSidebar() {
        if (window.innerWidth >= 992) {
            // Desktop: Toggle collapsed state
            body.classList.toggle('sidebar-collapsed');
        } else {
            // Mobile: Toggle active state
            sidebar.classList.toggle('active');
            sidebarOverlay.classList.toggle('active');
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleSidebar();
        });
    }

    if (sidebarClose) {
        sidebarClose.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }

    // -------------------------------------------------------------------------
    // Dropdown Management (Robust Event Delegation)
    // -------------------------------------------------------------------------
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.header-btn, .profile-pill');
        const dropdown = e.target.closest('.header-dropdown');

        // Handle Click on Toggle Button
        if (btn && dropdown) {
            console.log('Dropdown toggle clicked:', btn.id || btn.className);
            e.preventDefault();
            e.stopPropagation();

            // Close all other dropdowns
            document.querySelectorAll('.header-dropdown.active').forEach(openDropdown => {
                if (openDropdown !== dropdown) {
                    openDropdown.classList.remove('active');
                }
            });

            // Toggle current
            dropdown.classList.toggle('active');
            console.log('Toggle result:', dropdown.classList.contains('active'));
            return;
        }

        // Handle Logout Confirmation
        const logoutTrigger = e.target.closest('#logoutBtnTrigger');
        if (logoutTrigger) {
            e.preventDefault();
            e.stopPropagation();

            const isConfirmed = confirm(
                "Konfirmasi Keluar\n\nApakah Anda yakin ingin keluar dari aplikasi?\nPastikan semua pekerjaan Anda telah disimpan."
            );

            if (isConfirmed) {
                const form = document.getElementById('logout-form-header');
                if (form) form.submit();
            }
            return;
        }

        // Handle Click Outside (Close all)
        if (!dropdown) {
            document.querySelectorAll('.header-dropdown.active').forEach(openDropdown => {
                openDropdown.classList.remove('active');
            });
        }
    });

    // Submenu Toggle (Accordion) - Optimized
    document.querySelectorAll('.has-submenu > .nav-link').forEach(trigger => {
        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const parent = this.parentElement;
            const isOpen = parent.classList.contains('open');

            // Optional: Close other submenus (Accordion style)
            document.querySelectorAll('.has-submenu.open').forEach(openedItem => {
                if (openedItem !== parent) {
                    openedItem.classList.remove('open');
                }
            });

            // Toggle current
            parent.classList.toggle('open');
            console.log('Submenu toggle:', parent.querySelector('.nav-text').innerText, '->', !isOpen);
        });
    });

    // -------------------------------------------------------------------------
    // Page Transitions & Loading Bar
    // -------------------------------------------------------------------------
    const loadingBar = document.createElement('div');
    loadingBar.id = 'page-loading-bar';
    document.body.appendChild(loadingBar);

    function startLoading() {
        loadingBar.style.width = '0%';
        loadingBar.style.opacity = '1';
        setTimeout(() => {
            loadingBar.style.width = '70%';
        }, 10);
    }

    function completeLoading() {
        loadingBar.style.width = '100%';
        setTimeout(() => {
            loadingBar.style.opacity = '0';
            setTimeout(() => {
                loadingBar.style.width = '0%';
            }, 500);
        }, 300);
    }

    // Capture all link clicks for the loading effect
    document.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (link &&
            link.href &&
            !link.href.startsWith('javascript') &&
            !link.href.includes('#') &&
            !link.getAttribute('target') &&
            link.hostname === window.location.hostname) {

            // Only show loader for actual page navigations
            startLoading();
        }
    });

    // Handle back/forward button
    window.addEventListener('pageshow', function (event) {
        completeLoading();
    });

    // Complete loader on initial load
    completeLoading();

    // -------------------------------------------------------------------------
    // Form Auto-Save Resilience (localStorage)
    // -------------------------------------------------------------------------
    class FormAutoSave {
        constructor(formId) {
            this.form = document.getElementById(formId);
            if (!this.form) return;

            this.storageKey = `autosave_${window.location.pathname}_${formId}`;
            this.init();
        }

        init() {
            // 1. Load existing data
            this.restore();

            // 2. Listen for changes
            this.form.querySelectorAll('input, select, textarea').forEach(input => {
                input.addEventListener('input', () => this.save());
                input.addEventListener('change', () => this.save());
            });

            // 3. Clear on submit
            this.form.addEventListener('submit', () => this.clear());
        }

        save() {
            const formData = new FormData(this.form);
            const data = {};
            formData.forEach((value, key) => {
                // Jangan simpan file
                if (!(value instanceof File)) {
                    data[key] = value;
                }
            });
            localStorage.setItem(this.storageKey, JSON.stringify(data));
        }

        restore() {
            const savedData = localStorage.getItem(this.storageKey);
            if (!savedData) return;

            const data = JSON.parse(savedData);
            console.log('Restoring form data for:', this.storageKey);

            Object.entries(data).forEach(([key, value]) => {
                const input = this.form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'radio' || input.type === 'checkbox') {
                        const target = this.form.querySelector(`[name="${key}"][value="${value}"]`);
                        if (target) target.checked = true;
                    } else {
                        input.value = value;
                    }
                }
            });

            // Show a subtle toast or alert
            this.showRecoveryAlert();
        }

        showRecoveryAlert() {
            const alert = document.createElement('div');
            alert.className = 'alert alert-info border-0 shadow-sm rounded-4 mb-4 d-flex align-items-center animate__animated animate__fadeInDown';
            alert.innerHTML = `
                <i class="fas fa-magic me-3"></i>
                <div class="flex-grow-1 small">
                    <strong>Data Terpelihara:</strong> Kami memulihkan data yang belum sempat Anda simpan sebelumnya.
                </div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
            `;
            this.form.prepend(alert);
        }

        clear() {
            localStorage.removeItem(this.storageKey);
        }
    }

    // Initialize for specific forms
    const pembangunanForm = document.querySelector('form[action*="pembangunan"]');
    if (pembangunanForm) {
        if (!pembangunanForm.id) pembangunanForm.id = 'pembangunanForm';
        new FormAutoSave(pembangunanForm.id);
    }

    const bltForm = document.querySelector('form[action*="blt"]');
    if (bltForm) {
        if (!bltForm.id) bltForm.id = 'bltForm';
        new FormAutoSave(bltForm.id);
    }
});
