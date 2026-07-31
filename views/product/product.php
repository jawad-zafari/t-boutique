<?php
/**
 * Vue Principale du Produit (product.php)
 * Affiche les informations principales, la galerie d'images, le prix et les actions.
 * SÉCURITÉ : Protection Anti-XSS stricte et respect absolu de l'architecture MVC.
 */

// SÉCURITÉ : Récupération et sécurisation des données transmises par le contrôleur
$product = $data['productInfo'] ?? [];
$gallery = $data['gallery'] ?? [];
$csrfToken = $data['csrf_token'] ?? '';
$userId = Model::sessionGet('userId');

// RÈGLE MVC : Récupération de l'état "Favori" calculé par le contrôleur (Aucune requête SQL dans la vue)
$isFavorite = $data['isFavorite'] ?? false;

// Préparation de la galerie d'images (Image principale + images secondaires)
$allImages = [];
$productId = (int)($product['id'] ?? 0);
$mainImage = URL . 'public/images/products/' . $productId . '/product_350.jpg';
$allImages[] = [
    'url' => $mainImage, 
    'alt' => htmlspecialchars($product['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8')
];

if (!empty($gallery)) {
    foreach ($gallery as $g) {
        $allImages[] = [
            'url' => URL . 'public/images/products/' . $productId . '/gallery/large/' . htmlspecialchars($g['image_name'], ENT_QUOTES, 'UTF-8'),
            'alt' => 'Galerie image ' . htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8')
        ];
    }
}
?>

<div id="mainProductWrapper" class="product-page-container" data-csrf="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

    <nav class="breadcrumb-navigation" aria-label="Fil d'Ariane">
        <button type="button" class="btn-go-back js-back-button" aria-label="Retourner à la page précédente">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
        </button>
        <span class="divider-icon" aria-hidden="true">|</span>
        <a href="<?= URL ?>Index/index" class="link-home">
            <i class="fa-solid fa-house" aria-hidden="true"></i> Accueil
        </a>
        <i class="fa-solid fa-angle-right divider-icon" aria-hidden="true"></i>
        <span class="current-page-title" aria-current="page"><?= htmlspecialchars($product['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?></span>
    </nav>

    <div class="product-main-grid">
        
        <div class="product-gallery-column">
            <div class="main-image-preview-box">
                <img id="mainProductImageNode" src="<?= $mainImage ?>" alt="<?= htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="gallery-main-img">
                <div class="zoom-indicator-overlay" id="btnTriggerImageZoom" title="Agrandir l'image">
                    <i class="fa-solid fa-magnifying-glass-plus" aria-hidden="true"></i>
                </div>
            </div>
            
            <?php if (count($allImages) > 1): ?>
                <div class="gallery-thumbnails-row">
                    <?php foreach ($allImages as $index => $img): ?>
                        <div class="thumb-item-box <?= $index === 0 ? 'active' : '' ?>" data-src="<?= $img['url'] ?>">
                            <img src="<?= $img['url'] ?>" alt="<?= htmlspecialchars($img['alt'], ENT_QUOTES, 'UTF-8') ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="product-details-column">
            <h1 class="product-page-title"><?= htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h1>
            
            <div class="product-meta-row">
                <span class="product-views-count">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i> <?= (int)($product['views'] ?? 0) ?> vues
                </span>
            </div>

            <div class="product-description-excerpt">
                <p><?= htmlspecialchars($product['description'] ?? 'Aucune description disponible pour ce produit.', ENT_QUOTES, 'UTF-8') ?></p>
            </div>

            <div class="product-purchase-box">
                <div class="product-pricing">
                    <?php 
                    $price = (float)($product['price'] ?? 0);
                    $discount = (int)($product['discount_percent'] ?? 0);
                    $priceTotal = (float)($product['price_total'] ?? $price);
                    
                    if ($discount > 0): 
                    ?>
                        <del class="price-old-large"><?= number_format($price, 0, ',', ' ') ?> €</del>
                        <span class="product-price-large price-danger"><?= number_format($priceTotal, 0, ',', ' ') ?> €</span>
                        <span class="badge-discount-large">-<?= $discount ?>%</span>
                    <?php else: ?>
                        <span class="product-price-large price-primary"><?= number_format($price, 0, ',', ' ') ?> €</span>
                    <?php endif; ?>
                </div>

                <div class="action-buttons-row">
                    <button type="button" id="btnAddToCart" class="btn-add-cart" data-id="<?= $productId ?>" aria-label="Ajouter ce produit au panier">
                        <i class="fa-solid fa-cart-plus" aria-hidden="true"></i> Ajouter au panier
                    </button>
                    
                    <button type="button" class="btn-favorite-toggle <?= $isFavorite ? 'active' : '' ?>" data-id="<?= $productId ?>" aria-label="Ajouter aux favoris" title="Ajouter aux favoris">
                        <i class="fa-<?= $isFavorite ? 'solid' : 'regular' ?> fa-heart" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

    </div>

    <div id="imageZoomModal" class="image-zoom-modal" role="dialog" aria-modal="true" aria-labelledby="zoomModalTitle">
        <span class="close-zoom-modal" id="closeZoomModal" aria-label="Fermer le zoom" role="button" tabindex="0">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </span>
        <h2 id="zoomModalTitle" class="sr-only">Zoom sur l'image du produit</h2>
        <img class="zoom-modal-content" id="zoomedImage" alt="Image agrandie du produit">
    </div>

    <?php 
    require 'tabs.php'; 
    require 'exclusives.php'; 
    ?>

</div>

<script src="<?= URL ?>public/assets/js/product.js" defer></script>