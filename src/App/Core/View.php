<?php

declare(strict_types=1);

namespace App\App\Core;

use ErrorException;

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
   private static string $fallbackViewPath = '';

   /**
    * Initialize view path
    */
   private static function init(): void
   {
      if (empty(self::$viewPath)) {
         self::$viewPath = dirname(__DIR__, 2) . '/resources/views/';
         self::$fallbackViewPath = __DIR__ . '/resources/views/';
      }
   }

   /**
    * Output core SPA scripts
    * 
    * @return string HTML script tags
    */
   public static function coreScripts(): string
   {
      self::init();

      $timerPatch = file_get_contents(__DIR__ . '/resources/js/timer-patch.js');
      $basePath = \App\App\Core\Router::basePath();
      $appUrl = \App\App\Core\Router::url('/core-js/app.js');

      return <<<HTML
      <!-- Core Timer Patch (Must run first) -->
      <script>
      {$timerPatch}
      </script>
      
      <!-- App Configuration -->
      <script>
         window.APP_BASE_PATH = '{$basePath}';
      </script>
      
      <!-- SPA Application -->
      <script type="module" src="{$appUrl}"></script>
HTML;
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

      // Convert dot notation to path
      $view = str_replace('.', '/', $view);

      // Extract data to variables
      extract($data);

      // Build view file path
      // 1. Check pages directory first
      $pagesFile = self::$viewPath . 'pages/' . $view . '.php';

      if (file_exists($pagesFile)) {
         $viewFile = $pagesFile;
      } else {
         // 2. Check root views directory (for layouts, errors, partials)
         $viewFile = self::$viewPath . $view . '.php';

         if (!file_exists($viewFile)) {
            // 3. Check fallback paths
            $fallbackPages = self::$fallbackViewPath . 'pages/' . $view . '.php';
            $fallbackRoot = self::$fallbackViewPath . $view . '.php';

            if (file_exists($fallbackPages)) {
               $viewFile = $fallbackPages;
            } elseif (file_exists($fallbackRoot)) {
               $viewFile = $fallbackRoot;
            } else {
               throw new \Exception("View file not found: {$view} (checked pages/ and root)");
            }
         }
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
      int $executeAfter = 100,
      int $statusCode = 200
   ): void {
      $content = self::render($view, $data);

      // Auto-inject assets (CSS/JS) matching the view name
      $assets = self::getAssets($view);

      // Always append JS to content (bottom of body)
      if (!empty($assets['js'])) {
         $content .= $assets['js'];
      }

      // Check if SPA request
      if (self::isSpaRequest()) {
         // For SPA, we append CSS to content so it loads with the partial
         // The router replaces the app container content, so this works
         if (!empty($assets['css'])) {
            $content .= $assets['css'];
         }

         self::json([
            'type' => 'html',
            'content' => $content,
            'title' => $title ?: ($data['title'] ?? APP_NAME),
            'scripts' => $scripts,
            'executeAfter' => $executeAfter,
         ], $statusCode);
      } else {
         // Full page load
         $data['content'] = $content;
         $data['title'] = $title ?: ($data['title'] ?? APP_NAME);

         // Inject CSS into <head> via layout variable
         if (!empty($assets['css'])) {
            $data['head'] = $assets['css'];
         }

         // Render layout
         echo self::render('app', $data);
      }
   }

   /**
    * Check if request is an SPA request
    */
   private static function isSpaRequest(): bool
   {
      return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
         strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
   }

   /**
    * Get automatic assets (CSS/JS) if they exist
    * 
    * @param string $view View name (e.g. 'dashboard/stats')
    * @return array ['css' => string, 'js' => string]
    */
   private static function getAssets(string $view): array
   {
      $view = str_replace('.', '/', $view);
      $css = '';
      $js = '';

      // Check for CSS
      $cssPath = '/css/' . $view . '.css'; // URL path
      $cssFile = dirname(__DIR__, 2) . '/resources' . $cssPath; // File path

      if (file_exists($cssFile)) {
         $url = \App\App\Core\Router::url($cssPath);
         $css = '<link rel="stylesheet" href="' . $url . '">';
      }

      // Check for JS
      $jsPath = '/js/' . $view . '.js'; // URL path
      $jsFile = dirname(__DIR__, 2) . '/resources' . $jsPath; // File path

      if (file_exists($jsFile)) {
         $url = \App\App\Core\Router::url($jsPath);
         // Add timestamp to force reload if needed, or just use clean URL
         // Using module type if it contains import/export, strictly standard script for now
         // adhering to user request for "script"
         $js = '<script src="' . $url . '"></script>';
      }

      return ['css' => $css, 'js' => $js];
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
    * Render a Main layout with content
    * 
    * @param string $layout Layout file path
    * @param string $content Content to insert
    * @param array $data Additional data
    * @return string Rendered HTML
    */
   public static function mainLayout(string $mainLayout, string $content, array $data = []): string
   {
      $data['content'] = $content;
      return self::render($mainLayout, $data);
   }

   /**
    * Find A partial to include if need. They are found inside the pages/<pageName>/partials
    * @param string $name
    * @param array $data
    * @throws ErrorException
    */
   public static function partials(string $name, array $data = [])
   {
      $required = [];
      $name = str_replace('.', '/', $name);
      foreach ($required as $r => $val) {
         $data[$r] = $val;
      }

      $file = self::$viewPath . "$name.php";
      if (!file_exists($file)) {
         throw new ErrorException("View file not found: $file");
      }
      return require_once $file;
   }

   /**
    * Render and cache a view
    * 
    * @param string $view View file path
    * @param array $data Data to pass to view
    * @param int $ttl Cache time to live in seconds
    * @param string $mainApp Main app layout file (default: 'app')
    * @return void
    */
   public static function cached(string $view, array $data = [], int $ttl = 3600, string $mainApp = 'app'): void
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
      self::page($view, $data, $mainApp);
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

   /**
    * Render a component
    * 
    * @param string $name Component name (dot notation supported, e.g. 'ui.card')
    * @param array $data Data to pass to component
    * @return string Rendered component HTML
    */
   public static function component(string $name, array $data = []): string
   {
      self::init();

      // Convert dot notation to path
      $path = str_replace('.', '/', $name);

      // Build component file path
      // Components are stored in src/resources/views/components/
      $file = self::$viewPath . 'components/' . $path . '.php';

      if (!file_exists($file)) {
         // Try fallback path if defined
         $fallbackFile = self::$fallbackViewPath . 'components/' . $path . '.php';
         if (!file_exists($fallbackFile)) {
            return "<!-- Component '{$name}' not found at {$file} -->";
         }
         $file = $fallbackFile;
      }

      // Extract data
      extract($data);

      // Start output buffering
      ob_start();

      // Include component
      include $file;

      return ob_get_clean();
   }
}
