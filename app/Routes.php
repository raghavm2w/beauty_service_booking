<?php

use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\ViewController;

$router = new Router();

$router->get('/', [ViewController::class, 'home']);
$router->get('/register', [ViewController::class, 'showRegister']);
$router->get('/login', [ViewController::class, 'showLogin']);
$router->get('/admin/dash', [ViewController::class, 'adminDashboard']);

$router->post('/register', [AuthController::class, 'register']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);


$router->dispatch();
