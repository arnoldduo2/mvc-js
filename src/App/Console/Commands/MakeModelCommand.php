<?php

declare(strict_types=1);

namespace App\App\Console\Commands;

use App\App\Console\Command;

class MakeModelCommand extends Command
{
   protected function signature(): string
   {
      return 'make:model';
   }

   protected function description(): string
   {
      return 'Create a new model class';
   }

   public function handle(): int
   {
      $name = $this->argument('0');

      if (!$name) {
         $this->error('Please provide a model name');
         return 1;
      }

      $className = ucfirst($name);
      $directory = __DIR__ . '/../../Models';
      $file = "{$directory}/{$className}.php";

      if (file_exists($file)) {
         $this->error("Model {$className} already exists!");
         return 1;
      }

      $content = <<<PHP
<?php

declare(strict_types=1);

namespace App\App\Models;

use App\App\Core\Model;

class {$className} extends Model
{
    protected string \$table = '{$this->snakeCase($className)}s';
}

PHP;

      if (!is_dir($directory)) {
         mkdir($directory, 0755, true);
      }

      file_put_contents($file, $content);

      $this->info("Model {$className} created successfully.");

      return 0;
   }

   private function snakeCase(string $input): string
   {
      return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
   }
}
