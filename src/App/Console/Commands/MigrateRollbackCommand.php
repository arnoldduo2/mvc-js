<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationManager;

/**
 * Migrate Rollback Command
 * 
 * Rollback the last batch of migrations.
 */
class MigrateRollbackCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate:rollback';
   }

   protected function description(): string
   {
      return 'Rollback the last batch of migrations';
   }

   public function handle(): int
   {
      $this->warn('Rolling back migrations...');
      $this->line('');

      $manager = new MigrationManager();
      $step = $this->hasOption('step') ? (int)$this->option('step') : null;

      try {
         $rolledBack = $manager->rollback($step);

         if (empty($rolledBack)) {
            $this->comment('Nothing to rollback.');
            return 0;
         }

         foreach ($rolledBack as $migration) {
            $this->warn("  ✓ Rolled back: {$migration}");
         }

         $this->line('');
         $this->info('Rollback completed successfully!');
         return 0;
      } catch (\Exception $e) {
         $this->error('Rollback failed!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
