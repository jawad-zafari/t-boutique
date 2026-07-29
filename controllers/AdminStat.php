<?php

/**
 * Contrôleur AdminStat
 * Gère la génération des rapports et statistiques des commandes.
 * Respecte l'architecture MVC et la sécurité centralisée (CSRF).
 */
class AdminStat extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès (Admin uniquement)
        Model::sessionInit();
        $level = Model::getUserLevel();
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }
    }

    /**
     * Affiche le formulaire de sélection des dates
     */
    public function index()
    {
        $currentYear = date('Y');
        
        $data = [
            'currentYear' => $currentYear,
            // RÈGLE MVC : Appel de la méthode globale unifiée pour générer le jeton
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_statistics/reports', $data);
    }

    /**
     * Calcule et affiche les résultats des statistiques
     */
    public function orderStatistics()
    {
        // SÉCURITÉ CRITIQUE : N'accepter que les requêtes POST sécurisées
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée. Veuillez utiliser le formulaire.');
        }
            
        // VÉRIFICATION CSRF : Centralisée et non bloquante (Remplace le "die")
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
        // Demander au modèle de calculer les données
        $statistics = $this->model->order($_POST);
        
        $data = [
            'stat' => $statistics,
            'csrf_token' => $this->generateCsrfToken()
        ];
        
        $this->view('admin/admin_statistics/results', $data);
    }
}
?>