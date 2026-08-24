<?php

// SÉCURITÉ : Récupération et nettoyage du jeton CSRF transmis par le contrôleur
$csrfToken = $data['csrf_token'] ?? '';
?>

<div class="register-container">
    
    <div class="register-box">
        
        <div class="register-info">
            <div class="info-icon">
                <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
            </div>
            <h2>Créer un compte client</h2>
            <ul class="benefits-list">
                <li>
                    <i class="fa-solid fa-bolt" aria-hidden="true"></i> 
                    <span>Achetez plus rapidement et plus simplement</span>
                </li>
                <li>
                    <i class="fa-solid fa-list-check" aria-hidden="true"></i> 
                    <span>Gérez facilement votre historique d'achats</span>
                </li>
                <li>
                    <i class="fa-solid fa-heart" aria-hidden="true"></i> 
                    <span>Créez vos listes d'envies et favoris</span>
                </li>
            </ul>
        </div>

        <div class="register-form-section">
            <h3>
                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> 
                <span>Inscription</span>
            </h3>

            <div id="jsRegisterErrorMessage" class="alert-message alert-danger-modern is-hidden" role="alert"></div>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'exists'): ?>
                <div class="alert-message alert-danger-modern" role="alert">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                    <span>Un compte existe déjà avec cette adresse e-mail.</span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error']) && $_GET['error'] === 'validation'): ?>
                <div class="alert-message alert-danger-modern" role="alert">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                    <span>Veuillez vérifier les informations saisies dans le formulaire.</span>
                </div>
            <?php endif; ?>

            <form action="<?= URL ?>Register/save" method="post" id="formRegister" class="modern-form" autocomplete="off">
                
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label for="lastName">Nom complet * :</label>
                    <input type="text" id="lastName" name="last_name" class="form-control" placeholder="Jean Dupont" autocomplete="name" required aria-required="true">
                </div>

                <div class="form-group">
                    <label for="mobile">Numéro de mobile * :</label>
                    <input type="tel" id="mobile" name="mobile" class="form-control" dir="ltr" placeholder="0612345678" autocomplete="tel" required aria-required="true">
                </div>

                <div class="form-group">
                    <label for="email">Adresse E-mail * :</label>
                    <input type="email" id="email" name="email" class="form-control" dir="ltr" placeholder="exemple@email.com" autocomplete="email" required aria-required="true">
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe (min. 6 caractères) * :</label>
                    <input type="password" id="password" name="password" class="form-control" dir="ltr" placeholder="••••••••" autocomplete="new-password" required aria-required="true">
                </div>

                <div class="form-group">
                    <label for="passwordConfirm">Confirmer le mot de passe * :</label>
                    <input type="password" id="passwordConfirm" name="password_confirm" class="form-control" dir="ltr" placeholder="••••••••" autocomplete="new-password" required aria-required="true">
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="rules" name="rules" value="1" required aria-required="true">
                    <label for="rules">
                        J'ai lu et j'accepte les <a href="<?= URL ?>Page/conditions" class="terms-link">conditions générales</a>
                    </label>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="newsletter" name="newsletter" value="1">
                    <label for="newsletter">S'abonner à la newsletter pour recevoir nos offres</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-action" aria-label="Valider l'inscription">
                        <span>S'inscrire</span>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="login-redirect">
                    <span>Déjà inscrit ?</span> 
                    <a href="<?= URL ?>Login/index" class="login-link">Se connecter</a>
                </div>

            </form>
        </div>

    </div>
</div>

<script src="<?= URL ?>public/assets/js/register.js" defer></script>