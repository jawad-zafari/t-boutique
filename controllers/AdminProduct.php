<?php

/**
 * Contrôleur AdminProduct
 * Gère toutes les actions liées aux produits, la galerie, les attributs et les critiques.
 */
class AdminProduct extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès. (Seul l'administrateur a accès)
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
            'product' => $this->model->getProduct(),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        $this->view('admin/admin_product/products', $data);
    }

    public function addProduct($productId = 0)
    {
        // SÉCURITÉ : Vérification de la méthode POST et du jeton CSRF
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }
            
            $image = $_FILES['image'] ?? null;
            $this->model->addProductAction($_POST, (int)$productId, $image);
            
            header('Location: ' . URL . 'AdminProduct/index?success=product_saved');
            exit;
        }

        $data = [
            'category' => $this->model->getCategory(),
            'color' => $this->model->getColor(),
            'garantee' => $this->model->getGarantee(),
            'productInfo' => $this->model->getProductInfo((int)$productId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_product/add_product', $data);
    }

    public function deleteProduct()
    {
        // SÉCURITÉ CRITIQUE : Bloquer les suppressions via méthode GET
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
            $this->model->deleteProduct($ids);
        }
        
        header('Location: ' . URL . 'AdminProduct/index?success=product_deleted');
        exit;
    }

    // ==========================================
    // GESTION DE LA GALERIE D'IMAGES
    // ==========================================

    public function gallery($productId)
    {
        $data = [
            'gallery' => $this->model->getGallery((int)$productId),
            'productInfo' => $this->model->getProductInfo((int)$productId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        $this->view('admin/admin_product/gallery', $data);
    }

    public function addGallery($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }
            $this->model->addGallery((int)$productId, $_FILES['images'] ?? null);
        }
        header('Location: ' . URL . 'AdminProduct/gallery/' . (int)$productId . '?success=image_added');
        exit;
    }

    public function deleteGallery($productId)
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
            $this->model->deleteGallery($ids);
        }
        header('Location: ' . URL . 'AdminProduct/gallery/' . (int)$productId . '?success=image_deleted');
        exit;
    }

    // ==========================================
    // GESTION DES ATTRIBUTS
    // ==========================================

    public function attributes($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }
            
            $this->model->editAttribute($_POST, (int)$productId);
            header('Location: ' . URL . 'AdminProduct/attributes/' . (int)$productId . '?success=1');
            exit;
        }

        $data = [
            'attr' => $this->model->getProductAttr((int)$productId),
            'productInfo' => $this->model->getProductInfo((int)$productId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        $this->view('admin/admin_product/attributes', $data);
    }

    // ==========================================
    // GESTION DES CRITIQUES ET AVIS
    // ==========================================

    public function reviews($productId)
    {
        $data = [
            'naghd' => $this->model->getReview((int)$productId),
            'productInfo' => $this->model->getProductInfo((int)$productId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        $this->view('admin/admin_product/reviews', $data);
    }

    public function addReview($productId, $reviewId = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? '';
            if ($token !== Model::sessionGet('csrf_token')) {
                die('Erreur de sécurité : Jeton CSRF invalide.');
            }
            
            $this->model->addReview($_POST, (int)$productId, (int)$reviewId);
            header('Location: ' . URL . 'AdminProduct/reviews/' . (int)$productId);
            exit;
        }

        $data = [
            'productInfo' => $this->model->getProductInfo((int)$productId),
            'naghdInfo' => $this->model->getReviewInfo((int)$reviewId),
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        $this->view('admin/admin_product/add_review', $data);
    }

    public function deleteReview($productId)
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
            $this->model->deleteReview($ids);
        }
        header('Location: ' . URL . 'AdminProduct/reviews/' . (int)$productId);
        exit;
    }
}
?>