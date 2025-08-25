<?php
// Minimal Router class for modular route registration
class Router
{
    private $middleware = [];
    // Register global middleware
    public function middleware($callable)
    {
        $this->middleware[] = $callable;
    }
    private $routes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
    ];
    private $dynamicRoutes = [
        'GET' => [],
        'POST' => [],
        'PUT' => [],
        'DELETE' => [],
        'PATCH' => [],
    ];
    private $namedRoutes = [];

    public function get($path, $handler, $name = null)
    {
        $this->registerRoute('GET', $path, $handler, $name);
    }
    public function post($path, $handler, $name = null)
    {
        $this->registerRoute('POST', $path, $handler, $name);
    }
    public function put($path, $handler, $name = null)
    {
        $this->registerRoute('PUT', $path, $handler, $name);
    }
    public function delete($path, $handler, $name = null)
    {
        $this->registerRoute('DELETE', $path, $handler, $name);
    }
    public function patch($path, $handler, $name = null)
    {
        $this->registerRoute('PATCH', $path, $handler, $name);
    }
    private function registerRoute($method, $path, $handler, $name = null)
    {
        if (strpos($path, '{') !== false) {
            // Dynamic route
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $this->normalize($path));
            $pattern = '#^' . $pattern . '$#';
            $this->dynamicRoutes[$method][] = [
                'pattern' => $pattern,
                'handler' => $handler,
                'paramNames' => $this->extractParamNames($path),
                'name' => $name
            ];
            if ($name) {
                $this->namedRoutes[$name] = ['method' => $method, 'path' => $path];
            }
        } else {
            $this->routes[$method][$this->normalize($path)] = $handler;
            if ($name) {
                $this->namedRoutes[$name] = ['method' => $method, 'path' => $path];
            }
        }
    }
    // Get route path by name
    public function route($name, $params = [])
    {
        if (!isset($this->namedRoutes[$name])) {
            return null;
        }
        $route = $this->namedRoutes[$name];
        $path = $route['path'];
        // Replace params in path
        foreach ($params as $key => $value) {
            $path = preg_replace('#\{' . preg_quote($key, '#') . '\}#', $value, $path);
        }
        return $path;
    }
    private function extractParamNames($path)
    {
        preg_match_all('#\{([^/]+)\}#', $path, $matches);
        return $matches[1];
    }
    public function dispatch($method, $path)
    {
        $path = $this->normalize($path);
        $request = [
            'method' => $method,
            'path' => $path,
            'params' => $_REQUEST,
        ];
        $next = function() use ($method, $path) {
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
                if (!isset($h1)) { $h1 = 'Page Not Found'; }
                if (!isset($message)) { $message = 'Sorry, the page you requested could not be found.'; }
                if (!isset($homeLink)) { $homeLink = App::config('base_url') ?? '/'; }
                include $custom404;
            } else {
                echo '404 Not Found';
            }
            exit;
        };
        // Run middleware chain
        $middlewareChain = array_reverse($this->middleware);
        $runner = array_reduce($middlewareChain, function($next, $mw) {
            return function($request) use ($mw, $next) {
                return $mw($request, $next);
            };
        }, $next);
        return $runner($request);
    }
    private function normalize($path)
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
