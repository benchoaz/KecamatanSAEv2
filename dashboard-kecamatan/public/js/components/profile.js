/**
 * Component: User Profile
 * Strategy: Robust Event Delegation & Framework Agnostic
 */

(function () {
    'use strict';

    const initProfileComponent = () => {
        const profileClass = 'gov-profile';
        const activeClass = 'is-active';

        // 1. Global Click Handler (Event Delegation)
        document.addEventListener('click', (event) => {
            const container = event.target.closest(`.${profileClass}`);
            const isTrigger = event.target.closest(`.${profileClass}__trigger`);

            // If clicking a trigger, toggle its dropdown
            if (isTrigger && container) {
                event.preventDefault();
                event.stopPropagation();

                const isOpen = container.classList.contains(activeClass);

                // Close all others first
                document.querySelectorAll(`.${profileClass}.${activeClass}`).forEach(el => {
                    el.classList.remove(activeClass);
                });

                // Toggle target
                if (!isOpen) {
                    container.classList.add(activeClass);
                }
                return;
            }

            // If clicking outside, close all active profile menus
            if (!container) {
                document.querySelectorAll(`.${profileClass}.${activeClass}`).forEach(el => {
                    el.classList.remove(activeClass);
                });
            }
        });

        // 2. Keyboard Accessibility (Escape to close)
        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                document.querySelectorAll(`.${profileClass}.${activeClass}`).forEach(el => {
                    el.classList.remove(activeClass);
                    // Return focus to trigger for a11y
                    const trigger = el.querySelector(`.${profileClass}__trigger`);
                    if (trigger) trigger.focus();
                });
            }
        });

        // 3. Logout Confirmation Logic
        document.addEventListener('click', (event) => {
            const logoutLink = event.target.closest('.gov-profile__item--logout');
            if (logoutLink) {
                event.preventDefault();
                const formId = logoutLink.getAttribute('data-form-id');
                const form = document.getElementById(formId);

                if (window.confirm("Konfirmasi Keluar\n\nApakah Anda yakin ingin keluar dari aplikasi?\nPastikan semua pekerjaan Anda telah disimpan.")) {
                    if (form) {
                        form.submit();
                    } else {
                        window.location.href = logoutLink.href;
                    }
                }
            }
        });
    };

    // Auto-init on DOM Content Loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initProfileComponent);
    } else {
        initProfileComponent();
    }
})();
