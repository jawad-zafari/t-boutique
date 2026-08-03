/**
 * Logique JavaScript pour le processus de checkout et de suivi de paiement
 * Vanilla JS - Architecture sécurisée (Simulation de passerelle de paiement)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    function getCsrfToken() {
        const wrapper = document.querySelector('[data-csrf]');
        if (wrapper) return wrapper.getAttribute('data-csrf');
        const input = document.querySelector('input[name="csrf_token"]');
        if (input) return input.value;
        return '';
    }

    // Notification Toast pour les erreurs mineures
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
        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    }

    // =========================================================================
    // LOGIQUE DE SIMULATION DU PAIEMENT EN LIGNE (MOCK GATEWAY)
    // =========================================================================
    const mockLoader = document.getElementById('mockPaymentLoader');
    
    if (mockLoader) {
        const orderId = mockLoader.getAttribute('data-order-id');
        const csrfToken = getCsrfToken();

        // Simulation d'une attente réseau (3 secondes) pour faire réaliste
        setTimeout(async () => {
            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);

                const response = await fetch(`${baseUrl}Checkout/processMockPaymentAjax/${orderId}`, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();

                if (result.status === 'success') {
                    // Si le paiement est réussi, on recharge la page pour afficher la facture
                    window.location.reload();
                } else {
                    // En cas d'échec (fonds insuffisants), on redirige vers la page d'erreur
                    const errorMessage = encodeURIComponent(result.message || 'Erreur lors du paiement.');
                    window.location.href = `${baseUrl}Checkout/showError?error=${errorMessage}&orderId=${orderId}`;
                }

            } catch (error) {
                console.error("Erreur serveur lors de la simulation:", error);
                window.location.href = `${baseUrl}Checkout/showError?error=Erreur_serveur_inattendue&orderId=${orderId}`;
            }
        }, 3000);
    }

    // =========================================================================
    // VALIDATION DU FORMULAIRE VIREMENT BANCAIRE (Si existant)
    // =========================================================================
    const formBankTransfer = document.querySelector('form[action*="Checkout/bankTransfer"]');
    if (formBankTransfer) {
        formBankTransfer.addEventListener('submit', (e) => {
            const creditCardInput = document.getElementById('creditcard');
            if (creditCardInput) {
                const cardValue = creditCardInput.value.replace(/\s+/g, '');
                if (cardValue.length < 4) {
                    e.preventDefault(); 
                    showPaymentToast("Veuillez saisir un numéro de compte ou de référence valide.");
                    creditCardInput.classList.add('is-invalid');
                    creditCardInput.focus();
                }
            }
        });
    }
});