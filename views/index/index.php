<div id="homeMainWrapper" class="home-global-wrapper" data-csrf="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <section class="main-slider" aria-roledescription="carousel" aria-label="Bannières principales">
        <div class="slider-track" id="sliderTrack">
            <?php
            $slider1 = $data['slider1'] ?? [];
            if(!empty($slider1)): foreach($slider1 as $slide):
            ?>
            <div class="slide" role="group" aria-roledescription="slide">
                <a href="<?= htmlspecialchars($slide['link'] ?? '#', ENT_QUOTES, 'UTF-8') ?>" class="slide-link-container">
                    <img src="<?= URL . htmlspecialchars($slide['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($slide['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="slide-bg">
                    
                    <div class="slide-content">
                        <h2 class="slide-title"><?= htmlspecialchars($slide['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
                        
                        <?php if(!empty($slide['description'])): ?>
                            <p class="slide-desc"><?= htmlspecialchars($slide['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        
                        <span class="btn-slide-action">
                            <?= htmlspecialchars($slide['button_text'] ?? 'Découvrir', ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                </a>
            </div>
            <?php endforeach; else: ?>
                <div class="slide" role="group">
                    <img src="https://placehold.co/1920x600/3b5bdb/ffffff?text=Bannière+Principale" class="slide-bg" alt="Bannière par défaut">
                    <div class="slide-content">
                        <h2 class="slide-title">Bienvenue sur notre boutique</h2>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <button type="button" class="slider-btn prev cyber-btn" id="btnPrev" aria-label="Bannière précédente"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
        <button type="button" class="slider-btn next cyber-btn" id="btnNext" aria-label="Bannière suivante"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
        
        <div class="slider-dots" id="sliderDots" role="tablist"></div>
    </section>

    <div class="home-container">
        
        <section class="features-row modern-shadow">
            <div class="feature-item">
                <div class="icon-wrapper"><i class="fa-solid fa-truck-fast" aria-hidden="true"></i></div>
                <div class="feature-text">
                    <span class="feature-title">Livraison Gratuite</span>
                    <span class="feature-subtitle">Dès 50€ d'achat</span>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon-wrapper"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i></div>
                <div class="feature-text">
                    <span class="feature-title">Retours Faciles</span>
                    <span class="feature-subtitle">Sous 30 jours</span>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon-wrapper"><i class="fa-solid fa-lock" aria-hidden="true"></i></div>
                <div class="feature-text">
                    <span class="feature-title">Paiement Sécurisé</span>
                    <span class="feature-subtitle">100% garanti</span>
                </div>
            </div>
            
            <div class="feature-item">
                <div class="icon-wrapper"><i class="fa-solid fa-headset" aria-hidden="true"></i></div>
                <div class="feature-text">
                    <span class="feature-title">Support 24/7</span>
                    <span class="feature-subtitle">À votre écoute</span>
                </div>
            </div>
        </section>

        <section class="brands-carousel-section glass-panel" aria-label="Nos marques">
            <div class="section-header">
                <div class="header-titles">
                    <h3 class="section-title"><i class="fa-regular fa-star" aria-hidden="true"></i> Nos Marques Officielles</h3>
                </div>
                <div class="header-nav-buttons">
                    <button type="button" class="nav-btn prev" id="brandsBtnPrev" aria-label="Marques précédentes"><i class="fa-solid fa-angle-left" aria-hidden="true"></i></button>
                    <button type="button" class="nav-btn next" id="brandsBtnNext" aria-label="Marques suivantes"><i class="fa-solid fa-angle-right" aria-hidden="true"></i></button>
                </div>
            </div>

            <div class="brands-carousel-wrapper">
                <div class="brands-carousel-track" id="brandsCarouselTrack">
                    <?php 
                    $brands = $data['brands'] ?? [];
                    if(!empty($brands)): foreach($brands as $brand): 
                        $brandId = (int)($brand['id'] ?? 0);
                        $brandName = htmlspecialchars($brand['title'] ?? '', ENT_QUOTES, 'UTF-8');
                    ?>
                    <div class="brands-carousel-item">
                        <a href="<?= URL ?>Collection/index/category/<?= $brandId ?>/1" class="brand-item-circle" title="<?= $brandName ?>" aria-label="<?= $brandName ?>">
                            <img src="<?= URL . htmlspecialchars($brand['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= $brandName ?>" 
                                 onerror="this.outerHTML='<span class=\'brand-text-fallback\'><?= addslashes($brandName) ?></span>'">
                        </a>
                    </div>
                    <?php endforeach; else: ?>
                        <p class="text-empty-msg-center">Aucune marque définie.</p>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <?php 
        $specialOffers = $data['slider2_items'] ?? []; 
        if(!empty($specialOffers)):
        ?>
        <section class="product-carousel-section promo-section" aria-label="Offres du moment">
            <div class="section-header">
                <div class="header-titles">
                    <h3 class="section-title">Offres du moment</h3>
                    <p class="section-subtitle">Ne manquez pas ces produits à prix réduit.</p>
                </div>
                <a href="<?= URL ?>Collection/index/special" class="btn-see-all">Voir tout <i class="fa-solid fa-angle-right" aria-hidden="true"></i></a>
            </div>
            
            <div class="carousel-track">
                <?php foreach($specialOffers as $product): 
                    $price = (float)($product['price'] ?? 0);
                    $discount = (int)($product['discount_percent'] ?? 0);
                    $hasDiscount = $discount > 0;
                    $prodId = (int)($product['id'] ?? 0);
                    $prodTitle = htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="product-card hover-glow">
                    <button type="button" class="btn-favorite-toggle" data-id="<?= $prodId ?>" aria-label="Ajouter aux favoris" title="Ajouter aux favoris">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                    </button>
                    <?php if($hasDiscount): ?>
                        <div class="badge-item badge-discount">-<?= $discount ?>%</div>
                    <?php else: ?>
                        <div class="badge-item badge-new">Nouveau</div>
                    <?php endif; ?>
                    
                    <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="card-link-wrapper" aria-label="Voir le produit <?= $prodTitle ?>">
                        <div class="image-wrapper">
                            <img src="<?= URL ?>public/images/products/<?= $prodId ?>/product_220.jpg" alt="<?= $prodTitle ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                        </div>
                    </a>

                    <div class="card-content">
                        <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="product-title-link">
                            <h4 class="product-title"><?= $prodTitle ?></h4>
                        </a>
                        
                        <div class="price-cart-row">
                            <div class="product-price-container">
                                <?php if($hasDiscount): ?>
                                    <del class="price-old"><?= number_format($price, 0, ',', ' ') ?> €</del>
                                    <span class="product-price price-danger"><?= number_format((float)($product['price_total'] ?? 0), 0, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <span class="product-price price-primary"><?= number_format($price, 0, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-quick-add square-btn" data-id="<?= $prodId ?>" aria-label="Ajouter au panier" title="Ajouter au panier">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php 
        $latestProducts = $data['latest_products'] ?? []; 
        if(!empty($latestProducts)):
        ?>
        <section class="product-carousel-section" aria-label="Nouveautés">
            <div class="section-header">
                <div class="header-titles">
                    <h3 class="section-title">Nouveautés</h3>
                    <p class="section-subtitle">Les derniers produits ajoutés à notre catalogue.</p>
                </div>
                <a href="<?= URL ?>Collection/index/latest" class="btn-see-all">Voir tout <i class="fa-solid fa-angle-right" aria-hidden="true"></i></a>
            </div>
            
            <div class="carousel-track">
                <?php foreach($latestProducts as $product): 
                    $price = (float)($product['price'] ?? 0);
                    $discount = (int)($product['discount_percent'] ?? 0);
                    $hasDiscount = $discount > 0;
                    $prodId = (int)($product['id'] ?? 0);
                    $prodTitle = htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="product-card hover-glow">
                    <button type="button" class="btn-favorite-toggle" data-id="<?= $prodId ?>" aria-label="Ajouter aux favoris">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                    </button>
                    <?php if($hasDiscount): ?>
                        <div class="badge-item badge-discount">-<?= $discount ?>%</div>
                    <?php else: ?>
                        <div class="badge-item badge-new">Nouveau</div>
                    <?php endif; ?>
                    
                    <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="card-link-wrapper" aria-label="Voir le produit <?= $prodTitle ?>">
                        <div class="image-wrapper">
                            <img src="<?= URL ?>public/images/products/<?= $prodId ?>/product_220.jpg" alt="<?= $prodTitle ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                        </div>
                    </a>

                    <div class="card-content">
                        <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="product-title-link">
                            <h4 class="product-title"><?= $prodTitle ?></h4>
                        </a>
                        
                        <div class="price-cart-row">
                            <div class="product-price-container">
                                <?php if($hasDiscount): ?>
                                    <del class="price-old"><?= number_format($price, 0, ',', ' ') ?> €</del>
                                    <span class="product-price price-danger"><?= number_format((float)($product['price_total'] ?? 0), 0, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <span class="product-price price-primary"><?= number_format($price, 0, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-quick-add square-btn" data-id="<?= $prodId ?>" aria-label="Ajouter au panier">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php 
        $exclusives = $data['exclusives'] ?? [];
        if(!empty($exclusives)):
        ?>
        <section class="product-carousel-section" aria-label="Exclusivités">
            <div class="section-header">
                <div class="header-titles">
                    <h3 class="section-title">Exclusivités Boutique</h3>
                </div>
                <a href="<?= URL ?>Collection/index/exclusive" class="btn-see-all">Voir tout <i class="fa-solid fa-angle-right" aria-hidden="true"></i></a>
            </div>
            
            <div class="carousel-track">
                <?php foreach($exclusives as $product): 
                    $price = (float)($product['price'] ?? 0);
                    $discount = (int)($product['discount_percent'] ?? 0);
                    $hasDiscount = $discount > 0;
                    $prodId = (int)($product['id'] ?? 0);
                    $prodTitle = htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="product-card hover-glow">
                    <button type="button" class="btn-favorite-toggle" data-id="<?= $prodId ?>" aria-label="Ajouter aux favoris">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                    </button>
                    <?php if($hasDiscount): ?>
                        <div class="badge-item badge-discount">-<?= $discount ?>%</div>
                    <?php else: ?>
                        <div class="badge-item badge-new">Nouveau</div>
                    <?php endif; ?>
                    
                    <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="card-link-wrapper">
                        <div class="image-wrapper">
                            <img src="<?= URL ?>public/images/products/<?= $prodId ?>/product_220.jpg" alt="<?= $prodTitle ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                        </div>
                    </a>

                    <div class="card-content">
                        <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="product-title-link">
                            <h4 class="product-title"><?= $prodTitle ?></h4>
                        </a>
                        
                        <div class="price-cart-row">
                            <div class="product-price-container">
                                <?php if($hasDiscount): ?>
                                    <del class="price-old"><?= number_format($price, 0, ',', ' ') ?> €</del>
                                    <span class="product-price price-danger"><?= number_format((float)($product['price_total'] ?? 0), 0, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <span class="product-price price-primary"><?= number_format($price, 0, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-quick-add square-btn" data-id="<?= $prodId ?>" aria-label="Ajouter au panier">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <?php 
        $mostViewed = $data['most_viewed'] ?? [];
        if(!empty($mostViewed)):
        ?>
        <section class="product-carousel-section" aria-label="Les plus vus">
            <div class="section-header">
                <div class="header-titles">
                    <h3 class="section-title">Les plus vus</h3>
                </div>
                <a href="<?= URL ?>Collection/index/mostviewed" class="btn-see-all">Voir tout <i class="fa-solid fa-angle-right" aria-hidden="true"></i></a>
            </div>
            
            <div class="carousel-track">
                <?php foreach($mostViewed as $product): 
                    $price = (float)($product['price'] ?? 0);
                    $discount = (int)($product['discount_percent'] ?? 0);
                    $hasDiscount = $discount > 0;
                    $prodId = (int)($product['id'] ?? 0);
                    $prodTitle = htmlspecialchars($product['title'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="product-card hover-glow">
                    <button type="button" class="btn-favorite-toggle" data-id="<?= $prodId ?>" aria-label="Ajouter aux favoris">
                        <i class="fa-regular fa-heart" aria-hidden="true"></i>
                    </button>
                    <?php if($hasDiscount): ?>
                        <div class="badge-item badge-discount">-<?= $discount ?>%</div>
                    <?php else: ?>
                        <div class="badge-item badge-new">Nouveau</div>
                    <?php endif; ?>
                    
                    <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="card-link-wrapper">
                        <div class="image-wrapper">
                            <img src="<?= URL ?>public/images/products/<?= $prodId ?>/product_220.jpg" alt="<?= $prodTitle ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
                        </div>
                    </a>

                    <div class="card-content">
                        <a href="<?= URL ?>Product/index/<?= $prodId ?>" class="product-title-link">
                            <h4 class="product-title"><?= $prodTitle ?></h4>
                        </a>
                        
                        <div class="price-cart-row">
                            <div class="product-price-container">
                                <?php if($hasDiscount): ?>
                                    <del class="price-old"><?= number_format($price, 0, ',', ' ') ?> €</del>
                                    <span class="product-price price-danger"><?= number_format((float)($product['price_total'] ?? 0), 0, ',', ' ') ?> €</span>
                                <?php else: ?>
                                    <span class="product-price price-primary"><?= number_format($price, 0, ',', ' ') ?> €</span>
                                <?php endif; ?>
                            </div>
                            <button type="button" class="btn-quick-add square-btn" data-id="<?= $prodId ?>" aria-label="Ajouter au panier">
                                <i class="fa-solid fa-cart-plus" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <section class="media-widget dark-cyber-panel" aria-label="Boutique TV">
            <div class="section-header">
                <h3 class="section-title"><i class="fa-solid fa-tv" aria-hidden="true"></i> Boutique TV</h3>
                <div class="header-nav-buttons">
                    <button type="button" class="nav-btn prev" id="tvBtnPrev" aria-label="Vidéo précédente"><i class="fa-solid fa-angle-left" aria-hidden="true"></i></button>
                    <button type="button" class="nav-btn next" id="tvBtnNext" aria-label="Vidéo suivante"><i class="fa-solid fa-angle-right" aria-hidden="true"></i></button>
                </div>
            </div>
            
            <div class="tv-carousel-track" id="tvCarouselTrack">
                <?php
                $tvSettings = $data['tv_settings'] ?? [];
                $tvCover = !empty($tvSettings['tv_cover_image']) ? URL . htmlspecialchars($tvSettings['tv_cover_image'], ENT_QUOTES, 'UTF-8') : 'https://placehold.co/600x400/1a1a2e/ffffff?text=Boutique+TV+1';
                $tvLink = !empty($tvSettings['tv_video_link']) ? htmlspecialchars($tvSettings['tv_video_link'], ENT_QUOTES, 'UTF-8') : 'https://www.youtube.com/embed/tgbNymZ7vqY';
                
                $tvItems = $data['tv_items'] ?? [
                    ['video_link' => $tvLink, 'cover_image' => $tvCover, 'title' => 'Présentation de notre Boutique'],
                    ['video_link' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'cover_image' => 'https://placehold.co/600x400/0f172a/ffffff?text=Review+Smartphones+2026', 'title' => 'Focus Technologies 2026'],
                    ['video_link' => 'https://www.youtube.com/embed/tgbNymZ7vqY', 'cover_image' => 'https://placehold.co/600x400/311010/ffffff?text=Offres+Exclusives+Boutique', 'title' => 'Guide d\'Achat et Garanties']
                ];

                foreach($tvItems as $item):
                    $vLink = htmlspecialchars($item['video_link'] ?? '', ENT_QUOTES, 'UTF-8');
                    $vCover = htmlspecialchars($item['cover_image'] ?? '', ENT_QUOTES, 'UTF-8');
                    $vTitle = htmlspecialchars($item['title'] ?? 'Vidéo TV', ENT_QUOTES, 'UTF-8');
                ?>
                <div class="tv-carousel-item" data-video-src="<?= $vLink ?>">
                    <div class="tv-image-container hover-zoom" role="button" aria-label="Lire la vidéo <?= $vTitle ?>" tabindex="0">
                        <img src="<?= $vCover ?>" alt="<?= $vTitle ?>">
                        <div class="play-btn glowing-circle"><i class="fa-solid fa-play" aria-hidden="true"></i></div>
                        <div class="tv-item-caption"><?= $vTitle ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="news-section full-width-panel" aria-label="Dernières actualités">
            <div class="section-header">
                <h3 class="section-title"><i class="fa-regular fa-newspaper" aria-hidden="true"></i> Dernières actualités</h3>
                <div class="header-nav-buttons">
                    <button type="button" class="nav-btn prev" id="newsBtnPrev" aria-label="Actualités précédentes"><i class="fa-solid fa-angle-left" aria-hidden="true"></i></button>
                    <button type="button" class="nav-btn next" id="newsBtnNext" aria-label="Actualités suivantes"><i class="fa-solid fa-angle-right" aria-hidden="true"></i></button>
                </div>
            </div>
            
            <div class="news-carousel-track" id="newsCarouselTrack">
                <?php 
                $newsList = $data['latest_news'] ?? [];
                if(!empty($newsList)): foreach($newsList as $news): 
                    $newsTitle = htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8');
                    $newsDesc = htmlspecialchars($news['short_desc'] ?? '', ENT_QUOTES, 'UTF-8');
                    $newsImg = URL . htmlspecialchars($news['image_path'] ?? '', ENT_QUOTES, 'UTF-8');
                    
                    // UTILISATION DE LA FONCTION STANDARD
                    $newsDate = htmlspecialchars(Model::formatDateForDisplay($news['created_at'] ?? ''), ENT_QUOTES, 'UTF-8');
                ?>
                <div class="news-card clickable-news-item hover-glow" role="button" tabindex="0" data-title="<?= $newsTitle ?>" data-desc="<?= $newsDesc ?>" data-img="<?= $newsImg ?>" data-date="<?= $newsDate ?>">
                    <div class="news-card-img">
                        <img src="<?= $newsImg ?>" alt="<?= $newsTitle ?>" onerror="this.src='https://placehold.co/400x250/f1f3f5/3b5bdb?text=News'">
                    </div>
                    <div class="news-card-body">
                        <span class="news-date"><?= $newsDate ?></span>
                        <h4 class="news-title"><?= $newsTitle ?></h4>
                        <p class="news-excerpt"><?= htmlspecialchars(mb_strimwidth($news['short_desc'] ?? '', 0, 80, '...'), ENT_QUOTES, 'UTF-8') ?></p>
                        <span class="news-read-more">Lire la suite <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </div>
                <?php endforeach; else: ?>
                    <p class="text-empty-msg-center">Aucune actualité pour le moment.</p>
                <?php endif; ?>
            </div>
        </section>

    </div>
</div>

<div id="newsModal" class="news-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="newsModalTitle">
    <div class="news-modal-content">
        <button class="close-news-modal" id="closeNewsModal" aria-label="Fermer la fenêtre"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        <div class="news-modal-body">
            <img id="newsModalImg" src="" alt="Image de l'actualité" class="modal-news-img">
            <div class="news-modal-text-content">
                <span id="newsModalDate" class="modal-news-date"></span>
                <h3 id="newsModalTitle" class="modal-news-title"></h3>
                <div id="newsModalDesc" class="modal-news-desc"></div>
            </div>
        </div>
    </div>
</div>

<script src="<?= URL ?>public/assets/js/home.js" defer></script>