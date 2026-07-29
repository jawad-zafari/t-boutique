<?php

/**
 * Contrôleur Cart (Panier)
 * Gère les actions du panier d'achats via des requêtes AJAX sécurisées (POST & CSRF).
 * Standard DWWM : Séparation des responsabilités et sécurité des flux.
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
        
        // PROTECTION CSRF : Génération du jeton pour sécuriser les actions du panier
        $data = [
            'cartItems' => $cartData[0] ?? [],
            'priceTotalAll' => $cartData[1] ?? 0,
            'csrf_token' => $this->generateCsrfToken()
        ];

        $this->view('cart/cart', $data);
    }

    // SÉCURITÉ : Suppression d'un article (Validation POST + CSRF)
    public function deleteCart($cartRowId)
    {
        // Bloquer les requêtes GET pour éviter les suppressions accidentelles ou malveillantes
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
        }

        // Vérification du jeton CSRF unifiée
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->deleteCartItem((int)$cartRowId);
        
        // Renvoie les nouvelles données du panier au format JSON pour la mise à jour asynchrone (AJAX)
        $cartData = $this->model->getCartData();
        echo json_encode($cartData);
    }

    // SÉCURITÉ : Mise à jour de la quantité (Validation POST + CSRF)
    public function updateCart()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->updateCartItem($_POST);
        
        $cartData = $this->model->getCartData();
        echo json_encode($cartData);
    }

    // SÉCURITÉ : Ajout d'un article au panier (Validation POST + CSRF)
    public function addToCart($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit(json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']));
        }

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