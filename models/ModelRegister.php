<?php

/**
 * Modèle ModelRegister
 * Gère la base de données pour l'inscription avec prévention des erreurs SQL.
 * Assure le hachage sécurisé des mots de passe.
 */
class ModelRegister extends Model 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function insertUser($data) 
    {
        // SÉCURITÉ : Nettoyage strict des données entrantes (Anti-XSS)
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $lastName = strip_tags(trim($data['last_name'] ?? ''));
        $mobile = strip_tags(trim($data['mobile'] ?? ''));
        $newsletter = isset($data['newsletter']) ? 1 : 0;
        
        // SÉCURITÉ CRITIQUE : Hachage du mot de passe avec l'algorithme standard de PHP
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Paramètres par défaut de l'utilisateur
        $roleId = 3; // Rôle 3 = Client standard
        $nationalId = '';
        $phone = '';
        $birthDate = '';
        $address = '';
        $gender = 1; 
        
        // CORRECTION DE BUG : Ajout des champs obligatoires (NOT NULL) pour éviter le plantage SQL
        $username = '';
        $city = '';
        $postalCode = '';
        
        $createdAt = self::jaliliDate('Y/m/d'); 

        // 1. Vérifier si l'adresse e-mail existe déjà
        $sqlCheck = "SELECT id FROM users WHERE email = ?";
        $result = $this->doSelect($sqlCheck, [$email]);

        // Si on trouve un résultat, l'inscription est refusée
        if (!empty($result)) {
            return false; 
        }

        // 2. Insérer le nouvel utilisateur dans la base de données avec tous les champs requis
        $sqlInsert = "INSERT INTO users (email, username, password, last_name, national_id, phone, mobile, birth_date, address, city, postal_code, gender, newsletter, role_id, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $values = [
            $email, $username, $hashedPassword, $lastName, $nationalId, $phone, $mobile, $birthDate, $address, $city, $postalCode, $gender, $newsletter, $roleId, $createdAt
        ];
        
        $this->doQuery($sqlInsert, $values);
        
        return true; 
    }
}
?>