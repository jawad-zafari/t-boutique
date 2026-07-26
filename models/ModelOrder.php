<?php

/**
 * Model ModelOrder
 * Gestion sécurisée de la création des commandes et de l'assainissement des données.
 * Parfaitement protégé contre les injections SQL et les failles XSS.
 */
class ModelOrder extends Model 
{
    public function __construct() 
    {
        parent::__construct();
    }

    public function getAddresses() 
    {
        $sql = "SELECT * FROM user_addresses WHERE user_id = ?";
        Model::sessionInit();
        $userId = (int)Model::sessionGet('userId');
        return $this->doSelect($sql, [$userId]);
    }

    public function getAddressById($addressId)
    {
        $sql = "SELECT * FROM user_addresses WHERE id = ?";
        $result = $this->doSelect($sql, [(int)$addressId], true);
        return $result;
    }

    /**
     * Ajout d'une adresse via AJAX
     */
    public function addAddress($data)
    {
        Model::sessionInit();
        $userId = (int)Model::sessionGet('userId');
        
        if ($userId <= 0) return 0; 

        $lastName = htmlspecialchars(trim($data['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $mobile = htmlspecialchars(trim($data['mobile'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars(trim($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $provinceName = htmlspecialchars(trim($data['province_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $cityName = htmlspecialchars(trim($data['city_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $postalCode = htmlspecialchars(trim($data['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8');
        $address = htmlspecialchars(trim($data['address'] ?? ''), ENT_QUOTES, 'UTF-8');

        $sql = "INSERT INTO user_addresses 
                (user_id, last_name, mobile, phone, province_id, city_id, neighborhood, address, postal_code, province_name, city_name) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $userId, 
            $lastName, 
            $mobile, 
            $phone, 
            '', 
            '', 
            '', 
            $address, 
            $postalCode, 
            $provinceName, 
            $cityName
        ];
        
        $this->doQuery($sql, $params);

        $sqlId = "SELECT id FROM user_addresses WHERE user_id = ? ORDER BY id DESC LIMIT 1";
        $resId = $this->doSelect($sqlId, [$userId], true);
        
        return (int)($resId['id'] ?? 0);
    }

    public function getShippingTypes()
    {
        $sql = "SELECT * FROM shipping_methods";
        return $this->doSelect($sql);
    }

    public function getShippingPrice($shippingId)
    {
        $sql = "SELECT * FROM shipping_methods WHERE id = ?";
        $res = $this->doSelect($sql, [(int)$shippingId], true);
        return isset($res['price']) ? (float)$res['price'] : 0.0;
    }

    public function getCartData()
    {
        return parent::getCart();
    }

    public function getPaymentStatus()
    {
        $sql = "SELECT * FROM payment_methods";
        return $this->doSelect($sql);
    }

    public function verifyPromoCode($code)
    {
        if (empty($code)) return [];
        $sql = "SELECT * FROM discount_codes WHERE code = ? AND is_used = 0";
        return $this->doSelect($sql, [$code], true);
    }

    public function calculateTotalPrice($code)
    {
        $cart = $this->getCartData();
        $total = isset($cart[1]) ? (float)$cart[1] : 0.0;
        
        $promo = $this->verifyPromoCode($code);
        if ($promo) {
            $percent = (float)($promo['discount_percent'] ?? 0);
            if ($percent > 0) {
                $discountAmount = $total * ($percent / 100);
                $total = $total - $discountAmount;
            }
        }
        return $total < 0 ? 0 : $total;
    }

    /**
     * Crée la commande brute dans la base de données
     */
    public function saveOrder($data) 
    {
        Model::sessionInit();
        $userId = (int)Model::sessionGet('userId');
        
        $addressId = (int)Model::sessionGet('selected_address_id');
        $shippingMethodId = (int)Model::sessionGet('selected_shipping_type_id');
        
        $addressInfo = $this->getAddressById($addressId);
        if (!$addressInfo) return 0;

        $lastName = strip_tags(trim($addressInfo['last_name'] ?? ''));
        $addressText = strip_tags(trim($addressInfo['address'] ?? ''));
        $city = strip_tags(trim($addressInfo['city_name'] ?? ''));
        $postalCode = strip_tags(trim($addressInfo['postal_code'] ?? ''));
        $mobile = strip_tags(trim($addressInfo['mobile'] ?? ''));
        $phone = strip_tags(trim($addressInfo['phone'] ?? ''));
        $province = strip_tags(trim($addressInfo['province_name'] ?? ''));

        $cartData = $this->getCartData();
        $cartItems = $cartData[0] ?? [];
        $cartDataString = serialize($cartItems);

        $shippingPrice = $this->getShippingPrice($shippingMethodId);
        
        $codePromo = htmlspecialchars(trim($data['code_promo'] ?? ''), ENT_QUOTES, 'UTF-8');
        $totalAmount = $this->calculateTotalPrice($codePromo) + $shippingPrice;

        $timestamp = time();
        date_default_timezone_set('Europe/Paris');
        $date = date('Y-m-d H:i:s');
        
        $barcode = 'ORD-' . $timestamp . '-' . rand(1000, 9999);

        // CORRECTION STRICTE : Ajout des champs pay_card_number et pay_bank_name vides par défaut
        $sql = "INSERT INTO orders 
                (transaction_id_before, transaction_id_after, barcode, tracking_code, last_name, province, city, postal_code, mobile, phone, address_data, cart_data, total_amount, shipping_method_id, shipping_price, user_id, is_paid, payment_method_id, pay_card_number, pay_bank_name, created_timestamp, created_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 1, ?, ?, ?, ?)";
        
        $params = [
            '', 
            '', 
            $barcode, 
            '', 
            $lastName, 
            $province, 
            $city, 
            $postalCode, 
            $mobile, 
            $phone, 
            $addressText, 
            $cartDataString, 
            $totalAmount, 
            $shippingMethodId, 
            $shippingPrice, 
            $userId, 
            '', 
            '', 
            $timestamp, 
            $date
        ];
        
        $this->doQuery($sql, $params);

        $cookie = parent::getCartCookie();
        $sqlEmptyCart = "DELETE FROM cart_items WHERE session_cookie = ?";
        $this->doQuery($sqlEmptyCart, [$cookie]);

        $sqlId = "SELECT id FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 1";
        $resId = $this->doSelect($sqlId, [$userId], true);
        
        return (int)($resId['id'] ?? 0);
    }
}
?>