<?php

/**
 * Contrôleur AdminSetting
 * Gère la configuration globale du site (Sécurisé par accès Niveau 1 et CSRF).
 */
class AdminSetting extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification stricte des droits d'accès (Seul l'administrateur a accès)
        Model::sessionInit();
        $userLevel = Model::getUserLevel();
        if ($userLevel != 1) {
            header('Location: ' . URL . 'AdminLogin/index');
            exit;
        }

        // PROTECTION CSRF : Génération globale du jeton
        if (!Model::sessionGet('csrf_token')) {
            Model::sessionSet('csrf_token', bin2hex(random_bytes(32)));
        }
    }

    /**
     * Affiche le formulaire des paramètres (Requête GET)
     */
    public function index()
    {
        $settings = $this->model->getSettings();
        
        $data = [
            'option' => $settings,
            'csrf_token' => Model::sessionGet('csrf_token')
        ];

        $this->view('admin/admin_setting/settings', $data);
    }

    /**
     * Traite la sauvegarde des paramètres (Requête POST)
     */
    public function update()
    {
        // SÉCURITÉ CRITIQUE : Bloquer tout accès direct via GET
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée');
        }
            
        // VÉRIFICATION CSRF : Bloquer les attaques de falsification de requêtes
        $token = $_POST['csrf_token'] ?? '';
        if ($token !== Model::sessionGet('csrf_token')) {
            die('Erreur de sécurité : Jeton CSRF invalide.');
        }
        
        // Gestion spécifique de la case à cocher (checkbox)
        if (!isset($_POST['maintenance_mode'])) {
            $_POST['maintenance_mode'] = '0';
        }

        $this->model->saveSetting($_POST);
        
        // Redirection avec un paramètre de succès (PRG pattern : Post/Redirect/Get)
        header('Location: ' . URL . 'AdminSetting/index?success=1');
        exit;
    }
}
?>