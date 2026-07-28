<?php

/**
 * Contrôleur Register
 * Gère l'inscription des utilisateurs.
 * Architecture harmonisée avec la gestion CSRF globale.
 */
class Register extends Controller 
{
    public function __construct() 
    {
        parent::__construct();
        Model::sessionInit(); 
    }
    
    /**
     * Affiche le formulaire d'inscription
     */
    public function index(): void 
    {
        $data = [
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('register/register', $data);
    }

    /**
     * Traite la sauvegarde d'un nouvel utilisateur
     */
    public function save(): void 
    {
        // Validation stricte de la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // Vérification CSRF globale (DRY Principle)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validation côté serveur
        if (!$email || strlen($password) < 6 || $password !== $passwordConfirm) {
            header('Location: ' . URL . 'Register/index?error=validation');
            exit;
        }

        $isRegistered = $this->model->insertUser($_POST);
        
        if ($isRegistered) {
            header('Location: ' . URL . 'Login/index?register=success');
        } else {
            header('Location: ' . URL . 'Register/index?error=exists');
        }
        exit;
    }
}
?>