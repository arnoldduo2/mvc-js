<?php

declare(strict_types=1);

namespace App\App\Core;

use PDO;
use Closure;

/**
 * Validator Class
 * 
 * Comprehensive form validation with built-in and custom rules
 */
class Validator
{
   private array $data = [];
   private array $rules = [];
   private array $errors = [];
   private array $validated = [];
   private array $customRules = [];
   private static ?array $config = null;

   /**
    * Create a new validator instance
    */
   public function __construct(array $data, array $rules)
   {
      $this->data = $data;
      $this->rules = $rules;
      $this->loadConfig();
   }

   /**
    * Load validation configuration
    */
   private function loadConfig(): void
   {
      if (self::$config === null) {
         $configFile = APP_CONFIG . '/validation.php';
         self::$config = file_exists($configFile) ? require $configFile : [];
      }
   }

   /**
    * Create validator instance (static factory)
    */
   public static function make(array $data, array $rules): self
   {
      $validator = new self($data, $rules);
      $validator->validate();
      return $validator;
   }

   /**
    * Perform validation
    */
   public function validate(): void
   {
      foreach ($this->rules as $field => $ruleString) {
         $rules = is_array($ruleString) ? $ruleString : explode('|', $ruleString);

         foreach ($rules as $rule) {
            $this->validateField($field, $rule);
         }
      }
   }

   /**
    * Validate a single field against a rule
    */
   private function validateField(string $field, string $rule): void
   {
      // Parse rule and parameters
      [$ruleName, $parameters] = $this->parseRule($rule);

      // Get field value
      $value = $this->data[$field] ?? null;

      // Skip validation if field is not required and empty
      if ($ruleName !== 'required' && empty($value) && $value !== '0') {
         return;
      }

      // Check for custom rule
      if (isset($this->customRules[$ruleName])) {
         $this->validateCustomRule($field, $value, $ruleName, $parameters);
         return;
      }

      // Validate using built-in rules
      $method = 'validate' . ucfirst($ruleName);
      if (method_exists($this, $method)) {
         $this->$method($field, $value, $parameters);
      }
   }

   /**
    * Parse rule string into name and parameters
    */
   private function parseRule(string $rule): array
   {
      if (strpos($rule, ':') === false) {
         return [$rule, []];
      }

      [$name, $params] = explode(':', $rule, 2);

      // Handle different parameter formats
      if (strpos($params, '=') !== false) {
         // Key=value format (e.g., min=8,upper,lower)
         $parameters = [];
         foreach (explode(',', $params) as $param) {
            if (strpos($param, '=') !== false) {
               [$key, $val] = explode('=', $param, 2);
               $parameters[$key] = $val;
            } else {
               $parameters[$param] = true;
            }
         }
         return [$name, $parameters];
      }

      // Simple comma-separated values
      return [$name, explode(',', $params)];
   }

    // ==================== VALIDATION RULES ====================

   /**
    * Required field validation
    */
   private function validateRequired(string $field, mixed $value, array $params): void
   {
      if (empty($value) && $value !== '0') {
         $this->addError($field, 'required');
      } else {
         $this->validated[$field] = $value;
      }
   }

