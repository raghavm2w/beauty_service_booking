<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class User extends Model{
    
     public function findByEmail(string $email)
    {
          $stmt = $this->db->prepare(
            "SELECT id FROM users WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);

        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }
     public function register(array $data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users
            (name, email, phone, gender, password, role, is_verified, created_at)
            VALUES
            (:name, :email, :phone, :gender, :password, :role, 0, NOW())
        ");

        $stmt->execute([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'gender'   => $data['gender'] ?? null,
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'role'     => $data['role'] ?? 'customer',
        ]);

        return (int) $this->db->lastInsertId();
    }
}
