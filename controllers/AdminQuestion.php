<?php

/**
 * Contrôleur AdminQuestion
 * Gère la modération des questions/réponses avec une sécurité stricte (POST & CSRF).
 * Code standardisé et simplifié pour le niveau Junior.
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
    }

    public function index()
    {
        $questions = $this->model->getQuestions();
        
        $data = [
            'questions' => $questions,
            // RÈGLE MVC : Appel de la méthode globale de génération de jeton
            'csrf_token' => $this->generateCsrfToken() 
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

        // VÉRIFICATION CSRF : Unifiée (Remplace le "die" bloquant)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->confirm($_POST);
        
        header('Location: ' . URL . 'AdminQuestion/index');
        exit;
    }

    public function unconfirm()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->unconfirm($ids);
        }
        
        header('Location: ' . URL . 'AdminQuestion/index');
        exit;
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->delete($ids);
        }
        
        header('Location: ' . URL . 'AdminQuestion/index');
        exit;
    }
}
?>