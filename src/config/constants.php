<?php

declare(strict_types=1);

/**
 * Modern Application Constants
 * 
 * Using const keyword for better performance and modern PHP practices.
 * Constants are extracted to global namespace for easy access.
 */

// Calculate paths once
$appPath = dirname(__DIR__);
$appRoot = dirname(__DIR__, 2);

// Modern constant definitions using const-like approach
// These will be extracted to global constants
$constants = [
   // Application Information
   'APP_NAME' => 'MVC-JS',
   'APP_ENV' => 'development',
   'APP_DEBUG' => true,
   'APP_URL' => 'http://localhost/mvc-js',

   // Directory Paths
   'APP_PATH' => $appPath,
   'APP_ROOT' => $appRoot,
   'APP_STORAGE' => $appPath . '/storage',
   'APP_CACHE' => $appPath . '/storage/cache',
   'APP_LOG' => $appPath . '/storage/logs',
   'APP_TEMP' => $appPath . '/storage/temp',
   'APP_UPLOAD' => $appPath . '/storage/uploads',
   'APP_PUBLIC' => $appRoot . '/public',
   'APP_VENDOR' => $appRoot . '/vendor',
   'APP_CONFIG' => $appPath . '/config',

   // Database Configuration
   'DB_ENG' => env('DB_ENG', 'mysql'),
   'DB_HOST' => env('DB_HOST', '127.0.0.1'),
   'DB_PORT' => env('DB_PORT', '3306'),
   'DB_NAME' => env('DB_NAME', 'mvc_js'),
   'DB_USER' => env('DB_USER', 'root'),
   'DB_PASS' => env('DB_PASS', ''),

   // Session Configuration
   'SESSION_LIFETIME' => (int) env('SESSION_LIFETIME', 120),
   'SESSION_DRIVER' => env('SESSION_DRIVER', 'file'),

   // Security
   'BCRYPT_ROUNDS' => (int) env('BCRYPT_ROUNDS', 12),

   // Logging
   'LOG_LEVEL' => env('LOG_LEVEL', 'debug'),
   'LOG_CHANNEL' => env('LOG_CHANNEL', 'stack'),
];

// Extract constants to global namespace
foreach ($constants as $name => $value) {
   if (!defined($name)) {
      define($name, $value);
   }
}

// Clean up
unset($constants, $appPath, $appRoot, $name, $value);