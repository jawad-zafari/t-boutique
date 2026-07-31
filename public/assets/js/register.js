/**
 * Validation et gestion dynamique du formulaire d'inscription client (Register)
 * Code 100% Vanilla JS - Conforme aux normes DWWM.
 * Sécurité : Prévention stricte des failles DOM-Based XSS (Utilisation de textContent).
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
            let errors = []; 

            // 1. Réinitialisation des styles d'erreur sur les champs de saisie
            [lastNameInput, mobileInput, emailInput, passwordInput, passwordConfirmInput].forEach(input => {
                if (input) {
                    input.classList.remove('is-invalid');
                }
            });
            
            // Masquer et vider le conteneur de messages d'erreur
            errorContainer.classList.add('is-hidden');
            // SÉCURITÉ : Utilisation de textContent au lieu de innerHTML pour vider l'élément
            errorContainer.textContent = ''; 

            // 2. Validation du Nom Complet
            if (!lastNameInput || lastNameInput.value.trim() === '') {
                isValid = false;
                errors.push("Le nom complet est obligatoire.");
                if (lastNameInput) lastNameInput.classList.add('is-invalid');
            }

            // 3. Validation du Numéro de Mobile (Expression régulière)
            if (!mobileInput || mobileInput.value.trim() === '') {
                isValid = false;
                errors.push("Le numéro de mobile est obligatoire.");
                if (mobileInput) mobileInput.classList.add('is-invalid');
            } else if (!/^[0-9]{10,14}$/.test(mobileInput.value.trim())) {
                isValid = false;
                errors.push("Le format du numéro de mobile est invalide (uniquement des chiffres, entre 10 et 14).");
                if (mobileInput) mobileInput.classList.add('is-invalid');
            }

            // 4. Validation de l'E-mail
            if (!emailInput || emailInput.value.trim() === '') {
                isValid = false;
                errors.push("L'adresse e-mail est obligatoire.");
                if (emailInput) emailInput.classList.add('is-invalid');
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                isValid = false;
                errors.push("Le format de l'adresse e-mail est invalide.");
                if (emailInput) emailInput.classList.add('is-invalid');
            }

            // 5. Validation du Mot de passe
            if (!passwordInput || passwordInput.value.trim() === '') {
                isValid = false;
                errors.push("Le mot de passe est obligatoire.");
                if (passwordInput) passwordInput.classList.add('is-invalid');
            } else if (passwordInput.value.trim().length < 6) {
                isValid = false;
                errors.push("Le mot de passe doit contenir au moins 6 caractères pour des raisons de sécurité.");
                if (passwordInput) passwordInput.classList.add('is-invalid');
            }

            // 6. Validation de la Confirmation du Mot de passe
            if (!passwordConfirmInput || passwordConfirmInput.value.trim() === '') {
                isValid = false;
                errors.push("La confirmation du mot de passe est obligatoire.");
                if (passwordConfirmInput) passwordConfirmInput.classList.add('is-invalid');
            } else if (passwordInput.value.trim() !== passwordConfirmInput.value.trim()) {
                isValid = false;
                errors.push("Les deux mots de passe ne correspondent pas.");
                if (passwordConfirmInput) passwordConfirmInput.classList.add('is-invalid');
            }

            // 7. Validation des Conditions Générales
            if (!rulesCheckbox || !rulesCheckbox.checked) {
                isValid = false;
                errors.push("Vous devez accepter les conditions générales avant de continuer.");
            }

            // 8. Affichage des erreurs si la validation échoue
            if (!isValid) {
                event.preventDefault(); // Bloque la soumission du formulaire vers le serveur

                // SÉCURITÉ : Création sécurisée de l'en-tête d'erreur (DOM Building)
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

                // Construction d'une liste HTML pour présenter chaque erreur
                const ul = document.createElement('ul');
                ul.className = 'error-list';

                errors.forEach(msg => {
                    const li = document.createElement('li');
                    // SÉCURITÉ : textContent empêche l'injection de scripts (XSS)
                    li.textContent = msg; 
                    ul.appendChild(li);
                });

                errorContainer.appendChild(ul);
                errorContainer.classList.remove('is-hidden');

                // Défilement fluide vers la boîte de message pour une meilleure expérience utilisateur
                errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }

});