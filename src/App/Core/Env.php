<?php

declare(strict_types=1);

namespace App\App\Core;

use InvalidArgumentException;

class Env
{

   /**
    * Get an environment variable.
    * @param string $key The environment variable key.
    * @param mixed $default The default value to return if the environment variable is not set.
    * @return mixed The environment variable value or the default value.
    */
   public static function get(string $key, mixed $default = null): mixed
   {
      $path = dirname(__FILE__, 4) . DIRECTORY_SEPARATOR . '.env';
      $file = file_get_contents($path);
      $key = strtoupper($key);
      $env = $default;

      // Explode the file content by new lines
      $variables = explode("\n", $file);

      $envArray = [];
      foreach ($variables as $variable) {
         // Skip empty lines
         if (empty(trim($variable))) {
            continue;
         }
         // Explode each line by the equals sign
         $item = explode('=', $variable, 2); // Limit to 2 parts to handle values with =

         // Ensure we have both key and value
         if (count($item) == 2) {
            $envArray[] = [trim($item[0]), trim($item[1])];
         }
      }

      foreach ($envArray as $item) {
         if ($key == $item[0]) {
            $env = $item[1];
            $env = match (strtolower($env)) {
               'true' => true,
               'false' => false,
               'null' => null,
               default => $env,
            };
            break;
         }
      }

      return $env;
   }

   /**
    * Set an environment variable.
    * @param string $key The environment variable key.
    * @param string $value The environment variable value.
    * @return void
    */
   public static function set(string $key, string $value)
   {
      if (str_contains($key, '='))
         throw new InvalidArgumentException('Invalid environment variable key. Key cannot contain an equals sign.');
      $key = strtoupper($key);
      $path = dirname(__FILE__, 3) . DIRECTORY_SEPARATOR . '.env';
      $file = file_get_contents($path);

      $variables = explode("\n", $file);
      $envArray = [];
      $found = false;
      foreach ($variables as $variable) {
         if (empty(trim($variable))) {
            $envArray[] = $variable;
            continue;
         }
         $item = explode('=', $variable, 2);
         if (count($item) == 2) {
            $envKey = trim($item[0]);
            if ($envKey == $key) {
               $envArray[] = "$key=$value";
               $found = true;
            } else {
               $envArray[] = $variable;
            }
         } else {
            $envArray[] = $variable;
         }
      }
      if (!$found) {
         $envArray[] = "$key=$value";
      }
      $newEnv = implode("\n", $envArray);
      file_put_contents($path, $newEnv);
   }
}