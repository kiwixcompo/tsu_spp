<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middlewares = [];

    public function get(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, $handler, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // Debug output for development
        if ($_ENV['APP_DEBUG'] ?? false) {
            error_log("Router Debug - Original URI: " . $_SERVER['REQUEST_URI']);
            error_log("Router Debug - Parsed path: " . $path);
            error_log("Router Debug - Script name: " . $_SERVER['SCRIPT_NAME']);
        }
        
        // Remove project folder from path if present
        $scriptName = dirname($_SERVER['SCRIPT_NAME']);
        if ($scriptName !== '/' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
        }
        
        // Remove trailing slash except for root
        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }
        
        // Ensure path starts with /
        if (empty($path) || $path[0] !== '/') {
            $path = '/' . $path;
        }

        if ($_ENV['APP_DEBUG'] ?? false) {
            error_log("Router Debug - Final path: " . $path);
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertToRegex($route['path']);
            if (preg_match($pattern, $path, $matches)) {
                // Extract parameters
                $params = array_slice($matches, 1);
                
                // Run middleware
                foreach ($route['middleware'] as $middleware) {
                    $middlewareClass = "App\\Middleware\\{$middleware}";
                    if (class_exists($middlewareClass)) {
                        $middlewareInstance = new $middlewareClass();
                        if (!$middlewareInstance->handle()) {
                            return;
                        }
                    }
                }

                // Handle the route
                $this->handleRoute($route['handler'], $params);
                return;
            }
        }

        // No route found
        http_response_code(404);
        if ($_ENV['APP_DEBUG'] ?? false) {
            echo "<h1>404 - Route Not Found</h1>";
            echo "<p>Path: <code>{$path}</code></p>";
            echo "<p>Method: <code>{$method}</code></p>";
            echo "<h3>Available Routes:</h3><ul>";
            foreach ($this->routes as $route) {
                echo "<li>{$route['method']} {$route['path']}</li>";
            }
            echo "</ul>";
        } else {
            include __DIR__ . '/../Views/errors/404.php';
        }
    }

    private function convertToRegex(string $path): string
    {
        // Convert {param} to regex capture groups
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    private function handleRoute($handler, array $params): void
    {
        if (is_string($handler)) {
            // Handle "Controller@method" format
            if (strpos($handler, '@') !== false) {
                [$controller, $method] = explode('@', $handler);
                $controllerClass = "App\\Controllers\\{$controller}";
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    if (method_exists($controllerInstance, $method)) {
                        call_user_func_array([$controllerInstance, $method], $params);
                        return;
                    }
                }
            }
            
            // Handle view file
            $viewPath = __DIR__ . "/../Views/{$handler}.php";
            if (file_exists($viewPath)) {
                include $viewPath;
                return;
            }
        }

        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        throw new \Exception("Invalid route handler");
    }
}