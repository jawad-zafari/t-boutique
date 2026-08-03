<?php
// ARCHITECTURE MVC : Aucune logique métier complexe dans la vue.
$cart = $data['cartData'][0] ?? [];
if (!is_array($cart)) { $cart = []; }

$totalProductsPrice = (float)($data['cartData'][1] ?? 0);
$totalDiscount = (float)($data['cartData'][2] ?? 0);
$addresses = $data['addresses'] ?? [];
$postTypes = $data['postType'] ?? [];
?>
<div class="checkout-modern-container order-page-wrapper" id="step2AddressWrapper" data-csrf="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <?php if(isset($_GET['error']) && $_GET['error'] == 'address_missing'): ?>
        <div class="alert-sticky danger alert-box-modern" role="alert">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Veuillez sélectionner une adresse et un mode de livraison avant de continuer.
        </div>
    <?php endif; ?>

    <div id="jsErrorMessage" class="alert-sticky danger alert-box-modern display-none-box" role="alert"></div>

    <div class="checkout-grid-layout">
        
        <div class="checkout-left-column">
            
            <div class="checkout-back-nav">
                <a href="<?= URL ?>Order/index" class="link-back-navigation"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i> Retour à la connexion</a>
            </div>

            <nav class="checkout-stepper-modern-bar">
                <ul class="stepper-steps-flex">
                    <li class="completed">Connexion</li>
                    <li class="active" aria-current="step">Livraison</li>
                    <li>Paiement</li>
                </ul>
            </nav>

            <div class="checkout-section-card">
                <div class="section-title-flex">
                    <h3><i class="fa-solid fa-map-location-dot" aria-hidden="true"></i> 1. Choisissez une adresse de livraison</h3>
                    <button type="button" class="btn-add-address-trigger" id="btnToggleAddressForm" aria-expanded="false" aria-controls="inlineAddressFormContainer">
                        <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter une adresse
                    </button>
                </div>

                <div class="address-cards-grid" id="addressListContainer">
                    <?php if(!empty($addresses)): foreach($addresses as $addr): ?>
                        <div class="modern-selection-card js-address-card" data-id="<?= (int)$addr['id'] ?>">
                            <div class="card-radio-select">
                                <input type="radio" name="selected_address" id="addr_<?= (int)$addr['id'] ?>" value="<?= (int)$addr['id'] ?>">
                                <label for="addr_<?= (int)$addr['id'] ?>"><strong><?= htmlspecialchars($addr['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></label>
                            </div>
                            <p class="address-text-summary"><?= htmlspecialchars($addr['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                            <span class="address-city-zip"><?= htmlspecialchars($addr['city'] ?? $addr['city_name'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($addr['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</span>
                        </div>
                    <?php endforeach; else: ?>
                        <p class="empty-section-notice" id="emptyAddressNotice">Aucune adresse enregistrée. Veuillez en ajouter une ci-dessous.</p>
                    <?php endif; ?>
                </div>

                <div id="inlineAddressFormContainer" class="inline-address-form-wrapper display-none-box margin-top-md">
                    <div class="inline-form-card">
                        <h4 class="inline-form-title"><i class="fa-solid fa-thumbtack" aria-hidden="true"></i> Saisir une nouvelle adresse</h4>
                        
                        <form id="formAddAddress" method="post" autocomplete="off">
                            <div class="form-grid-double">
                                <div class="form-group"><label for="last_name">Nom complet du destinataire *</label><input type="text" id="last_name" name="last_name" class="form-control" required></div>
                                <div class="form-group"><label for="mobile">Téléphone mobile *</label><input type="text" id="mobile" name="mobile" class="form-control" dir="ltr" required></div>
                                <div class="form-group"><label for="province_name">Région / Province *</label><input type="text" id="province_name" name="province_name" class="form-control" required></div>
                                <div class="form-group"><label for="city_name">Ville *</label><input type="text" id="city_name" name="city_name" class="form-control" required></div>
                                <div class="form-group full-width"><label for="postal_code">Code postal *</label><input type="text" id="postal_code" name="postal_code" class="form-control" dir="ltr" required></div>
                                <div class="form-group full-width"><label for="address">Adresse complète *</label><textarea id="address" name="address" rows="2" class="form-control" required></textarea></div>
                            </div>
                            <div class="inline-form-actions margin-top-md flex-end-gap">
                                <button type="button" class="btn-account-secondary" id="btnCancelAddressInline">Annuler</button>
                                <button type="submit" class="btn-account-submit color-bg-success" id="btnSubmitAddress"><i class="fa-solid fa-save" aria-hidden="true"></i> Enregistrer l'adresse</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="checkout-section-card margin-top-md">
                <h3><i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i> 2. Choisissez le mode de livraison</h3>
                
                <div class="shipping-methods-grid">
                    <?php if(!empty($postTypes)): foreach($postTypes as $method): ?>
                        <div class="modern-selection-card js-shipping-card" data-id="<?= (int)$method['id'] ?>">
                            <div class="card-radio-select">
                                <input type="radio" name="selected_shipping" id="ship_<?= (int)$method['id'] ?>" value="<?= (int)$method['id'] ?>">
                                <label for="ship_<?= (int)$method['id'] ?>"><strong><?= htmlspecialchars($method['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></label>
                            </div>
                            <span class="shipping-price-tag font-weight-bold color-success">
                                <?= (isset($method['price']) && (float)$method['price'] > 0) ? number_format((float)$method['price'], 2, ',', ' ') . ' €' : 'Gratuit' ?>
                            </span>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

        </div>

        <div class="checkout-right-column">
            <div class="checkout-summary-card">
                <h3><i class="fa-solid fa-basket-shopping" aria-hidden="true"></i> Vos articles (<?= count($cart) ?>)</h3>
                
                <div class="summary-products-list">
                    <?php if(!empty($cart)): foreach($cart as $item):
                        $qty = (int)($item['quantity'] ?? 1);
                        $price = (float)($item['price'] ?? 0);
                        $productId = (int)($item['id'] ?? 0);
                    ?>
                        <div class="summary-product-item">
                            <div class="summary-product-img-box">
                                <img src="<?= URL ?>public/images/products/<?= $productId ?>/product_220.jpg" 
                                     alt="<?= htmlspecialchars($item['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?>"
                                     onerror="this.src='https://placehold.co/60x60/f8f9fa/adb5bd?text=Img';">
                                <span class="product-qty-badge"><?= $qty ?></span>
                            </div>
                            <div class="product-info-col">
                                <span class="product-name"><?= htmlspecialchars($item['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="product-meta"><?= number_format($price, 2, ',', ' ') ?> € / un.</span>
                            </div>
                            <span class="product-price"><?= number_format($price * $qty, 2, ',', ' ') ?> €</span>
                        </div>
                    <?php endforeach; endif; ?>
                </div>

                <div class="summary-totals">
                    <div class="summary-line"><span class="label">Sous-total</span><span class="value"><?= number_format($totalProductsPrice, 2, ',', ' ') ?> €</span></div>
                    <?php if($totalDiscount > 0): ?>
                        <div class="summary-line text-danger"><span class="label">Remise</span><span class="value">- <?= number_format($totalDiscount, 2, ',', ' ') ?> €</span></div>
                    <?php endif; ?>
                    <div class="summary-line total-line">
                        <span class="label">Total TTC</span>
                        <span class="highlight-total"><?= number_format($totalProductsPrice - $totalDiscount, 2, ',', ' ') ?> €</span>
                    </div>
                </div>

                <button type="button" id="btnContinueToSummary" class="btn-checkout-massive btn-full-width margin-top-md">
                    Procéder au paiement <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>

    </div>
</div>

<script src="<?= URL ?>public/assets/js/order.js" defer></script>