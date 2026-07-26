<?php

/**
 * Model ModelCart
 * Gère la logique des données du panier 
 * (Parfaitement sécurisé contre les injections SQL via PDO et le casting strict)
 */
class ModelCart extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCartData()
    {
        return parent::getCart();
    }

    public function deleteCartItem($cartRowId)
    {
        // SÉCURITÉ CRITIQUE : Forcer en entier (Integer Casting)
        // Bloque toute tentative d'injection SQL dans l'URL ou la requête Ajax
        $cartRowId = (int) $cartRowId;
        $sql = "DELETE FROM cart_items WHERE id = ?";
        $this->doQuery($sql, [$cartRowId]);
    }

    public function updateCartItem($data)
    {
        // SÉCURITÉ : Nettoyage et typage strict des données entrantes
        $quantity = (int) ($data['quantity'] ?? 1);
        $cartRowId = (int) ($data['cartRow'] ?? 0);

        if ($quantity > 0 && $cartRowId > 0) {
            $sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
            $this->doQuery($sql, [$quantity, $cartRowId]);
        }
    }

    public function addToCart($productId, $quantity = 1, $colorId = 0, $guaranteeId = 0)
    {
        $cookie = parent::getCartCookie();

        // Requête préparée (PDO) pour chercher si le produit exact existe déjà
        $sqlCheck = "SELECT id, quantity FROM cart_items WHERE product_id = ? AND session_cookie = ? AND color_id = ? AND guarantee_id = ?";
        $result = $this->doSelect($sqlCheck, [(int)$productId, $cookie, (int)$colorId, (int)$guaranteeId]);

        if (!empty($result)) {
            $newQuantity = (int)$result[0]['quantity'] + (int)$quantity;
            $cartItemId = (int)$result[0]['id'];
            
            $sqlUpdate = "UPDATE cart_items SET quantity = ? WHERE id = ?";
            $this->doQuery($sqlUpdate, [$newQuantity, $cartItemId]);
        } else {
            $sqlInsert = "INSERT INTO cart_items (session_cookie, product_id, quantity, color_id, guarantee_id) VALUES (?, ?, ?, ?, ?)";
            $this->doQuery($sqlInsert, [$cookie, (int)$productId, (int)$quantity, (int)$colorId, (int)$guaranteeId]);
        }

        return $this->getCartTotalCount();
    }

    public function getCartTotalCount()
    {
        $cookie = parent::getCartCookie();
        
        $sql = "SELECT SUM(quantity) as total FROM cart_items WHERE session_cookie = ?";
        $result = $this->doSelect($sql, [$cookie]);
        
        if (!empty($result) && isset($result[0]['total']) && $result[0]['total'] !== null) {
            return (int)$result[0]['total'];
        }
        
        return 0;
    }
}
?>