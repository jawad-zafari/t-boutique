<?php
$productInfo = $data['productInfo'] ?? [];
$reviewInfo = $data['naghdInfo'] ?? [];
$isEdit = isset($reviewInfo['title']);
$pId = (int)($productInfo['id'] ?? 0);
$rId = (int)($reviewInfo['id'] ?? 0);
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-pen-nib" aria-hidden="true"></i> 
            <?= $isEdit ? 'Modifier la critique' : 'Ajouter une critique' ?>
            <span class="separator">/</span>
            <span class="sub-breadcrumb-info"><?= htmlspecialchars($productInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="admin-actions">
            <a href="#" class="btn-admin-back js-back-button" aria-label="Retour">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box form-box-wide mx-auto">
        
        <form action="<?= URL ?>AdminProduct/addReview/<?= $pId ?>/<?= $rId > 0 ? $rId : '' ?>" method="post" id="formReviewManage">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="reviewTitle">Titre de la critique * :</label>
                <input type="text" id="reviewTitle" name="title" class="form-control" value="<?= htmlspecialchars($reviewInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" placeholder="Ex: Performances exceptionnelles...">
            </div>

            <div class="form-group mt-20">
                <label for="editorDescription">Description détaillée * :</label>
                <textarea id="editorDescription" name="description" class="form-control textarea-tall" required aria-required="true" aria-label="Description de la critique"><?= htmlspecialchars($reviewInfo['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="mt-20 flex-end-container">
                <button type="submit" class="btn-admin-submit btn-wide" aria-label="Enregistrer la critique">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Enregistrer la critique
                </button>
            </div>

        </form>
    </div>
</div>
<script src="<?= URL ?>public/assets/js/admin_product.js" defer></script>