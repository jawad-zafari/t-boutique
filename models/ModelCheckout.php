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
     * Vérifie le statut du paiement au retour de la passerelle
     */
    public function stripeCheckout($data)
    {
        $authority = $data['Authority'] ?? '';
        
        $sql = "SELECT * FROM orders WHERE transaction_id_before = ?";
        $result = $this->doSelect($sql, [$authority], 'fetch', PDO::FETCH_ASSOC);
        
        if (!$result) {
            return false;
        }

        // Ici, vous intégrez l'API de votre passerelle (Stripe, etc.) pour vérifier le statut réel.
        // C'est une simulation standard pour le projet de formation.
        $status = "OK"; 
        $refId = uniqid('ref_');

        if ($status === "OK") {
            // Mettre à jour la commande comme payée
            $sqlUpdate = "UPDATE orders SET is_paid = 1, status_id = 2, transaction_id_after = ? WHERE id = ?";
            $this->doQuery($sqlUpdate, [$refId, (int)$result['id']]);
            return $this->getOrderInfo((int)$result['id']);
        } else {
            return false;
        }
    }

    /**
     * Initialise la session de paiement
     */
    public function payOnline($orderId)
    {
        $orderInfo = $this->getOrderInfo($orderId);
        
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande introuvable.'));
            exit;
        }

        $amount = (float)($orderInfo['amount'] ?? 0);
        
        if ($amount <= 0) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Montant invalide.') . '&orderId=' . (int)$orderId);
            exit;
        }

        // Simulation de création d'une session de paiement (API Stripe / Bank)
        $result = ['Status' => 100, 'Authority' => uniqid('auth_')];
        $redirectUrl = URL . "Checkout/index?Authority=" . $result['Authority'];

        if ($result['Status'] == 100) {
            $authority = $result['Authority'];
            
            // Enregistrer l'autorité temporaire avant la redirection
            $sqlAuth = "UPDATE orders SET transaction_id_before = ? WHERE id = ?";
            $this->doQuery($sqlAuth, [$authority, (int)$orderId]);
            
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $error = $result['Error'] ?? 'Erreur lors de la création de la session de paiement.';
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
        
        // SÉCURITÉ CRITIQUE : Utilisation de htmlspecialchars pour contrer le XSS stocké
        $creditCard = htmlspecialchars(trim($data['creditcard'] ?? ''), ENT_QUOTES, 'UTF-8');
        $bank = htmlspecialchars(trim($data['bank'] ?? ''), ENT_QUOTES, 'UTF-8');

        $orderInfo = $this->getOrderInfo($orderId);
        
        if ($orderInfo) {
            // SÉCURITÉ LOGIQUE : is_paid n'est PAS mis à 1 ici, car le virement doit être validé par l'admin.
            $sql = "UPDATE orders SET payment_method_id = 2, pay_card_number = ?, pay_bank_name = ?, pay_day = ?, pay_month = ?, pay_year = ? WHERE id = ?";
            $this->doQuery($sql, [$creditCard, $bank, $day, $month, $year, (int)$orderId]);
        }
    }
}
?>