<?php

/**
 * Contrôleur AdminNews
 * Gère les actualités du site (CRUD) avec sécurité renforcée (CSRF & Method POST).
 */
class AdminNews extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Contrôle d'accès strict (Administrateur et Employé)
        Model::sessionInit();
        $userLevel = Model::getUserLevel();
        
        if ($userLevel != 1 && $userLevel != 2) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit; 
        }
    }

    public function index()
    {
        $news = $this->model->getNews();
        
        $data = [
            'news' => $news, 
            'activeMenu' => 'news',
            'csrf_token' => $this->generateCsrfToken() // Utilisation de la méthode globale unifiée
        ];
        
        $this->view('admin/admin_news/news', $data);
    }

    public function add()
    {
        $data = [
            'activeMenu' => 'news',
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->view('admin/admin_news/add', $data);
    }

    public function doAdd()
    {
        // SÉCURITÉ : Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // SÉCURITÉ : Vérification du jeton CSRF unifiée
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->addNews($_POST, $_FILES);
        
        header('Location: ' . URL . 'AdminNews/index');
        exit;
    }

    public function edit($id)
    {
        $newsInfo = $this->model->getNewsById((int)$id);
        
        $data = [
            'newsInfo' => $newsInfo, 
            'activeMenu' => 'news',
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_news/edit', $data);
    }

    public function doEdit($id)
    {
        // SÉCURITÉ : Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // SÉCURITÉ : Vérification du jeton CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->editNews((int)$id, $_POST, $_FILES);
        
        header('Location: ' . URL . 'AdminNews/index');
        exit;
    }

    public function delete($id)
    {
        // SÉCURITÉ CRITIQUE : Empêcher la suppression via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // SÉCURITÉ : Vérification du jeton CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->deleteNews((int)$id);
        
        header('Location: ' . URL . 'AdminNews/index');
        exit;
    }
}
?>