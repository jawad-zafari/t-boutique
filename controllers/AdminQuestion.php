<?php

/**
 * Contrôleur AdminQuestion
 * Gère la modération des questions/réponses avec une sécurité stricte (POST & CSRF).
 */
class AdminQuestion extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification des droits d'accès administrateur (Niveau 1)
        Model::sessionInit();
        $level = Model::getUserLevel();
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }

        // PROTECTION CSRF : Génération globale du jeton
        if (!Model::sessionGet('csrf_token')) {
            Model::sessionSet('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    public function index()
    {
        $questions = $this->model->getQuestions();
        
        $data = [
            'questions' => $questions,
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_question/question', $data);
    }

    public function confirm()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
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
        
        header('Location: ' . URL . 'AdminQuestion/index');
        exit;
    }

    public function unconfirm()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
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
        
        header('Location: ' . URL . 'AdminQuestion/index');
        exit;
    }

    public function delete()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
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
        
        header('Location: ' . URL . 'AdminQuestion/index');
        exit;
    }
}
?>