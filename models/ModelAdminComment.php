<?php

/**
 * Modèle ModelAdminComment
 * Gère les requêtes SQL liées à la modération des commentaires.
 * Sécurité renforcée (Anti-XSS avec htmlspecialchars et Anti-Injection).
 */
class ModelAdminComment extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Obtenir la liste de tous les commentaires ordonnés par ID décroissant
    public function getComment()
    {
        $sql = "SELECT * FROM comments ORDER BY id DESC";
        return $this->doSelect($sql);
    }

    // Valider, modifier et approuver les commentaires sélectionnés
    public function confirm($data)
    {
        if (empty($data['id'])) return;

        foreach ($data['id'] as $id) {
            $sql = "UPDATE comments SET title = ?, positive_points = ?, negative_points = ?, content = ? WHERE id = ?";
            
            // SÉCURITÉ CRITIQUE : Utilisation de htmlspecialchars pour bloquer le XSS stocké
            $title = htmlspecialchars(trim($data['title_' . $id] ?? ''), ENT_QUOTES, 'UTF-8');
            $positive = htmlspecialchars(trim($data['positive_points_' . $id] ?? ''), ENT_QUOTES, 'UTF-8');
            $negative = htmlspecialchars(trim($data['negative_points_' . $id] ?? ''), ENT_QUOTES, 'UTF-8');
            $content = htmlspecialchars(trim($data['content_' . $id] ?? ''), ENT_QUOTES, 'UTF-8');

            $params = [$title, $positive, $negative, $content, (int)$id];
            $this->doQuery($sql, $params);
        }

        // SÉCURITÉ CRITIQUE : Conversion forcée en entier pour bloquer l'injection SQL dans IN()
        $safeIds = array_map('intval', $data['id']);
        $idsString = implode(',', $safeIds);
        
        if(!empty($idsString)) {
            $sqlApprove = "UPDATE comments SET is_approved = 1 WHERE id IN (" . $idsString . ")";
            $this->doQuery($sqlApprove);
        }
    }

    // Désapprouver ou masquer les commentaires sélectionnés
    public function unconfirm($ids)
    {
        if (empty($ids)) return;

        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        if(!empty($idsString)) {
            $sql = "UPDATE comments SET is_approved = 0 WHERE id IN (" . $idsString . ")";
            $this->doQuery($sql);
        }
    }

    // Supprimer définitivement les commentaires de la base de données
    public function delete($ids)
    {
        if (empty($ids)) return;

        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        if(!empty($idsString)) {
            $sql = "DELETE FROM comments WHERE id IN (" . $idsString . ")";
            $this->doQuery($sql);
        }
    }
}
?>