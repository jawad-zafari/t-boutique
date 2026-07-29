/**
 * Logique JavaScript pour la validation du formulaire de connexion (Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de alert() (Normes A11y/UX)
 * Sécurité Anti-XSS : Utilisation exclusive de textContent et createElement.
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const loginForm = document.getElementById('adminLoginForm');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const errorContainer = document.getElementById('jsLoginErrorMessage');

    if (loginForm && errorContainer) {
        loginForm.addEventListener('submit', (event) => {
            let isValid = true;
            let erreurs = [];

            // Réinitialisation des styles d'erreurs
            emailInput.style.borderColor = "";
            passwordInput.style.borderColor = "";
            errorContainer.style.display = "none";
            errorContainer.innerHTML = ""; // Nettoyage du conteneur

            const emailValue = emailInput.value.trim();
            const passwordValue = passwordInput.value.trim();

            // Validation de l'email
            if (emailValue === "") {
                isValid = false;
                erreurs.push("L'adresse e-mail est requise.");
                emailInput.style.borderColor = "#dc2626"; // Indication visuelle de l'erreur (rouge)
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailValue)) {
                isValid = false;
                erreurs.push("Le format de l'adresse e-mail est invalide.");
                emailInput.style.borderColor = "#dc2626";
            }

            // Validation du mot de passe
            if (passwordValue === "") {
                isValid = false;
                erreurs.push("Le mot de passe est requis.");
                passwordInput.style.borderColor = "#dc2626";
            }

            // Affichage dynamique des erreurs (Sans alert et avec sécurité Anti-XSS)
            if (!isValid) {
                event.preventDefault(); // Empêcher la soumission du formulaire
                
                // Construction du DOM de manière sécurisée (Protection DOM-based XSS)
                const icon = document.createElement('i');
                icon.className = "fa-solid fa-triangle-exclamation";
                icon.setAttribute('aria-hidden', 'true');
                
                const strong = document.createElement('strong');
                strong.textContent = " Attention :";
                
                errorContainer.appendChild(icon);
                errorContainer.appendChild(strong);
                
                const ul = document.createElement('ul');
                ul.style.marginTop = "10px";
                ul.style.paddingLeft = "20px";
                
                erreurs.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg; // Sécurisé
                    ul.appendChild(li);
                });
                
                errorContainer.appendChild(ul);
                errorContainer.style.display = "block"; // Afficher la boîte d'erreur
            }
        });
    }
});