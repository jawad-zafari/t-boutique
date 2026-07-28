<?php

/**
 * Contrôleur Product
 * Gère l'affichage d'un produit et les interactions AJAX (Panier, Questions).
 * Architecture 100% sécurisée contre le CSRF et le Method Spoofing.
 */
class Product extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // Initialisation de la session pour gérer le CSRF et l'authentification
        Model::sessionInit(); 
    }

    /**
     * Affiche la page principale du produit
     * * @param int $id L'identifiant du produit
     * @param string $activeTab L'onglet actif par défaut
     */
    public function index($id, $activeTab = 'reviews')
    {
        // PROTECTION CSRF : Utilisation de la méthode de base du contrôleur
        $csrf_token = $this->generateCsrfToken();

        // Sécurisation : on force le type entier (int) pour éviter les injections
        $productId = (int)$id;
        $productInfo = $this->model->productInfo($productId);
        
        // Gestion d'erreur : Redirection si le produit n'existe pas dans la base
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
        
        // Paramètres et scores pour les avis clients
        $commentParam = $this->model->getCommentParameters($idCategory, $productId);
        $commentParamNames = $commentParam[0] ?? [];
        $commentParamScores = $commentParam[1] ?? [];
        
        $comments = $this->model->getProductComments($productId);
        
        // Questions et réponses
        $qaData = $this->model->getQuestionsAndAnswers($productId);
        $questions = $qaData[0] ?? [];
        $answers = $qaData[1] ?? [];

        // Préparation des données pour la vue
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
            'activeTab'      => htmlspecialchars($activeTab, ENT_QUOTES, 'UTF-8'),
            'csrf_token'     => $csrf_token
        ];

        // Appel de la vue
        $this->view('product/product', $data);
    }

    /**
     * Action AJAX : Ajouter un produit au panier
     * * @param int $productId L'identifiant du produit
     */
    public function addToCart($productId)
    {
        // SÉCURITÉ : Définir le type de réponse en JSON
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); // ANTI-CRASH : Nettoie les espaces ou erreurs avant d'envoyer le JSON

        // SÉCURITÉ : Vérifier que la requête utilise la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Méthode HTTP non autorisée.']);
            exit;
        }

        // VÉRIFICATION CSRF (DRY Principle : On utilise la fonction du contrôleur parent)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        // Récupération sécurisée des options du produit
        $colorId = isset($_POST['colorId']) ? (int)$_POST['colorId'] : 0;
        $guaranteeId = isset($_POST['guaranteeId']) ? (int)$_POST['guaranteeId'] : 0;

        // Appel au modèle pour ajouter au panier
        $totalItems = $this->model->addToCart((int)$productId, $colorId, $guaranteeId);
        
        echo json_encode(['status' => 'success', 'totalItems' => (int)$totalItems]);
        exit;
    }

    /**
     * Action AJAX : Ajouter une question sur le produit
     * * @param int $productId L'identifiant du produit
     */
    public function addQuestion($productId)
    {
        // SÉCURITÉ : Définir le type de réponse
        header('Content-Type: application/json; charset=utf-8');
        ob_clean(); // ANTI-CRASH

        // SÉCURITÉ : Validation de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Méthode HTTP non autorisée.']);
            exit;
        }

        // SÉCURITÉ CRITIQUE : Vérifier si l'utilisateur est connecté (Auth Guard)
        $userId = Model::sessionGet('userId');
        if (!$userId) {
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Vous devez être connecté pour poser une question.']);
            exit;
        }

        // VÉRIFICATION CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $questionText = $_POST['question'] ?? '';
        
        // Validation : La question ne doit pas être vide
        if (!empty(trim($questionText))) {
            // ARCHITECTURE MVC : Le contrôleur passe l'ID utilisateur au modèle
            $this->model->addQuestion((int)$productId, $userId, $questionText);
            echo json_encode(['status' => 'success', 'message' => 'Votre question a été soumise avec succès. Elle sera visible après validation.']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Le champ de la question ne peut pas être vide.']);
        }
        exit;
    }
}
?>