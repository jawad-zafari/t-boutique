<?php

/**
 * Model ModelCheckout
 * Gestion sécurisée des paiements Stripe et des commandes.
 * Adapté strictement à la base de données standard existante.
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

        // SÉCURITÉ (IDOR) : On s'assure que la commande appartient à l'utilisateur connecté
        if ($userId > 0) {
            $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
            return $this->doSelect($sql, [(int)$orderId, $userId], 'fetch', PDO::FETCH_ASSOC);
        }
        
        return false;
    }

    /**
     * Vérifie le statut du paiement au retour de Stripe
     */
    public function stripeCheckout($data)
    {
        $authority = $data['Authority'] ?? '';
        
        $sql = "SELECT * FROM orders WHERE transaction_id_before = ?";
        $result = $this->doSelect($sql, [$authority], 'fetch', PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }

        // Correspondance avec la colonne "total_amount" de votre BDD
        $amount = (float)($result['total_amount'] ?? 0); 
        $orderId = (int)$result['id'];

        require_once 'core/payment.php';
        $payment = new Payment();
        $verifyResult = $payment->stripeVerify($amount, $authority);

        // 100 = Paiement réussi
        if (isset($verifyResult['Status']) && $verifyResult['Status'] == 100) {
            $refId = $verifyResult['RefID'] ?? '';
            // CORRECTION : Utilisation de "is_paid" au lieu de "pay_status"
            $sqlUpdate = "UPDATE orders SET is_paid = 1, transaction_id_after = ? WHERE id = ?";
            $this->doQuery($sqlUpdate, [$refId, $orderId]);
        }

        return $this->getOrderInfo($orderId);
    }

    /**
     * Prépare et envoie la requête de paiement à Stripe
     */
    public function payOnline($orderId)
    {
        $orderInfo = $this->getOrderInfo($orderId);
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande invalide.'));
            exit;
        }

        // Correspondance avec la colonne "total_amount" de votre BDD
        $amount = (float)($orderInfo['total_amount'] ?? 0);
        $email = 'client@example.com'; 
        $description = "Paiement pour la commande #" . (int)$orderId;

        require_once 'core/payment.php';
        $payment = new Payment();
        $result = $payment->stripeRequest($amount, $description, $email);

        if (isset($result['Status']) && $result['Status'] == 100) {
            $authority = $result['Authority'] ?? '';
            $redirectUrl = $result['RedirectURL'] ?? '';

            $sqlAuth = "UPDATE orders SET transaction_id_before = ? WHERE id = ?";
            $this->doQuery($sqlAuth, [$authority, (int)$orderId]);
            
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $error = $result['Error'] ?? 'Erreur lors de la création de la session Stripe.';
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode($error) . '&orderId=' . (int)$orderId);
            exit;
        }
    }

    /**
     * Met à jour les informations pour un virement bancaire manuel
     */
    public function updateCreditCard($data, $orderId)
    {
        $day = (int)($data['day'] ?? 0);
        $month = (int)($data['month'] ?? 0);
        $year = (int)($data['year'] ?? 0);
        
        $creditCard = htmlspecialchars(trim($data['creditcard'] ?? ''), ENT_QUOTES, 'UTF-8');
        $bank = htmlspecialchars(trim($data['bank'] ?? ''), ENT_QUOTES, 'UTF-8');

        $orderInfo = $this->getOrderInfo($orderId);
        
        if ($orderInfo) {
            // CORRECTION: Utilisation de payment_method_id, pay_card_number et pay_bank_name
            // SÉCURITÉ LOGIQUE: is_paid n'est PAS mis à 1 car le virement doit être validé par l'admin !
            $sql = "UPDATE orders SET pay_day = ?, pay_month = ?, pay_year = ?, pay_card_number = ?, pay_bank_name = ?, payment_method_id = 2 WHERE id = ?";
            $this->doQuery($sql, [$day, $month, $year, $creditCard, $bank, (int)$orderId]);
        }
    }
}
?>