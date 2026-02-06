<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * View Class
 * 
 * Handles view rendering and JSON responses for SPA
 */
class View
{
   /**
    * Base path for views
    */
   private static string $viewPath = '';

   /**
    * Initialize view path
    */
   private static function init(): void
   {
      if (empty(self::$viewPath)) {
         self::$viewPath = dirname(__DIR__, 2) . '/resources/views/';
      }
   }

   /**
    * Render a view with data
    * 
    * @param string $view View file path (e.g., 'pages/home')
    * @param array $data Data to pass to view
    * @return string Rendered HTML
    */
   public static function render(string $view, array $data = []): string
   {
      self::init();

      // Extract data to variables
      extract($data);

      // Build view file path
      $viewFile = self::$viewPath . $view . '.php';

      if (!file_exists($viewFile)) {
         throw new \Exception("View file not found: {$viewFile}");
      }

      // Start output buffering
      ob_start();

      // Include the view file
      include $viewFile;

      // Get the contents and clean buffer
      return ob_get_clean();
   }

   /**
    * Render a view and return as JSON for SPA
    * 
    * @param string $view View file path
    * @param array $data Data to pass to view
    * @param string $title Page title
    * @param array $scripts Scripts to execute
    * @param int $executeAfter Delay before executing scripts (ms)
    * @return void
    */
   public static function page(
      string $view,
      array $data = [],
      string $title = '',
      array $scripts = [],
      int $executeAfter = 100
   ): void {
      $content = self::render($view, $data);

      self::json([
         'type' => 'html',
         'content' => $content,
         'title' => $title ?: ($data['title'] ?? APP_NAME),
         'scripts' => $scripts,
         'executeAfter' => $executeAfter,
      ]);
   }

   /**
    * Return JSON response
    * 
    * @param array $data Data to return
    * @param int $statusCode HTTP status code
    * @return void
    */
   public static function json(array $data, int $statusCode = 200): void
   {
      http_response_code($statusCode);
      header('Content-Type: application/json');
      echo json_encode($data);
      exit;
   }

   /**
    * Render a layout with content
    * 
    * @param string $layout Layout file path
    * @param string $content Content to insert
    * @param array $data Additional data
    * @return string Rendered HTML
    */
   public static function layout(string $layout, string $content, array $data = []): string
   {
      $data['content'] = $content;
      return self::render($layout, $data);
   }

   /**
    * Render and cache a view
    * 
    * @param string $view View file path
    * @param array $data Data to pass to view
    * @param int $ttl Cache time to live in seconds
    * @param string $layout Layout file (default: 'layouts/app')
    * @return void
    */
   public static function cached(string $view, array $data = [], int $ttl = 3600, string $layout = 'layouts/app'): void
   {
      // Generate cache key based on view and data
      $cacheKey = 'view:' . $view . ':' . md5(serialize($data));

      // Try to get from cache
      $cached = Cache::get($cacheKey);

      if ($cached !== null) {
         echo $cached;
         return;
      }

      // Render view
      ob_start();
      self::page($view, $data, $layout);
      $output = ob_get_clean();

      // Cache the output
      Cache::put($cacheKey, $output, $ttl);

      echo $output;
   }

   /**
    * Clear view cache
    * 
    * @param string|null $view Specific view to clear (null for all views)
    * @return bool
    */
   public static function clearCache(?string $view = null): bool
   {
      if ($view === null) {
         return Cache::tags(['views'])->flushTags();
      }

      // Clear specific view cache (all variations)
      $pattern = 'view:' . $view . ':';
      // Note: This is a simple implementation
      // For production, you'd want a more sophisticated pattern matching
      return Cache::forget($pattern);
   }
}
