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
            if (empty($availability['start_time']) || empty($availability['end_time'])) {
                error(400, "Start and end time required for showing availability");
            }

            if ($availability['start_time'] >= $availability['end_time']) {
                error(400, "End time must be after start time");
            }

            $timezone = $this->avail->getTimezone($provider_id);
            
            // Store original time values for booking comparison (before UTC conversion)
            $originalStartTime = $availability['start_time']; // e.g., "09:00"
            $originalEndTime = $availability['end_time'];     // e.g., "17:00"
            
            $dummyDate = '1970-01-01';
        $startUTC = convertToUTC($dummyDate, $availability['start_time'], $timezone);
        $endUTC   = convertToUTC($dummyDate, $availability['end_time'], $timezone);
            error_log($startUTC);
        $availability['start_time'] = $startUTC;
        $availability['end_time']   = $endUTC;
        
        // Check for future confirmed bookings that would be affected
        // Weekly availability applies to all future dates, so we need to check from tomorrow onwards
        $tz = new \DateTimeZone($timezone ?: 'UTC');
        $tomorrow = new \DateTime('tomorrow', $tz);
        $tomorrowStr = $tomorrow->format('Y-m-d');
        
        // Check next 90 days for potential conflicts
        $futureDate = clone $tomorrow;
        $futureDate->modify('+90 days');
        $futureDateStr = $futureDate->format('Y-m-d');
        
        $bookingModel = new \App\Models\Booking();
        
        // We need to check each day of the week for bookings
        $tomorrowStartUTC = convertToUTC($tomorrowStr, '00:00', $timezone);
        $futureEndUTC = convertToUTC($futureDateStr, '23:59', $timezone);
        
        $allFutureBookings = $bookingModel->hasConfirmedBookingsInTimeRange(
            $provider_id,
            $tomorrowStartUTC,
            $futureEndUTC
        );
        
        if (!empty($allFutureBookings)) {
            // Check if any bookings fall outside the new weekly availability window
            $conflictingBookings = [];
            
            // Use the original time values (before UTC conversion) for comparison
            $newStartTime = substr($originalStartTime, 0, 5); // "HH:MM" format
            $newEndTime = substr($originalEndTime, 0, 5);     // "HH:MM" format
            
            foreach ($allFutureBookings as $booking) {
                // Convert booking times to provider timezone
                $bookingStartLocal = convertFromUTC($booking['start_time'], $timezone);
                $bookingEndLocal = convertFromUTC($booking['end_time'], $timezone);
                
                // Extract just the time portion (HH:MM format)
                $bookingStartTime = substr($bookingStartLocal, 11, 5); // "HH:MM"
                $bookingEndTime = substr($bookingEndLocal, 11, 5);     // "HH:MM"
                
                // Compare times (string comparison works for HH:MM format)
                if ($bookingStartTime < $newStartTime || $bookingEndTime > $newEndTime) {
                    $conflictingBookings[] = [
                        'date' => substr($bookingStartLocal, 0, 10),
                        'customer' => $booking['customer_name'],
                        'service' => $booking['service_name'],
                        'time' => $bookingStartTime . ' - ' . $bookingEndTime
                    ];
                }
            }
            
            if (!empty($conflictingBookings)) {
                error(409, "Cannot change weekly availability. You have confirmed bookings that fall outside the new time range. Please cancel these bookings first ");
            }
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
        $date = $input['date'];
        $provider_id = $_REQUEST['auth_user']['id'];
        if($dayOfWeek <0 || $dayOfWeek >6){
            error(400,"Invalid day of week");
        }
        
        // Past date validation
        $timezone = $this->avail->getTimezone($provider_id);
        $tz = new \DateTimeZone($timezone ?: 'UTC');
        $today = new \DateTime('now', $tz);
        $selectedDate = new \DateTime($date, $tz);
        
        $todayDateOnly = clone $today;
        $todayDateOnly->setTime(0,0,0);
        $selectedDate->setTime(0,0,0);

        if ($selectedDate < $todayDateOnly) {
            error(400, "Cannot set availability for past dates");
        }
        
        // If updating for today, check against current time
        if ($selectedDate == $todayDateOnly) {
            $currentTime = $today->format('H:i');
            if ($startTime < $currentTime) {
                error(400, "Start time cannot be in the past for today");
            }
        }

        if(empty($startTime) || empty($endTime)){
            error(400,"Start time and end time are required");
        }
        
        if ($startTime >= $endTime) {
            error(400, "End time must be after start time");
        }
        
        // Check for confirmed bookings that would conflict
        $startUTC = convertToUTC($date, $startTime, $timezone);
        $endUTC   = convertToUTC($date, $endTime, $timezone);
        
        // Check for bookings on this entire day
        $dayStartUTC = convertToUTC($date, '00:00', $timezone);
        $dayEndUTC = convertToUTC($date, '23:59', $timezone);
        
        $bookingModel = new \App\Models\Booking();
        $conflictingBookings = $bookingModel->hasConfirmedBookingsInTimeRange(
            $provider_id,
            $dayStartUTC,
            $dayEndUTC
        );
        
        if (!empty($conflictingBookings)) {
            // Check if any bookings fall outside the new availability window
            foreach ($conflictingBookings as $booking) {
                $bookingStart = $booking['start_time'];
                $bookingEnd = $booking['end_time'];
                
                // If booking is outside the new time range, it's a conflict
                if ($bookingStart < $startUTC || $bookingEnd > $endUTC) {
                    $bookingDetails = [];
                    foreach ($conflictingBookings as $b) {
                        $bookingDetails[] = [
                            'customer' => $b['customer_name'],
                            'service' => $b['service_name'],
                            'time' => convertFromUTC($b['start_time'], $timezone) . ' - ' . convertFromUTC($b['end_time'], $timezone)
                        ];
                    }
                    error(409, "Cannot change availability. You have confirmed bookings on this day. Please cancel these bookings first" );
                }
            }
        }
        
        $result = $this->avail->setSingleDayAvailability($provider_id, $startUTC, $endUTC, $date);
        if(!$result){
            error(500,"An error occurred while saving single day availability");   
        }
        success(200,"Single day availability saved successfully");

    } catch(\Exception $e){
            error_log("An error occurred: " . $e->getMessage());
            error(500, "Internal Server Error: ");
        }
    }
    public function getAvailability(){
        try{
            $provider_id = $_REQUEST['auth_user']['id'];
             $timezone = $this->avail->getTimezone($provider_id);
            $tz = new \DateTimeZone($timezone); 

            $startParam = $_GET['start'] ?? null;
            $endParam = $_GET['end'] ?? null;

            if ($startParam && $endParam) {
                $startDate = substr($startParam, 0, 10);
                $endDate = substr($endParam, 0, 10);
            } else {
                $today = new \DateTime('now', $tz);
                $monday = clone $today;
                $monday->modify('monday this week');
                $startDate = $monday->format('Y-m-d');
                
                $sunday = clone $monday;
                $sunday->modify('+6 days'); 
                $endDate = $sunday->format('Y-m-d');
            }

            $availability = $this->avail->getProviderAvailability($provider_id, $startDate, $endDate);

            if(!$availability){
               
                 if($availability === false){
                     error(500,"An error occurred while fetching availability");
                 }
            }            
            foreach ($availability as &$slot) {
             if (!empty($slot['start_time'])) {
                $slot['start_time'] = convertFromUTC($slot['start_time'], $timezone);
                }

            if (!empty($slot['end_time'])) {
                  $slot['end_time'] = convertFromUTC($slot['end_time'], $timezone);
            }
        }
            success(200, "Availability fetched successfully", $availability);
        } catch(\Exception $e){
            error_log("An error occurred: " . $e->getMessage());
            error(500, "Internal Server Error in fetching availability ");
        }
    }
    public function setDayOff(){
        try{
        $input = json_decode(file_get_contents("php://input"), true);
        $dayOfWeek = $input['day_of_week'];
        $date = $input['change_date'];
        $provider_id = $_REQUEST['auth_user']['id'];
        if($dayOfWeek <0 || $dayOfWeek >6){
            error(400,"Invalid day of week");
        }
        
        // Check for confirmed bookings on this date
        $timezone = $this->avail->getTimezone($provider_id);
        $dayStartUTC = convertToUTC($date, '00:00', $timezone);
        $dayEndUTC = convertToUTC($date, '23:59', $timezone);
        
        $bookingModel = new \App\Models\Booking();
        $conflictingBookings = $bookingModel->hasConfirmedBookingsInTimeRange(
            $provider_id,
            $dayStartUTC,
            $dayEndUTC
        );
        
        if (!empty($conflictingBookings)) {
            $bookingDetails = [];
            foreach ($conflictingBookings as $b) {
                $bookingDetails[] = [
                    'customer' => $b['customer_name'],
                    'service' => $b['service_name'],
                    'time' => convertFromUTC($b['start_time'], $timezone) . ' - ' . convertFromUTC($b['end_time'], $timezone)
                ];
            }
            error(409, "Cannot set day off. You have confirmed bookings on this day. Please cancel these bookings first " );
        }
        
        $result = $this->avail->setDayOff($provider_id, $date);
        if(!$result){
            error(500,"An error occurred while marking day off");   
        }
        success(200,"Day marked off successfully");

    } catch(\Exception $e){
            error_log("An error occurred: " . $e->getMessage());
            error(500, "Internal Server Error: ");
                }
    }
    public function fetchWeeklyAvailability(){
        try{
             if (empty($_GET['provider_id'])) {
                error(400,"provider_id is required");
             }

    $providerId = (int) $_GET['provider_id'];


  $data = $this->avail->getRecurringAvailability($providerId);

   success(200,"weekly data fetched successfully",$data);

        }catch(\Exception $e){
            error_log("An error occurred while fetching weekly availability: " . $e->getMessage());
            error(500, "Internal Server Error while fetching availablity weekly ");
            }
    }
