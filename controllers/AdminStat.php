<?php

/**
 * Contrôleur AdminStat
 * Gère la génération des rapports et statistiques des commandes.
 */
class AdminStat extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès
        Model::sessionInit();
        $level = Model::getUserLevel();
        if ($level != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }

        // PROTECTION CSRF : Génération globale du jeton
        if (!Model::sessionGet('csrf_token')) {
            Model::sessionSet('csrf_token', bin2hex(random_bytes(32)));
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
            'csrf_token' => Model::sessionGet('csrf_token')
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
            
        // VÉRIFICATION CSRF : Bloquer les requêtes automatisées malveillantes
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }
        
        // Demander au modèle de calculer les données
        $statistics = $this->model->order($_POST);
        
        $data = [
            'stat' => $statistics,
            'csrf_token' => Model::sessionGet('csrf_token')
        ];
        
        $this->view('admin/admin_statistics/results', $data);
    }
}
?>