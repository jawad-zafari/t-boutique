/**
 * Logique JavaScript pour la page d'avis (Commentaires)
 * Code optimisé pour l'accessibilité (A11y)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    // Sélectionner tous les inputs de type range (Curseurs)
    const rangeInputs = document.querySelectorAll('.native-range');

    rangeInputs.forEach(input => {
        // Sélectionner le badge (l'étiquette affichant la note) correspondant à cet input
        const badge = input.nextElementSibling;

        // Écouter l'événement 'input' (déclenchement en temps réel lors du glissement)
        input.addEventListener('input', function() {
            // Mettre à jour le texte du badge
            badge.textContent = this.value;
            
            // Accessibilité : Mettre à jour aria-valuenow pour les lecteurs d'écran (Screen Readers)
            this.setAttribute('aria-valuenow', this.value);
            
            // Effet visuel (Changement de couleur selon la note)
            if(this.value >= 4) {
                badge.style.backgroundColor = '#36be2b'; // Vert (Bon)
            } else if (this.value == 3) {
                badge.style.backgroundColor = '#f39c12'; // Orange (Moyen)
            } else {
                badge.style.backgroundColor = '#e74c3c'; // Rouge (Mauvais)
            }
        });
        
        // Déclencher l'événement une fois au chargement pour initialiser les couleurs correctement
        input.dispatchEvent(new Event('input'));
    });

});