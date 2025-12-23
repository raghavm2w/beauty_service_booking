<?php
namespace App\Models;
use App\Core\Model;
use App\Helpers\JwtHelper as JWT;
use PDO;

class User extends Model{

     public function findByEmail(string $email)
    {
          $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE email = :email LIMIT 1"
        );
        $stmt->execute(['email' => $email]);

        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }
     public function register(array $data)
    {
        try{
        $this->db->beginTransaction();
        $stmt = $this->db->prepare("
            INSERT INTO users
            (name, email, phone, gender, password, role, is_verified)
            VALUES
            (:name, :email, :phone, :gender, :password, :role,0)
        ");

        $stmt->execute([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'gender'   => $data['gender'] ?? null,
            'password' => $data['password'],
            'role'     => $data['role'] ?? 0,
        ]);

        $userId = $this->db->lastInsertId();
         $accessToken = JWT::generateAccessToken($userId,$data['role']);
        $refreshData = JWT::generateRefreshToken($userId);
        $this->setRefreshToken($refreshData);// store in db
        $this->db->commit();
        return [
                'user_id'=>$userId,
                'access_token'=>$accessToken
            ];

    }catch(\Exception $e){
        $this->db->rollBack();
        throw $e;
    }
    }
    public function setRefreshToken($data){
        $refreshSql = "INSERT INTO refresh_tokens (user_id, refresh_token, expires_at)
               VALUES (:user_id, :refresh_token, :expires_at)";

        $stmt = $this->db->prepare($refreshSql);
        $stmt->execute([
            ':user_id'       => $data['user_id'],
             ':refresh_token' => $data['token'],
            ':expires_at'    => $data['expires_at']
        ]);
    }
    public function deleteRefreshToken(int $userId)
{
    $stmt = $this->db->prepare("
        DELETE FROM refresh_tokens WHERE user_id = :user_id
    ");
    return $stmt->execute([':user_id' => $userId]);
}
public function getValidRefreshToken()
{
    $stmt = $this->db->prepare("
        SELECT rt.user_id, rt.expires_at, u.role
        FROM refresh_tokens rt
        JOIN users u ON u.id = rt.user_id
        WHERE rt.expires_at > NOW()
        ORDER BY rt.id DESC
        LIMIT 1
    ");

    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
