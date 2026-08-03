<?php
/**
 * Vue : Page Principale du Panier d'Achats (cart.php)
 * Architecture MVC : Données transmises par le contrôleur Cart.php
 * Sécurité DWWM : Protection XSS avec htmlspecialchars, typage strict et jeton CSRF.
 */

$cartItems = $data['cartItems'] ?? [];
$totalPriceAll = (float)($data['priceTotalAll'] ?? 0);
$csrfToken = htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8');
?>
<div id="mainCart" class="cart-modern-container" data-csrf="<?= $csrfToken ?>">
    
    <div class="cart-header-main">
        <h2><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> Mon Panier d'Achats</h2>
    </div>

    <?php if (!empty($cartItems) && is_array($cartItems)): ?>
        <div class="cart-grid-layout">
            
            <div class="cart-items-column">
                <?php foreach ($cartItems as $row): 
                    $currentRowId = (int)($row['cartRow'] ?? 0);
                    // NETTOYAGE DWWM : Utilisation stricte de 'quantity' (chaine 'tedad' supprimée)
                    $currentQty = (int)($row['quantity'] ?? 1);
                    $unitPrice = (float)($row['price'] ?? 0);
                    $totalPrice = $unitPrice * $currentQty;
                    $productId = (int)($row['id'] ?? 0);
                    $productTitle = htmlspecialchars($row['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8');
                    $colorTitle = !empty($row['colorTitle']) ? htmlspecialchars($row['colorTitle'], ENT_QUOTES, 'UTF-8') : null;
                    $guaranteeTitle = !empty($row['garanteeTitle']) ? htmlspecialchars($row['garanteeTitle'], ENT_QUOTES, 'UTF-8') : null;
                ?>
                <div class="cart-product-card" data-row="<?= $currentRowId ?>">
                    
                    <div class="product-image-box">
                        <img src="<?= URL ?>public/images/products/<?= $productId ?>/product_220.jpg" 
                             alt="<?= $productTitle ?>" 
                             class="product-thumbnail-img"
                             onerror="this.src='https://placehold.co/100x100/f1f3f5/3b5bdb?text=Produit'">
                    </div>

                    <div class="product-details-box">
                        <h3 class="product-title"><?= $productTitle ?></h3>
                        
                        <?php if ($colorTitle): ?>
                            <p class="product-meta"><i class="fa-solid fa-palette" aria-hidden="true"></i> Couleur : <strong><?= $colorTitle ?></strong></p>
                        <?php endif; ?>

                        <?php if ($guaranteeTitle): ?>
                            <p class="product-meta"><i class="fa-solid fa-shield" aria-hidden="true"></i> Garantie : <strong><?= $guaranteeTitle ?></strong></p>
                        <?php endif; ?>

                        <div class="product-price-unit margin-top-sm">
                            Prix unitaire : <span><?= number_format($unitPrice, 2, ',', ' ') ?> €</span>
                        </div>
                    </div>

                    <div class="product-actions-box">
                        <div class="quantity-selector-modern">
                            <button type="button" class="btn-qty minus" data-row="<?= $currentRowId ?>" aria-label="Diminuer la quantité">-</button>
                            <input type="text" class="input-qty" value="<?= $currentQty ?>" readonly aria-label="Quantité" data-row="<?= $currentRowId ?>">
                            <button type="button" class="btn-qty plus" data-row="<?= $currentRowId ?>" aria-label="Augmenter la quantité">+</button>
                        </div>

                        <div class="product-total-price">
                            <?= number_format($totalPrice, 2, ',', ' ') ?> €
                        </div>

                        <button type="button" class="btn-remove-item" data-row="<?= $currentRowId ?>" aria-label="Supprimer cet article">
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                        </button>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary-column">
                <div class="cart-summary-card">
                    <h3>Récapitulatif de la commande</h3>
                    
                    <div class="summary-lines">
                        <div class="summary-line">
                            <span class="label">Sous-total articles</span>
                            <span class="value"><?= number_format($totalPriceAll, 2, ',', ' ') ?> €</span>
                        </div>

                        <div class="summary-line">
                            <span class="label">Frais de livraison</span>
                            <span class="value text-muted">Calculés à l'étape suivante</span>
                        </div>
                        
                        <div class="summary-line-separator"></div>

                        <div class="summary-line total-line">
                            <span class="label">Total TTC</span>
                            <span class="value highlight-total total-all-price"><?= number_format($totalPriceAll, 2, ',', ' ') ?> €</span>
                        </div>
                    </div>
                    
                    <a class="btn-checkout-massive" href="<?= URL ?>Order/address" aria-label="Procéder au règlement de la commande">
                        Valider mon panier <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    
                    <div class="secure-checkout-badge">
                        <i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Processus de paiement 100% sécurisé
                    </div>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="empty-cart-container">
            <div class="empty-icon"><i class="fa-solid fa-basket-shopping" aria-hidden="true"></i></div>
            <h3>Votre panier est actuellement vide</h3>
            <p>Découvrez nos nouveautés et offres exclusives pour remplir votre panier !</p>
            <a href="<?= URL ?>Index/index" class="btn-return-shop">Continuer mes achats</a>
        </div>
    <?php endif; ?>

</div>