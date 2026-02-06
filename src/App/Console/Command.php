<?php

declare(strict_types=1);

namespace App\App\Console;

/**
 * Base Command Class
 * 
 * All console commands should extend this class.
 */
abstract class Command
{
   protected array $arguments = [];
   protected array $options = [];

   /**
    * Command signature (name)
    */
   abstract protected function signature(): string;

   /**
    * Command description
    */
   abstract protected function description(): string;

   /**
    * Execute the command
    */
   abstract public function handle(): int;

   /**
    * Set command arguments
    */
   public function setArguments(array $arguments): void
   {
      $this->arguments = $arguments;
   }

   /**
    * Set command options
    */
   public function setOptions(array $options): void
   {
      $this->options = $options;
   }

   /**
    * Get argument value
    */
   protected function argument(string $key, mixed $default = null): mixed
   {
      return $this->arguments[$key] ?? $default;
   }

   /**
    * Get option value
    */
   protected function option(string $key, mixed $default = null): mixed
   {
      return $this->options[$key] ?? $default;
   }

   /**
    * Check if option exists
    */
   protected function hasOption(string $key): bool
   {
      return isset($this->options[$key]);
   }

   /**
    * Get signature
    */
   public function getSignature(): string
   {
      return $this->signature();
   }

   /**
    * Get description
    */
   public function getDescription(): string
   {
      return $this->description();
   }

    // ==================== OUTPUT HELPERS ====================

   /**
    * Write a line to output
    */
   protected function line(string $message): void
   {
      echo $message . "\n";
   }

   /**
    * Write an info message
    */
   protected function info(string $message): void
   {
      echo "\033[32m{$message}\033[0m\n";
   }

   /**
    * Write a comment message
    */
   protected function comment(string $message): void
   {
      echo "\033[33m{$message}\033[0m\n";
   }

   /**
    * Write an error message
    */
   protected function error(string $message): void
   {
      echo "\033[31m{$message}\033[0m\n";
   }

   /**
    * Write a warning message
    */
   protected function warn(string $message): void
   {
      echo "\033[93m{$message}\033[0m\n";
   }

   /**
    * Display a table
    */
   protected function table(array $headers, array $rows): void
   {
      // Calculate column widths
      $widths = [];
      foreach ($headers as $i => $header) {
         $widths[$i] = strlen($header);
      }

      foreach ($rows as $row) {
         foreach ($row as $i => $cell) {
            $widths[$i] = max($widths[$i], strlen((string)$cell));
         }
      }

      // Print header
      $this->line('');
      echo '  ';
      foreach ($headers as $i => $header) {
         echo str_pad($header, $widths[$i] + 2);
      }
      echo "\n";

      // Print separator
      echo '  ';
      foreach ($widths as $width) {
         echo str_repeat('-', $width + 2);
      }
      echo "\n";

      // Print rows
      foreach ($rows as $row) {
         echo '  ';
         foreach ($row as $i => $cell) {
            echo str_pad((string)$cell, $widths[$i] + 2);
         }
         echo "\n";
      }
      $this->line('');
   }

   /**
    * Ask for confirmation
    */
   protected function confirm(string $question, bool $default = false): bool
   {
      $defaultText = $default ? 'Y/n' : 'y/N';
      echo "{$question} [{$defaultText}]: ";

      $handle = fopen('php://stdin', 'r');
      $line = trim(fgets($handle));
      fclose($handle);

      if (empty($line)) {
         return $default;
      }

      return in_array(strtolower($line), ['y', 'yes']);
   }

   /**
    * Ask a question
    */
   protected function ask(string $question, ?string $default = null): string
   {
      $defaultText = $default ? " [{$default}]" : '';
      echo "{$question}{$defaultText}: ";

      $handle = fopen('php://stdin', 'r');
      $line = trim(fgets($handle));
      fclose($handle);

      return empty($line) && $default ? $default : $line;
   }
}
