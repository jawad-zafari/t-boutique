<?php

/**
 * Contrôleur Order
 * Gère le tunnel d'achat optimisé en 2 étapes (Livraison, Paiement).
 * Sécurité stricte : Vérification du panier, requêtes POST obligatoires, et protection CSRF.
 */
class Order extends Controller 
{
    public function __construct() 
    {
        parent::__construct();
        Model::sessionInit(); 
    }

    private function checkLogin()
    {
        $userId = Model::sessionGet('userId');
        if ($userId === false) {
            header('Location: ' . URL . 'Login/index?back=Order/address');
            exit;
        }
    }

    private function checkCartNotEmpty()
    {
        $cartData = $this->processCartData();
        $items = $cartData[0] ?? [];
        
        if (empty($items)) {
            header('Location: ' . URL . 'Cart/index?error=empty_cart');
            exit;
        }
    }

    private function processCartData() 
    {
        $rawCartData = $this->model->getCartData() ?? [];
        
        $cart = [];
        $totalPrice = 0;
        $totalDiscount = 0;

        if (isset($rawCartData[0]) && is_array($rawCartData[0]) && isset($rawCartData[1]) && is_numeric($rawCartData[1])) {
            $cart = $rawCartData[0];
            $totalPrice = (float)$rawCartData[1];
            $totalDiscount = (float)($rawCartData[2] ?? 0);
        } else {
            $cart = is_array($rawCartData) ? $rawCartData : [];
        }

        if ($totalPrice <= 0 && !empty($cart)) {
            foreach ($cart as $item) {
                // NETTOYAGE : Utilisation exclusive de 'quantity' (Standardisation)
                $qty = (int)($item['quantity'] ?? 1);
                $price = (float)($item['price'] ?? 0);
                $totalPrice += ($price * $qty);
            }
        }

        return [$cart, $totalPrice, $totalDiscount];
    }

    public function index() 
    {
        $userId = Model::sessionGet('userId');
        
        if ($userId != false) {
            header('Location: ' . URL . 'Order/address');
            exit;
        } else {
            header('Location: ' . URL . 'Login/index?back=Order/address');
            exit;
        }
    }

    public function address() 
    {
        $this->checkLogin(); 
        $this->checkCartNotEmpty(); 

        $addresses = $this->model->getAddresses();
        $shippingTypes = $this->model->getShippingTypes();
        
        $data = [
            'cartData'   => $this->processCartData(),
            'addresses'  => $addresses, 
            'postType'   => $shippingTypes,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('order/step2_address', $data);
    }

    public function addAddressAjax()
    {
        $this->checkLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
            exit;
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        if (empty($_POST['last_name']) || empty($_POST['mobile']) || empty($_POST['city_name']) || empty($_POST['postal_code']) || empty($_POST['address'])) {
            echo json_encode(['status' => 'error', 'message' => 'Veuillez remplir tous les champs obligatoires.']);
            exit;
        }

        $addressId = $this->model->addAddress($_POST);
        $userId = (int)Model::sessionGet('userId');

        if ($addressId > 0) {
            $newAddress = $this->model->getAddressById($addressId, $userId);
            echo json_encode([
                'status'  => 'success',
                'message' => 'Adresse enregistrée avec succès !',
                'address' => $newAddress
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'enregistrement de l\'adresse.']);
        }
        exit;
    }

    public function summary() 
    {
        header('Location: ' . URL . 'Order/payment');
        exit;
    }

    public function payment() 
    {
        $this->checkLogin();
        $this->checkCartNotEmpty(); 

        $addressId = Model::sessionGet('selected_address_id');
        $shippingTypeId = Model::sessionGet('selected_shipping_type_id');
        $userId = (int)Model::sessionGet('userId');

        if (!$addressId || !$shippingTypeId) {
            header('Location: ' . URL . 'Order/address?error=address_missing');
            exit;
        }

        $addressInfo = $this->model->getAddressById((int)$addressId, $userId);
        if (!$addressInfo) {
            header('Location: ' . URL . 'Order/address?error=unauthorized_address');
            exit;
        }

        $shippingPrice = $this->model->getShippingPrice((int)$shippingTypeId);
        $status = $this->model->getPaymentStatus();

        $data = [
            'status'      => $status,
            'cartData'    => $this->processCartData(),
            'addressInfo' => $addressInfo,
            'postPrice'   => $shippingPrice,
            'postType'    => $shippingTypeId,
            'csrf_token'  => $this->generateCsrfToken()
        ];
        
        $this->view('order/step4_payment', $data);
    }

    public function saveAddressSession()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
            exit;
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $addressId = (int)($_POST['addressId'] ?? 0);
        $shippingId = (int)($_POST['shippingId'] ?? 0);

        if ($addressId > 0 && $shippingId > 0) {
            Model::sessionSet('selected_address_id', $addressId);
            Model::sessionSet('selected_shipping_type_id', $shippingId);
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Données invalides.']);
        }
        exit;
    }

    public function checkPromoCode() 
    {
        $this->checkLogin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
            exit;
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        $code = $_POST['code'] ?? '';
        $safeCode = htmlspecialchars(trim($code), ENT_QUOTES, 'UTF-8');
        
        $result = $this->model->verifyPromoCode($safeCode);
        $totalPrice = $this->model->calculateTotalPrice($safeCode);

        echo json_encode([$result, $totalPrice]);
        exit;
    }

    public function saveOrder() 
    {
        $this->checkLogin();
        $this->checkCartNotEmpty(); 

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $orderId = $this->model->saveOrder($_POST);
        
        if ($orderId > 0) {
            Model::sessionSet('selected_address_id', null);
            Model::sessionSet('selected_shipping_type_id', null);
            
            header('Location: ' . URL . 'Checkout/index/' . $orderId);
        } else {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode("Erreur lors de la création de la commande"));
        }
        exit;
    }
}