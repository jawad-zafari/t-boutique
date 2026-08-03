<div class="checkout-stepper-container">

    <nav class="checkout-stepper-nav" aria-label="Étapes de la commande">
        <div class="stepper-progress-bar"></div>
        <ul class="stepper-steps-list" role="tablist">
            <li class="step-item active" role="tab" aria-current="step" aria-selected="true">
                <span class="step-number">1</span>
                <span class="step-text">Connexion</span>
            </li>
            <li class="step-item" role="tab" aria-selected="false">
                <span class="step-number">2</span>
                <span class="step-text">Livraison</span>
            </li>
            <li class="step-item" role="tab" aria-selected="false">
                <span class="step-number">3</span>
                <span class="step-text">Paiement</span>
            </li>
        </ul>
    </nav>

    <div class="checkout-auth-split-grid">
        
        <div class="auth-action-card card-bg-white text-center">
            <div class="card-icon-box color-success"><i class="fa-solid fa-user-check" aria-hidden="true"></i></div>
            <h3>Déjà membre ?</h3>
            <p class="text-muted-sm">Connectez-vous à votre espace client pour finaliser votre achat rapidement.</p>
            <a href="<?= URL ?>Login/index?back=Order/address" class="btn-stepper-primary btn-full-width" aria-label="Se connecter à mon compte client">Se connecter</a>
        </div>
        
        <div class="auth-action-card card-bg-white text-center">
            <div class="card-icon-box color-primary"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></div>
            <h3>Pas encore membre ?</h3>
            <p class="text-muted-sm">Créez un compte en quelques clics ou continuez en mode invité pour valider votre panier.</p>
            <a href="<?= URL ?>Register/index?back=Order/address" class="btn-stepper-secondary btn-full-width margin-bottom-sm" aria-label="Créer un nouveau compte utilisateur">Créer un compte</a>
            
            <div class="divider-dashed"><span>OU</span></div>

            <a href="<?= URL ?>Order/address" class="btn-link-guest" aria-label="Continuer le processus de commande sans inscription">
                Continuer en mode invité <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
            </a>
        </div>

    </div>
</div>