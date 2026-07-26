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
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer
            </button>
        </div>
    </header>

    <form id="formAttributeSelection" action="<?= URL ?>AdminCategory/deleteAttribute/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)($data['attrInfo']['id'] ?? 0) ?>" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des attributs de spécification">
                <thead>
                    <tr>
                        <th scope="col" style="width: 60px;" class="text-center">ID</th>
                        <th scope="col">Nom de l'attribut</th>
                        <?php if (empty($data['attrInfo']['id'])): ?>
                            <th scope="col" style="width: 180px;" class="text-center">Sous-attributs</th>
                        <?php endif; ?>
                        <th scope="col" style="width: 180px;" class="text-center">Valeurs par défaut</th>
                        <th scope="col" style="width: 100px;" class="text-center">Modifier</th>
                        <th scope="col" style="width: 60px;" class="text-center">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Tout cocher">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $attr = $data['attr'] ?? [];
                    if (!empty($attr)): foreach ($attr as $row): 
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold"><?= (int)$row['id']; ?></td>
                        <td><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <?php if (empty($data['attrInfo']['id'])): ?>
                            <td class="text-center">
                                <a href="<?= URL ?>AdminCategory/showAttributes/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)$row['id']; ?>" class="action-icon icon-sitemap" title="Gérer les sous-attributs" aria-label="Gérer les sous-attributs">
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