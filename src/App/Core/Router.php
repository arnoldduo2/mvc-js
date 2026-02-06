<?php

declare(strict_types=1);

namespace App\App\Core;

use Closure;
use Exception;

/**
 * Modern Router Class
 * 
 * Laravel-style router with fluent API, middleware support, and route groups.
 */
class Router
{
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static array $middlewareAliases = [
        'auth' => \App\App\Core\Middleware\AuthMiddleware::class,
        'permission' => \App\App\Core\Middleware\PermissionMiddleware::class,
        'role' => \App\App\Core\Middleware\RoleMiddleware::class,
    ];

    /**
     * Register a GET route
     * 
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    public static function get(string $uri, mixed $action): Route
    {
        return self::addRoute('GET', $uri, $action);
    }

    /**
     * Register a POST route
     * 
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    public static function post(string $uri, mixed $action): Route
    {
        return self::addRoute('POST', $uri, $action);
    }

    /**
     * Register a PUT route
     * 
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    public static function put(string $uri, mixed $action): Route
    {
        return self::addRoute('PUT', $uri, $action);
    }

    /**
     * Register a PATCH route
     * 
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    public static function patch(string $uri, mixed $action): Route
    {
        return self::addRoute('PATCH', $uri, $action);
    }

    /**
     * Register a DELETE route
     * 
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    public static function delete(string $uri, mixed $action): Route
    {
        return self::addRoute('DELETE', $uri, $action);
    }

    /**
     * Register a route that responds to any HTTP method
     * 
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    public static function any(string $uri, mixed $action): Route
    {
        $route = null;
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            $route = self::addRoute($method, $uri, $action);
        }
        return $route;
    }

    /**
     * Create a route group with shared attributes
     * 
     * @param array $attributes Group attributes (prefix, middleware)
     * @param callable $callback Routes definition callback
     * @return void
     */
    public static function group(array $attributes, callable $callback): void
    {
        RouteGroup::push($attributes);
        $callback();
        RouteGroup::pop();
    }

    /**
     * Add a route to the collection
     * 
     * @param string $method HTTP method
     * @param string $uri Route URI
     * @param mixed $action Controller/closure
     * @return Route
     */
    private static function addRoute(string $method, string $uri, mixed $action): Route
    {
        // Apply group prefix
        $uri = RouteGroup::applyPrefix($uri);

        // Create route
        $route = new Route($method, $uri, $action);

        // Apply group middleware
        $groupMiddleware = RouteGroup::getMiddleware();
        if (!empty($groupMiddleware)) {
            $route->middleware($groupMiddleware);
        }

        // Store route
        self::$routes[] = $route;

        return $route;
    }

    /**
     * Register a named route
     * 
     * @param string $name Route name
     * @param Route $route Route instance
     * @return void
     */
    public static function registerNamedRoute(string $name, Route $route): void
    {
        self::$namedRoutes[$name] = $route;
    }

    /**
     * Get a route by name
     * 
     * @param string $name Route name
     * @return Route|null
     */
    public static function getRouteByName(string $name): ?Route
    {
        return self::$namedRoutes[$name] ?? null;
    }

    /**
     * Generate URL for a named route
     * 
     * @param string $name Route name
     * @param array $params Route parameters
     * @return string
     */
    public static function route(string $name, array $params = []): string
    {
        $route = self::getRouteByName($name);

        if (!$route) {
            throw new Exception("Route '{$name}' not found");
        }

        $uri = $route->getUri();

        // Replace parameters
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
            $uri = str_replace('{' . $key . '?}', $value, $uri);
        }

