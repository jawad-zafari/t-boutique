<?php

/**
 * Contrôleur AdminUser
 * Gère la liste des utilisateurs et leurs rôles (Niveaux d'accès).
 */
class AdminUser extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Seul l'administrateur principal (Niveau 1) peut gérer les utilisateurs
        Model::sessionInit();
        $level = Model::getUserLevel();
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }

        // PROTECTION CSRF : Génération globale du jeton pour ce contrôleur
        if (!Model::sessionGet('csrf_token')) {
            Model::sessionSet('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    /**
     * Affiche la liste des utilisateurs (Requête GET)
     */
    public function index()
    {
        $users = $this->model->getUsers();
        
        $data = [
            'users' => $users,
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_user/users', $data);
    }

    /**
     * Promeut des utilisateurs au rang d'Administrateur (Niveau 1)
     */
    public function changeLevel1()
    {
        // SÉCURITÉ CRITIQUE : Bloquer les requêtes GET (Method Spoofing)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Bloquer les requêtes forgées
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }
            
        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->changeLevel1($ids);
        }
        
        header('Location: ' . URL . 'AdminUser/index');
        exit;
    }
    
    /**
     * Modifie le rôle des utilisateurs en Employé (Niveau 2)
     */
    public function changeLevel2()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }
            
        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->changeLevel2($ids);
        }
        
        header('Location: ' . URL . 'AdminUser/index');
        exit;
    }

    /**
     * Modifie le rôle des utilisateurs en Utilisateur Normal (Niveau 3)
     */
    public function changeLevel3()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }
            
        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->changeLevel3($ids);
        }
        
        header('Location: ' . URL . 'AdminUser/index');
        exit;
    }

    /**
     * Supprime définitivement des comptes utilisateurs
     */
    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }
            
        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->delete($ids);
        }
        
        header('Location: ' . URL . 'AdminUser/index');
        exit;
    }
}
?>