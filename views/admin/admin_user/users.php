<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-users" aria-hidden="true"></i> Gestion des Utilisateurs
        </div>
        
        <div class="admin-actions">
            <label for="actionSelect" class="sr-only">Choisir une action en masse</label>
            <select id="actionSelect" class="form-control action-select-inline" aria-label="Action de groupe pour les utilisateurs">
                <option value="1">Promouvoir Administrateur (Niveau 1)</option>
                <option value="2">Modifier en Employé (Niveau 2)</option>
                <option value="3">Définir comme Utilisateur Normal (Niveau 3)</option>
                <option value="4">Supprimer le compte définitivement</option>
            </select>
            <button type="button" class="btn-admin-primary" id="btnApplyUserAction" aria-label="Appliquer l'action aux comptes sélectionnés">
                <i class="fa-solid fa-bolt" aria-hidden="true"></i> Appliquer
            </button>
        </div>
    </header>

    <form id="formUsersManage" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des utilisateurs enregistrés">
                <thead>
                    <tr>
                        <th scope="col" class="text-center col-id">N°</th>
                        <th scope="col">Nom & Prénom / Identifiant</th>
                        <th scope="col">Téléphone Mobile</th>
                        <th scope="col" class="text-center">Rôle actuel</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner tous les utilisateurs">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    $users = $data['users'] ?? [];
                    if (!empty($users)): 
                        foreach ($users as $row): 
                            $roleId = (int)($row['role_id'] ?? 3);
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold"><?= $i ?></td>
                        
                        <td><strong><?= htmlspecialchars($row['family'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        
                        <td><span dir="ltr"><?= htmlspecialchars($row['mobile'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
                        
                        <td class="text-center">
                            <span class="badge-role role-<?= $roleId ?>">
                                <?= htmlspecialchars($row['levelTitle'] ?? 'Utilisateur', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= (int)$row['id']; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner l'utilisateur <?= htmlspecialchars($row['family'] ?? 'Inconnu', ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                    </tr>
                    <?php 
                        $i++; 
                        endforeach; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="5" class="text-empty-table text-center">Aucun utilisateur trouvé dans la base de données.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_user.js" defer></script>