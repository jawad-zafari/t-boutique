<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <base href="<?= URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administration</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= URL ?>public/assets/css/main.css">
</head>
<body class="admin-login-body">

    <div class="admin-login-container">
        
        <div class="login-card">
            
            <div class="login-header">
                <i class="fa-solid fa-user-shield login-icon" aria-hidden="true"></i>
                <h2>Connexion à l'administration</h2>
                <p>Veuillez saisir vos identifiants pour accéder au panneau de contrôle.</p>
            </div>

            <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                <div class="alert-message alert-danger text-center" role="alert">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Identifiants incorrects ou accès non autorisé.
                </div>
            <?php endif; ?>

            <div id="jsLoginErrorMessage" class="alert-message alert-danger" style="display: none;" role="alert"></div>

            <form action="<?= URL ?>AdminLogin/checkUser" method="post" id="adminLoginForm">
                
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                
                <div class="form-group">
                    <label for="email"><i class="fa-solid fa-envelope" aria-hidden="true"></i> Adresse e-mail :</label>
                    <input type="email" id="email" name="email" class="form-control" dir="ltr" placeholder="admin@boutique.fr" autocomplete="username" required aria-required="true">
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fa-solid fa-lock" aria-hidden="true"></i> Mot de passe :</label>
                    <input type="password" id="password" name="password" class="form-control" dir="ltr" placeholder="••••••••" autocomplete="current-password" required aria-required="true">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-admin-login" aria-label="Se connecter au panneau d'administration">
                        Se connecter <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>
                    </button>
                </div>

            </form>

        </div>
    </div>

    <script src="<?= URL ?>public/assets/js/admin_login.js" defer></script>

</body>
</html>