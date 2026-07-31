<?php
// SÉCURITÉ : Récupération du paramètre de retour (Intended URL) de manière propre et protégée contre XSS
$backUrl = isset($_GET['back']) ? htmlspecialchars($_GET['back'], ENT_QUOTES, 'UTF-8') : '';
?>
<div class="login-container">
    <div class="login-box">
        
        <div class="login-info">
            <div class="info-icon">
                <i class="fa-solid fa-users-viewfinder" aria-hidden="true"></i>
            </div>
            <h2>Bienvenue sur notre boutique</h2>
            <ul class="benefits-list">
                <li><i class="fa-solid fa-bolt" aria-hidden="true"></i> Achetez plus rapidement et plus simplement</li>
                <li><i class="fa-solid fa-list-check" aria-hidden="true"></i> Gérez facilement votre historique d'achats</li>
                <li><i class="fa-solid fa-heart" aria-hidden="true"></i> Créez vos listes d'envies et suivez leurs évolutions</li>
            </ul>
        </div>

        <div class="login-form-section">
            <h3><i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i> Connexion à votre compte</h3>
            
            <div id="jsLoginErrorMessage" class="alert-message alert-danger-modern" role="alert"></div>
            
            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert-message alert-danger-modern show-error" role="alert">
                    <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
                    <span> Identifiants incorrects. Veuillez réessayer.</span>
                </div>
            <?php endif; ?>

            <form action="<?= URL ?>Login/checkUser" method="POST" id="formLogin" class="modern-form">
                
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                
                <?php if(!empty($backUrl)): ?>
                    <input type="hidden" name="back_url" value="<?= $backUrl ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope" aria-hidden="true"></i> Adresse E-mail :</label>
                    <input type="email" id="email" name="email" class="form-control" dir="ltr" placeholder="exemple@email.com" autocomplete="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock" aria-hidden="true"></i> Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" dir="ltr" placeholder="••••••••" autocomplete="current-password" required>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="rememberMe" name="remember_me" value="1">
                    <label for="rememberMe">Se souvenir de moi</label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-action" aria-label="Se connecter au site">
                        Se connecter <i class="fa-solid fa-check" aria-hidden="true"></i>
                    </button>
                </div>

                <div class="register-redirect">
                    Nouveau client ? <a href="<?= URL ?>Register/index" class="register-link">Créer un compte</a>
                </div>

            </form>
        </div>

    </div>
</div>

<script src="<?= URL ?>public/assets/js/login.js" defer></script>