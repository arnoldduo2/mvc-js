<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationManager;
use App\App\Database\MigrationGenerator;
use App\App\Database\SchemaInspector;

/**
 * Migrate Sync Command
 * 
 * Detect and fix inconsistencies between database and migrations.
 */
class MigrateSyncCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate:sync';
   }

   protected function description(): string
   {
      return 'Detect and fix inconsistencies between database and migrations';
   }

   public function handle(): int
   {
      $this->info('Checking for inconsistencies...');
      $this->line('');

      $inspector = new SchemaInspector();
      $manager = new MigrationManager();
      $generator = new MigrationGenerator();

      try {
         // Get all database tables
         $dbTables = $inspector->getTables();

         // Get migration status
         $migrationStatus = $manager->status();
         $migrationTables = [];

         // Extract table names from migration files
         foreach ($migrationStatus as $migration) {
            if (preg_match('/create_(.+)_table/', $migration['migration'], $matches)) {
               $migrationTables[] = $matches[1];
            }
         }

         // Find tables without migrations
         $tablesWithoutMigrations = array_diff($dbTables, $migrationTables);

         // Exclude migrations table
         $tablesWithoutMigrations = array_diff($tablesWithoutMigrations, ['migrations']);

         if (empty($tablesWithoutMigrations)) {
            $this->info('✓ No inconsistencies found!');
            $this->comment('  All database tables have corresponding migrations.');
            return 0;
         }

         $this->warn('Found tables without migrations:');
         foreach ($tablesWithoutMigrations as $table) {
            $this->line("  • {$table}");
         }
         $this->line('');

         if ($this->hasOption('fix')) {
            $this->info('Generating missing migrations...');
            $this->line('');

            foreach ($tablesWithoutMigrations as $table) {
               $filename = $generator->generateFromTable($table);
               $this->info("  ✓ Generated: {$filename}");

               // Mark as already executed (batch 0 for pre-existing tables)
               $manager->markAsExecuted($filename, 0);
            }

            $this->line('');
            $this->info('Sync completed successfully!');
            $this->comment('Generated migrations have been marked as already executed.');
         } else {
            $this->line('');
            $this->comment('Run with --fix to generate missing migrations:');
            $this->comment('  php console migrate:sync --fix');
         }

         return 0;
      } catch (\Exception $e) {
         $this->error('Sync failed!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
