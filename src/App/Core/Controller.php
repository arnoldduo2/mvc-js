<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Base Controller Class
 * 
 * Provides helper methods for all controllers
 */
abstract class Controller
{
   /**
    * Render a view
    * 
    * @param string $view View file path
    * @param array $data Data to pass to view
    * @return void
    */
   protected function view(string $view, array $data = []): void
   {
      // Check if this is an AJAX/SPA request
      $isAjax = $this->isAjaxRequest();

      if ($isAjax) {
         // Return JSON for SPA
         View::page($view, $data);
      } else {
         // Render full HTML page
         $content = View::render($view, $data);
         echo View::layout('layouts/app', $content, $data);
      }
   }

   /**
    * Return JSON response
    * 
    * @param array $data Data to return
    * @param int $statusCode HTTP status code
    * @return void
    */
   protected function json(array $data, int $statusCode = 200): void
   {
      View::json($data, $statusCode);
   }

   /**
    * Redirect to URL
    * 
    * @param string $url URL to redirect to
    * @return void
    */
   protected function redirect(string $url): void
   {
      header("Location: {$url}");
      exit;
   }

   /**
    * Check if request is AJAX/SPA
    * 
    * @return bool
    */
   protected function isAjaxRequest(): bool
   {
      return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
         && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
   }

   /**
    * Get request input
    * 
    * @param string $key Input key
    * @param mixed $default Default value
    * @return mixed
    */
   protected function input(string $key, mixed $default = null): mixed
   {
      return $_POST[$key] ?? $_GET[$key] ?? $default;
   }

   /**
    * Validate required fields
    * 
    * @param array $required Required field names
    * @return array Validation errors (empty if valid)
    */
   protected function validate(array $required): array
   {
      $errors = [];

      foreach ($required as $field) {
         if (empty($this->input($field))) {
            $errors[$field] = "The {$field} field is required.";
         }
      }

      return $errors;
   }
}