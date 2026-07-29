<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane des spécifications">
            <i class="fa-solid fa-list-check" aria-hidden="true"></i> Gestion des Attributs
            <span class="separator">/</span>
            
            <a href="<?= URL ?>AdminCategory/showAttributes/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>">
                Catégorie : <?= htmlspecialchars($data['categoryInfo']['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </a>

            <?php if (!empty($data['attrInfo']['id'])): ?>
                <span class="separator">/</span>
                <span class="active-breadcrumb-item">Attribut : <?= htmlspecialchars($data['attrInfo']['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif; ?>
        </div>

        <div class="admin-actions">
            <a href="<?= URL ?>AdminCategory/addAttribute/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)($data['attrInfo']['id'] ?? 0) ?>" class="btn-admin-primary" aria-label="Ajouter un nouvel attribut">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter
            </a>
            <button type="button" id="btnDeleteAttribute" class="btn-admin-danger" aria-label="Supprimer les attributs cochés">
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Supprimer
            </button>
            <a href="<?= URL ?>AdminCategory/showChildren/<?= (int)($data['categoryInfo']['parent_id'] ?? 0) ?>" class="btn-admin-back" aria-label="Retour aux catégories">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <form action="<?= URL ?>AdminCategory/deleteAttribute/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)($data['attrInfo']['id'] ?? 0) ?>" method="post" id="formAttributeSelection">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Tableau des attributs">
                <thead>
                    <tr>
                        <th scope="col" class="col-id">N°</th>
                        <th scope="col">Titre de l'attribut</th>
                        
                        <?php if (empty($data['attrInfo']['id'])): ?>
                            <th scope="col" class="text-center col-action">Sous-attributs</th>
                        <?php endif; ?>

                        <th scope="col" class="text-center col-action">Valeurs par défaut</th>
                        <th scope="col" class="text-center col-action">Modifier</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner tout">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $attributes = $data['attr'] ?? [];
                    if (!empty($attributes)): 
                        foreach ($attributes as $row): 
                    ?>
                    <tr>
                        <td class="col-id"><strong><?= (int)$row['id']; ?></strong></td>
                        
                        <td>
                            <strong class="attribute-title-text"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        </td>

                        <?php if (empty($data['attrInfo']['id'])): ?>
                            <td class="text-center">
                                <a href="<?= URL ?>AdminCategory/showAttributes/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)$row['id']; ?>" class="action-icon icon-children" title="Gérer les sous-attributs" aria-label="Gérer les sous-attributs">
                                    <i class="fa-solid fa-sitemap" aria-hidden="true"></i>
                                </a>
                            </td>
                        <?php endif; ?>

                        <td class="text-center">
                            <a href="<?= URL ?>AdminCategory/attributeValues/<?= (int)$row['id']; ?>" class="action-icon icon-tags" title="Gérer les valeurs" aria-label="Gérer les valeurs">
                                <i class="fa-solid fa-tags" aria-hidden="true"></i>
                            </a>
                        </td>

                        <td class="text-center">
                            <a href="<?= URL ?>AdminCategory/addAttribute/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)($data['attrInfo']['id'] ?? 0) ?>/<?= (int)$row['id'] ?>" class="action-icon icon-edit" title="Modifier" aria-label="Modifier l'attribut">
                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            </a>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= (int)$row['id']; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner cette ligne">
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="<?= empty($data['attrInfo']['id']) ? '6' : '5' ?>" class="text-empty-table">Aucun attribut trouvé pour cette catégorie.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>