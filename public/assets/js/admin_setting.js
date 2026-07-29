/**
 * Logique JavaScript pour la page des paramètres (Admin)
 * Séparation stricte du code (SoC) - 100% Vanilla JS
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Fonction réutilisable pour initialiser les sélecteurs de couleur
    function setupColorPicker(inputId) {
        const inputElement = document.getElementById(inputId);
        if (inputElement) {
            inputElement.addEventListener('click', () => {
                // Vérifie si l'instance jscolor (librairie externe) est bien attachée à l'élément
                if (inputElement.jscolor) {
                    inputElement.jscolor.show();
                }
            });
        }
    }

    setupColorPicker('bodyColor');
    setupColorPicker('menuColor');

    // =========================================================================
    // NETTOYAGE DE L'URL ET ANIMATION DE SUCCÈS (Clean URL Pattern)
    // Améliore l'UX et empêche la re-soumission accidentelle en rechargeant
    // =========================================================================
    if (window.history.replaceState && window.location.search !== '') {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('success')) {
            // Nettoie l'URL en retirant le paramètre GET ?success=1 sans rafraîchir la page
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
            
            // Animation de disparition douce pour le message de succès
            const alertBox = document.querySelector('.alert-sticky.success');
            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.transition = 'opacity 0.5s ease';
                    alertBox.style.opacity = '0';
                    // Supprime complètement l'élément du DOM après l'animation
                    setTimeout(() => alertBox.remove(), 500);
                }, 4000);
            }
        }
    }
});