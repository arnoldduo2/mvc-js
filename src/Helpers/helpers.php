<?php

declare(strict_types=1);

// Helper functions are defined in the global namespace
// This allows them to be called from anywhere in the application
// without namespace qualification

use App\App\Core\Env;
use App\Helpers\StringHelper;
use App\Helpers\EncyptionHelper;

/**
 * Get an environment variable value
 * @param string $key The environment variable key
 * @param mixed $default The default value if not found
 * @return mixed
 */
function env(string $key, mixed $default = null): mixed
{
   return Env::get($key, $default);
}

/**
 * Convert string to snake_case
 * @param string $str
 * @return string
 */
function snakeCase(string $str): string
{
   return StringHelper::toSnakeCase($str);
}

/**
 * Convert string to camelCase
 * @param string $str
 * @return string
 */
function camelCase(string $str): string
{
   return StringHelper::toCamelCase($str);
}

/**
 * Convert string to kebab-case
 * @param string $str
 * @return string
 */
function kebabCase(string $str): string
{
   return StringHelper::toKebabCase($str);
}

/**
 * Convert string to PascalCase
 * @param string $str
 * @return string
 */
function pascalCase(string $str): string
{
   return StringHelper::toPascalCase($str);
}

/**
 * Hash a password using bcrypt
 * @param string $password
 * @return string
 */
function hashPassword(string $password): string
{
   static $encyptionHelper;
   if (!$encyptionHelper) {
      $encyptionHelper = new EncyptionHelper();
   }
   return $encyptionHelper->hash($password);
}

/**
 * Verify a password against a bcrypt hash
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword(string $password, string $hash): bool
{
   static $encyptionHelper;
   if (!$encyptionHelper) {
      $encyptionHelper = new EncyptionHelper();
   }
   return $encyptionHelper->verify($password, $hash);
}

//Message Helper

/**
 * Send an alert message
 * @param string $type The type of alert (success, error, warning, info)
 * @param string $message The message to display
 * @param bool $isHtml Whether to return HTML or plain text
 * @return string
 */
function sendAlert(string $type, string $message, bool $isHtml = true): string
{
   return MessageHelper::sendAlert($type, $message, $isHtml);
}

/**
 * Get exception message
 * @param mixed $e
 * @return string
 */
function __eMsg($e): string
{
   return MessageHelper::eMsg($e);
}

//Users Helper

/**
 * Get user data
 * @param string $key The key to get
 * @return mixed
 */
function __getUser(string $key): mixed
{
   return AppLogicHelpers::__getUser($key);
}

/**
 * Generate a URL with base path prefix
 * 
 * @param string $path Path (e.g., '/about', 'dashboard')
 * @return string Full URL with base path
 */
function url(string $path = ''): string
{
   return \App\App\Core\Router::url($path);
}

/**
 * Get or set cache value
 * 
 * @param string $key Cache key
 * @param mixed $value Value to cache (null to get)
 * @param int $ttl Time to live in seconds
 * @return mixed
 */
function cache(string $key, mixed $value = null, int $ttl = 3600): mixed
{
   if ($value === null) {
      return \App\App\Core\Cache::get($key);
   }

   \App\App\Core\Cache::put($key, $value, $ttl);
   return $value;
}

/**
 * Get from cache or execute callback and cache result
 * 
 * @param string $key Cache key
 * @param int $ttl Time to live in seconds
 * @param Closure $callback Callback to execute if cache miss
 * @return mixed
 */
function cache_remember(string $key, int $ttl, Closure $callback): mixed
{
   return \App\App\Core\Cache::remember($key, $ttl, $callback);
}

/**
 * Delete cached item
 * 
 * @param string $key Cache key
 * @return bool
 */
function cache_forget(string $key): bool
{
   return \App\App\Core\Cache::forget($key);
}

/**
 * Check if cache key exists
 * 
 * @param string $key Cache key
 * @return bool
 */
function cache_has(string $key): bool
{
   return \App\App\Core\Cache::has($key);
}

/**
 * Clear all cache
 * 
 * @return bool
 */
function cache_flush(): bool
{
   return \App\App\Core\Cache::flush();
}

// ==================== CSRF HELPERS ====================

/**
 * Get CSRF token
 * 
 * @return string
 */
function csrf_token(): string
{
   return \App\App\Core\CSRF::getToken();
}

/**
 * Generate CSRF token field
 * 
 * @return string
 */
function csrf_field(): string
{
   return \App\App\Core\CSRF::field();
}

/**
 * Verify CSRF token
 * 
 * @param array $data Request data
 * @return bool
 */
function csrf_verify(array $data): bool
{
   return \App\App\Core\CSRF::verify($data);
}

// ==================== VALIDATION HELPERS ====================

/**
 * Validate data against rules
 * 
 * @param array $data Data to validate
 * @param array $rules Validation rules
 * @return \App\App\Core\Validator
 */
function validate(array $data, array $rules): \App\App\Core\Validator
{
   return \App\App\Core\Validator::make($data, $rules);
}

/**
 * Get old input value
 * 
 * @param string $field Field name
 * @param mixed $default Default value
 * @return mixed
 */
function old(string $field, mixed $default = null): mixed
{
   return \App\App\Core\Form::old($field, $default);
}

/**
 * Get all validation errors
 * 
 * @return array
 */
function errors(): array
{
   return \App\App\Core\Form::errors();
}

/**
 * Get error for specific field
 * 
 * @param string $field Field name
 * @return string|null
 */
function error(string $field): ?string
{
   $errors = \App\App\Core\Form::errors();
   return $errors[$field][0] ?? null;
}

/**
 * Check if field has error
 * 
 * @param string $field Field name
 * @return bool
 */
function has_error(string $field): bool
{
   return \App\App\Core\Form::hasError($field);
}

/**
 * Redirect to URL
 * 
 * @param string $url URL to redirect to
 * @param int $code HTTP status code
 */
function redirect(string $url, int $code = 302): void
{
   header("Location: {$url}", true, $code);
   exit;
}
