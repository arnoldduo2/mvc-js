<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Component Helper Class
 * 
 * Allows for slot-based component rendering (wrapping content)
 */
class Component
{
   private static array $stack = [];

   /**
    * Open a component to wrap content
    * 
    * @param string $name Component name (dot notation)
    * @param array $data Data to pass to component
    * @return void
    */
   public static function open(string $name, array $data = []): void
   {
      // Push component info to stack
      self::$stack[] = [
         'name' => $name,
         'data' => $data
      ];

      // Start output buffering to capture slot content
      ob_start();
   }

   /**
    * Close the current component and render it
    * 
    * @return string Rendered component HTML
    */
   public static function close(): string
   {
      if (empty(self::$stack)) {
         return '';
      }

      // Get content from buffer (the slot)
      $slot = ob_get_clean();

      // Pop component info
      $component = array_pop(self::$stack);

      // Add slot to data
      $data = $component['data'];
      $data['slot'] = $slot;

      // Render the component
      return View::component($component['name'], $data);
   }
}
