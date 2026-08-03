<?php

/**
 * Classe Model (Modèle de base)
 * Gère la connexion PDO à la base de données et les requêtes préparées (Sécurité DWWM).
 */
class Model
{
    public static ?PDO $conn = null;
    public $totalMenu = array();

    public function __construct()
    {
        // 1. Inclusion de la configuration d'environnement sécurisée
        $envPath = __DIR__ . '/env.php';
        if (file_exists($envPath)) {
            require_once $envPath;
        } else {
            die("Erreur de sécurité : Le fichier core/env.php est introuvable. Veuillez le créer à partir de core/env.example.php.");
        }

        // Récupération des constantes d'environnement
        $servername = defined('DB_HOST') ? DB_HOST : 'localhost';
        $username   = defined('DB_USER') ? DB_USER : 'root';
        $password   = defined('DB_PASS') ? DB_PASS : '';
        $dbname     = defined('DB_NAME') ? DB_NAME : 'digi_mvc';

        $initCommand = defined('Pdo\Mysql::ATTR_INIT_COMMAND') ? \Pdo\Mysql::ATTR_INIT_COMMAND : \PDO::MYSQL_ATTR_INIT_COMMAND;

        // 2. Options PDO sécurisées (Blocage des injections SQL avancées)
        $attr = array(
            $initCommand => "SET NAMES utf8mb4",
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        if (self::$conn === null) {
            try {
                self::$conn = new PDO('mysql:host=' . $servername . ';dbname=' . $dbname, $username, $password, $attr);
            } catch (PDOException $e) {
                die("Erreur de connexion à la base de données. Vérifiez la configuration dans core/env.php");
            }
        }
    }

    /**
     * Récupère les options/paramètres du système depuis la base de données
     */
    public static function getoption()
    {
        if (self::$conn === null) {
            new self();
        }
        $sql = "SELECT * FROM settings";
        $stmt = self::$conn->prepare($sql);
        $stmt->execute();
        $optionsList = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $options_new = array();

        foreach ($optionsList as $option) {
            $setting = $option['setting_key'];
            $value = $option['setting_value'];
            $options_new[$setting] = $value;
        }
        return $options_new;
    }

    public function calculateDiscount($price, $discount)
    {
        $price_discount = ($discount * $price) / 100;
        $price_total = $price - $price_discount;
        return array($price_discount, $price_total);
    }

    public function calculateProductsPrices($products)
    {
        if (!is_array($products)) return array();
        foreach ($products as $key => $product) {
            $price = $product['price'] ?? 0;
            $discount = $product['discount_percent'] ?? 0;
            $prices = $this->calculateDiscount($price, $discount);

            $products[$key]['price_discount'] = $prices[0];
            $products[$key]['price_total'] = $prices[1];
        }
        return $products;
    }

    /**
     * Exécute une requête SELECT avec des requêtes préparées
     */
    public function doSelect($sql, $values = array(), $fetch = '', $fetchStyle = PDO::FETCH_ASSOC)
    {
        $stmt = self::$conn->prepare($sql);
        foreach ($values as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        $stmt->execute();

        if ($fetch == '') {
            $result = $stmt->fetchAll($fetchStyle);
        } else {
            $result = $stmt->fetch($fetchStyle);
        }
        return $result;
    }

    /**
     * Exécute une requête INSERT, UPDATE ou DELETE sécurisée
     */
    public function doQuery($sql, $values = array())
    {
        $stmt = self::$conn->prepare($sql);
        foreach ($values as $key => $value) {
            $stmt->bindValue($key + 1, $value);
        }
        $stmt->execute();
    }

    /**
     * Récupère le nombre de favoris pour un utilisateur
     */
    public function getFavoriteCount($userId)
    {
        if (!$userId) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as total FROM favorites WHERE user_id = ?";
        $result = $this->doSelect($sql, [$userId], 1);

        return isset($result['total']) ? (int)$result['total'] : 0;
    }

    /**
     * Redimensionne et génère une vignette d'image de manière sécurisée
     */
    public function create_thumbnail($file, $pathToSave, $w, $h = '', $crop = false)
    {
        if (!file_exists($file)) return false;

        $new_height = $h;
        list($width, $height) = getimagesize($file);
        if (!$width || !$height) return false;

        $r = $width / $height;

        if ($crop) {
            if ($width > $height) {
                $width = (int) round($width - ($width * abs($r - $w / $h)));
            } else {
                $height = (int) round($height - ($height * abs($r - $w / $h)));
            }
            $newwidth = (int) $w;
            $newheight = (int) $h;
        } else {
            if ($w / $h > $r) {
                $newwidth = (int) round($h * $r);
                $newheight = (int) $h;
            } else {
                $newheight = (int) round($w / $r);
                $newwidth = (int) $w;
            }
        }

        $what = getimagesize($file);

        switch (strtolower($what['mime'])) {
            case 'image/png': $src = imagecreatefrompng($file); break;
            case 'image/jpeg': $src = imagecreatefromjpeg($file); break;
            case 'image/gif': $src = imagecreatefromgif($file); break;
            case 'image/webp': $src = imagecreatefromwebp($file); break;
            default: return false;
        }

        if ($new_height != '') {
            $newheight = (int) $new_height;
        }

        $dst = imagecreatetruecolor($newwidth, $newheight);

        if (strtolower($what['mime']) == 'image/png' || strtolower($what['mime']) == 'image/webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $newwidth, $newheight, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, (int) $width, (int) $height);

        $ext = strtolower(pathinfo($pathToSave, PATHINFO_EXTENSION));
        switch ($ext) {
            case 'png': imagepng($dst, $pathToSave); break;
            case 'webp': imagewebp($dst, $pathToSave, 90); break;
            case 'gif': imagegif($dst, $pathToSave); break;
            default: imagejpeg($dst, $pathToSave, 95);
        }

        unset($src);
        unset($dst);

        return true;
    }

    /**
     * Initialise la session PHP de manière propre et sécurisée
     */
    public static function sessionInit()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function sessionSet($name, $value)
    {
        self::sessionInit();
        $_SESSION[$name] = $value;
    }

    public static function sessionGet($name)
    {
        self::sessionInit();
        return isset($_SESSION[$name]) ? $_SESSION[$name] : false;
    }

    /**
     * SÉCURITÉ DWWM : Génère un identifiant de panier cryptographiquement sécurisé et imprévisible
     */
    public static function getCartCookie()
    {
        if (isset($_COOKIE['cart']) && !empty($_COOKIE['cart'])) {
            return $_COOKIE['cart'];
        } else {
            $expire = time() + 7 * 24 * 3600;
            // Utilisation de random_bytes au lieu de time() pour éviter la prédictibilité de session
            $value = bin2hex(random_bytes(16));

            setcookie('cart', $value, [
                'expires' => $expire,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            return $value;
        }
    }

    public function getCart()
    {
        $sql = "SELECT c.quantity AS quantity, c.id AS cartRow, p.*, cl.title AS colorTitle, g.title AS garanteeTitle
         FROM cart_items c 
         LEFT JOIN products p ON c.product_id = p.id
         LEFT JOIN colors cl ON c.color_id = cl.id
         LEFT JOIN guarantees g ON c.guarantee_id = g.id
         WHERE c.session_cookie = ?";

        $cookie = self::getCartCookie();
        $params = array($cookie);
        $result = $this->doSelect($sql, $params);
        $discountTotalAll = 0;

        foreach ($result as $key => $row) {
            $discount = (($row['discount_percent'] ?? 0) * ($row['price'] ?? 0)) / 100;
            $discountTotal = ($row['quantity'] ?? 1) * $discount;
            $discountTotalAll = $discountTotalAll + $discountTotal;
            $result[$key]['discountTotal'] = $discountTotal;
        }

        $priceTotalall = 0;
        foreach ($result as $row) {
            $price = $row['price'] ?? 0;
            $quantity = $row['quantity'] ?? 1;
            $priceTotal = $price * $quantity;
            $priceTotalall = $priceTotalall + $priceTotal;
        }

        return array($result, $priceTotalall, $discountTotalAll);
    }

    /**
     * Calcule les frais de livraison depuis la base de données
     */
    public function calculatePostPrice($cityId = 0)
    {
        // Architecture DWWM : Récupération dynamique des prix depuis la table shipping_methods
        $sql = "SELECT id, price FROM shipping_methods";
        $methods = $this->doSelect($sql);
        
        // Valeurs par défaut sécurisées
        $prices = array(
            'express' => 5.00, 
            'standard' => 0.00,
            'pishtaz' => 5.00,   // Rétrocompatibilité si un ancien contrôleur l'utilise
            'sefareshi' => 0.00  // Rétrocompatibilité si un ancien contrôleur l'utilise
        );

        if (is_array($methods)) {
            foreach ($methods as $method) {
                $id = (int)$method['id'];
                $price = (float)$method['price'];
                
                if ($id === 1) { // 1 correspond généralement à la Livraison Express
                    $prices['express'] = $price;
                    $prices['pishtaz'] = $price;
                } elseif ($id === 2) { // 2 correspond généralement à la Livraison Standard
                    $prices['standard'] = $price;
                    $prices['sefareshi'] = $price;
                }
            }
        }

        return $prices;
    }

    /**
     * Formatage standard de la date actuelle
     */
    public static function getCurrentDate($format = 'Y-m-d H:i:s') 
    {
        return date($format);
    }

    /**
     * Traitement et normalisation d'une date pour la base de données
     */
    public static function formatDateForDB($dateStr, $format = '/')
    {
        try {
            $cleanDate = str_replace('/', '-', $dateStr);
            $date = new DateTime($cleanDate);
            return $date->format('Y-m-d');
        } catch (Exception $e) {
            return date('Y-m-d');
        }
    }

    /**
     * Formatage d'une date standard pour l'affichage (JJ/MM/AAAA)
     */
    public static function formatDateForDisplay($dateStr, $format = '/')
    {
        try {
            $cleanDate = str_replace('/', '-', $dateStr);
            $date = new DateTime($cleanDate);
            return $date->format('d' . $format . 'm' . $format . 'Y');
        } catch (Exception $e) {
            return date('d/m/Y');
        }
    }

    public function getMenu($parentId = 0)
    {
        $data = array();
        $sql = "SELECT * FROM categories WHERE parent_id = ?";
        $result = $this->doSelect($sql, array($parentId));
        foreach ($result as $row) {
            $children = $this->getMenu($row['id']);
            if (is_array($children) && count($children) > 0) {
                $row['children'] = $children;
            }
            $data[] = $row;
        }
        return $data;
    }

    public static function getUserLevel()
    {
        self::sessionInit();
        $userId = self::sessionGet('userId');
        if (!$userId) return 0;

        $sql = "SELECT * FROM users WHERE id = ?";
        $model_instance = new Model();
        $userInfo = $model_instance->doSelect($sql, array($userId), 1);
        return $userInfo['role_id'] ?? 0;
    }
}
?>