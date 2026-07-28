<?php

/**
 * Fichier d'exemple pour la configuration de l'environnement.
 * Les développeurs doivent copier ce fichier, le renommer en "env.php" 
 * et remplir leurs propres valeurs.
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'votre_base_de_donnees');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');

// Configuration de l'API de paiement (Stripe)
define('STRIPE_PUBLIC_KEY', 'pk_test_votre_cle_publique_ici');
define('STRIPE_SECRET_KEY', 'sk_test_votre_cle_secrete_ici');

?>