<?php
// Récupération sécurisée des données de la commande
$orderInfo = $data['orderInfo'] ?? [];

// Protection contre l'injection d'objets PHP via unserialize
$basketProducts = !empty($orderInfo['cart_data']) ? unserialize($orderInfo['cart_data'], ['allowed_classes' => false]) : [];

$creationTimestamp = $orderInfo['created_timestamp'] ?? 0;
$timeElapsed = time() - $creationTimestamp;
$maxDelay = (defined('MOHLAT_PAY') ? MOHLAT_PAY : 24) * 3600; 

// Logique des états
$isPaid = !empty($orderInfo['is_paid']); 
$isBankTransferPending = (!empty($orderInfo['pay_card_number']) && $orderInfo['payment_method_id'] == 2 && !$isPaid); 
$isSuccess = $isPaid || $isBankTransferPending; 
$isExpired = ($timeElapsed > $maxDelay && !$isSuccess); 

// Calculs financiers
$subTotal = 0;
foreach ($basketProducts as $item) {
    $subTotal += (float)($item['price'] ?? 0) * (int)($item['tedad'] ?? $item['quantity'] ?? 1);
}
$shippingCost = (float)($orderInfo['shipping_price'] ?? $orderInfo['post_price'] ?? 0);
$totalPayable = (float)($orderInfo['total_amount'] ?? $orderInfo['amount'] ?? ($subTotal + $shippingCost));
?>

