<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;

class ServeCommand extends Command
{
   protected function signature(): string
   {
      return 'serve';
   }

   protected function description(): string
   {
      return 'Serve the application on the PHP development server';
   }

   public function handle(): int
   {
      $port = $this->hasOption('port') ? $this->option('port') : '8000';
      $host = $this->hasOption('host') ? $this->option('host') : '127.0.0.1';

      $this->info("MVC-JS Development Server started on http://{$host}:{$port}");
      $this->line("Press Ctrl+C to stop the server");
      $command = sprintf(
         'php -S %s:%s %s/../../../boot/boot.php',
         $host,
         $port,
         __DIR__
      );

      passthru($command);

      return 0;
   }
}