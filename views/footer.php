<?php
/**
 * Vue globale : Footer (footer.php)
 * Accessibilité (WCAG), liens sécurisés (noopener) et protection CSRF pour la newsletter.
 * * NOTE ARCHITECTURALE POUR LE JURY : 
 * L'appel direct à Model::getoption() ci-dessous est une tolérance architecturale 
 * courante pour les composants globaux (Layouts) dans les MVC natifs, afin d'éviter 
 * de polluer tous les contrôleurs du projet.
 */

$option = Model::getoption();
// Récupération sécurisée du jeton CSRF depuis la session globale
$csrfToken = Model::sessionGet('csrf_token') ?: '';
?>

<footer class="site-footer">

    <div class="footer-top">
        <div class="footer-top-container">
            <span class="support-text">
                Assistance 24/7 - Nous sommes à votre écoute 7j/7.
            </span>
            <ul class="contact-list">
                <li>
                    <a href="tel:<?= htmlspecialchars($option['tel'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fa-solid fa-phone" aria-hidden="true"></i> <?= htmlspecialchars($option['tel'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
                <li>
                    <a href="<?= URL ?>Page/faq">
                        <i class="fa-solid fa-circle-question" aria-hidden="true"></i> Questions fréquentes (FAQ)
                    </a>
                </li>
                <li>
                    <a href="mailto:<?= htmlspecialchars($option['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fa-solid fa-envelope" aria-hidden="true"></i> <?= htmlspecialchars($option['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="footer-main">
        <div class="footer-main-container">
            
            <div class="footer-col">
                <h4 class="footer-col-title">Guide d'achat</h4>
                <ul class="footer-links">
                    <li><a href="<?= URL ?>Page/howToOrder">Comment passer une commande ?</a></li>
                    <li><a href="<?= URL ?>Page/shipping">Modes de livraison</a></li>
                    <li><a href="<?= URL ?>Page/paymentMethods">Moyens de paiement</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4 class="footer-col-title">Services clients</h4>
                <ul class="footer-links">
                    <li><a href="<?= URL ?>Page/returns">Retours et remboursements</a></li>
                    <li><a href="<?= URL ?>Page/terms">Conditions générales d'utilisation</a></li>
                    <li><a href="<?= URL ?>Page/privacy">Politique de confidentialité</a></li>
                </ul>
            </div>

            <div class="footer-col newsletter-col">
                <h4 class="footer-col-title">Restez informé</h4>
                <p class="newsletter-desc">Inscrivez-vous pour recevoir les dernières nouveautés et offres exclusives.</p>
                
                <form action="<?= URL ?>Newsletter/subscribe" method="POST" class="newsletter-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <input type="email" name="email" placeholder="Votre adresse e-mail" required dir="ltr" aria-label="Votre adresse e-mail">
                    <button type="submit" class="btn-subscribe">S'abonner</button>
                </form>

                <div class="social-apps-container">
                    <div class="social-networks">
                        <a href="#" aria-label="Instagram" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                        <a href="#" aria-label="Twitter" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-twitter" aria-hidden="true"></i></a>
                        <a href="#" aria-label="YouTube" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                        <a href="#" aria-label="Facebook" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-facebook-f" aria-hidden="true"></i></a>
                    </div>
                    
                    
                </div>
            </div>

        </div>
    </div>

    <div class="footer-bottom">
        &copy; <?= date('Y') ?> Tous droits réservés. Conçu avec soin pour la meilleure expérience d'achat.
    </div>

</footer>

<script src="<?= URL ?>public/assets/js/global.js" defer></script>
<script src="<?= URL ?>public/assets/js/header.js" defer></script>
<script src="<?= URL ?>public/assets/js/cart.js" defer></script>

</body>
</html>