/**
 * Logique JavaScript pour la gestion des questions (Panel Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de alert() ni confirm() natif (Normes UX/A11y)
 */
document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. DÉTECTION DYNAMIQUE DE L'URL DE BASE (Sans variable globale polluante)
    // =========================================================================
    const baseTag = document.querySelector('base');
    const baseUrl = baseTag ? baseTag.getAttribute('href') : '/';

    // =========================================================================
    // 2. SYSTÈME DYNAMIQUE DE NOTIFICATIONS (TOAST)
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
    // 3. FENÊTRE DE CONFIRMATION MODALE (REMPLACE confirm() NATIF)
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
    // 4. GESTION DES ACTIONS DE MASSE SUR LES QUESTIONS
    // =========================================================================
    const btnApply = document.getElementById('btnApplyAction');
    const actionSelect = document.getElementById('actionSelect');
    const form = document.getElementById('formQuestionsManage');

    if (btnApply && actionSelect && form) {
        btnApply.addEventListener('click', () => {
            const action = actionSelect.value;
            const checkedBoxes = form.querySelectorAll('.row-checkbox:checked');

            // Validation 1 : Vérifier si au moins une question est cochée
            if (checkedBoxes.length === 0) {
                showAdminToast("Veuillez sélectionner au moins une question pour appliquer l'action.");
                return;
            }

            // Routage sécurisé vers le contrôleur
            if (action === '1') {
                form.action = baseUrl + 'AdminQuestion/confirm';
                form.submit();
            } else if (action === '2') {
                form.action = baseUrl + 'AdminQuestion/unconfirm';
                form.submit();
            } else if (action === '3') {
                // Utilisation de notre modal customisé pour la suppression
                showAdminConfirm("Voulez-vous vraiment supprimer définitivement ces questions ? Cette action est irréversible.", () => {
                    form.action = baseUrl + 'AdminQuestion/delete';
                    form.submit();
                });
            }
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