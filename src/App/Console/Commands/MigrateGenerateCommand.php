<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Database\MigrationGenerator;

/**
 * Migrate Generate Command
 * 
 * Generate migrations from existing database tables.
 */
class MigrateGenerateCommand extends Command
{
   protected function signature(): string
   {
      return 'migrate:generate';
   }

   protected function description(): string
   {
      return 'Generate migrations from existing database tables';
   }

   public function handle(): int
   {
      $this->info('Generating migrations from database...');
      $this->line('');

      $generator = new MigrationGenerator();
      $table = $this->option('table');

      try {
         if ($table) {
            // Generate for specific table
            $filename = $generator->generateFromTable($table);
            $this->info("  ✓ Generated migration for table: {$table}");
            $this->comment("    {$filename}");
         } else {
            // Generate for all tables
            $exclude = $this->option('exclude')
               ? explode(',', $this->option('exclude'))
               : ['migrations'];

            $generated = $generator->generateFromAllTables($exclude);

            if (empty($generated)) {
               $this->comment('No tables found to generate migrations.');
               return 0;
            }

            foreach ($generated as $filename) {
               $this->info("  ✓ Generated: {$filename}");
            }
         }

         $this->line('');
         $this->info('Migration generation completed!');
         $this->comment('Note: Generated migrations are marked as already executed.');
         $this->comment('Run "php console migrate:sync --fix" to register them.');

         return 0;
      } catch (\Exception $e) {
         $this->error('Generation failed!');
         $this->error($e->getMessage());
         return 1;
      }
   }
}
