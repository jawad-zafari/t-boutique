<?php

class Product extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Initialisation de la session
        Model::sessionInit(); 
    }

    
    //  Affiche la page principale d'un produit
    
    public function index($id, $activeTab = 'reviews')
    {
        // PROTECTION CSRF
        $csrf_token = $this->generateCsrfToken();

        $productId = (int)$id;
        $productInfo = $this->model->productInfo($productId);
        
        // Gestion d'erreur
        if (empty($productInfo)) {
            header('Location: ' . URL . 'Index/index');
            exit;
        }

        // Récupération des données associées au produit
        $exclusives = $this->model->getExclusiveProducts();
        $gallery = $this->model->getGallery($productId);

        $idCategory = (int)($productInfo['category_id'] ?? 0);
        $expertReviews = $this->model->getExpertReviews($productId);
        $specifications = $this->model->getTechnicalSpecs($idCategory, $productId);
        
        // Récupération des paramètres et des scores pour les avis clients
        $commentParam = $this->model->getCommentParameters($idCategory, $productId);
        $commentParamNames = $commentParam[0] ?? [];
        $commentParamScores = $commentParam[1] ?? [];
        
        $comments = $this->model->getProductComments($productId);
        
        // Récupération des questions et des réponses associées
        $qaData = $this->model->getQuestionsAndAnswers($productId);
        $questions = $qaData[0] ?? [];
        $answers = $qaData[1] ?? [];

        // Préparation des données pour la vue (Respect de l'architecture MVC)
        $data = [
            'productInfo'    => $productInfo,
            'exclusives'     => $exclusives,
            'gallery'        => $gallery,
            'reviews'        => $expertReviews,
            'specs'          => $specifications,
            'comment_params' => $commentParamNames,
            'comment_scores' => $commentParamScores,
            'comments'       => $comments,
            'questions'      => $questions,
            'answers'        => $answers,
            // Protection XSS sur la variable de l'onglet actif
            'activeTab'      => htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8'),
            'csrf_token'     => $csrf_token
        ];

        // Chargement de la vue
        $this->view('product/product', $data);
    }

    
    //  Ajoute un produit au panier de l'utilisateur

    public function addToCart($productId)
    {
        // Définir le type de réponse attendu en JSON
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); 

        // Rejeter toute requête qui n'est pas de type POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Méthode HTTP non autorisée.']);
            exit;
        }

        // Vérification du jeton CSRF pour éviter les attaques intersites
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        // Récupération sécurisée des options sélectionnées par l'utilisateur
        $colorId = isset($_POST['colorId']) ? (int)$_POST['colorId'] : 0;
        $guaranteeId = isset($_POST['guaranteeId']) ? (int)$_POST['guaranteeId'] : 0;

        // Appel au modèle pour gérer l'insertion ou la mise à jour dans le panier
        $totalItems = $this->model->addToCart((int)$productId, $colorId, $guaranteeId);
        
        echo json_encode(['status' => 'success', 'totalItems' => (int)$totalItems]);
        exit;
    }

    // Soumet une nouvelle question pour le produit
    public function addQuestion($productId)
    {
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); 

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Méthode HTTP non autorisée.']);
            exit;
        }

        // Vérification de l'authentification
        $userId = Model::sessionGet('userId');
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour poser une question.']);
            exit;
        }

        // Vérification du jeton CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $questionText = $_POST['question'] ?? '';
        
        if (!empty(trim($questionText))) {
            // L'ID utilisateur est géré par la session
            $this->model->addQuestion((int)$productId, (int)$userId, $questionText);
            echo json_encode(['status' => 'success', 'message' => 'Votre question a été soumise avec succès. Elle sera visible après validation par notre équipe.']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Le champ de la question ne peut pas être vide.']);
        }
        exit;
    }
}
?>