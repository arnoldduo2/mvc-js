<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationGenerator;

/**
 * Make Migration Command
 * 
 * Create a new migration file.
 */
class MakeMigrationCommand extends Command
{
   protected function signature(): string
   {
      return 'make:migration';
   }

   protected function description(): string
   {
      return 'Create a new migration file';
   }

   public function handle(): int
   {
      $name = $this->arguments[0] ?? null;

      if (!$name) {
         $this->error('Migration name is required.');
         $this->comment('Usage: php console make:migration create_users_table');
         return 1;
      }

      $table = $this->option('table');
      $generator = new MigrationGenerator();

      try {
         $filename = $generator->create($name, $table);
         $path = $generator->getMigrationsPath() . '/' . $filename;

         $this->info("Migration created successfully!");
         $this->line("  {$path}");

         return 0;
      } catch (\Exception $e) {
         $this->error('Failed to create migration!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
