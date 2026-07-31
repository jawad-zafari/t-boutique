<?php
// SÉCURITÉ : Typage rigoureux
$totalProductsPrice = (float)($data['cartData'][1] ?? 0);
$totalDiscount = (float)($data['cartData'][2] ?? 0);
$shippingPrice = (float)($data['postPrice'] ?? 0);
$finalTotal = $totalProductsPrice + $shippingPrice - $totalDiscount;
?>
<div class="checkout-modern-container order-page-wrapper">
    
    <form id="formPayment" action="<?= URL ?>Order/saveOrder" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="checkout-grid-layout">
            
            <div class="checkout-left-column">
                
                <div class="checkout-back-nav">
                    <a href="<?= URL ?>Order/summary" class="link-back-navigation"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i> Retour au résumé</a>
                </div>

                <nav class="checkout-stepper-modern-bar">
                    <ul class="stepper-steps-flex">
                        <li class="completed">Connexion</li>
                        <li class="completed">Livraison</li>
                        <li class="completed">Résumé</li>
                        <li class="active" aria-current="step">Paiement</li>
                    </ul>
                </nav>

                <div class="checkout-section-card">
                    <h3><i class="fa-solid fa-ticket" aria-hidden="true"></i> Avez-vous un code de réduction ?</h3>
                    <p class="text-muted-sm">Si vous possédez un code promotionnel, veuillez l'appliquer ci-dessous pour mettre à jour votre facture :</p>
                    
                    <div class="promo-code-input-flex-row">
                        <input type="text" id="codePromoInput" name="code_promo" class="form-control code-promo-field" placeholder="Saisir votre code promo..." autocomplete="off">
                        <button type="button" id="btnVerifyPromo" class="btn-stepper-secondary">Appliquer</button>
                    </div>
                </div>
            </div>

            <div class="checkout-right-column">
                <div class="checkout-summary-card">
                    <h3>Facturation finale</h3>
                    
                    <div class="summary-lines-box">
                        <div class="summary-line"><span class="label">Total des articles</span><span class="value"><?= number_format($totalProductsPrice, 2, ',', ' ') ?> €</span></div>
                        <div class="summary-line"><span class="label">Frais de transport</span><span class="value"><?= number_format($shippingPrice, 2, ',', ' ') ?> €</span></div>
                        
                        <div class="summary-line text-danger display-none-box" id="summaryDiscountLine">
                            <span>Remise code promo</span>
                            <span id="summaryDiscountValue">- 0,00 €</span>
                        </div>
                        
                        <div class="summary-line-separator"></div>
                        
                        <div class="summary-line total-large-line">
                            <span>Total TTC à payer</span>
                            <span id="finalTotalAmount" class="color-success font-size-large"><?= number_format($finalTotal, 2, ',', ' ') ?> €</span>
                        </div>
                    </div>

                    <div class="margin-top-md">
                        <button type="submit" class="btn-checkout-massive btn-full-width color-bg-success">
                            Confirmer et Payer <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        </button>
                        <div class="secure-badge-stepper text-center font-weight-semibold">
                            <i class="fa-solid fa-shield-halved color-teal" aria-hidden="true"></i> Transactions 100% cryptées et sécurisées
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/order.js" defer></script>