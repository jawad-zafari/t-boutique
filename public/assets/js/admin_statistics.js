/**
 * Logique JavaScript pour les rapports et statistiques (Admin)
 * Architecture 100% Vanilla JS - Sécurisé et Orienté UX
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // =========================================================================
    // 1. VALIDATION DU FORMULAIRE DE STATISTIQUES (Contrôle des dates)
    // =========================================================================
    const formStatistics = document.getElementById('formStatistics');

    if (formStatistics) {
        formStatistics.addEventListener('submit', (event) => {
            
            // Récupération des valeurs des années sélectionnées par l'utilisateur
            const year1Element = document.querySelector('select[name="year1"]');
            const year2Element = document.querySelector('select[name="year2"]');
            
            if (year1Element && year2Element) {
                const year1 = parseInt(year1Element.value, 10);
                const year2 = parseInt(year2Element.value, 10);

                // Validation UX : l'année de fin ne peut pas être antérieure à l'année de début
                if (year2 < year1) {
                    event.preventDefault(); // On bloque l'envoi du formulaire au serveur
                    
                    // Utilisation de notre système global de Toast (Clean Architecture)
                    if (typeof window.showGlobalAdminToast === 'function') {
                        window.showGlobalAdminToast("Erreur : L'année de fin ne peut pas être antérieure à l'année de début.", "danger");
                    } else {
                        // Fallback de sécurité natif
                        alert("L'année de fin ne peut pas être antérieure à l'année de début.");
                    }
                }
            }
        });
    }

    // =========================================================================
    // 2. GESTION DU BOUTON RETOUR (SoC - Séparation des préoccupations)
    // =========================================================================
    const backButtons = document.querySelectorAll('.js-back-button');
    
    backButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            // Retourne à la page précédente de manière fluide via l'historique du navigateur
            window.history.back();
        });
    });

});