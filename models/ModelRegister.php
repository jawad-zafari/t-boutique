<?php

/**
 * Modèle ModelRegister
 * Insère un nouvel utilisateur de manière sécurisée.
 */
class ModelRegister extends Model 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Enregistre un utilisateur dans la base de données
     * @param array $data Les données du formulaire
     * @return bool Vrai si l'insertion réussit
     */
    public function insertUser(array $data): bool 
    {
        $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $data['password'] ?? '';
        $lastName = htmlspecialchars(trim($data['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
        $mobile = htmlspecialchars(trim($data['mobile'] ?? ''), ENT_QUOTES, 'UTF-8');
        $newsletter = isset($data['newsletter']) ? 1 : 0;
        
        // Hachage sécurisé
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $roleId = 3; 
        $nationalId = '';
        $phone = '';
        $birthDate = '';
        $address = '';
        $gender = 1; 
        $username = '';
        $city = '';
        $postalCode = '';
        $createdAt = self::jaliliDate('Y/m/d'); 

        // Vérification de l'existence de l'e-mail
        $sqlCheck = "SELECT id FROM users WHERE email = ?";
        $result = $this->doSelect($sqlCheck, [$email], 'fetch', PDO::FETCH_ASSOC);

        if (!empty($result)) {
            return false; 
        }

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