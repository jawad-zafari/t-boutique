<?php

/**
 * Contrôleur AdminOrder
 * Gère les commandes des clients avec une sécurité stricte (Vérification POST & CSRF).
 * Standards DWWM respectés.
 */
class AdminOrder extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification des droits d'accès. Seul l'administrateur (Niveau 1) peut y accéder.
        Model::sessionInit();
        $level = Model::getUserLevel();
        
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }
    }

    public function index()
    {
        $orders = $this->model->getOrders();
        $statuses = $this->model->orderStatus();
        
        $data = [
            'orders' => $orders,
            'statuses' => $statuses,
            'csrf_token' => $this->generateCsrfToken() // Utilisation de la méthode globale unifiée
        ];
        
        $this->view('admin/admin_order/orders', $data);
    }

    public function bulkUpdateStatus()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF UNIFIÉE : Remplace le "die" bloquant
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        if (isset($_POST['id']) && !empty($_POST['bulk_status_id'])) {
            $this->model->bulkUpdateStatus($_POST['id'], $_POST['bulk_status_id']);
        }
        
        header('Location: ' . URL . 'AdminOrder/index');
        exit;
    }

    public function detail($orderId)
    {
        $orderStatuses = $this->model->orderStatus();
        $orderInfo = $this->model->getOrderInfo((int)$orderId);
        
        $data = [
            'orderInfo' => $orderInfo,
            'order_status' => $orderStatuses,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_order/detail', $data);
    }

    public function editOrder($orderId)
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF UNIFIÉE
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        $this->model->editOrder((int)$orderId, $_POST);
        
        // Redirection après la mise à jour
        header('Location: ' . URL . 'AdminOrder/detail/' . (int)$orderId);
        exit;
    }

    public function showInvoice($orderId)
    {
        $orderInfo = $this->model->getOrderInfo((int)$orderId);
        $data = ['orderInfo' => $orderInfo];
        
        // Affichage de la facture (Sans Header ni Footer classiques de l'admin)
        $this->view('admin/admin_order/factor', $data, 1, 1);
    }

    public function delete()
    {
        // SÉCURITÉ CRITIQUE : Sécurisation de l'action de suppression contre les failles CSRF/GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }

        // VÉRIFICATION CSRF UNIFIÉE
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        if (!empty($_POST['id'])) {
            $this->model->delete($_POST);
        }
        
        header('Location: ' . URL . 'AdminOrder/index');
        exit;
    }
}
?>