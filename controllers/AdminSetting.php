<?php

/**
 * Contrôleur AdminSetting
 * Gère la configuration globale du site.
 * Code standardisé et simplifié pour le niveau Junior (Règles DWWM).
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
    }

    /**
     * Affiche le formulaire des paramètres (Requête GET)
     */
    public function index()
    {
        $settings = $this->model->getSettings();
        
        $data = [
            'option' => $settings,
            // RÈGLE MVC : Appel de la méthode globale unifiée pour générer le jeton
            'csrf_token' => $this->generateCsrfToken()
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
            
        // VÉRIFICATION CSRF : Unifiée (Remplace le "die" bloquant)
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');
        
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