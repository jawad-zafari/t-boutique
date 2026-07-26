<?php
$attr = $data['attr'] ?? [];
$productInfo = $data['productInfo'] ?? [];
$pId = (int)($productInfo['id'] ?? 0);
?>
<div class="admin-container">
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-sticky success" role="alert">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> 
            <span>Les attributs du produit ont été mis à jour avec succès !</span>
        </div>
    <?php endif; ?>

    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-list-check" aria-hidden="true"></i> Attributs du produit
            <span class="separator">/</span>
            <span class="active-breadcrumb-item"><?= htmlspecialchars($productInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="admin-actions">
            <a href="#" class="btn-admin-back js-back-button" aria-label="Retour">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <form action="<?= URL ?>AdminProduct/attributes/<?= $pId ?>" method="post" id="formProductAttributes">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <div class="admin-form-box form-box-wide mx-auto">
            
            <div class="attr-grid-layout">
                
                <?php if(!empty($attr)): foreach ($attr as $row): ?>
                    <div class="attr-group-card">
                        <label class="attr-title" for="attr_<?= (int)$row['id'] ?>"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?> :</label>
                        
                        <select id="attr_<?= (int)$row['id'] ?>" name="x<?= (int)$row['id'] ?>" class="form-control" aria-label="Sélectionner la valeur pour <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>">
                            <option value="">-- Non spécifié --</option>
                            <?php 
                            $possibleValues = $row['possible_values'] ?? [];
                            foreach ($possibleValues as $val): 
                            ?>
                                <option value="<?= (int)$val['id']; ?>" <?= ($val['id'] == ($row['selected_val'] ?? 0)) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($val['value'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        
                        <div class="attr-action-link">
                            <a href="<?= URL ?>AdminCategory/attributeValues/<?= (int)$row['id'] ?>" class="link-success" aria-label="Gérer les valeurs de cet attribut">
                                <i class="fa-solid fa-gear" aria-hidden="true"></i> Gérer les valeurs
                            </a>
                        </div>
                        
                        <input type="hidden" name="id[]" value="<?= (int)$row['id'] ?>">
                    </div>
                <?php endforeach; else: ?>
                    <div class="text-empty-table text-center full-width">
                        Aucun attribut n'est disponible pour la catégorie de ce produit.
                    </div>
                <?php endif; ?>

            </div>

            <?php if(!empty($attr)): ?>
                <div class="border-top-dashed mt-20 flex-end-container">
                    <button type="submit" class="btn-admin-submit btn-wide" aria-label="Mettre à jour les attributs">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Mettre à jour les attributs
                    </button>
                </div>
            <?php endif; ?>
            
        </div>
    </form>
</div>
<script src="<?= URL ?>public/assets/js/admin_product.js" defer></script>