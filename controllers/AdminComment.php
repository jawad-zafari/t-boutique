<?php

/**
 * Contrôleur AdminComment
 * Gère la modération des commentaires de manière strictement sécurisée (POST & CSRF).
 */
class AdminComment extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès administrateur (Niveau 1)
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

    public function index()
    {
        $data = [
            'comment' => $this->model->getComment(),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_comment/comment', $data);
    }

    public function confirm()
    {
        // SÉCURITÉ : La modification DOIT être une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }

        $this->model->confirm($_POST);
        
        header('Location: ' . URL . 'AdminComment/index');
        exit;
    }

    public function unconfirm()
    {
        // SÉCURITÉ : La modification DOIT être une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->unconfirm($ids);
        }
        
        header('Location: ' . URL . 'AdminComment/index');
        exit;
    }

    public function delete()
    {
        // SÉCURITÉ : La suppression DOIT être une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->delete($ids);
        }
        
        header('Location: ' . URL . 'AdminComment/index');
        exit;
    }
}
?>