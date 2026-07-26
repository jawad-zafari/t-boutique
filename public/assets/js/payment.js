/**
 * Gestion du processus de paiement et de l'interface utilisateur moderne
 * Vanilla JS - Architecture sécurisée (Soumission POST avec CSRF & Direction intelligente)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Initialisation des composants de la page de paiement
    initializeModernCheckout();

});

/**
 * Système de Toast pour remplacer les alert() natifs bloquants
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
    toast.innerHTML = '';
    
    const icon = document.createElement('i');
    icon.className = (type === 'danger') ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-check';
    icon.style.marginRight = '10px';
    
    const textNode = document.createTextNode(message);

    toast.appendChild(icon);
    toast.appendChild(textNode);

    toast.style.opacity = '1';
    toast.style.display = 'block';

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => { toast.style.display = 'none'; }, 300);
    }, 4000);
}

/**
 * Configure les écouteurs pour la sélection de paiement et la redirection
 */
function initializeModernCheckout() {
    
    const btnConfirmPayment = document.getElementById('btnConfirmPayment');
    const paymentOptions = document.querySelectorAll('input[name="payment_choice"]');
    const paymentForm = document.getElementById('paymentMethodsForm');
    
    // 1. Gestion visuelle de la sélection des cartes de paiement
    paymentOptions.forEach(option => {
        option.addEventListener('change', function() {
            document.querySelectorAll('.payment-method-option').forEach(label => {
                label.classList.remove('active');
            });
            
            if (this.checked) {
                this.closest('.payment-method-option').classList.add('active');
            }
        });
    });

    // 2. Action finale du bouton "Confirmer et Payer"
    if (btnConfirmPayment) {
        btnConfirmPayment.addEventListener('click', (e) => {
            e.preventDefault();

            const selectedPayment = document.querySelector('input[name="payment_choice"]:checked');
            
            if (selectedPayment) {
                const paymentType = selectedPayment.value;
                const targetUrl = selectedPayment.getAttribute('data-url');

                btnConfirmPayment.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Traitement en cours...';
                btnConfirmPayment.disabled = true;
                
                if (paymentType === 'transfer') {
                    // Redirection vers le formulaire de saisie des détails du virement
                    window.location.href = targetUrl;
                } else if (paymentForm) {
                    // Soumission sécurisée en POST pour la passerelle Stripe
                    paymentForm.setAttribute('action', targetUrl);
                    paymentForm.submit();
                }
            } else {
                showPaymentToast("Veuillez sélectionner une méthode de paiement avant de continuer.");
            }
        });
    }

    // 3. Validation de sécurité pour le formulaire de transfert bancaire
    const formBankTransfer = document.querySelector('form[action*="Checkout/bankTransfer"]');
    if (formBankTransfer) {
        formBankTransfer.addEventListener('submit', (e) => {
            const creditCardInput = document.getElementById('creditcard');
            if (creditCardInput) {
                const cardValue = creditCardInput.value.replace(/\s+/g, '');
                
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