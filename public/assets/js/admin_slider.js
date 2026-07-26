/**
 * Logique JavaScript pour la gestion du diaporama (Panel Admin)
 * Architecture 100% Vanilla JS - Sans confirm() natif (Normes UX/A11y)
 */
document.addEventListener("DOMContentLoaded", () => {

    // Garde de sécurité pour éviter les multiples attachements d'événements
    if (window.adminSliderEventsBound) return;
    window.adminSliderEventsBound = true;

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

        toast.appendChild(icon);
        toast.appendChild(document.createTextNode(message));

        toast.style.opacity = '1';
        toast.style.display = 'block';

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => { toast.style.display = 'none'; }, 300);
        }, 3500);
    }

    // =========================================================================
    // 2. MODAL DE CONFIRMATION PERSONNALISÉ
    // =========================================================================
    function showCustomConfirm(message, onConfirmCallback) {
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
    // 3. APERÇU EN DIRECT DE L'IMAGE (LIVE PREVIEW)
    // =========================================================================
    const imageInput = document.getElementById('slideImage');
    const livePreviewContainer = document.getElementById('liveImagePreview');
    const previewImgElement = document.getElementById('previewImgElement');

    if (imageInput && livePreviewContainer && previewImgElement) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Vérification basique du type MIME côté client
                if (!file.type.match('image/.*')) {
                    showAdminToast("Veuillez sélectionner un fichier image valide (JPG, PNG, WEBP).", "danger");
                    this.value = ''; 
                    livePreviewContainer.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImgElement.src = e.target.result;
                    livePreviewContainer.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                livePreviewContainer.style.display = 'none';
            }
        });
    }

    // =========================================================================
    // 4. SUPPRESSION GROUPÉE DES SLIDES (AVEC MODAL)
    // =========================================================================
    const btnDeleteSliders = document.getElementById('btnDeleteSliders');
    const formSlidersManage = document.getElementById('formSlidersManage');
    
    if (btnDeleteSliders && formSlidersManage) {
        btnDeleteSliders.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            const checkboxes = formSlidersManage.querySelectorAll('.row-checkbox:checked');
            
            if (checkboxes.length === 0) {
                showAdminToast("Veuillez sélectionner au moins un slide à supprimer.", "danger");
                return;
            }

            showCustomConfirm(
                "Êtes-vous sûr de vouloir supprimer définitivement ces slides ? L'image associée sur le serveur sera également effacée.", 
                () => { formSlidersManage.submit(); }
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
            rowCheckboxes.forEach(cb => cb.checked = isChecked);
        });
    }

    // =========================================================================
    // 6. NETTOYAGE DES PARAMÈTRES D'URL (DISPARITION DOUCE DES ALERTES)
    // =========================================================================
    if (window.history.replaceState && window.location.search !== '') {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('success') || urlParams.has('error')) {
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
            
            const stickyAlerts = document.querySelectorAll('.alert-sticky');
            stickyAlerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 4000);
            });
        }
    }
});