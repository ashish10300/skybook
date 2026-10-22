
/**
 * SkyBook - Main Global JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const mobileToggle = document.querySelector('.nav-mobile-toggle');
    const mobileMenu = document.querySelector('.nav-mobile-menu');

    if (mobileToggle && mobileMenu) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenu.classList.toggle('open');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
                mobileMenu.classList.remove('open');
            }
        });
    }

    // 2. User Profile Dropdown Toggle
    const userBtn = document.querySelector('.user-btn');
    const userDropdown = document.querySelector('.user-dropdown');

    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });

        document.addEventListener('click', (e) => {
            if (!userDropdown.contains(e.target) && !userBtn.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }

    // 3. Alert auto-dismiss and close buttons
    const alertCloses = document.querySelectorAll('.alert-close');
    alertCloses.forEach(btn => {
        btn.addEventListener('click', () => {
            const alertBox = btn.closest('.alert');
            if (alertBox) {
                alertBox.style.opacity = '0';
                setTimeout(() => alertBox.remove(), 250);
            }
        });
    });

    // Auto-dismiss alerts after 6 seconds
    const autoAlerts = document.querySelectorAll('.alert');
    autoAlerts.forEach(al => {
        setTimeout(() => {
            if (al && al.parentElement) {
                al.style.transition = 'opacity 0.4s ease';
                al.style.opacity = '0';
                setTimeout(() => al.remove(), 400);
            }
        }, 6000);
    });
});

// ==============================================================
// ==============================================================
// SkyBook Simple Conditional Preloader & Image Load System
// (Only shows if >1s or images pending; stays until images load)
// ==============================================================
(function initSkyBookLoadExperience() {
    const preloader = document.getElementById('skybook-preloader');
    let isDismissed = false;

    // 1. Mark image and parent wrappers
    function markImageLoaded(img) {
        if (!img) return;
        img.classList.add('img-loaded');
        const wrap = img.closest('.dest-img-wrap, .cabin-img-wrap, .about-image-card, .img-loading-wrap');
        if (wrap) {
            wrap.classList.add('img-loaded');
            wrap.classList.remove('img-has-error');
        }
    }

    function markImageError(img) {
        if (!img) return;
        img.classList.add('img-loaded');
        const wrap = img.closest('.dest-img-wrap, .cabin-img-wrap, .about-image-card, .img-loading-wrap');
        if (wrap) {
            wrap.classList.add('img-loaded', 'img-has-error');
        }
    }

    // 2. Track all images on the page
    const images = Array.from(document.querySelectorAll('img')).filter(img => !preloader || !preloader.contains(img));
    const pendingImages = [];

    images.forEach(img => {
        if (img.complete && img.naturalHeight > 0) {
            markImageLoaded(img);
        } else {
            pendingImages.push(img);
            img.addEventListener('load', () => markImageLoaded(img), { once: true });
            img.addEventListener('error', () => markImageError(img), { once: true });
        }
    });

    // 3. Dismiss preloader
    function fadeOutPreloader() {
        if (isDismissed || !preloader) return;
        isDismissed = true;
        window.__skybookLoaded = true;

        if (window.__skybookTimer) {
            clearTimeout(window.__skybookTimer);
        }

        preloader.classList.remove('is-active');
        preloader.classList.add('preloader-hidden');

        setTimeout(() => {
            preloader.style.display = 'none';
        }, 320);
    }

    // 4. Check if all images and document are ready
    function checkAllReady() {
        const allImagesDone = pendingImages.every(img => img.complete);
        const isDocDone = document.readyState === 'complete';

        if (allImagesDone && isDocDone) {
            fadeOutPreloader();
        }
    }

    if (pendingImages.length === 0) {
        if (document.readyState === 'complete') {
            fadeOutPreloader();
        } else {
            window.addEventListener('load', fadeOutPreloader, { once: true });
        }
    } else {
        pendingImages.forEach(img => {
            img.addEventListener('load', checkAllReady, { once: true });
            img.addEventListener('error', checkAllReady, { once: true });
        });
        window.addEventListener('load', checkAllReady, { once: true });

        // Failsafe timeout so user is never stuck if an external asset stalls
        setTimeout(fadeOutPreloader, 4000);
    }

    // 5. Global helper to trigger loader
    window.showSkyBookLoader = function() {
        if (!preloader) return;
        preloader.style.display = 'flex';
        preloader.classList.remove('preloader-hidden');
        preloader.classList.add('is-active');
    };

    // 6. Form submissions: show loader only if response takes > 1 second
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            if (form.checkValidity && !form.checkValidity()) {
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
            }

            setTimeout(() => {
                if (!window.__skybookFormCompleted) {
                    window.showSkyBookLoader();
                }
            }, 1000);
        });
    });

    // Handle browser back/forward cache
    window.addEventListener('pageshow', (event) => {
        fadeOutPreloader();
        document.querySelectorAll('.btn-loading').forEach(b => b.classList.remove('btn-loading'));
    });
})();



