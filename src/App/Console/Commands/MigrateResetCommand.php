<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationManager;

/**
 * Migrate Reset Command
 * 
 * Rollback all migrations.
 */
class MigrateResetCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate:reset';
   }

   protected function description(): string
   {
      return 'Rollback all migrations';
   }

   public function handle(): int
   {
      if (!$this->hasOption('force')) {
         if (!$this->confirm('This will rollback all migrations. Are you sure?', false)) {
            $this->comment('Operation cancelled.');
            return 0;
         }
      }

      $this->warn('Resetting database...');
      $this->line('');

      $manager = new MigrationManager();

      try {
         $rolledBack = $manager->reset();

         if (empty($rolledBack)) {
            $this->comment('Nothing to rollback.');
            return 0;
         }

         foreach ($rolledBack as $migration) {
            $this->warn("  ✓ Rolled back: {$migration}");
         }

         $this->line('');
         $this->info('Database reset successfully!');
         return 0;
      } catch (\Exception $e) {
         $this->error('Reset failed!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
