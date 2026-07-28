<?php

/**
 * Modèle ModelAdminProduct
 * Gère les opérations de base de données pour les produits.
 * Sécurité optimisée : Typage strict, Anti-XSS et requêtes préparées avec placeholders.
 */
class ModelAdminProduct extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function findProductImage(int $id, int $size = 220): string 
    {
        $basePath = 'public/images/products/' . $id . '/product_' . $size;
        $extensions = ['jpg', 'webp', 'png', 'jpeg'];
        
        foreach ($extensions as $ext) {
            if (file_exists($basePath . '.' . $ext)) {
                return URL . $basePath . '.' . $ext . '?v=' . time();
            }
        }
        return '';
    }

    public function getProduct(): array
    {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        $products = $this->doSelect($sql);
        
        if (is_array($products)) {
            foreach($products as $key => $p) {
                $products[$key]['thumb_url'] = $this->findProductImage((int)$p['id'], 220);
            }
        }
        return is_array($products) ? $products : [];
    }

    public function getCategory(): array { return $this->doSelect("SELECT * FROM categories") ?: []; }
    public function getColor(): array { return $this->doSelect("SELECT * FROM colors") ?: []; }
    public function getGarantee(): array { return $this->doSelect("SELECT * FROM guarantees") ?: []; }

    public function getProductInfo(int $id): array 
    {
        if (empty($id)) return [];

        $sql = "SELECT * FROM products WHERE id = ?";
        // CORRECTION : Utilisation de 'fetch' au lieu de true pour respecter la signature de Model::doSelect
        $result = $this->doSelect($sql, [$id], 'fetch');
        
        if ($result && is_array($result)) {
            $sqlColors = "SELECT c.* FROM product_colors pc JOIN colors c ON pc.color_id = c.id WHERE pc.product_id = ?";
            $result['colorsInfo'] = $this->doSelect($sqlColors, [$id]);
            
            $sqlGarantees = "SELECT g.* FROM product_guarantees pg JOIN guarantees g ON pg.guarantee_id = g.id WHERE pg.product_id = ?";
            $result['garanteesInfo'] = $this->doSelect($sqlGarantees, [$id]);
        }
        
        return is_array($result) ? $result : [];
    }

    public function addProductAction(array $data, int $productId, ?array $file): void
    {
        // SÉCURITÉ CRITIQUE : Assainissement contre le Stored XSS
        $title = strip_tags(trim($data['title'] ?? ''));
        $description = strip_tags(trim($data['description'] ?? ''), '<b><i><strong><em><u><ul><li><ol><p><br>');
        
        $categoryId = (int)($data['categoryId'] ?? 0);
        $price = (int)($data['price'] ?? 0);
        $discount = (int)($data['discount'] ?? 0);

        if (empty($title)) return;

        if (empty($productId)) {
            $sql = "INSERT INTO products (title, category_id, price, discount_percent, description) VALUES (?, ?, ?, ?, ?)";
            $this->doQuery($sql, [$title, $categoryId, $price, $discount, $description]);
            $productId = (int)self::$conn->lastInsertId();
        } else {
            $sql = "UPDATE products SET title = ?, category_id = ?, price = ?, discount_percent = ?, description = ? WHERE id = ?";
            $this->doQuery($sql, [$title, $categoryId, $price, $discount, $description, $productId]);
            
            $this->doQuery("DELETE FROM product_colors WHERE product_id = ?", [$productId]);
            $this->doQuery("DELETE FROM product_guarantees WHERE product_id = ?", [$productId]);
        }

        if (!empty($data['color']) && is_array($data['color'])) {
            foreach ($data['color'] as $colorId) {
                $this->doQuery("INSERT INTO product_colors (product_id, color_id) VALUES (?, ?)", [$productId, (int)$colorId]);
            }
        }
        
        if (!empty($data['garantee']) && is_array($data['garantee'])) {
            foreach ($data['garantee'] as $gId) {
                $this->doQuery("INSERT INTO product_guarantees (product_id, guarantee_id) VALUES (?, ?)", [$productId, (int)$gId]);
            }
        }

        if ($file !== null) {
            $this->uploadProductImage($file, $productId);
        }
    }

    private function uploadProductImage(array $file, int $productId): void
    {
        if (!empty($file['name']) && $file['error'] === 0) {
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($ext, $allowedExtensions)) return;
            
            $mime = mime_content_type($file['tmp_name']);
            if (strpos($mime, 'image/') !== 0) return;

            $folder = 'public/images/products/' . $productId . '/';
            if (!file_exists($folder)) mkdir($folder, 0777, true);

            $dest = $folder . 'product_220.' . $ext;
            
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $this->create_thumbnail($dest, $folder . 'product_220.' . $ext, 220, 220);
                $this->create_thumbnail($dest, $folder . 'product_350.' . $ext, 350, 350);
            }
        }
    }

    /**
     * Supprime plusieurs produits
     * SÉCURITÉ DWWM : Utilisation de placeholders dynamiques (?,?,?) pour la clause IN
     */
    public function deleteProduct(array $ids): void
    {
        if (empty($ids)) return;
        
        $safeIds = array_map('intval', $ids);
        $placeholders = rtrim(str_repeat('?,', count($safeIds)), ',');
        
        $sql = "DELETE FROM products WHERE id IN ($placeholders)";
        $this->doQuery($sql, $safeIds);
    }

    // ==========================================
    // MÉTHODES DE LA GALERIE
    // ==========================================

    public function getGallery(int $productId): array
    {
        $result = $this->doSelect("SELECT * FROM product_galleries WHERE product_id = ? ORDER BY id DESC", [$productId]);
        return is_array($result) ? $result : [];
    }

    public function addGallery(int $productId, ?array $files): void
    {
        if (empty($files['name'][0])) return;
        
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        $folder = 'public/images/products/' . $productId . '/gallery/large/';
        $smallFolder = 'public/images/products/' . $productId . '/gallery/small/';
        
        if (!file_exists($folder)) mkdir($folder, 0777, true);
        if (!file_exists($smallFolder)) mkdir($smallFolder, 0777, true);

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === 0) {
                $ext = strtolower(pathinfo($files['name'][$i], PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedExtensions)) continue;
                
                $mime = mime_content_type($files['tmp_name'][$i]);
                if (strpos($mime, 'image/') !== 0) continue;

                $fileName = time() . '_' . rand(1000, 9999) . '.' . $ext;
                $dest = $folder . $fileName;

                if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                    $this->create_thumbnail($dest, $smallFolder . $fileName, 115, 115);
                    $this->doQuery("INSERT INTO product_galleries (product_id, image_name) VALUES (?, ?)", [$productId, $fileName]);
                }
            }
        }
    }

    public function deleteGallery(array $ids): void
    {
        if (empty($ids)) return;
        
        $safeIds = array_map('intval', $ids);
        
        foreach ($safeIds as $galleryId) {
            $result = $this->doSelect("SELECT * FROM product_galleries WHERE id=?", [$galleryId], 'fetch');
            if (!empty($result) && $result['image_name']) {
                $productId = $result['product_id'];
                
                $galleryLargePath = 'public/images/products/' . $productId . '/gallery/large/' . $result['image_name'];
                @unlink('public/images/products/' . $productId . '/gallery/small/' . $result['image_name']);
                @unlink($galleryLargePath);
            }
        }
        
        // Requête préparée dynamique pour la suppression
        $placeholders = rtrim(str_repeat('?,', count($safeIds)), ',');
        $sql = "DELETE FROM product_galleries WHERE id IN ($placeholders)";
        $this->doQuery($sql, $safeIds);
    }

    // ==========================================
    // MÉTHODES DES ATTRIBUTS
    // ==========================================

    public function getProductAttr(int $productId): array
    {
        $productInfo = $this->getProductInfo($productId);
        $categoryId = (int)($productInfo['category_id'] ?? 0);
        
        $sql = "SELECT a.*, (SELECT value_id FROM product_attribute_values pav WHERE pav.attribute_id = a.id AND pav.product_id = ?) as selected_val 
                FROM attributes a WHERE a.category_id = ?";
        
        $attributes = $this->doSelect($sql, [$productId, $categoryId]);

        if (is_array($attributes)) {
            foreach ($attributes as $key => $attr) {
                $sqlVals = "SELECT * FROM attribute_values WHERE attribute_id = ?";
                $attributes[$key]['possible_values'] = $this->doSelect($sqlVals, [(int)$attr['id']]);
            }
        }
        
        return is_array($attributes) ? $attributes : [];
    }

    public function editAttribute(array $data, int $productId): void
    {
        $ids = $data['id'] ?? [];
        if (!is_array($ids)) return;

        foreach ($ids as $attrId) {
            $valId = $data['x' . $attrId] ?? '';
            
            $this->doQuery("DELETE FROM product_attribute_values WHERE product_id = ? AND attribute_id = ?", [$productId, (int)$attrId]);
            
            if (!empty($valId)) {
                $this->doQuery("INSERT INTO product_attribute_values (product_id, attribute_id, value_id) VALUES (?, ?, ?)", [$productId, (int)$attrId, (int)$valId]);
            }
        }
    }

    // ==========================================
    // MÉTHODES DES AVIS (REVIEWS)
    // ==========================================

    public function getReview(int $productId): array
    {
        $result = $this->doSelect("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC", [$productId]);
        return is_array($result) ? $result : [];
    }

    public function getReviewInfo(int $reviewId): array
    {
        if (empty($reviewId)) return [];
        $result = $this->doSelect("SELECT * FROM reviews WHERE id = ?", [$reviewId], 'fetch');
        return is_array($result) ? $result : [];
    }

    public function addReview(array $data, int $productId, int $reviewId): void
    {
        $title = strip_tags(trim($data['title'] ?? ''));
        $description = strip_tags(trim($data['description'] ?? ''), '<b><i><strong><em><u><ul><li><ol><p><br>');

        if (empty($title)) return;

        if (empty($reviewId)) {
            $sql = "INSERT INTO reviews (product_id, title, description) VALUES (?, ?, ?)";
            $this->doQuery($sql, [$productId, $title, $description]);
        } else {
            $sql = "UPDATE reviews SET title = ?, description = ? WHERE id = ?";
            $this->doQuery($sql, [$title, $description, $reviewId]);
        }
    }

    public function deleteReview(array $ids): void
    {
        if (empty($ids)) return;
        
        $safeIds = array_map('intval', $ids);
        $placeholders = rtrim(str_repeat('?,', count($safeIds)), ',');
        
        $sql = "DELETE FROM reviews WHERE id IN ($placeholders)";
        $this->doQuery($sql, $safeIds);
    }
}
?>