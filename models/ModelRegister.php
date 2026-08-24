<?php

class ModelRegister extends Model 
{
    public function __construct() 
    {
        parent::__construct();
    }
    

    public function insertUser(array $data): bool 
    {
        // Nettoyage de l'e-mail
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        
        // SÉCURITÉ ANTI-XSS : Échappement des caractères spéciaux avant l'insertion en base de données
        $lastName = htmlspecialchars(trim($data['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $mobile = htmlspecialchars(trim($data['mobile'] ?? ''), ENT_QUOTES, 'UTF-8');
        $newsletter = isset($data['newsletter']) ? 1 : 0;
        
        // Hachage robuste du mot de passe avec l'algorithme par défaut (bcrypt)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Valeurs par défaut pour le profil d'un nouvel inscrit
        $roleId = 3; // 3 correspond au rôle client/utilisateur standard
        $nationalId = '';
        $phone = '';
        $birthDate = '';
        $address = '';
        $gender = 1; 
        $username = '';
        $city = '';
        $postalCode = '';
        
        // Gestion de la date 
        $createdAt = date('Y-m-d H:i:s'); 
        if (method_exists($this, 'getCurrentDate')) {
            $createdAt = self::getCurrentDate('Y-m-d H:i:s'); 
        }

        // Vérification de l'existence de l'e-mail pour éviter les doublons
        $sqlCheck = "SELECT id FROM users WHERE email = ?";
        $result = $this->doSelect($sqlCheck, [$email], 'fetch', PDO::FETCH_ASSOC);

        if (!empty($result)) {
            // Un compte avec cet e-mail existe déjà
            return false; 
        }

        // SÉCURITÉ : Insertion des données avec requête préparée (PDO) pour contrer les injections SQL
        $sqlInsert = "INSERT INTO users (email, username, password, last_name, national_id, phone, mobile, birth_date, address, city, postal_code, gender, newsletter, role_id, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $values = [
            $email, 
            $username, 
            $hashedPassword, 
            $lastName, 
            $nationalId, 
            $phone, 
            $mobile, 
            $birthDate, 
            $address, 
            $city, 
            $postalCode, 
            $gender, 
            $newsletter, 
            $roleId, 
            $createdAt
        ];

        $this->doQuery($sqlInsert, $values);
        
        return true;
    }
}
?>