   /**
    * Email validation
    */
   private function validateEmail(string $field, mixed $value, array $params): void
   {
      if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
         $this->addError($field, 'email');
      }
   }

   /**
    * Minimum length/value validation
    */
   private function validateMin(string $field, mixed $value, array $params): void
   {
      $min = (int) ($params[0] ?? 0);

      if (is_numeric($value)) {
         if ($value < $min) {
            $this->addError($field, 'min', ['min' => $min]);
         }
      } else {
         if (strlen((string) $value) < $min) {
            $this->addError($field, 'min', ['min' => $min]);
         }
      }
   }

   /**
    * Maximum length/value validation
    */
   private function validateMax(string $field, mixed $value, array $params): void
   {
      $max = (int) ($params[0] ?? 0);

      if (is_numeric($value)) {
         if ($value > $max) {
            $this->addError($field, 'max', ['max' => $max]);
         }
      } else {
         if (strlen((string) $value) > $max) {
            $this->addError($field, 'max', ['max' => $max]);
         }
      }
   }

   /**
    * Numeric validation
    */
   private function validateNumeric(string $field, mixed $value, array $params): void
   {
      if (!is_numeric($value)) {
         $this->addError($field, 'numeric');
      }
   }

   /**
    * Integer validation
    */
   private function validateInteger(string $field, mixed $value, array $params): void
   {
      if (!filter_var($value, FILTER_VALIDATE_INT) && $value !== 0) {
         $this->addError($field, 'integer');
      }
   }

   /**
    * Alphabetic characters only
    */
   private function validateAlpha(string $field, mixed $value, array $params): void
   {
      if (!preg_match('/^[a-zA-Z]+$/', (string) $value)) {
         $this->addError($field, 'alpha');
      }
   }

   /**
    * Alphanumeric characters only
    */
   private function validateAlphanumeric(string $field, mixed $value, array $params): void
   {
      if (!preg_match('/^[a-zA-Z0-9]+$/', (string) $value)) {
         $this->addError($field, 'alphanumeric');
      }
   }

   /**
    * URL validation
    */
   private function validateUrl(string $field, mixed $value, array $params): void
   {
      if (!filter_var($value, FILTER_VALIDATE_URL)) {
         $this->addError($field, 'url');
      }
   }

   /**
    * Confirmation field validation
    */
   private function validateConfirmed(string $field, mixed $value, array $params): void
   {
      $confirmField = $field . '_confirmation';
      $confirmValue = $this->data[$confirmField] ?? null;

      if ($value !== $confirmValue) {
         $this->addError($field, 'confirmed');
      }
   }

   /**
    * In array validation
    */
   private function validateIn(string $field, mixed $value, array $params): void
   {
      if (!in_array($value, $params)) {
         $this->addError($field, 'in');
      }
   }

   /**
    * Unique in database validation
    */
   private function validateUnique(string $field, mixed $value, array $params): void
   {
      $table = $params[0] ?? null;
      $column = $params[1] ?? $field;

      if (!$table) {
         return;
      }

      $db = Model::conn();
      $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = :value");
      $stmt->execute(['value' => $value]);

      if ($stmt->fetchColumn() > 0) {
         $this->addError($field, 'unique');
      }
   }

   /**
    * Exists in database validation
    */
   private function validateExists(string $field, mixed $value, array $params): void
   {
      $table = $params[0] ?? null;
      $column = $params[1] ?? $field;

      if (!$table) {
         return;
      }

      $db = Model::conn();
      $stmt = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = :value");
      $stmt->execute(['value' => $value]);

      if ($stmt->fetchColumn() === 0) {
         $this->addError($field, 'exists');
      }
   }

   /**
    * Regex pattern validation
    */
   private function validateRegex(string $field, mixed $value, array $params): void
   {
      $pattern = $params[0] ?? '';

      if (!preg_match($pattern, (string) $value)) {
         $this->addError($field, 'regex');
      }
   }

   /**
    * Password complexity validation
    */
   private function validatePassword(string $field, mixed $value, array $params): void
   {
      $config = self::$config['password'] ?? [];

      // If complexity is disabled globally
      if (!($config['enabled'] ?? true)) {
         return;
      }

      // Parse parameters (can override config)
      $minLength = isset($params['min']) ? (int) $params['min'] : ($config['min_length'] ?? 8);
      $requireUpper = isset($params['upper']) ? true : ($config['require_uppercase'] ?? false);
      $requireLower = isset($params['lower']) ? true : ($config['require_lowercase'] ?? false);
      $requireNumber = isset($params['number']) ? true : ($config['require_numbers'] ?? false);
      $requireSpecial = isset($params['special']) ? true : ($config['require_special'] ?? false);

      // Minimum length
      if (strlen((string) $value) < $minLength) {
         $this->addError($field, 'password_min', ['min' => $minLength]);
         return;
      }

      // Uppercase letter
      if ($requireUpper && !preg_match('/[A-Z]/', (string) $value)) {
         $this->addError($field, 'password_uppercase');
      }

      // Lowercase letter
      if ($requireLower && !preg_match('/[a-z]/', (string) $value)) {
         $this->addError($field, 'password_lowercase');
      }

      // Number
      if ($requireNumber && !preg_match('/[0-9]/', (string) $value)) {
         $this->addError($field, 'password_number');
      }

      // Special character
      if ($requireSpecial) {
         $specialChars = preg_quote($config['special_characters'] ?? '!@#$%^&*()_+-=[]{}|;:,.<>?', '/');
         if (!preg_match('/[' . $specialChars . ']/', (string) $value)) {
            $this->addError($field, 'password_special');
         }
      }
   }

    // ==================== CUSTOM RULES ====================

   /**
    * Add custom validation rule
    */
   public function addRule(string $name, Closure $callback): void
   {
      $this->customRules[$name] = $callback;
   }

   /**
    * Validate using custom rule
    */
   private function validateCustomRule(string $field, mixed $value, string $ruleName, array $parameters): void
   {
      $callback = $this->customRules[$ruleName];
      $result = $callback($value, $parameters, $this->data);

      if ($result !== true) {
         $message = is_string($result) ? $result : "The {$field} field is invalid.";
         $this->errors[$field][] = $message;
      }
   }

    // ==================== ERROR HANDLING ====================

   /**
    * Add validation error
    */
   private function addError(string $field, string $rule, array $replacements = []): void
   {
      $message = $this->getErrorMessage($field, $rule, $replacements);
      $this->errors[$field][] = $message;
   }

   /**
    * Get error message for rule
    */
   private function getErrorMessage(string $field, string $rule, array $replacements = []): string
   {
      $messages = self::$config['messages'] ?? [];
      $template = $messages[$rule] ?? "The {$field} field is invalid.";

      // Replace placeholders
      $replacements['field'] = ucfirst(str_replace('_', ' ', $field));

      foreach ($replacements as $key => $value) {
         $template = str_replace(':' . $key, (string) $value, $template);
      }

      return $template;
   }

    // ==================== RESULTS ====================

   /**
    * Check if validation failed
    */
   public function fails(): bool
   {
      return !empty($this->errors);
   }

   /**
    * Check if validation passed
    */
   public function passes(): bool
   {
      return empty($this->errors);
   }

   /**
    * Get all validation errors
    */
   public function errors(): array
   {
      return $this->errors;
   }

   /**
    * Get validated data
    */
   public function validated(): array
   {
      return $this->validated;
   }

   /**
    * Get first error for a field
    */
   public function first(string $field): ?string
   {
      return $this->errors[$field][0] ?? null;
   }

   /**
    * Check if field has error
    */
   public function hasError(string $field): bool
   {
      return isset($this->errors[$field]);
   }
}
