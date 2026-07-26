<?php

/**
 * Modèle ModelAdminComment
 * Gère les requêtes SQL liées à la modération des commentaires (Anti-XSS et Anti-Injection).
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
            
            // SÉCURITÉ CRITIQUE : Utilisation de strip_tags au lieu de htmlspecialchars pour la BDD
            // Cela empêche le XSS stocké tout en évitant le double-échappement lors de l'édition
            $title = strip_tags(trim($data['title_' . $id] ?? ''));
            $positive = strip_tags(trim($data['positive_points_' . $id] ?? ''));
            $negative = strip_tags(trim($data['negative_points_' . $id] ?? ''));
            $content = strip_tags(trim($data['content_' . $id] ?? ''));

            $params = [$title, $positive, $negative, $content, (int)$id];
            $this->doQuery($sql, $params);
        }

        // SÉCURITÉ CRITIQUE : Conversion forcée en entier pour bloquer l'injection SQL dans IN()
        $safeIds = array_map('intval', $data['id']);
        $idsString = implode(',', $safeIds);
        
        $sqlApprove = "UPDATE comments SET is_approved = 1 WHERE id IN (" . $idsString . ")";
        $this->doQuery($sqlApprove);
    }

    // Désapprouver ou masquer les commentaires sélectionnés
    public function unconfirm($ids)
    {
        if (empty($ids)) return;

        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $sql = "UPDATE comments SET is_approved = 0 WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }

    // Supprimer définitivement les commentaires de la base de données
    public function delete($ids)
    {
        if (empty($ids)) return;

        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $sql = "DELETE FROM comments WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }
}
?>