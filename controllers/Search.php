<?php

/**
 * Controller Search
 * Gère la recherche et les filtres dynamiques de manière sécurisée (Validation des requêtes POST).
 */
class Search extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index($categoryId = 0)
    {
        // SÉCURITÉ : Cast en entier pour éviter les injections via l'URL
        $categoryId = (int)$categoryId;
        
        $attributes = $this->model->getAttr($categoryId);
        $attributesRight = $this->model->getAttrRight($categoryId);
        $colors = $this->model->getColors();
        
        // SÉCURITÉ : Assainissement du mot-clé saisi par l'utilisateur (Anti-XSS)
        $keyword = isset($_POST['keyword']) ? strip_tags(trim($_POST['keyword'])) : '';
        
        $data = [
            'attr'       => $attributes, 
            'attrRight'  => $attributesRight, 
            'colors'     => $colors,
            'categoryId' => $categoryId,
            'keyword'    => $keyword,
            // AJOUT DU JETON CSRF POUR AUTORISER LE PANIER
            'csrf_token' => $this->generateCsrfToken() 
        ];
        
        $this->view('search/search', $data);
    }

    /**
     * Méthode appelée via AJAX pour récupérer les résultats filtrés
     */
    public function doSearch()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct (GET) à cette méthode
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['error' => 'Méthode non autorisée. Veuillez utiliser POST.']);
            exit;
        }

        $result = $this->model->doSearch($_POST);
        echo json_encode($result);
    }

    /**
     * Méthode pour l'auto-complétion (Suggestions en direct dans le Header)
     */
    public function autoSuggest()
    {
        // SÉCURITÉ : Bloquer l'accès GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['error' => 'Méthode non autorisée.']);
            exit;
        }

        $keyword = isset($_POST['keyword']) ? strip_tags(trim($_POST['keyword'])) : '';
        
        $results = $this->model->suggestProducts($keyword);
        echo json_encode($results);
    }
}
?>