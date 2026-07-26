<?php

/**
 * Model ModelLogin
 * Gère la vérification des utilisateurs en respectant l'architecture MVC globale.
 */
class ModelLogin extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Vérifie les identifiants et initialise la session utilisateur de manière sécurisée
     */
    public function checkUser($form)
    {
        // Nettoyage et assainissement de l'adresse e-mail
        $email = filter_var($form['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $form['password'] ?? '';

        if (empty($email) || empty($password)) {
            return false;
        }

        // SÉCURITÉ & ARCHITECTURE : Utilisation de doSelect() défini dans le modèle parent
        $sql = "SELECT id, password FROM users WHERE email = ?";
        $result = $this->doSelect($sql, [$email]);

        // doSelect retourne un tableau de résultats. On récupère la première ligne.
        $user = $result[0] ?? null;

        // SÉCURITÉ CRITIQUE : Vérification du mot de passe crypté via password_verify
        if ($user && password_verify($password, $user['password'])) {
            
            Model::sessionInit();
            
            // PRÉVENTION : Régénération de l'ID de session pour bloquer la fixation de session
            session_regenerate_id(true);
            
            // Stockage sécurisé de l'identifiant utilisateur
            Model::sessionSet('userId', (int)$user['id']);
            
            return true; 
        }
        
        return false; 
    }
}
?>