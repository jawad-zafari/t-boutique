<?php 
// En Architecture MVC, les données sont fournies proprement par le contrôleur
$cartItems = $data['cartItems'] ?? [];
$totalPriceAll = $data['priceTotalAll'] ?? 0;
?>
<div id="mainCart" class="cart-modern-container" data-csrf="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <div class="cart-header-main">
        <h2><i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> Mon Panier d'Achats</h2>
    </div>

    <?php if(!empty($cartItems)): ?>
        <div class="cart-grid-layout">
            
            <div class="cart-items-column">
                <?php foreach ($cartItems as $row): 
                    $currentRowId = $row['cartRow'] ?? 0;
                    $currentQty = $row['quantity'] ?? $row['tedad'] ?? 1;
                    $unitPrice = $row['price'] ?? 0;
                    $totalPrice = $unitPrice * $currentQty;
                ?>
                <div class="cart-product-card">
                    
                    <div class="product-image-box">
                        <img src="<?= URL ?>public/images/products/<?= (int)$row['id'] ?>/product_220.jpg" alt="<?= htmlspecialchars($row['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?>" onerror="this.src='https://placehold.co/120x120/f8f9fa/adb5bd?text=Image'">
                    </div>
                    
                    <div class="product-details-box">
                        <h3 class="product-title"><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                        
                        <div class="product-attributes">
                            <?php if(!empty($row['colorTitle'])): ?>
                                <span class="attr-badge"><i class="fa-solid fa-palette" aria-hidden="true"></i> <?= htmlspecialchars($row['colorTitle'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?php if(!empty($row['garanteeTitle'])): ?>
                                <span class="attr-badge"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i> <?= htmlspecialchars($row['garanteeTitle'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="product-actions-row">
                            <div class="quantity-selector-modern">
                                <button type="button" class="btn-qty minus" data-row="<?= (int)$currentRowId ?>" aria-label="Diminuer la quantité de 1"><i class="fa-solid fa-minus" aria-hidden="true"></i></button>
                                <input type="number" class="input-qty" value="<?= (int)$currentQty ?>" readonly aria-label="Quantité actuelle" data-row="<?= (int)$currentRowId ?>">
                                <button type="button" class="btn-qty plus" data-row="<?= (int)$currentRowId ?>" aria-label="Augmenter la quantité de 1"><i class="fa-solid fa-plus" aria-hidden="true"></i></button>
                            </div>
                            
                            <div class="product-price-info">
                                <span class="total-item-price"><?= number_format($totalPrice, 2, ',', ' ') ?> €</span>
                            </div>

                            <button type="button" class="btn-remove-modern btn-remove-item" data-row="<?= (int)$currentRowId ?>" aria-label="Retirer définitivement ce produit du panier" title="Retirer ce produit">
                                <i class="fa-regular fa-trash-can" aria-hidden="true"></i> Supprimer
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary-column">
                <div class="cart-summary-card">
                    <h3 class="summary-title">Résumé de la commande</h3>
                    
                    <div class="summary-details">
                        <div class="summary-line">
                            <span class="label">Total des articles</span>
                            <span class="value"><?= count($cartItems) ?></span>
                        </div>
                        <div class="summary-line">
                            <span class="label">Sous-total</span>
                            <span class="value"><?= number_format($totalPriceAll, 2, ',', ' ') ?> €</span>
                        </div>
                        <div class="summary-line">
                            <span class="label">Frais de livraison</span>
                            <span class="value text-muted">Calculés à l'étape suivante</span>
                        </div>
                        
                        <div class="summary-line total-line">
                            <span class="label">Total TTC</span>
                            <span class="value highlight-total"><?= number_format($totalPriceAll, 2, ',', ' ') ?> €</span>
                        </div>
                    </div>
                    
                    <a class="btn-checkout-massive" href="<?= URL ?>Checkout/index" aria-label="Procéder au règlement de la commande">
                        Valider mon panier <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                    
                    <div class="secure-checkout-badge">
                        <i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Processus de paiement sécurisé
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