<?php

/**
 * Modèle ModelAdminLogin
 * Sécurise le processus d'authentification (Protection contre l'injection SQL et Fixation de Session).
 */
class ModelAdminLogin extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Vérifier les identifiants et les droits d'accès de l'utilisateur
    public function checkUser($form)
    {
        // SÉCURITÉ : Nettoyage et assainissement strict de l'adresse e-mail
        $email = filter_var($form['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $form['password'] ?? '';

        if (empty($email) || empty($password)) {
            return false;
        }

        // SÉCURITÉ : On récupère l'utilisateur par son e-mail uniquement
        $sql = "SELECT id, password, role_id FROM users WHERE email = ?";
        $user = $this->doSelect($sql, [$email], true);

        // Si l'utilisateur existe
        if (!empty($user)) {
            
            // SÉCURITÉ CRITIQUE 1 : Vérification du mot de passe haché (Bcrypt/Argon2)
            // SÉCURITÉ CRITIQUE 2 : Contrôle d'accès (RBAC) - Seuls les rôles 1 (Admin) et 2 (Employé) peuvent se connecter
            if (password_verify($password, $user['password']) && ($user['role_id'] == 1 || $user['role_id'] == 2)) {
                
                Model::sessionInit();
                
                // PRÉVENTION : Régénération de l'ID de session pour empêcher l'attaque "Session Fixation"
                session_regenerate_id(true);
                
                // Stockage sécurisé dans la session
                Model::sessionSet('userId', (int)$user['id']);
                Model::sessionSet('userLevel', (int)$user['role_id']);
                
                return true;
            }
        }

        return false;
    }
}
?>