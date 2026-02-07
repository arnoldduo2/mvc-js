<?php

declare(strict_types=1);

// Helper functions are defined in the global namespace
// This allows them to be called from anywhere in the application
// without namespace qualification

use App\App\Core\Env;
use App\App\Core\View;
use App\Helpers\AppLogicHelpers;
use App\Helpers\StringHelper;
use App\Helpers\EncyptionHelper;
use App\Helpers\MessageHelper;


//Dev Helpers
require_once __DIR__ . '/dev.php';


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

// ==================== COMPONENT HELPERS ====================

/**
 * Render a component
 * 
 * @param string $name Component name
 * @param array $data Component data
 * @return string
 */
function component(string $name, array $data = []): string
{
   return \App\App\Core\View::component($name, $data);
}

/**
 * Open a component wrapping context
 * 
 * @param string $name Component name
 * @param array $data Component data
 * @return void
 */
function component_open(string $name, array $data = []): void
{
   \App\App\Core\Component::open($name, $data);
}

/**
 * Close a component wrapping context
 * 
 * @return string
 */
function component_close(): string
{
   return \App\App\Core\Component::close();
}

// ==================== FORM HELPERS ====================

/**
 * Open a form
 * 
 * @param string $action Form action URL
 * @param string $method HTTP method
 * @param array $attributes HTML attributes
 * @return string
 */
function form_open(string $action, string $method = 'POST', array $attributes = []): string
{
   return \App\App\Core\Form::open($action, $method, $attributes);
}

/**
 * Close a form
 * 
 * @return string
 */
function form_close(): string
{
   return \App\App\Core\Form::close();
}

/**
 * Create a text input
 * 
 * @param string $name Field name
 * @param mixed $value Field value
 * @param array $attributes HTML attributes
 * @return string
 */
function form_input(string $name, mixed $value = null, array $attributes = []): string
{
   return \App\App\Core\Form::input($name, $value, $attributes);
}

/**
 * Create an email input
 * 
 * @param string $name Field name
 * @param mixed $value Field value
 * @param array $attributes HTML attributes
 * @return string
 */
function form_email(string $name, mixed $value = null, array $attributes = []): string
{
   return \App\App\Core\Form::email($name, $value, $attributes);
}

/**
 * Create a password input
 * 
 * @param string $name Field name
 * @param array $attributes HTML attributes
 * @return string
 */
function form_password(string $name, array $attributes = []): string
{
   return \App\App\Core\Form::password($name, $attributes);
}

/**
 * Create a number input
 * 
 * @param string $name Field name
 * @param mixed $value Field value
 * @param array $attributes HTML attributes
 * @return string
 */
function form_number(string $name, mixed $value = null, array $attributes = []): string
{
   return \App\App\Core\Form::number($name, $value, $attributes);
}

/**
 * Create a textarea
 * 
 * @param string $name Field name
 * @param mixed $value Field value
 * @param array $attributes HTML attributes
 * @return string
 */
function form_textarea(string $name, mixed $value = null, array $attributes = []): string
{
   return \App\App\Core\Form::textarea($name, $value, $attributes);
}

/**
 * Create a select dropdown
 * 
 * @param string $name Field name
 * @param array $options Options array [value => label]
 * @param mixed $selected Selected value
 * @param array $attributes HTML attributes
 * @return string
 */
function form_select(string $name, array $options, mixed $selected = null, array $attributes = []): string
{
   return \App\App\Core\Form::select($name, $options, $selected, $attributes);
}

/**
 * Create a checkbox
 * 
 * @param string $name Field name
 * @param mixed $value Checkbox value
 * @param bool $checked Is checked?
 * @param array $attributes HTML attributes
 * @return string
 */
function form_checkbox(string $name, mixed $value = '1', bool $checked = false, array $attributes = []): string
{
   return \App\App\Core\Form::checkbox($name, $value, $checked, $attributes);
}

