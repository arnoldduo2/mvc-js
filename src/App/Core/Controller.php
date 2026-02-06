<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Base Controller Class
 * 
 * Provides comprehensive helper methods for all controllers including:
 * - View rendering with SPA support
 * - Request handling and validation
 * - Session and flash message management
 * - Authorization helpers
 * - File upload handling
 * - Response utilities
 */
abstract class Controller
{
   // ==================== VIEW & RESPONSE METHODS ====================

   /**
    * Render a view with automatic SPA detection
    * 
    * @param string $view View file path
    * @param array $data Data to pass to view
    * @return void
    */
   protected function view(string $view, array $data = []): void
   {
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
    * Return success JSON response
    * 
    * @param string $message Success message
    * @param array $data Additional data
    * @param int $statusCode HTTP status code
    * @return void
    */
   protected function success(string $message, array $data = [], int $statusCode = 200): void
   {
      $this->json(array_merge([
         'success' => true,
         'message' => $message,
      ], $data), $statusCode);
   }

   /**
    * Return error JSON response
    * 
    * @param string $message Error message
    * @param array $errors Validation errors or additional error data
    * @param int $statusCode HTTP status code
    * @return void
    */
   protected function error(string $message, array $errors = [], int $statusCode = 400): void
   {
      $response = [
         'success' => false,
         'error' => true,
         'message' => $message,
      ];

      if (!empty($errors)) {
         $response['errors'] = $errors;
      }

      $this->json($response, $statusCode);
   }

   // ==================== REDIRECT METHODS ====================

   /**
    * Redirect to URL
    * 
    * @param string $url URL to redirect to
    * @param int $code HTTP status code
    * @return void
    */
   protected function redirect(string $url, int $code = 302): void
   {
      header("Location: {$url}", true, $code);
      exit;
   }

   /**
    * Redirect back to previous page
    * 
    * @param string $fallback Fallback URL if no referer
    * @return void
    */
   protected function back(string $fallback = '/'): void
   {
      $referer = $_SERVER['HTTP_REFERER'] ?? url($fallback);
      $this->redirect($referer);
   }

   /**
    * Redirect with flash message
    * 
    * @param string $url URL to redirect to
    * @param string $message Flash message
    * @param string $type Message type (success, error, warning, info)
    * @return void
    */
   protected function redirectWith(string $url, string $message, string $type = 'success'): void
   {
      $this->flash($type, $message);
      $this->redirect($url);
   }

   // ==================== REQUEST METHODS ====================

   /**
    * Get request input value
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
    * Get all request inputs
    * 
    * @return array
    */
   protected function all(): array
   {
      return array_merge($_GET, $_POST);
   }

   /**
    * Get only specified inputs
    * 
    * @param array $keys Keys to retrieve
    * @return array
    */
   protected function only(array $keys): array
   {
      $data = [];
      foreach ($keys as $key) {
         $data[$key] = $this->input($key);
      }
      return $data;
   }

   /**
    * Get all inputs except specified keys
    * 
    * @param array $keys Keys to exclude
    * @return array
    */
   protected function except(array $keys): array
   {
      $data = $this->all();
      foreach ($keys as $key) {
         unset($data[$key]);
      }
      return $data;
   }

   /**
    * Check if input exists
    * 
    * @param string $key Input key
    * @return bool
    */
   protected function has(string $key): bool
   {
      return isset($_POST[$key]) || isset($_GET[$key]);
   }

   /**
    * Check if input exists and is not empty
    * 
    * @param string $key Input key
    * @return bool
    */
   protected function filled(string $key): bool
   {
      return $this->has($key) && !empty($this->input($key));
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
    * Check if request method is POST
    * 
    * @return bool
    */
   protected function isPost(): bool
   {
      return $_SERVER['REQUEST_METHOD'] === 'POST';
   }

   /**
    * Check if request method is GET
    * 
    * @return bool
    */
   protected function isGet(): bool
   {
      return $_SERVER['REQUEST_METHOD'] === 'GET';
   }

   /**
    * Get request method
    * 
    * @return string
    */
   protected function method(): string
   {
      return $_SERVER['REQUEST_METHOD'];
   }

   // ==================== VALIDATION METHODS ====================

   /**
    * Validate request data
    * 
    * @param array $rules Validation rules
    * @param array $data Data to validate (defaults to all inputs)
    * @return Validator
    */
   protected function validate(array $rules, ?array $data = null): Validator
   {
      $data = $data ?? $this->all();
      return Validator::make($data, $rules);
   }

   /**
    * Validate and redirect back with errors on failure
    * 
    * @param array $rules Validation rules
    * @param array $data Data to validate (defaults to all inputs)
    * @return array Validated data
    */
   protected function validateOrFail(array $rules, ?array $data = null): array
   {
      $validator = $this->validate($rules, $data);

      if ($validator->fails()) {
         $_SESSION['errors'] = $validator->errors();
         $_SESSION['old'] = $data ?? $this->all();
         $this->back();
      }

      return $validator->validated();
   }

   /**
    * Verify CSRF token
    * 
    * @param array $data Request data (defaults to $_POST)
    * @return bool
    */
   protected function verifyCsrf(?array $data = null): bool
   {
      return CSRF::verify($data ?? $_POST);
   }

   /**
    * Verify CSRF token or fail
    * 
    * @param array $data Request data (defaults to $_POST)
    * @return void
    */
   protected function verifyCsrfOrFail(?array $data = null): void
   {
      if (!$this->verifyCsrf($data)) {
         $_SESSION['errors'] = ['_token' => ['Invalid CSRF token. Please try again.']];
         $this->back();
      }
   }

   // ==================== SESSION METHODS ====================

   /**
    * Get session value
    * 
    * @param string $key Session key
    * @param mixed $default Default value
    * @return mixed
    */
   protected function session(string $key, mixed $default = null): mixed
   {
      $this->ensureSession();
      return $_SESSION[$key] ?? $default;
   }

   /**
    * Set session value
    * 
    * @param string $key Session key
    * @param mixed $value Value to store
    * @return void
    */
   protected function setSession(string $key, mixed $value): void
   {
      $this->ensureSession();
      $_SESSION[$key] = $value;
   }

   /**
    * Check if session key exists
    * 
    * @param string $key Session key
    * @return bool
    */
   protected function hasSession(string $key): bool
   {
      $this->ensureSession();
      return isset($_SESSION[$key]);
   }

   /**
    * Remove session key
    * 
    * @param string $key Session key
    * @return void
    */
   protected function forgetSession(string $key): void
   {
      $this->ensureSession();
      unset($_SESSION[$key]);
   }

   /**
    * Ensure session is started
    * 
    * @return void
    */
   private function ensureSession(): void
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }
   }

