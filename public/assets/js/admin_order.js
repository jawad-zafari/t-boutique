/**
 * Logique JavaScript pour la gestion des commandes (Panel Admin)
 * Architecture 100% Vanilla JS - Réutilisation des utilitaires globaux (Principe DRY)
 */
document.addEventListener("DOMContentLoaded", () => {

    const showToast = window.showGlobalAdminToast || alert;
    const showConfirm = window.showGlobalAdminConfirm || ((msg, cb) => { if(confirm(msg)) cb(); });

    // =========================================================================
    // 1. SOUMISSION DES ACTIONS EN MASSE SUR LES COMMANDES
    // =========================================================================
    const btnBulkUpdateStatus = document.getElementById('btnBulkUpdateStatus');
    const btnDeleteSelectedOrders = document.getElementById('btnDeleteSelectedOrders');
    const formOrdersSelection = document.getElementById('formOrdersSelection');

    if (btnBulkUpdateStatus && formOrdersSelection) {
        btnBulkUpdateStatus.addEventListener('click', (e) => {
            const checkedBoxes = formOrdersSelection.querySelectorAll('.row-checkbox:checked');
            const bulkSelect = document.getElementById('bulkStatusSelect');

            if (checkedBoxes.length === 0) {
                e.preventDefault();
                showToast("Veuillez sélectionner au moins une commande dans la liste.", "danger");
                return;
            }

            if (!bulkSelect || bulkSelect.value === "") {
                e.preventDefault();
                showToast("Veuillez choisir un nouveau statut à appliquer.", "danger");
                return;
            }
        });
    }

    if (btnDeleteSelectedOrders && formOrdersSelection) {
        btnDeleteSelectedOrders.addEventListener('click', (e) => {
            e.preventDefault();
            const checkedBoxes = formOrdersSelection.querySelectorAll('.row-checkbox:checked');

            if (checkedBoxes.length === 0) {
                showToast("Veuillez sélectionner au moins une commande à supprimer.", "danger");
                return;
            }

            showConfirm(
                "Êtes-vous sûr de vouloir supprimer définitivement les commandes sélectionnées ? Cette action est irréversible.",
                () => {
                    formOrdersSelection.action = btnDeleteSelectedOrders.getAttribute('formaction');
                    formOrdersSelection.submit();
                }
            );
        });
    }

    // =========================================================================
    // 2. GESTION DES BOUTONS INDÉPENDANTS (Impression et Retour)
    // =========================================================================
    const btnPrintInvoice = document.getElementById('btnPrintInvoice');
    if (btnPrintInvoice) {
        btnPrintInvoice.addEventListener('click', (e) => {
            e.preventDefault();
            window.print();
        });
    }

    const btnBackHistory = document.getElementById('btnBackHistory');
    if (btnBackHistory) {
        btnBackHistory.addEventListener('click', (e) => {
            e.preventDefault();
            window.history.back();
        });
    }

    // =========================================================================
    // 3. SÉLECTIONNER / DÉSÉLECTIONNER TOUT
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