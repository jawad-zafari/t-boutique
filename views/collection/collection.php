<?php
$data = $data ?? [];
$type = $data['type'] ?? 'latest';
$products = $data['products'] ?? [];
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$categoryId = $data['categoryId'] ?? 0;

$filters = $data['filters'] ?? [];
$inStock = $filters['in_stock'] ?? 0;
$order1 = $filters['orderType1'] ?? 3;
$order2 = $filters['orderType2'] ?? 2;
$limitVal = $filters['limit'] ?? 20;

$pageTitle = "Collection";
if ($type === 'latest') { $pageTitle = "Nouveautés"; }
elseif ($type === 'special') { $pageTitle = "Offres du moment"; }
elseif ($type === 'exclusive') { $pageTitle = "Exclusivités Boutique"; }
elseif ($type === 'mostviewed') { $pageTitle = "Les plus vus"; }
elseif ($type === 'category') { $pageTitle = $data['categoryTitle'] ?: "Catégorie"; }
?>

<div id="collectionMainWrapper" class="collection-container collection-page-wrapper" data-csrf="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <nav class="breadcrumb-navigation" aria-label="Fil d'Ariane">
        <button type="button" class="btn-go-back js-back-button" aria-label="Retour à la page précédente">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
        </button>
        <span class="divider-icon" aria-hidden="true">|</span>
        <a href="<?= URL ?>Index/index" class="link-home">
            <i class="fa-solid fa-house" aria-hidden="true"></i> Accueil
        </a>
        <i class="fa-solid fa-angle-right divider-icon" aria-hidden="true"></i>
        <span class="current-page-title" aria-current="page"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></span>
    </nav>

    <div class="collection-header-box">
        <h2 class="main-title"><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h2>
        <?php if ($type === 'latest'): ?>
            <p class="subtitle">Les derniers produits ajoutés à notre catalogue.</p>
        <?php elseif ($type === 'special'): ?>
            <p class="subtitle">Ne manquez pas ces produits à prix réduit.</p>
        <?php endif; ?>
    </div>

    <form id="collectionFilterForm" aria-label="Filtres de la collection">
        <div class="search-toolbar glass-panel">
            <label class="toggle-switch" title="Afficher uniquement les produits en stock">
                <input type="checkbox" id="toggleInStock" name="in_stock" value="1" <?= $inStock == 1 ? 'checked' : '' ?>>
                <span class="slider round"></span>
                <span class="toggle-label">En stock</span>
            </label>

            <select name="orderType1" class="form-control" aria-label="Trier par">
                <option value="3" <?= $order1 == 3 ? 'selected' : '' ?>>Plus récents</option>
                <option value="1" <?= $order1 == 1 ? 'selected' : '' ?>>Prix</option>
                <option value="2" <?= $order1 == 2 ? 'selected' : '' ?>>Vues</option>
            </select>
            
            <select name="orderType2" class="form-control" aria-label="Ordre du tri">
                <option value="2" <?= $order2 == 2 ? 'selected' : '' ?>>Décroissant</option>
                <option value="1" <?= $order2 == 1 ? 'selected' : '' ?>>Croissant</option>
            </select>
            
            <select name="limit" class="form-control" aria-label="Nombre de produits par page">
                <option value="20" <?= $limitVal == 20 ? 'selected' : '' ?>>20 / page</option>
                <option value="40" <?= $limitVal == 40 ? 'selected' : '' ?>>40 / page</option>
                <option value="60" <?= $limitVal == 60 ? 'selected' : '' ?>>60 / page</option>
            </select>
        </div>
    </form>

    <div class="products-grid-layout">
        <?php if (!empty($products)): foreach ($products as $product): 
            $price = $product['price'] ?? 0;
            $discount = $product['discount_percent'] ?? 0;
            $hasDiscount = $discount > 0;
        ?>
            <div class="product-card hover-glow">
                <button type="button" class="btn-favorite-toggle" data-id="<?= (int)$product['id'] ?>" aria-label="Ajouter aux favoris" title="Ajouter aux favoris">
                    <i class="fa-regular fa-heart" aria-hidden="true"></i>
                </button>
                <?php if ($hasDiscount): ?>
                    <div class="badge-item badge-discount">-<?= $discount ?>%</div>
                <?php else: ?>
                    <div class="badge-item badge-new">Nouveau</div>
                <?php endif; ?>

                <a href="<?= URL ?>Product/index/<?= (int)$product['id'] ?>" class="card-link-wrapper" aria-label="Voir le produit <?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="image-wrapper">
                        <img src="<?= URL ?>public/images/products/<?= (int)$product['id'] ?>/product_220.jpg" alt="<?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                    </div>
                </a>

                <div class="card-content">
                    <a href="<?= URL ?>Product/index/<?= (int)$product['id'] ?>" class="product-title-link">
                        <h4 class="product-title"><?= htmlspecialchars($product['title'], ENT_QUOTES, 'UTF-8') ?></h4>
                    </a>
                    
                    <div class="price-cart-row">
                        <div class="product-price-container">
                            <?php if($hasDiscount): ?>
                                <del class="price-old"><?= number_format($price, 0, ',', ' ') ?> €</del>
                                <span class="product-price price-danger"><?= number_format($product['price_total'] ?? 0, 0, ',', ' ') ?> €</span>
                            <?php else: ?>
                                <span class="product-price price-primary"><?= number_format($price, 0, ',', ' ') ?> €</span>
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn-quick-add" data-id="<?= (int)$product['id'] ?>" aria-label="Ajouter au panier" title="Ajouter au panier">
                            <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <p class="empty-collection-text empty-collection-box">Aucun produit trouvé dans cette collection avec les filtres actuels.</p>
        <?php endif; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav aria-label="Pagination" class="pagination-wrapper">
            <?php 
            $linkUrl = ($type === 'category') ? "Collection/index/category/$categoryId/" : "Collection/index/$type/";
            ?>

            <?php if ($currentPage > 1): ?>
                <a href="<?= URL . $linkUrl . ($currentPage - 1) ?>" class="page-link prev-next" aria-label="Page précédente">
                    <i class="fa-solid fa-angle-left" aria-hidden="true"></i> Précédent
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="<?= URL . $linkUrl . $i ?>" class="page-link <?= $i === $currentPage ? 'active' : '' ?>" <?= $i === $currentPage ? 'aria-current="page"' : '' ?>>
                    <?= $i ?>
                </a>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="<?= URL . $linkUrl . ($currentPage + 1) ?>" class="page-link prev-next" aria-label="Page suivante">
                    Suivant <i class="fa-solid fa-angle-right" aria-hidden="true"></i>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>

</div>

<script src="<?= URL ?>public/assets/js/collection.js" defer></script>