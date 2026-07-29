<?php 
// Récupération des données transmises par le contrôleur (Principe MVC)
$userInfo = $data['userInfo'] ?? [];
$userName = !empty($userInfo['username']) ? $userInfo['username'] : (!empty($userInfo['last_name']) ? $userInfo['last_name'] : 'Utilisateur');
$userEmail = $userInfo['email'] ?? '';
$userInitial = strtoupper(mb_substr($userName, 0, 1, 'UTF-8'));

$orders = $data['orders'] ?? [];
$totalOrdersCount = $data['totalOrdersCount'] ?? count($orders);
$totalSpent = $data['totalSpent'] ?? 0;
$latestOrder = $data['latestOrder'] ?? ($orders[0] ?? null);
?>

<div class="account-dashboard-wrapper" id="mainAccountDashboard" data-csrf="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    
    <aside class="account-sidebar">
        <div class="user-profile-summary">
            <div class="user-avatar"><?= htmlspecialchars($userInitial, ENT_QUOTES, 'UTF-8') ?></div>
            <h3 class="user-name"><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></h3>
            <span class="user-since">Client(e) de la boutique</span>
        </div>

        <ul class="account-nav-list">
            <li class="nav-item active" data-target="tabDashboard"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i> Tableau de bord</li>
            <li class="nav-item" data-target="tabOrders"><i class="fa-solid fa-box-open" aria-hidden="true"></i> Mes commandes</li>
            
            <li class="nav-item">
                <a href="<?= URL ?>Account/favorites" class="account-sidebar-link">
                    <i class="fa-solid fa-heart" aria-hidden="true"></i> Mes favoris
                </a>
            </li>
            
            <li class="nav-item" data-target="tabVouchers"><i class="fa-solid fa-ticket" aria-hidden="true"></i> Mes réductions</li>
            <li class="nav-item" data-target="tabInfo"><i class="fa-solid fa-user-pen" aria-hidden="true"></i> Mes informations</li>
            <li class="nav-separator"></li>
            <li><button type="button" class="nav-link-danger" id="btnOpenLogoutModal"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Déconnexion</button></li>
            <li><button type="button" class="nav-link-danger delete-account-btn" id="btnOpenDeleteModal"><i class="fa-solid fa-user-xmark" aria-hidden="true"></i> Supprimer le compte</button></li>
        </ul>
    </aside>

    <main class="account-main-content">
        
        <section id="tabDashboard" class="account-tab-content active">
            <div class="dashboard-header">
                <h2>Bienvenue, <?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?> !</h2>
                <p>Depuis votre <span>tableau de bord</span>, vous pouvez avoir un aperçu de vos activités récentes.</p>
            </div>

            <div class="dashboard-stats-row">
                <div class="stat-card">
                    <div class="stat-icon bg-blue-light"><i class="fa-solid fa-bag-shopping color-blue" aria-hidden="true"></i></div>
                    <div class="stat-details"><span class="stat-title">COMMANDES TOTALES</span><span class="stat-value"><?= (int)$totalOrdersCount ?></span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-green-light"><i class="fa-solid fa-wallet color-green" aria-hidden="true"></i></div>
                    <div class="stat-details"><span class="stat-title">TOTAL DES DÉPENSES</span><span class="stat-value"><?= number_format($totalSpent, 2, ',', ' ') ?> €</span></div>
                </div>
            </div>

            <div class="recent-order-section">
                <div class="section-title-row">
                    <h3>Votre dernière commande</h3>
                    <button class="link-btn" id="btnViewAllOrdersShortcut">Voir tout</button>
                </div>
                <?php if($latestOrder): ?>
                    <div class="recent-order-card modern-order-card-box">
                        <div class="recent-order-flex-row">
                            <div>
                                <strong class="order-ref-highlight">Commande #<?= (int)$latestOrder['id'] ?></strong><br>
                                <span class="order-date-label">Passée le <?= htmlspecialchars($latestOrder['created_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="order-amount-large"><?= number_format($latestOrder['total_amount'] ?? 0, 2, ',', ' ') ?> €</div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="recent-order-card empty-order">
                        <div class="order-info"><span class="order-number">Aucune commande récente.</span></div>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <section id="tabOrders" class="account-tab-content">
            <div class="dashboard-header">
                <h2>Mes commandes</h2>
                <p>Consultez l'historique et les détails de vos achats.</p>
            </div>
            <div class="account-table-wrapper">
                <table class="account-table" aria-label="Historique de vos commandes">
                    <thead>
                        <tr>
                            <th scope="col">Référence</th>
                            <th scope="col">Date</th>
                            <th scope="col">Montant</th>
                            <th scope="col">Statut</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($orders)): foreach($orders as $order): ?>
                        <tr>
                            <td><strong>#<?= (int)$order['id'] ?></strong></td>
                            <td><?= htmlspecialchars($order['created_date'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                            <td><strong><?= number_format($order['total_amount'] ?? 0, 2, ',', ' ') ?> €</strong></td>
                            <td>
                                <?php if(isset($order['is_paid']) && $order['is_paid'] == 1): ?>
                                    <span class="status-badge-paid">Payée</span>
                                <?php else: ?>
                                    <span class="status-badge-pending">En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn-view-order" data-id="<?= (int)$order['id'] ?>" title="Voir les détails de la commande" aria-label="Voir les détails de la commande #<?= (int)$order['id'] ?>">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i> Détails
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr><td colspan="5" class="text-empty-table text-center">Aucune commande récente.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section id="tabVouchers" class="account-tab-content">
            <div class="dashboard-header">
                <h2>Bons de réduction</h2>
                <p>Activez et gérez vos codes promotionnels.</p>
            </div>
            <div class="voucher-activation-box">
                <label for="voucherCode">Activer un bon de réduction :</label>
                <div class="input-group-flex">
                    <input type="text" id="voucherCode" class="form-control" placeholder="Saisir un code...">
                    <button type="button" id="btnActivateVoucher" class="btn-account-submit">Activer</button>
                </div>
            </div>
        </section>

        <section id="tabInfo" class="account-tab-content">
            <div class="dashboard-header">
                <h2>Mes informations</h2>
                <p>Gérer vos données personnelles, adresses et paramètres de sécurité.</p>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert-sticky success" role="alert" aria-live="polite">
                    <i class="fa-solid fa-circle-check" aria-hidden="true"></i> 
                    <span>
                        <?php
                        if($_GET['success'] === 'profile') echo "Vos coordonnées personnelles ont été mises à jour avec succès !";
                        if($_GET['success'] === 'password') echo "Votre mot de passe a été modifié et sécurisé avec succès !";
                        ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-sticky danger" role="alert" aria-live="assertive">
                    <i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> 
                    <span>
                        <?php
                        if($_GET['error'] === 'password') echo "Erreur : Le mot de passe actuel saisi est incorrect.";
                        if($_GET['error'] === 'password_mismatch') echo "Erreur : Les deux nouveaux mots de passe ne correspondent pas.";
                        if($_GET['error'] === 'delete') echo "Erreur : Mot de passe incorrect. Impossible de valider la suppression du compte.";
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <div class="profile-forms-grid">
                
                <div class="form-card">
                    <h3>Données personnelles</h3>
                    <form action="<?= URL ?>Account/saveProfile" method="post" autocomplete="off">
                        
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-group"><label for="profileUser">Pseudo *</label><input type="text" id="profileUser" name="username" class="form-control" value="<?= htmlspecialchars($userInfo['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required aria-required="true"></div>
                        <div class="form-group"><label for="profileName">Nom Complet</label><input type="text" id="profileName" name="last_name" class="form-control" value="<?= htmlspecialchars($userInfo['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
                        <div class="form-group"><label for="profileEmail">E-mail *</label><input type="email" id="profileEmail" name="email" class="form-control" value="<?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>" required aria-required="true" autocomplete="email"></div>
                        <div class="form-group"><label for="profileMobile">Mobile</label><input type="text" id="profileMobile" name="mobile" class="form-control" dir="ltr" value="<?= htmlspecialchars($userInfo['mobile'] ?? '', ENT_QUOTES, 'UTF-8') ?>" autocomplete="tel"></div>
                        <div class="form-group"><label for="profileAddress">Adresse</label><textarea id="profileAddress" name="address" class="form-control" rows="2"><?= htmlspecialchars($userInfo['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
                        <div class="form-group"><label for="profileCity">Ville</label><input type="text" id="profileCity" name="city" class="form-control" value="<?= htmlspecialchars($userInfo['city'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
                        <div class="form-group"><label for="profileZip">Code postal</label><input type="text" id="profileZip" name="postal_code" class="form-control" value="<?= htmlspecialchars($userInfo['postal_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>"></div>
                        
                        <button type="submit" class="btn-account-submit btn-account-submit-full">Mettre à jour le profil</button>
                    </form>
                </div>
                
                <div class="form-card">
                    <h3>Sécurité</h3>
                    <form action="<?= URL ?>Account/updatePassword" method="post" autocomplete="off">
                        
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

                        <div class="form-group">
                            <label for="passOld">Ancien mot de passe</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="passOld" name="pass_old" class="form-control" autocomplete="new-password" required aria-required="true">
                                <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="passNew">Nouveau mot de passe</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="passNew" name="pass_new" class="form-control" autocomplete="new-password" required aria-required="true">
                                <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="passConfirm">Confirmer le nouveau mot de passe</label>
                            <div class="password-input-wrapper">
                                <input type="password" id="passConfirm" name="pass_confirm" class="form-control" autocomplete="new-password" required aria-required="true">
                                <button type="button" class="toggle-password" aria-label="Afficher/Masquer le mot de passe"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                            </div>
                        </div>
                        <button type="submit" class="btn-password-submit btn-account-submit btn-account-submit-full">Changer le mot de passe</button>
                    </form>
                </div>

            </div>
        </section>

    </main>
</div>

<div id="darkOverlay" class="action-modal-overlay"></div>

<div id="logoutModal" class="action-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="logoutModalTitle">
    <div class="action-modal-box">
        <h4 id="logoutModalTitle" class="modal-warning-title"><i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Déconnexion</h4>
        <p class="modal-text">Êtes-vous sûr(e) de vouloir vous déconnecter de votre compte ? Vous devrez entrer vos identifiants lors de votre prochaine visite.</p>
        
        <div class="modal-actions">
            <button type="button" class="btn-account-secondary" id="btnCancelLogout">Annuler</button>
            <a href="<?= URL ?>Login/logout" class="btn-account-submit bg-danger-btn text-decoration-none-btn">Oui, me déconnecter</a>
        </div>
    </div>
</div>

<div id="deleteAccountModal" class="action-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
    <div class="action-modal-box">
        <h4 id="deleteModalTitle" class="modal-danger-title"><i class="fa-solid fa-triangle-exclamation" aria-hidden="true"></i> Suppression définitive</h4>
        <p class="modal-text">Cette action est irréversible. Toutes vos données, commandes et avantages seront perdus. Veuillez confirmer votre identité.</p>
        
        <form action="<?= URL ?>Account/deleteAccount" method="post" id="formDeleteAccount" autocomplete="off">
            
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

            <div class="form-group">
                <label for="deleteReason">Pourquoi souhaitez-vous nous quitter ? *</label>
                <select id="deleteReason" name="reason" class="form-control" required aria-required="true">
                    <option value="">Sélectionnez une raison...</option>
                    <option value="1">Je n'utilise plus ce compte</option>
                    <option value="2">Je reçois trop d'e-mails</option>
                    <option value="3">Je ne suis pas satisfait(e) du service</option>
                    <option value="4">Autre raison</option>
                </select>
            </div>
            
            <div class="form-group modal-spacing-top">
                <label for="deletePassword">Mot de passe pour confirmer * :</label>
                <div class="password-input-wrapper">
                    <input type="password" id="deletePassword" name="password" class="form-control" autocomplete="new-password" required aria-required="true">
                    <button type="button" class="toggle-password" aria-label="Afficher le mot de passe"><i class="fa-solid fa-eye" aria-hidden="true"></i></button>
                </div>
            </div>
            
            <div class="modal-actions modal-spacing-top">
                <button type="button" class="btn-account-secondary" id="btnCancelDelete">Annuler</button>
                <button type="submit" class="btn-account-submit bg-danger-btn">Confirmer la suppression</button>
            </div>
        </form>
    </div>
</div>

<div id="orderDetailsModal" class="action-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="orderModalTitle">
    <div class="action-modal-box modal-large">
        <div class="modal-header-flex">
            <h4 id="orderModalTitle" class="modal-title-custom"><i class="fa-solid fa-receipt" aria-hidden="true"></i> Détails de la commande <span id="modalOrderRef"></span></h4>
            <button type="button" class="btn-close-icon" id="btnCloseOrderModal" aria-label="Fermer la fenêtre"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        
        <div class="modal-body-scrollable">
            <div id="orderDetailsLoader" class="loader-spinner"><i class="fa-solid fa-spinner fa-spin" aria-hidden="true"></i> Chargement des données...</div>
            
            <div id="orderDetailsContent" class="display-none-box">
                
                <div class="order-info-grid">
                    <div class="info-block">
                        <span class="info-label">Date :</span>
                        <span class="info-value" id="modalOrderDate"></span>
                    </div>
                    <div class="info-block">
                        <span class="info-label">Statut :</span>
                        <span class="info-value" id="modalOrderStatus"></span>
                    </div>
                    <div class="info-block full-width">
                        <span class="info-label">Adresse de livraison :</span>
                        <span class="info-value" id="modalOrderAddress"></span>
                    </div>
                </div>

                <h5 class="sub-section-title">Articles achetés</h5>
                <div class="order-products-list" id="modalOrderProducts"></div>

                <div class="order-total-box">
                    <div class="total-line">
                        <span>Frais de livraison :</span>
                        <span id="modalOrderShipping"></span>
                    </div>
                    <div class="total-line grand-total">
                        <span>Montant Total Payé :</span>
                        <span id="modalOrderTotal"></span>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="<?= URL ?>public/assets/js/account.js" defer></script>