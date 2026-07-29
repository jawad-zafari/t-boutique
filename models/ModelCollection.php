<?php

/**
 * Model ModelCollection
 * Gère les requêtes dynamiques pour les collections.
 * Architecture sécurisée : PDO, Typage strict (Casting), Liste blanche (Whitelisting).
 */
class ModelCollection extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupère les produits d'une collection ou catégorie avec filtres et pagination
     */
    public function getCollectionProducts($type, $limit, $offset, $categoryId = 0, $filters = [])
    {
        $products = [];
        $total = 0;
        $categoryTitle = '';

        $whereClauses = [];
        $params = [];

        // ÉTAPE 1 : Condition de base selon le type de collection
        if ($type === 'category') {
            // Utilisation de requêtes préparées (?) pour éviter les injections SQL
            $whereClauses[] = "(category_id = ? OR secondary_category_id = ?)";
            $params[] = (int)$categoryId;
            $params[] = (int)$categoryId;

            // Récupérer le nom de la catégorie pour le fil d'Ariane
            $sqlCat = "SELECT title FROM categories WHERE id = ?";
            $catRes = $this->doSelect($sqlCat, [(int)$categoryId]);
            if (!empty($catRes)) { 
                $categoryTitle = $catRes[0]['title']; 
            }
            
        } elseif ($type === 'special') {
            $whereClauses[] = "(discount_percent > 0 OR is_special_offer = 1)";
        } elseif ($type === 'exclusive') {
            $whereClauses[] = "is_exclusive = 1";
        }

        // ÉTAPE 2 : Application des filtres utilisateur (Venant du GET)
        if (isset($filters['in_stock']) && $filters['in_stock'] == 1) {
            $whereClauses[] = "stock_quantity > 0";
        }

        $whereSql = "";
        if (count($whereClauses) > 0) {
            $whereSql = "WHERE " . implode(" AND ", $whereClauses);
        }

        // ÉTAPE 3 : Gestion du Tri (ORDER BY) - SÉCURITÉ MAJEURE (Whitelisting)
        // Empêche un attaquant de passer des requêtes SQL malveillantes via l'URL
        $orderCol = "id"; 
        if (isset($filters['orderType1'])) {
            if ($filters['orderType1'] == 1) $orderCol = "price";
            if ($filters['orderType1'] == 2) $orderCol = "views";
        }

        $orderDir = "DESC"; 
        if (isset($filters['orderType2'])) {
            if ($filters['orderType2'] == 1) $orderDir = "ASC";
        }

        if ($type === 'mostviewed' && !isset($_GET['orderType1'])) {
            $orderCol = "views";
            $orderDir = "DESC";
        }

        // ÉTAPE 4 : Exécution des requêtes SQL sécurisées (PDO)
        
        $sqlCount = "SELECT COUNT(id) as total FROM products $whereSql";
        $resultCount = $this->doSelect($sqlCount, $params);
        if (!empty($resultCount)) { 
            $total = (int)$resultCount[0]['total']; 
        }

        // SÉCURITÉ : Forçage des variables limit et offset en entiers (Integer Casting) pour bloquer toute injection
        $safeLimit = (int)$limit;
        $safeOffset = (int)$offset;

        // Concaténation de variables sûres (Whitelisting et Casting)
        $sqlData = "SELECT * FROM products $whereSql ORDER BY $orderCol $orderDir LIMIT $safeLimit OFFSET $safeOffset";
        $products = $this->doSelect($sqlData, $params);

        return [
            'products'       => $products,
            'total'          => $total,
            'category_title' => $categoryTitle
        ];
    }
}
?>