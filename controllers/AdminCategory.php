<?php

/**
 * Controller AdminCategory
 * Gère les catégories et les attributs du panel d'administration.
 * Sécurisé avec vérification des droits, méthode POST et jetons CSRF (Standard DWWM).
 */
class AdminCategory extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès
        Model::sessionInit();
        $level = Model::getUserLevel();
        
        // Seul l'administrateur (level == 1) peut accéder à ce contrôleur
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }
    }

    public function index()
    {
        $data = [
            'category' => $this->model->getChildren(0),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_category/category', $data); 
    }

    public function showChildren($categoryId = 0)
    {
        $data = [
            'categoryInfo' => $this->model->categoryInfo((int)$categoryId),
            'category' => $this->model->getChildren((int)$categoryId),
            'parents' => $this->model->getParents((int)$categoryId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_category/category', $data);
    }

    public function addCategory($parentId = 0, $editId = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SÉCURITÉ : Vérification unifiée du jeton CSRF
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
            $this->model->addCategory($_POST, (int)$parentId, (int)$editId);
            header('Location: ' . URL . 'AdminCategory/showChildren/' . (int)$parentId);
            exit;
        }

        $data = [
            'parentId' => (int)$parentId,
            'edit' => (int)$editId,
            'category' => $this->model->getCategory(),
            'csrf_token' => $this->generateCsrfToken()
        ];

        if ($editId > 0) {
            $data['categoryInfo'] = $this->model->categoryInfo((int)$editId);
        }

        $this->view('admin/admin_category/add_category', $data);
    }

    public function deleteCategory($parentId = 0)
    {
        // SÉCURITÉ : La suppression DOIT être une requête POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // VÉRIFICATION CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->deleteCategory($ids);
        }
        
        header('Location: ' . URL . 'AdminCategory/showChildren/' . (int)$parentId);
        exit;
    }

    public function showAttributes($categoryId, $attrId = 0)
    {
        $data = [
            'attr' => $this->model->getAttr((int)$categoryId, (int)$attrId),
            'categoryInfo' => $this->model->categoryInfo((int)$categoryId),
            'attrInfo' => $this->model->attrInfo((int)$attrId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_category/show_attr', $data);
    }

    public function addAttribute($categoryId, $parentId = 0, $editId = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
            $this->model->addAttribute($_POST, (int)$categoryId, (int)$editId);
            header('Location: ' . URL . 'AdminCategory/showAttributes/' . (int)$categoryId . '/' . (int)$parentId);
            exit;
        }

        $data = [
            'categoryId' => (int)$categoryId,
            'parentId' => (int)$parentId,
            'categoryInfo' => $this->model->categoryInfo((int)$categoryId),
            'attrInfo' => $this->model->attrInfo((int)$parentId),
            'attr' => $this->model->getAttr((int)$categoryId, 0),
            'csrf_token' => $this->generateCsrfToken()
        ];

        if ($editId > 0) {
            $data['editInfo'] = $this->model->attrInfo((int)$editId);
        }

        $this->view('admin/admin_category/add_attr', $data);
    }

    public function deleteAttribute($categoryId, $attributeId = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $ids = $_POST['id'] ?? [];
        if (!empty($ids)) {
            $this->model->deleteAttr($ids);
        }
        
        header('Location: ' . URL . 'AdminCategory/showAttributes/' . (int)$categoryId . '/' . (int)$attributeId);
        exit;
    }

    public function attributeValues($attributeId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submited'])) {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            $this->model->saveAttrVal($_POST, (int)$attributeId);
            
            // Recharger la page après sauvegarde pour éviter de soumettre deux fois
            header('Location: ' . URL . 'AdminCategory/attributeValues/' . (int)$attributeId);
            exit;
        }

        $data = [
            'attrval' => $this->model->getAttrVal((int)$attributeId),
            'attrInfo' => $this->model->attrInfo((int)$attributeId),
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('admin/admin_category/attr_val', $data);
    }
}
?>