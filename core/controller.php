<?php

/**
 * Classe Controller (Contrôleur de base)
 * Fournit les fonctionnalités communes : chargement des vues, gestion du CSRF et protection XSS.
 * Injecte automatiquement les données du Header pour respecter strictement l'architecture MVC.
 */
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
            require_once $modelPath;
            if (class_exists($modelName)) {
                $this->model = new $modelName();
            }
        }
    }

    /**
     * Génère un jeton CSRF unique pour sécuriser les formulaires
     * @return string Le jeton CSRF généré
     */
    public function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie la validité du jeton CSRF reçu
     * @param string $token Le jeton soumis par le formulaire
     * @return bool True si valide, sinon stoppe l'exécution
     */
    public function checkCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token']) || empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
            die("Erreur de sécurité : Jeton CSRF invalide ou expiré.");
        }
        return true;
    }

    /**
     * Échappe les données dynamiques avant l'affichage dans les vues (Protection XSS)
     * @param string|null $value La chaîne à sécuriser
     * @return string La chaîne échappée
     */
    public function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Charge une vue avec son layout et ses données
     * ARCHITECTURE DWWM : Injection automatique des données globales pour le Header
     * @param string $viewName Le nom du fichier vue (ex: 'index/index')
     * @param array $data Les données à passer à la vue
     */
    public function view(string $viewName, array $data = []): void
    {
        $controllerName = get_class($this);

        // Injection des données globales pour le site public (Non-Admin)
        if (strpos($controllerName, 'Admin') !== 0 || $controllerName === 'AdminLogin') {
            Model::sessionInit();
            $baseModel = new Model();
            $userId = Model::sessionGet('userId');

            if (!isset($data['menuList'])) {
                $data['menuList'] = $baseModel->getMenu(0);
            }

            if (!isset($data['cartItems']) || !isset($data['priceTotalAll'])) {
                $cartData = $baseModel->getCart();
                $data['cartItems'] = $cartData[0] ?? [];
                $data['priceTotalAll'] = $cartData[1] ?? 0;
            }

            if (!isset($data['cartCount'])) {
                $cartCount = 0;
                foreach ($data['cartItems'] as $item) {
                    $cartCount += (int)($item['quantity'] ?? $item['tedad'] ?? 1);
                }
                $data['cartCount'] = $cartCount;
            }

            if (!isset($data['favCount'])) {
                $data['favCount'] = $userId ? $baseModel->getFavoriteCount($userId) : 0;
            }

            if (!isset($data['userId'])) {
                $data['userId'] = $userId ?: false;
            }

            if (!isset($data['userLevel'])) {
                $data['userLevel'] = $userId ? Model::getUserLevel() : 0;
            }

            if (!isset($data['csrf_token'])) {
                $data['csrf_token'] = $_SESSION['csrf_token'] ?? $this->generateCsrfToken();
            }
        }

        // EXTR_SKIP empêche l'écrasement des variables internes de sécurité
        extract($data, EXTR_SKIP);

        // 1. Administration
        if (strpos($controllerName, 'Admin') === 0 && $controllerName !== 'AdminLogin') {

            $activeMenu = strtolower(str_replace('Admin', '', $controllerName));

            if (file_exists('views/admin/layout.php')) {
                require 'views/admin/layout.php';
            }

            if (file_exists('views/' . $viewName . '.php')) {
                require 'views/' . $viewName . '.php';
            }

            if (file_exists('views/admin/footer.php')) {
                require 'views/admin/footer.php';
            }

        // 2. Site public
        } else {
            if (file_exists('views/header.php') && $controllerName !== 'AdminLogin') {
                require 'views/header.php';
            }

            if (file_exists('views/' . $viewName . '.php')) {
                require 'views/' . $viewName . '.php';
            }

            if (file_exists('views/footer.php') && $controllerName !== 'AdminLogin') {
                require 'views/footer.php';
            }
        }
    }
}
?>