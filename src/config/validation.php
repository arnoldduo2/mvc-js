<?php

declare(strict_types=1);

/**
 * Validation Configuration
 */

return [
   /*
    |--------------------------------------------------------------------------
    | Password Complexity Rules
    |--------------------------------------------------------------------------
    */
   'password' => [
      'enabled' => env('PASSWORD_COMPLEXITY_ENABLED', true),
      'min_length' => (int) env('PASSWORD_MIN_LENGTH', 8),
      'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
      'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
      'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
      'require_special' => env('PASSWORD_REQUIRE_SPECIAL', true),
      'special_characters' => '!@#$%^&*()_+-=[]{}|;:,.<>?',
   ],

   /*
    |--------------------------------------------------------------------------
    | CSRF Protection
    |--------------------------------------------------------------------------
    */
   'csrf' => [
      'enabled' => env('CSRF_ENABLED', true),
      'token_name' => '_token',
      'expire' => 7200, // 2 hours in seconds
   ],

   /*
    |--------------------------------------------------------------------------
    | Validation Error Messages
    |--------------------------------------------------------------------------
    */
   'messages' => [
      'required' => 'The :field field is required.',
      'email' => 'The :field must be a valid email address.',
      'min' => 'The :field must be at least :min characters.',
      'max' => 'The :field must not exceed :max characters.',
      'numeric' => 'The :field must be a number.',
      'integer' => 'The :field must be an integer.',
      'alpha' => 'The :field may only contain letters.',
      'alphanumeric' => 'The :field may only contain letters and numbers.',
      'url' => 'The :field must be a valid URL.',
      'confirmed' => 'The :field confirmation does not match.',
      'in' => 'The selected :field is invalid.',
      'unique' => 'The :field has already been taken.',
      'exists' => 'The selected :field is invalid.',
      'regex' => 'The :field format is invalid.',
      'password' => 'The :field does not meet complexity requirements.',
      'password_min' => 'The password must be at least :min characters.',
      'password_uppercase' => 'The password must contain at least one uppercase letter.',
      'password_lowercase' => 'The password must contain at least one lowercase letter.',
      'password_number' => 'The password must contain at least one number.',
      'password_special' => 'The password must contain at least one special character.',
   ],
];
