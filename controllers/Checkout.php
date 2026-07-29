<?php

/**
 * Controller Checkout
 * Gère le processus de paiement sécurisé (En ligne & Virement Bancaire).
 * Standard DWWM : Protection contre IDOR, CSRF et validation stricte des requêtes.
 */
class Checkout extends Controller
{
    public function __construct()
    {
        parent::__construct();
        // S'assure que la session est active pour l'utilisateur
        Model::sessionInit(); 
    }

    /**
     * Affiche la page de paiement ou gère le retour de la passerelle
     */
    public function index($orderId = null)
    {
        $data = [];

        // 1. Vérification du retour de la passerelle de paiement (ex: Stripe/Zarinpal)
        if (isset($_GET['Authority'])) {
            $safeAuthority = htmlspecialchars(trim($_GET['Authority']), ENT_QUOTES, 'UTF-8');
            $result = $this->model->stripeCheckout(['Authority' => $safeAuthority]);
            $data = ['orderInfo' => $result];
        }
        
        // 2. Vérification directe par ID de commande
        if ($orderId !== null) {
            $result = $this->model->getOrderInfo((int)$orderId);
            
            // SÉCURITÉ (IDOR) : Si la commande n'appartient pas à l'utilisateur connecté, on le bloque
            if (!$result) {
                header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Accès refusé ou commande introuvable.'));
                exit;
            }
            $data['orderInfo'] = $result;
        }

        // RÈGLE MVC : Injection du jeton CSRF unifié pour sécuriser les formulaires
        $data['csrf_token'] = $this->generateCsrfToken();
        
        $this->view('checkout/checkout', $data);
    }

    /**
     * Initie le paiement en ligne (Appelé depuis le formulaire de checkout.php)
     */
    public function payOnline($orderId)
    {
        // SÉCURITÉ CRITIQUE : Seules les requêtes POST sont autorisées pour initier un paiement
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // SÉCURITÉ : Vérification du jeton CSRF avant de procéder au paiement
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        $this->model->payOnline((int)$orderId);
    }

    /**
     * Affiche la page d'erreur
     */
    public function showError()
    {
        // Protection contre le Reflected XSS
        $error = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : 'Erreur inconnue';
        $orderId = isset($_GET['orderId']) ? (int)$_GET['orderId'] : 0;
        
        $data = [
            'Error'   => $error,
            'orderId' => $orderId,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('checkout/error', $data);
    }

    /**
     * Gère le paiement par virement bancaire manuel
     */
    public function bankTransfer($orderId)
    {
        // Traitement de la soumission du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');

            $this->model->updateCreditCard($_POST, (int)$orderId);
            
            header('Location: ' . URL . 'Checkout/index/' . (int)$orderId);
            exit;
        }

        // Affichage du formulaire (Requête GET)
        $orderInfo = $this->model->getOrderInfo((int)$orderId);
        
        // SÉCURITÉ (IDOR) : Vérifier que l'utilisateur a le droit d'accéder à cette commande
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande introuvable ou accès refusé.'));
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