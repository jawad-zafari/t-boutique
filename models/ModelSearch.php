<?php

/**
 * Modèle ModelSearch
 * Gère les requêtes de recherche complexes avec filtrage dynamique et pagination native.
 * Sécurité maximale : Requêtes préparées PDO, Liste blanche (Whitelisting) et Typage.
 */
class ModelSearch extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupère les attributs de filtre pour la catégorie donnée
     */
    public function getAttr($categoryId)
    {
        $sql = "SELECT * FROM attributes WHERE category_id = ? AND is_filter = 1";
        $result = $this->doSelect($sql, [(int)$categoryId]);
        
        foreach ($result as $key => $row) {
            $sqlValues = "SELECT * FROM attribute_values WHERE attribute_id = ?";
            $result[$key]['values'] = $this->doSelect($sqlValues, [(int)$row['id']]);
        }
        return $result;
    }

    /**
     * Récupère les attributs secondaires de filtre
     */
    public function getAttrRight($categoryId)
    {
        $sql = "SELECT * FROM attributes WHERE category_id = ? AND is_right_filter = 1";
        $result = $this->doSelect($sql, [(int)$categoryId]);

        foreach ($result as $key => $row) {
            $sqlValues = "SELECT * FROM attribute_values WHERE attribute_id = ?";
            $result[$key]['values'] = $this->doSelect($sqlValues, [(int)$row['id']]);
        }
        return $result;
    }

    /**
     * Récupère la liste de toutes les couleurs disponibles
     */
    public function getColors()
    {
        $sql = "SELECT * FROM colors";
        return $this->doSelect($sql);
    }

    /**
     * Moteur de recherche principal avec filtrage dynamique et pagination
     */
    public function doSearch($data)
    {
        // 1. Assainissement et conversion stricte des paramètres (Anti-XSS et Type Casting)
        $keyword = isset($data['keyword']) ? htmlspecialchars(trim($data['keyword']), ENT_QUOTES, 'UTF-8') : '';
        $categoryId = isset($data['categoryId']) ? (int)$data['categoryId'] : 0;
        $inStock = isset($data['in_stock']) ? (int)$data['in_stock'] : 0;
        
        $orderType1 = isset($data['orderType1']) ? (int)$data['orderType1'] : 3; 
        $orderType2 = isset($data['orderType2']) ? (int)$data['orderType2'] : 2; 

        $currentPage = isset($data['current_page']) ? (int)$data['current_page'] : 1;
        if ($currentPage < 1) { $currentPage = 1; }

        // SÉCURITÉ CRITIQUE : Liste blanche (Whitelisting) stricte pour la limite par page
        $limit = isset($data['limit']) ? (int)$data['limit'] : 20;
        if (!in_array($limit, [20, 40, 60])) { $limit = 20; }

        $offset = ($currentPage - 1) * $limit;

        // 2. Construction dynamique des clauses WHERE
        $whereClauses = ["1=1"];
        $params = [];

        if (!empty($keyword)) {
            $whereClauses[] = "title LIKE ?";
            $params[] = '%' . $keyword . '%';
        }

        if ($categoryId > 0) {
            $whereClauses[] = "(category_id = ? OR secondary_category_id = ?)";
            $params[] = $categoryId;
            $params[] = $categoryId;
        }

        if ($inStock == 1) {
            $whereClauses[] = "stock_quantity > 0"; 
        }

        $whereSql = implode(" AND ", $whereClauses);

        // 3. Calcul du nombre total de résultats
        $sqlCount = "SELECT COUNT(id) as total FROM products WHERE $whereSql";
        $resultCount = $this->doSelect($sqlCount, $params, true);
        $totalProducts = (int)($resultCount['total'] ?? 0);

        // 4. Tri et récupération (Validation stricte de l'ordre pour éviter l'injection SQL)
        $orderBy = "id";
        if ($orderType1 == 1) { $orderBy = "price"; }
        if ($orderType1 == 2) { $orderBy = "views"; }
        
        $orderDir = ($orderType2 == 2) ? "DESC" : "ASC";
        
        // $limit et $offset sont déjà forcés en (int), donc l'injection est impossible ici
        $sqlData = "SELECT * FROM products WHERE $whereSql ORDER BY $orderBy $orderDir LIMIT $limit OFFSET $offset";
        $productsRaw = $this->doSelect($sqlData, $params);
        
        // Calcul des remises et prix finaux pour chaque produit
        $products = $this->calculateProductsPrices($productsRaw);

        // Calcul du nombre de pages
        $pageNumber = ($totalProducts > 0) ? ceil($totalProducts / $limit) : 1;

        return [$products, $pageNumber];
    }

    /**
     * Recherche instantanée (Auto-suggestion)
     */
    public function suggestProducts($keyword)
    {
        $safeKeyword = htmlspecialchars(trim($keyword), ENT_QUOTES, 'UTF-8');
        
        // Utilisation de mb_strlen pour compter correctement les caractères spéciaux (ex: accents)
        if (mb_strlen($safeKeyword, 'UTF-8') < 2) {
            return [];
        }
        
        $sql = "SELECT * FROM products WHERE title LIKE ? LIMIT 5";
        $results = $this->doSelect($sql, ['%' . $safeKeyword . '%']);
        
        return $this->calculateProductsPrices($results);
    }

    /**
     * Méthode auxiliaire pour calculer les prix remisés
     */
    public function calculateProductsPrices($products)
    {
        if (empty($products)) {
            return [];
        }

        foreach ($products as $key => $product) {
            $price = (float)($product['price'] ?? 0);
            $discount = (float)($product['discount_percent'] ?? 0);
            
            $priceCalculate = $this->calculateDiscount($price, $discount);
            $products[$key]['price_discount'] = $priceCalculate[0];
            $products[$key]['price_total'] = $priceCalculate[1];
        }

        return $products;
    }
}
?>