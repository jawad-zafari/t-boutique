<?php

/**
 * Controller AdminCategory
 * Gère les catégories et les attributs du panel d'administration.
 * Sécurisé avec vérification des droits, méthode POST et jetons CSRF.
 */
class AdminCategory extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès
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
            'category' => $this->model->getChildren(0),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_category/category', $data); 
    }

    public function showChildren($categoryId = 0)
    {
        $data = [
            'categoryInfo' => $this->model->categoryInfo((int)$categoryId),
            'category' => $this->model->getChildren((int)$categoryId),
            'parents' => $this->model->getParents((int)$categoryId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_category/category', $data);
    }

    public function addCategory($parentId = 0, $editId = 0)
    {
        // Traitement du formulaire (POST)
        if (isset($_POST['title'])) {
            // SÉCURITÉ : Bloquer si la méthode n'est pas POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.1 405 Method Not Allowed');
                exit('Méthode non autorisée');
            }

            // VÉRIFICATION CSRF
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }

            $this->model->addCategory($_POST, (int)$editId);
            header('Location: ' . URL . 'AdminCategory/showChildren/' . (int)$parentId);
            exit;
        }

        // Affichage du formulaire (GET)
        $data = [
            'category' => $this->model->getCategory(),
            'parentId' => (int)$parentId,
            'edit' => (int)$editId,
            'categoryInfo' => $this->model->categoryInfo((int)$editId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_category/add_category', $data);
    }

    public function deleteCategory($categoryId = 0)
    {
        // SÉCURITÉ : La suppression DOIT être une requête POST
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
            $this->model->deleteCategory($ids);
        }
        
        header('Location: ' . URL . 'AdminCategory/showChildren/' . (int)$categoryId);
        exit;
    }

    public function showAttributes($categoryId, $attrId = 0)
    {
        $data = [
            'attr' => $this->model->getAttr((int)$categoryId, (int)$attrId),
            'categoryInfo' => $this->model->categoryInfo((int)$categoryId),
            'attrInfo' => $this->model->attrInfo((int)$attrId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_category/show_attr', $data);
    }

    public function addAttribute($categoryId, $parentId = 0, $editId = 0)
    {
        if (isset($_POST['title'])) {
            // SÉCURITÉ : Vérification de la méthode POST et du CSRF
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('HTTP/1.1 405 Method Not Allowed');
                exit('Méthode non autorisée');
            }

            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }

            $this->model->addAttribute($_POST, (int)$categoryId, (int)$editId);
            header('Location: ' . URL . 'AdminCategory/showAttributes/' . (int)$categoryId . '/' . (int)$parentId);
            exit;
        }

        $data = [
            'attr' => $this->model->getAttr((int)$categoryId, 0),
            'categoryInfo' => $this->model->categoryInfo((int)$categoryId),
            'parentId' => (int)$parentId,
            'editInfo' => $this->model->attrInfo((int)$editId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_category/add_attr', $data);
    }

    public function deleteAttribute($categoryId, $attributeId)
    {
        // SÉCURITÉ : La suppression DOIT être une requête POST
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
            $this->model->deleteAttr($ids);
        }
        
        header('Location: ' . URL . 'AdminCategory/showAttributes/' . (int)$categoryId . '/' . (int)$attributeId);
        exit;
    }

    public function attributeValues($attributeId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SÉCURITÉ CSRF
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }
            
            if (isset($_POST['submited'])) {
                $this->model->saveAttrVal($_POST, (int)$attributeId);
            }
        }

        $data = [
            'attrval' => $this->model->getAttrVal((int)$attributeId),
            'attrInfo' => $this->model->attrInfo((int)$attributeId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_category/attr_val', $data);
    }
}
?>