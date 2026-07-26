<?php

/**
 * Controller Login
 * Gère l'authentification des utilisateurs avec vérification POST et CSRF.
 * Supporte la redirection intelligente (Intended URL).
 */
class Login extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit(); // Initialisation sécurisée de la session
    }

    public function index()
    {
        // PROTECTION CSRF : Utilisation de la méthode globale unifiée
        $data = [
            'csrf_token' => $this->generateCsrfToken()
        ];

        // Chargement de la vue du formulaire de connexion
        $this->view('login/login', $data);
    }

    public function checkUser()
    {
        // SÉCURITÉ : Validation stricte de la méthode HTTP POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Utilisation de la méthode globale unifiée
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $formData = $_POST;
        $isLoggedIn = $this->model->checkUser($formData);
        
        if ($isLoggedIn) {
            // REDIRECTION INTELLIGENTE : Si un paramètre 'back_url' existe, on y retourne !
            $backUrl = isset($_POST['back_url']) ? trim($_POST['back_url']) : '';
            
            if (!empty($backUrl) && strpos($backUrl, 'http') === false) {
                // SÉCURITÉ : On vérifie que le backUrl est bien un chemin local pour éviter l'Open Redirect
                header('Location: ' . URL . $backUrl);
            } else {
                // Redirection par défaut vers la page d'accueil ou le profil
                header('Location: ' . URL . 'Index/index');
            }
        } else {
            // Redirection avec un paramètre d'erreur et on conserve le lien de retour
            $backParam = isset($_POST['back_url']) ? '&back=' . urlencode($_POST['back_url']) : '';
            header('Location: ' . URL . 'Login/index?error=1' . $backParam);
        }
        exit;
    }

    public function logout()
    {
        Model::sessionInit();
        
        // SÉCURITÉ : Nettoyage complet de la session pour la déconnexion
        $_SESSION = array();
        
        // Destruction sécurisée du cookie de session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
        
        // Redirection propre après déconnexion
        header('Location: ' . URL . 'Index/index');
        exit;
    }
}
?>