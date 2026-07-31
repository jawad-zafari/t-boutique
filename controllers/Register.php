<?php

/**
 * Contrôleur Register
 * Gère l'inscription des nouveaux utilisateurs.
 * Sécurité : Protection CSRF, validation des données et vérification de la méthode HTTP.
 */
class Register extends Controller 
{
    public function __construct() 
    {
        parent::__construct();
        // SÉCURITÉ : Initialisation de la session pour la gestion du jeton CSRF
        Model::sessionInit(); 
    }
    
    /**
     * Affiche la page du formulaire d'inscription
     */
    public function index(): void 
    {
        $data = [
            // PROTECTION CSRF : Génération du jeton sécurisé
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('register/register', $data);
    }

    /**
     * Traite les données soumises pour créer un compte utilisateur
     */
    public function save(): void 
    {
        // SÉCURITÉ : N'accepter que les requêtes de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // SÉCURITÉ : Vérification du jeton CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        // SÉCURITÉ : Validation stricte de l'e-mail côté serveur
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Validation basique côté serveur (Longueur et correspondance des mots de passe)
        if (!$email || strlen($password) < 6 || $password !== $passwordConfirm) {
            header('Location: ' . URL . 'Register/index?error=validation');
            exit;
        }

        // Appel au modèle pour insérer les données
        $isRegistered = $this->model->insertUser($_POST);
        
        if ($isRegistered) {
            // Redirection vers la page de connexion après une inscription réussie
            header('Location: ' . URL . 'Login/index?success=registered');
        } else {
            // L'utilisateur existe déjà ou une erreur SQL s'est produite
            header('Location: ' . URL . 'Register/index?error=exists');
        }
        exit;
    }
}
?>