<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Available;


class AvailableController extends Controller {
     private Available $avail;

    public function __construct()
    {
        $this->avail = new Available();
    }
    public function addWeeklyAvailability(){
        try{
        $input = json_decode(file_get_contents("php://input"), true);
        $availability = $input['availability'];
        $provider_id = $_REQUEST['auth_user']['id'];
        if(empty($availability)){
            error(400,"Availability data is required");
        }
        error_log(print_r($availability,true));
            if (empty($availability['start_time']) || empty($availability['end_time'])) {
                error(400, "Start and end time required for availability");
            }
        $result = $this->avail->setWeeklyAvailability($provider_id, $availability);
        if(!$result){
            error(500,"An error occurred while saving availability");   
        }
        success(200,"Weekly availability saved successfully");

    } catch(\Exception $e){
            error_log("An error occurred: " . $e->getMessage());
            error(500, "Internal Server Error: ");
        }
    }
    public function updateSingleDayAvailability(){
        try{
        $input = json_decode(file_get_contents("php://input"), true);
        $dayOfWeek = $input['day_of_week'];
        $startTime = $input['start_time'];
        $endTime = $input['end_time'];
        $provider_id = $_REQUEST['auth_user']['id'];
        if($dayOfWeek <0 || $dayOfWeek >6){
            error(400,"Invalid day of week");
        }
        if(empty($startTime) || empty($endTime)){
            error(400,"Start time and end time are required");
        }
        $result = $this->avail->setSingleDayAvailability($provider_id, $dayOfWeek, $startTime, $endTime, $status);
        if(!$result){
            error(500,"An error occurred while saving single day availability");   
        }
        success(200,"Single day availability saved successfully");

    } catch(\Exception $e){
            error_log("An error occurred: " . $e->getMessage());
            error(500, "Internal Server Error: ");
        }
    
}
     