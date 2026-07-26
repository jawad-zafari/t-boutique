<?php

/**
 * Modèle ModelAdminProduct
 * Gère toutes les opérations de base de données liées aux produits, sécurisées contre les injections SQL et XSS.
 */
class ModelAdminProduct extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findProductImage($id, $size = 220) 
    {
        $basePath = 'public/images/products/' . (int)$id . '/product_' . (int)$size;
        $extensions = ['jpg', 'webp', 'png', 'jpeg'];
        
        foreach ($extensions as $ext) {
            if (file_exists($basePath . '.' . $ext)) {
                return URL . $basePath . '.' . $ext . '?v=' . time();
            }
        }
        return '';
    }

    public function getProduct()
    {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $products = $this->doSelect($sql);
        
        if (is_array($products)) {
            foreach($products as $key => $p) {
                $products[$key]['thumb_url'] = $this->findProductImage($p['id'], 220);
            }
        }
        return $products;
    }

    public function getCategory() { return $this->doSelect("SELECT * FROM categories"); }
    public function getColor() { return $this->doSelect("SELECT * FROM colors"); }
    public function getGarantee() { return $this->doSelect("SELECT * FROM guarantees"); }

    public function getProductInfo($id) 
    {
        if (empty($id)) return [];

        $sql = "SELECT * FROM products WHERE id = ?";
        $result = $this->doSelect($sql, [(int)$id], true);
        
        if ($result) {
            $sqlColors = "SELECT c.* FROM product_colors pc JOIN colors c ON pc.color_id = c.id WHERE pc.product_id = ?";
            $result['colorsInfo'] = $this->doSelect($sqlColors, [(int)$id]);
            
            $sqlGarantees = "SELECT g.* FROM product_guarantees pg JOIN guarantees g ON pg.guarantee_id = g.id WHERE pg.product_id = ?";
            $result['garanteesInfo'] = $this->doSelect($sqlGarantees, [(int)$id]);
        }
        
        return $result;
    }

    public function addProductAction($data, $productId, $file)
    {
        // SÉCURITÉ CRITIQUE : Nettoyage avec strip_tags pour prévenir le Stored XSS
        $title = strip_tags(trim($data['title'] ?? ''));
        
        // CORRECTION DE L'ERREUR SQL : On utilise exclusivement 'description' au lieu de 'introduction'
        $description = strip_tags(trim($data['description'] ?? ''), '<b><i><strong><em><u><ul><li><ol><p><br>');
        
        $categoryId = (int)($data['categoryId'] ?? 0);
        $price = (int)($data['price'] ?? 0);
        $discount = (int)($data['discount'] ?? 0);

        if (empty($title)) return;

        if (empty($productId)) {
            // CORRECTION : Changement de 'introduction' vers 'description' dans la requête SQL d'insertion
            $sql = "INSERT INTO products (title, category_id, price, discount_percent, description) VALUES (?, ?, ?, ?, ?)";
            $this->doQuery($sql, [$title, $categoryId, $price, $discount, $description]);
            $productId = self::$conn->lastInsertId();
        } else {
            // CORRECTION : Changement de 'introduction' vers 'description' dans la requête SQL de mise à jour
            $sql = "UPDATE products SET title = ?, category_id = ?, price = ?, discount_percent = ?, description = ? WHERE id = ?";
            $this->doQuery($sql, [$title, $categoryId, $price, $discount, $description, (int)$productId]);
            
            $this->doQuery("DELETE FROM product_colors WHERE product_id = ?", [(int)$productId]);
            $this->doQuery("DELETE FROM product_guarantees WHERE product_id = ?", [(int)$productId]);
        }

        if (!empty($data['color'])) {
            foreach ($data['color'] as $colorId) {
                $this->doQuery("INSERT INTO product_colors (product_id, color_id) VALUES (?, ?)", [(int)$productId, (int)$colorId]);
            }
        }
        
        if (!empty($data['garantee'])) {
            foreach ($data['garantee'] as $gId) {
                $this->doQuery("INSERT INTO product_guarantees (product_id, guarantee_id) VALUES (?, ?)", [(int)$productId, (int)$gId]);
            }
        }

        $this->uploadProductImage($file, $productId);
    }

    private function uploadProductImage($file, $productId)
    {
        if (!empty($file['name']) && $file['error'] == 0) {
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowedExtensions)) return;
            
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') !== 0) return;

            $folder = 'public/images/products/' . (int)$productId . '/';
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $dest = $folder . 'product_220.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $this->create_thumbnail($dest, $folder . 'product_220.' . $ext, 220, 220);
                $this->create_thumbnail($dest, $folder . 'product_350.' . $ext, 350, 350);
            }
        }
    }

    public function deleteProduct($ids)
    {
        if (empty($ids)) return;
        
        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $this->doQuery("DELETE FROM products WHERE id IN (" . $idsString . ")");
    }

    // ==========================================
    // MÉTHODES DE LA GALERIE
    // ==========================================

    public function getGallery($productId)
    {
        return $this->doSelect("SELECT * FROM product_galleries WHERE product_id = ? ORDER BY id DESC", [(int)$productId]);
    }

    public function addGallery($productId, $files)
    {
        if (empty($files['name'][0])) return;
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $folder = 'public/images/products/' . (int)$productId . '/gallery/large/';
        $smallFolder = 'public/images/products/' . (int)$productId . '/gallery/small/';
        
        if (!file_exists($folder)) mkdir($folder, 0777, true);
        if (!file_exists($smallFolder)) mkdir($smallFolder, 0777, true);

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] == 0) {
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExtensions)) continue;
                
                $mime = mime_content_type($files['tmp_name'][$i]);
                if (strpos($mime, 'image/') !== 0) continue;

                $fileName = time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dest = $folder . $fileName;

                if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                    $this->create_thumbnail($dest, $smallFolder . $fileName, 115, 115);
                    $this->doQuery("INSERT INTO product_galleries (product_id, image_name) VALUES (?, ?)", [(int)$productId, $fileName]);
                }
            }
        }
    }

    public function deleteGallery($ids)
    {
        if (empty($ids)) return;
        
        $safeIds = array_map('intval', $ids);
        
        foreach ($safeIds as $galleryId) {
            $result = $this->doSelect("SELECT * FROM product_galleries WHERE id=?", [$galleryId], true);
            if (!empty($result) && $result['image_name']) {
                $productId = $result['product_id'];
                
                $galleryLargePath = 'public/images/products/' . $productId . '/gallery/large/' . $result['image_name'];
                @unlink('public/images/products/' . $productId . '/gallery/small/' . $result['image_name']);
                @unlink($galleryLargePath);
            }
        }
        
        $idsString = implode(',', $safeIds);
        $this->doQuery("DELETE FROM product_galleries WHERE id IN (" . $idsString . ")");
    }

    // ==========================================
    // MÉTHODES DES ATTRIBUTS (CORRECTION MVC)
    // ==========================================

    public function getProductAttr($productId)
    {
        $productInfo = $this->getProductInfo($productId);
        $categoryId = $productInfo['category_id'] ?? 0;
        
        $sql = "SELECT a.*, (SELECT value_id FROM product_attribute_values pav WHERE pav.attribute_id = a.id AND pav.product_id = ?) as selected_val 
                FROM attributes a WHERE a.category_id = ?";
        
        $attributes = $this->doSelect($sql, [(int)$productId, (int)$categoryId]);

        if (is_array($attributes)) {
            foreach ($attributes as $key => $attr) {
                $sqlVals = "SELECT * FROM attribute_values WHERE attribute_id = ?";
                $attributes[$key]['possible_values'] = $this->doSelect($sqlVals, [(int)$attr['id']]);
            }
        }
        
        return $attributes;
    }

    public function editAttribute($data, $productId)
    {
        $ids = $data['id'] ?? [];
        foreach ($ids as $attrId) {
            $valId = $data['x' . $attrId] ?? '';
            
            $this->doQuery("DELETE FROM product_attribute_values WHERE product_id = ? AND attribute_id = ?", [(int)$productId, (int)$attrId]);
            
            if (!empty($valId)) {
                $this->doQuery("INSERT INTO product_attribute_values (product_id, attribute_id, value_id) VALUES (?, ?, ?)", [(int)$productId, (int)$attrId, (int)$valId]);
            }
        }
    }

    // ==========================================
    // MÉTHODES DES CRITIQUES
    // ==========================================

    public function getReview($productId)
    {
        return $this->doSelect("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC", [(int)$productId]);
    }

    public function getReviewInfo($reviewId)
    {
        if (empty($reviewId)) return [];
        return $this->doSelect("SELECT * FROM reviews WHERE id = ?", [(int)$reviewId], true);
    }

    public function addReview($data, $productId, $reviewId)
    {
        // SÉCURITÉ CRITIQUE : Nettoyage pour empêcher les attaques Stored XSS
        $title = strip_tags(trim($data['title'] ?? ''));
        $description = strip_tags(trim($data['description'] ?? ''), '<b><i><strong><em><u><ul><li><ol><p><br>');

        if (empty($title)) return;

        if (empty($reviewId)) {
            $sql = "INSERT INTO reviews (product_id, title, description) VALUES (?, ?, ?)";
            $this->doQuery($sql, [(int)$productId, $title, $description]);
        } else {
            $sql = "UPDATE reviews SET title = ?, description = ? WHERE id = ?";
            $this->doQuery($sql, [$title, $description, (int)$reviewId]);
        }
    }

    public function deleteReview($ids)
    {
        if (empty($ids)) return;
        
        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $this->doQuery("DELETE FROM reviews WHERE id IN (" . $idsString . ")");
    }
}
?>