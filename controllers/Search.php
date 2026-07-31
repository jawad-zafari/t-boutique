<?php

/**
 * Contrôleur Search
 * Gère la recherche et les filtres dynamiques de manière sécurisée.
 * Architecture : Vérification stricte des requêtes POST, protection CSRF ciblée et formatage JSON.
 */
class Search extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // SÉCURITÉ : Initialisation de la session pour la vérification du jeton CSRF
        Model::sessionInit();
    }

    /**
     * Page principale de recherche
     */
    public function index($categoryId = 0)
    {
        // SÉCURITÉ : Cast en entier pour éviter les injections via l'URL
        $categoryId = (int)$categoryId;
        
        $attributes = $this->model->getAttr($categoryId);
        $attributesRight = $this->model->getAttrRight($categoryId);
        $colors = $this->model->getColors();
        
        // SÉCURITÉ : Assainissement strict du mot-clé (Anti-XSS)
        $keyword = isset($_POST['keyword']) ? htmlspecialchars(trim($_POST['keyword']), ENT_QUOTES, 'UTF-8') : '';
        
        $data = [
            'attr'       => $attributes, 
            'attrRight'  => $attributesRight, 
            'colors'     => $colors,
            'categoryId' => $categoryId,
            'keyword'    => $keyword,
            // AJOUT DU JETON CSRF POUR PROTÉGER LES REQUÊTES AJAX (Filtres et Panier)
            'csrf_token' => $this->generateCsrfToken() 
        ];
        
        $this->view('search/search', $data);
    }

    /**
     * Méthode appelée via AJAX pour récupérer les résultats filtrés
     */
    public function doSearch()
    {
        // SÉCURITÉ : Définir le type de contenu et prévenir le crash JSON
        header('Content-Type: application/json; charset=utf-8');
        ob_clean();

        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct (GET)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée. Veuillez utiliser POST.']);
            exit;
        }

        // SÉCURITÉ : Vérification obligatoire du jeton CSRF pour les filtres complexes
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $result = $this->model->doSearch($_POST);
        echo json_encode($result);
        exit;
    }

    /**
     * Méthode pour l'auto-complétion (Suggestions en direct dans le Header)
     */
    public function autoSuggest()
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_clean();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée.']);
            exit;
        }

        // CORRECTION : Retrait de la vérification CSRF ici.
        // L'auto-complétion est une action "Read-Only" (Lecture seule).
        // Exiger un jeton CSRF bloquait la barre de recherche du Header.

        // Assainissement du mot-clé
        $keyword = isset($_POST['keyword']) ? htmlspecialchars(trim($_POST['keyword']), ENT_QUOTES, 'UTF-8') : '';
        
        $results = $this->model->suggestProducts($keyword);
        echo json_encode($results);
        exit;
    }
}
?>