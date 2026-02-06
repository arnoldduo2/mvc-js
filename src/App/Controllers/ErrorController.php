<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;
use App\App\Core\View;

/**
 * Error Controller
 * 
 * Handles error pages with SPA and API compatibility
 */
class ErrorController extends Controller
{
   /**
    * Handle 404 Not Found errors
    * 
    * @return void
    */
   public function notFound(): void
   {
      http_response_code(404);

      $uri = $_SERVER['REQUEST_URI'] ?? '/';
      $isApiRequest = str_starts_with($uri, '/api/');
      $isSpaRequest = $this->isSpaRequest();

      if ($isApiRequest) {
         // Return JSON error for API requests
         View::json([
            'error' => true,
            'message' => 'Route not found',
            'code' => 404,
         ], 404);
      } elseif ($isSpaRequest) {
         // Return SPA-compatible response
         View::page('errors/404', [
            'title' => '404 - Page Not Found'
         ]);
      } else {
         // Return full HTML page for regular requests
         $content = View::render('errors/404');
         echo View::layout('layouts/app', $content, [
            'title' => '404 - Page Not Found'
         ]);
      }
   }

   /**
    * Handle 403 Forbidden errors
    * 
    * @param array $permissions Required permissions (optional)
    * @param array $roles Required roles (optional)
    * @return void
    */
   public function forbidden(array $permissions = [], array $roles = []): void
   {
      http_response_code(403);

      $uri = $_SERVER['REQUEST_URI'] ?? '/';
      $isApiRequest = str_starts_with($uri, '/api/');
      $isSpaRequest = $this->isSpaRequest();

      $data = [
         'title' => '403 - Forbidden',
         'permissions' => $permissions,
         'roles' => $roles,
      ];

      if ($isApiRequest) {
         // Return JSON error for API requests
         $jsonData = [
            'error' => true,
            'message' => 'Forbidden - Insufficient permissions',
            'code' => 403,
         ];

         if (!empty($permissions)) {
            $jsonData['required_permissions'] = $permissions;
         }

         if (!empty($roles)) {
            $jsonData['required_roles'] = $roles;
         }

         View::json($jsonData, 403);
      } elseif ($isSpaRequest) {
         // Return SPA-compatible response
         View::page('errors/403', $data);
      } else {
         // Return full HTML page for regular requests
         $content = View::render('errors/403', $data);
         echo View::layout('layouts/app', $content, [
            'title' => '403 - Forbidden'
         ]);
      }
   }

   /**
    * Check if the current request is a SPA request
    * 
    * @return bool
    */
   private function isSpaRequest(): bool
   {
      return (
         isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
      ) || (
         isset($_SERVER['HTTP_ACCEPT']) &&
         str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')
      );
   }
}
