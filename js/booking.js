/**
 * SkyBook - Booking & Payment Flow Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Payment Tabs Switcher
    const paymentTabs = document.querySelectorAll('.payment-tab-btn');
    const paymentPanes = document.querySelectorAll('.payment-pane');
    const selectedMethodInput = document.getElementById('selected-payment-method');

    if (paymentTabs.length > 0) {
        paymentTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const targetMethod = tab.dataset.method;

                paymentTabs.forEach(t => t.classList.remove('active'));
                paymentPanes.forEach(p => p.classList.remove('active'));

                tab.classList.add('active');
                const activePane = document.getElementById(`pane-${targetMethod}`);
                if (activePane) activePane.classList.add('active');

                if (selectedMethodInput) {
                    selectedMethodInput.value = tab.dataset.methodLabel || targetMethod;
                }
            });
        });
    }

    // 2. Demo Card Number auto-formatter (4-digit spacing)
    const cardInput = document.getElementById('card-number');
    if (cardInput) {
        cardInput.addEventListener('input', (e) => {
            let val = e.target.value.replace(/\D/g, '').substring(0, 16);
            let formatted = val.match(/.{1,4}/g)?.join(' ') || val;
            e.target.value = formatted;
        });
    }

    // 3. Demo Card Expiry auto-formatter (MM/YY)
    const expInput = document.getElementById('card-expiry');
    if (expInput) {
        expInput.addEventListener('input', (e) => {
            let val = e.target.value.replace(/\D/g, '').substring(0, 4);
            if (val.length >= 2) {
                e.target.value = val.substring(0, 2) + '/' + val.substring(2);
            } else {
                e.target.value = val;
            }
        });
    }

    // 4. Payment Submission Button with Loading State
    const paymentForm = document.getElementById('payment-form');
    const paySubmitBtn = document.getElementById('pay-submit-btn');

    if (paymentForm && paySubmitBtn) {
        paymentForm.addEventListener('submit', (e) => {
            paySubmitBtn.disabled = true;
            paySubmitBtn.innerHTML = `
                <svg style="animation: spin 1s linear infinite; width: 18px; height: 18px; margin-right: 8px; display: inline-block;" viewBox="0 0 24 24" fill="none">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" stroke-opacity="0.3"></circle>
                    <path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing Payment...
            `;
        });
    }
});
