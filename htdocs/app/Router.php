<?php
// Minimal Router class for modular route registration
class Router
{
    private $routes = [
        'GET' => [],
        'POST' => [],
        // Add more HTTP methods as needed
    ];
    private $dynamicRoutes = [
        'GET' => [],
        'POST' => [],
    ];

    public function get($path, $handler)
    {
        $this->registerRoute('GET', $path, $handler);
    }
    public function post($path, $handler)
    {
        $this->registerRoute('POST', $path, $handler);
    }
    private function registerRoute($method, $path, $handler)
    {
        if (strpos($path, '{') !== false) {
            // Dynamic route
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $this->normalize($path));
            $pattern = '#^' . $pattern . '$#';
            $this->dynamicRoutes[$method][] = [
                'pattern' => $pattern,
                'handler' => $handler,
                'paramNames' => $this->extractParamNames($path)
            ];
        } else {
            $this->routes[$method][$this->normalize($path)] = $handler;
        }
    }
    private function extractParamNames($path)
    {
        preg_match_all('#\{([^/]+)\}#', $path, $matches);
        return $matches[1];
    }
    public function dispatch($method, $path)
    {
        $path = $this->normalize($path);
        // Exact match first
        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            if (is_array($handler)) {
                $controller = new $handler[0]();
                $action = $handler[1];
                return $controller->$action();
            } elseif (is_callable($handler)) {
                return call_user_func($handler);
            }
        }
        // Dynamic match
        foreach ($this->dynamicRoutes[$method] as $route) {
            if (preg_match($route['pattern'], $path, $matches)) {
                array_shift($matches); // Remove full match
                $params = $matches;
                $handler = $route['handler'];
                if (is_array($handler)) {
                    $controller = new $handler[0]();
                    $action = $handler[1];
                    return call_user_func_array([$controller, $action], $params);
                } elseif (is_callable($handler)) {
                    return call_user_func_array($handler, $params);
                }
            }
        }
        // Not found
        http_response_code(404);
        // Try NotFoundController if it exists
        $controllerPath = dirname(__DIR__) . '/controllers/NotFoundController.php';
        if (file_exists($controllerPath)) {
            include_once $controllerPath;
            if (class_exists('NotFoundController')) {
                $nf = new NotFoundController();
                $nf->index();
                exit;
            }
        }
        // Fallback to direct include
        $custom404 = dirname(__DIR__) . '/views/system/404.php';
        if (file_exists($custom404)) {
            // Set default values for the custom 404 page
            if (!isset($h1)) { $h1 = 'Page Not Found';
            }
            if (!isset($message)) { $message = 'Sorry, the page you requested could not be found.';
            }
            if (!isset($homeLink)) { $homeLink = App::config('base_url') ?? '/';
            }
            include $custom404;
        } else {
            echo '404 Not Found';
        }
        exit;
    }
    private function normalize($path)
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
