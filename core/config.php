<?php


// Configuration stricte des cookies de session pour contrer les attaques XSS et CSRF
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_samesite', 'Lax');

// Chargement sécurisé des paramètres depuis la base de données
try {
    $options = Model::getoption();
} catch (Exception $e) {
    $options = [];
}

// Définition de l'URL racine de l'application
define('URL', $options['root'] ?? 'http://localhost/maboutique/');

// Délai d'expiration pour le paiement d'une commande (en heures)
define('PAYMENT_DEADLINE', $options['payment_deadline'] ?? 24);

// Paramètres de personnalisation visuelle du thème
define('menu_color', $options['menu_color'] ?? '');
define('body_color', $options['body_color'] ?? '');


function style(string $path): string 
{
    return URL . 'public/assets/css/' . $path;
}


function script(string $path): string 
{
    return URL . 'public/assets/js/' . $path;
}


function dd($var) 
{
    echo '<pre style="background: #f4f4f4; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">';
    var_dump($var);
    echo '</pre>';
}