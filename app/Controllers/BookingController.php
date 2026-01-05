<?php
namespace App\Controllers;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Available;




class BookingController extends Controller
{
    private Booking $book;

    public function __construct()
    {
        $this->book = new Booking();
    }

//----------- booking slots make status 0 and check for double booking and slot locking
    public function bookSlot()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);

            if (empty($_REQUEST['auth_user']['id'])) {
                error(401, "Unauthorized");
            }

            $required = ['provider_id', 'service_id', 'date', 'start_time', 'end_time'];
            foreach ($required as $field) {
                if (empty($input[$field])) {
                    error(400, "Missing required field: $field");
                }
            }

            $availableModel = new Available();
            $providerTimezone = $availableModel->getTimezone($input['provider_id']);


            $utcStart = convertToUTC($input['date'], $input['start_time'], $providerTimezone);
            $utcEnd = convertToUTC($input['date'], $input['end_time'], $providerTimezone);

            $data = [
                'customer_id' => $_REQUEST['auth_user']['id'],
                'provider_id' => $input['provider_id'],
                'service_id' => $input['service_id'],
                'start_time' => $utcStart,
                'end_time' => $utcEnd
            ];

            $result = $this->book->createBooking($data);

            if ($result['status'] === 'error') {
                error(409, $result['message']);
            }

            success(201, "Booking created successfully", ['booking_id' => $result['booking_id']]);

        } catch (\Exception $e) {
              log_event(
            'error',
            'exception',
            'booking slots failed: ' . $e->getMessage(),
            null
        );
            error_log("Booking error: " . $e->getMessage());
            error(500, "Internal Server Error in booking slots");
        }
    }
//------------get booking details for payment
    public function getBookingDetails()
    {
        try {
            if (empty($_GET['booking_id'])) {
                error(400, "Booking ID is required");
            }
            $id = $_GET['booking_id'];
            $details = $this->book->getBookingDetails($id);

            if (!$details) {
                error(404, "Booking not found");
            }

            $availableModel = new Available();
            $providerTimezone = $availableModel->getTimezone($details['provider_id']);

            $details['start_time'] = convertFromUTC($details['start_time'], $providerTimezone);
            $details['end_time'] = convertFromUTC($details['end_time'], $providerTimezone);

            success(200, "Booking details fetched", $details);
        } catch (\Exception $e) {
            error_log("Get details error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
//-------------make the booking status 1 -confirmed
    public function confirmPayment()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input['booking_id'])) {
                error(400, "Booking ID is required");
            }

            $success = $this->book->confirmBookingPayment($input['booking_id']);

            if (!$success) {
                error(500, "Failed to confirm payment");
            }

            success(200, "Payment confirmed");
        } catch (\Exception $e) {
              log_event(
            'error',
            'exception',
            'payment failed: ' . $e->getMessage(),
            null
        );
            error_log("Payment error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
    //------cancel booking and make status to 3 -cancelled
    public function cancelBooking()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input['booking_id'])) {
                error(400, "Booking ID is required");
            }

            $success = $this->book->cancelBooking($input['booking_id']);

            if (!$success) {
                error(500, "Failed to cancel booking");
            }

            success(200, "Booking cancelled");
        } catch (\Exception $e) {
            error_log("Cancel error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
    //----used by provider to complete the booking status --2
    public function completeBooking()
    {
        try {
            $input = json_decode(file_get_contents("php://input"), true);
            if (empty($input['booking_id'])) {
                error(400, "Booking ID is required");
            }

            $success = $this->book->completeBooking($input['booking_id']);

            if (!$success) {
                error(500, "Failed to complete booking");
            }

            success(200, "Booking completed");
        } catch (\Exception $e) {
              log_event(
            'error',
            'exception',
            'booking completion failed: ' . $e->getMessage(),
            null
        );
            error_log("Complete error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
    //---fetch only customer bookings
    public function fetchUserBookings()
    {
        try {
            if (empty($_REQUEST['auth_user']['id'])) {
                error(401, "Unauthorized");
            }

            $userId = $_REQUEST['auth_user']['id'];
            $filter = $_GET['filter'] ?? 'upcoming';
            $page   = max(1, (int)($_GET['page'] ?? 1));
            $limit  = max(1, (int)($_GET['limit'] ?? 9));
            $offset = ($page - 1) * $limit;

            // Fetch limit + 1 to check if there is a next page
            $bookings = $this->book->getUserBookings($userId, $filter, $limit + 1, $offset);

            $hasMore = count($bookings) > $limit;
            if ($hasMore) {
                array_pop($bookings);
            }

            $availableModel = new Available();
            foreach ($bookings as &$booking) {
                $providerTimezone = $availableModel->getTimezone($booking['provider_id']);
                $booking['start_time'] = convertFromUTC($booking['start_time'], $providerTimezone);
                $booking['end_time'] = convertFromUTC($booking['end_time'], $providerTimezone);
            }

            success(200, "User bookings fetched", [
                'bookings' => $bookings,
                'hasMore'  => $hasMore
            ]);
        } catch (\Exception $e) {
            error_log("Fetch bookings error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
    //--fetch bookings for provider services
    public function fetchProviderBookings()
    {
        try {
            if (empty($_REQUEST['auth_user']['id'])) {
                error(401, "Unauthorized");
            }
            $providerId = $_REQUEST['auth_user']['id'];

            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
            $filters = [
                'status' => $_GET['status'] ?? '',
                'search' => $_GET['search'] ?? ''
            ];

            $result = $this->book->getProviderBookings($providerId, $filters, $page, $limit);

            $availableModel = new Available();
            require_once __DIR__ . '/../Helpers/dateHelper.php';
            $providerTimezone = $availableModel->getTimezone($providerId) ?: 'UTC';

            foreach ($result['bookings'] as &$booking) {
                $booking['start_time'] = convertFromUTC($booking['start_time'], $providerTimezone);
                $booking['end_time'] = convertFromUTC($booking['end_time'], $providerTimezone);
            }

            success(200, "Provider bookings fetched", $result);

        } catch (\Exception $e) {
            error_log("Fetch provider bookings error: " . $e->getMessage());
            error(500, "Internal Server Error");
        }
    }
    //----fetch only current date bookings for dashboard
    public function getTodayBookings()
    {
        try {
            if (empty($_REQUEST['auth_user']['id'])) {
                error(401, "Unauthorized");
            }
            $providerId = $_REQUEST['auth_user']['id'];
            $bookings = $this->book->getTodayBookings($providerId);
            
         
             $availableModel = new Available();
             $providerTimezone = $availableModel->getTimezone($providerId) ?: 'UTC';

            foreach ($bookings as &$booking) {
                $booking['start_time'] = convertFromUTC($booking['start_time'], $providerTimezone);
                $booking['end_time'] = convertFromUTC($booking['end_time'], $providerTimezone);
            }

            success(200, "Bookings fetched", $bookings);
        } catch (\Exception $e) {
            error(500, $e->getMessage());
        }
    }
}
