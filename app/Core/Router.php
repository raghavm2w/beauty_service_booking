<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, callable|array $handler): void
    {
$this->routes['GET'][$this->normalizePath($path)] = $handler;
   }

    public function post(string $path, callable|array $handler): void
    {
$this->routes['POST'][$this->normalizePath($path)] = $handler;
 }

    public function dispatch(): void
    {
         $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = strtok($_SERVER['REQUEST_URI'], '?');
    $uri = $this->normalizePath($uri);

    if (!isset($this->routes[$httpMethod][$uri])) {
        http_response_code(404);
        require_once __DIR__ . "/../Views/404.php";
        return;
    }

    $handlers = $this->routes[$httpMethod][$uri];

    if (!is_array($handlers) || isset($handlers[0]) && !is_array($handlers[0])) {
        $handlers = [$handlers];
    }

    foreach ($handlers as $handler) {

        if (is_array($handler)) {
            [$class, $method] = $handler;

            if (!class_exists($class)) {
                throw new \Exception("Class {$class} not found");
            }

            $instance = new $class;

            if (!method_exists($instance, $method)) {
                throw new \Exception("Method {$method} not found in {$class}");
            }

            $instance->$method();
            continue;
        }

        // Closure
        if (is_callable($handler)) {
            call_user_func($handler);
            continue;
        }

        throw new \Exception("Invalid route handler");
    }
}
private function normalizePath(string $path)
{
     $path = '/' . trim($path, '/'); 
     return $path === '/' ? '/' : rtrim($path, '/'); 
}
}
