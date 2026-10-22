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
// SkyBook Aviation Preloader & Image Loading Tracker
// ==============================================================
(function initSkyBookPreloader() {
    const preloader = document.getElementById('skybook-preloader');
    if (!preloader) return;

    const progressBar = document.getElementById('preloader-progress-bar');
    const progressPercent = document.getElementById('preloader-percentage');
    const statusText = document.getElementById('preloader-status-text');

    let isFinished = false;
    let currentPercent = 0;

    function setVisualProgress(percent) {
        currentPercent = Math.max(currentPercent, Math.min(Math.round(percent), 100));
        if (progressBar) progressBar.style.width = currentPercent + '%';
        if (progressPercent) progressPercent.textContent = currentPercent + '%';

        if (statusText) {
            if (currentPercent < 35) {
                statusText.textContent = 'Calibrating Global Corridors...';
            } else if (currentPercent < 75) {
                statusText.textContent = 'Loading High-Resolution Fleet Imagery...';
            } else if (currentPercent < 100) {
                statusText.textContent = 'Finalizing Avionics & Seat Allocator...';
            } else {
                statusText.textContent = 'Ready for Departure!';
            }
        }
    }

    function markImageLoaded(img) {
        img.classList.add('img-loaded');
    }

    function checkAllImages() {
        const images = Array.from(document.querySelectorAll('img:not(.radar-plane)'));
        const totalImages = images.length;

        if (totalImages === 0) {
            setVisualProgress(100);
            dismissPreloader();
            return;
        }

        let loadedCount = 0;

        function onImageHandled(img) {
            markImageLoaded(img);
            loadedCount++;
            setVisualProgress((loadedCount / totalImages) * 100);
            if (loadedCount >= totalImages) {
                dismissPreloader();
            }
        }

        images.forEach(img => {
            if (img.complete && img.naturalHeight !== 0) {
                onImageHandled(img);
            } else {
                img.addEventListener('load', () => onImageHandled(img), { once: true });
                img.addEventListener('error', () => onImageHandled(img), { once: true });
            }
        });

        if (loadedCount > 0) {
            setVisualProgress((loadedCount / totalImages) * 100);
        }
    }

    function dismissPreloader() {
        if (isFinished) return;
        isFinished = true;
        setVisualProgress(100);

        setTimeout(() => {
            preloader.classList.add('preloader-hidden');
            setTimeout(() => {
                if (preloader.parentNode) preloader.remove();
            }, 600);
        }, 300);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', checkAllImages);
    } else {
        checkAllImages();
    }

    window.addEventListener('load', () => {
        setTimeout(dismissPreloader, 200);
    });

    // Safety fallback: Never trap user on slow 2G/offline
    setTimeout(dismissPreloader, 2500);
})();

