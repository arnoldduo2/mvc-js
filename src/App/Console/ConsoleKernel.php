<?php

declare(strict_types=1);

namespace App\App\Console;

/**
 * Console Kernel
 * 
 * Handles console command registration and execution.
 */
class ConsoleKernel
{
   private array $commands = [];

   public function __construct()
   {
      $this->registerCommands();
   }

   /**
    * Register all console commands
    */
   private function registerCommands(): void
   {
      $this->commands = [
         new Commands\MigrateCommand(),
         new Commands\MigrateRollbackCommand(),
         new Commands\MigrateStatusCommand(),
         new Commands\MigrateFreshCommand(),
         new Commands\MigrateResetCommand(),
         new Commands\MakeMigrationCommand(),
         new Commands\MigrateGenerateCommand(),
         new Commands\MigrateSyncCommand(),
         new Commands\MakeModelCommand(),
         new Commands\MakeControllerCommand(),
         new Commands\MakeControllerCommand(),
         new Commands\ServeCommand(),
         new Commands\RequirementsCommand(),
      ];
   }

   /**
    * Handle console command
    */
   public function handle(array $argv): int
   {
      array_shift($argv); // Remove script name

      if (empty($argv)) {
         $this->showHelp();
         return 0;
      }

      $commandName = array_shift($argv);

      // Parse arguments and options
      [$arguments, $options] = $this->parseArguments($argv);

      // Find and execute command
      foreach ($this->commands as $command) {
         if ($command->getSignature() === $commandName) {
            $command->setArguments($arguments);
            $command->setOptions($options);
            return $command->handle();
         }
      }

      echo "\033[31mCommand not found:\033[0m {$commandName}\n\n";
      $this->showHelp();
      return 1;
   }

   /**
    * Parse command arguments and options
    */
   private function parseArguments(array $argv): array
   {
      $arguments = [];
      $options = [];

      foreach ($argv as $arg) {
         if (strpos($arg, '--') === 0) {
            // Long option
            $arg = substr($arg, 2);
            if (strpos($arg, '=') !== false) {
               [$key, $value] = explode('=', $arg, 2);
               $options[$key] = $value;
            } else {
               $options[$arg] = true;
            }
         } elseif (strpos($arg, '-') === 0) {
            // Short option
            $options[substr($arg, 1)] = true;
         } else {
            // Argument
            $arguments[] = $arg;
         }
      }

      return [$arguments, $options];
   }

   /**
    * Show help message
    */
   private function showHelp(): void
   {
      echo "\033[33mMVC-JS Console\033[0m\n\n";
      echo "Usage:\n";
      echo "  php console <command> [options]\n\n";
      echo "Available commands:\n";

      foreach ($this->commands as $command) {
         $signature = str_pad($command->getSignature(), 30);
         echo "  \033[32m{$signature}\033[0m {$command->getDescription()}\n";
      }

      echo "\n";
   }
}
