<?php

namespace App\Router;

class Router
{
    private array $routes = [];
    private array $middlewares = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * Add a GET route
     */
    public function get(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    /**
     * Add a POST route
     */
    public function post(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    /**
     * Add a PUT route
     */
    public function put(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    /**
     * Add a DELETE route
     */
    public function delete(string $path, $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    /**
     * Add a route for any HTTP method
     */
    public function any(string $path, $handler, array $middlewares = []): void
    {
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
        foreach ($methods as $method) {
            $this->addRoute($method, $path, $handler, $middlewares);
        }
    }

    /**
     * Add a route group with shared middleware
     */
    public function group(array $middlewares, callable $callback): void
    {
        $previousMiddlewares = $this->middlewares;
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        
        $callback($this);
        
        $this->middlewares = $previousMiddlewares;
    }

    /**
     * Add a route
     */
    private function addRoute(string $method, string $path, $handler, array $middlewares = []): void
    {
        // Don't add base path to route definition
        // Base path is only used to strip from incoming requests
        $path = '/' . ltrim($path, '/');
        $path = rtrim($path, '/') ?: '/';
        
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'middlewares' => array_merge($this->middlewares, $middlewares),
            'pattern' => $this->convertToPattern($path)
        ];
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertToPattern(string $path): string
    {
        // Convert {param} to named capture groups
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch the request
     */
    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        // DEBUG: Show what we're working with
        $debug = false; // Set to true to see debug info
        if ($debug) {
            echo "<h3>Router Debug</h3>";
            echo "<pre>";
            echo "Original URI: " . $_SERVER['REQUEST_URI'] . "\n";
            echo "Parsed URI: " . $uri . "\n";
            echo "Base Path: " . $this->basePath . "\n";
            echo "Method: " . $method . "\n";
        }
        
        // Remove base path if set
        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        
        $uri = rtrim($uri, '/') ?: '/';
        
        if ($debug) {
            echo "Final URI: " . $uri . "\n";
            echo "\nRegistered Routes:\n";
            foreach ($this->routes as $route) {
                echo $route['method'] . " " . $route['path'] . " -> " . $route['pattern'] . "\n";
            }
            echo "</pre>";
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uri, $matches)) {
                // Extract route parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Execute middlewares
                foreach ($route['middlewares'] as $middleware) {
                    $result = $this->executeMiddleware($middleware, $params);
                    if ($result === false) {
                        return; // Middleware stopped execution
                    }
                }

                // Execute handler
                $this->executeHandler($route['handler'], $params);
                return;
            }
        }

        // No route found
        $this->handleNotFound();
    }

    /**
     * Execute middleware
     */
    private function executeMiddleware($middleware, array $params)
    {
        if (is_callable($middleware)) {
            return call_user_func($middleware, $params);
        }

        if (is_string($middleware) && class_exists($middleware)) {
            $instance = new $middleware();
            if (method_exists($instance, 'handle')) {
                return $instance->handle($params);
            }
        }

        return true;
    }

    /**
     * Execute route handler
     */
    private function executeHandler($handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        if (is_string($handler)) {
            // Handle Controller@method format
            if (strpos($handler, '@') !== false) {
                [$controller, $method] = explode('@', $handler);
                
                if (class_exists($controller)) {
                    $instance = new $controller();
                    if (method_exists($instance, $method)) {
                        call_user_func([$instance, $method], $params);
                        return;
                    }
                }
            }
            
            // Handle file path
            if (file_exists($handler)) {
                extract($params);
                require $handler;
                return;
            }
        }

        if (is_array($handler) && count($handler) === 2) {
            [$controller, $method] = $handler;
            
            if (is_string($controller) && class_exists($controller)) {
                $controller = new $controller();
            }
            
            if (is_object($controller) && method_exists($controller, $method)) {
                call_user_func([$controller, $method], $params);
                return;
            }
        }

        $this->handleNotFound();
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound(): void
    {
        http_response_code(404);
        
        // Show debug info in development
        echo "<h1>404 - Page Not Found</h1>";
        echo "<p>The requested page could not be found.</p>";
        
        // Debug information
        echo "<hr>";
        echo "<h3>Debug Information</h3>";
        echo "<pre>";
        echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
        echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
        echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
        echo "Base Path: " . $this->basePath . "\n";
        
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($this->basePath && strpos($uri, $this->basePath) === 0) {
            $uri = substr($uri, strlen($this->basePath));
        }
        $uri = rtrim($uri, '/') ?: '/';
        echo "Processed URI: " . $uri . "\n";
        
        echo "\nRegistered Routes (" . count($this->routes) . "):\n";
        foreach ($this->routes as $route) {
            echo $route['method'] . " " . $route['path'] . "\n";
        }
        echo "</pre>";
        
        exit;
    }

    /**
     * Redirect to a URL
     */
    public static function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: $url", true, $statusCode);
        exit;
    }

    /**
     * Get current URL
     */
    public static function currentUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    /**
     * Get base URL
     */
    public static function baseUrl(): string
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'];
    }
}
