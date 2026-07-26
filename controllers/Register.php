<?php

/**
 * Contrôleur Register
 * Gère l'inscription des utilisateurs avec validation stricte côté serveur.
 * Protection complète contre les failles CSRF et les données invalides.
 */
class Register extends Controller 
{
    public function __construct() 
    {
        parent::__construct();
        Model::sessionInit(); // Initialisation des sessions pour gérer la sécurité
    }
    
    public function index() 
    {
        // PROTECTION CSRF : Génération d'un jeton unique pour le formulaire
        if (!Model::sessionGet('csrf_token')) {
            Model::sessionSet('csrf_token', bin2hex(random_bytes(32)));
        }
        
        $data = [
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        // Affichage de la vue
        $this->view('register/register', $data);
    }

    public function save() 
    {
        // SÉCURITÉ : Bloquer les requêtes qui ne sont pas de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Vérifier l'authenticité de la requête
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = Model::sessionGet('csrf_token');

        if (!$token || $token !== $sessionToken) {
            die('Erreur de sécurité : Jeton CSRF invalide ou expiré.');
        }

        // SÉCURITÉ : Validation stricte des données côté serveur (Ne jamais faire confiance au Front-end)
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Si les données sont invalides ou incomplètes, on redirige avec une erreur
        if (!$email || strlen($password) < 6 || $password !== $passwordConfirm) {
            header('Location: ' . URL . 'Register/index?error=validation');
            exit;
        }

        // Le modèle tente d'enregistrer l'utilisateur
        $isRegistered = $this->model->insertUser($_POST);
        
        if ($isRegistered) {
            // Succès : Redirection vers la page de connexion
            header('Location: ' . URL . 'Login/index?register=success');
        } else {
            // Échec : L'adresse e-mail existe déjà
            header('Location: ' . URL . 'Register/index?error=exists');
        }
        exit;
    }
}
?>