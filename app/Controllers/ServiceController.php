<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Service;


class ServiceController extends Controller {
     private Service $service;

    public function __construct()
    {
        $this->service = new Service();
    }
     
public function addService(){
    $input = json_decode(file_get_contents("php://input"), true);
  if (empty($input['name'])  || empty($input['price']) || empty($input['duration']) 
    ||empty($input['category_id']) || empty($input['subcategory_id']) )
        {
            error(400,"All fields are required");
        }
    $provider_id = $_REQUEST['auth_user']['id'];
    $name = trim($input['name']);
    $price = trim($input['price']);
    $duration = (int) $input['duration'];
    $description = trim($input['description'] ?? null);
    $category_id = (int) $input['category_id'];
    $subcategory_id = (int) $input['subcategory_id'];
    

        if (strlen($name) < 3) {
            error(400,"Service name must be at least 3 characters.");
        }
         if (!is_numeric($price) || $price <= 0) {
            error(400,"Enter a valid price value");
        }
        if (!is_numeric($duration) || $duration <= 0) {
             error(400,"Enter a valid time duation minutes");

        }
        $existingService = $this->service->getProviderServices($provider_id,$name);
        if ($existingService) {
            error(400,"Service with this name already exists.");
        }
        $result = $this->service->addService($provider_id, $name, $category_id, $subcategory_id, $price, $duration, $description);
        if(!$result){
            error(500,"An error occurred while adding the service");
        }
        success(201,"Service added successfully");


}

public function fetchCategories(){
    try{
    $categories = $this->service->getCategories();
    success(200,"Categories fetched successfully", $categories);
    }catch(\Exception $e){
        error(500,"An error occurred while fetching categories");
}
}
public function fetchSubcategories(){
    try{
    $id =$_GET['id'] ?? null;
    $subcategories = $this->service->getSubcategories($id);
    success(200,"sub-Categories fetched successfully", $subcategories);
    }catch(\Exception $e){
        error(500,"An error occurred while fetching subcategories");
}
}
}