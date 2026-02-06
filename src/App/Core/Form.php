<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Form Helper Class
 * 
 * Build HTML forms with automatic CSRF protection and error handling
 */
class Form
{
   private static array $errors = [];
   private static array $old = [];

   /**
    * Initialize form helper (load errors and old input from session)
    */
   public static function init(): void
   {
      if (session_status() === PHP_SESSION_NONE) {
         session_start();
      }

      self::$errors = $_SESSION['errors'] ?? [];
      self::$old = $_SESSION['old'] ?? [];

      // Clear after reading
      unset($_SESSION['errors'], $_SESSION['old']);
   }

   /**
    * Open a form
    */
   public static function open(string $action, string $method = 'POST', array $attributes = []): string
   {
      self::init();

      $attrs = self::buildAttributes(array_merge([
         'action' => $action,
         'method' => strtoupper($method),
      ], $attributes));

      $html = "<form {$attrs}>";

      // Add CSRF token for POST requests
      if (strtoupper($method) === 'POST') {
         $html .= self::csrf();
      }

      return $html;
   }

   /**
    * Close a form
    */
   public static function close(): string
   {
      return '</form>';
   }

   /**
    * Generate CSRF token field
    */
   public static function csrf(): string
   {
      return CSRF::field();
   }

   /**
    * Text input field
    */
   public static function input(string $name, mixed $value = null, array $attributes = []): string
   {
      $value = $value ?? self::old($name);

      $attrs = self::buildAttributes(array_merge([
         'type' => 'text',
         'name' => $name,
         'id' => $name,
         'value' => $value,
      ], $attributes));

      return "<input {$attrs}>";
   }

   /**
    * Email input field
    */
   public static function email(string $name, mixed $value = null, array $attributes = []): string
   {
      return self::input($name, $value, array_merge(['type' => 'email'], $attributes));
   }

   /**
    * Password input field
    */
   public static function password(string $name, array $attributes = []): string
   {
      $attrs = self::buildAttributes(array_merge([
         'type' => 'password',
         'name' => $name,
         'id' => $name,
      ], $attributes));

      return "<input {$attrs}>";
   }

   /**
    * Number input field
    */
   public static function number(string $name, mixed $value = null, array $attributes = []): string
   {
      return self::input($name, $value, array_merge(['type' => 'number'], $attributes));
   }

   /**
    * Textarea field
    */
   public static function textarea(string $name, mixed $value = null, array $attributes = []): string
   {
      $value = htmlspecialchars($value ?? self::old($name) ?? '');

      $attrs = self::buildAttributes(array_merge([
         'name' => $name,
         'id' => $name,
      ], $attributes));

      return "<textarea {$attrs}>{$value}</textarea>";
   }

   /**
    * Select dropdown
    */
   public static function select(string $name, array $options, mixed $selected = null, array $attributes = []): string
   {
      $selected = $selected ?? self::old($name);

      $attrs = self::buildAttributes(array_merge([
         'name' => $name,
         'id' => $name,
      ], $attributes));

      $html = "<select {$attrs}>";

      foreach ($options as $value => $label) {
         $isSelected = $value == $selected ? ' selected' : '';
         $html .= "<option value=\"" . htmlspecialchars((string) $value) . "\"{$isSelected}>" . htmlspecialchars($label) . "</option>";
      }

      $html .= "</select>";

      return $html;
   }

   /**
    * Checkbox input
    */
   public static function checkbox(string $name, mixed $value = '1', bool $checked = false, array $attributes = []): string
   {
      $oldValue = self::old($name);
      $isChecked = $oldValue !== null ? ($oldValue == $value) : $checked;

      $attrs = self::buildAttributes(array_merge([
         'type' => 'checkbox',
         'name' => $name,
         'id' => $name,
         'value' => $value,
      ], $attributes));

      if ($isChecked) {
         $attrs .= ' checked';
      }

      return "<input {$attrs}>";
   }

   /**
    * Radio button
    */
   public static function radio(string $name, mixed $value, bool $checked = false, array $attributes = []): string
   {
      $oldValue = self::old($name);
      $isChecked = $oldValue !== null ? ($oldValue == $value) : $checked;

      $attrs = self::buildAttributes(array_merge([
         'type' => 'radio',
         'name' => $name,
         'value' => $value,
      ], $attributes));

      if ($isChecked) {
         $attrs .= ' checked';
      }

      return "<input {$attrs}>";
   }

   /**
    * Submit button
    */
   public static function submit(string $text = 'Submit', array $attributes = []): string
   {
      $attrs = self::buildAttributes(array_merge([
         'type' => 'submit',
      ], $attributes));

      return "<button {$attrs}>" . htmlspecialchars($text) . "</button>";
   }

   /**
    * Button
    */
   public static function button(string $text, array $attributes = []): string
   {
      $attrs = self::buildAttributes(array_merge([
         'type' => 'button',
      ], $attributes));

      return "<button {$attrs}>" . htmlspecialchars($text) . "</button>";
   }

   /**
    * Display error for a field
    */
   public static function error(string $field): string
   {
      if (!self::hasError($field)) {
         return '';
      }

      $error = self::$errors[$field][0] ?? '';
      return "<span class=\"error-message\">" . htmlspecialchars($error) . "</span>";
   }

   /**
    * Get old input value
    */
   public static function old(string $field, mixed $default = null): mixed
   {
      return self::$old[$field] ?? $default;
   }

   /**
    * Check if field has error
    */
   public static function hasError(string $field): bool
   {
      return isset(self::$errors[$field]);
   }

   /**
    * Get all errors
    */
   public static function errors(): array
   {
      return self::$errors;
   }

   /**
    * Build HTML attributes string
    */
   private static function buildAttributes(array $attributes): string
   {
      $html = [];

      foreach ($attributes as $key => $value) {
         if (is_bool($value)) {
            if ($value) {
               $html[] = $key;
            }
         } else {
            $html[] = $key . '="' . htmlspecialchars((string) $value) . '"';
         }
      }

      return implode(' ', $html);
   }
}
