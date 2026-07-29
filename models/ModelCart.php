<?php

/**
 * Modèle ModelCart
 * Gère la logique des données du panier (Ajout, Mise à jour, Suppression).
 * Sécurité DWWM : Requêtes préparées PDO et typage strict (Integer Casting).
 */
class ModelCart extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getCartData()
    {
        // Appelle la méthode globale du modèle parent pour récupérer le panier de la session/cookie
        return parent::getCart();
    }

    public function deleteCartItem($cartRowId)
    {
        // SÉCURITÉ CRITIQUE : Transtypage (Casting) en entier pur (int)
        // Empêche toute injection SQL si un utilisateur manipule l'ID dans la requête AJAX
        $safeCartRowId = (int) $cartRowId;
        
        $sql = "DELETE FROM cart_items WHERE id = ?";
        $this->doQuery($sql, [$safeCartRowId]);
    }

    public function updateCartItem($data)
    {
        // SÉCURITÉ : Nettoyage et typage strict des données entrantes
        $quantity = (int) ($data['quantity'] ?? 1);
        $cartRowId = (int) ($data['cartRow'] ?? 0);

        // On s'assure que la quantité et l'ID sont valides avant d'exécuter la requête
        if ($quantity > 0 && $cartRowId > 0) {
            $sql = "UPDATE cart_items SET quantity = ? WHERE id = ?";
            $this->doQuery($sql, [$quantity, $cartRowId]);
        }
    }

    public function addToCart($productId, $quantity = 1, $colorId = 0, $guaranteeId = 0)
    {
        $cookie = parent::getCartCookie();

        $safeProductId = (int)$productId;
        $safeColorId = (int)$colorId;
        $safeGuaranteeId = (int)$guaranteeId;
        $safeQuantity = (int)$quantity;

        // Requête préparée (PDO) pour chercher si le produit exact existe déjà avec les mêmes options
        $sqlCheck = "SELECT id, quantity FROM cart_items WHERE product_id = ? AND session_cookie = ? AND color_id = ? AND guarantee_id = ?";
        $result = $this->doSelect($sqlCheck, [$safeProductId, $cookie, $safeColorId, $safeGuaranteeId]);

        if (!empty($result)) {
            // Si le produit existe déjà, on additionne la quantité
            $newQuantity = (int)$result[0]['quantity'] + $safeQuantity;
            $cartItemId = (int)$result[0]['id'];
            
            $sqlUpdate = "UPDATE cart_items SET quantity = ? WHERE id = ?";
            $this->doQuery($sqlUpdate, [$newQuantity, $cartItemId]);
        } else {
            // Sinon, on crée une nouvelle ligne dans le panier
            $sqlInsert = "INSERT INTO cart_items (session_cookie, product_id, quantity, color_id, guarantee_id) VALUES (?, ?, ?, ?, ?)";
            $this->doQuery($sqlInsert, [$cookie, $safeProductId, $safeQuantity, $safeColorId, $safeGuaranteeId]);
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