<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';
foreach (glob(__DIR__ . '/Helpers/*.php') as $file) {
    require_once $file;
}
// Load env
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

if ($_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

session_start();