        return $uri;
    }

    /**
     * Dispatch the request to the appropriate route
     * 
     * @return void
     */
    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove query string
        $uri = strtok($uri, '?');

        // Remove base path if app is in subdirectory
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $basePath = dirname($scriptName);
        if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }

        // Ensure URI starts with /
        $uri = '/' . ltrim($uri, '/');

        // Find matching route
        foreach (self::$routes as $route) {
            $params = $route->matches($method, $uri);

            if ($params !== false) {
                // Route matched, execute with middleware
                self::executeRoute($route, $params);
                return;
            }
        }

        // No route found
        self::handleNotFound();
    }

    /**
     * Execute a route with middleware pipeline
     * 
     * @param Route $route Matched route
     * @param array $params Route parameters
     * @return void
     */
    private static function executeRoute(Route $route, array $params): void
    {
        $middleware = $route->getMiddleware();

        // Build middleware pipeline
        $pipeline = array_reduce(
            array_reverse($middleware),
            function ($next, $middleware) use ($route, $params) {
                return function ($request) use ($next, $middleware, $route, $params) {
                    // Resolve middleware
                    $middlewareInstance = self::resolveMiddleware($middleware);

                    // Execute middleware
                    return $middlewareInstance->handle($request, $next);
                };
            },
            function ($request) use ($route, $params) {
                // Final handler - execute the route action
                return self::executeAction($route->getAction(), $params);
            }
        );

        // Execute pipeline
        $request = array_merge($_GET, $_POST, $params);
        $pipeline($request);
    }

    /**
     * Resolve middleware instance
     * 
     * @param string $middleware Middleware class or alias
     * @return object
     */
    private static function resolveMiddleware(string $middleware): object
    {
        // Check if it's a middleware with parameters (e.g., "permission:users.create")
        if (str_contains($middleware, ':')) {
            [$middlewareName, $parameters] = explode(':', $middleware, 2);
            $params = explode(',', $parameters);

            // Resolve alias
            $middlewareClass = self::$middlewareAliases[$middlewareName] ?? $middlewareName;

            // Instantiate with parameters
            return new $middlewareClass(...$params);
        }

        // Resolve alias
        $middlewareClass = self::$middlewareAliases[$middleware] ?? $middleware;

        // Instantiate without parameters
        return new $middlewareClass();
    }

    /**
     * Execute the route action
     * 
     * @param mixed $action Controller/closure
     * @param array $params Route parameters
     * @return mixed
     */
    private static function executeAction(mixed $action, array $params): mixed
    {
        if ($action instanceof Closure) {
            // Execute closure
            return call_user_func_array($action, $params);
        }

        if (is_array($action) && count($action) === 2) {
            // Execute controller method
            [$controller, $method] = $action;

            if (is_string($controller)) {
                $controller = new $controller();
            }

            if (method_exists($controller, $method)) {
                return call_user_func_array([$controller, $method], $params);
            }

            throw new Exception("Method {$method} not found in controller");
        }

        if (is_string($action) && str_contains($action, '::')) {
            // Execute static method
            [$controller, $method] = explode('::', $action);
            $controller = new $controller();
            return call_user_func_array([$controller, $method], $params);
        }

        throw new Exception("Invalid route action");
    }

    /**
     * Handle 404 Not Found
     * 
     * @return void
     */
    private static function handleNotFound(): void
    {
        $errorController = new \App\App\Controllers\ErrorController();
        $errorController->notFound();
    }

    /**
     * Register a middleware alias
     * 
     * @param string $alias Middleware alias
     * @param string $class Middleware class
     * @return void
     */
    public static function aliasMiddleware(string $alias, string $class): void
    {
        self::$middlewareAliases[$alias] = $class;
    }

    /**
     * Get all registered routes
     * 
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Get the base path for the application
     * 
     * @return string
     */
    public static function basePath(): string
    {
        static $basePath = null;

        if ($basePath === null) {
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $basePath = dirname($scriptName);

            // If in root, return empty string
            if ($basePath === '/' || $basePath === '\\') {
                $basePath = '';
            }
        }

        return $basePath;
    }

    /**
     * Generate a URL with base path prefix
     * 
     * @param string $path Path to append (e.g., '/about', 'dashboard')
     * @return string Full URL with base path
     */
    public static function url(string $path = ''): string
    {
        // Ensure path starts with /
        $path = '/' . ltrim($path, '/');

        // Get base path
        $basePath = self::basePath();

        // Combine base path and path
        return $basePath . $path;
    }

    /**
     * Legacy method for backward compatibility
     * Resolves and dispatches the current request
     * 
     * @return void
     */
    public static function requestResolve(): void
    {
        self::dispatch();
    }
}
