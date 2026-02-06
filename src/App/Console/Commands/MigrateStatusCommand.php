<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationManager;

/**
 * Migrate Status Command
 * 
 * Show the status of all migrations.
 */
class MigrateStatusCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate:status';
   }

   protected function description(): string
   {
      return 'Show the status of all migrations';
   }

   public function handle(): int
   {
      $manager = new MigrationManager();
      $status = $manager->status();

      if (empty($status)) {
         $this->comment('No migrations found.');
         return 0;
      }

      $headers = ['Migration', 'Batch', 'Status'];
      $rows = [];

      foreach ($status as $migration) {
         $rows[] = [
            $migration['migration'],
            $migration['batch'] ?? '-',
            $migration['ran'] ? "\033[32mRan\033[0m" : "\033[33mPending\033[0m"
         ];
      }

      $this->table($headers, $rows);

      return 0;
   }
}
