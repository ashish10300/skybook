
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
// SkyBook Real-Time Conditional Image & Network Load Experience
// (Only shows if images/network take real time; if in time, no loader)
// ==============================================================
(function initSkyBookLoadExperience() {
    const preloader = document.getElementById('skybook-preloader');
    const statusMsg = document.getElementById('preloader-status-msg');
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

    // 2. Scan and manage all images on page
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

    // 3. Smooth Dismissal
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
        }, 360);
    }

    // 4. Verification Check: Are all images and document ready?
    function checkAllReady() {
        if (isDismissed) return;

        const allImagesDone = pendingImages.every(img => img.complete);
        const isDocDone = (document.readyState === 'complete');

        if (allImagesDone && isDocDone) {
            fadeOutPreloader();
        }
    }

    // If all images loaded immediately ("loads in time"), cancel timer so loader NEVER appears
    if (pendingImages.length === 0 && document.readyState === 'complete') {
        fadeOutPreloader();
    } else {
        pendingImages.forEach(img => {
            img.addEventListener('load', checkAllReady, { once: true });
            img.addEventListener('error', checkAllReady, { once: true });
        });
        window.addEventListener('load', checkAllReady, { once: true });

        // Failsafe timer (6s) in case an external image stalls
        setTimeout(fadeOutPreloader, 6000);
    }

    // 5. Global helper to trigger loader during navigation / form submissions
    window.showSkyBookLoader = function(message) {
        if (!preloader) return;
        if (statusMsg && message) {
            statusMsg.textContent = message;
        }
        preloader.style.display = 'flex';
        preloader.classList.remove('preloader-hidden');
        preloader.classList.add('is-active');
    };

    // 6. Intercept Search & Booking Form submissions to show active flight search loader
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (form.checkValidity && !form.checkValidity()) {
                return;
            }

            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.classList.add('btn-loading');
            }

            const action = (form.getAttribute('action') || window.location.pathname).toLowerCase();
            let msg = 'Searching Live Flight Schedules...';

            if (action.includes('payment.php') || action.includes('confirm')) {
                msg = 'Securing Flight Reservation...';
            } else if (action.includes('passenger.php')) {
                msg = 'Reserving Passenger Seats...';
            } else if (action.includes('login.php')) {
                msg = 'Authenticating Account...';
            } else if (action.includes('results.php')) {
                msg = 'Scanning International Routes & Fares...';
            }

            window.showSkyBookLoader(msg);
        });
    });

    // Handle browser back/forward cache (pageshow)
    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            fadeOutPreloader();
            document.querySelectorAll('.btn-loading').forEach(b => b.classList.remove('btn-loading'));
        }
    });
})();




