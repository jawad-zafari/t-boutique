<?php
/**
 * Vue globale : Header (header.php)
 * Architecture DWWM stricte : Séparation des préoccupations (SoC).
 * Sécurité : Protection XSS (htmlspecialchars), Typage strict (int/float) et jeton CSRF.
 */

// Récupération sécurisée des variables transmises par le contrôleur global
$userId    = $userId ?? false;
$userLevel = $userLevel ?? 0;
$menuList  = $menuList ?? [];
$cartItems = $cartItems ?? [];
$cartCount = $cartCount ?? 0;
$favCount  = $favCount ?? 0;
$csrfToken = $csrf_token ?? '';
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <base href="<?= URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boutique en ligne</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= URL ?>public/assets/css/main.css">
</head>
<body>

<header class="site-header">
    <div class="header-container">
        
        <div class="logo-container">
            <a href="<?= URL ?>Index/index" aria-label="Page d'accueil">
                <img src="<?= URL ?>public/images/logo.png" alt="MaBoutique" class="site-logo">
            </a>
        </div>

       <div class="search-container">
            <form action="<?= URL ?>Search/index" method="POST" id="headerSearchForm" class="search-form-wrapper">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" id="headerKeyword" name="keyword" class="search-input" placeholder="Rechercher un produit..." autocomplete="off" aria-label="Champ de recherche">
                <button type="submit" class="search-btn" aria-label="Lancer la recherche">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                </button>
            </form>
            
            <ul id="headerAutoSuggest" class="header-suggest-list" role="listbox"></ul>
        </div>

        <div class="header-actions">
            
            <?php if ((int)$userLevel === 1): ?>
                <a href="<?= URL ?>AdminDashboard/index" class="btn-icon" aria-label="Administration" title="Administration">
                    <i class="fa-solid fa-user-gear" aria-hidden="true"></i>
                </a>
            <?php endif; ?>

            <a href="<?= URL ?>Account/favorites" class="btn-icon" aria-label="Mes favoris" title="Mes favoris">
                <i class="fa-regular fa-heart" aria-hidden="true"></i>
                <span class="cart-counter <?= (int)$favCount === 0 ? 'is-hidden' : '' ?>" id="navFavCounterBadge"><?= (int)$favCount ?></span>
            </a>

            <?php if ($userId == false): ?>
                <a href="<?= URL ?>Login/index" class="btn-icon" aria-label="Se connecter ou s'inscrire" title="Se connecter / S'inscrire">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </a>
            <?php else: ?>
                <a href="<?= URL ?>Account/index" class="btn-icon" aria-label="Mon Espace Client" title="Mon Espace Client">
                    <i class="fa-solid fa-user" aria-hidden="true"></i>
                </a>
            <?php endif; ?>

            <a href="#" class="btn-icon cart-btn" aria-label="Voir mon panier" title="Mon panier">
                <i class="fa-solid fa-cart-shopping" aria-hidden="true"></i> 
                <span class="cart-counter" id="navCartCounterBadge"><?= (int)$cartCount ?></span>
            </a>

            <button class="btn-mobile-menu" id="btnToggleNav" aria-label="Ouvrir le menu de navigation">
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>
        </div>
        
    </div>
</header>

<nav class="main-nav" id="mainNavigation">
    <div class="nav-carousel-wrapper">
        <button type="button" class="nav-arrow left" id="btnNavPrev" aria-label="Faire défiler à gauche"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
        <div class="nav-scroll-container" id="navScrollContainer">
            <ul class="menu-level-1" role="menubar">
                <li class="menu-item all-products-link" role="none"><a href="<?= URL ?>Collection/index/latest" role="menuitem">TOUT</a></li>            
                <?php if(!empty($menuList)): foreach ($menuList as $menu1): ?>
                    <li class="menu-item" role="none">
                        <a href="<?= URL ?>Collection/index/category/<?= (int)$menu1['id'] ?>/1" role="menuitem">
                            <?= htmlspecialchars($menu1['title'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if (!empty($menu1['children'])): ?><i class="fa-solid fa-angle-down nav-dropdown-icon" aria-hidden="true"></i><?php endif; ?>
                        </a>
                        <?php if (!empty($menu1['children'])): ?>
                            <ul class="menu-level-2" role="menu">
                                <?php foreach ($menu1['children'] as $menu2): ?>
                                    <li role="none"><a href="<?= URL ?>Collection/index/category/<?= (int)$menu2['id'] ?>/1" role="menuitem"><?= htmlspecialchars($menu2['title'], ENT_QUOTES, 'UTF-8') ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; endif; ?>
            </ul>
        </div>
        <button type="button" class="nav-arrow right" id="btnNavNext" aria-label="Faire défiler à droite"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
    </div>
</nav>

<div class="cart-overlay" id="cartOverlay"></div>
<div class="cart-sidebar" id="cartSidebar" role="dialog" aria-labelledby="cartSidebarTitle">
    <div class="cart-header">
        <h3 id="cartSidebarTitle">Votre Panier</h3>
        <button type="button" class="close-cart-btn" id="closeCartBtn" aria-label="Fermer le panier"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
    </div>
    
    <div class="cart-body" id="cartSidebarBody">
        <?php 
        $priceTotalAll = 0;
        if(!empty($cartItems)): 
            foreach($cartItems as $item): 
                // SÉCURITÉ : Typage rigoureux pour les calculs financiers (suppression de la variable obsolète)
                $qty = (int)($item['quantity'] ?? 1);
                $price = (float)($item['price'] ?? 0);
                $priceTotalAll += ($price * $qty);
        ?>
        <div class="cart-item-card">
            <img src="<?= URL ?>public/images/products/<?= (int)($item['id'] ?? 0) ?>/product_220.jpg" alt="<?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="item-img" onerror="this.src='https://placehold.co/80x80/f5f5f5/111?text=Img'">
            <div class="item-details">
                <h4 class="item-title"><?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h4>
                <div class="item-price"><?= number_format($price, 2, '.', '') ?> €</div>
                <div class="item-controls">
                    <div class="qty-wrapper">
                        <button type="button" class="btn-qty minus" data-row="<?= (int)($item['cartRow'] ?? 0) ?>" aria-label="Diminuer la quantité">-</button>
                        <input type="text" class="input-qty" value="<?= $qty ?>" readonly aria-label="Quantité" data-row="<?= (int)($item['cartRow'] ?? 0) ?>">
                        <button type="button" class="btn-qty plus" data-row="<?= (int)($item['cartRow'] ?? 0) ?>" aria-label="Augmenter la quantité">+</button>
                    </div>
                    <button type="button" class="btn-remove-item" data-row="<?= (int)($item['cartRow'] ?? 0) ?>" aria-label="Supprimer cet article"><i class="fa-solid fa-trash-can" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>
        <?php endforeach; else: ?>
            <div class="sidebar-empty-state">
                <div class="empty-msg-text">Votre panier est vide.</div>
                <div class="empty-bg-icon"><i class="fa-solid fa-cart-arrow-down" aria-hidden="true"></i></div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="cart-footer">
        <div class="total-row">
            <span class="total-label">Total de la commande</span>
            <span class="total-price" id="cartSidebarTotal"><?= number_format($priceTotalAll, 2, '.', '') ?> €</span>
        </div>
        <a href="<?= URL ?>Order/index" class="btn-checkout-sidebar">Passer la commande <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
    </div>
</div>