<div class="checkout-modern-container">

    <?php if($isExpired): ?>
        <div class="elegant-alert-banner error" role="alert">
            <div class="banner-icon"><i class="fa-solid fa-circle-xmark" aria-hidden="true"></i></div>
            <div class="banner-content">
                <h2>Commande expirée</h2>
                <p>Le délai maximum de paiement de <?= defined('MOHLAT_PAY') ? MOHLAT_PAY : 24 ?> heures a été dépassé. Veuillez renouveler votre commande.</p>
            </div>
        </div>

    <?php elseif($isSuccess): ?>
        <div class="elegant-alert-banner success" role="alert">
            <div class="banner-icon"><i class="fa-solid fa-circle-check" aria-hidden="true"></i></div>
            <div class="banner-content">
                <?php if($isPaid): ?>
                    <h2>Commande n° #<?= (int)($orderInfo['id'] ?? 0) ?> payée avec succès !</h2>
                    <p>Votre paiement a été confirmé. N° de transaction : <strong><?= htmlspecialchars($orderInfo['transaction_id_after'] ?? $orderInfo['transaction_id_before'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></p>
                <?php else: ?>
                    <h2>Règlement par virement enregistré</h2>
                    <p>Vos coordonnées bancaires ont été transmises. Votre commande sera expédiée après vérification du virement par notre équipe.</p>
                <?php endif; ?>
            </div>
            <div class="banner-action">
                <a href="<?= URL ?>Account/index" class="btn-outline-success"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Voir dans mon compte</a>
            </div>
        </div>

    <?php else: ?>
        <div class="elegant-alert-banner info" role="alert">
            <div class="banner-icon"><i class="fa-solid fa-circle-info" aria-hidden="true"></i></div>
            <div class="banner-content">
                <h2>Commande n° #<?= (int)($orderInfo['id'] ?? 0) ?> enregistrée</h2>
                <p>Veuillez choisir un moyen de paiement ci-dessous pour valider définitivement votre commande.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="invoice-header-bar">
        <div class="invoice-ref-box">
            <span class="ref-label">Référence Commande :</span>
            <strong class="ref-code"><?= htmlspecialchars($orderInfo['barcode'] ?? ('ORD-' . ($orderInfo['id'] ?? '')), ENT_QUOTES, 'UTF-8') ?></strong>
        </div>
        <div class="invoice-date-box">
            <i class="fa-regular fa-calendar-check" aria-hidden="true"></i> Date : <?= htmlspecialchars($orderInfo['created_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>

    <div class="checkout-grid margin-top-md">
        
        <div class="checkout-left-column">
            
            <div class="checkout-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-boxes-packing" aria-hidden="true"></i> Articles de la commande
                </div>

                <div class="invoice-products-grid">
                    <?php foreach ($basketProducts as $item): 
                        $qty = (int)($item['tedad'] ?? $item['quantity'] ?? 1);
                        $unitPrice = (float)($item['price'] ?? 0);
                        $productId = (int)($item['id'] ?? 0);
                        
                        // Récupération sécurisée du chemin de l'image
                        $imgSrc = !empty($item['image']) ? (URL . $item['image']) : (URL . 'public/images/products/' . $productId . '/product_thumb.jpg');
                    ?>
                    <div class="invoice-product-row">
                        <div class="product-thumb-container">
                            <img src="<?= htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8') ?>" 
                                 alt="<?= htmlspecialchars($item['title'] ?? 'Produit', ENT_QUOTES, 'UTF-8') ?>" 
                                 class="product-invoice-img"
                                 onerror="this.src='https://placehold.co/80x80/f1f3f5/3b5bdb?text=Produit'">
                        </div>
                        <div class="product-details-container">
                            <h4 class="product-title-text"><?= htmlspecialchars($item['title'] ?? 'Article', ENT_QUOTES, 'UTF-8') ?></h4>
                            <div class="product-meta-text">
                                Quantité : <strong>x<?= $qty ?></strong> | Prix unitaire : <?= number_format($unitPrice, 2, ',', ' ') ?> €
                            </div>
                        </div>
                        <div class="product-total-price">
                            <?= number_format($unitPrice * $qty, 2, ',', ' ') ?> €
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="checkout-card margin-top-md">
                <div class="card-header-title">
                    <i class="fa-solid fa-truck-ramp-box" aria-hidden="true"></i> Informations de livraison
                </div>
                
                <div class="checkout-form-readonly">
                    <div class="form-group">
                        <label>Destinataire</label>
                        <div class="readonly-input"><?= htmlspecialchars($orderInfo['last_name'] ?? $orderInfo['family'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Adresse de livraison</label>
                        <div class="readonly-input multiline"><?= htmlspecialchars($orderInfo['address_data'] ?? $orderInfo['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
                    </div>

                    <div class="form-row-double">
                        <div class="form-group">
                            <label>Ville & Code Postal</label>
                            <div class="readonly-input"><?= htmlspecialchars($orderInfo['city'] ?? '', ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($orderInfo['postal_code'] ?? $orderInfo['code_posti'] ?? '', ENT_QUOTES, 'UTF-8') ?>)</div>
                        </div>
                        <div class="form-group">
                            <label>Téléphone / Mobile</label>
                            <div class="readonly-input"><span dir="ltr"><?= htmlspecialchars($orderInfo['mobile'] ?? $orderInfo['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?></span></div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(!$isExpired && !$isSuccess): ?>
            <div class="checkout-card margin-top-md">
                <div class="card-header-title">
                    <i class="fa-solid fa-credit-card" aria-hidden="true"></i> Mode de règlement
                </div>
                
                <form id="paymentMethodsForm" method="POST" action="<?= URL ?>Checkout/payOnline/<?= htmlspecialchars((string)($orderInfo['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                    <div class="payment-methods-grid" id="paymentMethodsContainer">
                        <label class="payment-method-option active">
                            <input type="radio" name="payment_choice" value="online" checked 
                                   data-url="<?= URL ?>Checkout/payOnline/<?= htmlspecialchars((string)($orderInfo['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <span class="method-content">
                                <span class="radio-custom"></span>
                                <i class="fa-regular fa-credit-card method-icon" aria-hidden="true"></i>
                                <span class="method-text">Paiement par carte en ligne (Stripe)</span>
                            </span>
                        </label>

                        <label class="payment-method-option">
                            <input type="radio" name="payment_choice" value="transfer" 
                                   data-url="<?= URL ?>Checkout/bankTransfer/<?= htmlspecialchars((string)($orderInfo['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                            <span class="method-content">
                                <span class="radio-custom"></span>
                                <i class="fa-solid fa-money-bill-transfer method-icon" aria-hidden="true"></i>
                                <span class="method-text">Virement / Transfert bancaire</span>
                            </span>
                        </label>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        </div>

        <div class="checkout-right-column">
            <div class="checkout-summary-card">
                <div class="card-header-title">
                    <i class="fa-solid fa-receipt" aria-hidden="true"></i> Récapitulatif financier
                </div>

                <div class="summary-totals">
                    <div class="summary-line">
                        <span class="label">Sous-total articles</span>
                        <span class="value"><?= number_format($subTotal, 2, ',', ' ') ?> €</span>
                    </div>
                    <div class="summary-line">
                        <span class="label">Frais de livraison</span>
                        <span class="value <?= $shippingCost == 0 ? 'text-success font-weight-bold' : '' ?>">
                            <?= $shippingCost == 0 ? 'Offert' : number_format($shippingCost, 2, ',', ' ') . ' €' ?>
                        </span>
                    </div>
                    
                    <div class="summary-line total-line">
                        <span class="label">Total TTC</span>
                        <span class="value highlight-total"><?= number_format($totalPayable, 2, ',', ' ') ?> €</span>
                    </div>
                </div>

                <?php if(!$isExpired && !$isSuccess): ?>
                <button type="button" id="btnConfirmPayment" class="btn-checkout-massive margin-top-md" aria-label="Confirmer et procéder au paiement">
                    Confirmer et Payer <i class="fa-solid fa-lock" aria-hidden="true"></i>
                </button>
                <div class="secure-badge margin-top-sm">
                    <i class="fa-solid fa-shield-halved" aria-hidden="true"></i> Transaction 100% sécurisée et cryptée
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
</div>

<script src="<?= URL ?>public/assets/js/payment.js" defer></script>