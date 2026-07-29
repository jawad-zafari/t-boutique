<?php
$reviews = $data['naghd'] ?? [];
$productInfo = $data['productInfo'] ?? [];
$pId = (int)($productInfo['id'] ?? 0);
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-comments" aria-hidden="true"></i> Gestion des Critiques
            <span class="separator">/</span>
            <span class="active-breadcrumb-item"><?= htmlspecialchars($productInfo['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminProduct/addReview/<?= $pId ?>" class="btn-admin-primary" aria-label="Ajouter une critique">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter une critique
            </a>
            <button type="button" class="btn-admin-danger" id="btnDeleteReview" aria-label="Supprimer les critiques cochées">
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer
            </button>
            <a href="#" class="btn-admin-back js-back-button" aria-label="Retour">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <form action="<?= URL ?>AdminProduct/deleteReview/<?= $pId ?>" method="post" id="formReviewsSelection">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des critiques du produit">
                <thead>
                    <tr>
                        <th scope="col">Titre de la critique</th>
                        <th scope="col" class="text-center col-action">Modifier</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner tout">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($reviews)): foreach ($reviews as $row): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        
                        <td class="text-center">
                            <a href="<?= URL ?>AdminProduct/addReview/<?= $pId ?>/<?= (int)$row['id'] ?>" class="action-icon icon-edit" title="Modifier cette critique" aria-label="Modifier">
                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            </a>
                        </td>
                        
                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= (int)$row['id']; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner">
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="3" class="text-empty-table text-center">Aucune critique trouvée pour ce produit.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>
<script src="<?= URL ?>public/assets/js/admin_product.js" defer></script>