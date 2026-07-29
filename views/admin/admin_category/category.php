<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane de navigation">
            <i class="fa-solid fa-folder-tree" aria-hidden="true"></i> Gestion des Catégories
            <span class="separator">/</span>
            
            <?php if(!empty($data['parents'])): 
                $parents = array_reverse($data['parents']);
                foreach ($parents as $row): ?>
                    <a href="<?= URL ?>AdminCategory/showChildren/<?= (int)$row['id']; ?>">
                        <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                    <span class="separator">&gt;</span>
            <?php endforeach; endif; ?>
            
            <span class="active-breadcrumb-item">
                <?= htmlspecialchars($data['categoryInfo']['title'] ?? 'Catégories Principales', ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>

        <div class="admin-actions">
            <a href="<?= URL ?>AdminCategory/addCategory/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>" class="btn-admin-primary" aria-label="Ajouter une nouvelle catégorie">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter
            </a>
            <button type="button" id="btnDeleteCategory" class="btn-admin-danger" aria-label="Supprimer les catégories sélectionnées">
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Supprimer
            </button>
        </div>
    </header>

    <form action="<?= URL ?>AdminCategory/deleteCategory/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>" method="post" id="formActionAdmin">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des catégories">
                <thead>
                    <tr>
                        <th scope="col" class="col-id">N°</th>
                        <th scope="col">Titre de la catégorie</th>
                        <th scope="col" class="text-center col-action">Sous-catégories</th>
                        <th scope="col" class="text-center col-action">Attributs</th>
                        <th scope="col" class="text-center col-action">Modifier</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner toutes les lignes">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $categories = $data['category'] ?? [];
                    if(!empty($categories)): 
                        foreach ($categories as $row): 
                    ?>
                    <tr>
                        <td class="col-id"><strong><?= (int)$row['id']; ?></strong></td>
                        
                        <td>
                            <strong class="category-title-text"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                        </td>

                        <td class="text-center">
                            <a href="<?= URL ?>AdminCategory/showChildren/<?= (int)$row['id']; ?>" class="action-icon icon-children" title="Voir les sous-catégories" aria-label="Voir les sous-catégories">
                                <i class="fa-solid fa-sitemap" aria-hidden="true"></i>
                            </a>
                        </td>
                        
                        <td class="text-center">
                            <a href="<?= URL ?>AdminCategory/showAttributes/<?= (int)$row['id']; ?>" class="action-icon icon-list" title="Gérer les attributs" aria-label="Gérer les attributs">
                                <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                            </a>
                        </td>

                        <td class="text-center">
                            <a href="<?= URL ?>AdminCategory/addCategory/<?= (int)($data['categoryInfo']['id'] ?? 0) ?>/<?= (int)$row['id']; ?>" class="action-icon icon-edit" title="Modifier" aria-label="Modifier la catégorie">
                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            </a>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= (int)$row['id']; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner la ligne">
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="6" class="text-empty-table">Aucune catégorie trouvée à ce niveau.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>