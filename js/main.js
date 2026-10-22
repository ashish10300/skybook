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
