<?php
// RÈGLE MVC : Les données sont préparées par le contrôleur et transmises via $data
$option = $data['option'] ?? [];
?>
<div class="admin-container">
    
    <header class="admin-header">
        <div class="admin-breadcrumb" aria-label="Fil d'Ariane">
            <i class="fa-solid fa-gears" aria-hidden="true"></i> Paramètres généraux du site
        </div>
    </header>

    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div class="alert-sticky success" role="alert" aria-live="polite">
            <i class="fa-solid fa-circle-check" aria-hidden="true"></i> La configuration a été mise à jour avec succès !
        </div>
    <?php endif; ?>

    <form action="<?= URL ?>AdminSetting/update" method="post" id="formSettingsManage">
        
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="settings-layout-grid">
            
            <div class="admin-form-box">
                <h3 class="settings-section-title">
                    <i class="fa-solid fa-globe" aria-hidden="true"></i> Général & Maintenance
                </h3>
                
                <div class="form-group mb-25">
                    <label class="toggle-switch" for="maintenanceModeCheckbox">
                        <input type="checkbox" id="maintenanceModeCheckbox" name="maintenance_mode" value="1" <?= (isset($option['maintenance_mode']) && $option['maintenance_mode'] == '1') ? 'checked' : '' ?> aria-describedby="maintenanceHelpText">
                        <span class="slider-toggle" aria-hidden="true"></span>
                        <span class="toggle-label text-danger">Activer le mode maintenance</span>
                    </label>
                    <span id="maintenanceHelpText" class="help-text">Bloque l'accès public au site pour vos clients pendant vos mises à jour techniques.</span>
                </div>

                <div class="form-group">
                    <label for="limitSlider">Limite de produits (Sliders d'accueil) * :</label>
                    <input type="number" id="limitSlider" name="limit_slider" class="form-control" value="<?= htmlspecialchars($option['limit_slider'] ?? '10', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true">
                </div>
            </div>

            <div class="admin-form-box">
                <h3 class="settings-section-title">
                    <i class="fa-solid fa-building" aria-hidden="true"></i> Informations de Contact
                </h3>
                
                <div class="form-group">
                    <label for="contactEmail">E-mail de support * :</label>
                    <input type="email" id="contactEmail" name="email" class="form-control" value="<?= htmlspecialchars($option['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" autocomplete="email" dir="ltr">
                </div>
                <div class="form-group">
                    <label for="contactTel">Téléphone * :</label>
                    <input type="text" id="contactTel" name="tel" class="form-control" value="<?= htmlspecialchars($option['tel'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" autocomplete="tel" dir="ltr">
                </div>
                <div class="form-group">
                    <label for="storeAddress">Adresse physique (Facturation) :</label>
                    <textarea id="storeAddress" name="store_address" class="form-control" rows="2" placeholder="Sera affichée sur les factures clients..."><?= htmlspecialchars($option['store_address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div class="admin-form-box">
                <h3 class="settings-section-title">
                    <i class="fa-solid fa-credit-card" aria-hidden="true"></i> Finances & Paiement
                </h3>
                
                <div class="form-row-double">
                    <div class="form-group">
                        <label for="taxPercent">TVA applicable (%) :</label>
                        <input type="number" id="taxPercent" name="tax_percent" class="form-control" value="<?= htmlspecialchars($option['tax_percent'] ?? '20', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-group">
                        <label for="shippingCost">Frais de port base (€) :</label>
                        <input type="number" step="0.01" id="shippingCost" name="shipping_cost" class="form-control" value="<?= htmlspecialchars($option['shipping_cost'] ?? '5.00', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="zarinpalMID">Clé API (Passerelle de paiement) :</label>
                    <input type="text" id="zarinpalMID" name="zarinpalMID" class="form-control" value="<?= htmlspecialchars($option['zarinpalMID'] ?? '', ENT_QUOTES, 'UTF-8') ?>" dir="ltr" placeholder="ex: sk_live_123abc...">
                </div>
            </div>

            <div class="admin-form-box">
                <h3 class="settings-section-title">
                    <i class="fa-solid fa-hashtag" aria-hidden="true"></i> Réseaux Sociaux
                </h3>
                
                <div class="form-group">
                    <label for="socialInstagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i> URL Instagram :</label>
                    <input type="url" id="socialInstagram" name="social_instagram" class="form-control" value="<?= htmlspecialchars($option['social_instagram'] ?? '', ENT_QUOTES, 'UTF-8') ?>" dir="ltr">
                </div>
                <div class="form-group">
                    <label for="socialFacebook"><i class="fa-brands fa-facebook" aria-hidden="true"></i> URL Facebook :</label>
                    <input type="url" id="socialFacebook" name="social_facebook" class="form-control" value="<?= htmlspecialchars($option['social_facebook'] ?? '', ENT_QUOTES, 'UTF-8') ?>" dir="ltr">
                </div>
            </div>

            <div class="admin-form-box grid-full-width">
                <h3 class="settings-section-title">
                    <i class="fa-solid fa-palette" aria-hidden="true"></i> Charte Graphique (UI)
                </h3>
                
                <div class="form-row-double">
                    <div class="form-group">
                        <label for="bodyColor">Couleur de fond globale :</label>
                        <input id="bodyColor" class="form-control" data-jscolor="{}" type="text" name="body_color" value="<?= htmlspecialchars($option['body_color'] ?? '#f4f6f9', ENT_QUOTES, 'UTF-8') ?>" dir="ltr">
                    </div>
                    <div class="form-group">
                        <label for="menuColor">Couleur principale du menu :</label>
                        <input id="menuColor" class="form-control" data-jscolor="{}" type="text" name="menu_color" value="<?= htmlspecialchars($option['menu_color'] ?? '#0033fe', ENT_QUOTES, 'UTF-8') ?>" dir="ltr">
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-25">
            <button type="submit" class="btn-admin-submit btn-wide w-100" aria-label="Sauvegarder et appliquer définitivement la configuration du site">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Mettre à jour la configuration
            </button>
        </div>

    </form>
</div>

<script src="<?= URL ?>public/assets/js/admin_setting.js" defer></script>