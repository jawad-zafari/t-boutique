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
                elseif($_GET['success'] === 'edit') echo "Le slide a été mis à jour avec succès !";
                elseif($_GET['success'] === 'delete') echo "Le slide a été supprimé avec succès !";
                else echo "L'opération a été effectuée avec succès !";
                ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="admin-form-box form-box-full">
        <form action="<?= URL ?>AdminSlider/save/<?= $sId ?>" method="post" enctype="multipart/form-data" id="formSliderManage">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="slider-form-grid">
                
                <div class="form-group">
                    <label for="slideTitle">Titre du slide * :</label>
                    <input type="text" id="slideTitle" name="title" class="form-control" value="<?= htmlspecialchars($editSlider['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" placeholder="Ex: Offres Spéciales d'Été">
                </div>

                <div class="form-group">
                    <label for="slideLink">Lien de redirection (URL) :</label>
                    <input type="url" id="slideLink" name="link" class="form-control" value="<?= htmlspecialchars($editSlider['link'] ?? '', ENT_QUOTES, 'UTF-8') ?>" dir="ltr" placeholder="https://exemple.fr/produit">
                </div>

                <div class="form-group">
                    <label for="slideButtonText">Texte du bouton d'action :</label>
                    <input type="text" id="slideButtonText" name="button_text" class="form-control" value="<?= htmlspecialchars($editSlider['button_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Ex: Découvrir maintenant">
                </div>

                <div class="form-group">
                    <label for="slideTextColor">Couleur du texte & bouton :</label>
                    <div class="color-picker-wrapper">
                        <input type="color" id="slideTextColor" name="text_color" class="color-input" value="<?= htmlspecialchars($editSlider['text_color'] ?? '#ffffff', ENT_QUOTES, 'UTF-8') ?>">
                        <span class="color-hint">Sélectionnez la couleur visuelle.</span>
                    </div>
                </div>

                <div class="form-group grid-full-width">
                    <label for="slideDescription">Description du slide :</label>
                    <textarea id="slideDescription" name="description" class="form-control" rows="3" placeholder="Saisir un court résumé descriptif..."><?= htmlspecialchars($editSlider['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-group grid-full-width">
                    <label for="slideImage">Image de bannière <?= $isEditMode ? '' : '*' ?> (Format recommandé : 1200x420px) :</label>
                    <input type="file" id="slideImage" name="image" class="form-control" accept="image/jpeg, image/png, image/webp" <?= $isEditMode ? '' : 'required' ?>>
                    
                    <?php if ($isEditMode && !empty($editSlider['image_path'])): ?>
                        <div class="current-image-preview">
                            <img src="<?= URL . htmlspecialchars($editSlider['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="Aperçu actuel">
                            <span class="badge-current">Image Actuelle</span>
                        </div>
                    <?php endif; ?>

                    <div id="liveImagePreview" class="live-preview-container" style="display: none;">
                        <img id="previewImgElement" src="#" alt="Aperçu du nouveau fichier">
                    </div>
                </div>
            </div>

            <div class="mt-25 flex-end-container">
                <button type="submit" class="btn-admin-submit btn-wide" aria-label="Enregistrer le slide">
                    <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> <?= $isEditMode ? 'Mettre à jour le slide' : 'Enregistrer le slide' ?>
                </button>
            </div>
        </form>
    </div>

    <?php if (!$isEditMode): ?>
    <form id="formSlidersManage" action="<?= URL ?>AdminSlider/delete" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        
        <div class="slider-list-header">
            <h3 class="list-title">
                <i class="fa-solid fa-list" aria-hidden="true"></i> Liste des bannières actives
            </h3>
            <button type="button" id="btnDeleteSliders" class="btn-admin-danger" aria-label="Supprimer les slides sélectionnés">
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer la sélection
            </button>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des slides enregistrés">
                <thead>
                    <tr>
                        <th scope="col" style="width: 120px;" class="text-center">Image</th>
                        <th scope="col">Titre / Description</th>
                        <th scope="col" style="width: 80px;" class="text-center">Modifier</th>
                        <th scope="col" style="width: 60px;" class="text-center">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Tout sélectionner">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sliders)): foreach ($sliders as $row): ?>
                    <tr>
                        <td class="text-center">
                            <img src="<?= URL . htmlspecialchars($row['image_path'] ?? '', ENT_QUOTES, 'UTF-8') ?>" alt="Slide" class="slider-preview-img" onerror="this.src='https://placehold.co/100x60/f1f3f5/3b5bdb?text=Slide'">
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                            <?php if(!empty($row['description'])): ?>
                                <p class="help-text"><?= htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <?php endif; ?>
                            <div class="help-text"><strong>Lien :</strong> <a href="<?= htmlspecialchars($row['link'] ?? '#', ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?= htmlspecialchars($row['link'] ?? '#', ENT_QUOTES, 'UTF-8'); ?></a></div>
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