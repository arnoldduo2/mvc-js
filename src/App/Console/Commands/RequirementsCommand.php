<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;
use App\App\Services\RequirementChecker;

class RequirementsCommand extends Command
{
   protected function signature(): string
   {
      return 'requirements:check';
   }

   protected function description(): string
   {
      return 'Check system requirements';
   }

   public function handle(): int
   {
      $checker = new RequirementChecker();
      $results = $checker->check();

      $this->info("System Requirements Check");
      $this->line(str_repeat('-', 40));

      // PHP Version
      $php = $results['php'];
      $this->renderResult($php['name'], $php['pass'], $php['message']);

      // Extensions
      if (isset($results['extensions'])) {
         $this->line("\nPHP Extensions:");
         foreach ($results['extensions'] as $ext) {
            $this->renderResult($ext['name'], $ext['pass'], $ext['message']);
         }
      }

      // Functions
      if (isset($results['functions'])) {
         $this->line("\nPHP Functions:");
         foreach ($results['functions'] as $func) {
            $this->renderResult($func['name'], $func['pass'], $func['message']);
         }
      }

      // Environment
      if (isset($results['env'])) {
         $this->line("\nEnvironment Variables:");
         foreach ($results['env'] as $env) {
            $this->renderResult($env['name'], $env['pass'], $env['message']);
         }
      }

      $this->line(str_repeat('-', 40));

      if ($checker->passes()) {
         $this->info("All requirements met successfully!");
         return 0;
      } else {
         $this->error("Some requirements are missing.");
         return 1;
      }
   }

   private function renderResult(string $name, bool $pass, string $message): void
   {
      $status = $pass ? "\033[32m[PASS]\033[0m" : "\033[31m[FAIL]\033[0m";
      $this->line(sprintf("%-30s %s", $name, $status));
      if (!$pass) {
         $this->line("  \033[33m-> {$message}\033[0m");
      }
   }
}
