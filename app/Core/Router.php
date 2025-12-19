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
        $method = $_SERVER['REQUEST_METHOD'];

        // Remove query string
        $uri = strtok($_SERVER['REQUEST_URI'], '?');

        // Normalize URI
        $uri = $this->normalizePath($uri);

        if (!isset($this->routes[$method][$uri])) {
            http_response_code(404);
            require_once __DIR__."/../Views/404.php";
            return;
        }

        $handler = $this->routes[$method][$uri];

        // Controller + method
        if (is_array($handler)) {
            [$class, $method] = $handler;

            if (!class_exists($class)) {
                throw new \Exception("Controller {$class} not found");
            }

            $controller = new $class;

            if (!method_exists($controller, $method)) {
                throw new \Exception("Method {$method} not found in {$class}");
            }

            $controller->$method();
            return;
        }

        // Closure / callable
        call_user_func($handler);
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
