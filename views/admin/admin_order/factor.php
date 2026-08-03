<?php 
$orderInfo = $data['orderInfo'] ?? []; 
// SÉCURITÉ CRITIQUE : Prévention des attaques PHP Object Injection via unserialize
$cart = !empty($orderInfo['cart_data']) ? unserialize($orderInfo['cart_data'], ['allowed_classes' => false]) : [];

$subTotal = 0;
foreach ($cart as $row) {
    $qty = (int)($row['quantity'] ?? 1);
    $subTotal += ($row['price'] ?? 0) * $qty;
}
$shippingPrice = $orderInfo['shipping_price'] ?? $orderInfo['post_price'] ?? 0;
$totalAmount = $orderInfo['total_amount'] ?? $orderInfo['total_price'] ?? $orderInfo['amount'] ?? 0;

$invoiceDate = date('Y-m-d');
if (!empty($orderInfo['created_timestamp'])) {
    $invoiceDate = date('Y-m-d', $orderInfo['created_timestamp']);
} elseif (!empty($orderInfo['created_date'])) {
    $invoiceDate = $orderInfo['created_date'];
}
$orderId = (int)($orderInfo['id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <base href="<?= URL ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture N° <?= $orderId ?></title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= URL ?>public/assets/css/main.css">
</head>
<body class="invoice-body">

    <div class="invoice-actions-bar no-print">
        <button type="button" id="btnPrintInvoice" class="btn-admin-primary">
            <i class="fa-solid fa-print" aria-hidden="true"></i> Imprimer cette facture
        </button>
        <button type="button" id="btnBackHistory" class="btn-admin-back">
            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Fermer / Retour
        </button>
    </div>

    <div class="invoice-container">
        
        <header class="invoice-header">
            <div class="company-info">
                <h2>MA BOUTIQUE</h2>
                <p>123 Avenue du Commerce, 75000 Paris</p>
                <p>SIRET : 123 456 789 00012 | TVA : FR123456789</p>
                <p>Email : contact@maboutique.com</p>
            </div>
            <div class="invoice-details">
                <h1>FACTURE</h1>
                <p><strong>N° Facture :</strong> FACT-<?= $orderId ?></p>
                <p><strong>Date :</strong> <?= htmlspecialchars($invoiceDate, ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Mode de paiement :</strong> <?= htmlspecialchars($orderInfo['payTypeTitle'] ?? 'Carte Bancaire', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </header>

        <hr class="invoice-divider">

        <div class="invoice-addresses">
            <div class="address-box">
                <h4>Client & Adresse de livraison :</h4>
                <p><strong>Code client :</strong> #<?= (int)($orderInfo['user_id'] ?? 0) ?></p>
                <p><?= nl2br(htmlspecialchars($orderInfo['address_data'] ?? 'Adresse non spécifiée', ENT_QUOTES, 'UTF-8')) ?></p>
                <p><strong>Code Postal :</strong> <?= htmlspecialchars($orderInfo['postal_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($orderInfo['phone'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <table class="invoice-table" aria-label="Détail de la facture">
            <thead>
                <tr>
                    <th scope="col">Désignation du produit</th>
                    <th scope="col" class="text-center">Quantité</th>
                    <th scope="col" class="text-right">Prix Unitaire HT</th>
                    <th scope="col" class="text-right">Prix Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cart)): foreach ($cart as $row): 
                    $qty = (int)($row['quantity'] ?? 1);
                    $unitPrice = $row['price'] ?? 0;
                ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
                        <small class="text-muted"><?= htmlspecialchars($row['colorTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($row['garanteeTitle'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                    </td>
                    <td class="text-center"><?= $qty ?></td>
                    <td class="text-right"><?= number_format($unitPrice * 0.8, 2, ',', ' ') ?> €</td>
                    <td class="text-right"><strong><?= number_format($unitPrice * $qty, 2, ',', ' ') ?> €</strong></td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-center">Aucun produit détaillé trouvé.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="invoice-summary">
            <div class="summary-line">
                <span>Sous-total HT</span>
                <span><?= number_format($subTotal * 0.8, 2, ',', ' ') ?> €</span>
            </div>
            <div class="summary-line">
                <span>TVA (20%)</span>
                <span><?= number_format($subTotal * 0.2, 2, ',', ' ') ?> €</span>
            </div>
            <div class="summary-line">
                <span>Frais de livraison</span>
                <span><?= number_format($shippingPrice, 2, ',', ' ') ?> €</span>
            </div>
            <div class="summary-line total">
                <span>TOTAL TTC</span>
                <span><?= number_format($totalAmount, 2, ',', ' ') ?> €</span>
            </div>
        </div>

        <footer class="invoice-footer">
            <p><strong>Merci pour votre confiance !</strong></p>
            <p>Les produits livrés demeurent la propriété de MA BOUTIQUE jusqu'au paiement complet de la facture.</p>
            <p class="mt-10">Si vous avez des questions concernant cette facture, veuillez contacter notre support à contact@maboutique.com</p>
        </footer>

    </div>

    <script src="<?= URL ?>public/assets/js/admin_order.js" defer></script>
</body>
</html>