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
}
