<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Route Group Class
 * 
 * Manages route groups with shared attributes like middleware and prefix.
 */
class RouteGroup
{
   private array $attributes;
   private static array $groupStack = [];

   /**
    * Create a new route group
    * 
    * @param array $attributes Group attributes (prefix, middleware, etc.)
    */
   public function __construct(array $attributes = [])
   {
      $this->attributes = $attributes;
   }

   /**
    * Start a new route group
    * 
    * @param array $attributes Group attributes
    * @return void
    */
   public static function push(array $attributes): void
   {
      self::$groupStack[] = $attributes;
   }

   /**
    * End the current route group
    * 
    * @return void
    */
   public static function pop(): void
   {
      array_pop(self::$groupStack);
   }

   /**
    * Get merged attributes from all active groups
    * 
    * @return array
    */
   public static function getMergedAttributes(): array
   {
      if (empty(self::$groupStack)) {
         return [];
      }

      $merged = [
         'prefix' => '',
         'middleware' => [],
      ];

      foreach (self::$groupStack as $group) {
         // Merge prefixes
         if (isset($group['prefix'])) {
            $merged['prefix'] .= '/' . trim($group['prefix'], '/');
         }

         // Merge middleware
         if (isset($group['middleware'])) {
            $middleware = is_array($group['middleware']) ? $group['middleware'] : [$group['middleware']];
            $merged['middleware'] = array_merge($merged['middleware'], $middleware);
         }
      }

      // Clean up prefix
      $merged['prefix'] = '/' . trim($merged['prefix'], '/');
      if ($merged['prefix'] === '/') {
         $merged['prefix'] = '';
      }

      return $merged;
   }

   /**
    * Apply group attributes to a URI
    * 
    * @param string $uri Original URI
    * @return string Modified URI with group prefix
    */
   public static function applyPrefix(string $uri): string
   {
      $attributes = self::getMergedAttributes();

      if (empty($attributes['prefix'])) {
         return $uri;
      }

      return $attributes['prefix'] . '/' . trim($uri, '/');
   }

   /**
    * Get group middleware
    * 
    * @return array
    */
   public static function getMiddleware(): array
   {
      $attributes = self::getMergedAttributes();
      return $attributes['middleware'] ?? [];
   }
}
