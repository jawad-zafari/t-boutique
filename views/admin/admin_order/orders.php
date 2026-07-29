<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-box-open" aria-hidden="true"></i> Gestion des Commandes
        </div>
    </header>

    <form id="formOrdersSelection" method="post" action="<?= URL ?>AdminOrder/bulkUpdateStatus">
        
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
                <button type="submit" id="btnBulkUpdateStatus" class="btn-admin-primary" aria-label="Appliquer le statut aux commandes sélectionnées">
                    <i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i> Mettre à jour
                </button>
            </div>

            <button type="button" formaction="<?= URL ?>AdminOrder/delete" id="btnDeleteSelectedOrders" class="btn-admin-danger" aria-label="Supprimer les commandes sélectionnées">
                <i class="fa-solid fa-trash-can" aria-hidden="true"></i> Supprimer la sélection
            </button>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table" aria-label="Liste des commandes reçues">
                <thead>
                    <tr>
                        <th scope="col" class="col-id">N°</th>
                        <th scope="col">Date de commande</th>
                        <th scope="col">Référence Transaction</th>
                        <th scope="col">Montant Total</th>
                        <th scope="col" class="text-center">Paiement</th>
                        <th scope="col" class="text-center">Statut</th>
                        <th scope="col">Ville</th>
                        <th scope="col" class="text-center col-action">Détails</th>
                        <th scope="col" class="text-center col-checkbox">
                            <input type="checkbox" id="selectAllCheckboxes" class="admin-checkbox" aria-label="Sélectionner toutes les commandes">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $orders = $data['orders'] ?? [];
                    if (!empty($orders)): 
                        foreach ($orders as $row): 
                            $orderId = (int)($row['id'] ?? 0);
                            $isPaid = isset($row['is_paid']) && $row['is_paid'] == 1;
                            $badgeClass = $isPaid ? 'badge-success' : 'badge-warning';
                    ?>
                    <tr>
                        <td class="col-id"><strong>#<?= $orderId; ?></strong></td>
                        <td><?= htmlspecialchars($row['created_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><code><?= htmlspecialchars($row['transaction_id'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></code></td>
                        <td><strong><?= number_format($row['total_amount'] ?? 0, 2, ',', ' '); ?> €</strong></td>
                        
                        <td class="text-center">
                            <?php if ($isPaid): ?>
                                <span class="status-badge status-paid"><i class="fa-solid fa-check" aria-hidden="true"></i> Payée</span>
                            <?php else: ?>
                                <span class="status-badge status-pending"><i class="fa-solid fa-clock" aria-hidden="true"></i> En attente</span>
                            <?php endif; ?>
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