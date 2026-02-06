<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationManager;

/**
 * Migrate Command
 * 
 * Run pending database migrations.
 */
class MigrateCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate';
   }

   protected function description(): string
   {
      return 'Run pending database migrations';
   }

   public function handle(): int
   {
      $this->info('Running migrations...');
      $this->line('');

      $manager = new MigrationManager();
      $step = $this->hasOption('step') ? (int)$this->option('step') : null;

      try {
         $executed = $manager->run($step);

         if (empty($executed)) {
            $this->comment('Nothing to migrate.');
            return 0;
         }

         foreach ($executed as $migration) {
            $this->info("  ✓ Migrated: {$migration}");
         }

         $this->line('');
         $this->info('Migration completed successfully!');
         return 0;
      } catch (\Exception $e) {
         $this->error('Migration failed!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
