<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-comments" aria-hidden="true"></i> Modération des Commentaires
        </div>

        <div class="admin-actions">
            <select id="actionSelect" class="action-select-inline" aria-label="Sélectionner l'action de groupe">
                <option value="1">Approuver et Enregistrer</option>
                <option value="2">Rejeter (Masquer)</option>
                <option value="3">Supprimer définitivement</option>
            </select>
            <button type="button" class="btn-admin-primary" id="btnApplyAction" aria-label="Appliquer l'action aux commentaires sélectionnés">
                <i class="fa-solid fa-check-double" aria-hidden="true"></i> Appliquer l'action
            </button>
        </div>
    </header>

    <form id="formCommentsManage" action="" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des commentaires à modérer">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;" class="text-center">N°</th>
                        <th scope="col" style="width: 120px;">Date</th>
                        <th scope="col">Titre du commentaire</th>
                        <th scope="col">Points forts</th>
                        <th scope="col">Points faibles</th>
                        <th scope="col">Texte du commentaire</th>
                        <th scope="col" style="width: 130px;" class="text-center">Statut</th>
                        <th scope="col" style="width: 60px;" class="text-center">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner toutes les lignes">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    $comments = $data['comment'] ?? [];
                    if (!empty($comments)): 
                        foreach ($comments as $row): 
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold"><?= $i ?></td>
                        <td><?= htmlspecialchars($row['tarikh'] ?? $row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <td>
                            <input type="text" name="title_<?= (int)$row['id'] ?>" value="<?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-comment-small" aria-label="Modifier le titre du commentaire <?= $i ?>">
                        </td>
                        <td>
                            <input type="text" name="positive_points_<?= (int)$row['id'] ?>" value="<?= htmlspecialchars($row['positive_points'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-comment-small" aria-label="Modifier les points positifs <?= $i ?>">
                        </td>
                        <td>
                            <input type="text" name="negative_points_<?= (int)$row['id'] ?>" value="<?= htmlspecialchars($row['negative_points'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="input-comment-small" aria-label="Modifier les points faibles <?= $i ?>">
                        </td>
                        <td>
                            <textarea name="content_<?= (int)$row['id'] ?>" class="textarea-comment" rows="2" aria-label="Modifier le corps du commentaire <?= $i ?>"><?= htmlspecialchars($row['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </td>

                        <td class="text-center">
                            <?php if (isset($row['is_approved']) && $row['is_approved'] == 1): ?>
                                <span class="status-badge status-approved"><i class="fa-solid fa-check" aria-hidden="true"></i> Approuvé</span>
                            <?php else: ?>
                                <span class="status-badge status-rejected"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Masqué</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= (int)$row['id']; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner cette ligne pour la modération">
                        </td>
                    </tr>
                    <?php 
                        $i++; 
                        endforeach; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="8" class="text-empty-table">Aucun commentaire trouvé dans la base de données.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_comment.js" defer></script>