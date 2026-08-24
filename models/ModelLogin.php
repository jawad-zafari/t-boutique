<?php

/**
 * Modèle ModelLogin
 * Gère la vérification des identifiants utilisateurs en base de données.
 * Sécurité: Requêtes préparées PDO, hachage de mot de passe et régénération de session.
 */
class ModelLogin extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Vérifie les identifiants de l'utilisateur
     * * @param array $form Données soumises via le formulaire
     * @return bool Retourne true si la connexion est réussie, sinon false
     */
    public function checkUser(array $form): bool
    {
        // SÉCURITÉ : Nettoyage et validation de l'adresse e-mail
        $email = filter_var($form['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $form['password'] ?? '';

        if (empty($email) || empty($password)) {
            return false;
        }

        // Prévention de l'injection SQL grâce aux requêtes préparées PDO
        $sql = "SELECT id, password FROM users WHERE email = ?";
        $user = $this->doSelect($sql, [$email], 'fetch', PDO::FETCH_ASSOC);

        // Vérification du mot de passe haché 
        if ($user && password_verify($password, $user['password'])) {
            
            Model::sessionInit();
            
            // Régénération de l'ID de session contre la fixation de session
            session_regenerate_id(true);
            
            // Forçage du typage en entier
            Model::sessionSet('userId', (int)$user['id']);
            Model::sessionSet('loggedIn', true);
            
            return true;
        }
        
        return false;
    }
}
?>