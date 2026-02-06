<?php

declare(strict_types=1);

namespace App\App\Core\Middleware;

/**
 * Role Middleware
 * 
 * Checks if the authenticated user has the required role(s).
 */
class RoleMiddleware implements MiddlewareInterface
{
   private array $roles;
   private bool $requireAll;

   /**
    * Create a new role middleware instance
    * 
    * @param string|array $roles Required role(s)
    * @param bool $requireAll Whether all roles are required (AND) or any (OR)
    */
   public function __construct(string|array $roles, bool $requireAll = false)
   {
      $this->roles = is_array($roles) ? $roles : [$roles];
      $this->requireAll = $requireAll;
   }

   /**
    * Handle the request
    * 
    * @param array $request Request data
    * @param callable $next Next middleware in the pipeline
    * @return mixed
    */
   public function handle(array $request, callable $next): mixed
   {
      // Check if user has required role
      if (!$this->hasRole()) {
         return $this->handleUnauthorized($request);
      }

      // User has role, continue to next middleware
      return $next($request);
   }

   /**
    * Check if user has required role
    * 
    * @return bool
    */
   private function hasRole(): bool
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      // Get user role from session
      $userRole = $_SESSION['role'] ?? null;
      $userRoles = $_SESSION['roles'] ?? [];

      // Support both single role and multiple roles
      if ($userRole && !in_array($userRole, $userRoles)) {
         $userRoles[] = $userRole;
      }

      if (empty($userRoles)) {
         return false;
      }

      // Check roles
      if ($this->requireAll) {
         // User must have ALL roles
         foreach ($this->roles as $role) {
            if (!in_array($role, $userRoles)) {
               return false;
            }
         }
         return true;
      } else {
         // User must have ANY role
         foreach ($this->roles as $role) {
            if (in_array($role, $userRoles)) {
               return true;
            }
         }
         return false;
      }
   }

   /**
    * Handle unauthorized request
    * 
    * @param array $request Request data
    * @return mixed
    */
   private function handleUnauthorized(array $request): mixed
   {
      $errorController = new \App\App\Controllers\ErrorController();
      $errorController->forbidden([], $this->roles);
      exit;
   }
}
