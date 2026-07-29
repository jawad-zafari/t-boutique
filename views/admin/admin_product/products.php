<?php $products = $data['product'] ?? []; ?>
<div class="admin-container">
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert-sticky success" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> 
            <span>L'action a été effectuée avec succès !</span>
        </div>
    <?php endif; ?>

    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-box-open" aria-hidden="true"></i> Gestion des Produits
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminProduct/addProduct" class="btn-admin-primary" aria-label="Ajouter un nouveau produit">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter un produit
            </a>
            <button type="submit" form="formProductsSelection" id="btnDeleteProducts" class="btn-admin-danger" aria-label="Supprimer les produits sélectionnés">
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer
            </button>
        </div>
    </header>

    <form id="formProductsSelection" action="<?= URL ?>AdminProduct/deleteProduct" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste complète des produits de la boutique">
                <thead>
                    <tr>
                        <th scope="col" class="col-id">ID</th>
                        <th scope="col" class="col-img text-center">Image</th>
                        <th scope="col">Titre du produit</th>
                        <th scope="col">Prix</th>
                        <th scope="col" class="text-center col-action">Modifier</th>
                        <th scope="col" class="text-center col-action">Galerie</th>
                        <th scope="col" class="text-center col-action">Avis</th>
                        <th scope="col" class="text-center col-action">Attributs</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner tout">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($products)): foreach ($products as $row): 
                        $pId = (int)$row['id'];
                    ?>
                    <tr>
                        <td class="col-id"><strong><?= $pId; ?></strong></td>
                        <td class="col-img text-center">
                            <img src="<?= htmlspecialchars($row['thumb_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Image produit" class="table-img-preview" onerror="this.src='https://placehold.co/50x50/f1f3f5/3b5bdb?text=Img'">
                        </td>
                        <td><strong><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        <td><?= number_format($row['price'] ?? 0, 0, ',', ' '); ?> €</td>
                        
                        <td class="text-center">
                            <a href="<?= URL ?>AdminProduct/addProduct/<?= $pId; ?>" class="action-icon icon-edit" title="Modifier le produit" aria-label="Modifier">
                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="<?= URL ?>AdminProduct/gallery/<?= $pId; ?>" class="action-icon icon-warning" title="Gérer la galerie d'images" aria-label="Galerie">
                                <i class="fa-solid fa-images" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="<?= URL ?>AdminProduct/reviews/<?= $pId; ?>" class="action-icon icon-primary" title="Critiques et avis" aria-label="Avis">
                                <i class="fa-solid fa-comments" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="<?= URL ?>AdminProduct/attributes/<?= $pId; ?>" class="action-icon icon-success" title="Gérer les attributs" aria-label="Attributs">
                                <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                            </a>
                        </td>
                        
                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= $pId; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner">
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="9" class="text-empty-table text-center">Aucun produit trouvé dans la base de données.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>
<script src="<?= URL ?>public/assets/js/admin_product.js" defer></script>