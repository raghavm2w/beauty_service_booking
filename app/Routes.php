<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ViewController;
use App\Middlewares\AuthMiddleware;
use App\Controllers\ServiceController;
use App\Controllers\AvailableController;

$router = new Router();

$router->get('/', [ViewController::class, 'home']);
$router->get('/register', [ViewController::class, 'showRegister']);
$router->get('/login', [ViewController::class, 'showLogin']);
// $router->get('/admin', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'admin']]);
$router->get('/admin/services', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'adminServices']]);
$router->get('/admin/dash', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'adminDash']]);
$router->get('/admin/avail', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'adminAvail']]);

$router->get('/admin/categories', [ServiceController::class, 'fetchCategories']);
$router->get('/admin/subcategories', [ServiceController::class, 'fetchSubcategories']);
$router->get('/admin/services-list', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ServiceController::class, 'fetchServices']]);
$router->get('/admin/getAvailability', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[AvailableController::class, 'getAvailability']]);


$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);
$router->post('/admin/set-timezone', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[AuthController::class, 'setTimezone']]);
$router->post('/admin/add-service', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ServiceController::class, 'addService']]);
$router->post('/admin/edit-service', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ServiceController::class, 'editService']]);
$router->post('/admin/delete-service', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ServiceController::class, 'deleteService']]);

$router->post('/admin/add-weekAvailability', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[AvailableController::class, 'addWeeklyAvailability']]);
$router->post('/admin/update-dayAvailability', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[AvailableController::class, 'updateSingleDayAvailability']]);
$router->post('/admin/set-dayoff', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[AvailableController::class, 'setDayOff']]);
$router->dispatch();
