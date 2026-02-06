<?php

declare(strict_types=1);

namespace App\App\Core\Middleware;

/**
 * Middleware Interface
 * 
 * Base interface for all middleware classes.
 */
interface MiddlewareInterface
{
   /**
    * Handle the request
    * 
    * @param array $request Request data
    * @param callable $next Next middleware in the pipeline
    * @return mixed
    */
   public function handle(array $request, callable $next): mixed;
}
