<?php

/**
 * Contrôleur AdminDashboard
 * Gère l'affichage du tableau de bord de l'administration.
 */
class AdminDashboard extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ ET CONTRÔLE D'ACCÈS (RBAC) : 
        // Initialisation de session et autorisation uniquement pour Admin (1) et Employé (2)
        Model::sessionInit();
        $userLevel = Model::getUserLevel();
        
        if ($userLevel != 1 && $userLevel != 2) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit; 
        }
    }

    public function index()
    {
        // Récupération des statistiques (corrigées et optimisées) depuis le modèle
        $orderStatistics = $this->model->getStat();
        
        $data = [
            'orderStat' => $orderStatistics
        ];
        
        // Chargement de la vue du tableau de bord
        $this->view('admin/admin_dashboard/dashboard', $data);    
    }
}
?>