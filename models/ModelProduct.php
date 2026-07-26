<?php

/**
 * Modèle ModelProduct
 * Gestion sécurisée des requêtes SQL pour la page produit.
 * Utilise PDO avec requêtes préparées pour éviter les injections SQL.
 */
class ModelProduct extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Recherche la meilleure image disponible pour le produit.
     */
    public function findProductImage($id, $size = 350) 
    {
        $basePath = 'public/images/products/' . (int)$id . '/product_' . (int)$size;
        $extensions = ['jpg', 'webp', 'png', 'jpeg'];
        
        foreach ($extensions as $ext) {
            if (file_exists($basePath . '.' . $ext)) {
                return URL . $basePath . '.' . $ext . '?v=' . time();
            }
        }
        return 'https://placehold.co/' . (int)$size . 'x' . (int)$size . '/f8f9fa/adb5bd?text=Image';
    }

    /**
     * Récupère toutes les informations d'un produit spécifique.
     */
    public function productInfo($id)
    {
        $id = (int)$id; 
        
        // Incrémentation du nombre de vues
        $sqlUpdateView = "UPDATE products SET views = views + 1 WHERE id = ?";
        $this->doQuery($sqlUpdateView, [$id]);

        $sql = "SELECT * FROM products WHERE id = ?";
        $result = $this->doSelect($sql, [$id], true);
        
        if (!$result) return [];

        // Calcul des prix et remises
        $price = $result['price'] ?? 0;
        $discount = $result['discount_percent'] ?? 0;
        $priceCalculate = $this->calculateDiscount($price, $discount);
        $result['price_discount'] = $priceCalculate[0];
        $result['price_total'] = $priceCalculate[1];

        // Calcul de la date d'expiration pour l'offre spéciale
        $timeSpecial = $result['special_offer_expires_at'] ?? 0;
        $options = self::getoption();
        $durationSpecial = $options['special_time'] ?? 0;
        $timeEnd = $timeSpecial + $durationSpecial;
        
        date_default_timezone_set('Europe/Paris');
        $result['date_special'] = date('F d,Y H:i:s', $timeEnd);

        // Récupération des couleurs
        $sqlColors = "SELECT * FROM product_colors pc JOIN colors c ON pc.color_id = c.id WHERE pc.product_id = ?";
        $result['colors'] = $this->doSelect($sqlColors, [$id]);

        // Récupération des garanties
        $sqlGuarantees = "SELECT * FROM product_guarantees pg JOIN guarantees g ON pg.guarantee_id = g.id WHERE pg.product_id = ?";
        $result['guarantees'] = $this->doSelect($sqlGuarantees, [$id]);

        return $result;
    }

    /**
     * Récupère les produits marqués comme exclusifs (Limite : 5).
     */
    public function getExclusiveProducts()
    {
        $sql = "SELECT * FROM products WHERE is_exclusive = 1 ORDER BY id DESC LIMIT 5";
        return $this->doSelect($sql);
    }

    /**
     * Récupère la galerie d'images du produit.
     */
    public function getGallery($id)
    {
        // On classe par "is_main" pour afficher l'image principale en premier si elle existe
        $sql = "SELECT * FROM product_galleries WHERE product_id = ? ORDER BY is_main DESC";
        return $this->doSelect($sql, [(int)$id]);
    }

    /**
     * Récupère les avis des experts.
     */
    public function getExpertReviews($id)
    {
        $sql = "SELECT * FROM product_reviews WHERE product_id = ?";
        return $this->doSelect($sql, [(int)$id]);
    }

    /**
     * Récupère les spécifications techniques.
     */
    public function getTechnicalSpecs($categoryId, $productId)
    {
        $sql = "SELECT a.title, av.value FROM attributes a LEFT JOIN attribute_values av ON a.id = av.attribute_id AND av.product_id = ? WHERE a.category_id = ?";
        return $this->doSelect($sql, [(int)$productId, (int)$categoryId]);
    }

    /**
     * Récupère les critères d'évaluation et les scores moyens.
     */
    public function getCommentParameters($categoryId, $productId)
    {
        $sqlParams = "SELECT * FROM review_parameters WHERE category_id = ?";
        $params = $this->doSelect($sqlParams, [(int)$categoryId]);

        $sqlScores = "SELECT parameter_id, AVG(score) as avg_score FROM comment_scores cs JOIN comments c ON cs.comment_id = c.id WHERE c.product_id = ? GROUP BY parameter_id";
        $scoresRaw = $this->doSelect($sqlScores, [(int)$productId]);
        
        $scores = [];
        foreach ($scoresRaw as $row) {
            $scores[$row['parameter_id']] = $row['avg_score'];
        }

        return [$params, $scores];
    }

    /**
     * Récupère les commentaires validés par l'administrateur.
     */
    public function getProductComments($id)
    {
        // La colonne `username` est utilisée comme prénom d'utilisateur
        $sql = "SELECT c.*, u.username as first_name, u.last_name FROM comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.product_id = ? AND c.is_approved = 1 ORDER BY c.id DESC";
        return $this->doSelect($sql, [(int)$id]);
    }

    /**
     * Récupère les questions et leurs réponses associées.
     */
    public function getQuestionsAndAnswers($id)
    {
        // Récupérer les questions (parent_id = 0)
        $sqlQ = "SELECT * FROM questions WHERE product_id = ? AND parent_id = 0 AND is_approved = 1 ORDER BY id DESC";
        $questions = $this->doSelect($sqlQ, [(int)$id]);

        // Récupérer les réponses associées
        $sqlA = "SELECT * FROM questions WHERE product_id = ? AND parent_id != 0 AND is_approved = 1";
        $answersRaw = $this->doSelect($sqlA, [(int)$id]);
        
        $answers = [];
        foreach ($answersRaw as $ans) {
            // Le parent_id correspond à l'ID de la question
            $answers[$ans['parent_id']] = $ans;
        }

        return [$questions, $answers];
    }

    /**
     * Ajoute un produit au panier (Gère les quantités si le produit existe déjà).
     */
    public function addToCart($productId, $colorId, $guaranteeId)
    {
        $cookie = parent::getCartCookie();
        
        // Vérifier si le produit avec ces options exactes existe déjà dans le panier
        $sqlCheck = "SELECT * FROM cart_items WHERE session_cookie = ? AND product_id = ? AND color_id = ? AND guarantee_id = ?";
        $params = [$cookie, (int)$productId, (int)$colorId, (int)$guaranteeId];
        $result = $this->doSelect($sqlCheck, $params);

        if (isset($result[0])) {
            // Mise à jour de la quantité
            $sql = "UPDATE cart_items SET quantity = quantity + 1 WHERE session_cookie = ? AND product_id = ? AND color_id = ? AND guarantee_id = ?";
        } else {
            // Insertion d'une nouvelle ligne
            $sql = "INSERT INTO cart_items (session_cookie, product_id, quantity, color_id, guarantee_id) VALUES (?, ?, 1, ?, ?)";
        }
        $this->doQuery($sql, $params);

        // Retourner le nombre total d'articles dans le panier
        $sqlCount = "SELECT SUM(quantity) as total FROM cart_items WHERE session_cookie = ?";
        $countResult = $this->doSelect($sqlCount, [$cookie], true);
        return $countResult['total'] ?? 0;
    }

    /**
     * Enregistre une nouvelle question dans la base de données.
     */
    public function addQuestion($productId, $questionText)
    {
        self::sessionInit();
        $userId = (int)self::sessionGet('userId'); 
        
        // SÉCURITÉ MINEURE : Bloquer l'insertion si l'ID utilisateur est invalide
        if ($userId <= 0) {
            return;
        }

        $createdAt = date('Y-m-d H:i:s');
        
        // SÉCURITÉ ANTI-XSS : Nettoyage strict des balises HTML
        $safeContent = strip_tags(trim($questionText));
        
        $sql = "INSERT INTO questions (content, product_id, user_id, parent_id, is_approved, created_at) VALUES (?, ?, ?, 0, 0, ?)";
        $this->doQuery($sql, [$safeContent, (int)$productId, $userId, $createdAt]);
    }
}
?>