<?php

class Controller
{
    protected $model;

    public function __construct()
    {
        // Détecter automatiquement le nom du contrôleur enfant
        $controllerName = get_class($this);
        
        // Construire le nom du modèle correspondant (Ex: AdminCategory -> ModelAdminCategory)
        $modelName = 'Model' . $controllerName;
        $modelPath = 'models/' . $modelName . '.php';

        // Charger et instancier le modèle s'il existe
        if (file_exists($modelPath)) {
            require $modelPath;
            $this->model = new $modelName();
        }
    }

    // GÉNÉRATEUR DE JETON CSRF : Crée un jeton unique et sécurisé pour les formulaires
    public function generateCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // VÉRIFICATEUR DE JETON CSRF : Valide le jeton reçu pour bloquer les requêtes malveillantes
    public function checkCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
            die("Erreur de sécurité : Jeton CSRF invalide ou expiré.");
        }
        return true;
    }

    // PROTECTION XSS (Junior-friendly) : Échappe les données dynamiques avant l'affichage dans la vue
    public function e($value)
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function view($viewName, $data = [])
    {
        // Extraire les données du tableau pour les transformer en variables simples
        extract($data);
        
        $controllerName = get_class($this);

        // 1. Si nous sommes dans l'administration (le nom du contrôleur commence par 'Admin')
        if (strpos($controllerName, 'Admin') === 0 && $controllerName !== 'AdminLogin') {
            
            // Générer automatiquement le menu actif (Ex: 'AdminCategory' devient 'category')
            $activeMenu = strtolower(str_replace('Admin', '', $controllerName));
            
            if (file_exists('views/admin/layout.php')) {
                require 'views/admin/layout.php';
            }
            
            require 'views/' . $viewName . '.php';
            
            if (file_exists('views/admin/footer.php')) {
                require 'views/admin/footer.php';
            }

        // 2. Si nous sommes sur le site public
        } else {
            if (file_exists('views/header.php') && $controllerName !== 'AdminLogin') {
                require 'views/header.php';
            }
            
            require 'views/' . $viewName . '.php';
            
            if (file_exists('views/footer.php') && $controllerName !== 'AdminLogin') {
                require 'views/footer.php';
            }
        }
    }
}
?>