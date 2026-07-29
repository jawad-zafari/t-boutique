<?php

/**
 * Modèle ModelAdminSlider
 * Gère les requêtes BDD du diaporama et l'upload sécurisé des images.
 */
class ModelAdminSlider extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getslider()
    {
        $sql = "SELECT * FROM sliders ORDER BY id DESC";
        return $this->doSelect($sql);
    }

    public function getSliderById($id)
    {
        $sql = "SELECT * FROM sliders WHERE id = ?";
        return $this->doSelect($sql, [(int)$id], true);
    }

    public function addSlider($data, $files)
    {
        // SÉCURITÉ CRITIQUE : Nettoyage des entrées avec htmlspecialchars pour bloquer les failles Stored XSS
        $title = htmlspecialchars(trim($data['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $link = filter_var(trim($data['link'] ?? '#'), FILTER_SANITIZE_URL);
        $description = htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $button_text = htmlspecialchars(trim(!empty($data['button_text']) ? $data['button_text'] : 'Découvrir'), ENT_QUOTES, 'UTF-8');
        $text_color = htmlspecialchars(trim(!empty($data['text_color']) ? $data['text_color'] : '#ffffff'), ENT_QUOTES, 'UTF-8');
        
        $file = $files['image'] ?? null;
        $target = '';

        // SÉCURITÉ : Vérification stricte du fichier téléchargé
        if ($file && !empty($file['name']) && $file['error'] == 0) {
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowedExtensions)) {
                
                // Utilisation de mime_content_type pour vérifier le VRAI type du fichier
                $mimeType = mime_content_type($file['tmp_name']);
                
                if (strpos($mimeType, 'image/') === 0 && $file['size'] <= 5242880) { // Limite de 5MB
                    $targetMain = 'public/images/slider/';
                    $newName = uniqid('slide_') . '.' . $ext;
                    
                    if (!file_exists($targetMain)) {
                        mkdir($targetMain, 0777, true);
                    }
                    
                    $target = $targetMain . $newName;
                    if (!move_uploaded_file($file['tmp_name'], $target)) {
                        return false; 
                    }
                } else {
                    return false; 
                }
            } else {
                return false; 
            }
        }

        $sql = "INSERT INTO sliders (title, link, image_path, description, button_text, text_color) VALUES (?, ?, ?, ?, ?, ?)";
        $this->doQuery($sql, [$title, $link, $target, $description, $button_text, $text_color]);
        return true;
    }

    public function updateSlider($id, $data, $files)
    {
        // SÉCURITÉ CRITIQUE : Nettoyage avec htmlspecialchars
        $title = htmlspecialchars(trim($data['title'] ?? ''), ENT_QUOTES, 'UTF-8');
        $link = filter_var(trim($data['link'] ?? '#'), FILTER_SANITIZE_URL);
        $description = htmlspecialchars(trim($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
        $button_text = htmlspecialchars(trim(!empty($data['button_text']) ? $data['button_text'] : 'Découvrir'), ENT_QUOTES, 'UTF-8');
        $text_color = htmlspecialchars(trim(!empty($data['text_color']) ? $data['text_color'] : '#ffffff'), ENT_QUOTES, 'UTF-8');

        $sliderInfo = $this->getSliderById((int)$id);
        $imagePath = $sliderInfo['image_path'] ?? '';
        $file = $files['image'] ?? null;

        // Si une nouvelle image est téléchargée, vérifier sa sécurité
        if ($file && !empty($file['name']) && $file['error'] == 0) {
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (in_array($ext, $allowedExtensions)) {
                $mimeType = mime_content_type($file['tmp_name']);
                
                if (strpos($mimeType, 'image/') === 0 && $file['size'] <= 5242880) {
                    $targetMain = 'public/images/slider/';
                    $newName = uniqid('slide_') . '.' . $ext;
                    $target = $targetMain . $newName;

                    if (!file_exists($targetMain)) mkdir($targetMain, 0777, true);

                    if (move_uploaded_file($file['tmp_name'], $target)) {
                        // Supprimer l'ancienne image en toute sécurité
                        if (!empty($imagePath) && file_exists($imagePath)) {
                            unlink($imagePath);
                        }
                        $imagePath = $target;
                    } else {
                        return false;
                    }
                } else {
                    return false; 
                }
            } else {
                return false; 
            }
        }

        $sql = "UPDATE sliders SET title = ?, link = ?, image_path = ?, description = ?, button_text = ?, text_color = ? WHERE id = ?";
        $this->doQuery($sql, [$title, $link, $imagePath, $description, $button_text, $text_color, (int)$id]);
        return true;
    }

    public function delete($data)
    {
        $ids = $data['id'] ?? [];
        if (!empty($ids)) {
            
            // SÉCURITÉ : Transformation des ID en entiers pour éviter l'injection SQL
            $safeIds = array_map('intval', $ids);
            
            foreach ($safeIds as $id) {
                $sqlFind = "SELECT image_path FROM sliders WHERE id = ?";
                $result = $this->doSelect($sqlFind, [$id], true);
                
                if ($result && !empty($result['image_path']) && file_exists($result['image_path'])) {
                    unlink($result['image_path']);
                }
            }
            
            // SÉCURITÉ CRITIQUE (Standard DWWM) : Utilisation de placeholders dynamiques pour la clause IN()
            $placeholders = rtrim(str_repeat('?,', count($safeIds)), ',');
            $sql = "DELETE FROM sliders WHERE id IN ($placeholders)";
            $this->doQuery($sql, $safeIds);
        }
    }
}
?>