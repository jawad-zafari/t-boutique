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

    toast.style.backgroundColor = (type === 'danger') ? '#e03131' : '#2b8a3e';
    toast.innerHTML = '';
    
    const icon = document.createElement('i');
    icon.className = type === 'danger' ? 'fa-solid fa-circle-exclamation' : 'fa-solid fa-circle-check';
    icon.style.marginRight = '10px';
    
    // SÉCURITÉ : Empêche l'injection HTML dans le toast
    const textNode = document.createTextNode(message);
    
    toast.appendChild(icon);
    toast.appendChild(textNode);

    toast.style.display = 'block';
    setTimeout(() => toast.style.opacity = '1', 10); // animation fix

    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => { toast.style.display = 'none'; }, 300);
    }, 4000);
};

// =========================================================================
// 2. MODAL DE CONFIRMATION GLOBALE - ANTI-XSS (Remplace window.confirm)
// =========================================================================
window.showGlobalAdminConfirm = function(message, onConfirmCallback) {
    const overlay = document.createElement('div');
    overlay.style.position = 'fixed';
    overlay.style.top = '0';
    overlay.style.left = '0';
    overlay.style.width = '100vw';
    overlay.style.height = '100vh';
    overlay.style.backgroundColor = 'rgba(0,0,0,0.6)';
    overlay.style.backdropFilter = 'blur(3px)';
    overlay.style.display = 'flex';
    overlay.style.justifyContent = 'center';
    overlay.style.alignItems = 'center';
    overlay.style.zIndex = '11000';

    const modal = document.createElement('div');
    modal.style.backgroundColor = '#fff';
    modal.style.padding = '30px';
    modal.style.borderRadius = '8px';
    modal.style.maxWidth = '400px';
    modal.style.width = '90%';
    modal.style.boxShadow = '0 15px 30px rgba(0,0,0,0.2)';
    modal.style.textAlign = 'center';
    modal.style.animation = 'modalSlideIn 0.3s ease';

    const icon = document.createElement('i');
    icon.className = 'fa-solid fa-triangle-exclamation';
    icon.style.fontSize = '2.5rem';
    icon.style.color = '#e03131';
    icon.style.marginBottom = '15px';

    const text = document.createElement('p');
    text.style.margin = '0 0 20px 0';
    text.style.fontSize = '1rem';
    text.style.color = '#343a40';
    // SÉCURITÉ : textContent au lieu de innerHTML pour éviter les failles
    text.textContent = message; 

    const btnGroup = document.createElement('div');
    btnGroup.style.display = 'flex';
    btnGroup.style.justifyContent = 'center';
    btnGroup.style.gap = '15px';

    const btnCancel = document.createElement('button');
    btnCancel.textContent = 'Annuler';
    btnCancel.style.padding = '10px 20px';
    btnCancel.style.border = '1px solid #ced4da';
    btnCancel.style.backgroundColor = '#f8f9fa';
    btnCancel.style.color = '#495057';
    btnCancel.style.borderRadius = '4px';
    btnCancel.style.cursor = 'pointer';
    btnCancel.style.fontWeight = 'bold';

    const btnConfirm = document.createElement('button');
    btnConfirm.textContent = 'Oui, continuer';
    btnConfirm.style.padding = '10px 20px';
    btnConfirm.style.border = 'none';
    btnConfirm.style.backgroundColor = '#e03131';
    btnConfirm.style.color = '#fff';
    btnConfirm.style.borderRadius = '4px';
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
// 3. FONCTIONS GLOBALES SÉCURISÉES (Soumission des formulaires)
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
    if (form) form.submit();
};