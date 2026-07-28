<?php

/**
 * Classe App (Routeur principal du système MVC)
 * Analyse l'URL et charge le contrôleur, la méthode et les paramètres correspondants.
 */
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
                // Formater le nom du contrôleur (Ex: "adminProduct" -> "AdminProduct")
                $this->controller = ucfirst($url[0]);
                unset($url[0]);
            }

            if (isset($url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }

            // Réindexer le tableau des paramètres
            $this->params = array_values($url);
        }

        // 2. VALIDATION DE SÉCURITÉ : Bloquer les caractères spéciaux (Prévention LFI / Path Traversal)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $this->controller) || !preg_match('/^[a-zA-Z0-9_]+$/', $this->method)) {
            die("Erreur de sécurité : Caractères non autorisés détectés dans l'URL.");
        }

        // 3. Vérification de l'existence du fichier contrôleur
        $controllerPath = 'controllers/' . $this->controller . '.php';

        if (file_exists($controllerPath)) {
            require_once $controllerPath;

            // Vérifier si la classe existe dans le fichier chargé
            if (class_exists($this->controller)) {
                $controllerObject = new $this->controller();

                // 4. SÉCURITÉ DWWM : Réflexion pour vérifier que la méthode est publique et déclarée dans le contrôleur enfant
                if (method_exists($controllerObject, $this->method)) {
                    $reflection = new ReflectionMethod($controllerObject, $this->method);

                    // Empêcher l'exécution des méthodes héritées (ex: view, generateCsrfToken) ou non publiques
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

    /**
     * Nettoie et découpe l'URL passée en paramètre
     * @param string $url L'URL brute depuis $_GET['url']
     * @return array Tableau des segments de l'URL
     */
    private function parseUrl($url)
    {
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = rtrim($url, '/');
        return explode('/', $url);
    }
}
?>