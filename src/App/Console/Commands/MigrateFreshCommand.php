<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationManager;
use App\App\Database\Schema;

/**
 * Migrate Fresh Command
 * 
 * Drop all tables and re-run all migrations.
 */
class MigrateFreshCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate:fresh';
   }

   protected function description(): string
   {
      return 'Drop all tables and re-run all migrations';
   }

   public function handle(): int
   {
      if (!$this->hasOption('force')) {
         if (!$this->confirm('This will drop all tables. Are you sure?', false)) {
            $this->comment('Operation cancelled.');
            return 0;
         }
      }

      $this->warn('Dropping all tables...');

      try {
         // Get all tables
         $tables = Schema::getTables();

         // Disable foreign key checks
         Schema::disableForeignKeyConstraints();

         // Drop all tables
         foreach ($tables as $table) {
            Schema::drop($table);
            $this->warn("  ✓ Dropped: {$table}");
         }

         // Re-enable foreign key checks
         Schema::enableForeignKeyConstraints();

         $this->line('');
         $this->info('Running migrations...');
         $this->line('');

         // Run migrations
         $manager = new MigrationManager();
         $executed = $manager->run();

         foreach ($executed as $migration) {
            $this->info("  ✓ Migrated: {$migration}");
         }

         $this->line('');
         $this->info('Database refreshed successfully!');
         return 0;
      } catch (\Exception $e) {
         $this->error('Operation failed!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
