/**
 * Logique JavaScript pour le processus de checkout et de paiement
 * Vanilla JS - Architecture sécurisée (Routage dynamique, Validation Regex Anti-Injection)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Initialisation des composants de la page de paiement
    initializeModernCheckout();

});

/**
 * Système de Notification Toast (Substitut aux alert() natifs)
 */
function showPaymentToast(message, type = 'danger') {
    let toast = document.getElementById('paymentToastNotification');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'paymentToastNotification';
        toast.style.position = 'fixed';
        toast.style.top = '20px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%)';
        toast.style.padding = '15px 30px';
        toast.style.borderRadius = '8px';
        toast.style.color = '#fff';
        toast.style.fontWeight = 'bold';
        toast.style.zIndex = '10000';
        toast.style.boxShadow = '0 10px 25px rgba(0,0,0,0.15)';
        toast.style.transition = 'opacity 0.3s ease-in-out';
        document.body.appendChild(toast);
    }

    toast.style.backgroundColor = (type === 'danger') ? '#e03131' : '#2b8a3e';
    toast.textContent = message; // Anti-XSS : Utilisation stricte de textContent
    toast.style.opacity = '1';

    setTimeout(() => {
        toast.style.opacity = '0';
    }, 3500);
}

function initializeModernCheckout() {
    const paymentForm = document.getElementById('paymentMethodsForm');
    const btnConfirm = document.getElementById('btnConfirmPayment');
    const paymentMethodsContainer = document.getElementById('paymentMethodsContainer');

    // 1. Sélection visuelle de la méthode de paiement
    if (paymentMethodsContainer) {
        const options = paymentMethodsContainer.querySelectorAll('.payment-method-option');
        options.forEach(option => {
            option.addEventListener('click', () => {
                options.forEach(opt => opt.classList.remove('active'));
                option.classList.add('active');
                const radio = option.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
            });
        });
    }

    // 2. Soumission du formulaire de paiement
    if (btnConfirm) {
        btnConfirm.addEventListener('click', (e) => {
            e.preventDefault();
            const selectedRadio = document.querySelector('input[name="payment_choice"]:checked');
            
            if (selectedRadio) {
                const targetUrl = selectedRadio.getAttribute('data-url');
                const val = selectedRadio.value;

                if (val === 'transfer') {
                    // Redirection vers le formulaire de virement bancaire
                    window.location.href = targetUrl;
                } else if (paymentForm) {
                    // Soumission sécurisée en POST avec le jeton CSRF
                    paymentForm.setAttribute('action', targetUrl);
                    paymentForm.submit();
                }
            } else {
                showPaymentToast("Veuillez sélectionner une méthode de paiement avant de continuer.");
            }
        });
    }

    // 3. Validation سمت کلاینت برای شماره کارت واریز آنلاین/کارت به کارت
    const formBankTransfer = document.querySelector('form[action*="Checkout/bankTransfer"]');
    if (formBankTransfer) {
        formBankTransfer.addEventListener('submit', (e) => {
            const creditCardInput = document.getElementById('creditcard');
            if (creditCardInput) {
                const cardValue = creditCardInput.value.replace(/\s+/g, '');
                
                // Regex : N'accepter QUE des chiffres
                if (!/^\d+$/.test(cardValue)) {
                    e.preventDefault(); 
                    showPaymentToast("Le numéro de carte ou de compte ne doit contenir que des chiffres.");
                    creditCardInput.classList.add('is-invalid');
                    creditCardInput.focus();
                } else {
                    creditCardInput.classList.remove('is-invalid');
                }
            }
        });
    }
}