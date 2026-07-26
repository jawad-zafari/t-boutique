/**
 * Fichier JavaScript principal pour le panneau d'administration
 * Fournit des utilitaires globaux (Toasts, Modals) pour remplacer alert() et confirm()
 */

document.addEventListener("DOMContentLoaded", function() {
    // Initialisation globale du panel d'administration
});

// =========================================================================
// 1. SYSTÈME GLOBAL DE NOTIFICATIONS (TOAST) - ANTI-XSS
// =========================================================================
window.showGlobalAdminToast = function(message, type = 'danger') {
    let toast = document.getElementById('globalAdminToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'globalAdminToast';
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.left = '20px'; // Placé à gauche pour être global
        toast.style.padding = '15px 25px';
        toast.style.borderRadius = '8px';
        toast.style.color = '#fff';
        toast.style.fontWeight = 'bold';
        toast.style.zIndex = '10000';
        toast.style.boxShadow = '0 10px 15px -3px rgba(0,0,0,0.1)';
        toast.style.transition = 'opacity 0.3s ease-in-out';
        document.body.appendChild(toast);
    }

    toast.style.backgroundColor = (type === 'danger') ? '#dc2626' : '#16a34a';
    toast.innerHTML = ''; // Réinitialisation sécurisée
    
    const icon = document.createElement('i');
    icon.className = (type === 'danger') ? 'fa-solid fa-triangle-exclamation' : 'fa-solid fa-circle-check';
    icon.style.marginRight = '10px';
    
    toast.appendChild(icon);
    toast.appendChild(document.createTextNode(message)); // SÉCURITÉ : textNode contre le XSS
    
    toast.style.opacity = '1';
    toast.style.display = 'block';

    setTimeout(() => { 
        toast.style.opacity = '0'; 
        setTimeout(() => { toast.style.display = 'none'; }, 300);
    }, 3500);
};

// =========================================================================
// 2. MODAL GLOBAL DE CONFIRMATION (A11y Compliant)
// =========================================================================
window.showGlobalAdminConfirm = function(message, onConfirmCallback) {
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100%';
    overlay.style.height = '100%';
    overlay.style.backgroundColor = 'rgba(15, 23, 42, 0.7)';
    overlay.style.zIndex = '10500';
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
    btnConfirm.textContent = 'Confirmer';
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
};

// =========================================================================
// 3. FONCTIONS GLOBALES SÉCURISÉES (Remplacent les anciennes fonctions)
// =========================================================================
window.soumettreSuppression = function() {
    const casesCochees = document.querySelectorAll('.admin-checkbox:checked');
    
    if (casesCochees.length === 0) {
        window.showGlobalAdminToast("Veuillez sélectionner au moins un élément pour cette action.");
        return;
    }

    window.showGlobalAdminConfirm("Êtes-vous sûr de vouloir supprimer les éléments sélectionnés ? Cette action est irréversible.", () => {
        const form = document.getElementById('formActionAdmin');
        if (form) form.submit();
    });
};

window.soumettreFormulaire = function() {
    const form = document.getElementById('formActionAdmin');
    if (form) {
        form.submit();
    }
};