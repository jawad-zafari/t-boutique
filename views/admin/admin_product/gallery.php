<?php
$gallery = $data['gallery'] ?? [];
$productInfo = $data['productInfo'] ?? [];
$pId = (int)($productInfo['id'] ?? 0);
?>
<div class="admin-container">
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-sticky success" role="alert">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> 
            <span>L'action a été effectuée avec succès !</span>
        </div>
    <?php endif; ?>

    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-images" aria-hidden="true"></i> Galerie du produit
            <span class="separator">/</span>
            <span class="active-breadcrumb-item"><?= htmlspecialchars($productInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="admin-actions">
            <a href="#" class="btn-admin-back js-back-button" aria-label="Retour">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box form-box-wide mx-auto mb-25">
        <form action="<?= URL ?>AdminProduct/addGallery/<?= $pId ?>" method="post" enctype="multipart/form-data" id="formAddGallery">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="galleryImages">Ajouter de nouvelles images (Sélection multiple possible) :</label>
                <input type="file" id="galleryImages" name="images[]" class="form-control" multiple accept="image/jpeg, image/png, image/webp" required>
            </div>
            
            <div class="flex-end-container mt-15">
                <button type="submit" class="btn-admin-submit" aria-label="Uploader les images">
                    <i class="fa-solid fa-cloud-arrow-up" aria-hidden="true"></i> Uploader les images
                </button>
            </div>
        </form>
    </div>

    <form action="<?= URL ?>AdminProduct/deleteGallery/<?= $pId ?>" method="post" id="formGallerySelection">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <?php if(!empty($gallery)): ?>
        <div class="mb-15 flex-end-container">
            <button type="button" id="btnDeleteGallery" class="btn-admin-danger" aria-label="Supprimer les images cochées">
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer la sélection
            </button>
        </div>
        <?php endif; ?>

        <div class="gallery-grid-layout">
            <?php if(!empty($gallery)): foreach ($gallery as $row): ?>
                <div class="gallery-card-item">
                    <div class="checkbox-wrapper" aria-label="Sélectionner l'image pour suppression">
                        <input type="checkbox" name="id[]" value="<?= (int)$row['id'] ?>" class="admin-checkbox row-checkbox">
                    </div>
                    <div class="card-image-wrapper">
                        <img src="<?= URL ?>public/images/products/<?= $pId ?>/gallery/small/<?= htmlspecialchars($row['image_name'], ENT_QUOTES, 'UTF-8') ?>" alt="Image supplémentaire" loading="lazy">
                    </div>
                </div>
            <?php endforeach; else: ?>
                <div class="gallery-empty-state">
                    <i class="fa-regular fa-image empty-state-icon" aria-hidden="true"></i>
                    <p>Aucune image dans la galerie pour ce produit.</p>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>
<script src="<?= URL ?>public/assets/js/admin_product.js" defer></script>