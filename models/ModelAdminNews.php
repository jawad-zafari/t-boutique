<?php

/**
 * Modèle ModelAdminNews
 * Gère les requêtes BDD des actualités et sécurise l'upload des images.
 */
class ModelAdminNews extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getNews()
    {
        $sql = "SELECT * FROM news ORDER BY id DESC";
        return $this->doSelect($sql);
    }

    public function getNewsById($id)
    {
        $sql = "SELECT * FROM news WHERE id = ?";
        $result = $this->doSelect($sql, [(int)$id]);
        return $result[0] ?? [];
    }

    public function addNews($data, $files)
    {
        // SÉCURITÉ CRITIQUE : Nettoyage des entrées avec htmlspecialchars (Anti-XSS Stocké)
        $title = htmlspecialchars(trim($data['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $shortDesc = htmlspecialchars(trim($data['short_desc'] ?? ''), ENT_QUOTES, 'UTF-8');
        $createdAt = date('Y-m-d'); 

        if (empty($title)) return;

        $sql = "INSERT INTO news (title, short_desc, image_path, created_at) VALUES (?, ?, '', ?)";
        $this->doQuery($sql, [$title, $shortDesc, $createdAt]);
        
        $newsId = self::$conn->lastInsertId();

        // Gestion de l'upload sécurisé
        $this->uploadImage($files, (int)$newsId);
    }

    public function editNews($id, $data, $files)
    {
        // SÉCURITÉ CRITIQUE : Nettoyage strict avec htmlspecialchars
        $title = htmlspecialchars(trim($data['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $shortDesc = htmlspecialchars(trim($data['short_desc'] ?? ''), ENT_QUOTES, 'UTF-8');
        $createdAt = htmlspecialchars(trim($data['created_at'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8');
        $safeId = (int)$id;

        if (empty($title)) return;

        $sql = "UPDATE news SET title = ?, short_desc = ?, created_at = ? WHERE id = ?";
        $this->doQuery($sql, [$title, $shortDesc, $createdAt, $safeId]);

        // Gestion de l'upload sécurisé en mode édition
        $this->uploadImage($files, $safeId, true);
    }

    public function deleteNews($id)
    {
        $safeId = (int)$id;
        $news = $this->getNewsById($safeId);
        
        // Supprimer physiquement l'image du serveur
        if (!empty($news['image_path']) && file_exists($news['image_path'])) {
            unlink($news['image_path']);
        }

        $sql = "DELETE FROM news WHERE id = ?";
        $this->doQuery($sql, [$safeId]);
    }

    /**
     * Méthode privée pour sécuriser l'upload des images
     */
    private function uploadImage($files, $id, $isEdit = false)
    {
        if (!empty($files['image']['name']) && $files['image']['error'] == 0) {
            
            // SÉCURITÉ CRITIQUE : Liste blanche des extensions autorisées
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $fileName = $files['image']['name'];
            $fileTmpName = $files['image']['tmp_name'];
            
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Bloquer si l'extension n'est pas valide (Empêche l'upload de web shells)
            if (!in_array($extension, $allowedExtensions)) {
                return; 
            }

            // SÉCURITÉ CRITIQUE : Vérification du type MIME réel
            $mimeType = mime_content_type($fileTmpName);
            if (strpos($mimeType, 'image/') !== 0) {
                return;
            }

            $uploadDir = 'public/images/news/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = 'news_' . (int)$id . '_' . time() . '.' . $extension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpName, $destination)) {
                
                // Supprimer l'ancienne image si c'est une modification
                if ($isEdit) {
                    $oldNews = $this->getNewsById((int)$id);
                    if (!empty($oldNews['image_path']) && file_exists($oldNews['image_path'])) {
                        unlink($oldNews['image_path']);
                    }
                }

                $sqlUpdate = "UPDATE news SET image_path = ? WHERE id = ?";
                $this->doQuery($sqlUpdate, [$destination, (int)$id]);
            }
        }
    }
}
?>