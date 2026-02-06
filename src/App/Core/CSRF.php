<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * CSRF Protection Class
 * 
 * Generate and validate CSRF tokens for form security
 */
class CSRF
{
   private static string $tokenName = '_token';
   private static int $tokenExpire = 7200; // 2 hours

   /**
    * Generate a new CSRF token
    * 
    * @return string
    */
   public static function generateToken(): string
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      $token = bin2hex(random_bytes(32));
      $_SESSION[self::$tokenName] = $token;
      $_SESSION[self::$tokenName . '_time'] = time();

      return $token;
   }

   /**
    * Get current CSRF token (generate if not exists)
    * 
    * @return string
    */
   public static function getToken(): string
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      if (!isset($_SESSION[self::$tokenName]) || self::isExpired()) {
         return self::generateToken();
      }

      return $_SESSION[self::$tokenName];
   }

   /**
    * Check if token is expired
    * 
    * @return bool
    */
   private static function isExpired(): bool
   {
      if (!isset($_SESSION[self::$tokenName . '_time'])) {
         return true;
      }

      return (time() - $_SESSION[self::$tokenName . '_time']) > self::$tokenExpire;
   }

   /**
    * Validate CSRF token
    * 
    * @param string|null $token Token to validate
    * @return bool
    */
   public static function validateToken(?string $token): bool
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      if ($token === null || !isset($_SESSION[self::$tokenName])) {
         return false;
      }

      if (self::isExpired()) {
         return false;
      }

      return hash_equals($_SESSION[self::$tokenName], $token);
   }

   /**
    * Generate hidden input field with CSRF token
    * 
    * @return string
    */
   public static function field(): string
   {
      $token = self::getToken();
      return '<input type="hidden" name="' . self::$tokenName . '" value="' . htmlspecialchars($token) . '">';
   }

   /**
    * Verify request has valid CSRF token
    * 
    * @param array $data Request data (usually $_POST)
    * @return bool
    */
   public static function verify(array $data): bool
   {
      $token = $data[self::$tokenName] ?? null;
      return self::validateToken($token);
   }

   /**
    * Set token name
    * 
    * @param string $name
    */
   public static function setTokenName(string $name): void
   {
      self::$tokenName = $name;
   }

   /**
    * Set token expiration time
    * 
    * @param int $seconds
    */
   public static function setExpire(int $seconds): void
   {
      self::$tokenExpire = $seconds;
   }

   /**
    * Get token name
    * 
    * @return string
    */
   public static function getTokenName(): string
   {
      return self::$tokenName;
   }
}
