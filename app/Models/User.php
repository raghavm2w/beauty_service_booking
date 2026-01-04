<?php
namespace App\Models;
use App\Core\Model;
use App\Helpers\JwtHelper as JWT;
use PDO;

class User extends Model
{

    public function findByEmail(string $email)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT * FROM users WHERE email = :email LIMIT 1"
            );
            $stmt->execute(['email' => $email]);

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function register(array $data)
    {
        try {
            $this->db->beginTransaction();
            $stmt = $this->db->prepare("
            INSERT INTO users
            (name, email, phone, gender, password, role, is_verified)
            VALUES
            (:name, :email, :phone, :gender, :password, :role,0)
        ");

            $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'gender' => $data['gender'] ?? null,
                'password' => $data['password'],
                'role' => $data['role'] ?? 0,
            ]);

            $userId = $this->db->lastInsertId();
            $accessToken = JWT::generateAccessToken($userId, $data['role_label']);
            $refreshData = JWT::generateRefreshToken($userId);
            $this->setRefreshToken($refreshData);// store in db
            $this->db->commit();
            return [
                'user_id' => $userId,
                'access_token' => $accessToken,
                'refresh_token' => $refreshData['token']
            ];

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    public function setRefreshToken($data)
    {
        try {
            $refreshSql = "INSERT INTO refresh_tokens (user_id, refresh_token, expires_at)
               VALUES (:user_id, :refresh_token, :expires_at)";

            $stmt = $this->db->prepare($refreshSql);
            $stmt->execute([
                ':user_id' => $data['user_id'],
                ':refresh_token' => $data['token'],
                ':expires_at' => $data['expires_at']
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function deleteRefreshToken(int $userId)
    {
        try {
            $stmt = $this->db->prepare("
        DELETE FROM refresh_tokens WHERE user_id = :user_id
    ");
            return $stmt->execute([':user_id' => $userId]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function getValidRefreshToken($userId)
    {
        try {
            $stmt = $this->db->prepare("
        SELECT rt.user_id, rt.expires_at, u.role
        FROM refresh_tokens rt
        JOIN users u ON u.id = rt.user_id
        WHERE rt.user_id = :user_id
        AND rt.expires_at > NOW()
        ORDER BY rt.id DESC
        LIMIT 1
    ");

            $stmt->execute([':user_id' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function updateTimezone($timezone, $id)
    {
        try {
            $stmt = $this->db->prepare("
        UPDATE users set timezone = ? WHERE id = ?
    ");

            return $stmt->execute([$timezone, $id]);

        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function findById(int $id)
    {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, phone, gender, role FROM users WHERE id = :id");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateUser(int $id, array $data)
    {
        try {
            $fields = [];
            $params = [':id' => $id];

            if (isset($data['name'])) {
                $fields[] = "name = :name";
                $params[':name'] = $data['name'];
            }
            if (isset($data['phone'])) {
                $fields[] = "phone = :phone";
                $params[':phone'] = $data['phone'];
            }
            if (isset($data['gender'])) {
                $fields[] = "gender = :gender";
                $params[':gender'] = $data['gender'];
            }

            if (empty($fields)) {
                return true;
            }

            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function createContact($name, $email, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO contact_us (name, email, message) VALUES (:name, :email, :message)");
            return $stmt->execute([
                'name' => $name,
                'email' => $email,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

}
