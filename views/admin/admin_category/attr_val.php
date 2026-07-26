<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-tags" aria-hidden="true"></i> Valeurs par défaut de l'attribut
            <span class="separator">/</span>
            <span class="active-breadcrumb-item"><?= htmlspecialchars($data['attrInfo']['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="admin-actions">
            <a href="javascript:history.back()" class="btn-admin-back" aria-label="Retour à la page précédente">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <div class="admin-form-box form-box-wide">
        <form action="<?= URL ?>AdminCategory/attributeValues/<?= (int)($data['attrInfo']['id'] ?? 0) ?>" method="post" id="formAttributeValuesManage">
            
            <input type="hidden" name="submited" value="1">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="values-grid-layout">
                
                <?php 
                $attrval = $data['attrval'] ?? [];
                foreach ($attrval as $index => $val): 
                ?>
                    <div class="form-group row-existing-value">
                        <label for="existing-<?= (int)$val['id'] ?>">Valeur <?= $index + 1 ?> :</label>
                        <input id="existing-<?= (int)$val['id'] ?>" name="attrval-<?= (int)$val['id'] ?>" type="text" value="<?= htmlspecialchars($val['value'] ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control control-existing" placeholder="Laisser vide pour supprimer">
                    </div>
                <?php endforeach; ?>

                <?php 
                $size = sizeof($attrval);
                // Toujours afficher des champs vides
                for ($i = 1; $i <= 10 - $size; $i++): 
                ?>
                    <div class="form-group row-new-value">
                        <label for="new-<?= $i ?>">Nouvelle valeur :</label>
                        <input id="new-<?= $i ?>" name="attrvalnew[]" type="text" value="" class="form-control control-new" placeholder="Saisir une valeur...">
                    </div>
                <?php endfor; ?>

            </div>

            <button type="submit" class="btn-admin-submit btn-wide" aria-label="Sauvegarder toutes les valeurs saisies">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Enregistrer les valeurs
            </button>

        </form>
    </div>
</div>