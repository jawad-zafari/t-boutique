<?php 
$orderInfo = $data['orderInfo'] ?? []; 
$cart = !empty($orderInfo['cart_data']) ? unserialize($orderInfo['cart_data']) : [];
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
        
        <div class="order-detail-grid">
            
            <div class="order-info-card">
                <h3><i class="fa-solid fa-credit-card" aria-hidden="true"></i> Paiement & Statut</h3>
                
                <div class="form-group">
                    <label for="orderStatus">Statut de la commande :</label>
                    <select id="orderStatus" name="order_status" class="form-control" aria-label="Modifier le statut de la commande">
                        <?php if(!empty($data['order_status'])): foreach ($data['order_status'] as $status): ?>
                            <option value="<?= (int)$status['id'] ?>" <?= (isset($orderInfo['status_id']) && $orderInfo['status_id'] == $status['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status['title'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payStatus">Statut du paiement :</label>
                    <select id="payStatus" name="pay_status" class="form-control" aria-label="Modifier le statut du paiement">
                        <option value="0" <?= (isset($orderInfo['is_paid']) && $orderInfo['is_paid'] == 0) ? 'selected' : '' ?>>En attente de paiement</option>
                        <option value="1" <?= (isset($orderInfo['is_paid']) && $orderInfo['is_paid'] == 1) ? 'selected' : '' ?>>Payé avec succès</option>
                    </select>
                </div>

                <div class="info-row">
                    <span class="info-label">Méthode de paiement :</span>
                    <span class="info-value"><?= htmlspecialchars($orderInfo['payTypeTitle'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Transaction ID :</span>
                    <span class="info-value"><?= htmlspecialchars($orderInfo['transaction_id'] ?? $orderInfo['beforepay'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>

            <div class="order-info-card">
                <h3><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Adresse de Livraison</h3>
                
                <div class="form-group">
                    <label for="recipientName">Destinataire :</label>
                    <input type="text" id="recipientName" class="form-control control-disabled" value="<?= htmlspecialchars($orderInfo['last_name'] ?? $orderInfo['family'] ?? '', ENT_QUOTES, 'UTF-8') ?>" disabled aria-label="Nom du destinataire (lecture seule)">
                </div>
                <div class="form-group">
                    <label for="deliveryAddress">Adresse complète :</label>
                    <input id="deliveryAddress" name="address" type="text" class="form-control" value="<?= htmlspecialchars($orderInfo['address'] ?? $orderInfo['address_data'] ?? '', ENT_QUOTES, 'UTF-8') ?>" aria-label="Modifier l'adresse de livraison">
                </div>
                
                <div class="form-row-double">
                    <div class="form-group">
                        <label for="postalCode">Code Postal :</label>
                        <input id="postalCode" name="postal_code" type="text" class="form-control" value="<?= htmlspecialchars($orderInfo['postal_code'] ?? $orderInfo['code_posti'] ?? '', ENT_QUOTES, 'UTF-8') ?>" aria-label="Modifier le code postal">
                    </div>
                    <div class="form-group">
                        <label for="phoneNumber">Téléphone :</label>
                        <input id="phoneNumber" name="phone" type="text" class="form-control" value="<?= htmlspecialchars($orderInfo['phone'] ?? $orderInfo['tel'] ?? '', ENT_QUOTES, 'UTF-8') ?>" aria-label="Modifier le numéro de téléphone">
                    </div>
                </div>
            </div>

            <div class="order-info-card full-width-card">
                <h3><i class="fa-solid fa-truck-fast" aria-hidden="true"></i> Logistique & Notes internes</h3>
                
                <div class="form-row-double">
                    <div class="form-group">
                        <label for="shippingMethod">Mode de livraison sélectionné :</label>
                        <input type="text" id="shippingMethod" class="form-control control-disabled" value="<?= htmlspecialchars($orderInfo['postTitle'] ?? 'Standard', ENT_QUOTES, 'UTF-8') ?>" disabled aria-label="Mode de livraison (lecture seule)">
                    </div>
                    <div class="form-group">
                        <label for="trackingCode">Numéro de suivi (Tracking) :</label>
                        <input id="trackingCode" name="tracking_code" type="text" class="form-control" placeholder="Ex: 8H0123456789" value="<?= htmlspecialchars($orderInfo['tracking_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" aria-label="Saisir le numéro de suivi du colis">
                    </div>
                </div>
                
                <div class="form-group mt-15">
                    <label for="adminNote">Notes internes de l'administrateur (Privé) :</label>
                    <textarea id="adminNote" name="admin_note" class="form-control" rows="3" placeholder="Ajoutez une note privée concernant cette commande..." aria-label="Notes internes (non visibles par le client)"><?= htmlspecialchars($orderInfo['admin_note'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

        </div>

        <div class="admin-table-wrapper mb-20">
            <table class="admin-table" aria-label="Liste des produits de la commande">
                <thead>
                    <tr>
                        <th scope="col">Produit</th>
                        <th scope="col">Options</th>
                        <th scope="col" class="text-center">Qté</th>
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
                        <td><?= number_format($row['price'] ?? 0, 0, ',', ' ') ?> €</td>
                        <td><strong><?= number_format(($row['price'] ?? 0) * $qty, 0, ',', ' ') ?> €</strong></td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr><td colspan="5" class="text-center text-empty-table">Aucun produit trouvé.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <button type="submit" class="btn-admin-submit btn-wide" aria-label="Sauvegarder les modifications apportées à la commande">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Mettre à jour la commande
        </button>

    </form>
</div>