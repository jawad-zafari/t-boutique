/**
 * Logique JavaScript pour la gestion et modération des commentaires (Panel Admin)
 * Clean Code - Reutilise les utilitaires globaux de admin.js (Principe DRY)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const btnApplyAction = document.getElementById('btnApplyAction');
    const formComments = document.getElementById('formCommentsManage');
    const actionSelect = document.getElementById('actionSelect');
    
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // Raccourcis vers les fonctions globales définies dans admin.js
    const showToast = window.showGlobalAdminToast || alert;
    const showConfirm = window.showGlobalAdminConfirm || ((msg, cb) => { if(confirm(msg)) cb(); });

    // =========================================================================
    // 1. EXÉCUTION SÉCURISÉE DES ACTIONS DE MODÉRATION EN GROUPE
    // =========================================================================
    if (btnApplyAction && formComments && actionSelect) {
        btnApplyAction.addEventListener('click', () => {
            const actionSelected = actionSelect.value;
            let actionUrl = '';
            let requiresConfirmation = false;
            let confirmMessage = '';
            
            if (actionSelected === '1') {
                actionUrl = 'AdminComment/confirm';
            } else if (actionSelected === '2') {
                actionUrl = 'AdminComment/unconfirm';
            } else if (actionSelected === '3') {
                actionUrl = 'AdminComment/delete';
                requiresConfirmation = true;
                confirmMessage = "Voulez-vous vraiment supprimer définitivement ces commentaires de la base de données ?";
            }

            const checkedBoxes = formComments.querySelectorAll('.row-checkbox:checked');
            
            if (checkedBoxes.length === 0) {
                showToast("Veuillez sélectionner au moins un commentaire pour appliquer l'action.", "danger");
                return;
            }

            const submitForm = () => {
                formComments.action = baseUrl + actionUrl;
                formComments.submit();
            };

            if (requiresConfirmation) {
                showConfirm(confirmMessage, submitForm);
            } else {
                submitForm();
            }
        });
    }

    // =========================================================================
    // 2. SÉLECTIONNER / DÉSÉLECTIONNER TOUT
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