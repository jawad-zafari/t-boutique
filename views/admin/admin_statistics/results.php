<?php
$stat = $data['stat'] ?? [];
$result = $stat['result'] ?? [];
$totalOrders = count($result);
$paiedOrders = $stat['order_paied'] ?? 0;
$paiedPercentage = $totalOrders > 0 ? round(($paiedOrders / $totalOrders) * 100, 2) : 0;

// Calcul du chiffre d'affaires total de la période
$totalRevenue = 0;
foreach ($result as $row) {
    $totalRevenue += (int)($row['total_price'] ?? $row['amount'] ?? 0);
}
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-chart-pie" aria-hidden="true"></i> Statistiques du 
            <strong class="text-highlight"><?= htmlspecialchars($stat['startDate'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong> au 
            <strong class="text-highlight"><?= htmlspecialchars($stat['endDate'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
        <div class="admin-actions">
            <a href="#" class="btn-admin-back js-back-button" aria-label="Retourner au formulaire de filtre">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour aux filtres
            </a>
        </div>
    </header>

    <div class="stats-summary-grid">
        <div class="summary-card card-blue">
            <span class="summary-title">Total des commandes</span>
            <span class="summary-value"><?= $totalOrders ?></span>
        </div>
        <div class="summary-card card-green">
            <span class="summary-title">Commandes Payées</span>
            <span class="summary-value"><?= $paiedOrders ?></span>
        </div>
        <div class="summary-card card-orange">
            <span class="summary-title">Taux de conversion</span>
            <span class="summary-value"><?= $paiedPercentage ?>%</span>
        </div>
        <div class="summary-card card-purple">
            <span class="summary-title">Chiffre d'affaires</span>
            <span class="summary-value"><?= number_format($totalRevenue, 0, ',', ' ') ?> €</span>
        </div>
    </div>

    <div class="admin-table-wrapper">
        <table class="admin-table" aria-label="Résultats détaillés des commandes">
            <thead>
                <tr>
                    <th scope="col" style="width: 80px;">Réf</th>
                    <th scope="col">Date</th>
                    <th scope="col">Client / Destinataire</th>
                    <th scope="col">Montant Total</th>
                    <th scope="col" class="text-center">Statut Paiement</th>
                    <th scope="col">Ville</th>
                    <th scope="col" class="text-center" style="width: 80px;">Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($result)): foreach ($result as $row): 
                    $isPaid = (isset($row['is_paid']) && $row['is_paid'] == 1) ? 1 : 0;
                ?>
                <tr>
                    <td><strong>#<?= (int)$row['id']; ?></strong></td>
                    <td><?= htmlspecialchars($row['created_date'] ?? $row['tarikh'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?= htmlspecialchars($row['last_name'] ?? 'Client', ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><strong><?= number_format($row['total_price'] ?? $row['amount'] ?? 0, 0, ',', ' '); ?> €</strong></td>
                    
                    <td class="text-center">
                        <span class="badge-payment <?= $isPaid === 1 ? 'paid' : 'unpaid' ?>">
                            <?= $isPaid === 1 ? 'Payé' : 'Non Payé' ?>
                        </span>
                    </td>
                    
                    <td><?= htmlspecialchars($row['city'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
                    
                    <td class="text-center">
                        <a href="<?= URL ?>AdminOrder/detail/<?= (int)$row['id']; ?>" class="action-icon icon-list" title="Voir les détails de la commande" aria-label="Voir la commande <?= (int)$row['id']; ?>">
                            <i class="fa-solid fa-eye" aria-hidden="true"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="text-empty-table text-center">Aucune commande trouvée pour cette période.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

<script src="<?= URL ?>public/assets/js/admin_statistics.js" defer></script>