<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-circle-question" aria-hidden="true"></i> Gestion des Questions & Réponses
        </div>
        <div class="admin-actions">
            <select id="actionSelect" class="form-control action-select-inline" aria-label="Sélectionner l'action de groupe">
                <option value="1">Approuver et Enregistrer</option>
                <option value="2">Rejeter (Masquer)</option>
                <option value="3">Supprimer définitivement</option>
            </select>
            <button type="button" class="btn-admin-primary" id="btnApplyAction" aria-label="Appliquer l'action aux questions sélectionnées">
                <i class="fa-solid fa-check-double" aria-hidden="true"></i> Appliquer l'action
            </button>
        </div>
    </header>

    <form id="formQuestionsManage" action="" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des questions des utilisateurs">
                <thead>
                    <tr>
                        <th scope="col" class="text-center col-id">N°</th>
                        <th scope="col">Date</th>
                        <th scope="col">Question de l'utilisateur</th>
                        <th scope="col">Réponse de l'administrateur</th>
                        <th scope="col" class="text-center">Statut</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner tout">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    $questions = $data['questions'] ?? [];
                    if (!empty($questions)): 
                        foreach ($questions as $row): 
                            $qId = (int)$row['id'];
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold"><?= $i ?></td>
                        <td><?= htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <td>
                            <textarea name="question_<?= $qId ?>" class="form-control textarea-small" rows="2" aria-label="Question <?= $i ?>"><?= htmlspecialchars($row['content'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </td>
                        
                        <td>
                            <textarea name="answer_<?= $qId ?>" class="form-control textarea-small" rows="2" placeholder="Saisir la réponse..." aria-label="Réponse de l'admin pour la question <?= $i ?>"><?= htmlspecialchars($row['admin_answer'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </td>

                        <td class="text-center">
                            <?php if (isset($row['is_approved']) && $row['is_approved'] == 1): ?>
                                <span class="status-badge status-approved"><i class="fa-solid fa-check" aria-hidden="true"></i> Approuvé</span>
                            <?php else: ?>
                                <span class="status-badge status-rejected"><i class="fa-solid fa-xmark" aria-hidden="true"></i> Masqué</span>
                            <?php endif; ?>
                        </td>

                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= $qId ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner la ligne <?= $i ?>">
                        </td>
                    </tr>
                    <?php 
                        $i++; 
                        endforeach; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="6" class="text-empty-table text-center">Aucune question trouvée.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_question.js" defer></script>