<?php

/**
 * Model ModelCheckout
 * Gestion sécurisée des paiements et des commandes (Anti-Injection SQL & Protection IDOR)
 */
class ModelCheckout extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrderInfo($orderId)
    {
        self::sessionInit();
        $userId = (int)self::sessionGet('userId');

        // SÉCURITÉ CRITIQUE (IDOR Protection) : 
        // On s'assure que la commande récupérée appartient bien à l'utilisateur actuellement connecté !
        if ($userId > 0) {
            $sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
            return $this->doSelect($sql, [(int)$orderId, $userId], true);
        } else {
            // Si l'utilisateur est un invité (Guest), on vérifie via le cookie de session
            $cookie = self::getCartCookie();
            $sql = "SELECT * FROM orders WHERE id = ? AND session_cookie = ?";
            return $this->doSelect($sql, [(int)$orderId, $cookie], true);
        }
    }

    public function stripeCheckout($data)
    {
        $authority = $data['Authority'] ?? '';
        
        $sql = "SELECT * FROM orders WHERE transaction_id_before = ?";
        $result = $this->doSelect($sql, [$authority], true);
        $amount = $result['total_amount'] ?? 0;

        $payment = new Payment();
        $verifyResult = $payment->stripeVerify($amount, $authority);
        
        $status = (int)($verifyResult['Status'] ?? 0);
        $refId = $verifyResult['RefID'] ?? '';

        if ($status === 100) {
            // CORRECTION : On met à jour is_paid = 1 ET status_id = 4 (Payée)
            $sqlUpdate = "UPDATE orders SET is_paid = 1, status_id = 4, transaction_id_after = ? WHERE transaction_id_before = ?";
            $this->doQuery($sqlUpdate, [$refId, $authority]);
        }

        $sqlFinal = "SELECT * FROM orders WHERE transaction_id_before = ?";
        return $this->doSelect($sqlFinal, [$authority], true);
    }

    public function payOnline($orderId)
    {
        $orderId = (int)$orderId;
        $orderInfo = $this->getOrderInfo($orderId);
        
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande introuvable.'));
            exit;
        }

        $payType = (int)($orderInfo['payment_method_id'] ?? 1);

        // CORRECTION : 2 correspond au virement bancaire dans votre BDD (pas 4)
        if ($payType === 2) {
            $sql = "UPDATE orders SET payment_method_id = 1 WHERE id = ?";
            $this->doQuery($sql, [$orderId]);
            $payType = 1;
        }

        if ($payType === 1) {
            $amount = $orderInfo['total_amount'] ?? 0;
            $email = 'contact@maboutique.fr'; 
            
            $payment = new Payment();
            $result = $payment->stripeRequest($amount, 'Paiement de commande sécurisé', $email);

            $status = (int)($result['Status'] ?? 0);
            $authority = $result['Authority'] ?? '';
            $redirectUrl = $result['RedirectURL'] ?? '';
            $error = $result['Error'] ?? '';

            if ($status === 100) {
                $sqlAuth = "UPDATE orders SET transaction_id_before = ? WHERE id = ?";
                $this->doQuery($sqlAuth, [$authority, $orderId]);
                
                header('Location: ' . $redirectUrl);
                exit;
            } else {
                header('Location: ' . URL . 'Checkout/showError?error=' . urlencode($error) . '&orderId=' . $orderId);
                exit;
            }
        }
    }

    public function updateCreditCard($data, $orderId)
    {
        $day = (int)($data['day'] ?? 0);
        $month = (int)($data['month'] ?? 0);
        $year = (int)($data['year'] ?? 0);
        
        $creditCard = htmlspecialchars(trim($data['creditcard'] ?? ''), ENT_QUOTES, 'UTF-8');
        $bank = htmlspecialchars(trim($data['bank'] ?? ''), ENT_QUOTES, 'UTF-8');

        $orderInfo = $this->getOrderInfo((int)$orderId);
        if ($orderInfo) {
            // CORRECTION : On assigne la méthode de paiement 2 (Virement) et le statut 1 (En attente de confirmation)
            $sql = "UPDATE orders SET pay_day = ?, pay_month = ?, pay_year = ?, pay_card_number = ?, pay_bank_name = ?, payment_method_id = 2, status_id = 1 WHERE id = ?";
            $params = [$day, $month, $year, $creditCard, $bank, (int)$orderId];
            $this->doQuery($sql, $params);
        }
    }
}
?>