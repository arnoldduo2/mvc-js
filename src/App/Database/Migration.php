<?php

declare(strict_types=1);

namespace App\App\Database;

/**
 * Base Migration Class
 * 
 * All migration files should extend this class and implement up() and down() methods.
 * The up() method is called when running migrations, and down() is called when rolling back.
 */
abstract class Migration
{
   /**
    * Run the migration (apply changes)
    */
   abstract public function up(): void;

   /**
    * Reverse the migration (revert changes)
    */
   abstract public function down(): void;

   /**
    * Get the migration name from the class name
    */
   public function getName(): string
   {
      return get_class($this);
   }

   /**
    * Get the migration timestamp from the filename
    */
   public function getTimestamp(): string
   {
      $className = $this->getName();
      // Extract timestamp from class name (e.g., Migration_20260206192000_create_users_table)
      if (preg_match('/Migration_(\d{14})_/', $className, $matches)) {
         return $matches[1];
      }
      return date('YmdHis');
   }
}
