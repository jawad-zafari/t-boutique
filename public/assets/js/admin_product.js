/**
 * Logique JavaScript pour la gestion des produits (Panel Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de alert() ni confirm() natif (Normes UX/A11y)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Garde de sécurité : Empêche les liaisons d'événements multiples
    if (window.adminProductEventsBound) return;
    window.adminProductEventsBound = true;

    // =========================================================================
    // 1. SYSTÈME DYNAMIQUE DE NOTIFICATIONS (TOAST)
    // =========================================================================
    function showAdminToast(message, type = 'danger') {
        let toast = document.getElementById('adminToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'adminToastNotification';
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.padding = '15px 25px';
            toast.style.borderRadius = '8px';
            toast.style.color = '#fff';
            toast.style.fontWeight = 'bold';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1)';
            toast.style.transition = 'opacity 0.3s ease-in-out';
            document.body.appendChild(toast);
        }

        toast.style.backgroundColor = (type === 'danger') ? '#dc2626' : '#16a34a';
        
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
        }, 3500);
    }

    // =========================================================================
    // 2. FENÊTRE DE CONFIRMATION MODALE (REMPLACE confirm() NATIF)
    // =========================================================================
    function showAdminConfirm(message, onConfirmCallback) {
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(15, 23, 42, 0.7)';
        overlay.style.zIndex = '10000';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';

        const modal = document.createElement('div');
        modal.style.backgroundColor = '#fff';
        modal.style.padding = '30px';
        modal.style.borderRadius = '10px';
        modal.style.maxWidth = '450px';
        modal.style.textAlign = 'center';
        modal.style.boxShadow = '0 20px 25px -5px rgba(0,0,0,0.1)';
        modal.setAttribute('role', 'alertdialog');
        modal.setAttribute('aria-modal', 'true');

        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-circle-exclamation';
        icon.style.fontSize = '3.5rem';
        icon.style.color = '#ef4444';
        icon.style.marginBottom = '20px';

        const text = document.createElement('p');
        text.textContent = message;
        text.style.fontSize = '1.1rem';
        text.style.color = '#1e293b';
        text.style.marginBottom = '25px';
        text.style.lineHeight = '1.5';

        const btnGroup = document.createElement('div');
        btnGroup.style.display = 'flex';
        btnGroup.style.justifyContent = 'center';
        btnGroup.style.gap = '15px';

        const btnCancel = document.createElement('button');
        btnCancel.type = 'button';
        btnCancel.textContent = 'Annuler';
        btnCancel.style.padding = '10px 20px';
        btnCancel.style.border = '1px solid #cbd5e1';
        btnCancel.style.backgroundColor = '#f8fafc';
        btnCancel.style.color = '#475569';
        btnCancel.style.borderRadius = '6px';
        btnCancel.style.cursor = 'pointer';
        btnCancel.style.fontWeight = 'bold';

        const btnConfirm = document.createElement('button');
        btnConfirm.type = 'button';
        btnConfirm.textContent = 'Oui, supprimer';
        btnConfirm.style.padding = '10px 20px';
        btnConfirm.style.border = 'none';
        btnConfirm.style.backgroundColor = '#ef4444';
        btnConfirm.style.color = '#fff';
        btnConfirm.style.borderRadius = '6px';
        btnConfirm.style.cursor = 'pointer';
        btnConfirm.style.fontWeight = 'bold';

        const closeModal = () => document.body.removeChild(overlay);

        btnCancel.addEventListener('click', closeModal);
        btnConfirm.addEventListener('click', () => {
            closeModal();
            onConfirmCallback();
        });

        btnGroup.appendChild(btnCancel);
        btnGroup.appendChild(btnConfirm);
        modal.appendChild(icon);
        modal.appendChild(text);
        modal.appendChild(btnGroup);
        overlay.appendChild(modal);
        document.body.appendChild(overlay);
    }

    // =========================================================================
    // 3. LOGIQUE SÉCURISÉE DE SUPPRESSION (Produits, Galerie, Critiques)
    // =========================================================================
    
    function setupDeletion(btnId, formId, confirmMessage) {
        const btn = document.getElementById(btnId);
        const form = document.getElementById(formId);
        
        if (btn && form) {
            // Empêche la soumission du formulaire par défaut si le bouton est un submit
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const checkedBoxes = form.querySelectorAll('.row-checkbox:checked');
                
                if (checkedBoxes.length === 0) {
                    showAdminToast("Veuillez sélectionner au moins un élément pour cette action.");
                    return;
                }

                showAdminConfirm(confirmMessage, () => {
                    form.submit();
                });
            });
        }
    }

    setupDeletion('btnDeleteProducts', 'formProductsSelection', "Êtes-vous sûr de vouloir supprimer les produits sélectionnés ? Toutes les données liées (images, attributs, avis) seront également effacées.");
    setupDeletion('btnDeleteGallery', 'formGallerySelection', "Êtes-vous sûr de vouloir supprimer les images sélectionnées de la galerie ?");
    setupDeletion('btnDeleteReview', 'formReviews', "Êtes-vous sûr de vouloir supprimer définitivement ces critiques ?");

    // =========================================================================
    // 4. GESTION DU BOUTON RETOUR (SoC)
    // =========================================================================
    const backButtons = document.querySelectorAll('.js-back-button');
    backButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.history.back();
        });
    });

    // =========================================================================
    // 5. NETTOYAGE DE L'URL ET DISPARITION DES MESSAGES DE SUCCÈS
    // =========================================================================
    if (window.history.replaceState && window.location.search !== '') {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success') || urlParams.has('error')) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        }
    }

    // Faire disparaître les alertes sticky après 5 secondes
    const stickyAlerts = document.querySelectorAll('.alert-sticky');
    stickyAlerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s ease';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // =========================================================================
    // 6. SÉLECTIONNER / DÉSÉLECTIONNER TOUT
    // =========================================================================
    const selectAllCheckboxes = document.getElementById("selectAllCheckboxes");
    if (selectAllCheckboxes) {
        selectAllCheckboxes.addEventListener("change", (e) => {
            const isChecked = e.target.checked;
            const rowCheckboxes = document.querySelectorAll(".row-checkbox");
            rowCheckboxes.forEach(cb => cb.checked = isChecked);
        });
    }

    // =========================================================================
    // 7. GESTION DES TAGS DYNAMIQUES (Couleurs & Garanties) - ANTI-XSS
    // =========================================================================
    function setupTagSelection(selectId, containerId, inputName) {
        const selectEl = document.getElementById(selectId);
        const containerEl = document.getElementById(containerId);
        
        if (selectEl && containerEl) {
            selectEl.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                const value = opt.value;
                const title = opt.getAttribute('data-title');

                if (value !== "") {
                    // Vérifier si le tag existe déjà
                    if (containerEl.querySelector(`input[value="${value}"]`)) {
                        showAdminToast("Cet élément a déjà été ajouté.", "danger");
                        this.selectedIndex = 0;
                        return;
                    }

                    // Création sécurisée des éléments DOM
                    const span = document.createElement('span');
                    span.className = 'tag-item';
                    span.textContent = title + " ";

                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = inputName;
                    input.value = value;

                    const icon = document.createElement('i');
                    icon.className = 'fa-solid fa-circle-xmark btn-remove-tag';
                    icon.setAttribute('aria-hidden', 'true');
                    icon.title = 'Retirer cet élément';

                    span.appendChild(input);
                    span.appendChild(icon);
                    containerEl.appendChild(span);
                    
                    this.selectedIndex = 0; 
                }
            });
        }
    }

    setupTagSelection('colorSelect', 'colorsContainer', 'color[]');
    setupTagSelection('garanteeSelect', 'garanteesContainer', 'garantee[]');

    // Suppression d'un tag
    document.body.addEventListener('click', (event) => {
        if (event.target.classList.contains('btn-remove-tag')) {
            const tagItem = event.target.closest('.tag-item');
            if (tagItem) tagItem.remove();
        }
    });

});