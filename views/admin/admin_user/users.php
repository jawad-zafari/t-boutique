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
            <table class="admin-table" aria-label="Liste complète des utilisateurs">
                <thead>
                    <tr>
                        <th scope="col" style="width: 50px;" class="text-center">N°</th>
                        <th scope="col" style="width: 140px;">Date d'inscription</th>
                        <th scope="col">Nom et Prénom</th>
                        <th scope="col" style="width: 150px;">Téléphone (Mobile)</th>
                        <th scope="col" style="width: 160px;" class="text-center">Rôle (Niveau)</th>
                        <th scope="col" style="width: 60px;" class="text-center">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Tout sélectionner">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i = 1; 
                    $users = $data['users'] ?? [];
                    if (!empty($users)): 
                        foreach ($users as $row): 
                            // Récupération sécurisée du niveau de l'utilisateur (Par défaut : 3)
                            $roleId = (int)($row['level'] ?? 3); 
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold"><?= $i ?></td>
                        <td><?= htmlspecialchars($row['tarikh'] ?? $row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
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
                        <td colspan="6" class="text-empty-table text-center">Aucun utilisateur trouvé dans la base de données.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_user.js" defer></script>