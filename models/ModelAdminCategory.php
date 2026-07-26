<?php

/**
 * Model ModelAdminCategory
 * Nettoyage des données (Sanitization avec strip_tags) pour éviter les failles XSS stockées.
 */
class ModelAdminCategory extends Model
{
    public $allChildrenIds = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function getCategory()
    {
        $sql = "SELECT * FROM categories";
        return $this->doSelect($sql);
    }

    public function getChildren($categoryId)
    {
        $sql = "SELECT * FROM categories WHERE parent_id = ?";
        return $this->doSelect($sql, [(int)$categoryId]);
    }

    public function getParents($categoryId)
    {
        $categoryInfo = $this->categoryInfo((int)$categoryId);
        if (!$categoryInfo) return [];

        $parentId = $categoryInfo['parent_id'];
        $allParents = [];

        while ($parentId != 0) {
            $sql = "SELECT * FROM categories WHERE id = ?";
            $parentCategory = $this->doSelect($sql, [(int)$parentId], true);
            if ($parentCategory) {
                $allParents[] = $parentCategory;
                $parentId = $parentCategory['parent_id'];
            } else {
                break;
            }
        }

        return $allParents;
    }

    public function categoryInfo($categoryId)
    {
        $sql = "SELECT * FROM categories WHERE id = ?";
        return $this->doSelect($sql, [(int)$categoryId], true);
    }

    public function addCategory($data, $editId)
    {
        // SÉCURITÉ : Utilisation de strip_tags au lieu de htmlspecialchars pour la BDD
        $title = strip_tags(trim($data['title'] ?? ''));
        $parentId = (int)($data['parent'] ?? 0);
        $editId = (int)$editId;

        if (empty($title)) return;

        if ($editId == 0) {
            $sql = "INSERT INTO categories (title, parent_id) VALUES (?, ?)";
            $this->doQuery($sql, [$title, $parentId]);
        } else {
            $sql = "UPDATE categories SET title = ?, parent_id = ? WHERE id = ?";
            $this->doQuery($sql, [$title, $parentId, $editId]);
        }
    }

    public function getChildsIds($categoryId)
    {
        $sql = "SELECT * FROM categories WHERE parent_id = ?";
        $children = $this->doSelect($sql, [(int)$categoryId]);
        
        foreach ($children as $child) {
            $this->allChildrenIds[] = (int)$child['id'];
            $this->getChildsIds((int)$child['id']);
        }
    }

    public function deleteCategory($ids)
    {
        if (empty($ids) || !is_array($ids)) return;

        // Assainissement des IDs
        $safeIds = array_map('intval', $ids);

        foreach ($safeIds as $id) {
            $this->allChildrenIds[] = $id;
            $this->getChildsIds($id);
        }

        $allIds = array_unique($this->allChildrenIds);
        if (empty($allIds)) return;

        $idsString = implode(',', $allIds);
        $sql = "DELETE FROM categories WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }

    public function getAttr($categoryId, $attrId)
    {
        $sql = "SELECT * FROM attributes WHERE category_id = ? AND parent_id = ?";
        return $this->doSelect($sql, [(int)$categoryId, (int)$attrId]);
    }

    public function attrInfo($attrId)
    {
        $sql = "SELECT * FROM attributes WHERE id = ?";
        return $this->doSelect($sql, [(int)$attrId], true);
    }

    public function addAttribute($data, $categoryId, $editId)
    {
        // SÉCURITÉ : Anti-XSS stocké
        $title = strip_tags(trim($data['title'] ?? ''));
        $parentId = (int)($data['parent'] ?? 0);
        $categoryId = (int)$categoryId;
        $editId = (int)$editId;

        if (empty($title)) return;

        if ($editId == 0) {
            $sql = "INSERT INTO attributes (title, parent_id, category_id) VALUES (?, ?, ?)";
            $this->doQuery($sql, [$title, $parentId, $categoryId]);
        } else {
            $sql = "UPDATE attributes SET title = ?, parent_id = ? WHERE id = ?";
            $this->doQuery($sql, [$title, $parentId, $editId]);
        }
    }

    public function deleteAttr($ids)
    {
        if (empty($ids) || !is_array($ids)) return;

        $safeIds = array_map('intval', $ids);
        $idsString = implode(',', $safeIds);
        
        $sql = "DELETE FROM attributes WHERE id IN (" . $idsString . ")";
        $this->doQuery($sql);
    }

    public function getAttrVal($attrId)
    {
        $sql = "SELECT * FROM attribute_values WHERE attribute_id = ?";
        return $this->doSelect($sql, [(int)$attrId]);
    }

    public function saveAttrVal($data, $attrId)
    {
        $safeAttrId = (int)$attrId;

        // 1. Insérer les nouvelles valeurs (SÉCURITÉ : strip_tags)
        $attrValNew = array_filter($data['attrvalnew'] ?? []);
        foreach ($attrValNew as $val) {
            $safeVal = strip_tags(trim($val));
            if (!empty($safeVal)) {
                $sql = "INSERT INTO attribute_values (attribute_id, value) VALUES (?, ?)";
                $this->doQuery($sql, [$safeAttrId, $safeVal]);
            }
        }
        
        // 2. Mettre à jour ou supprimer les valeurs existantes
        foreach ($data as $key => $val) {
            $keyParts = explode('-', $key);
            if (isset($keyParts[1]) && is_numeric($keyParts[1])) {
                $valId = (int)$keyParts[1];

                if (trim($val) != '') {
                    $safeVal = strip_tags(trim($val));
                    $sql = "UPDATE attribute_values SET value = ? WHERE id = ?";
                    $this->doQuery($sql, [$safeVal, $valId]);
                } else {
                    $sql = "DELETE FROM attribute_values WHERE id = ?";
                    $this->doQuery($sql, [$valId]);
                }
            }
        }
    }
}
?>