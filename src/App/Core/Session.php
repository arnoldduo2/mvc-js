<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Session Class
 * 
 * Handles session management with legacy compatibility.
 */
class Session
{
   /**
    * Initialize session (idempotent)
    * 
    * @return void
    */
   public static function init(): void
   {
      if (session_status() === PHP_SESSION_NONE) {
         // Session config should be loaded in Application::loadSystemFiles via config/session.php
         // But we double check here just in case
         session_start();
      }
   }

   /**
    * Set a session variable
    * 
    * @param string $key
    * @param mixed $value
    * @return void
    */
   public static function put(string $key, mixed $value): void
   {
      self::init();
      $_SESSION[$key] = $value;
   }

   /**
    * Get a session variable
    * 
    * @param string $key
    * @param mixed $default
    * @return mixed
    */
   public static function get(string $key, mixed $default = null): mixed
   {
      self::init();
      return $_SESSION[$key] ?? $default;
   }

   /**
    * Check if a session variable exists
    * 
    * @param string $key
    * @return bool
    */
   public static function has(string $key): bool
   {
      self::init();
      return isset($_SESSION[$key]);
   }

   /**
    * Remove a session variable
    * 
    * @param string $key
    * @return void
    */
   public static function forget(string $key): void
   {
      self::init();
      if (isset($_SESSION[$key])) {
         unset($_SESSION[$key]);
      }
   }

   /**
    * Clear all session data
    * 
    * @return void
    */
   public static function flush(): void
   {
      self::init();
      session_unset();
   }

   /**
    * Regenerate session ID
    * 
    * @param bool $deleteOldSession
    * @return bool
    */
   public static function regenerate(bool $deleteOldSession = true): bool
   {
      self::init();
      return session_regenerate_id($deleteOldSession);
   }

   /**
    * Destroy the session
    * 
    * @return void
    */
   public static function destroy(): void
   {
      self::init();
      session_destroy();
   }

   // ==================== LEGACY COMPATIBILITY METHODS ====================

   /**
    * Legacy: Initialize
    */
   public static function start(): void
   {
      self::init();
   }

   /**
    * Legacy: Regenerate ID
    */
   public static function regen(): void
   {
      self::regenerate(true);
   }


   /**
    * Legacy: Set value
    */
   public static function set(string $key, $value): void
   {
      self::put($key, $value);
   }

   /**
    * Legacy: Clear value
    */
   public static function clear(string $key): void
   {
      self::forget($key);
   }

   /**
    * Legacy: Set User Data
    */
   public static function set_userData(string $key, $value): void
   {
      self::put($key, $value);
   }

   /**
    * Legacy: Get User Data
    */
   public static function get_userData(string $key): mixed
   {
      return self::get($key);
   }

   /**
    * Legacy: Set Settings
    */
   public static function set_settings(string $key, mixed $value): void
   {
      self::put($key, $value);
   }

   /**
    * Legacy: Get Settings
    */
   public static function get_settings(string $key): mixed
   {
      return self::get($key);
   }

   /**
    * Legacy: Set Messages
    */
   public static function set_messages(string $key, $value): void
   {
      self::put($key, $value);
   }

   /**
    * Legacy: Get Message
    */
   public static function get_message(string $key): mixed
   {
      return self::get($key);
   }

   /**
    * Legacy: Get State
    */
   public static function get_state(?string $key, string $storageKey = 'state'): mixed
   {
      self::init();
      return $_SESSION[$storageKey][$key] ?? null;
   }

   /**
    * Legacy: Set State
    */
   public static function set_state(string $key, mixed $value, string $storageKey = 'state'): void
   {
      self::init();
      $_SESSION[$storageKey][$key] = $value;
   }

   /**
    * Legacy: Clear Message
    */
   public static function clear_message(string $key, ?string $message_array = null): void
   {
      self::init();
      if ($message_array && isset($_SESSION[$message_array][$key])) {
         unset($_SESSION[$message_array][$key]);
      }
      if (isset($_SESSION[$key])) {
         unset($_SESSION[$key]);
      }
   }

   /**
    * Legacy: Destroy specific keys and session
    * Note: Legacy implementation destroyed the WHOLE session after unsetting keys.
    * We replicate that behavior.
    */
   public static function destroy_legacy(array $keys): void
   {
      foreach ($keys as $key) {
         self::forget($key);
      }
      self::destroy();
   }
}
