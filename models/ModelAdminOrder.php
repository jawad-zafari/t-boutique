<?php

/**
 * Modèle ModelAdminOrder
 * Gère les requêtes BDD liées aux commandes en garantissant la protection contre l'injection SQL et XSS.
 */
class ModelAdminOrder extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getOrders()
    {
        $sql = "SELECT o.*, os.title as statusTitle 
                FROM orders o 
                LEFT JOIN order_statuses os ON o.status_id = os.id 
                ORDER BY o.id DESC";
        return $this->doSelect($sql);
    }

    public function bulkUpdateStatus($ids, $statusId)
    {
        if (empty($ids) || empty($statusId)) return;
        
        // SÉCURITÉ : Conversion des ID en entiers pour bloquer les injections SQL (IN clause)
        $sanitizedIds = array_map('intval', $ids);
        $idsString = implode(',', $sanitizedIds);
        
        $sql = "UPDATE orders SET status_id = ? WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql, [(int)$statusId]);
    }

    public function getOrderInfo($orderId)
    {
        $sql = "SELECT o.*, pa.title as payTypeTitle, po.title as postTitle
                FROM orders o 
                LEFT JOIN payment_methods pa ON o.payment_method_id = pa.id
                LEFT JOIN shipping_methods po ON o.shipping_method_id = po.id
                WHERE o.id = ?";

        return $this->doSelect($sql, [(int)$orderId], true);
    }

    public function editOrder($orderId, $data)
    {
        // SÉCURITÉ CRITIQUE : Utilisation de htmlspecialchars pour une protection robuste contre le Stored XSS
        $address = htmlspecialchars(trim($data['address'] ?? ''), ENT_QUOTES, 'UTF-8');
        $postalCode = htmlspecialchars(trim($data['postal_code'] ?? ''), ENT_QUOTES, 'UTF-8');
        $phone = htmlspecialchars(trim($data['phone'] ?? ''), ENT_QUOTES, 'UTF-8');
        $trackingCode = htmlspecialchars(trim($data['tracking_code'] ?? ''), ENT_QUOTES, 'UTF-8');
        $adminNote = htmlspecialchars(trim($data['admin_note'] ?? ''), ENT_QUOTES, 'UTF-8');
        
        // Typage strict pour les valeurs numériques
        $payStatus = (int)($data['pay_status'] ?? 0);
        $orderStatus = (int)($data['order_status'] ?? 1);
        $safeOrderId = (int)$orderId;

        $sql = "UPDATE orders SET address_data = ?, postal_code = ?, phone = ?, is_paid = ?, status_id = ?, tracking_code = ?, admin_note = ? WHERE id = ?";
        
        $this->doQuery($sql, [$address, $postalCode, $phone, $payStatus, $orderStatus, $trackingCode, $adminNote, $safeOrderId]);
    }

    public function orderStatus()
    {
        $sql = "SELECT * FROM order_statuses";
        return $this->doSelect($sql);
    }

    public function delete($data)
    {
        if (empty($data['id'])) return;
        
        // SÉCURITÉ CRITIQUE : Utilisation de array_map('intval') pour garantir 
        // qu'aucune commande SQL malveillante ne passe par les cases à cocher.
        $safeIds = array_map('intval', $data['id']);
        $idsString = implode(',', $safeIds);
        
        $sql = "DELETE FROM orders WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }
}
?>