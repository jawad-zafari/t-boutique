<div class="comment-container">
    
    <?php $productInfo = $data['productInfo'] ?? []; ?>

    <form method="post" action="<?= URL ?>AddComment/saveComment/<?= (int)($productInfo['id'] ?? 0) ?>" class="comment-form" id="formComment">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <aside class="product-summary">
            <img src="<?= URL ?>public/images/products/<?= (int)($productInfo['id'] ?? 0) ?>/product_220.jpg" alt="<?= htmlspecialchars($productInfo['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?>" class="product-img" onerror="this.src='https://placehold.co/220x220/f1f3f5/3b5bdb?text=Produit'">
            <h3 class="product-title">Évaluez ce produit</h3>
            <p class="product-desc">Partagez votre expérience pour aider les autres utilisateurs.</p>
        </aside>

        <main class="evaluation-section">
            
            <?php
            $commentInfo = $data['commentInfo'] ?? [];
            
            // SÉCURITÉ CRITIQUE : Prévention de l'attaque "PHP Object Injection"
            $commentResult = !empty($commentInfo['parameters']) ? unserialize($commentInfo['parameters'], ['allowed_classes' => false]) : [];
            
            $params = $data['params'] ?? [];
            ?>

            <h4 class="section-title"><i class="fa-solid fa-star-half-stroke" aria-hidden="true"></i> Vos critères d'évaluation</h4>
            
            <div class="sliders-grid">
                <?php foreach ($params as $row): 
                    $defaultValue = isset($commentResult[$row['id']]) ? (int)$commentResult[$row['id']] : 3;
                ?>
                    <div class="slider-group">
                        <label for="param_<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></label>
                        <div class="range-wrapper">
                            <input type="range" 
                                   id="param_<?= (int)$row['id'] ?>" 
                                   name="param<?= (int)$row['id'] ?>" 
                                   min="1" max="5" step="1" 
                                   value="<?= $defaultValue ?>" 
                                   class="native-range"
                                   aria-valuemin="1"
                                   aria-valuemax="5"
                                   aria-valuenow="<?= $defaultValue ?>">
                            <span class="range-badge"><?= $defaultValue ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr class="divider">

            <h4 class="section-title"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Rédigez votre avis</h4>

            <div class="form-group">
                <input type="text" id="commentTitle" name="title" aria-label="Titre de votre avis" value="<?= htmlspecialchars($commentInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Titre de votre avis (ex: Excellent produit !)" class="form-control" required>
            </div>
            
            <div class="form-row">
                <div class="form-group half">
                    <input type="text" id="commentPositive" name="positive" aria-label="Points forts du produit" value="<?= htmlspecialchars($commentInfo['positive_points'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Points forts (séparés par des virgules)" class="form-control input-success">
                </div>
                <div class="form-group half">
                    <input type="text" id="commentNegative" name="negative" aria-label="Points faibles du produit" value="<?= htmlspecialchars($commentInfo['negative_points'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Points faibles (séparés par des virgules)" class="form-control input-danger">
                </div>
            </div>

            <div class="form-group">
                <textarea id="commentContent" name="comment" aria-label="Détail de votre avis" placeholder="Expliquez pourquoi vous avez aimé ou non ce produit..." class="form-control textarea-large" required><?= htmlspecialchars($commentInfo['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-submit" aria-label="Publier mon avis">
                    Publier mon avis <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                </button>
            </div>

        </main>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/comment.js" defer></script>