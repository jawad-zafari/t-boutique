<div class="collection-container mt-30">
    
    <div class="breadcrumb-navigation">
        <button type="button" class="btn-go-back js-back-button" aria-label="Retourner à la page précédente">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
        </button>
        <span class="divider-icon">|</span>
        
        <a href="<?= URL ?>Index/index" class="link-home">
            <i class="fa-solid fa-house" aria-hidden="true"></i> Accueil
        </a>
        <i class="fa-solid fa-angle-right divider-icon" aria-hidden="true"></i>
        
        <a href="<?= URL ?>Account/index" class="link-home">
            <i class="fa-solid fa-user" aria-hidden="true"></i> Mon Compte
        </a>
        <i class="fa-solid fa-angle-right divider-icon" aria-hidden="true"></i>
        
        <span class="current-page-title">Mes Favoris</span>
    </div>

    <div class="collection-header-box">
        <h2 class="main-title"><i class="fa-solid fa-heart title-icon-danger" aria-hidden="true"></i> Mes produits favoris</h2>
        <p class="subtitle">Retrouvez ici tous les produits que vous avez sauvegardés.</p>
    </div>

    <div class="products-grid-layout">
        <?php 
        $favorites = $data['favorites'] ?? [];
        if (!empty($favorites)):
            foreach ($favorites as $product):
                $discount = (int)($product['discount_percent'] ?? 0);
                $hasDiscount = $discount > 0;
                $productId = (int)($product['id'] ?? 0);
        ?>
            <div class="product-card hover-glow" id="fav-card-<?= $productId ?>">
                
                <button type="button" class="btn-favorite-toggle active" data-id="<?= $productId ?>" title="Retirer des favoris" aria-label="Retirer ce produit de vos favoris">
                    <i class="fa-solid fa-heart" aria-hidden="true"></i>
                </button>

                <a href="<?= URL ?>Product/index/<?= $productId ?>" class="card-link-wrapper" aria-label="Voir la fiche du produit">
                    <div class="image-wrapper">
                        <img src="<?= URL ?>public/images/products/<?= $productId ?>/product_220.jpg" alt="<?= htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                    </div>
                </a>

                <div class="card-content">
                    <a href="<?= URL ?>Product/index/<?= $productId ?>" class="product-title-link">
                        <h4 class="product-title"><?= htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h4>
                    </a>
                    
                    <div class="price-cart-row">
                        <div class="product-price-container">
                            <?php if($hasDiscount): ?>
                                <del class="price-old"><?= number_format((float)($product['price'] ?? 0), 0, ',', ' ') ?> €</del>
                                <span class="product-price price-danger"><?= number_format((float)($product['price_total'] ?? 0), 0, ',', ' ') ?> €</span>
                            <?php else: ?>
                                <span class="product-price price-primary"><?= number_format((float)($product['price'] ?? 0), 0, ',', ' ') ?> €</span>
                            <?php endif; ?>
                        </div> 
                        <button type="button" class="btn-quick-add square-btn rounded-action-btn" data-id="<?= $productId ?>" aria-label="Ajouter ce produit au panier" title="Ajouter au panier">
                            <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="empty-favorites-block">
                <i class="fa-regular fa-heart empty-fav-icon-muted" aria-hidden="true"></i>
                <h3 class="text-muted-color">Votre liste de favoris est vide.</h3>
                <a href="<?= URL ?>" class="link-return-shopping-highlight">Continuer mes achats</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="<?= URL ?>public/assets/js/account.js" defer></script>