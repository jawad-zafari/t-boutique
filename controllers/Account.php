<?php

/**
 * Contrôleur Account
 * Gère l'espace client (Tableau de bord, profil, commandes, favoris).
 * Entièrement sécurisé. TOUTES les méthodes d'origine sont conservées !
 */
class Account extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit();
        $userId = Model::sessionGet('userId');

        // Analyse de l'URL pour identifier la méthode appelée
        $url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];
        $methodName = $url[1] ?? 'index';

        // Liste des méthodes AJAX qui gèrent elles-mêmes leur erreur d'authentification (JSON)
        $ajaxExceptions = ['toggleFavorite'];

        // SÉCURITÉ : Vérification stricte de l'authentification (sauf pour les requêtes AJAX spécifiques)
        if ($userId == false && !in_array($methodName, $ajaxExceptions)) {
            header('Location: ' . URL . 'Login/index');
            exit;
        }
    }

    public function index()
    {
        $userId = Model::sessionGet('userId');
        $userInfo = $this->model->getUserInfo($userId);
        $orders = $this->model->getOrders($userId);
        
        // PROTECTION CSRF : Génération du jeton pour tous les formulaires du tableau de bord
        $data = [
            'userInfo' => $userInfo,
            'orders' => $orders,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('account/account', $data);
    }

    public function saveProfile()
    {
        // SÉCURITÉ CRITIQUE : N'accepter que les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // VÉRIFICATION CSRF : Empêcher les mises à jour de profil non autorisées
        $token = $_POST['csrf_token'] ?? '';
        $this->checkCsrfToken($token);

        $userId = Model::sessionGet('userId');
        $this->model->updateProfile($_POST, $userId);
        
        header('Location: ' . URL . 'Account/index?success=profile');
        exit;
    }

    public function updatePassword()
    {
        // SÉCURITÉ CRITIQUE : N'accepter que les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // VÉRIFICATION CSRF : Protection critique pour le changement de mot de passe
        $token = $_POST['csrf_token'] ?? '';
        $this->checkCsrfToken($token);

        $userId = Model::sessionGet('userId');
        $passOld = $_POST['pass_old'] ?? '';
        $passNew = $_POST['pass_new'] ?? '';
        $passConfirm = $_POST['pass_confirm'] ?? '';

        if ($passNew !== $passConfirm) {
            header('Location: ' . URL . 'Account/index?error=password_mismatch');
            exit;
        }

        if ($this->model->checkOldPassword($userId, $passOld)) {
            $this->model->updatePassword($userId, $passNew);
            header('Location: ' . URL . 'Account/index?success=password');
        } else {
            header('Location: ' . URL . 'Account/index?error=password');
        }
        exit;
    }

    public function deleteAccount()
    {
        // SÉCURITÉ CRITIQUE : N'accepter que les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        // VÉRIFICATION CSRF : Protection contre la suppression forcée du compte
        $token = $_POST['csrf_token'] ?? '';
        $this->checkCsrfToken($token);

        $userId = Model::sessionGet('userId');
        $password = $_POST['password'] ?? '';

        if ($this->model->checkOldPassword($userId, $password)) {
            $this->model->deleteUser($userId);
            header('Location: ' . URL . 'Login/logout');
        } else {
            header('Location: ' . URL . 'Account/index?error=delete');
        }
        exit;
    }

    public function getOrderDetails($orderId)
    {
        // SÉCURITÉ : Lecture seule via AJAX, on force le format JSON et on nettoie les erreurs
        header('Content-Type: application/json');
        ob_clean(); // Anti-Crash JSON

        $userId = Model::sessionGet('userId');
        
        // PROTECTION IDOR : La requête SQL vérifie que la commande appartient à $userId
        $order = $this->model->getOrderById((int)$orderId, $userId);

        if ($order) {
            $cartData = !empty($order['cart_data']) ? unserialize($order['cart_data']) : [];
            echo json_encode([
                'status' => 'success',
                'order' => $order,
                'products' => $cartData
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Commande introuvable ou accès non autorisé.']);
        }
        exit; // Toujours terminer après un JSON
    }

    // =======================================================
    // PAGE ET API DES FAVORIS
    // =======================================================

    public function favorites()
    {
        $userId = Model::sessionGet('userId');
        $favorites = $this->model->getFavorites($userId);
        
        $data = [
            'favorites' => $favorites
        ];
        
        $this->view('account/favorites', $data);
    }

    public function toggleFavorite($productId)
    {
        // SOLUTION ANTI-CRASH : Force le navigateur à lire du JSON pur et ignore les warnings PHP
        header('Content-Type: application/json');
        ob_clean();

        // SÉCURITÉ CRITIQUE : N'accepter que les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
            exit;
        }

        // VÉRIFICATION CSRF AJAX : Vérifier le jeton passé par le JS
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            echo json_encode(['status' => 'error', 'message' => 'Jeton de sécurité invalide.']);
            exit;
        }

        $userId = Model::sessionGet('userId');
        
        if ($userId == false) {
            echo json_encode(['status' => 'unauthorized', 'message' => 'Veuillez vous connecter pour ajouter aux favoris.']);
            exit;
        }

        $action = $this->model->toggleFavorite($userId, (int)$productId);
        
        $favCountResult = $this->model->doSelect("SELECT COUNT(*) as total FROM favorites WHERE user_id = ?", [$userId]);
        $favCount = $favCountResult[0]['total'] ?? 0;
        
        echo json_encode([
            'status' => 'success', 
            'action' => $action, 
            'message' => $action === 'added' ? 'Produit ajouté à vos favoris !' : 'Produit retiré de vos favoris.',
            'favCount' => $favCount
        ]);
        exit; // Toujours terminer l'exécution après un echo JSON
    }
}
?>