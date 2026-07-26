/**
 * Validation du formulaire de connexion client (Login)
 * Architecture Vanilla JS - Respect du SoC (Zéro style en ligne)
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const formLogin = document.getElementById('formLogin');
    const errorContainer = document.getElementById('jsLoginErrorMessage');
    
    if (formLogin && errorContainer) {
        formLogin.addEventListener('submit', (event) => {
            
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            let isValid = true;
            let errorMessages = [];

            // 1. Réinitialisation des états (On utilise des classes CSS, pas de style inline)
            emailInput.classList.remove('input-error');
            passwordInput.classList.remove('input-error');
            
            // On vide le conteneur d'erreurs (le CSS :not(:empty) le masquera automatiquement)
            errorContainer.innerHTML = "";
            errorContainer.classList.remove('show-error');

            // 2. Validation de l'E-mail
            const emailValue = emailInput.value.trim();
            if (emailValue === "") {
                isValid = false;
                errorMessages.push("L'adresse e-mail est requise.");
                emailInput.classList.add('input-error'); // Ajout de la classe d'erreur
            } else {
                // Expression régulière standard pour valider le format de l'e-mail
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailValue)) {
                    isValid = false;
                    errorMessages.push("Le format de l'adresse e-mail n'est pas valide.");
                    emailInput.classList.add('input-error');
                }
            }

            // 3. Validation du Mot de passe
            if (passwordInput.value.trim() === "") {
                isValid = false;
                errorMessages.push("Le mot de passe est requis.");
                passwordInput.classList.add('input-error'); // Ajout de la classe d'erreur
            }

            // 4. Affichage des erreurs et blocage de l'envoi
            if (!isValid) {
                event.preventDefault(); 
                
                // SÉCURITÉ (Anti-XSS) : Création sécurisée des éléments DOM
                const icon = document.createElement('i');
                icon.className = "fa-solid fa-circle-exclamation";
                icon.setAttribute('aria-hidden', 'true');
                errorContainer.appendChild(icon);

                const textSpan = document.createElement('span');
                textSpan.textContent = " " + errorMessages.join(" ");
                errorContainer.appendChild(textSpan);
                
                // Activation visuelle via une classe CSS
                errorContainer.classList.add('show-error');
            }
        });
    }
});