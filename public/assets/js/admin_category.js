/**
 * Logique JavaScript pour la gestion des Catégories et Attributs (Panel Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de alert() ni confirm() (Normes UX/A11y/SÉCURITÉ)
 */
document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. SYSTÈME DYNAMIQUE DE NOTIFICATIONS (TOAST) AVEC ANTI-XSS
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
            toast.style.borderRadius = '6px';
            toast.style.color = '#fff';
            toast.style.fontWeight = 'bold';
            toast.style.zIndex = '9999';
            toast.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
            toast.style.transition = 'opacity 0.3s ease-in-out';
            document.body.appendChild(toast);
        }

        toast.style.backgroundColor = (type === 'danger') ? '#e03131' : '#2b8a3e';
        toast.innerHTML = ''; // Nettoyage
        
        const icon = document.createElement('i');
        icon.className = type === 'danger' ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
        icon.style.marginRight = '10px';
        
        // SÉCURITÉ : textNode empêche l'injection de code HTML/JS (DOM-based XSS)
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
    // 2. UTILISATION DU CONFIRM GLOBAL POUR RESTER COHÉRENT (Défini dans admin.js)
    // =========================================================================
    const showCustomConfirm = window.showGlobalAdminConfirm || function(msg, callback) {
        if(confirm(msg)) callback(); // Fallback de sécurité au cas où admin.js n'est pas chargé
    };

    // =========================================================================
    // 3. SOUMISSION : SUPPRESSION DE CATÉGORIES
    // =========================================================================
    const btnDeleteCategory = document.getElementById("btnDeleteCategory");
    const formAdmin = document.getElementById("formActionAdmin");

    if (btnDeleteCategory && formAdmin) {
        btnDeleteCategory.addEventListener("click", () => {
            const checkedBoxes = formAdmin.querySelectorAll(".row-checkbox:checked");
            
            if (checkedBoxes.length === 0) {
                showAdminToast("Veuillez sélectionner au moins une catégorie à supprimer.");
                return;
            }

            showCustomConfirm(
                "Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ? Toutes les sous-catégories et attributs liés seront définitivement supprimés.", 
                () => { formAdmin.submit(); }
            );
        });
    }

    // =========================================================================
    // 4. SOUMISSION : SUPPRESSION D'ATTRIBUTS
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