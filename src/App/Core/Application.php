<?php

declare(strict_types=1);

namespace App\App\Core;

use App\App\Core\Router;
use Anode\ErrorHandler\ErrorHandler;

/**
 * Application Class
 * 
 * Main application class that handles bootstrapping and request routing.
 * Implements singleton pattern to ensure only one instance exists.
 */
class Application
{
   private static ?Application $instance = null;
   private static bool $booted = false;

   /**
    * @var Container
    */
   private Container $container;

   /**
    * @var array Registered service providers
    */
   private array $providers = [];

   /**
    * Private constructor to prevent direct instantiation
    */
   private function __construct()
   {
      $this->container = new Container();

      // Bind the application instance to the container
      $this->container->singleton(Application::class, function () {
         return $this;
      });
   }

   /**
    * Get the singleton instance of the application
    * 
    * @return Application
    */
   public static function getInstance(): Application
   {
      if (self::$instance === null) {
         self::$instance = new self();
      }

      return self::$instance;
   }

   /**
    * Boot the application
    * 
    * Initializes all necessary system files and components.
    * This method is idempotent - calling it multiple times has no additional effect.
    * 
    * @return Application
    */
   public function boot(): Application
   {
      if (self::$booted) {
         return $this;
      }

      // Load system files
      $this->loadSystemFiles();

      // Initialize error handling
      $this->initializeErrorHandling();

      // Boot service providers
      $this->bootProviders();

      // Load routes
      $this->loadRoutes();

      // Mark as booted
      self::$booted = true;

      return $this;
   }

   /**
    * Register a service provider
    * 
    * @param string $providerClass
    * @return void
    */
   public function register(string $providerClass): void
   {
      $provider = new $providerClass($this);
      $provider->register();
      $this->providers[] = $provider;
   }

   /**
    * Boot all registered service providers
    */
   private function bootProviders(): void
   {
      foreach ($this->providers as $provider) {
         $provider->boot();
      }
   }

   /**
    * Resolve a class from the container
    * 
    * @param string $abstract
    * @return mixed
    */
   public function make(string $abstract): mixed
   {
      return $this->container->make($abstract);
   }

   /**
    * Bind a class to the container
    * 
    * @param string $abstract
    * @param mixed $concrete
    * @return void
    */
   public function bind(string $abstract, mixed $concrete = null): void
   {
      $this->container->bind($abstract, $concrete);
   }

   /**
    * Bind a singleton to the container
    * 
    * @param string $abstract
    * @param mixed $concrete
    * @return void
    */
   public function singleton(string $abstract, mixed $concrete = null): void
   {
      $this->container->singleton($abstract, $concrete);
   }

   /**
    * Check if the application has been booted
    * 
    * @return bool
    */
   public static function isBooted(): bool
   {
      return self::$booted;
   }

   /**
    * Load necessary system files
    * 
    * @return void
    */
   private function loadSystemFiles(): void
   {
      // Load configuration files if they exist
      $configFiles = [
         APP_CONFIG . '/app.php',
         APP_CONFIG . '/database.php',
         APP_CONFIG . '/session.php',
         APP_CONFIG . '/providers.php', // Add providers config
      ];

      foreach ($configFiles as $file) {
         if (file_exists($file)) {
            $result = require_once $file;

            // If it's the providers config, register them
            if ($file === APP_CONFIG . '/providers.php' && is_array($result)) {
               foreach ($result as $providerClass) {
                  $this->register($providerClass);
               }
            }
         }
      }
   }

   /**
    * Initialize error handling using Anode Error Handler
    * 
    * @return void
    */
   private function initializeErrorHandling(): void
   {
      // Initialize Anode Error Handler
      new ErrorHandler([
         'app_name' => APP_NAME,
         'app_enviroment' => APP_ENV,
         'app_debug' => APP_DEBUG,
         'base_url' => env('APP_URL', '/'),
         'error_reporting_level' => E_ALL,
         'display_errors' => APP_DEBUG,
         'log_errors' => true,
         'log_directory' => APP_STORAGE . '/logs/',
         'dev_logs' => APP_DEBUG,
         'dev_logs_directory' => APP_STORAGE . '/logs/dev/',
         'email_logging' => false,
         'error_view' => null, // Uses default error view
      ]);
   }

   /**
    * Load application routes
    * 
    * @return void
    */
   private function loadRoutes(): void
   {
      // Load web routes
      $webRoutes = APP_PATH . '/routes/web.php';
      if (file_exists($webRoutes)) {
         require_once $webRoutes;
      }

      // Load API routes
      $apiRoutes = APP_PATH . '/routes/api.php';
      if (file_exists($apiRoutes)) {
         require_once $apiRoutes;
      }
   }

   /**
    * Run the application
    * 
    * Handles the incoming request and dispatches it to the router.
    * 
    * @return void
    */
   public function run(): void
   {
      // Ensure application is booted
      if (!self::$booted) {
         $this->boot();
      }

      try {
         // Use the modern router's dispatch method
         Router::dispatch();
      } catch (\Exception $e) {
         // Error handler will catch this
         throw $e;
      }
   }

   /**
    * Prevent cloning of the instance
    */
   private function __clone() {}

   /**
    * Prevent unserializing of the instance
    */
   public function __wakeup()
   {
      throw new \Exception("Cannot unserialize singleton");
   }
}