   // ==================== FLASH MESSAGE METHODS ====================

   /**
    * Set flash message
    * 
    * @param string $type Message type (success, error, warning, info)
    * @param string $message Message content
    * @return void
    */
   protected function flash(string $type, string $message): void
   {
      $this->setSession("flash_{$type}", $message);
   }

   /**
    * Get flash message
    * 
    * @param string $type Message type
    * @return string|null
    */
   protected function getFlash(string $type): ?string
   {
      $key = "flash_{$type}";
      $message = $this->session($key);
      $this->forgetSession($key);
      return $message;
   }

   /**
    * Check if flash message exists
    * 
    * @param string $type Message type
    * @return bool
    */
   protected function hasFlash(string $type): bool
   {
      return $this->hasSession("flash_{$type}");
   }

   // ==================== FILE UPLOAD METHODS ====================

   /**
    * Check if file was uploaded
    * 
    * @param string $key File input name
    * @return bool
    */
   protected function hasFile(string $key): bool
   {
      return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK;
   }

   /**
    * Get uploaded file info
    * 
    * @param string $key File input name
    * @return array|null
    */
   protected function file(string $key): ?array
   {
      if (!$this->hasFile($key)) {
         return null;
      }

      return $_FILES[$key];
   }

   /**
    * Move uploaded file to destination
    * 
    * @param string $key File input name
    * @param string $destination Destination path
    * @param string|null $filename Custom filename (optional)
    * @return string|false Path to moved file or false on failure
    */
   protected function moveFile(string $key, string $destination, ?string $filename = null): string|false
   {
      if (!$this->hasFile($key)) {
         return false;
      }

      $file = $_FILES[$key];
      $filename = $filename ?? basename($file['name']);
      $path = rtrim($destination, '/') . '/' . $filename;

      if (move_uploaded_file($file['tmp_name'], $path)) {
         return $path;
      }

      return false;
   }

