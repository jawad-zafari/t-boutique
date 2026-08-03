<?php

/**
 * Controller Checkout
 * Gère le processus de confirmation de paiement et le suivi de commande.
 * Simulation de passerelle de paiement (Mock) intégrée pour l'examen DWWM.
 * Standard DWWM : Protection contre IDOR, CSRF et validation stricte des requêtes.
 */
class Checkout extends Controller
{
    public function __construct()
    {
        parent::__construct();
        Model::sessionInit(); 
    }

    /**
     * Page principale de confirmation de commande et d'affichage de la facture
     */
    public function index($orderId = null)
    {
        if ($orderId === null) {
            header('Location: ' . URL . 'Account/orders');
            exit;
        }

        $orderInfo = $this->model->getOrderInfo((int)$orderId);
        
        // SÉCURITÉ (IDOR) : Si la commande n'appartient pas à l'utilisateur connecté, on bloque
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande introuvable ou accès non autorisé.') . '&orderId=' . (int)$orderId);
            exit;
        }

        $data = [
            'orderInfo'  => $orderInfo,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('checkout/checkout', $data);
    }

    /**
     * Affichage des erreurs de paiement de la simulation
     */
    public function showError()
    {
        $error = $_GET['error'] ?? 'Une erreur est survenue lors de votre paiement.';
        $orderId = (int)($_GET['orderId'] ?? 0);
        
        $data = [
            'Error'      => $error,
            'orderId'    => $orderId,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('checkout/error', $data);
    }

    /**
     * Simulation du traitement de paiement (AJAX) - 100% Hors Ligne
     * Appelé par JavaScript après un délai de simulation
     */
    public function processMockPaymentAjax($orderId)
    {
        // 1. Vérification de la méthode POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
            exit;
        }

        // 2. Vérification du jeton CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        // 3. Vérification de la propriété de la commande (Anti-IDOR)
        $orderId = (int)$orderId;
        $orderInfo = $this->model->getOrderInfo($orderId);
        
        if (!$orderInfo) {
            echo json_encode(['status' => 'error', 'message' => 'Commande introuvable ou accès refusé.']);
            exit;
        }

        // Idempotence : Si la commande est déjà payée, on renvoie succès directement
        if (!empty($orderInfo['is_paid'])) {
            echo json_encode(['status' => 'success']);
            exit;
        }

        // 4. Simulation d'une probabilité de succès (85% de réussite pour la démo)
        $successChance = rand(1, 100);
        
        if ($successChance <= 85) {
            // Simulation de succès : Création d'une référence de transaction factice
            $transactionId = 'TXN-' . date('YmdHis') . '-' . rand(1000, 9999);
            
            // Mise à jour en base de données
            $this->model->markOrderAsPaid($orderId, $transactionId);
            
            echo json_encode(['status' => 'success']);
        } else {
            // Simulation d'échec : Fonds insuffisants ou erreur réseau de la banque
            echo json_encode(['status' => 'error', 'message' => 'Paiement refusé par votre établissement bancaire (Simulation).']);
        }
        exit;
    }

    /**
     * Formulaire d'enregistrement des informations de virement bancaire manuel
     */
    public function bankTransfer($orderId)
    {
        $orderId = (int)$orderId;
        $orderInfo = $this->model->getOrderInfo($orderId);
        
        if (!$orderInfo) {
            header('Location: ' . URL . 'Checkout/showError?error=' . urlencode('Commande introuvable ou accès non autorisé.') . '&orderId=' . $orderId);
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkCsrfToken($_POST['csrf_token'] ?? '');

            if (empty($_POST['creditcard'])) {
                header('Location: ' . URL . 'Checkout/bankTransfer/' . $orderId . '?error=missing_card');
                exit;
            }

            $this->model->updateCreditCard($_POST, $orderId);
            header('Location: ' . URL . 'Checkout/index/' . $orderId);
            exit;
        }

        $data = [
            'orderInfo'  => $orderInfo,
            'csrf_token' => $this->generateCsrfToken()
        ];
        $this->view('checkout/bank_transfer', $data);
    }
}