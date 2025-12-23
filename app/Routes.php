<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ViewController;
use App\Middlewares\AuthMiddleware;
use App\Controllers\ServiceController;

$router = new Router();

$router->get('/', [ViewController::class, 'home']);
$router->get('/register', [ViewController::class, 'showRegister']);
$router->get('/login', [ViewController::class, 'showLogin']);
$router->get('/admin', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'admin']]);
$router->get('/admin/services', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'adminServices']]);
$router->get('/admin/dash', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ViewController::class, 'adminDash']]);
$router->get('/admin/categories', [ServiceController::class, 'fetchCategories']);
$router->get('/admin/subcategories', [ServiceController::class, 'fetchSubcategories']);



$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->post('/add-service', [[AuthMiddleware::class,'verify'],[AuthMiddleware::class,'providerOnly'],[ServiceController::class, 'addService']]);


$router->dispatch();
