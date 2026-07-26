<?php $news = $data['newsInfo'] ?? []; ?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i> Modifier l'Actualité
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminNews/index" class="btn-admin-back" aria-label="Retourner à la liste des actualités">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box">
        
        <form action="<?= URL ?>AdminNews/doEdit/<?= (int)($news['id'] ?? 0) ?>" method="post" enctype="multipart/form-data" id="formEditNews">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="newsTitle">Titre de l'actualité * :</label>
                <input type="text" id="newsTitle" name="title" class="form-control" value="<?= htmlspecialchars($news['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="newsDate">Date de création * :</label>
                <input type="date" id="newsDate" name="created_at" class="form-control" value="<?= htmlspecialchars($news['created_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="newsImage">Changer l'image (Laissez vide pour conserver l'actuelle) :</label>
                
                <?php if(!empty($news['image_path'])): ?>
                    <div class="current-image-box">
                        <span class="image-label">Image actuelle :</span>
                        <img src="<?= URL . htmlspecialchars($news['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Aperçu de l'image actuelle">
                    </div>
                <?php endif; ?>
                
                <input type="file" id="newsImage" name="image" class="form-control" accept="image/jpeg, image/png, image/webp">
            </div>

            <div class="form-group">
                <label for="newsShortDesc">Description courte * :</label>
                <textarea id="newsShortDesc" name="short_desc" class="form-control" rows="5" required aria-required="true"><?= htmlspecialchars($news['short_desc'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <button type="submit" class="btn-admin-submit" aria-label="Mettre à jour l'actualité">
                <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i> Mettre à jour les informations
            </button>
            
        </form>

    </div>
</div>