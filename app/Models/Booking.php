<?php
namespace App\Models;
use App\Core\Model;
use PDO;

class Booking extends Model
{

    public function createBooking(array $data)
    {
        try {
            $this->db->beginTransaction();

            // Check for overlaps (Locking logic)
            // Ignore pending bookings that are older than 5 minutes (expired)
            $stmt = $this->db->prepare("
                SELECT id FROM bookings 
                WHERE provider_id = :provider_id 
                AND status != 3 
                AND NOT (status = 0 AND created_at < (NOW() - INTERVAL 5 MINUTE))
                AND (
                    (start_time < :end_time AND end_time > :start_time)
                )
                FOR UPDATE
            ");

            $stmt->execute([
                ':provider_id' => $data['provider_id'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time']
            ]);

            if ($stmt->fetch()) {
                $this->db->rollBack();
                return ['status' => 'error', 'message' => 'Slot already booked'];
            }

            // Insert booking
            $stmt = $this->db->prepare("
                INSERT INTO bookings 
                (user_id, provider_id, service_id, start_time, end_time, status, created_at)
                VALUES 
                (:customer_id, :provider_id, :service_id, :start_time, :end_time, 0, NOW())
            ");

            $stmt->execute([
                ':customer_id' => $data['customer_id'],
                ':provider_id' => $data['provider_id'],
                ':service_id' => $data['service_id'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time']
            ]);

            $bookingId = $this->db->lastInsertId();
            $this->db->commit();

            return ['status' => 'success', 'booking_id' => $bookingId];

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }

    }

    public function getBookingDetails(int $id)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    b.id, b.start_time, b.end_time, b.created_at, b.provider_id,
                    s.name as service_name, s.price,s.duration, 
                    u.name as provider_name
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                JOIN users u ON b.provider_id = u.id
                WHERE b.id = :id
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function confirmBookingPayment(int $bookingId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT id 
                FROM bookings 
                WHERE id = :id 
                AND status = 0 
                AND created_at < (NOW() - INTERVAL 5 MINUTE)
            ");
            $stmt->execute([':id' => $bookingId]);

            if ($stmt->fetch()) {
                // Expired
                $this->db->prepare("UPDATE bookings SET status = 3 WHERE id = ?")->execute([$bookingId]);
                return false;
            }

            $stmt = $this->db->prepare("
                UPDATE bookings 
                SET status = 1 
                WHERE id = :id AND status != 3
            "); // status 1: confirmed, status 3: cancelled
            return $stmt->execute([':id' => $bookingId]);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function cancelBooking(int $bookingId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE bookings 
                SET status = 3 
                WHERE id = :id AND (status = 0 OR status = 1)
            ");
            return $stmt->execute([':id' => $bookingId]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function completeBooking(int $bookingId)
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE bookings 
                SET status = 2 
                WHERE id = :id AND (status = 1)
            "); // Ensure only confirmed bookings can be completed
            return $stmt->execute([':id' => $bookingId]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getUserBookings(int $userId, string $filter, int $limit, int $offset)
    {
        try {
            $sql = "
                SELECT 
                    b.id, b.start_time, b.end_time, b.created_at, b.status, b.provider_id,
                    s.name as service_name, s.price, s.duration,
                    u.name as provider_name
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                JOIN users u ON b.provider_id = u.id
                WHERE b.user_id = :user_id
            ";

            if ($filter === 'upcoming') {
                $sql .= " AND b.status = 1 AND b.start_time >= NOW() ORDER BY b.start_time ASC";
            } elseif ($filter === 'cancelled') {
                $sql .= " AND b.status = 3 ORDER BY b.created_at DESC";
            } elseif ($filter === 'completed') {
                $sql .= " AND (b.status = 2 OR (b.status = 1 AND b.start_time < NOW())) ORDER BY b.start_time DESC";
            } else {
                $sql .= " ORDER BY b.created_at DESC";
            }

            $sql .= " LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getProviderBookings(int $providerId, array $filters, int $page, int $limit, string $sort = 'created_at', string $order = 'DESC')
    {
        try {
            $offset = ($page - 1) * $limit;
            $conditions = ["b.provider_id = :provider_id"];
            $params = [':provider_id' => $providerId];

            if (!empty($filters['status'])) {
                if ($filters['status'] === 'pending') {
                    $conditions[] = "b.status = 0";
                } elseif ($filters['status'] === 'confirmed') {
                    $conditions[] = "b.status = 1";
                } elseif ($filters['status'] === 'cancelled') {
                    $conditions[] = "b.status = 3";
                } elseif ($filters['status'] === 'completed') {
                    $conditions[] = "b.status = 2";
                }
            }

            if (!empty($filters['search'])) {
                $conditions[] = "(u.name LIKE :search OR s.name LIKE :search)";
                $params[':search'] = "%" . $filters['search'] . "%";
            }

            $whereClause = "WHERE " . implode(" AND ", $conditions);

            // Get Total Count
            $countSql = "
                SELECT COUNT(*) as total 
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN services s ON b.service_id = s.id
                $whereClause
            ";
            $stmt = $this->db->prepare($countSql);
            $stmt->execute($params);
            $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

            // Validate sort column
            $allowedSortColumns = [
                'customer_name' => 'u.name',
                'service_name' => 's.name',
                'start_time' => 'b.start_time',
                'duration' => 's.duration',
                'price' => 's.price',
                'created_at' => 'b.created_at'
            ];
            
            $sortColumn = $allowedSortColumns[$sort] ?? 'b.created_at';
            $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';

            // Get Bookings
            $sql = "
                SELECT 
                    b.id, b.start_time, b.end_time, b.created_at, b.status,
                    u.name as customer_name, u.email as customer_email,
                    s.name as service_name, s.price, s.duration
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN services s ON b.service_id = s.id
                $whereClause
                ORDER BY $sortColumn $order
                LIMIT :limit OFFSET :offset
            ";

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['bookings' => $bookings, 'total' => $total];

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getDashboardStats(int $providerId)
    {
        try {
            $stats = [];

            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM bookings 
                WHERE provider_id = :provider_id 
                AND DATE(start_time) = CURDATE() 
                AND status != 3
            ");
            $stmt->execute([':provider_id' => $providerId]);
            $stats['today_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            // Upcoming Bookings (status = confirmed (1), start_time > NOW())
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM bookings 
                WHERE provider_id = :provider_id 
                AND status = 1 
                AND start_time > NOW()
            ");
            $stmt->execute([':provider_id' => $providerId]);
            $stats['upcoming_bookings'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

            //  Total Revenue This Month(status = 2)
            $stmt = $this->db->prepare("
                SELECT SUM(s.price) as total_revenue
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                WHERE b.provider_id = :provider_id
                AND b.status = 2
                AND MONTH(b.start_time) = MONTH(CURDATE())
                AND YEAR(b.start_time) = YEAR(CURDATE())
            ");
            $stmt->execute([':provider_id' => $providerId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['month_revenue'] = $result['total_revenue'] ? $result['total_revenue'] : 0;

            return $stats;

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getTodayBookings(int $providerId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    b.id,
                    b.start_time,
                    b.end_time,
                    b.status,
                    s.name as service_name,
                    s.price,
                    s.duration,
                    u.name as customer_name
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                JOIN users u ON b.user_id = u.id
                WHERE b.provider_id = :provider_id 
                AND DATE(b.start_time) = CURDATE() 
                AND b.status IN (1, 2)
                ORDER BY b.start_time ASC
            ");
            $stmt->execute([':provider_id' => $providerId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function hasConfirmedBookingsInTimeRange(int $providerId, string $startDateTime, string $endDateTime)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    b.id,
                    b.start_time,
                    b.end_time,
                    s.name as service_name,
                    u.name as customer_name
                FROM bookings b
                JOIN services s ON b.service_id = s.id
                JOIN users u ON b.user_id = u.id
                WHERE b.provider_id = :provider_id 
                AND b.status = 1
                AND b.start_time < :end_time
                AND b.end_time > :start_time
                ORDER BY b.start_time ASC
            ");
            $stmt->execute([
                ':provider_id' => $providerId,
                ':start_time' => $startDateTime,
                ':end_time' => $endDateTime
            ]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}