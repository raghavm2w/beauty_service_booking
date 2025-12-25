<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class Available extends Model{
    public function setWeeklyAvailability($provider_id, $avail){
     $stmt = $this->db->prepare("   INSERT INTO provider_availability (
            provider_id,
            day_of_week,
             start_time,
            end_time,
             is_recurring,
             change_date,
            status
            )
            VALUES
             (?, 0, ?, ?, 1, NULL, 1), -- Sunday
             (?, 1, ?, ?, 1, NULL, 1), -- Monday
             (?, 2, ?, ?, 1, NULL, 1), -- Tuesday
            (?, 3, ?, ?, 1, NULL, 1), -- Wednesday
            (?, 4, ?, ?, 1, NULL, 1), -- Thursday
             (?, 5, ?, ?, 1, NULL, 1), -- Friday
             (?, 6, ?, ?, 1, NULL, 1)  -- Saturday
ON DUPLICATE KEY UPDATE
  start_time = VALUES(start_time),
  end_time   = VALUES(end_time),
  status     = 1,
  is_recurring = 1,
  change_date  = NULL;");
 return $stmt->execute([
  $provider_id,$avail['start_time'],$avail['end_time'],
  $provider_id,$avail['start_time'],$avail['end_time'],
  $provider_id,$avail['start_time'],$avail['end_time'],
  $provider_id,$avail['start_time'],$avail['end_time'],
  $provider_id,$avail['start_time'],$avail['end_time'],
  $provider_id,$avail['start_time'],$avail['end_time'],
  $provider_id,$avail['start_time'],$avail['end_time']
]);
    }
    public function setSingleDayAvailability($provider_id, $dayOfWeek, $startTime, $endTime, $status){
        $stmt = $this->db->prepare("INSERT INTO provider_availability (
            provider_id,
            day_of_week,
            start_time,
            end_time,
            is_recurring,
            change_date,
            status
        ) VALUES (
            ?, ?, ?, ?, 0, NOW(), ?
        )
        ON DUPLICATE KEY UPDATE
            start_time = VALUES(start_time),
            end_time   = VALUES(end_time),
            status     = VALUES(status),
            is_recurring = 0,
            change_date  = NOW();");
        return $stmt->execute([
            $provider_id,
            $dayOfWeek,
            $startTime,
            $endTime,
            $status
        ]);
    }
}