<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-newspaper" aria-hidden="true"></i> Gestion des Actualités
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminNews/add" class="btn-admin-primary" aria-label="Ajouter une nouvelle actualité">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter une actualité
            </a>
        </div>
    </header>

    <div class="admin-table-wrapper">
        <table class="admin-table" aria-label="Liste des actualités publiées">
            <thead>
                <tr>
                    <th scope="col" style="width: 80px;" class="text-center">Image</th>
                    <th scope="col">Titre de l'actualité</th>
                    <th scope="col" style="width: 150px;" class="text-center">Date de création</th>
                    <th scope="col" style="width: 120px;" class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $newsList = $data['news'] ?? [];
                if(!empty($newsList)): foreach($newsList as $news): 
                ?>
                <tr>
                    <td class="text-center">
                        <img src="<?= URL . htmlspecialchars($news['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                             alt="Image de <?= htmlspecialchars($news['title'] ?? 'l\'actualité', ENT_QUOTES, 'UTF-8') ?>" 
                             class="table-img-preview" 
                             onerror="this.src='https://placehold.co/50x50/f1f3f5/3b5bdb?text=News'">
                    </td>
                    <td><strong><?= htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td class="text-center"><?= htmlspecialchars($news['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                    
                    <td class="text-center">
                        <a href="<?= URL ?>AdminNews/edit/<?= (int)$news['id'] ?>" class="action-icon icon-edit" title="Modifier" aria-label="Modifier l'actualité">
                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                        </a>
                        
                        <form action="<?= URL ?>AdminNews/delete/<?= (int)$news['id'] ?>" method="POST" class="form-delete-news form-inline-action">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            <button type="button" class="btn-delete-trigger btn-icon-transparent text-danger action-icon" title="Supprimer" aria-label="Supprimer cette actualité">
                                <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="4" class="text-empty-table">Aucune actualité trouvée dans la base de données.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="<?= URL ?>public/assets/js/admin_news.js" defer></script>