<?php

/**
 * Fichier de Configuration Globale (Projet MVC DWWM)
 * Définit les constantes de sécurité des sessions et les fonctions d'aide pour les assets.
 * Entièrement anglophone et nettoyé des termes non standards.
 */

// SÉCURITÉ DWWM : Configuration stricte des cookies de session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

// Charger les options depuis la base de données de manière sécurisée
try {
    $options = Model::getoption();
} catch (Exception $e) {
    $options = [];
}

// Définition des constantes globales du site
define('URL', $options['root'] ?? 'http://localhost/maboutique/');
define('callbackURL', URL . 'Checkout/index');

// Délai d'expiration pour le paiement d'une commande (en heures)
define('PAYMENT_DEADLINE', $options['payment_deadline'] ?? $options['mohlatPay'] ?? 24);

// Compatibilité pour les anciens contrôleurs (si nécessaire)
define('mohlatPay', PAYMENT_DEADLINE);

// Paramètres de personnalisation visuelle
define('menu_color', $options['menu_color'] ?? '');
define('body_color', $options['body_color'] ?? '');

// =========================================================================
// NOTE SÉCURITÉ : Les clés d'API bancaires (ex: Stripe) ne doivent JAMAIS
// être définies ici. Elles sont gérées de manière sécurisée dans core/env.php
// =========================================================================

/**
 * Génère le chemin absolu pour les fichiers CSS
 * @param string $path Le nom du fichier CSS
 * @return string L'URL complète du fichier sécurisée
 */
function style(string $path): string 
{
    return URL . 'public/assets/css/' . $path;
}

/**
 * Génère le chemin absolu pour les fichiers JavaScript
 * @param string $path Le nom du fichier JS
 * @return string L'URL complète du fichier sécurisée
 */
function script(string $path): string 
{
    return URL . 'public/assets/js/' . $path;
}

?>