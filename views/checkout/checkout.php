<?php
/**
 * Vue Checkout / Récapitulatif et statut de la commande
 * SÉCURITÉ DWWM : Protection Anti-XSS, typage strict et désérialisation sécurisée.
 */
$orderInfo = $data['orderInfo'] ?? [];
$orderId = (int)($orderInfo['id'] ?? 0);
$csrfToken = htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');

// SÉCURITÉ CRITIQUE : Protection contre l'injection d'objets via unserialize
$basketProducts = !empty($orderInfo['cart_data']) ? unserialize($orderInfo['cart_data'], ['allowed_classes' => false]) : [];

$isPaid = !empty($orderInfo['is_paid']); 
$paymentMethodId = (int)($orderInfo['payment_method_id'] ?? 1); // 1 = Carte (En ligne), 2 = Virement
    
// Logique conditionnelle pour lancer la simulation si c'est un paiement en ligne non effectué
$isPendingOnlinePayment = (!$isPaid && $paymentMethodId === 1);

// Calculs financiers sécurisés
$subTotal = 0;
if (is_array($basketProducts)) {
    foreach ($basketProducts as $item) {
        // NETTOYAGE : Utilisation exclusive de 'quantity'
        $qty = (int)($item['quantity'] ?? 1);
        $price = (float)($item['price'] ?? 0);
        $subTotal += ($price * $qty);
    }
}
$shippingCost = (float)($orderInfo['shipping_price'] ?? 0);
$totalPayable = (float)($orderInfo['total_amount'] ?? ($subTotal + $shippingCost));
?>
<div class="checkout-modern-container order-page-wrapper" data-csrf="<?= $csrfToken ?>">

    <?php if ($isPendingOnlinePayment): ?>
        
        <div id="mockPaymentLoader" data-order-id="<?= $orderId ?>" class="payment-processing-box text-center padding-xl margin-top-md">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <h2 class="margin-top-md color-primary"><i class="fa-solid fa-lock" aria-hidden="true"></i> Traitement de votre paiement...</h2>
            <p class="text-muted-sm font-size-large margin-top-sm">Connexion sécurisée au serveur bancaire en cours.</p>
            <div class="alert-info-bank margin-top-md" style="display: inline-block; text-align: left;">
                <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Veuillez ne pas fermer ni rafraîchir cette page.
            </div>
        </div>

        <style>
            .payment-processing-box { background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); padding: 80px 20px; }
            .spinner-border { display: inline-block; width: 4rem; height: 4rem; border-width: 0.4rem; border-radius: 50%; border-style: solid; border-color: #0033fe transparent #0033fe transparent; animation: spin 1s linear infinite; }
            @keyframes spin { 100% { transform: rotate(360deg); } }
            .visually-hidden { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0; }
        </style>

    <?php else: ?>

        <div class="invoice-container margin-top-md">
            
            <?php if ($isPaid): ?>
                <div class="elegant-alert-banner success">
                    <div class="banner-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
                    <div class="banner-content">
                        <h2>Commande validée avec succès !</h2>
                        <p>Merci pour votre achat. Votre paiement a bien été reçu (Réf transaction : <strong><?= htmlspecialchars($orderInfo['transaction_id_after'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></strong>).</p>
                    </div>
                </div>
            <?php elseif ($paymentMethodId === 2): ?>
                <div class="elegant-alert-banner info">
                    <div class="banner-icon"><i class="fa-solid fa-clock" aria-hidden="true"></i></div>
                    <div class="banner-content">
                        <h2>Commande en attente de virement</h2>
                        <p>Votre commande est enregistrée. Elle sera expédiée dès réception de votre virement bancaire.</p>
                    </div>
                </div>
            <?php endif; ?>

            <div class="checkout-grid-layout margin-top-md">
                
                <div class="checkout-left-column">
                    <div class="checkout-section-card">
                        <div class="invoice-header-bar">
                            <div class="invoice-ref-box">
                                <span class="ref-label">N° de commande :</span>
                                <span class="ref-code"><?= htmlspecialchars($orderInfo['barcode'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="invoice-date-box">
                                <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                                <?= htmlspecialchars($orderInfo['created_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                            </div>
                        </div>

                        <h4 class="summary-sub-title margin-top-md"><i class="fa-solid fa-truck" aria-hidden="true"></i> Informations d'expédition</h4>
                        <p class="address-text-summary margin-top-sm">
                            <strong><?= htmlspecialchars($orderInfo['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong> (<?= htmlspecialchars($orderInfo['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?>)<br>
                            <?= htmlspecialchars($orderInfo['address_data'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                            <?= htmlspecialchars($orderInfo['city'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($orderInfo['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                        </p>
                    </div>

                    <div class="checkout-section-card margin-top-md">
                        <h4 class="summary-sub-title"><i class="fa-solid fa-box-open" aria-hidden="true"></i> Articles commandés</h4>
                        <div class="invoice-products-grid margin-top-sm">
                            <?php if (is_array($basketProducts)): foreach ($basketProducts as $item): 
                                $qty = (int)($item['quantity'] ?? 1);
                                $price = (float)($item['price'] ?? 0);
                            ?>
                                <div class="invoice-product-row">
                                    <div class="product-details-container">
                                        <p class="product-title-text"><?= htmlspecialchars($item['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?></p>
                                        <span class="product-meta-text">Qté : <strong><?= $qty ?></strong> | P.U : <?= number_format($price, 2, ',', ' ') ?> €</span>
                                    </div>
                                    <div class="product-total-price">
                                        <?= number_format($price * $qty, 2, ',', ' ') ?> €
                                    </div>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>

                <div class="checkout-right-column">
                    <div class="checkout-summary-card">
                        <h3>Total de la commande</h3>
                        <div class="summary-lines-box">
                            <div class="summary-line"><span class="label">Sous-total</span><span class="value"><?= number_format($subTotal, 2, ',', ' ') ?> €</span></div>
                            <div class="summary-line"><span class="label">Frais de livraison</span><span class="value"><?= $shippingCost > 0 ? number_format($shippingCost, 2, ',', ' ') . ' €' : 'Offerts' ?></span></div>
                            
                            <div class="summary-line-separator"></div>
                            
                            <div class="summary-line total-large-line">
                                <span class="label">Total TTC à payer</span>
                                <span class="value color-success font-size-large"><?= number_format($totalPayable, 2, ',', ' ') ?> €</span>
                            </div>
                        </div>
                        <div class="margin-top-md text-center">
                            <a href="<?= URL ?>Account/orders" class="btn-checkout-massive btn-full-width text-center" style="text-decoration: none;">
                                <i class="fa-solid fa-user" aria-hidden="true"></i> Retour à mon compte
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<script src="<?= URL ?>public/assets/js/checkout.js" defer></script>