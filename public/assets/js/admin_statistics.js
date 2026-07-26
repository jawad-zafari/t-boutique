/**
 * Logique JavaScript pour les rapports et statistiques (Admin)
 * Architecture 100% Vanilla JS - Junior Friendly & Clean Code
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // =========================================================================
    // 1. VALIDATION DU FORMULAIRE DE STATISTIQUES (Dates)
    // =========================================================================
    const formStatistics = document.getElementById('formStatistics');

    if (formStatistics) {
        formStatistics.addEventListener('submit', (event) => {
            
            // Récupération des valeurs des années sélectionnées par l'utilisateur
            const year1Element = document.querySelector('select[name="year1"]');
            const year2Element = document.querySelector('select[name="year2"]');
            
            if (year1Element && year2Element) {
                const year1 = parseInt(year1Element.value);
                const year2 = parseInt(year2Element.value);

                // Validation : l'année de fin ne peut pas être dans le passé par rapport au début
                if (year2 < year1) {
                    event.preventDefault(); // On bloque l'envoi du formulaire au serveur
                    
                    // Utilisation de notre système global de Toast (au lieu du vieux alert() bloquant)
                    if (typeof window.showGlobalAdminToast === 'function') {
                        window.showGlobalAdminToast("Erreur : L'année de fin ne peut pas être antérieure à l'année de début.", "danger");
                    } else {
                        // Fallback de sécurité au cas où le script global ne serait pas chargé
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
            // Retourne à la page précédente dans l'historique du navigateur
            window.history.back();
        });
    });

});