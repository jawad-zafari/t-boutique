<?php

/**
 * Controller Cart
 * Sécurisé selon les standards DWWM (Validation des méthodes HTTP et protection CSRF)
 */
class Cart extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    // Afficher la page complète du panier
    public function index()
    {
        $cartData = $this->model->getCartData();
        
        // PROTECTION CSRF : Génération du jeton pour la page du panier
        $data = [
            'cartItems' => $cartData[0] ?? [],
            'priceTotalAll' => $cartData[1] ?? 0,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('cart/cart', $data);
    }

    // SÉCURITÉ : Vérification de la méthode POST et du jeton CSRF
    public function deleteCart($cartRowId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
        }

        // Vérification du jeton pour empêcher la suppression malveillante
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->deleteCartItem((int)$cartRowId);
        $cartData = $this->model->getCartData();
        echo json_encode($cartData);
    }

    // SÉCURITÉ : Vérification de la méthode POST et du jeton CSRF
    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
        }

        // Vérification du jeton pour empêcher la modification malveillante des quantités
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->updateCartItem($_POST);
        $cartData = $this->model->getCartData();
        echo json_encode($cartData);
    }

    // SÉCURITÉ : Vérification de la méthode POST et du jeton CSRF
    public function addToCart($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
        }

        // Vérification du jeton pour empêcher l'ajout forcé de produits
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $quantity = $_POST['quantity'] ?? 1;
        $colorId = $_POST['colorId'] ?? 0;
        $guaranteeId = $_POST['guaranteeId'] ?? 0;

        $this->model->addToCart((int)$productId, (int)$quantity, (int)$colorId, (int)$guaranteeId);
        
        $cartData = $this->model->getCartData();
        echo json_encode($cartData);
    }
}
?>