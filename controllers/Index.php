<?php

/**
 * Controller: Index (Page d'accueil)
 * Gère le chargement de toutes les sections dynamiques de la page d'accueil.
 * Sécurité: Ajout du jeton CSRF pour sécuriser les actions AJAX (Panier, Favoris)
 */
class Index extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // SÉCURITÉ : Initialisation de la session pour la gestion du jeton CSRF
        Model::sessionInit();
    }

    public function index()
    {
        // 1. Récupération des données dynamiques via le modèle
        $slider1 = $this->model->getMainSliders();
        $slider2 = $this->model->getSpecialOffers();
        $exclusives = $this->model->getExclusiveProducts();
        $mostViewed = $this->model->getMostViewedProducts();
        $latestProducts = $this->model->getLatestProducts();
        
        // Nouvelles sections dynamiques (Actualités, Marques, Boutique TV)
        $latestNews = $this->model->getLatestNews();
        $brands = $this->model->getBrands();
        $tvSettings = $this->model->getTvSettings();
        
        // Séparation des données du slider des offres spéciales (Produits et Date de fin)
        $slider2Items = $slider2[0] ?? [];
        $dateEnd = $slider2[1] ?? '';

        // 2. Calcul des prix et réductions via le modèle (Respect strict de l'architecture MVC)
        $slider2Items = $this->model->calculateProductsPrices($slider2Items);
        $latestProducts = $this->model->calculateProductsPrices($latestProducts);
        $exclusives = $this->model->calculateProductsPrices($exclusives);
        $mostViewed = $this->model->calculateProductsPrices($mostViewed);

        // 3. Préparation du tableau de données à envoyer à la Vue
        $data = [
            'slider1'         => $slider1, 
            'slider2_items'   => $slider2Items,
            'date_end'        => $dateEnd,
            'exclusives'      => $exclusives,
            'most_viewed'     => $mostViewed,
            'latest_products' => $latestProducts,
            'latest_news'     => $latestNews,
            'brands'          => $brands,
            'tv_settings'     => $tvSettings,
            // SÉCURITÉ CRITIQUE : Génération du jeton pour protéger les actions sur la page d'accueil
            'csrf_token'      => $this->generateCsrfToken()
        ];

        // 4. Affichage de la vue
        $this->view('index/index', $data);
    }
}
?>