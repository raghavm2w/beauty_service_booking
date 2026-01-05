<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class Log extends Model
{

 public function create(
        ?int $user_id,
        string $type,
        string $action,
        ?string $message = null
    ) {
        $stmt = $this->db->prepare("
            INSERT INTO logs (user_id, type, action, message)
            VALUES (:user_id, :type, :action, :message)
        ");

        return $stmt->execute([
            ':user_id' => $user_id,
            ':type'    => $type,
            ':action'  => $action,
            ':message' => $message
        ]);
    }


}