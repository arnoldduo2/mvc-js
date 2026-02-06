<?php

declare(strict_types=1);

class Config
{
   private static $config = [];

   public function __construct()
   {
      self::load(APP_PATH . '/config/constants.php');
   }

   public static function get(string $key, $default = null)
   {
      return self::$config[$key] ?? $default;
   }

   public static function set(string $key, $value): void
   {
      self::$config[$key] = $value;
   }

   public static function has(string $key): bool
   {
      return isset(self::$config[$key]);
   }

   public static function all(): array
   {
      return self::$config;
   }

   public static function load(string $path): void
   {
      $config = require $path;
      foreach ($config as $key => $value) {
         self::set($key, $value);
      }
   }
}