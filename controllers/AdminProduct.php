<?php

/**
 * Contrôleur AdminProduct
 * Gère la section des produits dans le panneau d'administration.
 * Intègre le typage strict (PHP 8) et la sécurité centralisée (CSRF, Rôles).
 */
class AdminProduct extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès (Seul l'admin a accès)
        Model::sessionInit();
        $level = Model::getUserLevel();
        if ($level !== 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }
    }

    /**
     * Affiche la liste des produits
     */
    public function index(): void
    {
        $data = [
            'product' => $this->model->getProduct(),
            'csrf_token' => $this->generateCsrfToken() // RÈGLE MVC : Encapsulation (Pas d'appel direct à $_SESSION)
        ];
        $this->view('admin/admin_product/products', $data);
    }

    /**
     * Ajoute ou modifie un produit
     * @param int $productId L'identifiant du produit (0 pour un nouveau)
     */
    public function addProduct(int $productId = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // VÉRIFICATION CSRF CENTRALISÉE
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
            $image = $_FILES['image'] ?? null;
            $this->model->addProductAction($_POST, $productId, $image);
            
            header('Location: ' . URL . 'AdminProduct/index?success=product_saved');
            exit;
        }

        $data = [
            'category' => $this->model->getCategory(),
            'color' => $this->model->getColor(),
            'garantee' => $this->model->getGarantee(),
            'productInfo' => $this->model->getProductInfo($productId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_product/add_product', $data);
    }

    /**
     * Supprime un ou plusieurs produits
     */
    public function deleteProduct(): void
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        $ids = $_POST['id'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $this->model->deleteProduct($ids);
        }
        
        header('Location: ' . URL . 'AdminProduct/index?success=product_deleted');
        exit;
    }

    // ==========================================
    // GESTION DE LA GALERIE D'IMAGES
    // ==========================================

    public function gallery(int $productId): void
    {
        $data = [
            'gallery' => $this->model->getGallery($productId),
            'productInfo' => $this->model->getProductInfo($productId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->view('admin/admin_product/gallery', $data);
    }

    public function addGallery(int $productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            $this->model->addGallery($productId, $_FILES['images'] ?? null);
        }
        header('Location: ' . URL . 'AdminProduct/gallery/' . $productId . '?success=image_added');
        exit;
    }

    public function deleteGallery(int $productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        $ids = $_POST['id'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $this->model->deleteGallery($ids);
        }
        
        header('Location: ' . URL . 'AdminProduct/gallery/' . $productId . '?success=image_deleted');
        exit;
    }

    // ==========================================
    // GESTION DES ATTRIBUTS
    // ==========================================

    public function attributes(int $productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
            $this->model->editAttribute($_POST, $productId);
            header('Location: ' . URL . 'AdminProduct/attributes/' . $productId . '?success=1');
            exit;
        }

        $data = [
            'attr' => $this->model->getProductAttr($productId),
            'productInfo' => $this->model->getProductInfo($productId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->view('admin/admin_product/attributes', $data);
    }

    // ==========================================
    // GESTION DES AVIS (REVIEWS)
    // ==========================================

    public function reviews(int $productId): void
    {
        $data = [
            'naghd' => $this->model->getReview($productId),
            'productInfo' => $this->model->getProductInfo($productId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->view('admin/admin_product/reviews', $data);
    }

    public function addReview(int $productId, int $reviewId = 0): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');
            
            $this->model->addReview($_POST, $productId, $reviewId);
            header('Location: ' . URL . 'AdminProduct/reviews/' . $productId);
            exit;
        }

        $data = [
            'productInfo' => $this->model->getProductInfo($productId),
            'naghdInfo' => $this->model->getReviewInfo($reviewId),
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->view('admin/admin_product/add_review', $data);
    }

    public function deleteReview(int $productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        $ids = $_POST['id'] ?? [];
        if (!empty($ids) && is_array($ids)) {
            $this->model->deleteReview($ids);
        }
        
        header('Location: ' . URL . 'AdminProduct/reviews/' . $productId);
        exit;
    }
}
?>