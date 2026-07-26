<?php

/**
 * Model ModelIndex
 * Sécurisé contre les injections SQL (Forçage du typage des limites)
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
        $sql = "SELECT * FROM products WHERE is_exclusive = 1";
        $result = $this->doSelect($sql);

        foreach ($result as $key => $row) {
            $priceCalculate = $this->calculateDiscount($row['price'], $row['discount_percent']);
            $result[$key]['price_total'] = $priceCalculate[1];
        }
        return $result;
    }

    public function getMostViewedProducts()
    {
        $sqlLimit = "SELECT * FROM settings WHERE setting_key = 'limit_slider'";
        $resultLimit = $this->doSelect($sqlLimit, [], true);
        
        // SÉCURITÉ : Forcer en entier (Integer) pour éviter une injection SQL via la limite
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
        $sqlLimit = "SELECT * FROM settings WHERE setting_key = 'limit_slider'";
        $resultLimit = $this->doSelect($sqlLimit, [], true);
        
        // SÉCURITÉ : Forcer en entier
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
        // SÉCURITÉ : Forcer en entier
        $safeLimit = (int)$limit;
        $sql = "SELECT * FROM news ORDER BY id DESC LIMIT " . $safeLimit;
        return $this->doSelect($sql);
    }

    /**
     * Récupère les marques (Catégories définies comme marques)
     */
    public function getBrands($limit = 6)
    {
        // SÉCURITÉ : Forcer en entier
        $safeLimit = (int)$limit;
        $sql = "SELECT * FROM categories WHERE is_brand = 1 ORDER BY id DESC LIMIT " . $safeLimit;
        return $this->doSelect($sql);
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