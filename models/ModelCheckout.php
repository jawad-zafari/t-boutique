<?php

/**
 * Model ModelCheckout
 * Gestion sécurisée des paiements et des commandes.
 * Sécurité DWWM : Protection Anti-IDOR, Requêtes PDO, Typage strict.
 */
class ModelCheckout extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupère les informations d'une commande (Sécurisé contre IDOR)
     */
    public function getOrderInfo($orderId)
    {
        self::sessionInit();
        $userId = (int)self::sessionGet('userId');

        // SÉCURITÉ CRITIQUE (IDOR) : On s'assure via la requête SQL que la commande 
        // appartient obligatoirement à l'utilisateur actuellement connecté.
        if ($userId > 0) {
            $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
            return $this->doSelect($sql, [(int)$orderId, $userId], 'fetch', PDO::FETCH_ASSOC);
        }
        
        return false;
    }

    /**
     * Mise à jour du statut de la commande après validation du paiement (Mock)
     */
    public function markOrderAsPaid($orderId, $transactionId)
    {
        // On passe is_paid à 1 et on enregistre le numéro de transaction généré
        $sql = "UPDATE orders SET is_paid = 1, transaction_id_after = ? WHERE id = ?";
        $this->doQuery($sql, [$transactionId, (int)$orderId]);
        return true;
    }

    /**
     * Met à jour les informations pour un virement bancaire manuel
     */
    public function updateCreditCard($data, $orderId)
    {
        $day = (int)($data['day'] ?? 0);
        $month = (int)($data['month'] ?? 0);
        $year = (int)($data['year'] ?? 0);
        
        // SÉCURITÉ CRITIQUE : Utilisation de htmlspecialchars pour contrer le XSS stocké
        $creditCard = htmlspecialchars(trim($data['creditcard'] ?? ''), ENT_QUOTES, 'UTF-8');
        $bank = htmlspecialchars(trim($data['bank'] ?? ''), ENT_QUOTES, 'UTF-8');

        $orderInfo = $this->getOrderInfo($orderId);
        
        if ($orderInfo) {
            $sql = "UPDATE orders SET pay_card_number = ?, pay_bank_name = ?, pay_day = ?, pay_month = ?, pay_year = ? WHERE id = ?";
            $this->doQuery($sql, [$creditCard, $bank, $day, $month, $year, (int)$orderId]);
        }
    }
}