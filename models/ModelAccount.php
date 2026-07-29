<?php

/**
 * Modèle ModelAccount
 * Gère les données de l'espace client.
 * Requêtes sécurisées via PDO.
 */
class ModelAccount extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getUserInfo($userId)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $result = $this->doSelect($sql, [(int)$userId]);
        return $result[0] ?? [];
    }

    public function updateProfile($data, $userId)
    {
        // RÈGLE MVC : PDO sécurise contre les injections SQL (Prepared Statements).
        // L'échappement XSS (htmlspecialchars) se fait uniquement dans la vue pour éviter le double encodage.
        $username = trim($data['username'] ?? '');
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $lastName = trim($data['last_name'] ?? '');
        $mobile = trim($data['mobile'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $address = trim($data['address'] ?? '');
        $city = trim($data['city'] ?? '');
        $postalCode = trim($data['postal_code'] ?? '');
        $gender = (int) ($data['gender'] ?? 1); 
        $newsletter = isset($data['newsletter']) ? 1 : 0;

        $sql = "UPDATE users SET username = ?, email = ?, last_name = ?, mobile = ?, phone = ?, address = ?, city = ?, postal_code = ?, gender = ?, newsletter = ? WHERE id = ?";
        $this->doQuery($sql, [$username, $email, $lastName, $mobile, $phone, $address, $city, $postalCode, $gender, $newsletter, (int)$userId]);
    }

    public function checkOldPassword($userId, $oldPassword)
    {
        $sql = "SELECT password FROM users WHERE id = ?";
        $result = $this->doSelect($sql, [(int)$userId]);
        
        if (!empty($result)) {
            $hashedPassword = $result[0]['password'];
            return password_verify($oldPassword, $hashedPassword);
        }
        
        return false;
    }

    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $this->doQuery($sql, [$hashedPassword, (int)$userId]);
    }

    public function deleteUser($userId)
    {
        $sql = "DELETE FROM users WHERE id = ?";
        $this->doQuery($sql, [(int)$userId]);
    }

    public function getOrders($userId)
    {
        $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC";
        return $this->doSelect($sql, [(int)$userId]);
    }

    public function getOrderById($orderId, $userId)
    {
        // PROTECTION IDOR : Vérification stricte du user_id
        $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
        $result = $this->doSelect($sql, [(int)$orderId, (int)$userId], true);
        return $result;
    }

    public function toggleFavorite($userId, $productId)
    {
        $sqlCheck = "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?";
        $exists = $this->doSelect($sqlCheck, [(int)$userId, (int)$productId]);

        if (!empty($exists)) {
            $sqlDelete = "DELETE FROM favorites WHERE id = ?";
            $this->doQuery($sqlDelete, [$exists[0]['id']]);
            return 'removed';
        } else {
            $sqlInsert = "INSERT INTO favorites (user_id, product_id, folder_id, title) VALUES (?, ?, 0, '')";
            $this->doQuery($sqlInsert, [(int)$userId, (int)$productId]);
            return 'added';
        }
    }

    public function getFavorites($userId)
    {
        $sql = "SELECT p.*, f.id as favorite_id 
                FROM favorites f 
                INNER JOIN products p ON f.product_id = p.id 
                WHERE f.user_id = ? 
                ORDER BY f.id DESC";
        $products = $this->doSelect($sql, [(int)$userId]);
        
        if (method_exists($this, 'calculateProductsPrices')) {
            return $this->calculateProductsPrices($products);
        }
        return $products;
    }
}
?>