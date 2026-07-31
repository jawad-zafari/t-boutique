<?php
/**
 * Vue des Exclusivités Boutique (exclusives.php)
 * Affiche le carrousel des produits recommandés/exclusifs.
 */
$exclusives = $data['exclusives'] ?? [];
?>

<div class="exclusive-section">
    <div class="exclusive-header">
        <h3><i class="fa-solid fa-star" aria-hidden="true"></i> Exclusivités Boutique</h3>
    </div>
    
    <div class="exclusive-carousel-wrapper" id="exclusiveCarousel">
        <button type="button" class="nav-btn prev" id="btnExclusivePrev" aria-label="Produits précédents">
            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
        </button>
        
        <div class="carousel-viewport" id="exclusiveViewport">
            <?php if (!empty($exclusives)): foreach ($exclusives as $row): 
                $exId = (int)($row['id'] ?? 0);
                $exTitle = htmlspecialchars($row['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8');
                
                // RÈGLE MÉTIER : Affichage correct du prix avec remise (si calculé par le modèle)
                $exPrice = (float)($row['price_total'] ?? $row['price'] ?? 0);
                
                $thumbUrl = URL . 'public/images/products/' . $exId . '/product_220.jpg';
            ?>
                <div class="exclusive-item-wrapper">
                    
                    <button type="button" class="btn-favorite-toggle btn-exclusive-fav" data-id="<?= $exId ?>" title="Ajouter aux favoris" aria-label="Ajouter <?= $exTitle ?> aux favoris">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                    </button>

                    <a href="<?= URL ?>Product/index/<?= $exId ?>" class="exclusive-item" aria-label="Voir le produit <?= $exTitle ?>">
                        <div class="exclusive-img-wrapper">
                            <img src="<?= $thumbUrl ?>" alt="<?= $exTitle ?>" class="exclusive-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                            <span class="exclusive-badge">Spécial</span>
                        </div>
                        <div class="exclusive-info">
                            <h4 class="exclusive-title"><?= $exTitle ?></h4>
                            <span class="exclusive-price"><?= number_format($exPrice, 0, ',', ' ') ?> €</span>
                        </div>
                    </a>
                </div>

            <?php endforeach; else: ?>
                <p class="empty-text">Aucun produit exclusif pour le moment.</p>
            <?php endif; ?>
        </div>
        
        <button type="button" class="nav-btn next" id="btnExclusiveNext" aria-label="Produits suivants">
            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        </button>
    </div>
</div>