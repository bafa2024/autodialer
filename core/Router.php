<?php

/**
 * Router Class for AutoDial Pro
 * Handles URL routing, middleware, and request/response management
 */
class Router
{
    private $routes = [];
    private $middleware = [];
    private $currentRoute = null;
    private $basePath = '';

    public function __construct($basePath = '')
    {
        $this->basePath = $basePath;
    }

    /**
     * Add a GET route
     */
    public function get($path, $handler, $middleware = [])
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    /**
     * Add a POST route
     */
    public function post($path, $handler, $middleware = [])
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    /**
     * Add a PUT route
     */
    public function put($path, $handler, $middleware = [])
    {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    /**
     * Add a DELETE route
     */
    public function delete($path, $handler, $middleware = [])
    {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    /**
     * Add a route for any HTTP method
     */
    public function any($path, $handler, $middleware = [])
    {
        $this->addRoute(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], $path, $handler, $middleware);
    }

    /**
     * Add a route with specific HTTP methods
     */
    public function match($methods, $path, $handler, $middleware = [])
    {
        $this->addRoute($methods, $path, $handler, $middleware);
    }

    /**
     * Add middleware to the router
     */
    public function middleware($middleware)
    {
        $this->middleware[] = $middleware;
    }

    /**
     * Add a route to the routes array
     */
    private function addRoute($method, $path, $handler, $middleware = [])
    {
        $methods = is_array($method) ? $method : [$method];
        $fullPath = $this->basePath . $path;

        foreach ($methods as $httpMethod) {
            $this->routes[] = [
                'method' => $httpMethod,
                'path' => $fullPath,
                'pattern' => $this->pathToPattern($fullPath),
                'handler' => $handler,
                'middleware' => array_merge($this->middleware, $middleware)
            ];
        }
    }

    /**
     * Convert path to regex pattern
     */
    private function pathToPattern($path)
    {
        // Convert route parameters like {id} to regex patterns
        $pattern = preg_replace('/\{([^}]+)\}/', '(?P<$1>[^/]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch the request to the appropriate handler
     */
    public function dispatch()
    {
        $request = $this->getRequest();
        $method = $request['method'];
        $path = $request['path'];

        // Find matching route
        $route = $this->findRoute($method, $path);

        if (!$route) {
            return $this->handleNotFound();
        }

        $this->currentRoute = $route;

        // Extract parameters from URL
        $params = $this->extractParams($route['pattern'], $path);

        // Run middleware
        $response = $this->runMiddleware($route['middleware'], $request, $params);

        if ($response !== null) {
            return $response;
        }

        // Execute the handler
        return $this->executeHandler($route['handler'], $request, $params);
    }

    /**
     * Find a matching route for the given method and path
     */
    private function findRoute($method, $path)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && preg_match($route['pattern'], $path)) {
                return $route;
            }
        }
        return null;
    }

    /**
     * Extract parameters from URL using regex pattern
     */
    private function extractParams($pattern, $path)
    {
        $params = [];
        if (preg_match($pattern, $path, $matches)) {
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
        }
        return $params;
    }

    /**
     * Run middleware chain
     */
    private function runMiddleware($middleware, $request, $params)
    {
        foreach ($middleware as $mw) {
            if (is_callable($mw)) {
                $response = call_user_func($mw, $request, $params);
                if ($response !== null) {
                    return $response;
                }
            } elseif (is_string($mw) && class_exists($mw)) {
                $instance = new $mw();
                if (method_exists($instance, 'handle')) {
                    $response = $instance->handle($request, $params);
                    if ($response !== null) {
                        return $response;
                    }
                }
            }
        }
        return null;
    }

    /**
     * Execute the route handler
     */
    private function executeHandler($handler, $request, $params)
    {
        if (is_callable($handler)) {
            return call_user_func($handler, $request, $params);
        } elseif (is_string($handler)) {
            // Handle controller@method format
            if (strpos($handler, '@') !== false) {
                list($controller, $method) = explode('@', $handler);
                if (class_exists($controller)) {
                    $instance = new $controller();
                    if (method_exists($instance, $method)) {
                        return $instance->$method($request, $params);
                    }
                }
            } else {
                // Handle view rendering
                return $this->renderView($handler, $params);
            }
        }

        throw new Exception("Invalid route handler");
    }

    /**
     * Render a view file
     */
    private function renderView($view, $data = [])
    {
        $viewFile = "views/{$view}.php";
        
        if (file_exists($viewFile)) {
            extract($data);
            ob_start();
            include $viewFile;
            return ob_get_clean();
        }

        throw new Exception("View not found: {$view}");
    }

    /**
     * Get current request information
     */
    private function getRequest()
    {
        return [
            'method' => $_SERVER['REQUEST_METHOD'] ?? 'GET',
            'path' => parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH),
            'query' => $_GET,
            'body' => $_POST,
            'headers' => getallheaders(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ];
    }

    /**
     * Handle 404 Not Found
     */
    private function handleNotFound()
    {
        http_response_code(404);
        
        if ($this->isApiRequest()) {
            return json_encode(['error' => 'Not Found', 'message' => 'The requested resource was not found']);
        }
        
        return $this->renderView('errors/404');
    }

    /**
     * Check if the request is an API request
     */
    private function isApiRequest()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '';
        return strpos($path, '/api/') === 0 || 
               isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;
    }

    /**
     * Get current route information
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    /**
     * Generate URL for a named route
     */
    public function url($name, $params = [])
    {
        // This would need to be implemented with named routes
        // For now, return a simple URL
        return $name;
    }

    /**
     * Redirect to another URL
     */
    public function redirect($url, $statusCode = 302)
    {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    public function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }
} 