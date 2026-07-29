<?php
// SÉCURITÉ CRITIQUE : Prévention du Reflected XSS
$errorMessage = $data['Error'] ?? 'Une erreur inattendue s\'est produite.';
$orderId = (int)($data['orderId'] ?? 0);
?>
<div class="payment-container container-small text-center padding-xl">

    <div class="error-icon-massive">
        <i class="fa-regular fa-circle-xmark" aria-hidden="true"></i>
    </div>

    <h2 class="error-title">Échec de l'opération</h2>

    <div class="alert-danger margin-bottom-lg" role="alert">
        <?= htmlspecialchars((string)$errorMessage, ENT_QUOTES, 'UTF-8') ?>
    </div>

    <a href="<?= URL ?>Checkout/index/<?= $orderId ?>" class="btn-action btn-secondary">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour à la commande
    </a>

</div>