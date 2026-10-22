
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
// SkyBook Minimalist Preloader & Intelligent Image Cache Detector
// ==============================================================
(function initSkyBookMinimalPreloader() {
    const preloader = document.getElementById('skybook-preloader');
    if (!preloader) return;

    // Immediately mark loaded images so they don't remain hidden by initial CSS
    function markImageLoaded(img) {
        img.classList.add('img-loaded');
    }

    // Collect all image items to monitor (both <img> tags and background-images)
    function collectPendingMedia() {
        const pending = [];
        const contentImages = Array.from(document.querySelectorAll('img')).filter(img => !preloader.contains(img));

        contentImages.forEach(img => {
            if (img.complete && img.naturalHeight > 0) {
                markImageLoaded(img);
            } else {
                pending.push({
                    subscribe: (onDone) => {
                        img.addEventListener('load', () => { markImageLoaded(img); onDone(); }, { once: true });
                        img.addEventListener('error', () => { markImageLoaded(img); onDone(); }, { once: true });
                    }
                });
            }
        });

        // Also check key background images
        const bgElements = Array.from(document.querySelectorAll('[style*="background-image"], .hero-section, .cta-banner'));
        bgElements.forEach(el => {
            const style = window.getComputedStyle(el);
            const bg = style.backgroundImage;
            if (bg && bg !== 'none') {
                const match = bg.match(/url\(['"]?([^'"]+)['"]?\)/);
                if (match && match[1] && !match[1].startsWith('data:')) {
                    const testImg = new Image();
                    testImg.src = match[1];
                    if (!(testImg.complete && testImg.naturalHeight > 0)) {
                        pending.push({
                            subscribe: (onDone) => {
                                testImg.onload = onDone;
                                testImg.onerror = onDone;
                            }
                        });
                    }
                }
            }
        });

        return pending;
    }

    const pendingItems = collectPendingMedia();

    // 1. IF ALREADY LOADED / CACHED -> DO NOT SHOW ANIMATION AT ALL
    if (pendingItems.length === 0) {
        preloader.style.display = 'none';
        if (preloader.parentNode) {
            preloader.parentNode.removeChild(preloader);
        }
        return;
    }

    // 2. IF NOT LOADED -> SHOW MINIMALIST ANIMATION UNTIL IMAGES COMPLETE
    preloader.classList.add('is-active');

    let remaining = pendingItems.length;
    let isDismissed = false;

    function fadeOutPreloader() {
        if (isDismissed) return;
        isDismissed = true;

        preloader.classList.remove('is-active');
        preloader.classList.add('preloader-hidden');
        setTimeout(() => {
            if (preloader.parentNode) {
                preloader.parentNode.removeChild(preloader);
            }
        }, 400);
    }

    pendingItems.forEach(item => {
        item.subscribe(() => {
            remaining--;
            if (remaining <= 0) {
                fadeOutPreloader();
            }
        });
    });

    // Window load backup event
    window.addEventListener('load', () => {
        setTimeout(fadeOutPreloader, 100);
    }, { once: true });

    // Safety fallback: Never trap users if CDN/network hangs
    setTimeout(fadeOutPreloader, 2200);
})();


