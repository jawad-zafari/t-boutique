<?php

/**
 * Modèle ModelLogin
 * Vérifie les informations d'identification avec typage strict.
 */
class ModelLogin extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Vérifie les accès de l'utilisateur
     * @param array $form Les données soumises
     * @return bool Vrai si la connexion réussit
     */
    public function checkUser(array $form): bool
    {
        $email = filter_var($form['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $form['password'] ?? '';

        if (empty($email) || empty($password)) {
            return false;
        }

        $sql = "SELECT id, password FROM users WHERE email = ?";
        // Utilisation du paramètre 'fetch' pour récupérer un seul tableau associatif
        $user = $this->doSelect($sql, [$email], 'fetch', PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            Model::sessionInit();
            
            // Prévention contre la fixation de session
            session_regenerate_id(true);
            
            Model::sessionSet('userId', (int)$user['id']);
            Model::sessionSet('loggedIn', true);
            
            return true;
        }
        
        return false;
    }
}
?>