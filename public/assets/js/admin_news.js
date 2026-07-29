/**
 * Logique JavaScript pour la gestion des actualités (Panel Admin)
 * Architecture 100% Vanilla JS - Utilisation de l'utilitaire global (Principe DRY)
 */
document.addEventListener("DOMContentLoaded", () => {

    // Connexion au système global de confirmation défini dans admin.js (Fallback sur confirm() natif si non chargé)
    const showConfirm = window.showGlobalAdminConfirm || function(msg, callback) {
        if(confirm(msg)) callback();
    };

    // INTERCEPTION DE L'ACTION DE SUPPRESSION SUR LE TABLEAU DES ACTUALITÉS
    const deleteTriggers = document.querySelectorAll('.btn-delete-trigger');

    deleteTriggers.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // On récupère le formulaire parent le plus proche de ce bouton
            const parentForm = this.closest('.form-delete-news');
            
            if (parentForm) {
                // Appel du modal global (DRY Pattern)
                showConfirm(
                    "Êtes-vous sûr de vouloir supprimer définitivement cette actualité ? Cette action effacera également l'image associée sur le serveur.",
                    () => {
                        parentForm.submit(); // Exécution du formulaire POST sécurisé par CSRF
                    }
                );
            }
        });
    });

});