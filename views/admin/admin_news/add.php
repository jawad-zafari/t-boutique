<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> Ajouter une Nouvelle Actualité
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminNews/index" class="btn-admin-back" aria-label="Retourner à la liste des actualités">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box">
        
        <form action="<?= URL ?>AdminNews/doAdd" method="post" enctype="multipart/form-data" id="formAddNews">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="form-group">
                <label for="newsTitle">Titre de l'actualité * :</label>
                <input type="text" id="newsTitle" name="title" class="form-control" required aria-required="true" placeholder="Saisir le titre...">
            </div>

            <div class="form-group">
                <label for="newsImage">Image de couverture * (Format JPG/PNG/WEBP) :</label>
                <input type="file" id="newsImage" name="image" class="form-control" accept="image/jpeg, image/png, image/webp" required aria-required="true">
            </div>

            <div class="form-group">
                <label for="newsShortDesc">Description courte * :</label>
                <textarea id="newsShortDesc" name="short_desc" class="form-control" rows="5" required aria-required="true" placeholder="Saisir un résumé de l'actualité..."></textarea>
            </div>

            <button type="submit" class="btn-admin-submit" aria-label="Enregistrer la nouvelle actualité">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Enregistrer l'actualité
            </button>
            
        </form>

    </div>
</div>