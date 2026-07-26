<?php

/**
 * Modèle ModelAdminUser
 * Gère les opérations en base de données pour les utilisateurs.
 */
class ModelAdminUser extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Récupère la liste des utilisateurs avec leur rôle
     */
    public function getUsers()
    {
        // Jointure entre la table "users" et "user_roles" pour afficher le nom du rôle
        $sql = "SELECT users.*, user_roles.title as levelTitle
                FROM users
                LEFT JOIN user_roles ON users.role_id = user_roles.id
                ORDER BY users.id DESC";
                
        return $this->doSelect($sql);
    }

    /**
     * Changer le rôle d'un utilisateur en Administrateur (rôle 1)
     */
    public function changeLevel1($ids)
    {
        if (empty($ids)) return;
        
        // SÉCURITÉ CRITIQUE : Utilisation de array_map('intval')
        // Explication : Transforme chaque ID en nombre entier pur. Si un hacker
        // essaie d'envoyer du code SQL via la case à cocher, il sera converti en 0.
        $idsString = implode(',', array_map('intval', $ids));
        
        $sql = "UPDATE users SET role_id = 1 WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }
    
    /**
     * Changer le rôle d'un utilisateur en Employé (rôle 2)
     */
    public function changeLevel2($ids)
    {
        if (empty($ids)) return;
        
        $idsString = implode(',', array_map('intval', $ids));
        
        $sql = "UPDATE users SET role_id = 2 WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }

    /**
     * Changer le rôle d'un utilisateur en Utilisateur Normal (rôle 3)
     */
    public function changeLevel3($ids)
    {
        if (empty($ids)) return;
        
        $idsString = implode(',', array_map('intval', $ids));
        
        $sql = "UPDATE users SET role_id = 3 WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }

    /**
     * Supprimer des utilisateurs définitivement
     */
    public function delete($ids)
    {
        if (empty($ids)) return;
        
        $idsString = implode(',', array_map('intval', $ids));
        
        $sql = "DELETE FROM users WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }
}
?>