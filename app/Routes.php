<?php

use App\Core\Router;
use App\Controllers\AuthController;

$router = new Router();

$router->get('/register', [AuthController::class, 'showRegister']);
$router->get('/login', [AuthController::class, 'showLogin']);
// $router->post('/login', [AuthController::class, 'login']);

$router->dispatch();
