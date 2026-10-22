/**
 * SkyBook - Flight Search & Results Helper
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Swap Source and Destination
    const swapBtn = document.querySelector('.swap-btn');
    const sourceInput = document.getElementById('search-source');
    const destInput = document.getElementById('search-destination');

    if (swapBtn && sourceInput && destInput) {
        swapBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const tempVal = sourceInput.value;
            sourceInput.value = destInput.value;
            destInput.value = tempVal;
        });
    }

    // 2. Set min departure date to today
    const dateInput = document.getElementById('search-date');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        if (!dateInput.min) {
            dateInput.min = today;
        }
        if (!dateInput.value) {
            // Default to today or +1 day
            const tomorrow = new Date();
            tomorrow.setDate(tomorrow.getDate() + 2);
            dateInput.value = tomorrow.toISOString().split('T')[0];
        }
    }

    // 3. Results Sorting Handler
    const sortButtons = document.querySelectorAll('.sort-btn');
    const flightsContainer = document.querySelector('.flights-list');

    if (sortButtons.length > 0 && flightsContainer) {
        sortButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                sortButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const sortKey = btn.dataset.sort; // 'price', 'time', 'duration'
                sortFlightCards(sortKey, flightsContainer);
            });
        });
    }

    function sortFlightCards(key, container) {
        const cards = Array.from(container.querySelectorAll('.flight-card'));

        cards.sort((a, b) => {
            if (key === 'price') {
                const priceA = parseFloat(a.dataset.price || 0);
                const priceB = parseFloat(b.dataset.price || 0);
                return priceA - priceB;
            } else if (key === 'time') {
                const timeA = a.dataset.departure || '00:00';
                const timeB = b.dataset.departure || '00:00';
                return timeA.localeCompare(timeB);
            } else if (key === 'duration') {
                const durA = parseInt(a.dataset.durationMinutes || 0);
                const durB = parseInt(b.dataset.durationMinutes || 0);
                return durA - durB;
            }
            return 0;
        });

        cards.forEach(card => container.appendChild(card));
    }

    // 4. Intercept Book Route buttons to populate form instead of direct link
    const bookRouteBtns = document.querySelectorAll('.book-route-btn');
    bookRouteBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Extract source and destination from the href URL
            const urlObj = new URL(btn.href);
            const params = new URLSearchParams(urlObj.search);
            const source = params.get('source');
            const dest = params.get('destination');
            
            const sourceInput = document.getElementById('search-source');
            const destInput = document.getElementById('search-destination');
            const dateInput = document.getElementById('search-date');
            const searchWidget = document.querySelector('.search-widget-card');
            
            if (sourceInput && destInput && searchWidget) {
                sourceInput.value = source;
                destInput.value = dest;
                
                // Scroll to search widget smoothly
                searchWidget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                // Add a highlight effect temporarily
                searchWidget.style.transition = 'box-shadow 0.3s ease';
                searchWidget.style.boxShadow = '0 0 0 4px var(--accent)';
                setTimeout(() => {
                    searchWidget.style.boxShadow = 'var(--shadow-md)';
                }, 1500);
                
                // Focus date input to prompt user
                if (dateInput) {
                    setTimeout(() => dateInput.focus(), 600);
                }
            } else {
                // Fallback if form not found (shouldn't happen on home page)
                window.location.href = btn.href;
            }
        });
    });

    // 5. Auto-scroll to search if params are present in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('source') && urlParams.has('destination')) {
        const searchWidget = document.querySelector('.search-widget-card');
        const dateInput = document.getElementById('search-date');
        
        if (searchWidget) {
            setTimeout(() => {
                searchWidget.scrollIntoView({ behavior: 'smooth', block: 'center' });
                
                searchWidget.style.transition = 'box-shadow 0.3s ease';
                searchWidget.style.boxShadow = '0 0 0 4px var(--accent)';
                setTimeout(() => {
                    searchWidget.style.boxShadow = 'var(--shadow-md)';
                }, 1500);
                
                if (dateInput) {
                    setTimeout(() => dateInput.focus(), 600);
                }
            }, 100);
        }
    }
});
