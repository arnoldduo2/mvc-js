<?php

declare(strict_types=1);

namespace App\App\Providers;

use App\App\Core\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   /**
    * Register any application services.
    */
   public function register(): void
   {
      $this->app->bind(\App\App\Services\TestServiceContract::class, \App\App\Services\TestService::class);
   }

   /**
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      //
   }
}
