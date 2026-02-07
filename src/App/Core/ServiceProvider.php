<?php

declare(strict_types=1);

namespace App\App\Core;

/**
 * Service Provider
 * 
 * Abstract class for service providers.
 */
abstract class ServiceProvider
{
   /**
    * @var Application
    */
   protected Application $app;

   /**
    * Create a new service provider instance
    * 
    * @param Application $app
    */
   public function __construct(Application $app)
   {
      $this->app = $app;
   }

   /**
    * Register services
    * 
    * @return void
    */
   abstract public function register(): void;

   /**
    * Boot services
    * 
    * @return void
    */
   public function boot(): void
   {
      // Optional boot method
   }
}
