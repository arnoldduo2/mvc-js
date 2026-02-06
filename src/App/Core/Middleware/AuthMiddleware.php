<?php

declare(strict_types=1);

namespace App\App\Core\Middleware;

/**
 * Authentication Middleware
 * 
 * Checks if the user is authenticated before allowing access.
 */
class AuthMiddleware implements MiddlewareInterface
{
   /**
    * Handle the request
    * 
    * @param array $request Request data
    * @param callable $next Next middleware in the pipeline
    * @return mixed
    */
   public function handle(array $request, callable $next): mixed
   {
      // Check if user is authenticated
      if (!$this->isAuthenticated()) {
         return $this->handleUnauthenticated($request);
      }

      // User is authenticated, continue to next middleware
      return $next($request);
   }

   /**
    * Check if user is authenticated
    * 
    * @return bool
    */
   private function isAuthenticated(): bool
   {
      // Check session for authenticated user
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      // Check for session-based authentication
      if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
         return true;
      }

      // Check for API token authentication
      if ($this->hasValidApiToken()) {
         return true;
      }

      return false;
   }

   /**
    * Check for valid API token
    * 
    * @return bool
    */
   private function hasValidApiToken(): bool
   {
      // Check Authorization header
      $headers = getallheaders();
      $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

      if ($authHeader && str_starts_with($authHeader, 'Bearer ')) {
         $token = substr($authHeader, 7);

         // TODO: Validate token against database
         // For now, just check if token exists
         return !empty($token);
      }

      return false;
   }

   /**
    * Handle unauthenticated request
    * 
    * @param array $request Request data
    * @return mixed
    */
   private function handleUnauthenticated(array $request): mixed
   {
      $uri = $_SERVER['REQUEST_URI'] ?? '/';
      $isApiRequest = str_starts_with($uri, '/api/');

      if ($isApiRequest) {
         // Return JSON error for API requests
         http_response_code(401);
         header('Content-Type: application/json');
         echo json_encode([
            'error' => true,
            'message' => 'Unauthenticated',
            'code' => 401,
         ]);
         exit;
      } else {
         // Redirect to login for web requests
         $loginUrl = '/login';
         $returnUrl = urlencode($uri);
         header("Location: {$loginUrl}?return={$returnUrl}");
         exit;
      }
   }
}