public function fetchSlots(){
    try{
        if (
        empty($_GET['provider_id']) ||
        empty($_GET['service_id']) ||
        empty($_GET['date'])
    ) {
       error(400,"service-id,provider-id and date are required");
    }
       $providerId = (int) $_GET['provider_id'];
    $serviceId  = (int) $_GET['service_id'];
    $date       = $_GET['date'];

    $selectedDate = new \DateTime($date);
    $today = new \DateTime('today');

    if ($selectedDate < $today) {
       error(400,"cannot book for past date");
    }
     $service  = $this->avail->getServiceById($serviceId,$providerId);
         error_log("services get");

     if(!$service){
        error(404,"service not found");
     }
         $duration = (int) $service['duration'];

    //  AVAILABILITY CHECK 
    $availability = null;
    $availability = $this->avail->getDateOverride($providerId,$date);
      //  Weekly availability (if no override)
    if (!$availability) {
        $dayOfWeek = (int) (new \DateTime($date))->format('w');
        $availability = $this->avail->getDateWeekly($providerId,$dayOfWeek);

    }
     if (!$availability  || (int)$availability['status'] === 0) {
       error(200,"provider is not available on this day");
    }
    $timezone = $this->avail->getTimezone($providerId);

    $startLocal = convertFromUTC($availability['start_time'], $timezone);
    $endLocal   = convertFromUTC($availability['end_time'], $timezone);
    
    $startTimeStr = substr($startLocal, 11, 5); 
    $endTimeStr   = substr($endLocal, 11, 5);

    $startMinutes = toMinutes($startTimeStr);
    $endMinutes   = toMinutes($endTimeStr);
    
    
    $workingDayStartUtc = convertToUTC($date,$startTimeStr,$timezone);
      $workingDayEndUtc = convertToUTC($date,$endTimeStr,$timezone);
    
    $bookings = $this->avail->getBookingsBetween(
        $providerId, 
        $workingDayStartUtc,
        $workingDayEndUtc
    );

    $slots = [];
    $current = $startMinutes; 
    // current time relative to provider:
    $nowLocal = new \DateTime('now', new \DateTimeZone($timezone));
    $isToday = $selectedDate->format('Y-m-d') === $nowLocal->format('Y-m-d');
    $nowMinutes = toMinutes($nowLocal->format('H:i'));

    // Convert bookings to local minutes 
    $bookedIntervals = [];
    foreach($bookings as $booking){

        
        $bStartLocal = convertFromUTC($booking['start_time'], $timezone);
        $bEndLocal = convertFromUTC($booking['end_time'], $timezone);
        
     $bStartM = toMinutes(substr($bStartLocal, 11, 5));
        $bEndM   = toMinutes(substr($bEndLocal, 11, 5));
        
        $bookedIntervals[] = ['start' => $bStartM, 'end' => $bEndM];
    }
//slot calculation
    while (($current + $duration) <= $endMinutes) {
        $slotStart = $current;
        $slotEnd   = $current + $duration;
        
        //  Past check
        if ($isToday && $slotStart < $nowMinutes) {
            $current += $duration; 
            continue; 
        }

        //  Booking check
        $status = 'available';
        foreach ($bookedIntervals as $interval) {
             // Overlap: (SlotStart < IntervalEnd) AND (SlotEnd > IntervalStart)
            if ($slotStart < $interval['end'] && $slotEnd > $interval['start']) {
                $status = 'booked';
                break;
            }
        }
        
        $slots[] = [
            'start_time' => toTime($slotStart),
            'end_time'   => toTime($slotEnd),
            'status'     => $status
        ];
        
        $current += $duration;
    }
    
    success(200, "Slots fetched successfully", $slots);

    }catch(\Exception $e){
            error_log("An error occurred while fetching slots: " . $e->getMessage());
            error(500, "Internal Server Error while fetching slots ");
            }
}

}
     