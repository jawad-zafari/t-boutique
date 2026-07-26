<?php

/**
 * Contrôleur AdminLogin
 * Gère l'authentification sécurisée des administrateurs et employés au panneau de contrôle.
 */
class AdminLogin extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // PROTECTION CSRF : Génération du jeton pour le formulaire de connexion administrateur
        $data = [
            'csrf_token' => $this->generateCsrfToken()
        ];

        // Affichage de la vue de connexion
        $this->view('admin/admin_login/login', $data);
    }

    public function checkUser()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Bloquer les requêtes malveillantes externes (Cross-Site Request Forgery)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        // Le modèle vérifie les identifiants et renvoie vrai ou faux
        $isLoggedIn = $this->model->checkUser($_POST);
        
        if ($isLoggedIn) {
            // Connexion réussie : Redirection sécurisée vers le tableau de bord
            header('Location: ' . URL . 'AdminDashboard/index');
        } else {
            // Échec : Redirection vers la page de connexion avec un paramètre d'erreur
            header('Location: ' . URL . 'AdminLogin/index?error=1');
        }
        exit;
    }

    /**
     * Méthode pour déconnecter l'administrateur en toute sécurité
     */
    public function logout()
    {
        Model::sessionInit();
        
        // SÉCURITÉ : Destruction complète des données de la session en cours
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        
        // Redirection vers la page de connexion de l'administration
        header('Location: ' . URL . 'AdminLogin/index');
        exit;
    }
}
?>