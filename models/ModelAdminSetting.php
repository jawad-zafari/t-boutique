<?php

/**
 * Modèle ModelAdminSetting
 * Gère la lecture et la sauvegarde de la configuration du site.
 * Sécurisé contre les injections SQL et les failles XSS.
 */
class ModelAdminSetting extends Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // Récupérer tous les paramètres de la base de données
    public function getSettings()
    {
        $sql = "SELECT * FROM settings";
        $result = $this->doSelect($sql);
        
        $settings = [];
        // Transformer les résultats en un tableau associatif simple [clé => valeur]
        if (is_array($result)) {
            foreach ($result as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        }
        
        return $settings;
    }

    // Sauvegarder les paramètres dans la table "settings"
    public function saveSetting($data)
    {
        if (empty($data) || !is_array($data)) return;

        foreach ($data as $settingKey => $value) {
            
            // SÉCURITÉ : Ne pas enregistrer le jeton CSRF dans la base de données
            if ($settingKey === 'csrf_token') {
                continue;
            }

            // SÉCURITÉ CRITIQUE : Nettoyage contre les failles Stored XSS avec strip_tags.
            // On utilise strip_tags pour éviter le double échappement lors des futures éditions.
            $cleanValue = strip_tags(trim((string)$value));
            $cleanKey = strip_tags(trim((string)$settingKey));
            
            $sql = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
            $this->doQuery($sql, [$cleanValue, $cleanKey]);
        }
    }
}
?>