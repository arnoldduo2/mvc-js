<?php

declare(strict_types=1);

namespace App\App\Database;

/**
 * Migration Generator
 * 
 * Generates migration files from existing database tables or specifications.
 */
class MigrationGenerator
{
    private SchemaInspector $inspector;
    private string $migrationsPath;

    public function __construct(?SchemaInspector $inspector = null, ?string $migrationsPath = null)
    {
        $this->inspector = $inspector ?? new SchemaInspector();
        $this->migrationsPath = $migrationsPath ?? $this->getDefaultMigrationsPath();
    }

    /**
     * Get default migrations path
     */
    private function getDefaultMigrationsPath(): string
    {
        return dirname(__DIR__, 4) . '/src/database/migrations';
    }

    /**
     * Generate migration from existing table
     */
    public function generateFromTable(string $table): string
    {
        if (!$this->inspector->tableExists($table)) {
            throw new \Exception("Table '{$table}' does not exist");
        }

        $structure = $this->inspector->getTableStructure($table);
        $timestamp = date('YmdHis');
        $description = "create_{$table}_table";
        $filename = "{$timestamp}_{$description}.php";

        $content = $this->buildMigrationContent($structure, $description);

        $this->ensureMigrationsDirectoryExists();
        $path = $this->migrationsPath . '/' . $filename;

        file_put_contents($path, $content);

        return $filename;
    }

    /**
     * Generate migrations for all tables
     */
    public function generateFromAllTables(?array $excludeTables = null): array
    {
        $excludeTables = $excludeTables ?? ['migrations'];
        $tables = $this->inspector->getTables();
        $generated = [];

        foreach ($tables as $table) {
            if (!in_array($table, $excludeTables)) {
                $generated[] = $this->generateFromTable($table);
            }
        }

        return $generated;
    }

    /**
     * Create a new blank migration
     */
    public function create(string $name, ?string $table = null): string
    {
        $timestamp = date('YmdHis');
        $filename = "{$timestamp}_{$name}.php";

        $content = $this->buildBlankMigrationContent($name, $table);

        $this->ensureMigrationsDirectoryExists();
        $path = $this->migrationsPath . '/' . $filename;

        file_put_contents($path, $content);

        return $filename;
    }

    /**
     * Build migration file content from table structure
     */
    private function buildMigrationContent(array $structure, string $description): string
    {
        $className = $this->getClassName($description);
        $tableName = $structure['name'];

        // Build up() method
        $upMethod = $this->buildUpMethod($structure);

        // Build down() method
        $downMethod = "        Schema::dropIfExists('{$tableName}');";

        return <<<PHP
<?php

declare(strict_types=1);

use App\App\Database\Migration;
use App\App\Database\Schema;
use App\App\Database\Blueprint;

/**
 * Migration: {$description}
 * Generated from existing table: {$tableName}
 */
class {$className} extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
{$upMethod}
    }

    /**
     * Reverse the migration
     */
    public function down(): void
    {
{$downMethod}
    }
}

