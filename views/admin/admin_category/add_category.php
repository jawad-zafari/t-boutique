<?php
$categoryInfo = $data['categoryInfo'] ?? [];
$isEdit = !empty($data['edit']) && $data['edit'] > 0;
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> 
            <?= !$isEdit ? 'Créer une nouvelle catégorie' : 'Modifier la catégorie' ?>
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminCategory/showChildren/<?= (int)($data['parentId'] ?? 0) ?>" class="btn-admin-back" aria-label="Retourner à la liste">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box">
        <form action="<?= URL ?>AdminCategory/addCategory/<?= (int)($data['parentId'] ?? 0) ?>/<?= (int)($data['edit'] ?? 0) ?>" method="post" id="formCategoryManage">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="categoryTitle">Titre de la catégorie * :</label>
                <input id="categoryTitle" type="text" name="title" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($categoryInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') : '' ?>" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="categoryParent">Catégorie parente :</label>
                <select id="categoryParent" name="parent" class="form-control" aria-label="Sélectionner la catégorie parente">
                    <option value="0">-- Sélectionner (Catégorie Principale) --</option>
                    <?php
                    $selectedId = !$isEdit ? ((int)($data['parentId'] ?? 0)) : ((int)($categoryInfo['parent_id'] ?? 0));
                    
                    if(!empty($data['category'])):
                        foreach ($data['category'] as $row):
                            // Éviter qu'une catégorie soit son propre parent lors de l'édition
                            if ($isEdit && $row['id'] == $data['edit']) continue;
                            $isSelected = ($row['id'] == $selectedId) ? 'selected' : '';
                    ?>
                        <option value="<?= (int)$row['id'] ?>" <?= $isSelected ?>>
                            <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <button type="submit" class="btn-admin-submit" aria-label="Enregistrer les modifications">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Enregistrer la catégorie
            </button>
            
        </form>
    </div>
</div>