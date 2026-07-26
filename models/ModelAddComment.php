<?php

/**
 * Model ModelAddComment
 * Gère la logique des commentaires (Anti-Injection SQL et XSS)
 */
class ModelAddComment extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function productInfo($productId)
    {
        $sql = "SELECT * FROM products WHERE id = ?";
        return $this->doSelect($sql, [(int)$productId], true);
    }

    public function getParam($productId)
    {
        $productInfo = $this->productInfo($productId);
        $categoryId = $productInfo['category_id'] ?? 0;
        
        $sql = "SELECT * FROM review_parameters WHERE category_id = ?";
        return $this->doSelect($sql, [(int)$categoryId]);
    }

    public function saveComment($data, $productId)
    {
        $commentParams = $this->getParam($productId);
        $paramResult = [];
        
        foreach ($commentParams as $row) {
            $paramId = $row['id'];
            // SÉCURITÉ : Forcer la valeur en entier (entre 1 et 5)
            $value = isset($data['param' . $paramId]) ? (int) $data['param' . $paramId] : 3;
            if ($value < 1) $value = 1;
            if ($value > 5) $value = 5;
            $paramResult[$paramId] = $value;
        }
        
        // SÉCURITÉ (Nettoyage des entrées) : On utilise strip_tags pour retirer tout code HTML malveillant.
        // L'échappement final avec htmlspecialchars sera fait dans la Vue (Architecture MVC).
        $title = strip_tags(trim($data['title'] ?? ''));
        $positive = strip_tags(trim($data['positive'] ?? ''));
        $negative = strip_tags(trim($data['negative'] ?? ''));
        $comment = strip_tags(trim($data['comment'] ?? ''));
        
        $createdAt = self::jaliliDate('Y/m/d');

        self::sessionInit();
        $userId = self::sessionGet('userId');

        // Vérifier si l'utilisateur a déjà commenté ce produit
        $sqlCheck = "SELECT * FROM comments WHERE user_id = ? AND product_id = ?";
        $result = $this->doSelect($sqlCheck, [(int)$userId, (int)$productId]);

        if (isset($result[0])) {
            // Mise à jour si le commentaire existe déjà (remise en attente d'approbation)
            $commentId = $result[0]['id'];
            $sqlUpdate = "UPDATE comments SET title = ?, content = ?, positive_points = ?, negative_points = ?, parameters = ?, is_approved = 0 WHERE id = ?";
            $values = [$title, $comment, $positive, $negative, serialize($paramResult), $commentId];
            $this->doQuery($sqlUpdate, $values);
        } else {
            // Insertion d'un tout nouveau commentaire
            $sqlInsert = "INSERT INTO comments (title, content, created_at, positive_points, negative_points, product_id, parameters, user_id, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";
            $values = [$title, $comment, $createdAt, $positive, $negative, (int)$productId, serialize($paramResult), (int)$userId];
            $this->doQuery($sqlInsert, $values);
        }
    }

    public function commentInfo($productId)
    {
        self::sessionInit();
        $userId = self::sessionGet('userId');

        $sql = "SELECT * FROM comments WHERE product_id = ? AND user_id = ?";
        return $this->doSelect($sql, [(int)$productId, (int)$userId], true);
    }
}
?>