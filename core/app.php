<?php

class App
{
    protected $controller = 'Index';
    protected $method = 'index';
    protected $params = [];

    public function __construct()
    {
        // 1. Récupération et découpage de l'URL
        if (isset($_GET['url'])) {
            $url = $this->parseUrl($_GET['url']);

            if (!empty($url[0])) {
                $this->controller = ucfirst($url[0]);
                unset($url[0]);
            }

            if (isset($url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }

            // Réindexer le tableau des paramètres restants
            $this->params = array_values($url);
        }

        // Bloquer les caractères spéciaux 
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->controller) || !preg_match('/^[a-zA-Z0-9_]+$/', $this->method)) {
            die("Erreur de sécurité : Caractères non autorisés détectés dans l'URL.");
        }

        // Chargement sécurisé du contrôleur
        $controllerPath = 'controllers/' . $this->controller . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;

            if (class_exists($this->controller)) {
                $controllerObject = new $this->controller();

                // Vérification de l'existence de la méthode dans le contrôleur enfant
                if (method_exists($controllerObject, $this->method)) {
                    $reflection = new ReflectionMethod($controllerObject, $this->method);

                    // SÉCURITÉ : Empêcher l'exécution de méthodes héritées ou non publiques
                    if ($reflection->isPublic() && $reflection->getDeclaringClass()->getName() === $this->controller) {
                        call_user_func_array([$controllerObject, $this->method], $this->params);
                    } else {
                        die("Erreur de sécurité : Action non autorisée ou méthode inaccessible.");
                    }
                } else {
                    die("Erreur système : La méthode '" . $this->method . "' n'existe pas dans le contrôleur " . $this->controller . ".");
                }
            } else {
                die("Erreur système : La classe '" . $this->controller . "' est introuvable.");
            }
        } else {
            die("Erreur système : Le contrôleur '" . $this->controller . ".php' est introuvable.");
        }
    }

    
    // Nettoie et découpe l'URL passée en paramètre
     
    private function parseUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = rtrim($url, '/');
        return explode('/', $url);
    }
}