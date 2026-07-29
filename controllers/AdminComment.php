<?php

/**
 * Contrôleur AdminComment
 * Gère la modération des commentaires de manière strictement sécurisée (POST & CSRF).
 * Règle MVC et Standard DWWM respectés.
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
    }

    public function index()
    {
        $data = [
            'comment' => $this->model->getComment(),
            'csrf_token' => $this->generateCsrfToken() // Génération unifiée du token
        ];
        
        $this->view('admin/admin_comment/comment', $data);
    }

    public function confirm()
    {
        // SÉCURITÉ : La modification DOIT être une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // VÉRIFICATION CSRF UNIFIÉE (Remplace le "die" bloquant)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->confirm($_POST);
        
        header('Location: ' . URL . 'AdminComment/index');
        exit;
    }

    public function unconfirm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->unconfirm($ids);
        }
        
        header('Location: ' . URL . 'AdminComment/index');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->delete($ids);
        }
        
        header('Location: ' . URL . 'AdminComment/index');
        exit;
    }
}
?>