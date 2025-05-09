<?php

namespace App\Core;

class Router
{
    private $routes = [];

    public function get($path, $callback) {
        $this->routes['GET'][$path] = $callback;
    }

    public function post($path, $callback) {
        $this->routes['POST'][$path] = $callback;
    }

    public function route() {
        return $this->dispatch();
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Debug için
        error_log("Request URI: " . $uri);
        
        // URL'den gorev_sitesi/ kısmını kaldır
        $uri = str_replace('/gorev_sitesi/', '/', $uri);
        $uri = str_replace('/gorev_sitesi', '/', $uri);
        
        // Debug için
        error_log("Processed URI: " . $uri);
        
        // Trailing slash'i kaldır
        $uri = rtrim($uri, '/');
        if (empty($uri)) {
            $uri = '/';
        }

        if (!isset($this->routes[$method])) {
            error_log("Method not found: " . $method);
            header("HTTP/1.0 405 Method Not Allowed");
            return;
        }

        foreach ($this->routes[$method] as $route => $callback) {
            error_log("Checking route: " . $route);
            
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = "@^" . $pattern . "$@D";
            
            error_log("Pattern: " . $pattern);
            error_log("URI to match: " . $uri);
            
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                
                if (is_string($callback)) {
                    list($controller, $method) = explode('@', $callback);
                    $controller = "\\App\\Controllers\\" . $controller;
                    
                    if (!class_exists($controller)) {
                        error_log("Controller not found: " . $controller);
                        throw new \Exception("Controller {$controller} not found");
                    }
                    
                    $controller = new $controller();
                    if (!method_exists($controller, $method)) {
                        error_log("Method not found: " . $method);
                        throw new \Exception("Method {$method} not found in controller {$controller}");
                    }
                    
                    return call_user_func_array([$controller, $method], $matches);
                }
            }
        }
        
        error_log("No matching route found for: " . $uri);
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}