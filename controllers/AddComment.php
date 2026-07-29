<?php

/**
 * Controller AddComment
 * Gestion de l'ajout des commentaires et avis.
 * Règle MVC respectée : Le contrôleur gère la session et la passe au modèle.
 */
class AddComment extends Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // SÉCURITÉ : Vérification de l'authentification
        // Seuls les utilisateurs connectés peuvent laisser un avis
        Model::sessionInit();
        $userId = Model::sessionGet('userId');

        if ($userId == false) {
            header('Location: ' . URL . 'Login/index');
            exit;
        }
    }

    public function index($productId)
    {
        $userId = Model::sessionGet('userId');

        // PROTECTION CSRF ET MVC : On passe le userId au modèle pour récupérer les infos
        $data = [
            'params'      => $this->model->getParam((int)$productId),
            'productInfo' => $this->model->productInfo((int)$productId),
            'commentInfo' => $this->model->commentInfo((int)$productId, $userId),
            'csrf_token'  => $this->generateCsrfToken()
        ];
        
        $this->view('comment/add_comment', $data);
    }

    public function saveComment($productId)
    {
        // SÉCURITÉ : Vérifier que la requête est bien de type POST (Bloque le Method Spoofing)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit('Méthode non autorisée. Veuillez utiliser le formulaire.');
        }

        // VÉRIFICATION CSRF
        $this->checkCsrfToken($_POST['csrf_token'] ?? '');

        // ARCHITECTURE MVC : Le contrôleur lit la session et l'envoie au modèle
        $userId = Model::sessionGet('userId');

        // Sauvegarder le commentaire dans la base de données
        $this->model->saveComment($_POST, (int)$productId, $userId);
        
        // Redirection vers la page du produit après la soumission (PRG Pattern)
        header('Location: ' . URL . 'Product/index/' . (int)$productId);
        exit;
    }
}
?>