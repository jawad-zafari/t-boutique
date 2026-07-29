<?php

/**
 * Contrôleur Account
 * Gère l'espace client (Tableau de bord, profil, commandes, favoris).
 * Entièrement sécurisé. Règle MVC respectée (calculs dans le contrôleur).
 */
class Account extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit();
        $userId = Model::sessionGet('userId');

        $url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];
        $methodName = $url[1] ?? 'index';

        $ajaxExceptions = ['toggleFavorite'];

        // SÉCURITÉ : Vérification de l'authentification
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
        
        // ARCHITECTURE MVC : On fait les calculs ici (Contrôleur) et non dans la Vue (HTML)
        $totalOrdersCount = count($orders);
        $totalSpent = 0;
        foreach($orders as $o) {
            if(isset($o['is_paid']) && $o['is_paid'] == 1) {
                $totalSpent += ($o['total_amount'] ?? 0);
            }
        }
        $latestOrder = $orders[0] ?? null;

        $data = [
            'userInfo' => $userInfo,
            'orders' => $orders,
            'totalOrdersCount' => $totalOrdersCount,
            'totalSpent' => $totalSpent,
            'latestOrder' => $latestOrder,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('account/account', $data);
    }

    public function saveProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $token = $_POST['csrf_token'] ?? '';
        $this->checkCsrfToken($token);

        $userId = Model::sessionGet('userId');
        $this->model->updateProfile($_POST, $userId);
        
        header('Location: ' . URL . 'Account/index?success=profile');
        exit;
    }

    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

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
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

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
        header('Content-Type: application/json');
        ob_clean(); 

        $userId = Model::sessionGet('userId');
        $order = $this->model->getOrderById((int)$orderId, $userId);

        if ($order) {
            // Sécurité : PHP Object Injection prévenue par allowed_classes
            $cartData = !empty($order['cart_data']) ? unserialize($order['cart_data'], ['allowed_classes' => false]) : [];
            
            echo json_encode([
                'status' => 'success',
                'order' => $order,
                'products' => $cartData
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Commande introuvable ou accès non autorisé.']);
        }
        exit; 
    }

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
        header('Content-Type: application/json');
        ob_clean();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
            exit;
        }

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
        exit; 
    }
}
?>