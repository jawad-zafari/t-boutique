/**
 * Logique JavaScript pour la gestion des utilisateurs (Panel Admin)
 * Architecture 100% Vanilla JS - Clean Code & Principe DRY
 */
document.addEventListener("DOMContentLoaded", () => {

    // 1. Détection dynamique de l'URL de base
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // Raccourcis vers les utilitaires globaux définis dans admin.js
    const showToast = window.showGlobalAdminToast || alert;
    const showConfirm = window.showGlobalAdminConfirm || ((msg, cb) => { if(confirm(msg)) cb(); });

    // =========================================================================
    // 2. GESTION DES ACTIONS DE GROUPE SUR LES UTILISATEURS
    // =========================================================================
    const btnApplyUserAction = document.getElementById('btnApplyUserAction');
    const formUsersManage = document.getElementById('formUsersManage');
    const actionSelect = document.getElementById('actionSelect');

    if (btnApplyUserAction && formUsersManage && actionSelect) {
        
        btnApplyUserAction.addEventListener('click', (e) => {
            e.preventDefault();

            const checkboxes = formUsersManage.querySelectorAll('.row-checkbox:checked');
            
            // Validation : Au moins un utilisateur doit être sélectionné
            if (checkboxes.length === 0) {
                showToast("Veuillez sélectionner au moins un utilisateur pour appliquer une action.", "danger");
                return;
            }

            const actionValue = actionSelect.value;
            let urlAction = "";
            let messageConfirmation = "";

            switch (actionValue) {
                case "1":
                    urlAction = "AdminUser/changeLevel1";
                    messageConfirmation = "Voulez-vous vraiment promouvoir les utilisateurs sélectionnés au rang d'Administrateur ?";
                    break;
                case "2":
                    urlAction = "AdminUser/changeLevel2";
                    messageConfirmation = "Voulez-vous vraiment définir les utilisateurs sélectionnés comme Employés ?";
                    break;
                case "3":
                    urlAction = "AdminUser/changeLevel3";
                    messageConfirmation = "Voulez-vous vraiment passer les utilisateurs sélectionnés en Utilisateur Normal ?";
                    break;
                case "4":
                    urlAction = "AdminUser/delete";
                    messageConfirmation = "ATTENTION : Voulez-vous vraiment supprimer définitivement ces comptes utilisateurs ? Cette action est irréversible !";
                    break;
                default:
                    showToast("Action sélectionnée non valide.", "danger");
                    return;
            }

            // Soumission du formulaire après confirmation
            showConfirm(messageConfirmation, () => {
                formUsersManage.action = baseUrl + urlAction;
                formUsersManage.submit();
            });
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