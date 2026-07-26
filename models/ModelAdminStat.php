<?php

/**
 * Modèle ModelAdminStat
 * Gère les calculs et extractions de données pour les rapports.
 */
class ModelAdminStat extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function order($data)
    {
        // SÉCURITÉ CRITIQUE : Conversion stricte en entier (Integer Casting)
        // Cela empêche toute injection SQL ou caractères non valides dans les dates
        $y1 = (int)($data['year1'] ?? 0);
        $m1 = (int)($data['month1'] ?? 0);
        $d1 = (int)($data['day1'] ?? 0);
        
        $y2 = (int)($data['year2'] ?? 0);
        $m2 = (int)($data['month2'] ?? 0);
        $d2 = (int)($data['day2'] ?? 0);

        // Validation de base pour éviter des dates vides
        if ($y1 === 0 || $m1 === 0 || $d1 === 0) {
            return [
                'result' => [], 'order_paied' => 0, 'amount_total' => 0, 
                'startDate' => 'Invalide', 'endDate' => 'Invalide'
            ];
        }

        // Formatage standard des dates (YYYY-MM-DD)
        $startDate = sprintf('%04d-%02d-%02d', $y1, $m1, $d1);
        $endDate = sprintf('%04d-%02d-%02d', $y2, $m2, $d2);

        // Ajout des heures pour couvrir la journée complète
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // OPTIMISATION MAJEURE : Laisser la base de données filtrer les dates via BETWEEN
        $sql = "SELECT * FROM orders WHERE created_date BETWEEN ? AND ? ORDER BY created_date DESC";
        $result = $this->doSelect($sql, [$startDateTime, $endDateTime]);
        
        $ordersPaid = 0;
        $amountTotal = 0;

        // Calculs des totaux uniquement sur les résultats filtrés
        if (!empty($result) && is_array($result)) {
            foreach ($result as $row) {
                // On ne calcule le chiffre d'affaires que sur les commandes payées
                if (isset($row['is_paid']) && $row['is_paid'] == 1) {
                    $ordersPaid++;
                    // Fallback sécurisé pour récupérer le montant total (Cast en float)
                    $amountTotal += (float)($row['total_price'] ?? $row['amount'] ?? 0);
                }
            }
        }

        return [
            'result' => is_array($result) ? $result : [],
            'order_paied' => $ordersPaid,
            'amount_total' => $amountTotal,
            'startDate' => $startDate,
            'endDate' => $endDate
        ];
    }
}
?>