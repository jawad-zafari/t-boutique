/**
 * Validation et gestion dynamique du formulaire d'inscription client (Register)
 * Code 100% Vanilla JS - Conforme aux normes DWWM (Sécurité Anti-XSS et Accessibilité).
 */
document.addEventListener("DOMContentLoaded", () => {
    
    const formRegister = document.getElementById('formRegister');
    const errorContainer = document.getElementById('jsRegisterErrorMessage');
    
    if (formRegister && errorContainer) {
        formRegister.addEventListener('submit', (event) => {
            
            const lastNameInput = document.getElementById('lastName');
            const mobileInput = document.getElementById('mobile');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const passwordConfirmInput = document.getElementById('passwordConfirm');
            const rulesCheckbox = document.getElementById('rules');
            
            let isValid = true;
            let errors = []; // Stockage des messages d'erreur

            // Réinitialisation des styles d'erreur sur les champs de saisie
            [lastNameInput, mobileInput, emailInput, passwordInput, passwordConfirmInput].forEach(input => {
                if (input) {
                    input.classList.remove('is-invalid');
                }
            });
            
            // Masquer le conteneur de messages d'erreur
            errorContainer.classList.add('is-hidden');
            errorContainer.innerHTML = '';

            // 1. Validation du nom complet
            if (lastNameInput && lastNameInput.value.trim().length < 3) {
                isValid = false;
                errors.push("Veuillez saisir votre nom complet (minimum 3 caractères).");
                lastNameInput.classList.add('is-invalid');
            }

            // 2. Validation du numéro de mobile (Saisie numérique uniquement)
            if (mobileInput) {
                const mobileValue = mobileInput.value.replace(/\s+/g, '');
                // Accepte un format standard de mobile (10 à 14 chiffres)
                if (!/^\d{10,14}$/.test(mobileValue)) {
                    isValid = false;
                    errors.push("Veuillez entrer un numéro de mobile valide (10 à 14 chiffres).");
                    mobileInput.classList.add('is-invalid');
                }
            }

            // 3. Validation du format de l'adresse e-mail
            if (emailInput) {
                const emailValue = emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailValue || !emailRegex.test(emailValue)) {
                    isValid = false;
                    errors.push("Veuillez saisir une adresse e-mail valide.");
                    emailInput.classList.add('is-invalid');
                }
            }

            // 4. Validation de la sécurité et de la confirmation du mot de passe
            if (passwordInput && passwordConfirmInput) {
                const passwordVal = passwordInput.value;
                const passwordConfirmVal = passwordConfirmInput.value;

                if (passwordVal.length < 6) {
                    isValid = false;
                    errors.push("Le mot de passe doit contenir au moins 6 caractères.");
                    passwordInput.classList.add('is-invalid');
                } else if (passwordVal !== passwordConfirmVal) {
                    isValid = false;
                    errors.push("Les mots de passe ne correspondent pas.");
                    passwordInput.classList.add('is-invalid');
                    passwordConfirmInput.classList.add('is-invalid');
                }
            }

            // 5. Vérification de l'acceptation des conditions générales
            if (rulesCheckbox && !rulesCheckbox.checked) {
                isValid = false;
                errors.push("Vous devez accepter les conditions générales pour continuer.");
            }

            // Si le formulaire contient des erreurs, on bloque l'envoi et on affiche les erreurs
            if (!isValid) {
                event.preventDefault(); // Annulation de la soumission du formulaire

                // Création sécurisée de l'en-tête d'erreur (Anti-XSS)
                const headerBox = document.createElement('div');
                headerBox.className = 'error-header';

                const icon = document.createElement('i');
                icon.className = 'fa-solid fa-circle-exclamation';
                icon.setAttribute('aria-hidden', 'true');

                const titleElement = document.createElement('strong');
                titleElement.textContent = " Attention : Veuillez corriger les erreurs ci-dessous";

                headerBox.appendChild(icon);
                headerBox.appendChild(titleElement);
                errorContainer.appendChild(headerBox);

                // Construction d'une liste HTML pour présenter chaque erreur clairement
                const ul = document.createElement('ul');
                ul.className = 'error-list';

                errors.forEach(msg => {
                    const li = document.createElement('li');
                    li.textContent = msg; // SÉCURITÉ : textContent empêche l'injection HTML (XSS)
                    ul.appendChild(li);
                });

                errorContainer.appendChild(ul);
                errorContainer.classList.remove('is-hidden');

                // Défilement fluide vers le message d'erreur pour l'expérience utilisateur (UX)
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

});