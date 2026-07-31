<?php

/**
 * Model: ModelIndex
 * Gère les requêtes de la page d'accueil.
 * Sécurité: Prévention des injections SQL via PDO et conversion de type (casting).
 */
class ModelIndex extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getMainSliders()
    {
        $sql = "SELECT * FROM sliders";
        return $this->doSelect($sql);
    }

    public function getSpecialOffers()
    {
        // SÉCURITÉ : Utilisation de requête préparée avec PDO (?)
        $sql = "SELECT * FROM products WHERE is_special_offer = ?";
        $result = $this->doSelect($sql, [1]);

        foreach ($result as $key => $row) {
            $priceCalculate = $this->calculateDiscount($row['price'], $row['discount_percent']);
            $result[$key]['price_total'] = $priceCalculate[1];
        }

        $firstRow = $result[0] ?? [];
        $timeSpecial = $firstRow['special_offer_expires_at'] ?? 0;

        $options = self::getoption(); 
        $durationSpecial = $options['special_time'] ?? 0;

        $timeEnd = $timeSpecial + $durationSpecial;
        
        date_default_timezone_set('Europe/Paris'); 
        $date = date('F d,Y H:i:s', $timeEnd);

        return [$result, $date];
    }

    public function getExclusiveProducts()
    {
        // SÉCURITÉ : Utilisation de requête préparée
        $sql = "SELECT * FROM products WHERE is_exclusive = ?";
        $result = $this->doSelect($sql, [1]);

        foreach ($result as $key => $row) {
            $priceCalculate = $this->calculateDiscount($row['price'], $row['discount_percent']);
            $result[$key]['price_total'] = $priceCalculate[1];
        }
        return $result;
    }

    public function getMostViewedProducts()
    {
        // SÉCURITÉ : Requête préparée pour récupérer la limite
        $sqlLimit = "SELECT * FROM settings WHERE setting_key = ?";
        $resultLimit = $this->doSelect($sqlLimit, ['limit_slider'], true);
        
        // SÉCURITÉ : Forçage du type en entier (Integer) pour prévenir l'injection SQL
        $limit = isset($resultLimit['setting_value']) ? (int)$resultLimit['setting_value'] : 10;

        $sql = "SELECT * FROM products ORDER BY views DESC LIMIT " . $limit;
        $result = $this->doSelect($sql);

        foreach ($result as $key => $row) {
            $priceCalculate = $this->calculateDiscount($row['price'], $row['discount_percent']);
            $result[$key]['price_total'] = $priceCalculate[1];
        }
        return $result;
    }

    public function getLatestProducts()
    {
        // SÉCURITÉ : Requête préparée
        $sqlLimit = "SELECT * FROM settings WHERE setting_key = ?";
        $resultLimit = $this->doSelect($sqlLimit, ['limit_slider'], true);
        
        // SÉCURITÉ : Forçage du type (Integer)
        $limit = isset($resultLimit['setting_value']) ? (int)$resultLimit['setting_value'] : 10;

        $sql = "SELECT * FROM products ORDER BY id DESC LIMIT " . $limit;
        $result = $this->doSelect($sql);

        foreach ($result as $key => $row) {
            $priceCalculate = $this->calculateDiscount($row['price'], $row['discount_percent']);
            $result[$key]['price_total'] = $priceCalculate[1];
        }
        return $result;
    }

    /**
     * Récupère les dernières actualités (News)
     */
    public function getLatestNews($limit = 3)
    {
        // SÉCURITÉ : Forçage du type (Integer)
        $safeLimit = (int)$limit;
        $sql = "SELECT * FROM news ORDER BY id DESC LIMIT " . $safeLimit;
        return $this->doSelect($sql);
    }

    /**
     * Récupère les marques (Catégories définies comme marques)
     */
    public function getBrands($limit = 6)
    {
        // SÉCURITÉ : Forçage du type (Integer)
        $safeLimit = (int)$limit;
        // SÉCURITÉ : Utilisation de requête préparée
        $sql = "SELECT * FROM categories WHERE is_brand = ? ORDER BY id DESC LIMIT " . $safeLimit;
        return $this->doSelect($sql, [1]);
    }

    /**
     * Récupère les paramètres de la Boutique TV
     */
    public function getTvSettings()
    {
        $sql = "SELECT * FROM settings WHERE setting_key IN ('tv_video_link', 'tv_cover_image')";
        $results = $this->doSelect($sql);
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
}
?>