   /**
    * Validate file upload
    * 
    * @param string $key File input name
    * @param array $rules Validation rules (maxSize in bytes, allowedTypes)
    * @return array Errors (empty if valid)
    */
   protected function validateFile(string $key, array $rules = []): array
   {
      $errors = [];

      if (!$this->hasFile($key)) {
         $errors[] = "No file uploaded for {$key}";
         return $errors;
      }

      $file = $_FILES[$key];

      // Check max size
      if (isset($rules['maxSize']) && $file['size'] > $rules['maxSize']) {
         $maxMB = round($rules['maxSize'] / 1048576, 2);
         $errors[] = "File size exceeds maximum allowed size of {$maxMB}MB";
      }

      // Check allowed types
      if (isset($rules['allowedTypes'])) {
         $finfo = finfo_open(FILEINFO_MIME_TYPE);
         $mimeType = finfo_file($finfo, $file['tmp_name']);
         finfo_close($finfo);

         if (!in_array($mimeType, $rules['allowedTypes'])) {
            $errors[] = "File type not allowed. Allowed types: " . implode(', ', $rules['allowedTypes']);
         }
      }

      return $errors;
   }

   // ==================== AUTHORIZATION METHODS ====================

   /**
    * Check if user is authenticated
    * 
    * @return bool
    */
   protected function isAuthenticated(): bool
   {
      $this->ensureSession();
      return isset($_SESSION['user_id']) || isset($_SESSION['authenticated']);
   }

   /**
    * Get authenticated user ID
    * 
    * @return mixed
    */
   protected function userId(): mixed
   {
      return $this->session('user_id');
   }

   /**
    * Get authenticated user data
    * 
    * @return array|null
    */
   protected function user(): ?array
   {
      return $this->session('user');
   }

   /**
    * Require authentication or redirect
    * 
    * @param string $redirectTo URL to redirect if not authenticated
    * @return void
    */
   protected function requireAuth(string $redirectTo = '/login'): void
   {
      if (!$this->isAuthenticated()) {
         $this->flash('error', 'You must be logged in to access this page.');
         $this->redirect(url($redirectTo));
      }
   }

   /**
    * Require guest (not authenticated) or redirect
    * 
    * @param string $redirectTo URL to redirect if authenticated
    * @return void
    */
   protected function requireGuest(string $redirectTo = '/'): void
   {
      if ($this->isAuthenticated()) {
         $this->redirect(url($redirectTo));
      }
   }

   // ==================== UTILITY METHODS ====================

   /**
    * Abort with error page
    * 
    * @param int $code HTTP status code
    * @param string $message Error message
    * @return void
    */
   protected function abort(int $code = 404, string $message = ''): void
   {
      http_response_code($code);

      if ($this->isAjaxRequest()) {
         $this->error($message ?: "Error {$code}", [], $code);
      } else {
         $view = match ($code) {
            403 => 'errors/403',
            404 => 'errors/404',
            default => 'errors/error',
         };

         $content = View::render($view, [
            'code' => $code,
            'message' => $message,
         ]);

         echo View::layout('layouts/app', $content, [
            'title' => "Error {$code}",
         ]);
         exit;
      }
   }

   /**
    * Generate pagination data
    * 
    * @param int $total Total items
    * @param int $perPage Items per page
    * @param int $currentPage Current page
    * @return array Pagination data
    */
   protected function paginate(int $total, int $perPage = 15, ?int $currentPage = null): array
   {
      $currentPage = $currentPage ?? (int)($this->input('page', 1));
      $currentPage = max(1, $currentPage);
      $totalPages = (int)ceil($total / $perPage);
      $currentPage = min($currentPage, max(1, $totalPages));
      $offset = ($currentPage - 1) * $perPage;

      return [
         'total' => $total,
         'per_page' => $perPage,
         'current_page' => $currentPage,
         'total_pages' => $totalPages,
         'offset' => $offset,
         'has_more' => $currentPage < $totalPages,
         'has_previous' => $currentPage > 1,
      ];
   }
}