/**
 * Create a radio button
 * 
 * @param string $name Field name
 * @param mixed $value Radio value
 * @param bool $checked Is checked?
 * @param array $attributes HTML attributes
 * @return string
 */
function form_radio(string $name, mixed $value, bool $checked = false, array $attributes = []): string
{
   return \App\App\Core\Form::radio($name, $value, $checked, $attributes);
}

/**
 * Create a submit button
 * 
 * @param string $text Button text
 * @param array $attributes HTML attributes
 * @return string
 */
function form_submit(string $text = 'Submit', array $attributes = []): string
{
   return \App\App\Core\Form::submit($text, $attributes);
}

/**
 * Create a button
 * 
 * @param string $text Button text
 * @param array $attributes HTML attributes
 * @return string
 */
function form_button(string $text, array $attributes = []): string
{
   return \App\App\Core\Form::button($text, $attributes);
}

/**
 * Display validation error for a field
 * 
 * @param string $field Field name
 * @return string HTML error string
 */
function form_error(string $field): string
{
   return \App\App\Core\Form::error($field);
}

/**
 * Render a form component (like input, select, etc)
 * 
 * @param string $component Component name (e.g. 'form.input')
 * @param array $data Component data
 * @return string
 */
function form_component(string $component, array $data = []): string
{
   return \App\App\Core\View::component($component, $data);
}

function __requiredAttr($isRequired = null, $isDisabled = null, $isReadonly = null)
{
   $attr = $isRequired ? ' required' : '';
   $attr .=  $isDisabled ? ' disabled' : '';
   $attr .=  $isReadonly ? ' readonly' : '';
   return $attr;
}

function __attr($attr = [])
{
   $html = [];
   foreach ($attr as $key => $val) {
      if (is_bool($val)) {
         if ($val) $html[] = $key;
      } else {
         $html[] = $key . '="' . htmlspecialchars((string) $val) . '"';
      }
   }
   return implode(' ', $html);
}

/**
 * Get session instance or value
 * 
 * @param string|null $key
 * @param mixed $default
 * @return mixed|\App\App\Core\Session
 */
function session(?string $key = null, mixed $default = null): mixed
{
   if ($key === null) {
      return new \App\App\Core\Session();
   }

   return \App\App\Core\Session::get($key, $default);
}

/**
 * Get cookie instance or value
 * 
 * @param string|null $key
 * @param mixed $default
 * @return mixed|\App\App\Core\Cookie
 */
function cookie(?string $key = null, mixed $default = null): mixed
{
   if ($key === null) {
      return new \App\App\Core\Cookie();
   }

   return \App\App\Core\Cookie::get($key, $default);
}
function isAjaxRequest(): bool
{
   return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
      && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function addAssets($assets = [], $type = 'css')
{
   $html = '';
   foreach ($assets as $asset) {
      if ($type == 'css') {
         $html .= '<link rel="stylesheet" href="' . url($asset) . '">';
      } else if ($type == 'js') {
         $html .= '<script src="' . url($asset) . '"></script>';
      }
   }
   return $html;
}

/**
 * Summary of useSpa
 * @return string
 */
function useSpa(): string
{
   return View::coreScripts();
}
/**
 * Loads Includes Partials/ Layouts Render Template Views.
 * @param string $name Name of the view file or folder, separated with a . or /
 * @param array $data Data Array to be loaded with the view
 * @return mixed Returns the view Object that renders the file.
 */
function __includes(string $name, array $data = []): mixed
{
   return View::partials($name, $data);
}

/**
 * Render the Views Template.
 * @param string $view Name of the view or the folder with the view resides. folders can be seperated with a dot or a /
 * @param array $data Data Array that needs to be passed to the view.
 * @return mixed
 */
function view(string $view, array $data = []): mixed
{

   if (isAjaxRequest()) // Return JSON for SPA
      View::page($view, $data);
   else {
      // Render full HTML page
      $content = View::render($view, $data);
      echo View::mainLayout('app', $content, $data);
   }
   return true;
}
