<?php

declare(strict_types=1);

namespace App\App\Core;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use Exception;

/**
 * Service Container
 * 
 * Manages class dependencies and performs dependency injection.
 */
class Container
{
   /**
    * @var array Registered bindings
    */
   private array $bindings = [];

   /**
    * @var array Singleton instances
    */
   private array $instances = [];

   /**
    * Bind a class or interface to a concrete implementation
    * 
    * @param string $abstract Class or interface name
    * @param mixed $concrete Concrete implementation (classname or closure)
    * @return void
    */
   public function bind(string $abstract, mixed $concrete = null): void
   {
      if ($concrete === null) {
         $concrete = $abstract;
      }

      $this->bindings[$abstract] = $concrete;
   }

   /**
    * Bind a singleton instance
    * 
    * @param string $abstract Class or interface name
    * @param mixed $concrete Concrete implementation (classname or closure)
    * @return void
    */
   public function singleton(string $abstract, mixed $concrete = null): void
   {
      $this->bind($abstract, $concrete);
      $this->bindings[$abstract . '_singleton'] = true;
   }

   /**
    * Resolve a class dependency
    * 
    * @param string $abstract Class or interface name
    * @return mixed Resolved instance
    */
   public function make(string $abstract): mixed
   {
      // Return existing singleton instance if available
      if (isset($this->instances[$abstract])) {
         return $this->instances[$abstract];
      }

      $concrete = $this->bindings[$abstract] ?? $abstract;

      // If it's a closure, execute it
      if ($concrete instanceof Closure) {
         $object = $concrete($this);
      } else {
         // Otherwise, build the class
         $object = $this->build($concrete);
      }

      // Store if singleton
      if (isset($this->bindings[$abstract . '_singleton'])) {
         $this->instances[$abstract] = $object;
      }

      return $object;
   }

   /**
    * Instantiate a concrete class with dependencies
    * 
    * @param string $concrete Class name
    * @return object
    */
   public function build(string $concrete): object
   {
      try {
         $reflector = new ReflectionClass($concrete);
      } catch (\ReflectionException $e) {
         throw new Exception("Target class [$concrete] does not exist.", 0, $e);
      }

      if (!$reflector->isInstantiable()) {
         throw new Exception("Target [$concrete] is not instantiable.");
      }

      $constructor = $reflector->getConstructor();

      if ($constructor === null) {
         return new $concrete;
      }

      $dependencies = $constructor->getParameters();
      $instances = $this->resolveDependencies($dependencies);

      return $reflector->newInstanceArgs($instances);
   }

   /**
    * Resolve method dependencies
    * 
    * @param array $dependencies ReflectionParameters
    * @return array Resolved instances
    */
   private function resolveDependencies(array $dependencies): array
   {
      $results = [];

      foreach ($dependencies as $dependency) {
         $type = $dependency->getType();

         if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
            if ($dependency->isDefaultValueAvailable()) {
               $results[] = $dependency->getDefaultValue();
            } else {
               throw new Exception("Unresolvable dependency resolving [$dependency] in class {$dependency->getDeclaringClass()->getName()}");
            }
         } else {
            $results[] = $this->make($type->getName());
         }
      }

      return $results;
   }
}
