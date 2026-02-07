<?php

declare(strict_types=1);

namespace App\App\Core\Controllers;

use App\App\Core\Controller;
use App\App\Core\View;

/**
 * Core Error Controller
 * 
 * Handles default error pages when no application-level error controller is provided.
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
         View::json([
            'error' => true,
            'message' => 'Route not found',
            'code' => 404,
         ], 404);
      } elseif ($isSpaRequest) {
         View::page('errors/404', [
            'title' => '404 - Page Not Found'
         ], '', [], 100, 404);
      } else {
         // Try to use the app layout if possible, otherwise render standalone
         try {
            $content = View::render('errors/404');
            echo View::layout('layouts/app', $content, [
               'title' => '404 - Page Not Found'
            ]);
         } catch (\Throwable $e) {
            // Fallback to direct rendering if layout fails or views are missing in unusual ways
            echo $this->renderFallback404();
         }
      }
   }

   /**
    * Handle 403 Forbidden errors
    */
   public function forbidden(array $permissions = [], array $roles = []): void
   {
      http_response_code(403);
      // Implementation similar to 404 but for 403
      $uri = $_SERVER['REQUEST_URI'] ?? '/';
      $isApiRequest = str_starts_with($uri, '/api/');
      $isSpaRequest = $this->isSpaRequest();

      $data = [
         'title' => '403 - Forbidden',
         'permissions' => $permissions,
         'roles' => $roles,
      ];

      if ($isApiRequest) {
         View::json([
            'error' => true,
            'message' => 'Forbidden',
            'code' => 403,
         ], 403);
      } elseif ($isSpaRequest) {
         View::page('errors/403', $data, '', [], 100, 403);
      } else {
         try {
            $content = View::render('errors/403', $data);
            echo View::layout('layouts/app', $content, $data);
         } catch (\Throwable $e) {
            echo "<h1>403 Forbidden</h1>";
         }
      }
   }

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

   private function renderFallback404(): string
   {
      return '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body><h1>404 - Page Not Found</h1><p>The requested resource was not found.</p></body></html>';
   }
}
