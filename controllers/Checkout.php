<?php

/**
 * Controller Checkout
 * Gère le processus de paiement sécurisé (Stripe).
 * Code simplifié et adapté au niveau DWWM.
 */
class Checkout extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit(); // S'assure que la session est active
    }

    /**
     * Affiche la page de paiement ou gère le retour de Stripe
     */
    public function index($orderId = null)
    {
        $data = [];

        // 1. Vérification du retour de la passerelle de paiement (Stripe)
        if (isset($_GET['Authority'])) {
            $safeAuthority = htmlspecialchars(trim($_GET['Authority']), ENT_QUOTES, 'UTF-8');
            $result = $this->model->stripeCheckout(['Authority' => $safeAuthority]);
            $data = ['orderInfo' => $result];
        }
        
        // 2. Vérification directe par ID de commande
        if ($orderId !== null) {
            $result = $this->model->getOrderInfo((int)$orderId);
            
            // SÉCURITÉ (IDOR) : Si la commande n'appartient pas à l'utilisateur, on le bloque
            if (!$result) {
                header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Accès refusé ou commande introuvable.'));
                exit;
            }
            $data = ['orderInfo' => $result];
        }

        // PROTECTION CSRF : Ajout du jeton pour le formulaire
        $data['csrf_token'] = $this->generateCsrfToken();
        $this->view('checkout/checkout', $data);
    }

    /**
     * Lance le processus de paiement en ligne
     * CORRECTION : Le nom de la méthode correspond désormais au formulaire HTML (payOnline)
     */
    public function payOnline($orderId)
    {
        // SÉCURITÉ : Vérification du jeton CSRF avant de procéder au paiement
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        $this->model->payOnline((int)$orderId);
    }

    /**
     * Affiche la page d'erreur
     */
    public function showError()
    {
        $error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : 'Erreur inconnue';
        $orderId = isset($_GET['orderId']) ? (int)$_GET['orderId'] : 0;
        
        $data = [
            'Error'   => $error,
            'orderId' => $orderId
        ];
        
        $this->view('checkout/error', $data);
    }

    /**
     * Gère le paiement par virement bancaire manuel
     */
    public function bankTransfer($orderId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');

            $this->model->updateCreditCard($_POST, (int)$orderId);
            header('Location: ' . URL . 'Checkout/index/' . (int)$orderId);
            exit;
        }

        $orderInfo = $this->model->getOrderInfo((int)$orderId);
        
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande invalide.'));
            exit;
        }

        $data = [
            'orderInfo' => $orderInfo,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('checkout/bank_transfer', $data);
    }
}
?>