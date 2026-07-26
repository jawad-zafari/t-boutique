<?php 
$orderInfo = $data['orderInfo'] ?? []; 
$cart = !empty($orderInfo['cart_data']) ? unserialize($orderInfo['cart_data']) : [];

$subTotal = 0;
foreach ($cart as $row) {
    $qty = (int)($row['quantity'] ?? $row['tedad'] ?? 1);
    $subTotal += ($row['price'] ?? 0) * $qty;
}
$shippingPrice = $orderInfo['shipping_price'] ?? $orderInfo['post_price'] ?? 0;
$totalAmount = $orderInfo['total_price'] ?? $orderInfo['amount'] ?? 0;

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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= URL ?>public/assets/css/main.css">
</head>
<body class="facture-body">

<div class="invoice-controls">
    <button type="button" class="btn-print" id="btnPrintInvoice" aria-label="Imprimer ou sauvegarder la facture en PDF">
        <i class="fa-solid fa-print" aria-hidden="true"></i> Imprimer / Sauvegarder PDF
    </button>
</div>

<div class="invoice-box" role="document" aria-label="Facture de commande">
    
    <header class="invoice-header">
        <div class="brand">
            <h1>MA BOUTIQUE</h1>
            <p>123 Avenue des Champs-Élysées<br>75008 Paris, France<br>TVA: FR1234567890</p>
        </div>
        <div class="meta-info">
            <h2>FACTURE</h2>
            <p><strong>Réf :</strong> #<?= $orderId ?></p>
            <p><strong>Date :</strong> <?= htmlspecialchars($invoiceDate, ENT_QUOTES, 'UTF-8') ?></p>
            
            <div class="invoice-barcode" aria-hidden="true">
                <?php
                if (file_exists('public/barcode/BarcodeGenerator.php')) {
                    require_once('public/barcode/BarcodeGenerator.php');
                    if (file_exists('public/barcode/BarcodeGeneratorPNG.php')) {
                        require_once('public/barcode/BarcodeGeneratorPNG.php');
                        if (class_exists('\\Picqer\\Barcode\\BarcodeGeneratorPNG')) {
                            $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
                            $barcode = $generator->getBarcode((string)$orderId, $generator::TYPE_CODE_128);
                            echo '<img src="data:image/png;base64,' . base64_encode($barcode) . '" alt="Code barre de la facture">';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </header>

    <div class="invoice-addresses">
        <div class="address-block">
            <h3>Facturé à / Livré à :</h3>
            <strong><?= htmlspecialchars($orderInfo['last_name'] ?? $orderInfo['family'] ?? 'Client', ENT_QUOTES, 'UTF-8') ?></strong>
            <p><?= htmlspecialchars($orderInfo['address'] ?? $orderInfo['address_data'] ?? 'Adresse non renseignée', ENT_QUOTES, 'UTF-8') ?></p>
            <p><?= htmlspecialchars($orderInfo['postal_code'] ?? $orderInfo['code_posti'] ?? '', ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($orderInfo['city'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <p><?= htmlspecialchars($orderInfo['province'] ?? $orderInfo['ostan'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
            <p><i class="fa-solid fa-phone icon-small" aria-hidden="true"></i> <?= htmlspecialchars($orderInfo['phone'] ?? $orderInfo['tel'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        
        <div class="address-block">
            <h3>Détails de la commande :</h3>
            <p><strong>Méthode de livraison :</strong><br><?= htmlspecialchars($orderInfo['postTitle'] ?? 'Standard', ENT_QUOTES, 'UTF-8') ?></p>
            <p class="mt-15"><strong>Méthode de paiement :</strong><br><?= htmlspecialchars($orderInfo['payTypeTitle'] ?? 'Carte Bancaire', ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>ID Transaction :</strong> <?= htmlspecialchars($orderInfo['transaction_id'] ?? $orderInfo['beforepay'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        </div>
    </div>

    <table class="invoice-table" aria-label="Détails des produits facturés">
        <thead>
            <tr>
                <th scope="col" width="50%">Description du produit</th>
                <th scope="col" width="15%" class="text-center">Prix Unitaire</th>
                <th scope="col" width="15%" class="text-center">Qté</th>
                <th scope="col" width="20%" class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($cart)): foreach ($cart as $row): 
                $qty = (int)($row['quantity'] ?? $row['tedad'] ?? 1);
                $unitPrice = $row['price'] ?? 0;
            ?>
            <tr>
                <td>
                    <span class="item-title"><?= htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="item-desc">
                        <?= !empty($row['colorTitle']) ? 'Couleur: '.htmlspecialchars($row['colorTitle'], ENT_QUOTES, 'UTF-8') : '' ?>
                        <?= !empty($row['garanteeTitle']) ? ' | Gar: '.htmlspecialchars($row['garanteeTitle'], ENT_QUOTES, 'UTF-8') : '' ?>
                    </span>
                </td>
                <td class="text-center"><?= number_format($unitPrice, 2, ',', ' ') ?> €</td>
                <td class="text-center"><strong><?= $qty ?></strong></td>
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