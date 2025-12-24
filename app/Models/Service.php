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

        $sql = "SELECT
            s.id,
            s.provider_id,
            s.category_id,
            s.subcategory_id,
            s.name,
            s.duration,
            s.price,
            s.description,
            s.service_status,
            s.created_at,
            s.updated_at,
            c.name  AS category_name,
            sc.name AS subcategory_name
        FROM services s
        LEFT JOIN categories c 
            ON s.category_id = c.id
        LEFT JOIN subcategories sc 
            ON s.subcategory_id = sc.id

        WHERE s.provider_id = :provider_id
          AND s.name LIKE :search
          AND s.service_status != 2
        ORDER BY s.$sort $order
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
            "SELECT COUNT(*) as total FROM services WHERE provider_id = :provider_id AND service_status != 2"
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
    public function editService($serviceId, $provider_id, $name, $category_id, $subcategory_id, $price, $duration, $description,$service_status){
        $sql = "
    UPDATE services
    SET name = :name,
        category_id = :category_id,
        subcategory_id = :subcategory_id,
        price = :price,
        duration = :duration,
        description = :description,
        service_status = :service_status
    WHERE id = :service_id AND provider_id = :provider_id
    ";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':name'           => $name,
        ':category_id'    => $category_id,
        ':subcategory_id' => $subcategory_id,
        ':price'          => $price,
        ':duration'       => $duration,
        ':description'    => $description,
        ':service_id'     => $serviceId,
        ':provider_id'    => $provider_id,
        ':service_status' => $service_status
    ]);
}
function deleteService($serviceId, $provider_id){
    $sql = "
    UPDATE services
    SET service_status = 2
    WHERE id = :service_id AND provider_id = :provider_id
    ";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':service_id'  => $serviceId,
        ':provider_id' => $provider_id
    ]);
} 
}
