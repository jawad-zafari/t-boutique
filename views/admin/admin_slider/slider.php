<?php 
// Traitement des données transmises par le contrôleur AdminSlider
$editSlider = $data['editSlider'] ?? null; 
$isEditMode = ($editSlider !== null);
$sliders = $data['slider'] ?? [];
$sId = (int)($editSlider['id'] ?? 0);
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-images" aria-hidden="true"></i> 
            <?= $isEditMode ? "Modifier le Slide : " . htmlspecialchars($editSlider['title'] ?? '', ENT_QUOTES, 'UTF-8') : "Gestion du Diaporama" ?>
        </div>
        <?php if ($isEditMode): ?>
            <div class="admin-actions">
                <a href="<?= URL ?>AdminSlider/index" class="btn-admin-back" aria-label="Annuler l'édition et retourner">
                    <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
                </a>
            </div>
        <?php endif; ?>
    </header>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert-sticky success" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> 
            <span>
                <?php
                if($_GET['success'] === 'add') echo "Le nouveau slide a été ajouté avec succès !";
                elseif($_GET['success'] === 'update') echo "Le slide a été mis à jour avec succès !";
                elseif($_GET['success'] === 'delete') echo "La suppression a été effectuée avec succès !";
                ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert-sticky danger" role="alert" aria-live="polite">
            <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> 
            <span>Erreur lors du téléchargement de l'image. Vérifiez le format (JPG/PNG/WEBP) et la taille (Max 5Mo).</span>
        </div>
    <?php endif; ?>

    <div class="admin-form-box form-box-wide mx-auto mb-30">
        
        <form action="<?= $isEditMode ? URL . 'AdminSlider/update/' . $sId : URL . 'AdminSlider/add' ?>" method="post" enctype="multipart/form-data" id="formSliderManage">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-row-double">
                <div class="form-group">
                    <label for="sliderTitle">Titre du slide * :</label>
                    <input type="text" id="sliderTitle" name="title" class="form-control" value="<?= htmlspecialchars($editSlider['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" placeholder="Ex: Nouvelle Collection Été">
                </div>

                <div class="form-group">
                    <label for="sliderLink">Lien de redirection (URL) :</label>
                    <input type="url" id="sliderLink" name="link" class="form-control" value="<?= htmlspecialchars($editSlider['link'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="https://maboutique.fr/categorie/promo" dir="ltr">
                </div>
            </div>

            <div class="form-row-triple">
                <div class="form-group">
                    <label for="sliderButtonText">Texte du bouton :</label>
                    <input type="text" id="sliderButtonText" name="button_text" class="form-control" value="<?= htmlspecialchars($editSlider['button_text'] ?? 'Découvrir', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Acheter maintenant">
                </div>

                <div class="form-group">
                    <label for="sliderTextColor">Couleur du texte :</label>
                    <input type="color" id="sliderTextColor" name="text_color" class="form-control form-control-color" value="<?= htmlspecialchars($editSlider['text_color'] ?? '#ffffff', ENT_QUOTES, 'UTF-8') ?>" title="Choisir une couleur">
                </div>

                <div class="form-group">
                    <label for="sliderImage">Image <?= $isEditMode ? '(Optionnelle)' : '*' ?> (Max 5 Mo) :</label>
                    <input type="file" id="sliderImage" name="image" class="form-control" accept="image/jpeg, image/png, image/webp" <?= !$isEditMode ? 'required' : '' ?>>
                </div>
            </div>

            <?php if ($isEditMode && !empty($editSlider['image_path'])): ?>
                <div class="current-image-box mb-15">
                    <span class="image-label text-muted">Image actuelle :</span>
                    <img src="<?= URL . htmlspecialchars($editSlider['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Aperçu du slide" class="preview-thumb-medium">
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="sliderDescription">Description / Sous-titre :</label>
                <textarea id="sliderDescription" name="description" class="form-control" rows="3" placeholder="Saisir un court texte descriptif pour le slide..."><?= htmlspecialchars($editSlider['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
            </div>

            <div class="flex-end-container mt-20">
                <button type="submit" class="btn-admin-submit btn-wide" aria-label="Enregistrer les modifications du slide">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> 
                    <?= $isEditMode ? "Mettre à jour le slide" : "Ajouter au diaporama" ?>
                </button>
            </div>

        </form>
    </div>

    <?php if (!$isEditMode): ?>
    <form action="<?= URL ?>AdminSlider/delete" method="post" id="formSlidersSelection">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <?php if(!empty($sliders)): ?>
        <div class="mb-15 flex-end-container">
            <button type="button" id="btnDeleteSlider" class="btn-admin-danger" aria-label="Supprimer les slides cochés">
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer la sélection
            </button>
        </div>
        <?php endif; ?>

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des slides du diaporama">
                <thead>
                    <tr>
                        <th scope="col" style="width: 120px;" class="text-center">Aperçu</th>
                        <th scope="col">Titre & Lien</th>
                        <th scope="col" style="width: 80px;" class="text-center">Actions</th>
                        <th scope="col" style="width: 50px;" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner tous les slides">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($sliders)): foreach ($sliders as $row): ?>
                    <tr>
                        <td class="text-center">
                            <img src="<?= URL . htmlspecialchars($row['image_path'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Slide" class="table-img-preview" onerror="this.src='https://placehold.co/100x50/f1f3f5/3b5bdb?text=Slide'">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <div class="text-muted"><small><i class="fa-solid fa-link" aria-hidden="true"></i> <?= htmlspecialchars($row['link'] ?? '#', ENT_QUOTES, 'UTF-8'); ?></small></div>
                            <div class="help-text">Bouton : <?= htmlspecialchars($row['button_text'] ?? '', ENT_QUOTES, 'UTF-8'); ?> (Couleur: <?= htmlspecialchars($row['text_color'] ?? '', ENT_QUOTES, 'UTF-8'); ?>)</div>
                        </td>
                        <td class="text-center">
                            <a href="<?= URL ?>AdminSlider/edit/<?= (int)$row['id']; ?>" class="action-icon icon-edit" title="Modifier ce slide" aria-label="Modifier le slide <?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= (int)$row['id']; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner ce slide">
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="4" class="text-empty-table text-center">Aucun slide n'a été ajouté au diaporama.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
    <?php endif; ?>

</div>

<script src="<?= URL ?>public/assets/js/admin_slider.js" defer></script>