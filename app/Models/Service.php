<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class Service extends Model{


    public function getCategories()
    {
        $stmt = $this->db->query(
            "SELECT id, name FROM categories ORDER BY name ASC"
        );

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
      public function getSubcategories($id)
    {
        $stmt = $this->db->prepare(
            "SELECT  id,name FROM subcategories WHERE category_id = :id ORDER BY name ASC"
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getProviderServices($provider_id,$name)
    {
        $stmt = $this->db->prepare(
            "SELECT id FROM services WHERE provider_id = :provider_id AND name = :name LIMIT 1"
        );
        $stmt->execute(['provider_id' => $provider_id,'name'=>$name]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addService($provider_id, $name, $category_id, $subcategory_id, $price, $duration, $description){
        $sql = "
    INSERT INTO services
    (provider_id, name, category_id, subcategory_id, price, duration, description)
    VALUES
    (:provider_id, :name, :category_id, :subcategory_id, :price, :duration, :description)
    ";

    $stmt = $this->db->prepare($sql);

    $stmt->execute([
    ':provider_id'    => $provider_id,
    ':name'           => $name,
    ':category_id'    => $category_id,
    ':subcategory_id' => $subcategory_id,
    ':price'          => $price,
    ':duration'       => $duration,
    ':description'    => $description
    ]);

    return $this->db->lastInsertId();
    }
    public function countServices($provider_id,$search=""){
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM services WHERE provider_id = :provider_id AND name LIKE :search"
        );
        $stmt->execute(['provider_id' => $provider_id, 'search' => "%$search%"]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }
    public function getAllServices($provider_id, $search = "", $limit = 4, $offset = 0, $sort = 'name', $order = 'ASC'){
        $allowedSortColumns = ['name', 'price', 'duration'];
        if (!in_array($sort, $allowedSortColumns)) {
            $sort = 'name';
        }

        $sql = "
        SELECT id, name, price, duration, description, service_status
        FROM services
        WHERE provider_id = :provider_id AND name LIKE :search AND service_status != 2
        ORDER BY $sort $order
        LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':provider_id', $provider_id, PDO::PARAM_INT);
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

    $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function countTotalServices($provider_id){
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM services WHERE provider_id = :provider_id"
        );
        $stmt->execute(['provider_id' => $provider_id]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0;
    }
    public function countActiveServices($provider_id){
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as total FROM services WHERE provider_id = :provider_id AND service_status = 1"
        );
        $stmt->execute(['provider_id' => $provider_id]);    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['total'] : 0; 
    }
}
