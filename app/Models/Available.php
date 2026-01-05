<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class Available extends Model{
    public function setWeeklyAvailability($provider_id, $avail){
        try{
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
        }catch(\Exception $e){
        throw $e;
    }
    }
    public function setSingleDayAvailability($provider_id, $startTime, $endTime, $date){
        try{
            $stmt = $this->db->prepare("
                    INSERT INTO provider_availability (
                        provider_id,
                        change_date,
                        start_time,
                        end_time,
                        is_recurring,
                        status
                    ) VALUES (
                        ?, ?, ?, ?, 0, 1
                    )
                    ON DUPLICATE KEY UPDATE
                        start_time = VALUES(start_time),
                        end_time   = VALUES(end_time),
                        status     = 1
                ");

                return $stmt->execute([
                    $provider_id,
                    $date,
                    $startTime,
                    $endTime
                ]);
         }catch(\Exception $e){
        throw $e;
        }
    }
    public function getProviderAvailability($provider_id, $monday, $sunday){
        try{
        $stmt = $this->db->prepare("
        SELECT day_of_week, start_time, end_time, status, is_recurring, change_date
         FROM provider_availability
         WHERE provider_id = ?
          AND (
             is_recurring = 1
             OR (
             is_recurring = 0
                AND change_date BETWEEN ? AND ?
            )
             )
        ");

    $stmt->execute([
            $provider_id,
            $monday,
            $sunday
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
     }catch(\Exception $e){
        throw $e;
    }
    }
    public function setDayOff($provider_id, $date){
        try{
        $stmt = $this->db->prepare(" INSERT INTO provider_availability (
                provider_id,
                change_date,
                is_recurring,
                status
            ) VALUES (
                ?, ?, 0, 0
            )
            ON DUPLICATE KEY UPDATE
                status = 0
        ");
        return $stmt->execute([
            $provider_id,
            $date
        ]);
         }catch(\Exception $e){
        throw $e;
    }
    }
    public function getTimezone($id){
    try{
        $stmt = $this->db->prepare("
        SELECT timezone from users  WHERE id = ?
    ");

    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC)['timezone'];

    } catch(\Exception $e){
        throw $e;
    }
}
public function getRecurringAvailability($id){
    try{
          $stmt = $this->db->prepare("
        SELECT day_of_week, status
        FROM provider_availability
        WHERE provider_id = :provider_id
        AND is_recurring = 1
    ");

    $stmt->execute([
        ':provider_id' => $id
    ]);

    $workingDays = [];
    $offDays = [];

    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $day = (int) $row['day_of_week'];

        if ((int)$row['status'] === 1) {
            $workingDays[] = $day;
        } else {
            $offDays[] = $day;
        }
    }
    return ["workingDays"=>$workingDays,
            "offDays"=>$offDays
    ];

    }catch(\Exception $e){
        throw $e;
    }
}
public function getServiceById($service_id,$provider_id){
    try{
         $stmt = $this->db->prepare("
        SELECT duration,price
        FROM services
        WHERE id = :service_id
        AND provider_id = :provider_id
        AND service_status = 1
    ");
    $stmt->execute([
        ':service_id' => $service_id,
        ':provider_id' => $provider_id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);


    }catch(\Exception $e){
        throw $e;
    }
}
public function getDateOverride($providerId,$date){
    try{
         $stmt = $this->db->prepare("
        SELECT start_time, end_time, status
        FROM provider_availability
        WHERE provider_id = :provider_id
        AND is_recurring = 0
        AND change_date = :date
        LIMIT 1
    ");
    $stmt->execute([
        ':provider_id' => $providerId,
        ':date' => $date
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);

    }catch(\Exception $e){
        throw $e;
    }
}
public function getDateWeekly($providerId,$dayOfWeek){
    try{
         $stmt = $this->db->prepare("
            SELECT start_time, end_time, status
            FROM provider_availability
            WHERE provider_id = :provider_id
            AND is_recurring = 1
            AND day_of_week = :day
            LIMIT 1
        ");
        $stmt->execute([
            ':provider_id' => $providerId,
            ':day' => $dayOfWeek
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }catch(\Exception $e){
        throw $e;
    }
}

public function getBookingsBetween($providerId, $startUtc, $endUtc){
    try{
        $stmt = $this->db->prepare("
            SELECT start_time, end_time, status
            FROM bookings
            WHERE provider_id = :provider_id
            AND status != 3
            AND (
                (start_time < :end_time AND end_time > :start_time)
            )
        ");
        $stmt->execute([
            ':provider_id' => $providerId,
            ':start_time' => $startUtc,
            ':end_time' => $endUtc
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }catch(\Exception $e){
        throw $e;
    }
}

}