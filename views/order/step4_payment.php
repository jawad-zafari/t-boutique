<?php
/**
 * Vue Étape : Paiement et Récapitulatif Final
 * Interface dynamique séparant la sélection du mode de paiement et la saisie des détails.
 * Sécurité DWWM : Protection XSS avec htmlspecialchars et jeton CSRF.
 */

// Sécurisation et typage strict des données reçues du contrôleur
$totalProductsPrice = (float)($data['cartData'][1] ?? 0);
$totalDiscount = (float)($data['cartData'][2] ?? 0);
$shippingPrice = (float)($data['postPrice'] ?? 0);
$finalTotal = max(0, $totalProductsPrice + $shippingPrice - $totalDiscount);
$addressInfo = is_array($data['addressInfo'] ?? null) ? $data['addressInfo'] : [];
$csrfToken = htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<div class="checkout-modern-container order-page-wrapper" data-csrf="<?= $csrfToken ?>">
    
    <form id="formPayment" action="<?= URL ?>Order/saveOrder" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">

        <div class="checkout-grid-layout">
            
            <div class="checkout-left-column">
                
                <div class="checkout-back-nav">
                    <a href="<?= URL ?>Order/address" class="link-back-navigation">
                        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i> Retour à la livraison
                    </a>
                </div>

                <nav class="checkout-stepper-modern-bar" aria-label="Progression de la commande">
                    <ul class="stepper-steps-flex">
                        <li class="completed">Connexion</li>
                        <li class="completed">Livraison</li>
                        <li class="active" aria-current="step">Paiement</li>
                    </ul>
                </nav>

                <div class="checkout-section-card">
                    <h3><i class="fa-solid fa-credit-card" aria-hidden="true"></i> Choisissez votre mode de paiement</h3>
                    <p class="text-muted-sm">Veuillez sélectionner votre moyen de règlement préféré :</p>
                    
                    <div class="payment-methods-grid margin-top-md">
                        
                        <div class="modern-selection-card js-payment-card active" data-method="1">
                            <div class="card-radio-select">
                                <input type="radio" name="payment_method" id="pay_online" value="1" checked>
                                <label for="pay_online"><strong>Paiement en ligne sécurisé</strong></label>
                            </div>
                            <p class="address-text-summary">Carte bancaire (Visa, Mastercard, Carte Bleue). Traitement immédiat.</p>
                            <div class="payment-icons-row margin-top-sm">
                                <i class="fa-brands fa-cc-visa" aria-hidden="true"></i>
                                <i class="fa-brands fa-cc-mastercard" aria-hidden="true"></i>
                                <i class="fa-solid fa-shield-halved color-teal" aria-hidden="true"></i>
                            </div>
                        </div>

                        <div class="modern-selection-card js-payment-card" data-method="2">
                            <div class="card-radio-select">
                                <input type="radio" name="payment_method" id="pay_bank" value="2">
                                <label for="pay_bank"><strong>Virement bancaire</strong></label>
                            </div>
                            <p class="address-text-summary">Expédition de la commande après validation du virement par notre équipe.</p>
                            <div class="payment-icons-row margin-top-sm">
                                <i class="fa-solid fa-building-columns" aria-hidden="true"></i>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="checkout-section-card margin-top-md" id="paymentDetailsSection">
                    
                    <div id="cardPaymentDetails" class="payment-details-box">
                        <h3><i class="fa-solid fa-lock" aria-hidden="true"></i> Détails de la carte bancaire</h3>
                        <p class="text-muted-sm margin-bottom-sm">Saisissez les informations de votre carte pour valider le paiement :</p>

                        <div class="form-group margin-bottom-sm">
                            <label for="cardHolder">Titulaire de la carte</label>
                            <input type="text" id="cardHolder" name="card_holder" class="form-control" placeholder="M. Jean Dupont" autocomplete="cc-name">
                        </div>
                        <div class="form-group margin-bottom-sm">
                            <label for="cardNumber">Numéro de carte bancaire</label>
                            <input type="text" id="cardNumber" name="card_number" class="form-control" placeholder="4532 0000 0000 0000" maxlength="19" autocomplete="cc-number">
                        </div>
                        <div class="form-grid-modern">
                            <div class="form-group">
                                <label for="cardExpiry">Expiration (MM/AA)</label>
                                <input type="text" id="cardExpiry" name="card_expiry" class="form-control" placeholder="12/28" maxlength="5" autocomplete="cc-exp">
                            </div>
                            <div class="form-group">
                                <label for="cardCvv">Code CVV</label>
                                <input type="password" id="cardCvv" name="card_cvv" class="form-control" placeholder="123" maxlength="3" autocomplete="cc-csc">
                            </div>
                        </div>
                    </div>

                    <div id="bankTransferDetails" class="payment-details-box display-none-box">
                        <h3><i class="fa-solid fa-building-columns" aria-hidden="true"></i> Coordonnées bancaires pour le virement</h3>
                        <p class="text-muted-sm margin-bottom-sm">Veuillez effectuer votre virement vers le compte suivant :</p>

                        <div class="shipping-info-box-flex">
                            <div class="info-block-item">
                                <strong>Titulaire du compte :</strong> <span>MaBoutique SAS</span>
                            </div>
                            <div class="info-block-item">
                                <strong>IBAN :</strong> <span>FR76 3000 4000 0112 3456 7890 123</span>
                            </div>
                            <div class="info-block-item">
                                <strong>BIC / SWIFT :</strong> <span>BNPAFRPPXXX</span>
                            </div>
                            <div class="info-block-item margin-top-sm">
                                <span class="text-muted-sm"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> Indiquez votre nom et le numéro de commande en référence.</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="checkout-right-column">
                <div class="checkout-summary-card">
                    <h3>Récapitulatif final</h3>
                    
                    <div class="summary-lines-box">
                        <div class="summary-line">
                            <span class="label">Sous-total articles</span>
                            <span class="value"><?= number_format($totalProductsPrice, 2, ',', ' ') ?> €</span>
                        </div>
                        
                        <div class="summary-line">
                            <span class="label">Frais de transport</span>
                            <span class="value"><?= $shippingPrice > 0 ? number_format($shippingPrice, 2, ',', ' ') . ' €' : 'Gratuit' ?></span>
                        </div>
                        
                        <?php if($totalDiscount > 0): ?>
                            <div class="summary-line text-danger">
                                <span class="label">Remise initiale</span>
                                <span class="value">- <?= number_format($totalDiscount, 2, ',', ' ') ?> €</span>
                            </div>
                        <?php endif; ?>

                        <div class="summary-line text-danger display-none-box" id="summaryDiscountLine">
                            <span class="label">Remise code promo</span>
                            <span class="value" id="summaryDiscountValue">- 0,00 €</span>
                        </div>
                        
                        <div class="summary-line-separator"></div>
                        
                        <div class="summary-line total-large-line">
                            <span class="label">Total TTC</span>
                            <span id="finalTotalAmount" class="value color-success font-size-large"><?= number_format($finalTotal, 2, ',', ' ') ?> €</span>
                        </div>
                    </div>

                    <div class="summary-line-separator"></div>

                    <div class="summary-promo-section margin-top-sm">
                        <h4 class="summary-sub-title"><i class="fa-solid fa-ticket" aria-hidden="true"></i> Code de réduction</h4>
                        <div class="promo-code-input-flex-row margin-top-sm">
                            <input type="text" id="codePromoInput" name="code_promo" class="form-control code-promo-field" placeholder="Ex: PROMO2026" autocomplete="off">
                            <button type="button" id="btnVerifyPromo" class="btn-stepper-secondary">Appliquer</button>
                        </div>
                    </div>

                    <div class="summary-line-separator"></div>

                    <div class="summary-address-section margin-top-sm">
                        <h4 class="summary-sub-title"><i class="fa-solid fa-truck" aria-hidden="true"></i> Adresse d'expédition</h4>
                        <div class="address-text-summary margin-top-sm">
                            <strong><?= htmlspecialchars($addressInfo['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong> 
                            (<?= htmlspecialchars($addressInfo['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?>)<br>
                            <?= htmlspecialchars($addressInfo['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>, 
                            <?= htmlspecialchars($addressInfo['city_name'] ?? $addressInfo['city'] ?? '', ENT_QUOTES, 'UTF-8') ?> 
                            (<?= htmlspecialchars($addressInfo['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>)
                        </div>
                    </div>

                    <div class="margin-top-md">
                        <button type="submit" class="btn-checkout-massive btn-full-width color-bg-success">
                            Confirmer et Payer <i class="fa-solid fa-lock" aria-hidden="true"></i>
                        </button>
                        <div class="secure-badge-stepper text-center font-weight-semibold margin-top-sm">
                            <i class="fa-solid fa-shield-halved color-teal" aria-hidden="true"></i> Transactions 100% cryptées et sécurisées
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/order.js" defer></script>