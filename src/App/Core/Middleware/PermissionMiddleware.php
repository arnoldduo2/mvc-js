<?php

declare(strict_types=1);

namespace App\App\Core\Middleware;

/**
 * Permission Middleware
 * 
 * Checks if the authenticated user has the required permissions.
 */
class PermissionMiddleware implements MiddlewareInterface
{
   private array $permissions;
   private bool $requireAll;

   /**
    * Create a new permission middleware instance
    * 
    * @param string|array $permissions Required permission(s)
    * @param bool $requireAll Whether all permissions are required (AND) or any (OR)
    */
   public function __construct(string|array $permissions, bool $requireAll = false)
   {
      $this->permissions = is_array($permissions) ? $permissions : [$permissions];
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
      // Check if user has required permissions
      if (!$this->hasPermission()) {
         return $this->handleUnauthorized($request);
      }

      // User has permission, continue to next middleware
      return $next($request);
   }

   /**
    * Check if user has required permissions
    * 
    * @return bool
    */
   private function hasPermission(): bool
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      // Get user permissions from session
      $userPermissions = $_SESSION['permissions'] ?? [];

      if (empty($userPermissions)) {
         // TODO: Load permissions from database
         return false;
      }

      // Check permissions
      if ($this->requireAll) {
         // User must have ALL permissions
         foreach ($this->permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
               return false;
            }
         }
         return true;
      } else {
         // User must have ANY permission
         foreach ($this->permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
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
      $errorController->forbidden($this->permissions);
      exit;
   }
}
