<?php 
$productInfo = $data['productInfo'] ?? [];
$isEdit = isset($productInfo['title']);
$pId = (int)($productInfo['id'] ?? 0);
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-box" aria-hidden="true"></i> <?= $isEdit ? 'Modifier le produit' : 'Créer un nouveau produit' ?>
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminProduct/index" class="btn-admin-back" aria-label="Retourner à la liste des produits">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <form action="<?= URL ?>AdminProduct/addProduct/<?= $pId; ?>" method="post" enctype="multipart/form-data" id="formAddProduct">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-form-box form-box-wide mx-auto">
            
            <div class="form-group">
                <label for="productTitle">Titre du produit * :</label>
                <input type="text" id="productTitle" name="title" class="form-control" value="<?= htmlspecialchars($productInfo['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required aria-required="true">
            </div>
            
            <div class="form-row-half">
                <div class="form-group">
                    <label for="productCategory">Catégorie :</label>
                    <select id="productCategory" name="categoryId" class="form-control" aria-label="Sélectionner la catégorie">
                        <option value="0">-- Sélectionner --</option>
                        <?php foreach ($data['category'] ?? [] as $row): ?>
                            <option value="<?= (int)$row['id']; ?>" <?= (isset($productInfo['category_id']) && $productInfo['category_id'] == $row['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="productImage">Image Principale (220x220px) :</label>
                    <input type="file" id="productImage" name="image" class="form-control" accept="image/jpeg, image/png, image/webp">
                </div>
            </div>

            <div class="form-row-half">
                <div class="form-group">
                    <label for="productPrice">Prix TTC (€) * :</label>
                    <input type="number" id="productPrice" name="price" class="form-control" value="<?= (int)($productInfo['price'] ?? 0); ?>" required aria-required="true">
                </div>
                <div class="form-group">
                    <label for="productDiscount">Réduction (%) :</label>
                    <input type="number" id="productDiscount" name="discount" class="form-control" min="0" max="100" value="<?= (int)($productInfo['discount_percent'] ?? 0); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="editorDescription">Description détaillée du produit :</label>
                <textarea id="editorDescription" name="description" class="form-control textarea-tall" aria-label="Saisir la description du produit"><?= htmlspecialchars($productInfo['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="form-row-half mt-20">
                <div class="form-group">
                    <label for="colorSelect">Ajouter des couleurs disponibles :</label>
                    <select id="colorSelect" class="form-control" aria-label="Sélectionner une couleur à ajouter">
                        <option value="">-- Choisir une couleur --</option>
                        <?php foreach ($data['color'] ?? [] as $row): ?>
                            <option value="<?= (int)$row['id']; ?>" data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="colorsContainer" class="tags-container" aria-live="polite">
                        <?php foreach ($productInfo['colorsInfo'] ?? [] as $row): ?>
                            <span class="tag-item">
                                <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                                <input type="hidden" name="color[]" value="<?= (int)$row['id']; ?>">
                                <i class="fa-solid fa-circle-xmark btn-remove-tag" aria-hidden="true" title="Retirer cette couleur"></i>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="garanteeSelect">Ajouter des garanties :</label>
                    <select id="garanteeSelect" class="form-control" aria-label="Sélectionner une garantie à ajouter">
                        <option value="">-- Choisir une garantie --</option>
                        <?php foreach ($data['garantee'] ?? [] as $row): ?>
                            <option value="<?= (int)$row['id']; ?>" data-title="<?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="garanteesContainer" class="tags-container" aria-live="polite">
                        <?php foreach ($productInfo['garanteesInfo'] ?? [] as $row): ?>
                            <span class="tag-item">
                                <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                                <input type="hidden" name="garantee[]" value="<?= (int)$row['id']; ?>">
                                <i class="fa-solid fa-circle-xmark btn-remove-tag" aria-hidden="true" title="Retirer cette garantie"></i>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="form-action-right mt-30 flex-end-container">
                <button type="submit" class="btn-admin-submit-new" aria-label="Enregistrer les informations du produit">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Enregistrer le produit
                </button>
            </div>

        </div>
    </form>
</div>
<script src="<?= URL ?>public/assets/js/admin_product.js" defer></script>