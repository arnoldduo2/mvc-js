<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Route Class
 * 
 * Represents a single route with its properties and middleware.
 * Provides fluent API for route configuration.
 */
class Route
{
   private string $method;
   private string $uri;
   private mixed $action;
   private array $middleware = [];
   private ?string $name = null;
   private array $constraints = [];
   private array $defaults = [];

   /**
    * Create a new route instance
    * 
    * @param string $method HTTP method
    * @param string $uri Route URI pattern
    * @param mixed $action Controller/closure
    */
   public function __construct(string $method, string $uri, mixed $action)
   {
      $this->method = strtoupper($method);
      $this->uri = '/' . trim($uri, '/');
      $this->action = $action;
   }

   /**
    * Add middleware to the route
    * 
    * @param string|array $middleware Middleware class name(s)
    * @return self
    */
   public function middleware(string|array $middleware): self
   {
      $middleware = is_array($middleware) ? $middleware : [$middleware];
      $this->middleware = array_merge($this->middleware, $middleware);
      return $this;
   }

   /**
    * Set the route name
    * 
    * @param string $name Route name
    * @return self
    */
   public function name(string $name): self
   {
      $this->name = $name;
      // Auto-register with Router
      Router::registerNamedRoute($name, $this);
      return $this;
   }

   /**
    * Add parameter constraint
    * 
    * @param string $param Parameter name
    * @param string $pattern Regex pattern
    * @return self
    */
   public function where(string $param, string $pattern): self
   {
      $this->constraints[$param] = $pattern;
      return $this;
   }

   /**
    * Add multiple parameter constraints
    * 
    * @param array $constraints Array of param => pattern
    * @return self
    */
   public function whereArray(array $constraints): self
   {
      $this->constraints = array_merge($this->constraints, $constraints);
      return $this;
   }

   /**
    * Set default value for parameter
    * 
    * @param string $param Parameter name
    * @param mixed $value Default value
    * @return self
    */
   public function defaults(string $param, mixed $value): self
   {
      $this->defaults[$param] = $value;
      return $this;
   }

   /**
    * Check if the route matches the given method and URI
    * 
    * @param string $method HTTP method
    * @param string $uri Request URI
    * @return bool|array False if no match, array of parameters if match
    */
   public function matches(string $method, string $uri): bool|array
   {
      // Check method
      if ($this->method !== strtoupper($method)) {
         return false;
      }

      // Normalize URI
      $uri = '/' . trim($uri, '/');

      // Exact match
      if ($this->uri === $uri) {
         return [];
      }

      // Pattern match
      $pattern = $this->buildPattern();
      if (preg_match($pattern, $uri, $matches)) {
         array_shift($matches); // Remove full match

         // Extract parameter names
         $params = [];
         preg_match_all('/\{([a-zA-Z0-9_]+)\??}/', $this->uri, $paramNames);

         foreach ($paramNames[1] as $index => $paramName) {
            $params[$paramName] = $matches[$index] ?? ($this->defaults[$paramName] ?? null);
         }

         return $params;
      }

      return false;
   }

   /**
    * Build regex pattern from URI
    * 
    * @return string
    */
   private function buildPattern(): string
   {
      $pattern = $this->uri;

      // Replace {param} with regex
      $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\}/', function ($matches) {
         $param = $matches[1];
         // Use constraint if exists, otherwise match anything except /
         return '(' . ($this->constraints[$param] ?? '[^/]+') . ')';
      }, $pattern);

      // Replace {param?} with optional regex
      $pattern = preg_replace_callback('/\{([a-zA-Z0-9_]+)\?\}/', function ($matches) {
         $param = $matches[1];
         // Use constraint if exists, otherwise match anything except /
         return '(' . ($this->constraints[$param] ?? '[^/]+') . ')?';
      }, $pattern);

      return '#^' . $pattern . '$#';
   }

   /**
    * Get the route method
    * 
    * @return string
    */
   public function getMethod(): string
   {
      return $this->method;
   }

   /**
    * Get the route URI
    * 
    * @return string
    */
   public function getUri(): string
   {
      return $this->uri;
   }

   /**
    * Get the route action
    * 
    * @return mixed
    */
   public function getAction(): mixed
   {
      return $this->action;
   }

   /**
    * Get the route middleware
    * 
    * @return array
    */
   public function getMiddleware(): array
   {
      return $this->middleware;
   }

   /**
    * Get the route name
    * 
    * @return string|null
    */
   public function getName(): ?string
   {
      return $this->name;
   }

   /**
    * Get parameter constraints
    * 
    * @return array
    */
   public function getConstraints(): array
   {
      return $this->constraints;
   }
}
