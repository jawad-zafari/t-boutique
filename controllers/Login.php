<?php

/**
 * Contrôleur Login
 * Gère l'authentification et la déconnexion des utilisateurs.
 * Sécurité: Protection CSRF, vérification de la méthode POST et gestion sécurisée des sessions.
 */
class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // SÉCURITÉ : Initialisation de la session pour la gestion du jeton CSRF et de l'authentification
        Model::sessionInit(); 
    }

    /**
     * Affiche la page de connexion
     */
    public function index(): void
    {
        // Préparation du jeton CSRF pour le formulaire de connexion
        $data = [
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('login/login', $data);
    }

    /**
     * Traite la soumission du formulaire de connexion
     */
    public function checkUser(): void
    {
        // SÉCURITÉ : Bloquer les requêtes qui ne sont pas envoyées en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // SÉCURITÉ : Vérification obligatoire du jeton CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $formData = $_POST;
        $isLoggedIn = $this->model->checkUser($formData);
        
        if ($isLoggedIn) {
            // SÉCURITÉ : Protection contre les redirections ouvertes (Open Redirect)
            $backUrl = isset($_POST['back_url']) ? trim($_POST['back_url']) : '';
            
            if (!empty($backUrl) && strpos($backUrl, 'http') === false && strpos($backUrl, '//') === false) {
                header('Location: ' . URL . ltrim($backUrl, '/'));
            } else {
                header('Location: ' . URL . 'Index/index');
            }
        } else {
            $backParam = isset($_POST['back_url']) ? '&back=' . urlencode($_POST['back_url']) : '';
            header('Location: ' . URL . 'Login/index?error=1' . $backParam);
        }
        exit;
    }

    /**
     * Déconnecte l'utilisateur et détruit la session de manière sécurisée
     */
    public function logout(): void
    {
        Model::sessionInit();
        
        // Vider toutes les variables de la session actuelle
        $_SESSION = array();
        
        // SÉCURITÉ : Suppression sécurisée du cookie de session côté client
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruction de la session sur le serveur
        session_destroy();
        header('Location: ' . URL . 'Index/index');
        exit;
    }
}
?>