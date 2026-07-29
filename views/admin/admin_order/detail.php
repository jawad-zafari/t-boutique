<?php 
$orderInfo = $data['orderInfo'] ?? []; 
// SÉCURITÉ CRITIQUE : Prévention des attaques PHP Object Injection via unserialize
$cart = !empty($orderInfo['cart_data']) ? unserialize($orderInfo['cart_data'], ['allowed_classes' => false]) : [];
$orderId = (int)($orderInfo['id'] ?? 0);
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-circle-info" aria-hidden="true"></i> Commande <strong>#<?= $orderId ?></strong>
        </div>
        <div class="admin-actions">
            <a href="<?= URL ?>AdminOrder/showInvoice/<?= $orderId ?>" target="_blank" class="btn-admin-primary" aria-label="Imprimer la facture de la commande">
                <i class="fa-solid fa-print" aria-hidden="true"></i> Imprimer Facture
            </a>
            <a href="javascript:history.back()" id="btnBackHistory" class="btn-admin-back" aria-label="Retourner à la liste des commandes">
                <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Retour
            </a>
        </div>
    </header>

    <form id="formDetailOrder" method="post" action="<?= URL ?>AdminOrder/editOrder/<?= $orderId ?>">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="admin-grid-layout">
            
            <div class="admin-form-box">
                <h3 class="box-title"><i class="fa-solid fa-truck-ramping" aria-hidden="true"></i> Informations de livraison</h3>
                
                <div class="form-group">
                    <label for="orderAddress">Adresse de livraison :</label>
                    <textarea id="orderAddress" name="address" class="form-control" rows="3"><?= htmlspecialchars($orderInfo['address_data'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group half">
                        <label for="orderPostalCode">Code Postal :</label>
                        <input type="text" id="orderPostalCode" name="postal_code" class="form-control" value="<?= htmlspecialchars($orderInfo['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-group half">
                        <label for="orderPhone">Téléphone :</label>
                        <input type="text" id="orderPhone" name="phone" class="form-control" value="<?= htmlspecialchars($orderInfo['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="orderTracking">Code de suivi postal (Tracking) :</label>
                    <input type="text" id="orderTracking" name="tracking_code" class="form-control" value="<?= htmlspecialchars($orderInfo['tracking_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="ex: 1398745621">
                </div>
            </div>

            <div class="admin-form-box">
                <h3 class="box-title"><i class="fa-solid fa-slider" aria-hidden="true"></i> Statut & Notes internes</h3>

                <div class="form-group">
                    <label for="orderPayStatus">Statut du paiement :</label>
                    <select id="orderPayStatus" name="pay_status" class="form-control">
                        <option value="0" <?= (isset($orderInfo['is_paid']) && $orderInfo['is_paid'] == 0) ? 'selected' : '' ?>>Non payée (En attente)</option>
                        <option value="1" <?= (isset($orderInfo['is_paid']) && $orderInfo['is_paid'] == 1) ? 'selected' : '' ?>>Payée (Confirmée)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="orderStatusSelect">Statut de la commande :</label>
                    <select id="orderStatusSelect" name="order_status" class="form-control">
                        <?php if (!empty($data['order_status'])): foreach ($data['order_status'] as $status): ?>
                            <option value="<?= (int)$status['id'] ?>" <?= (isset($orderInfo['status_id']) && $orderInfo['status_id'] == $status['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['title'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="orderAdminNote">Note privée de l'administration :</label>
                    <textarea id="orderAdminNote" name="admin_note" class="form-control" rows="3" placeholder="Notes visibles uniquement par l'administrateur..."><?= htmlspecialchars($orderInfo['admin_note'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

        </div>

        <div class="admin-table-wrapper mt-20">
            <h3 class="box-title mb-15"><i class="fa-solid fa-basket-shopping" aria-hidden="true"></i> Articles commandés</h3>
            <table class="admin-table" aria-label="Liste des articles inclus dans la commande">
                <thead>
                    <tr>
                        <th scope="col">Produit</th>
                        <th scope="col">Couleur & Garantie</th>
                        <th scope="col" class="text-center">Quantité</th>
                        <th scope="col">Prix Unitaire</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($cart)): foreach ($cart as $row): 
                        $qty = (int)($row['quantity'] ?? $row['tedad'] ?? 1);
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong></td>
                        <td><?= htmlspecialchars($row['colorTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars($row['garanteeTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="text-center font-weight-bold"><?= $qty ?></td>
                        <td><?= number_format($row['price'] ?? 0, 2, ',', ' ') ?> €</td>
                        <td><strong><?= number_format(($row['price'] ?? 0) * $qty, 2, ',', ' ') ?> €</strong></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center text-empty-table">Aucun produit trouvé dans cette commande.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn-admin-submit btn-wide mt-20" aria-label="Sauvegarder les modifications apportées à la commande">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Mettre à jour la commande
        </button>

    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_order.js" defer></script>