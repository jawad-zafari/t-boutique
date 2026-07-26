<?php

/**
 * Controller Checkout
 * Sécurisé pour le processus de paiement (Validation CSRF, IDOR et méthodes HTTP)
 */
class Checkout extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit(); // S'assure que la session est active
    }

    public function index($orderId = null)
    {
        $data = [];

        // Vérification du retour de la passerelle de paiement
        if (isset($_GET['Authority'])) {
            $safeAuthority = htmlspecialchars(trim($_GET['Authority']), ENT_QUOTES, 'UTF-8');
            $result = $this->model->stripeCheckout(['Authority' => $safeAuthority]);
            $data = ['orderInfo' => $result];
        }
        
        // Vérification directe par ID de commande
        if ($orderId !== null) {
            $result = $this->model->getOrderInfo((int)$orderId);
            
            // SÉCURITÉ : Si la commande n'appartient pas à l'utilisateur, on le bloque
            if (!$result) {
                header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Accès refusé ou commande introuvable.'));
                exit;
            }
            $data = ['orderInfo' => $result];
        }

        // PROTECTION CSRF : Ajout du jeton pour le formulaire de sélection de paiement
        $data['csrf_token'] = $this->generateCsrfToken();

        $this->view('checkout/checkout', $data);
    }

    public function payOnline($orderId)
    {
        // SÉCURITÉ CRITIQUE : Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée. Veuillez utiliser le bouton de paiement.');
        }

        // VÉRIFICATION CSRF : Empêcher le lancement forcé de paiements par des scripts tiers
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->payOnline((int)$orderId);
    }

    public function showError()
    {
        // SÉCURITÉ : Assainissement des erreurs renvoyées dans l'URL
        $error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : 'Erreur inconnue';
        $orderId = isset($_GET['orderId']) ? (int)$_GET['orderId'] : 0;
        
        $data = [
            'Error'   => $error,
            'orderId' => $orderId
        ];
        
        $this->view('checkout/error', $data);
    }

    public function bankTransfer($orderId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // SÉCURITÉ : Vérification du token CSRF
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');

            $this->model->updateCreditCard($_POST, (int)$orderId);
            header('Location: ' . URL . 'Checkout/index/' . (int)$orderId);
            exit;
        }

        // Si c'est une requête GET, on affiche le formulaire
        $orderInfo = $this->model->getOrderInfo((int)$orderId);
        
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Accès refusé.'));
            exit;
        }

        $data = [
            'orderInfo'  => $orderInfo,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('checkout/bank_transfer', $data);
    }
}
?>