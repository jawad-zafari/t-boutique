/**
 * Logique JavaScript pour la gestion des Catégories et Attributs (Panel Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de alert() ni confirm() (Normes UX/A11y)
 */
document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. SYSTÈME DYNAMIQUE DE NOTIFICATIONS (TOAST)
    // =========================================================================
    function showAdminToast(message, type = 'danger') {
        let toast = document.getElementById('adminToastNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'adminToastNotification';
            // Styles injectés dynamiquement pour garantir l'affichage propre dans l'admin
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
        
        // SÉCURITÉ ANTI-XSS : Création manuelle des nœuds
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
    // 2. BOÎTE DE DIALOGUE PERSONNALISÉE (REMPLACE LE confirm() NATIF)
    // =========================================================================
    function showCustomConfirm(message, onConfirmCallback) {
        // Création de l'overlay sombre
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

        // Création de la boîte de dialogue
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
        btnCancel.textContent = 'Annuler';
        btnCancel.style.padding = '10px 20px';
        btnCancel.style.border = '1px solid #cbd5e1';
        btnCancel.style.backgroundColor = '#f8fafc';
        btnCancel.style.color = '#475569';
        btnCancel.style.borderRadius = '6px';
        btnCancel.style.cursor = 'pointer';
        btnCancel.style.fontWeight = 'bold';

        const btnConfirm = document.createElement('button');
        btnConfirm.textContent = 'Oui, supprimer';
        btnConfirm.style.padding = '10px 20px';
        btnConfirm.style.border = 'none';
        btnConfirm.style.backgroundColor = '#ef4444';
        btnConfirm.style.color = '#fff';
        btnConfirm.style.borderRadius = '6px';
        btnConfirm.style.cursor = 'pointer';
        btnConfirm.style.fontWeight = 'bold';

        // Gestion des événements
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
    // 3. LOGIQUE SÉCURISÉE DE SUPPRESSION DES CATÉGORIES
    // =========================================================================
    const btnDeleteCategory = document.getElementById("btnDeleteCategory");
    const formCategory = document.getElementById("formCategorySelection");

    if (btnDeleteCategory && formCategory) {
        btnDeleteCategory.addEventListener("click", () => {
            const checkedBoxes = formCategory.querySelectorAll(".row-checkbox:checked");
            
            if (checkedBoxes.length === 0) {
                showAdminToast("Veuillez sélectionner au moins une catégorie à supprimer.");
                return;
            }

            showCustomConfirm(
                "Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ainsi que toutes leurs sous-catégories ? Cette action est irréversible.", 
                () => { formCategory.submit(); }
            );
        });
    }

    // =========================================================================
    // 4. LOGIQUE SÉCURISÉE DE SUPPRESSION DES ATTRIBUTS
    // =========================================================================
    const btnDeleteAttribute = document.getElementById("btnDeleteAttribute");
    const formAttribute = document.getElementById("formAttributeSelection");

    if (btnDeleteAttribute && formAttribute) {
        btnDeleteAttribute.addEventListener("click", () => {
            const checkedBoxes = formAttribute.querySelectorAll(".row-checkbox:checked");
            
            if (checkedBoxes.length === 0) {
                showAdminToast("Veuillez sélectionner au moins un attribut à supprimer.");
                return;
            }

            showCustomConfirm(
                "Êtes-vous sûr de vouloir supprimer les attributs sélectionnés ? Les valeurs associées seront également perdues.", 
                () => { formAttribute.submit(); }
            );
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