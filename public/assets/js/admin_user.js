/**
 * Logique JavaScript pour la gestion des utilisateurs (Panel Admin)
 * Architecture 100% Vanilla JS - Junior Friendly & Clean Code (SoC)
 */
document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. DÉTECTION DYNAMIQUE DE L'URL DE BASE (Sécurité du Routage)
    // =========================================================================
    // Le script lit l'URL depuis la balise <base> du document HTML de façon sécurisée
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // =========================================================================
    // 2. GESTION DES ACTIONS DE GROUPE SUR LES UTILISATEURS
    // =========================================================================
    const btnApplyUserAction = document.getElementById('btnApplyUserAction');
    const formUsersManage = document.getElementById('formUsersManage');
    const actionSelect = document.getElementById('actionSelect');

    if (btnApplyUserAction && formUsersManage && actionSelect) {
        
        btnApplyUserAction.addEventListener('click', () => {
            const checkboxes = formUsersManage.querySelectorAll('.row-checkbox:checked');
            
            // Validation : Au moins un utilisateur doit être sélectionné
            if (checkboxes.length === 0) {
                // Utilisation de l'API globale (Toast) si disponible, sinon fallback sur alert()
                if (typeof window.showGlobalAdminToast === 'function') {
                    window.showGlobalAdminToast("Veuillez sélectionner au moins un utilisateur.", "danger");
                } else {
                    alert("Veuillez sélectionner au moins un utilisateur.");
                }
                return;
            }

            const action = actionSelect.value;
            let urlAction = '';
            let messageConfirmation = '';

            // Définition de l'action et du message de confirmation selon le choix
            switch (action) {
                case '1':
                    urlAction = 'AdminUser/makeAdmin';
                    messageConfirmation = "Voulez-vous vraiment promouvoir ces utilisateurs au rang d'Administrateur (Niveau 1) ?";
                    break;
                case '2':
                    urlAction = 'AdminUser/makeEmployee';
                    messageConfirmation = "Voulez-vous vraiment changer le rôle de ces utilisateurs en Employé (Niveau 2) ?";
                    break;
                case '3':
                    urlAction = 'AdminUser/makeNormalUser';
                    messageConfirmation = "Voulez-vous vraiment définir ces comptes comme Utilisateur Normal (Niveau 3) ?";
                    break;
                case '4':
                    urlAction = 'AdminUser/deleteUser';
                    messageConfirmation = "ATTENTION : Voulez-vous vraiment supprimer définitivement ces comptes ? Cette action est irréversible.";
                    break;
            }

            // Routage sécurisé et utilisation du Modal de confirmation global personnalisé
            if (urlAction) {
                if (typeof window.showGlobalAdminConfirm === 'function') {
                    window.showGlobalAdminConfirm(messageConfirmation, () => {
                        formUsersManage.action = baseUrl + urlAction;
                        formUsersManage.submit();
                    });
                } else {
                    // Fallback de sécurité si le fichier global de modales n'est pas chargé
                    if (confirm(messageConfirmation)) {
                        formUsersManage.action = baseUrl + urlAction;
                        formUsersManage.submit();
                    }
                }
            }
        });
    }

    // =========================================================================
    // 3. FONCTIONNALITÉ UX : SÉLECTIONNER / DÉSÉLECTIONNER TOUT
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