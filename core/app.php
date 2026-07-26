<?php

class App
{
    public $controller = 'Index';
    public $method = 'index';
    public $params = [];

    public function __construct()
    {
        if (isset($_GET['url'])) {
            $url = $_GET['url'];
            $url = $this->parseUrl($url);
            
            // Formatage propre : Première lettre en majuscule
            $this->controller = ucfirst($url[0]);
            unset($url[0]);
            
            if (isset($url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }

            // Réindexation du tableau des paramètres
            $this->params = array_values($url);
        }
        
        // VALIDATION DE SÉCURITÉ : Autoriser uniquement les lettres, les chiffres et les underscores
        // Cette vérification stricte empêche les attaques de type Path Traversal (LFI)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->controller) || !preg_match('/^[a-zA-Z0-9_]+$/', $this->method)) {
            die("Erreur de sécurité : Caractères non autorisés détectés dans l'URL.");
        }

        $controllerPath = 'controllers/' . $this->controller . '.php';
        
        if (file_exists($controllerPath)) {
            require($controllerPath);
            $object = new $this->controller();

            if (method_exists($object, $this->method)) {
                call_user_func_array([$object, $this->method], $this->params);
            }
        } else {
            die("Erreur système : Le contrôleur '" . $this->controller . ".php' est introuvable dans le dossier controllers.");
        }
    }

    private function parseUrl($url)
    {
        // Nettoyage de l'URL pour la sécurité
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = rtrim($url, '/');
        $url = explode('/', $url);
        return $url;
    }
}
?>