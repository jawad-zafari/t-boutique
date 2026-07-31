/**
 * Logique globale JavaScript pour la gestion des commandes (Checkout Stepper)
 * Architecture Vanilla JS
 * Sécurité : Protection CSRF pour toutes les requêtes Fetch & Prévention DOM-Based XSS
 */
document.addEventListener("DOMContentLoaded", () => {

    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';
    
    // SÉCURITÉ : Récupération dynamique du jeton CSRF pour les requêtes AJAX
    function getCsrfToken() {
        const wrapper = document.querySelector('[data-csrf]');
        if (wrapper) return wrapper.getAttribute('data-csrf');
        const input = document.querySelector('input[name="csrf_token"]');
        if (input) return input.value;
        return '';
    }

    // =========================================================================
    // 1. SYSTÈME DE TOAST (Notification sécurisée)
    // =========================================================================
    function showOrderToast(message, type = 'success') {
        let toast = document.getElementById('orderToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'orderToastNotification';
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.left = '50%';
            toast.style.transform = 'translateX(-50%)';
            toast.style.padding = '15px 30px';
            toast.style.borderRadius = '8px';
            toast.style.color = '#fff';
            toast.style.fontWeight = 'bold';
            toast.style.zIndex = '10000';
            toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.2)';
            toast.style.transition = 'opacity 0.3s ease-in-out';
            document.body.appendChild(toast);
        }

        toast.style.backgroundColor = (type === 'danger') ? '#e03131' : '#2b8a3e';
        toast.innerHTML = ''; 
        
        const icon = document.createElement('i');
        icon.className = type === 'danger' ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
        icon.style.marginRight = '10px';
        
        // SÉCURITÉ : Échappement des caractères HTML pour éviter le XSS
        const textNode = document.createTextNode(message);

        toast.appendChild(icon);
        toast.appendChild(textNode);

        toast.style.opacity = '1';
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    }

    // =========================================================================
    // 2. ÉTAPE 2 : GESTION DES ADRESSES ET MODES DE LIVRAISON
    // =========================================================================
    const btnOpenAddressModal = document.getElementById('btnOpenAddressModal');
    const addressModal = document.getElementById('newAddressModal');
    const btnCloseAddressModal = document.getElementById('btnCloseAddressModal');
    const btnCancelAddress = document.getElementById('btnCancelAddress');

    const openModal = () => { if (addressModal) addressModal.classList.add('active'); };
    const closeModal = () => { if (addressModal) addressModal.classList.remove('active'); };

    if (btnOpenAddressModal) btnOpenAddressModal.addEventListener('click', openModal);
    if (btnCloseAddressModal) btnCloseAddressModal.addEventListener('click', closeModal);
    if (btnCancelAddress) btnCancelAddress.addEventListener('click', closeModal);

    const formNewAddress = document.getElementById('formNewAddress');
    if (formNewAddress) {
        formNewAddress.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btnSubmit = document.getElementById('btnSubmitAddress');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Enregistrement...';
            }

            try {
                const formData = new FormData(this);
                // SÉCURITÉ : Ajout du jeton CSRF à la requête
                formData.append('csrf_token', getCsrfToken());

                const response = await fetch(`${baseUrl}Order/addAddressAjax`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.status === 'success') {
                    showOrderToast(data.message, 'success');
                    closeModal();
                    this.reset();

                    // SÉCURITÉ : Création sécurisée des éléments DOM au lieu de l'injection HTML brute
                    const addressList = document.getElementById('addressListContainer');
                    if (addressList && data.address) {
                        const emptyMsg = document.getElementById('emptyAddressMsg');
                        if (emptyMsg) emptyMsg.style.display = 'none';

                        const newDiv = document.createElement('div');
                        newDiv.className = 'address-card-item';

                        const inputRadio = document.createElement('input');
                        inputRadio.type = 'radio';
                        inputRadio.name = 'selected_address';
                        inputRadio.id = `addr_${data.address.id}`;
                        inputRadio.value = data.address.id;
                        inputRadio.className = 'address-radio';
                        inputRadio.checked = true; // Auto-sélection de la nouvelle adresse

                        const label = document.createElement('label');
                        label.htmlFor = `addr_${data.address.id}`;
                        label.className = 'address-label';

                        const headerDiv = document.createElement('div');
                        headerDiv.className = 'address-header';
                        const nameSpan = document.createElement('span');
                        nameSpan.className = 'address-name';
                        nameSpan.textContent = data.address.last_name || 'Utilisateur';
                        const checkIcon = document.createElement('i');
                        checkIcon.className = 'fa-solid fa-circle-check check-icon';
                        headerDiv.appendChild(nameSpan);
                        headerDiv.appendChild(checkIcon);

                        const bodyDiv = document.createElement('div');
                        bodyDiv.className = 'address-body';
                        const pAddress = document.createElement('p');
                        pAddress.textContent = data.address.address || '';
                        
                        const pMeta = document.createElement('p');
                        pMeta.className = 'address-meta';
                        pMeta.textContent = `${data.address.city_name || ''} - ${data.address.postal_code || ''} | Tél: ${data.address.mobile || ''}`;

                        bodyDiv.appendChild(pAddress);
                        bodyDiv.appendChild(pMeta);

                        label.appendChild(headerDiv);
                        label.appendChild(bodyDiv);

                        newDiv.appendChild(inputRadio);
                        newDiv.appendChild(label);

                        addressList.appendChild(newDiv);
                    }
                } else {
                    showOrderToast(data.message || "Erreur lors de l'enregistrement.", 'danger');
                }
            } catch (error) {
                console.error("Erreur d'ajout d'adresse :", error);
                showOrderToast("Une erreur de communication est survenue.", 'danger');
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Enregistrer l\'adresse';
                }
            }
        });
    }

    const btnContinueToSummary = document.getElementById('btnContinueToSummary');
    if (btnContinueToSummary) {
        btnContinueToSummary.addEventListener('click', async function() {
            
            const errBox = document.getElementById('jsErrorMessage');
            if (errBox) errBox.classList.add('display-none-box');

            const selectedAddress = document.querySelector('input[name="selected_address"]:checked');
            const selectedShipping = document.querySelector('input[name="selected_shipping"]:checked');

            if (!selectedAddress || !selectedShipping) {
                if (errBox) {
                    // SÉCURITÉ : Nettoyage sécurisé
                    errBox.innerHTML = '';
                    const errIcon = document.createElement('i');
                    errIcon.className = 'fa-solid fa-triangle-exclamation';
                    const errMsg = document.createTextNode(' Veuillez sélectionner une adresse et un mode de livraison.');
                    errBox.appendChild(errIcon);
                    errBox.appendChild(errMsg);
                    errBox.classList.remove('display-none-box');
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            const addressId = selectedAddress.value;
            const shippingId = selectedShipping.value;

            try {
                const formData = new URLSearchParams();
                formData.append('addressId', addressId);
                formData.append('shippingId', shippingId);
                // SÉCURITÉ : Ajout du jeton CSRF
                formData.append('csrf_token', getCsrfToken());

                const response = await fetch(`${baseUrl}Order/saveAddressSession`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const data = await response.json();
                
                if (data.status === 'success') {
                    window.location.href = `${baseUrl}Order/summary`;
                } else {
                    showOrderToast(data.message || "Erreur de sauvegarde de la sélection.", "danger");
                }

            } catch (error) {
                console.error("Erreur sauvegarde session :", error);
                showOrderToast("Erreur de connexion.", "danger");
            }
        });
    }

    // =========================================================================
    // 3. ÉTAPE 4 : GESTION DU CODE PROMO (PAIEMENT)
    // =========================================================================
    const btnVerifyPromo = document.getElementById('btnVerifyPromo');
    const codePromoInput = document.getElementById('codePromoInput');

    if (btnVerifyPromo && codePromoInput) {
        btnVerifyPromo.addEventListener('click', async function() {
            const code = codePromoInput.value.trim();
            if (code === "") {
                showOrderToast("Veuillez saisir un code promo.", "danger");
                return;
            }

            btnVerifyPromo.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const formData = new URLSearchParams();
                formData.append('code', code);
                // SÉCURITÉ : Ajout du jeton CSRF pour vérifier le code
                formData.append('csrf_token', getCsrfToken());

                const response = await fetch(`${baseUrl}Order/checkPromoCode`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: formData.toString()
                });

                const dataArr = await response.json();
                const promoData = dataArr[0];
                const newTotal = dataArr[1];

                const finalTotalAmount = document.getElementById('finalTotalAmount');
                const summaryDiscountLine = document.getElementById('summaryDiscountLine');
                const summaryDiscountValue = document.getElementById('summaryDiscountValue');

                if (promoData && promoData.id) {
                    codePromoInput.classList.remove('input-error');
                    codePromoInput.classList.add('input-success');
                    showOrderToast("Code de réduction appliqué avec succès !", "success");
                    
                    if(summaryDiscountLine && summaryDiscountValue) {
                        summaryDiscountLine.classList.remove('display-none-box');
                        // SÉCURITÉ : Utilisation de textContent
                        summaryDiscountValue.textContent = '- ' + new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(promoData.discount_amount || 0) + ' €';
                    }
                } else {
                    codePromoInput.classList.remove('input-success');
                    codePromoInput.classList.add('input-error');
                    showOrderToast("Le code saisi est invalide ou expiré.", "danger");
                    
                    if(summaryDiscountLine) {
                        summaryDiscountLine.classList.add('display-none-box');
                    }
                }
                
                if (finalTotalAmount) {
                    // SÉCURITÉ : textContent
                    finalTotalAmount.textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2 }).format(newTotal) + ' €';
                }
            } catch (error) { 
                console.error("Erreur Promo:", error);
                showOrderToast("Erreur lors de la vérification du code.", "danger"); 
            }
            
            btnVerifyPromo.innerHTML = 'Appliquer';
        });
    }

});