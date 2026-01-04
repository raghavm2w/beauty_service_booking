<?php
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ViewController;
use App\Middlewares\AuthMiddleware;
use App\Controllers\ServiceController;
use App\Controllers\AvailableController;
use App\Controllers\BookingController;
use App\Controllers\ContactController;

$router = new Router();

$router->get('/', [ViewController::class, 'home']);
$router->get('/register', [ViewController::class, 'showRegister']);
$router->get('/login', [ViewController::class, 'showLogin']);
$router->get('/services', [ViewController::class, 'userServices']);
$router->get('/bookings', [ViewController::class, 'userBookings']);

$router->get('/payments', [[AuthMiddleware::class, 'verify'], [ViewController::class, 'payments']]);

$router->get('/about', [ViewController::class, 'about']);
$router->get('/contact', [ViewController::class, 'contact']);
$router->post('/contact/submit', [ContactController::class, 'submit']);

$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/user/profile', [[AuthMiddleware::class, 'verify'], [AuthController::class, 'getUser']]);
$router->get('/user/profile/edit', [[AuthMiddleware::class, 'verify'], [ViewController::class, 'editProfile']]);
$router->post('/user/profile/update', [[AuthMiddleware::class, 'verify'], [AuthController::class, 'updateProfile']]);

$router->get('user/services', [ServiceController::class, 'fetchAllServices']);
$router->get('user/service-suggestions', [ServiceController::class, 'serviceSuggestions']);
$router->get('user/weekly-availability', [AvailableController::class, 'fetchWeeklyAvailability']);
$router->get('user/service-slots', [AvailableController::class, 'fetchSlots']);
$router->post('/user/book-slot', [[AuthMiddleware::class, 'verify'], [BookingController::class, 'bookSlot']]);
$router->get('/user/booking-details', [[AuthMiddleware::class, 'verify'], [BookingController::class, 'getBookingDetails']]);
$router->post('/user/confirm-payment', [[AuthMiddleware::class, 'verify'], [BookingController::class, 'confirmPayment']]);
$router->post('/user/cancel-booking', [[AuthMiddleware::class, 'verify'], [BookingController::class, 'cancelBooking']]);
$router->get('/user/my-bookings', [[AuthMiddleware::class, 'verify'], [BookingController::class, 'fetchUserBookings']]);

$router->get('/admin/services', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ViewController::class, 'adminServices']]);
$router->get('/admin/dash', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ViewController::class, 'adminDash']]);
$router->get('/admin/avail', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ViewController::class, 'adminAvail']]);
$router->get('/admin/bookings', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ViewController::class, 'adminBookings']]);
$router->get('/admin/profile', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ViewController::class, 'adminProfile']]);
$router->get('/admin/fetch-bookings', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [BookingController::class, 'fetchProviderBookings']]);
$router->get('/admin/categories', [ServiceController::class, 'fetchCategories']);
$router->get('/admin/subcategories', [ServiceController::class, 'fetchSubcategories']);
$router->get('/admin/services-list', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ServiceController::class, 'fetchServices']]);
$router->get('/admin/getAvailability', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AvailableController::class, 'getAvailability']]);



$router->post('/admin/set-timezone', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AuthController::class, 'setTimezone']]);
$router->post('/admin/add-service', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ServiceController::class, 'addService']]);
$router->post('/admin/edit-service', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ServiceController::class, 'editService']]);
$router->post('/admin/delete-service', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [ServiceController::class, 'deleteService']]);
$router->get('/admin/get-profile', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AuthController::class, 'getUser']]);
$router->post('/admin/profile-update', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AuthController::class, 'updateProfile']]);


$router->post('/admin/add-weekAvailability', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AvailableController::class, 'addWeeklyAvailability']]);
$router->post('/admin/update-dayAvailability', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AvailableController::class, 'updateSingleDayAvailability']]);
$router->post('/admin/set-dayoff', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [AvailableController::class, 'setDayOff']]);
$router->post('/admin/complete-booking', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [BookingController::class, 'completeBooking']]);
$router->post('/admin/cancel-booking', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [BookingController::class, 'cancelBooking']]);
$router->get('/admin/get-today-bookings', [[AuthMiddleware::class, 'verify'], [AuthMiddleware::class, 'providerOnly'], [BookingController::class, 'getTodayBookings']]);

$router->dispatch();
