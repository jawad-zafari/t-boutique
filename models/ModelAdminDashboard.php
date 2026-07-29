<?php

/**
 * Modèle ModelAdminDashboard
 * Récupère et calcule les statistiques de ventes pour le graphique.
 * Code protégé contre les erreurs (Anti-Crash) en cas de BDD vide.
 */
class ModelAdminDashboard extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // OPTIMISATION DE PERFORMANCE : Sélectionne uniquement 'created_date' pour économiser la RAM
    public function getOrderDates()
    {
        $sql = "SELECT created_date FROM orders";
        return $this->doSelect($sql);
    }

    // Calculer les statistiques des commandes des 7 derniers jours
    public function getStat()
    {
        // STANDARD DWWM : Utilisation du format standard SQL (Y-m-d)
        $todayDate = date('Y-m-d');
        $time = time();
        
        // 6 jours en arrière + aujourd'hui = 7 jours
        $lastWeekTime = $time - (6 * 24 * 3600); 
        $lastWeekDate = date('Y-m-d', $lastWeekTime);
        
        $dates = $this->getRange($lastWeekDate, $todayDate);
        $orders = $this->getOrderDates();
        $orderStat = [];

        // Initialiser le tableau des statistiques avec 0 pour chaque jour
        foreach ($dates as $date) {
            $orderStat[$date] = 0;
        }

        // Compter les commandes (avec vérification Anti-Crash)
        if (!empty($orders)) {
            foreach ($orders as $row) {
                // La date en BDD est au format "YYYY-MM-DD HH:II:SS"
                $fullDate = $row['created_date'] ?? '';
                
                if (!empty($fullDate)) {
                    // On extrait uniquement la partie "YYYY-MM-DD" (les 10 premiers caractères)
                    $orderDate = substr($fullDate, 0, 10);

                    // Si la date correspond à un jour de notre semaine, on incrémente
                    if (isset($orderStat[$orderDate])) {
                        $orderStat[$orderDate]++;
                    }
                }
            }
        }

        return $orderStat;
    }

    // Générer un tableau contenant toutes les dates entre deux périodes
    public function getRange($startDate, $lastDate)
    {
        $dates = [];
        $current = strtotime($startDate);
        $last = strtotime($lastDate);

        while ($current <= $last) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }

        return $dates;
    }
}
?>