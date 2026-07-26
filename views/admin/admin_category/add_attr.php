<?php
$editInfo = $data['editInfo'] ?? [];
$isEdit = !empty($editInfo['title']);
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> 
            <?= !$isEdit ? 'Créer un nouvel attribut' : 'Modifier l\'attribut' ?>
            <span class="separator">/</span>
            <span class="sub-breadcrumb-info">
                Catégorie : <?= htmlspecialchars($data['categoryInfo']['title'] ?? '', ENT_QUOTES, 'UTF-8') ?> 
                <?= !empty($data['attrInfo']['id']) ? '| Parent : ' . htmlspecialchars($data['attrInfo']['title'] ?? '', ENT_QUOTES, 'UTF-8') : '' ?>
            </span>
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminCategory/showAttributes/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)($data['parentId'] ?? 0) ?>" class="btn-admin-back" aria-label="Retour à la liste des attributs">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box">
        <form action="<?= URL ?>AdminCategory/addAttribute/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)($data['parentId'] ?? 0) ?>/<?= (int)($editInfo['id'] ?? 0) ?>" method="post" id="formAttributeManage">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="attributeTitle">Titre de l'attribut * :</label>
                <input type="text" id="attributeTitle" name="title" class="form-control" 
                       value="<?= $isEdit ? htmlspecialchars($editInfo['title'], ENT_QUOTES, 'UTF-8') : '' ?>" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="attributeParent">Attribut parent :</label>
                <select id="attributeParent" name="parent" class="form-control" aria-label="Sélectionner l'attribut parent">
                    <option value="0">-- Sélectionner (Attribut Principal) --</option>
                    <?php
                    $selectedId = (int)($data['parentId'] ?? 0);
                    if(!empty($data['attr'])):
                        foreach ($data['attr'] as $row):
                            if ($isEdit && $row['id'] == $editInfo['id']) continue;
                            $isSelected = ($row['id'] == $selectedId) ? 'selected' : '';
                    ?>
                        <option value="<?= (int)$row['id'] ?>" <?= $isSelected ?>>
                            <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
            </div>

            <button type="submit" class="btn-admin-submit" aria-label="Sauvegarder l'attribut">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Enregistrer l'attribut
            </button>
            
        </form>
    </div>
</div>