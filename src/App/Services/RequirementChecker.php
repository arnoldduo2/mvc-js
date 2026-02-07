<?php

declare(strict_types=1);

namespace App\App\Services;

class RequirementChecker
{
   private array $config;
   private array $results = [];

   public function __construct()
   {
      $this->config = require APP_CONFIG . '/requirements.php';
   }

   /**
    * Run all checks
    * 
    * @return array
    */
   public function check(): array
   {
      $this->checkPhp();
      $this->checkExtensions();
      $this->checkFunctions();
      $this->checkFunctions();
      $this->checkEnv();
      // $this->checkApache(); // Optional/Context dependent

      return $this->results;
   }

   /**
    * Check PHP version
    */
   public function checkPhp(): void
   {
      $minVersion = $this->config['php'];
      $currentVersion = PHP_VERSION;
      $pass = version_compare($currentVersion, $minVersion, '>=');

      $this->results['php'] = [
         'type' => 'Core',
         'name' => 'PHP Version',
         'required' => $minVersion,
         'current' => $currentVersion,
         'pass' => $pass,
         'message' => $pass ? "PHP {$currentVersion} is installed." : "PHP {$minVersion} or higher is required.",
      ];
   }

   /**
    * Check extensions
    */
   public function checkExtensions(): void
   {
      foreach ($this->config['extensions'] as $extension) {
         $pass = extension_loaded($extension);

         $this->results['extensions'][$extension] = [
            'type' => 'Extension',
            'name' => $extension,
            'pass' => $pass,
            'message' => $pass ? "Extension '{$extension}' is loaded." : "Extension '{$extension}' is missing.",
         ];
      }
   }

   /**
    * Check functions
    */
   public function checkFunctions(): void
   {
      foreach ($this->config['functions'] as $function) {
         $pass = function_exists($function);

         // Further check if it's disabled via php.ini
         if ($pass) {
            $disabled = explode(',', ini_get('disable_functions'));
            $pass = !in_array($function, array_map('trim', $disabled));
         }

         $this->results['functions'][$function] = [
            'type' => 'Function',
            'name' => $function,
            'pass' => $pass,
            'message' => $pass ? "Function '{$function}' is enabled." : "Function '{$function}' is disabled or missing.",
         ];
      }
   }

   /**
    * Check environment variables
    */
   public function checkEnv(): void
   {
      if (!isset($this->config['env'])) {
         return;
      }

      foreach ($this->config['env'] as $envVar) {
         // Check if env var is set and not empty (allow 0)
         $value = env($envVar);
         $pass = $value !== null && $value !== '';

         $this->results['env'][$envVar] = [
            'type' => 'Environment',
            'name' => $envVar,
            'pass' => $pass,
            'message' => $pass ? "Variable '{$envVar}' is set." : "Variable '{$envVar}' is missing in .env file.",
         ];
      }
   }

   /**
    * Check Apache modules (if running under Apache)
    */
   public function checkApache(): void
   {
      if (function_exists('apache_get_modules')) {
         $modules = apache_get_modules();
         foreach ($this->config['apache'] as $module) {
            $pass = in_array($module, $modules);
            $this->results['apache'][$module] = [
               'type' => 'Apache Module',
               'name' => $module,
               'pass' => $pass,
               'message' => $pass ? "Module '{$module}' is enabled." : "Module '{$module}' is missing.",
            ];
         }
      }
   }

   /**
    * Determine if overall check passed
    */
   public function passes(): bool
   {
      if (empty($this->results)) {
         $this->check();
      }

      foreach ($this->results as $category) {
         if (isset($category['pass'])) {
            if (!$category['pass']) return false;
         } else {
            foreach ($category as $item) {
               if (!$item['pass']) return false;
            }
         }
      }
      return true;
   }
}
