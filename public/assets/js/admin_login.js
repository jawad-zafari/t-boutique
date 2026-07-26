/**
 * Logique JavaScript pour la validation du formulaire de connexion (Admin)
 * Architecture 100% Vanilla JS - Sans utilisation de alert() (Normes A11y/UX)
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
            errorContainer.innerHTML = "";

            const emailValue = emailInput.value.trim();
            const passwordValue = passwordInput.value.trim();

            // Validation de l'email
            if (emailValue === "") {
                isValid = false;
                erreurs.push("L'adresse e-mail est requise.");
                emailInput.style.borderColor = "#ff0055"; // Indication visuelle de l'erreur
            }

            // Validation du mot de passe
            if (passwordValue === "") {
                isValid = false;
                erreurs.push("Le mot de passe est requis.");
                passwordInput.style.borderColor = "#ff0055";
            }

            // Affichage dynamique des erreurs (Sans alert)
            if (!isValid) {
                event.preventDefault(); 
                
                // Construction du DOM de manière sécurisée (Anti-XSS)
                const icon = document.createElement('i');
                icon.className = "fa-solid fa-triangle-exclamation";
                icon.setAttribute('aria-hidden', 'true');
                
                const strong = document.createElement('strong');
                strong.textContent = " Attention :";
                
                errorContainer.appendChild(icon);
                errorContainer.appendChild(strong);
                
                const ul = document.createElement('ul');
                // Les styles sont désormais gérés proprement dans _admin_login.scss
                
                erreurs.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg;
                    ul.appendChild(li);
                });
                
                errorContainer.appendChild(ul);
                errorContainer.style.display = "block"; // Afficher la boîte d'erreur
            }
        });
    }
});