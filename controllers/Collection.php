<?php

/**
 * Controller Collection
 * Gère l'affichage des listes de produits avec sécurité, filtres et protection CSRF.
 */
class Collection extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // SÉCURITÉ : Initialisation de la session pour générer le jeton CSRF
        Model::sessionInit(); 
    }

    /**
     * Gère l'affichage des collections et des catégories
     * @param string $type Le type (latest, special, exclusive, mostviewed, category)
     * @param int $param1 Numéro de page OU ID de la catégorie
     * @param int $param2 Numéro de page si le type est une catégorie
     */
    public function index($type = 'latest', $param1 = 1, $param2 = 1)
    {
        // SÉCURITÉ : Validation stricte du type de collection (Whitelisting)
        $allowedTypes = ['latest', 'special', 'exclusive', 'mostviewed', 'category'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'latest'; // Fallback sécurisé si l'URL est manipulée
        }

        // 1. Récupération sécurisée des paramètres GET (Filtres)
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        
        // SÉCURITÉ : Limiter les choix possibles pour éviter une surcharge du serveur (DDoS)
        if (!in_array($limit, [20, 40, 60])) {
            $limit = 20; 
        }

        $filters = [
            'in_stock'   => isset($_GET['in_stock']) ? (int)$_GET['in_stock'] : 0,
            'orderType1' => isset($_GET['orderType1']) ? (int)$_GET['orderType1'] : 3,
            'orderType2' => isset($_GET['orderType2']) ? (int)$_GET['orderType2'] : 2,
            'limit'      => $limit
        ];

        $categoryId = 0;
        $page = 1;

        // 2. Adapter les paramètres selon le type de requête
        if ($type === 'category') {
            $categoryId = (int)$param1;
            $page = (int)$param2;
        } else {
            $page = (int)$param1;
        }

        if ($page < 1) {
            $page = 1;
        }
        
        // 3. Calcul de l'offset pour la base de données
        $offset = ($page - 1) * $limit;

        // 4. Récupérer les données depuis le modèle
        $productsData = $this->model->getCollectionProducts($type, $limit, $offset, $categoryId, $filters);
        
        $products = $productsData['products'] ?? [];
        $totalProducts = $productsData['total'] ?? 0;
        $categoryTitle = $productsData['category_title'] ?? '';

        // Application du calcul des prix via le modèle (Respect de l'architecture MVC)
        $products = $this->model->calculateProductsPrices($products);

        // Calcul du nombre total de pages nécessaires
        $totalPages = ceil($totalProducts / $limit);

        // 5. Préparer les données pour la vue
        $data = [
            'type'          => $type,
            'products'      => $products,
            'currentPage'   => $page,
            'totalPages'    => $totalPages,
            'categoryId'    => $categoryId,
            'categoryTitle' => $categoryTitle,
            'filters'       => $filters,
            // SÉCURITÉ CRITIQUE : Génération du jeton pour les boutons "Ajouter au panier" et "Favoris"
            'csrf_token'    => $this->generateCsrfToken() 
        ];

        // Charger la vue finale
        $this->view('collection/collection', $data);
    }
}
?>