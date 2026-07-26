/**
 * Logique JavaScript pour la gestion des actualités (Panel Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de confirm() natif (Normes UX/A11y)
 */
document.addEventListener("DOMContentLoaded", () => {

    // =========================================================================
    // 1. BOÎTE DE DIALOGUE PERSONNALISÉE (REMPLACE LE confirm() DU NAVIGATEUR)
    // =========================================================================
    function showNewsDeleteConfirm(message, onConfirmCallback) {
        // Création de l'overlay de fond
        const overlay = document.createElement('div');
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(15, 23, 42, 0.6)'; // Fond sombre ardoise
        overlay.style.backdropFilter = 'blur(4px)'; // Effet de flou moderne
        overlay.style.zIndex = '10000';
        overlay.style.display = 'flex';
        overlay.style.alignItems = 'center';
        overlay.style.justifyContent = 'center';

        // Création de la boîte modale
        const modal = document.createElement('div');
        modal.style.backgroundColor = '#fff';
        modal.style.padding = '30px';
        modal.style.borderRadius = '12px';
        modal.style.maxWidth = '420px';
        modal.style.width = '90%';
        modal.style.textAlign = 'center';
        modal.style.boxShadow = '0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04)';
        modal.setAttribute('role', 'alertdialog');
        modal.setAttribute('aria-modal', 'true');

        // Icône d'avertissement
        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-circle-exclamation';
        icon.style.fontSize = '3.5rem';
        icon.style.color = '#ef4444'; // Rouge alerte
        icon.style.marginBottom = '20px';

        // Message textuel
        const text = document.createElement('p');
        text.textContent = message;
        text.style.fontSize = '1.1rem';
        text.style.color = '#334155';
        text.style.lineHeight = '1.6';
        text.style.marginBottom = '25px';

        // Groupe de boutons
        const btnGroup = document.createElement('div');
        btnGroup.style.display = 'flex';
        btnGroup.style.justifyContent = 'center';
        btnGroup.style.gap = '15px';

        const btnCancel = document.createElement('button');
        btnCancel.type = 'button';
        btnCancel.textContent = 'Annuler';
        btnCancel.style.padding = '10px 22px';
        btnCancel.style.border = '1px solid #cbd5e1';
        btnCancel.style.backgroundColor = '#f8fafc';
        btnCancel.style.color = '#475569';
        btnCancel.style.borderRadius = '8px';
        btnCancel.style.cursor = 'pointer';
        btnCancel.style.fontWeight = '600';
        btnCancel.style.transition = 'all 0.2s';

        const btnConfirm = document.createElement('button');
        btnConfirm.type = 'button';
        btnConfirm.textContent = 'Oui, supprimer';
        btnConfirm.style.padding = '10px 22px';
        btnConfirm.style.border = 'none';
        btnConfirm.style.backgroundColor = '#ef4444';
        btnConfirm.style.color = '#fff';
        btnConfirm.style.borderRadius = '8px';
        btnConfirm.style.cursor = 'pointer';
        btnConfirm.style.fontWeight = '600';
        btnConfirm.style.transition = 'all 0.2s';

        // Fermeture propre du modal
        const closeModal = () => document.body.removeChild(overlay);

        btnCancel.addEventListener('click', closeModal);
        btnConfirm.addEventListener('click', () => {
            closeModal();
            onConfirmCallback();
        });

        // Assemblage des éléments du DOM
        btnGroup.appendChild(btnCancel);
        btnGroup.appendChild(btnConfirm);
        
        modal.appendChild(icon);
        modal.appendChild(text);
        modal.appendChild(btnGroup);
        overlay.appendChild(modal);
        
        document.body.appendChild(overlay);
    }

    // =========================================================================
    // 2. INTERCEPTION DE L'ACTION DE SUPPRESSION SUR LE TABLEAU DES ACTUALITÉS
    // =========================================================================
    const deleteTriggers = document.querySelectorAll('.btn-delete-trigger');

    deleteTriggers.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // On récupère le formulaire parent le plus proche de ce bouton
            const parentForm = this.closest('.form-delete-news');
            
            if (parentForm) {
                // Appel du modal custom au lieu du confirm() synchrone bloqueur
                showNewsDeleteConfirm(
                    "Êtes-vous sûr de vouloir supprimer définitivement cette actualité ? Cette action effacera également l'image associée sur le serveur.",
                    () => {
                        parentForm.submit(); // Exécution du formulaire POST sécurisé par CSRF
                    }
                );
            }
        });
    });

});