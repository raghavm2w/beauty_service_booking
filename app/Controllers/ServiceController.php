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
    try{
    $input = json_decode(file_get_contents("php://input"), true);
      $required = ['name', 'category_id', 'subcategory_id', 'price', 'duration'];
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                error(400, "$field is required");
            }
        }

         $provider_id = $_REQUEST['auth_user']['id'];
          $name = trim($input['name']);
        $description = trim($input['description'] ?? '');

        $category_id = filter_var($input['category_id'], FILTER_VALIDATE_INT);
        $subcategory_id = filter_var($input['subcategory_id'], FILTER_VALIDATE_INT);
        $price = filter_var($input['price'], FILTER_VALIDATE_FLOAT);
        $duration = filter_var($input['duration'], FILTER_VALIDATE_INT);

        if (strlen($name) < 3 || strlen($name) > 50) {
            error_log($name);
            error(400, "Service name must be between 3 and 50 characters");
        }
        if (preg_match('/<script\b/i', $name)) {
            error(400, "Invalid service name");
        }
        if (preg_match('/<script\b/i', $description)) {
            error(400, "Invalid description");
        }
        if ($price === false || $price <= 0 || $price > 1000000) {
            error(400, "Invalid price value");
        }

        if ($duration === false || $duration < 5 || $duration > 1440) {
            error(400, "Duration must be valid number between 5 and 1440 minutes");
        }
        if (strlen($description) > 1500) {
            error(400, "Description must not exceed 1500 characters");
        }
        // Category integrity check
        if (!$this->service->isValidCategoryPair($category_id, $subcategory_id)) {
            error(400, "Invalid category or subcategory");
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

 }catch(\Exception $e){
        error_log("service add error".$e->getMessage());
        error(500,"An error occurred while adding services");
}
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
public function fetchServices(){
    try{
        $search = trim($_GET['search'] ?? '');
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = max(1, (int)($_GET['limit'] ?? 4));
        $offset = ($page - 1) * $limit;
        $sort = $_GET['sort'] ?? 'name';
         $order = strtolower($_GET['order'] ?? 'asc');
         $order = $order === 'desc' ? 'DESC' : 'ASC';
        $provider_id = $_REQUEST['auth_user']['id'];
        $overallTotal = $this->service->countTotalServices($provider_id);
        $activeTotal = $this->service->countActiveServices($provider_id);
        $totalServices = $this->service->countServices($provider_id, $search);
        $services = $this->service->getAllServices($provider_id, $search, $limit, $offset, $sort, $order);
        success(200,"services fetched successfully",[
            'totalRows'=>$totalServices,
            'services'=>$services,
            'overallTotal'=>$overallTotal,
            'activeTotal'=>$activeTotal
        ]);
    }
    catch(\Exception $e){
    error_log("Fetch services error: ".$e->getMessage());
        error(500,"An error occurred while fetching services");
    }
}
public function editService(){
    try{
    $input = json_decode(file_get_contents("php://input"), true);
     $required = ['id','name', 'category_id', 'subcategory_id', 'price', 'duration','service_status'];
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                error(400, "$field is required");
            }
        }
    $provider_id = $_REQUEST['auth_user']['id'];
    $name = trim($input['name']);
    $description = trim($input['description'] ?? null);
   
    $service_status = (int) $input['service_status'];
    $service_id = filter_var($input['id'], FILTER_VALIDATE_INT);
     $category_id = filter_var($input['category_id'], FILTER_VALIDATE_INT);
        $subcategory_id = filter_var($input['subcategory_id'], FILTER_VALIDATE_INT);
        $price = filter_var($input['price'], FILTER_VALIDATE_FLOAT);
        $duration = filter_var($input['duration'], FILTER_VALIDATE_INT);

        
        if (!in_array($service_status, [0, 1])) {
            error(400, "Invalid service status.");
        }
        if (strlen($name) < 3 || strlen($name) > 50) {
            error_log($name);
            error(400, "Service name must be between 3 and 50 characters");
        }
        if (preg_match('/<script\b/i', $name)) {
            error(400, "Invalid service name");
        }
        if (preg_match('/<script\b/i', $description)) {
            error(400, "Invalid description");
        }
        if ($price === false || $price <= 0 || $price > 1000000) {
            error(400, "Invalid price value");
        }

        if ($duration === false || $duration < 5 || $duration > 1440) {
            error(400, "Duration must be valid number between 5 and 1440 minutes");
        }
        if (strlen($description) > 1500) {
            error(400, "Description must not exceed 1500 characters");
        }
        // Category integrity check
        if (!$this->service->isValidCategoryPair($category_id, $subcategory_id)) {
            error(400, "Invalid category or subcategory");
        }
        $result = $this->service->editService($service_id,$provider_id, $name, $category_id, $subcategory_id, $price, $duration, $description, $service_status);
        if(!$result){
            error(500,"An error occurred while updating the service");
        }
        success(200,"Service updated successfully");
    }catch(\Exception $e){
        error_log("Edit service error: ".$e->getMessage());
        error(500,"An error occurred while updating the service");
    }
}
function deleteService(){
    try{
    $input = json_decode(file_get_contents("php://input"), true);
  if (empty($input['id']) )
        {
            error(400,"Service ID is required");
        }
    $service_id = (int) $input['id'];
    $provider_id = $_REQUEST['auth_user']['id'];
        $result = $this->service->deleteService($service_id,$provider_id);
        if(!$result){
            error(500,"An error occurred while deleting the service");
        }
        success(200,"Service deleted successfully");
    }catch(\Exception $e){
        error_log("Delete service error: ".$e->getMessage());
        error(500,"An error occurred while deleting the service");
    }
}
}