PHP;
    }

    /**
     * Build up() method content
     */
    private function buildUpMethod(array $structure): string
    {
        $lines = [];
        $lines[] = "        Schema::create('{$structure['name']}', function (Blueprint \$table) {";

        // Add columns
        foreach ($structure['columns'] as $column) {
            $lines[] = $this->buildColumnDefinition($column);
        }

        // Add indexes (excluding primary key which is handled by id())
        foreach ($structure['indexes'] as $index) {
            if (!$index['primary']) {
                $lines[] = $this->buildIndexDefinition($index);
            }
        }

        // Add foreign keys
        foreach ($structure['foreignKeys'] as $fk) {
            $lines[] = $this->buildForeignKeyDefinition($fk);
        }

        // Add table options if not default
        if ($structure['engine'] !== 'InnoDB') {
            $lines[] = "            \$table->engine('{$structure['engine']}');";
        }
        if ($structure['charset'] !== 'utf8mb4') {
            $lines[] = "            \$table->charset('{$structure['charset']}');";
        }
        if ($structure['collation'] !== 'utf8mb4_unicode_ci') {
            $lines[] = "            \$table->collation('{$structure['collation']}');";
        }

        $lines[] = "        });";

        return implode("\n", $lines);
    }

    /**
     * Build column definition code
     */
    private function buildColumnDefinition(array $column): string
    {
        $name = $column['name'];
        $type = $column['baseType'];

        // Handle special cases
        if (isset($column['autoIncrement']) && $column['autoIncrement']) {
            return "            \$table->id('{$name}');";
        }

        // Map database types to Blueprint methods
        $line = "            \$table->";

        switch ($type) {
            case 'VARCHAR':
            case 'CHAR':
                $length = $column['length'] ?? 255;
                $line .= "string('{$name}', {$length})";
                break;

            case 'TEXT':
                $line .= "text('{$name}')";
                break;

            case 'LONGTEXT':
                $line .= "longText('{$name}')";
                break;

            case 'INT':
                $line .= "integer('{$name}')";
                break;

            case 'BIGINT':
                $line .= "bigInteger('{$name}')";
                break;

            case 'TINYINT':
                if (isset($column['length']) && $column['length'] == 1) {
                    $line .= "boolean('{$name}')";
                } else {
                    $line .= "tinyInteger('{$name}')";
                }
                break;

            case 'DECIMAL':
                $precision = $column['precision'] ?? 8;
                $scale = $column['scale'] ?? 2;
                $line .= "decimal('{$name}', {$precision}, {$scale})";
                break;

            case 'FLOAT':
                $line .= "float('{$name}')";
                break;

            case 'DOUBLE':
                $line .= "double('{$name}')";
                break;

            case 'DATE':
                $line .= "date('{$name}')";
                break;

            case 'DATETIME':
                $line .= "dateTime('{$name}')";
                break;

            case 'TIMESTAMP':
                $line .= "timestamp('{$name}')";
                break;

            case 'ENUM':
                $values = isset($column['values']) ? "['" . implode("', '", $column['values']) . "']" : "[]";
                $line .= "enum('{$name}', {$values})";
                break;

            case 'JSON':
                $line .= "json('{$name}')";
                break;

            default:
                // Fallback to string
                $line .= "string('{$name}')";
        }

        // Add modifiers
        if (isset($column['unsigned']) && $column['unsigned']) {
            $line .= "->unsigned()";
        }

        if ($column['nullable']) {
            $line .= "->nullable()";
        }

        if ($column['default'] !== null && !isset($column['autoIncrement'])) {
            if (is_string($column['default'])) {
                $line .= "->default('{$column['default']}')";
            } else {
                $line .= "->default({$column['default']})";
            }
        }

        if (!empty($column['comment'])) {
            $comment = addslashes($column['comment']);
            $line .= "->comment('{$comment}')";
        }

        $line .= ";";

        return $line;
    }

    /**
     * Build index definition code
     */
    private function buildIndexDefinition(array $index): string
    {
        $columns = count($index['columns']) === 1
            ? "'{$index['columns'][0]}'"
            : "['" . implode("', '", $index['columns']) . "']";

        if ($index['unique']) {
            return "            \$table->unique({$columns});";
        } else {
            return "            \$table->index({$columns});";
        }
    }

    /**
     * Build foreign key definition code
     */
    private function buildForeignKeyDefinition(array $fk): string
    {
        $columns = count($fk['columns']) === 1
            ? "'{$fk['columns'][0]}'"
            : "['" . implode("', '", $fk['columns']) . "']";

        $refColumns = count($fk['referencedColumns']) === 1
            ? "'{$fk['referencedColumns'][0]}'"
            : "['" . implode("', '", $fk['referencedColumns']) . "']";

        $line = "            \$table->foreign({$columns})->references({$refColumns})->on('{$fk['referencedTable']}')";

        if ($fk['onDelete'] !== 'RESTRICT') {
            $line .= "->onDelete('" . strtolower($fk['onDelete']) . "')";
        }

        if ($fk['onUpdate'] !== 'RESTRICT') {
            $line .= "->onUpdate('" . strtolower($fk['onUpdate']) . "')";
        }

        $line .= ";";

        return $line;
    }

    /**
     * Build blank migration content
     */
    private function buildBlankMigrationContent(string $name, ?string $table = null): string
    {
        $className = $this->getClassName($name);

        if ($table) {
            // Migration to modify existing table
            $upMethod = <<<PHP
        Schema::table('{$table}', function (Blueprint \$table) {
            // Add your column modifications here
            // Example: \$table->string('new_column')->nullable();
        });
PHP;
            $downMethod = <<<PHP
        Schema::table('{$table}', function (Blueprint \$table) {
            // Reverse your modifications here
            // Example: Schema::dropColumn('{$table}', 'new_column');
        });
PHP;
        } else {
            // Migration to create new table
            $tableName = $this->extractTableNameFromDescription($name);
            $upMethod = <<<PHP
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            // Add your columns here
            \$table->timestamps();
        });
PHP;
            $downMethod = <<<PHP
        Schema::dropIfExists('{$tableName}');
PHP;
        }

        return <<<PHP
<?php

declare(strict_types=1);

use App\App\Database\Migration;
use App\App\Database\Schema;
use App\App\Database\Blueprint;

/**
 * Migration: {$name}
 */
class {$className} extends Migration
{
    /**
     * Run the migration
     */
    public function up(): void
    {
{$upMethod}
    }

    /**
     * Reverse the migration
     */
    public function down(): void
    {
{$downMethod}
    }
}

PHP;
    }

    /**
     * Get class name from description
     */
    private function getClassName(string $description): string
    {
        $timestamp = date('YmdHis');
        return 'Migration_' . $timestamp . '_' . $description;
    }

    /**
     * Extract table name from migration description
     */
    private function extractTableNameFromDescription(string $description): string
    {
        // Try to extract table name from patterns like "create_users_table"
        if (preg_match('/create_(.+)_table/', $description, $matches)) {
            return $matches[1];
        }

        // Default to the description itself
        return str_replace(['create_', '_table'], '', $description);
    }

    /**
     * Ensure migrations directory exists
     */
    private function ensureMigrationsDirectoryExists(): void
    {
        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }
    }

    /**
     * Get migrations path
     */
    public function getMigrationsPath(): string
    {
        return $this->migrationsPath;
    }
}
