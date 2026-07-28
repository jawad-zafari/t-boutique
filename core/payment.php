<?php

/**
 * Classe Payment
 * Gère les transactions financières en simulant l'API de Stripe.
 * Conçu selon les normes DWWM (Typage strict, Sécurité).
 */
class Payment
{
    private string $stripeSecretKey;

    public function __construct()
    {
        // Récupération sécurisée de la clé API depuis les variables d'environnement
        if (defined('STRIPE_SECRET_KEY')) {
            $this->stripeSecretKey = STRIPE_SECRET_KEY;
        } else {
            die("Erreur de configuration : La clé secrète Stripe est manquante.");
        }
    }

    /**
     * Initialise une demande de paiement auprès de Stripe.
     * * @param float $amount Le montant à payer.
     * @param string $description La description de la commande.
     * @param string $email L'email du client.
     * @return array Les détails de la session de paiement.
     */
    public function stripeRequest(float $amount, string $description, string $email): array
    {
        // Simulation d'une création de session de paiement sécurisée
        $sessionId = 'cs_test_' . bin2hex(random_bytes(16)); 
        
        // Création de l'URL de redirection vers la page de vérification de la boutique
        $redirectUrl = URL . 'Checkout/index/?Authority=' . $sessionId;

        return array(
            'Status' => 100, 
            'Authority' => $sessionId, 
            'RedirectURL' => $redirectUrl,
            'Error' => ''
        );
    }

    /**
     * Vérifie l'état d'un paiement après le retour du client sur le site.
     * * @param float $amount Le montant attendu.
     * @param string $authority L'identifiant de la session de paiement.
     * @return array Le statut final de la transaction.
     */
    public function stripeVerify(float $amount, string $authority): array
    {
        // Simulation de la vérification avec les serveurs de Stripe
        if (strpos($authority, 'cs_test_') === 0) {
            
            // Le paiement est validé avec succès
            $refId = 'pi_test_' . bin2hex(random_bytes(12)); 
            return array(
                'Status' => 100, 
                'RefID' => $refId
            );

        } else {
            // Échec du paiement ou identifiant invalide
            return array(
                'Status' => 0, 
                'Error' => 'Le paiement a échoué ou a été annulé par le client.'
            );
        }
    }
}
?>