<?php

// CONFIGURATION SÉCURISÉE DES SESSIONS (Sécurité par défaut pour le projet)
// Empêche le JavaScript d'accéder aux cookies de session (Protection contre le vol de session XSS)
ini_set('session.cookie_httponly', 1);

// Force l'utilisation exclusive des cookies pour stocker l'ID de session (Évite la fixation de session)
ini_set('session.use_only_cookies', 1);

// Protection initiale contre les requêtes intersites malveillantes (Attaques CSRF)
ini_set('session.cookie_samesite', 'Lax');

$model = new Model();
$options = Model::getoption();

// Définition des constantes globales du système
define('URL', $options['root'] ?? '');
define('zarinpalMerchantID', $options['zarinpalMID'] ?? '');

// Correction de l'URL de retour (Dynamique)
define('callbackURL', URL . 'Checkout/index');

define('zarinpalWebAdress', 'https://www.zarinpal.com/pg/services/WebGate/wsdl');
define('mohlatPay', $options['mohlatPay'] ?? 24);
define('menu_color', $options['menu_color'] ?? '');
define('body_color', $options['body_color'] ?? '');

// Traduction des erreurs Zarinpal en français
$zarinpalErrors = array(
    '-1' => 'Les informations envoyées sont incomplètes.',
    '-2' => 'L\'adresse IP ou le code marchand (MerchantID) est incorrect.',
    '-3' => 'Le niveau de validation du marchand est inférieur à "Argent".'
);
define('zarinpalErrors', serialize($zarinpalErrors));

// Fonction pour générer le chemin absolu des fichiers CSS
function style($path) {
    return URL . 'public/assets/css/' . $path;
}

// Fonction pour générer le chemin absolu des fichiers JS
function script($path) {
    return URL . 'public/assets/js/' . $path;
}

?>