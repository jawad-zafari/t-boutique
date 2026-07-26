<?php
$orderInfo = $data['orderInfo'] ?? [];
$orderId = (int)($orderInfo['id'] ?? 0);
?>
<div class="payment-container container-medium">

    <div class="payment-section-header">
        <h2><i class="fa-solid fa-building-columns" aria-hidden="true"></i> Informations de virement bancaire</h2>
    </div>

    <form action="<?= URL ?>Checkout/bankTransfer/<?= $orderId ?>" method="post">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="info-box box-transparent border-warning">
            <p class="text-muted margin-bottom-md">Veuillez saisir les détails de votre virement ou transfert par carte bancaire ci-dessous :</p>

            <div class="form-grid">
                <div class="form-group">
                    <label for="creditcard">Numéro de carte / Compte :</label>
                    <input id="creditcard" name="creditcard" type="text" class="form-control" dir="ltr" aria-required="true" required>
                </div>

                <div class="form-group">
                    <label for="bank">Banque émettrice :</label>
                    <input id="bank" name="bank" type="text" class="form-control" aria-required="true" required>
                </div>
            </div>

            <div class="form-grid margin-top-md">
                <div class="form-group grid-full-width"> 
                    <label>Date du virement :</label>
                    <div class="date-select-group">
                        <select name="day" class="form-control" aria-label="Jour du virement">
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?= $i ?>"><?= sprintf("%02d", $i) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="month" class="form-control" aria-label="Mois du virement">
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>"><?= sprintf("%02d", $i) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="year" class="form-control" aria-label="Année du virement">
                            <?php 
                            $currentYear = (int)date('Y'); 
                            for ($i = $currentYear; $i >= $currentYear - 2; $i--): 
                            ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-actions-right">
            <button type="submit" class="btn-action">
                <i class="fa-solid fa-check" aria-hidden="true"></i> Enregistrer les informations
            </button>
        </div>

    </form>
</div>