/**
 * Logique JavaScript pour la gestion des commandes (Panel Admin)
 * Architecture 100% Vanilla JS - Sans alert() ni confirm() natif (Normes UX/A11y strictes)
 */
document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. SYSTÈME DYNAMIQUE DE NOTIFICATIONS (TOAST)
    // =========================================================================
    function showOrderToast(message, type = 'danger') {
        let toast = document.getElementById('adminToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'adminToastNotification';
            toast.style.position = 'fixed';
            toast.style.bottom = '20px';
            toast.style.right = '20px';
            toast.style.padding = '15px 25px';
            toast.style.borderRadius = '6px';
            toast.style.color = '#fff';
            toast.style.fontWeight = 'bold';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
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
    function showOrderConfirm(message, onConfirmCallback) {
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0,0,0,0.6)';
        overlay.style.zIndex = '10000';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';

        const modal = document.createElement('div');
        modal.style.backgroundColor = '#fff';
        modal.style.padding = '25px';
        modal.style.borderRadius = '8px';
        modal.style.maxWidth = '400px';
        modal.style.textAlign = 'center';
        modal.style.boxShadow = '0 10px 15px rgba(0,0,0,0.2)';
        modal.setAttribute('role', 'alertdialog');
        modal.setAttribute('aria-modal', 'true');

        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-circle-exclamation';
        icon.style.fontSize = '3rem';
        icon.style.color = '#ef4444';
        icon.style.marginBottom = '15px';

        const text = document.createElement('p');
        text.textContent = message;
        text.style.fontSize = '1.1rem';
        text.style.color = '#1e293b';
        text.style.marginBottom = '20px';

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
    // 3. GESTION DU FORMULAIRE DES COMMANDES ET DES ACTIONS EN MASSE
    // =========================================================================
    const form = document.getElementById('formOrdersSelection');
    const bulkStatusSelect = document.getElementById('bulkStatusSelect');

    if (form) {
        form.addEventListener('submit', (e) => {
            // On empêche la soumission par défaut pour gérer la logique proprement
            e.preventDefault();

            const checkedBoxes = form.querySelectorAll('.row-checkbox:checked');
            
            // Validation 1 : Vérifier si au moins une case est cochée
            if (checkedBoxes.length === 0) {
                showOrderToast("Veuillez sélectionner au moins une commande.");
                return;
            }

            const submitter = e.submitter;
            const targetAction = submitter ? submitter.getAttribute('formaction') : form.action;

            // Validation 2 : Action de modification de statut en masse
            if (submitter && submitter.id === 'btnBulkUpdateStatus') {
                if (!bulkStatusSelect || !bulkStatusSelect.value) {
                    showOrderToast("Veuillez choisir un nouveau statut à appliquer.");
                    return;
                }
                // Si valide, soumettre vers l'URL spécifique
                form.action = targetAction;
                form.submit();
                return;
            }

            // Validation 3 : Action de suppression avec confirmation
            if (submitter && submitter.id === 'btnDeleteOrders') {
                showOrderConfirm("Êtes-vous sûr de vouloir supprimer les commandes sélectionnées ? Cette action est irréversible.", () => {
                    form.action = targetAction;
                    form.submit();
                });
                return;
            }

            // Fallback (sécurité de soumission générique)
            form.submit();
        });
    }

    // =========================================================================
    // 4. GESTION DES BOUTONS INDÉPENDANTS (Impression et Retour)
    // =========================================================================
    
    // Impression de la facture
    const btnPrintInvoice = document.getElementById('btnPrintInvoice');
    if (btnPrintInvoice) {
        btnPrintInvoice.addEventListener('click', (e) => {
            e.preventDefault();
            window.print();
        });
    }

    // Bouton de retour dans l'historique
    const btnBackHistory = document.getElementById('btnBackHistory');
    if (btnBackHistory) {
        btnBackHistory.addEventListener('click', (e) => {
            e.preventDefault();
            window.history.back();
        });
    }

    // =========================================================================
    // 5. FONCTIONNALITÉ UX : SÉLECTIONNER / DÉSÉLECTIONNER TOUT
    // =========================================================================
    const selectAllCheckboxes = document.getElementById("selectAllCheckboxes");
    if (selectAllCheckboxes) {
        selectAllCheckboxes.addEventListener("change", (e) => {
            const isChecked = e.target.checked;
            const rowCheckboxes = document.querySelectorAll(".row-checkbox");
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });
    }

});