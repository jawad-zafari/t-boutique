<?php

/**
 * Modèle ModelOrder
 * Gestion sécurisée de la création des commandes et de l'assainissement des données.
 * Protection stricte contre les injections SQL (PDO), les failles XSS et respect des normes PCI-DSS pour les cartes.
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

    public function getAddressById($addressId, $userId)
    {
        $sql = "SELECT * FROM user_addresses WHERE id = ? AND user_id = ?";
        $result = $this->doSelect($sql, [(int)$addressId, (int)$userId], 'fetch', PDO::FETCH_ASSOC);
        return $result;
    }

    public function addAddress($data)
    {
        Model::sessionInit();
        $userId = (int)Model::sessionGet('userId');

        // Nettoyage des données pour prévenir les failles XSS
        $lastName     = htmlspecialchars(trim($data['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $mobile       = htmlspecialchars(trim($data['mobile'] ?? ''), ENT_QUOTES, 'UTF-8');
        $provinceName = htmlspecialchars(trim($data['province_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $cityName     = htmlspecialchars(trim($data['city_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $postalCode   = htmlspecialchars(trim($data['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8');
        $address      = htmlspecialchars(trim($data['address'] ?? ''), ENT_QUOTES, 'UTF-8');

        // CORRECTION DWWM : Correspondance exacte avec les colonnes de la BDD (province_name, city_name)
        // Les colonnes sans valeur par défaut (phone, province_id, etc.) sont remplies avec des chaînes vides
        $sql = "INSERT INTO user_addresses (user_id, last_name, mobile, province_name, city_name, postal_code, address, phone, province_id, city_id, neighborhood) 
                VALUES (?, ?, ?, ?, ?, ?, ?, '', '', '', '')";
        
        $params = [$userId, $lastName, $mobile, $provinceName, $cityName, $postalCode, $address];
        
        $this->doQuery($sql, $params);
        return self::$conn->lastInsertId();
    }

    public function getShippingTypes() 
    {
        $sql = "SELECT * FROM shipping_methods";
        return $this->doSelect($sql);
    }

    public function getShippingPrice($shippingId)
    {
        $sql = "SELECT price FROM shipping_methods WHERE id = ?";
        $result = $this->doSelect($sql, [(int)$shippingId], 'fetch', PDO::FETCH_ASSOC);
        return isset($result['price']) ? (float)$result['price'] : 0;
    }

    public function getCartData() 
    {
        return parent::getCart();
    }

    public function getPaymentStatus() 
    {
        $sql = "SELECT * FROM settings WHERE setting_key = 'payment_status'";
        return $this->doSelect($sql, [], 'fetch', PDO::FETCH_ASSOC);
    }

    public function verifyPromoCode($code)
    {
        $sql = "SELECT * FROM discount_codes WHERE code = ? AND is_used = 0 AND expires_at > ?";
        // Vérification de la validité du code promo (comparaison avec la date actuelle)
        $currentDate = date('Y-m-d');
        return $this->doSelect($sql, [$code, $currentDate], 'fetch', PDO::FETCH_ASSOC);
    }

    public function calculateTotalPrice($code = '')
    {
        $cartData = $this->getCartData();
        $totalPrice = (float)($cartData[1] ?? 0);
        $discountTotal = (float)($cartData[2] ?? 0);

        if (!empty($code)) {
            $promo = $this->verifyPromoCode($code);
            if ($promo && isset($promo['discount_percent'])) {
                $promoDiscount = ($totalPrice * (float)$promo['discount_percent']) / 100;
                $discountTotal += $promoDiscount;
            }
        }

        return max(0, $totalPrice - $discountTotal);
    }

    /**
     * SÉCURITÉ DWWM : Enregistrement sécurisé de la commande
     * Masquage strict du numéro de carte (Norme PCI-DSS : uniquement les 4 derniers chiffres).
     */
    public function saveOrder($postData)
    {
        Model::sessionInit();
        $userId = (int)Model::sessionGet('userId');
        $addressId = (int)Model::sessionGet('selected_address_id');
        $shippingMethodId = (int)Model::sessionGet('selected_shipping_type_id');

        if (!$userId || !$addressId || !$shippingMethodId) {
            return 0;
        }

        $addressInfo = $this->getAddressById($addressId, $userId);
        if (!$addressInfo) {
            return 0;
        }

        $cartInfo = $this->getCartData();
        $cartItems = $cartInfo[0] ?? [];
        if (empty($cartItems)) {
            return 0;
        }

        $totalProductsPrice = (float)($cartInfo[1] ?? 0);
        $totalDiscount = (float)($cartInfo[2] ?? 0);
        $shippingPrice = $this->getShippingPrice($shippingMethodId);

        // Code Promo optionnel
        $codePromo = htmlspecialchars(trim($postData['code_promo'] ?? ''), ENT_QUOTES, 'UTF-8');
        if (!empty($codePromo)) {
            $promo = $this->verifyPromoCode($codePromo);
            if ($promo && isset($promo['discount_percent'])) {
                $promoDiscount = ($totalProductsPrice * (float)$promo['discount_percent']) / 100;
                $totalDiscount += $promoDiscount;
            }
        }

        $totalAmount = max(0, $totalProductsPrice + $shippingPrice - $totalDiscount);

        // RÉCUPÉRATION DU MODE DE PAIEMENT SÉLECTIONNÉ (1 = Carte Bancaire, 2 = Virement)
        $paymentMethodId = (int)($postData['payment_method'] ?? 1);
        $maskedCard = '';
        $payBankName = '';

        if ($paymentMethodId === 1) {
            $payBankName = 'Carte Bancaire';
            $rawCardNumber = preg_replace('/\D/', '', $postData['card_number'] ?? '');
            if (!empty($rawCardNumber)) {
                $last4Digits = substr($rawCardNumber, -4);
                $maskedCard = '**** **** **** ' . $last4Digits;
            }
        } else if ($paymentMethodId === 2) {
            $payBankName = 'Virement Bancaire';
            $maskedCard = 'N/A';
        }

        $lastName    = htmlspecialchars($addressInfo['last_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $mobile      = htmlspecialchars($addressInfo['mobile'] ?? '', ENT_QUOTES, 'UTF-8');
        $province    = htmlspecialchars($addressInfo['province_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $city        = htmlspecialchars($addressInfo['city_name'] ?? '', ENT_QUOTES, 'UTF-8');
        $postalCode  = htmlspecialchars($addressInfo['postal_code'] ?? '', ENT_QUOTES, 'UTF-8');
        $addressText = htmlspecialchars($addressInfo['address'] ?? '', ENT_QUOTES, 'UTF-8');

        $cartDataString = serialize($cartItems);

        $timestamp = time();
        $date = date('Y-m-d H:i:s');
        $barcode = 'ORD-' . $timestamp . '-' . rand(100, 999);

        $sql = "INSERT INTO orders 
                (transaction_id_before, transaction_id_after, barcode, tracking_code, last_name, province, city, postal_code, mobile, phone, address_data, cart_data, total_amount, shipping_method_id, shipping_price, user_id, is_paid, payment_method_id, pay_card_number, pay_bank_name, created_timestamp, created_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?, ?, ?, ?)";
        
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
            '', 
            $addressText, 
            $cartDataString, 
            $totalAmount, 
            $shippingMethodId, 
            $shippingPrice, 
            $userId, 
            $paymentMethodId, 
            $maskedCard, 
            $payBankName, 
            $timestamp, 
            $date
        ];
        
        $this->doQuery($sql, $params);
        $orderId = (int)self::$conn->lastInsertId();

        if ($orderId > 0) {
            $cookie = parent::getCartCookie();
            $sqlEmptyCart = "DELETE FROM cart_items WHERE session_cookie = ?";
            $this->doQuery($sqlEmptyCart, [$cookie]);
        }

        return $orderId;
    }
}
?>