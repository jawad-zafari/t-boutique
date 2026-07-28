<?php

/**
 * Contrôleur Login
 * Gère l'authentification des utilisateurs.
 * Utilise le typage strict et la protection CSRF centralisée.
 */
class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit(); 
    }

    /**
     * Affiche la page de connexion
     */
    public function index(): void
    {
        $data = [
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('login/login', $data);
    }

    /**
     * Traite les données du formulaire de connexion
     */
    public function checkUser(): void
    {
        // Bloquer les requêtes non-POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // Vérification du jeton CSRF via la méthode du contrôleur parent
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $formData = $_POST;
        $isLoggedIn = $this->model->checkUser($formData);
        
        if ($isLoggedIn) {
            // Redirection intelligente après connexion
            $backUrl = isset($_POST['back_url']) ? trim($_POST['back_url']) : '';
            
            if (!empty($backUrl) && strpos($backUrl, 'http') === false) {
                header('Location: ' . URL . $backUrl);
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
     * Déconnecte l'utilisateur et détruit la session
     */
    public function logout(): void
    {
        Model::sessionInit();
        
        // Vider toutes les variables de session
        $_SESSION = array();
        
        // Détruire le cookie de session de manière sécurisée
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        header('Location: ' . URL . 'Index/index');
        exit;
    }
}
?>