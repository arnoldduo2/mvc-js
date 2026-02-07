<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Cookie Management Class
 * 
 * Handles setting, getting, and deleting cookies with configurable defaults.
 */
class Cookie
{
   /**
    * Set a cookie
    * 
    * @param string $name
    * @param string $value
    * @param int $minutes Lifetime in minutes (0 for default config)
    * @param string|null $path
    * @param string|null $domain
    * @param bool|null $secure
    * @param bool|null $httpOnly
    * @param string|null $sameSite
    * @return bool
    */
   public static function set(
      string $name,
      string $value,
      int $minutes = 0,
      ?string $path = null,
      ?string $domain = null,
      ?bool $secure = null,
      ?bool $httpOnly = null,
      ?string $sameSite = null
   ): bool {
      // Load defaults from config
      // Note: We need a way to access config. Assuming a helper or manual include.
      // For now, we'll try to use the 'config' helper if it exists, otherwise define defaults.

      $config = function_exists('config') ? \config('cookie') : [];

      // If config('cookie') returns null (file not loaded/found), use hardcoded defaults
      if (empty($config)) {
         $configFile = __DIR__ . '/../../config/cookie.php';
         if (file_exists($configFile)) {
            $config = require $configFile;
         }
      }

      $lifetime = ($minutes > 0) ? $minutes : ($config['lifetime'] ?? 120);
      $expiry = time() + ($lifetime * 60);

      $path = $path ?? ($config['path'] ?? '/');
      $domain = $domain ?? ($config['domain'] ?? null);
      $secure = $secure ?? ($config['secure'] ?? false);
      $httpOnly = $httpOnly ?? ($config['httpOnly'] ?? true);
      $sameSite = $sameSite ?? ($config['sameSite'] ?? 'Lax');

      if (PHP_VERSION_ID >= 70300) {
         $options = [
            'expires' => $expiry,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
         ];
         return setcookie($name, $value, $options);
      } else {
         // Fallback for older PHP versions (less strict on samesite)
         return setcookie($name, $value, $expiry, $path, $domain, $secure, $httpOnly);
      }
   }

   /**
    * Set a cookie that lasts forever (5 years)
    * 
    * @param string $name
    * @param string $value
    * @param string|null $path
    * @param string|null $domain
    * @param bool|null $secure
    * @param bool|null $httpOnly
    * @return bool
    */
   public static function forever(
      string $name,
      string $value,
      ?string $path = null,
      ?string $domain = null,
      ?bool $secure = null,
      ?bool $httpOnly = null
   ): bool {
      return self::set($name, $value, 2628000, $path, $domain, $secure, $httpOnly);
   }

   /**
    * Get a cookie value
    * 
    * @param string $name
    * @param mixed $default
    * @return mixed
    */
   public static function get(string $name, mixed $default = null): mixed
   {
      return $_COOKIE[$name] ?? $default;
   }

   /**
    * Check if a cookie exists
    * 
    * @param string $name
    * @return bool
    */
   public static function has(string $name): bool
   {
      return isset($_COOKIE[$name]);
   }

   /**
    * Delete a cookie
    * 
    * @param string $name
    * @param string|null $path
    * @param string|null $domain
    * @return bool
    */
   public static function forget(string $name, ?string $path = null, ?string $domain = null): bool
   {
      // To delete, set expiration to the past
      return self::set($name, '', -2628000, $path, $domain);
   }
}
