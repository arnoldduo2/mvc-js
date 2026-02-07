<?php

declare(strict_types=1);

return [
   /*
    |--------------------------------------------------------------------------
    | PHP Version Requirement
    |--------------------------------------------------------------------------
    |
    | The minimum PHP version required for the application to run.
    |
    */
   'php' => '8.0.0',

   /*
    |--------------------------------------------------------------------------
    | Required PHP Extensions
    |--------------------------------------------------------------------------
    |
    | A list of PHP extensions that must be enabled on the server.
    |
    */
   'extensions' => [
      'bcmath',
      'ctype',
      'curl',
      'dom',
      'fileinfo',
      'filter',
      'hash',
      'json',
      'mbstring',
      'openssl',
      'pcre',
      'pdo',
      'session',
      'tokenizer',
      'xml',
   ],

   /*
    |--------------------------------------------------------------------------
    | Required PHP Functions
    |--------------------------------------------------------------------------
    |
    | A list of PHP functions that must be enabled (not disabled in php.ini).
    |
    */
   'functions' => [
      'symlink',
      'scandir',
      'exec',
      'shell_exec',
   ],

   /*
    |--------------------------------------------------------------------------
    | Apache Modules (Optional)
    |--------------------------------------------------------------------------
    |
    | A list of Apache modules that should be enabled.
    | Note: This check might not work on all server configurations (e.g. Nginx).
    |
    */
   'apache' => [
      'mod_rewrite',
   ],

   /*
    |--------------------------------------------------------------------------
    | Required Environment Variables
    |--------------------------------------------------------------------------
    |
    | A list of environment variables that must be present in the .env file.
    |
    */
   'env' => [
      'DB_ENGINE',
      'DB_HOST',
      'DB_NAME',
      'DB_USERNAME',
      'DB_PASSWORD',
   ],
];
