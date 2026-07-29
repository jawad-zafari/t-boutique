<?php

/**
 * Contrôleur AdminSlider
 * Gère le diaporama de la page d'accueil (Sécurisé par accès Niveau 1 et CSRF).
 * Architecture unifiée pour le niveau Junior DWWM.
 */
class AdminSlider extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification des droits d'accès. (Seul l'administrateur a accès)
        Model::sessionInit();
        $level = Model::getUserLevel();
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }
    }

    /**
     * Affiche la liste des slides et le formulaire (Requête GET)
     */
    public function index()
    {
        $sliders = $this->model->getslider();
        
        $data = [
            'slider' => $sliders,
            'editSlider' => null,
            // RÈGLE MVC : Appel de la méthode globale unifiée pour générer le jeton
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_slider/slider', $data);
    }

    /**
     * Traite l'ajout d'un nouveau slide (Requête POST)
     */
    public function add()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Unifiée (Remplace le "die" bloquant)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $result = $this->model->addSlider($_POST, $_FILES);
        if ($result) {
            header('Location: ' . URL . 'AdminSlider/index?success=add');
        } else {
            header('Location: ' . URL . 'AdminSlider/index?error=upload');
        }
        exit;
    }

    /**
     * Charge les données d'un slide pour modification (Requête GET)
     */
    public function edit($id)
    {
        $sliders = $this->model->getslider();
        $editSlider = $this->model->getSliderById((int)$id);

        if (empty($editSlider)) {
            header('Location: ' . URL . 'AdminSlider/index');
            exit;
        }

        $data = [
            'slider' => $sliders,
            'editSlider' => $editSlider,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('admin/admin_slider/slider', $data);
    }

    /**
     * Traite la mise à jour d'un slide existant (Requête POST)
     */
    public function update($id)
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Unifiée
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
        $result = $this->model->updateSlider((int)$id, $_POST, $_FILES);
        if ($result) {
            header('Location: ' . URL . 'AdminSlider/index?success=update');
        } else {
            header('Location: ' . URL . 'AdminSlider/edit/' . (int)$id . '?error=upload');
        }
        exit;
    }

    /**
     * Supprimer un ou plusieurs slides (Requête POST)
     */
    public function delete()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF : Unifiée
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
        $this->model->delete($_POST);
        header('Location: ' . URL . 'AdminSlider/index?success=delete');
        exit;
    }
}
?>