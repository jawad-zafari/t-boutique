<?php
// SÉCURITÉ : Typage rigoureux et vérification stricte des variables (Anti-Crash)
$cart = $data['cartData'][0] ?? [];

// Protection : Si les données du panier sont corrompues et renvoient un entier, on force un tableau vide
if (!is_array($cart)) { 
    $cart = []; 
}

$totalProductsPrice = (float)($data['cartData'][1] ?? 0);
$totalDiscount = (float)($data['cartData'][2] ?? 0);
$shippingPrice = (float)($data['postPrice'] ?? 0);

$addressInfo = $data['addressInfo'] ?? [];
if (!is_array($addressInfo)) {
    $addressInfo = [];
}

$shippingType = (int)($data['postType'] ?? 1);

$finalTotal = $totalProductsPrice + $shippingPrice - $totalDiscount;
?>
<div class="checkout-modern-container order-page-wrapper">

    <div class="checkout-grid-layout">
        
        <div class="checkout-left-column">
            
            <div class="checkout-back-nav">
                <a href="<?= URL ?>Order/address" class="link-back-navigation"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i> Retour à la livraison</a>
            </div>

            <nav class="checkout-stepper-modern-bar">
                <ul class="stepper-steps-flex">
                    <li class="completed">Connexion</li>
                    <li class="completed">Livraison</li>
                    <li class="active" aria-current="step">Résumé</li>
                    <li>Paiement</li>
                </ul>
            </nav>

            <div class="checkout-section-card">
                <h3><i class="fa-solid fa-basket-shopping" aria-hidden="true"></i> Récapitulatif des articles</h3>
                
                <div class="summary-items-list-grid">
                    <?php if (!empty($cart)): foreach($cart as $item):
                        $qty = (int)($item['quantity'] ?? 1);
                        $price = (float)($item['price'] ?? 0);
                    ?>
                        <div class="summary-item-row-card">
                            <span class="product-qty-tag">x<?= $qty ?></span>
                            <span class="product-title-text"><?= htmlspecialchars($item['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="product-price-tag font-weight-bold"><?= number_format($price * $qty, 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="text-muted-color">Votre panier est vide ou les données sont indisponibles.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="checkout-section-card margin-top-md">
                <h3><i class="fa-solid fa-truck" aria-hidden="true"></i> Informations d'expédition</h3>
                
                <div class="shipping-info-box-flex">
                    <div class="info-block-item">
                        <strong>Destinataire :</strong>
                        <span><?= htmlspecialchars($addressInfo['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($addressInfo['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</span>
                    </div>
                    <div class="info-block-item">
                        <strong>Adresse de livraison :</strong>
                        <span><?= htmlspecialchars($addressInfo['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($addressInfo['city_name'] ?? $addressInfo['city'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($addressInfo['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="checkout-right-column">
            <div class="checkout-summary-card">
                <h3>Résumé financier</h3>
                
                <div class="summary-lines-box">
                    <div class="summary-line"><span class="label">Sous-total</span><span class="value"><?= number_format($totalProductsPrice, 2, ',', ' ') ?> €</span></div>
                    <div class="summary-line"><span class="label">Frais de port</span><span class="value"><?= $shippingPrice > 0 ? number_format($shippingPrice, 2, ',', ' ') . ' €' : 'Gratuit' ?></span></div>
                    
                    <?php if($totalDiscount > 0): ?>
                        <div class="summary-line text-danger"><span class="label">Remise totale</span><span class="value">- <?= number_format($totalDiscount, 2, ',', ' ') ?> €</span></div>
                    <?php endif; ?>
                    
                    <div class="summary-line-separator"></div>
                    
                    <div class="summary-line total-large-line">
                        <span class="label">Montant total</span>
                        <span class="value color-dark-slate"><?= number_format($finalTotal, 2, ',', ' ') ?> €</span>
                    </div>
                </div>

                <div class="margin-top-md">
                    <a href="<?= URL ?>Order/payment" class="btn-checkout-massive btn-full-width text-center" aria-label="Passer à l'étape finale du paiement">
                        Procéder au paiement <i class="fa-solid fa-lock" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="<?= URL ?>public/assets/js/order.js" defer></script>