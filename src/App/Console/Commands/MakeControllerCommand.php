<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;

class MakeControllerCommand extends Command
{
   protected function signature(): string
   {
      return 'make:controller';
   }

   protected function description(): string
   {
      return 'Create a new controller class';
   }

   public function handle(): int
   {
      $name = $this->argument('0');

      if (!$name) {
         $this->error('Please provide a controller name');
         return 1;
      }

      $className = ucfirst($name);
      $directory = __DIR__ . '/../../Controllers';
      $file = "{$directory}/{$className}.php";

      if (file_exists($file)) {
         $this->error("Controller {$className} already exists!");
         return 1;
      }

      $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\App\Controllers;

use App\App\Core\Controller;

class {$className} extends Controller
{
    public function index(): void
    {
        // \$this->view('folder/view', ['title' => 'Page Title']);
    }
}

PHP;

      if (!is_dir($directory)) {
         mkdir($directory, 0755, true);
      }

      file_put_contents($file, $content);

      $this->info("Controller {$className} created successfully.");

      return 0;
   }
}
