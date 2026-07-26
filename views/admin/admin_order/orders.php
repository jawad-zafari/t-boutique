<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-box-open" aria-hidden="true"></i> Gestion des Commandes
        </div>
    </header>

    <form id="formOrdersSelection" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-actions-bulk">
            <div class="bulk-status-wrapper">
                <label for="bulkStatusSelect" class="sr-only">Sélectionner un statut pour l'action en masse</label>
                <select name="bulk_status_id" class="form-control select-bulk" id="bulkStatusSelect" aria-label="Sélectionner un statut pour l'action en masse">
                    <option value="">-- Action en masse : Modifier le statut --</option>
                    <?php if(!empty($data['statuses'])): foreach($data['statuses'] as $status): ?>
                        <option value="<?= (int)$status['id'] ?>"><?= htmlspecialchars($status['title'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; endif; ?>
                </select>
                <button type="submit" formaction="<?= URL ?>AdminOrder/bulkUpdateStatus" id="btnBulkUpdateStatus" class="btn-admin-primary" aria-label="Appliquer le nouveau statut aux commandes sélectionnées">
                    <i class="fa-solid fa-check" aria-hidden="true"></i> Appliquer
                </button>
            </div>
            
            <button type="submit" formaction="<?= URL ?>AdminOrder/delete" id="btnDeleteOrders" class="btn-admin-danger" aria-label="Supprimer les commandes sélectionnées">
                <i class="fa-solid fa-trash" aria-hidden="true"></i> Supprimer la sélection
            </button>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des commandes clients">
                <thead>
                    <tr>
                        <th scope="col" class="col-w-80">Réf</th>
                        <th scope="col">Date</th>
                        <th scope="col">Destinataire</th>
                        <th scope="col">Montant Total</th>
                        <th scope="col" class="text-center">Paiement</th>
                        <th scope="col" class="text-center">Statut Commande</th>
                        <th scope="col">Ville</th>
                        <th scope="col" class="text-center col-w-80">Détails</th>
                        <th scope="col" class="text-center col-w-80">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Tout sélectionner">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($data['orders'])): foreach ($data['orders'] as $row): 
                        $statusId = $row['status_id'] ?? 1;
                        $badgeClass = 'badge-default';
                        if ($statusId == 1) $badgeClass = 'badge-warning'; 
                        if ($statusId == 2) $badgeClass = 'badge-primary'; 
                        if ($statusId == 3) $badgeClass = 'badge-success'; 
                        if ($statusId == 4) $badgeClass = 'badge-danger';  
                        
                        $isPaid = $row['is_paid'] ?? 0;
                        $orderId = (int)$row['id'];
                    ?>
                    <tr>
                        <td><strong>#<?= $orderId; ?></strong></td>
                        <td><?= htmlspecialchars($row['created_date'] ?? $row['tarikh'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?= htmlspecialchars($row['last_name'] ?? $row['family'] ?? 'Client', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><strong><?= number_format($row['total_price'] ?? $row['amount'] ?? 0, 0, ',', ' '); ?> €</strong></td>
                        
                        <td class="text-center">
                            <span class="badge-payment <?= $isPaid == 1 ? 'paid' : 'unpaid' ?>">
                                <?= $isPaid == 1 ? 'Payé' : 'En attente' ?>
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="order-status-badge <?= $badgeClass ?>">
                                <?= htmlspecialchars($row['statusTitle'] ?? 'Nouveau', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </td>
                        
                        <td><?= htmlspecialchars($row['city'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                        
                        <td class="text-center">
                            <a href="<?= URL ?>AdminOrder/detail/<?= $orderId; ?>" class="action-icon icon-list" title="Gérer la commande" aria-label="Voir les détails de la commande <?= $orderId; ?>">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="id[]" value="<?= $orderId; ?>" class="admin-checkbox row-checkbox" aria-label="Sélectionner la commande <?= $orderId; ?>">
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr>
                        <td colspan="9" class="text-empty-table">Aucune commande trouvée dans la base de données.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_order.js" defer></script>