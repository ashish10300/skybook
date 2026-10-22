/**
 * SkyBook - Admin Management Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // 0. Mobile Sidebar Drawer Toggle
    const mobileToggle = document.getElementById('admin-mobile-toggle');
    const bottomMenuBtn = document.getElementById('admin-bottom-menu-btn');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('admin-sidebar-overlay');
    const sidebarClose = document.getElementById('admin-sidebar-close');

    function openMobileSidebar() {
        if (sidebar) sidebar.classList.add('show-mobile');
        if (overlay) overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeMobileSidebar() {
        if (sidebar) sidebar.classList.remove('show-mobile');
        if (overlay) overlay.classList.remove('show');
        document.body.style.overflow = '';
    }

    if (mobileToggle) mobileToggle.addEventListener('click', openMobileSidebar);
    if (bottomMenuBtn) bottomMenuBtn.addEventListener('click', openMobileSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeMobileSidebar);
    if (overlay) overlay.addEventListener('click', closeMobileSidebar);

    const adminMain = document.querySelector('.admin-main');

    function openModal(modal) {
        if (!modal) return;
        modal.classList.add('open');
        if (adminMain) adminMain.style.overflow = 'hidden';
        document.body.style.overflow = 'hidden';

        // Auto-focus first input in modal body
        const firstInput = modal.querySelector('.modal-body input:not([type="hidden"]), .modal-body select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 60);
        }
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.classList.remove('open');
        const anyOpen = document.querySelector('.modal-backdrop.open');
        if (!anyOpen) {
            if (adminMain) adminMain.style.overflow = '';
            document.body.style.overflow = '';
        }
    }

    function closeAllModals() {
        modalBackdrops.forEach(mb => mb.classList.remove('open'));
        if (adminMain) adminMain.style.overflow = '';
        document.body.style.overflow = '';
    }

    // Close mobile drawer and modals on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeMobileSidebar();
            closeAllModals();
        }
    });

    // 1. Generic Modal open/close
    const modalBackdrops = document.querySelectorAll('.modal-backdrop');
    const modalCloses = document.querySelectorAll('.modal-close, .modal-cancel-btn');

    modalCloses.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = btn.closest('.modal-backdrop');
            if (modal) {
                closeModal(modal);
            } else {
                closeAllModals();
            }
        });
    });

    modalBackdrops.forEach(mb => {
        mb.addEventListener('click', (e) => {
            if (e.target === mb) {
                closeModal(mb);
            }
        });

        // Prevent wheel scrolling directly on backdrop from reaching background
        mb.addEventListener('wheel', (e) => {
            if (e.target === mb) {
                e.preventDefault();
            }
        }, { passive: false });
    });

    // 2. Open Add Flight Modal
    const addFlightBtn = document.getElementById('open-add-flight-btn');
    const addFlightModal = document.getElementById('add-flight-modal');
    if (addFlightBtn && addFlightModal) {
        addFlightBtn.addEventListener('click', () => {
            openModal(addFlightModal);
        });
    }

    // 3. Open Edit Flight Modal & Pre-fill fields
    const editFlightButtons = document.querySelectorAll('.open-edit-flight-btn');
    const editFlightModal = document.getElementById('edit-flight-modal');

    if (editFlightButtons.length > 0 && editFlightModal) {
        editFlightButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                const flight = JSON.parse(btn.dataset.flight || '{}');
                
                // Populate form inputs
                document.getElementById('edit-flight-id').value = flight.id || '';
                document.getElementById('edit-flight-number').value = flight.flight_number || '';
                document.getElementById('edit-airline').value = flight.airline || '';
                document.getElementById('edit-source').value = flight.source || '';
                document.getElementById('edit-source-code').value = flight.source_code || '';
                document.getElementById('edit-destination').value = flight.destination || '';
                document.getElementById('edit-destination-code').value = flight.destination_code || '';
                document.getElementById('edit-departure-date').value = flight.departure_date || '';
                document.getElementById('edit-departure-time').value = flight.departure_time || '';
                document.getElementById('edit-arrival-time').value = flight.arrival_time || '';
                document.getElementById('edit-duration').value = flight.duration || '';
                document.getElementById('edit-travel-class').value = flight.travel_class || 'Economy';
                document.getElementById('edit-price').value = flight.price || '';
                document.getElementById('edit-available-seats').value = flight.available_seats || '';
                document.getElementById('edit-status').value = flight.status || 'active';

                openModal(editFlightModal);
            });
        });
    }

    // 4. Client-side Table Filter Search
    const searchInput = document.getElementById('admin-table-search');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            const rows = document.querySelectorAll('.data-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
