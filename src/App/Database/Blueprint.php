<?php

declare(strict_types=1);

namespace App\App\Database;

use PDO;

/**
 * Blueprint Class
 * 
 * Provides a fluent interface for defining table structure.
 * Used by Schema to create and modify database tables.
 */
class Blueprint
{
   private string $table;
   private array $columns = [];
   private array $indexes = [];
   private array $foreignKeys = [];
   private string $engine = 'InnoDB';
   private string $charset = 'utf8mb4';
   private string $collation = 'utf8mb4_unicode_ci';
   private ?string $primaryKey = null;

   public function __construct(string $table)
   {
      $this->table = $table;
   }

    // ==================== COLUMN TYPES ====================

   /**
    * Add an auto-incrementing primary key (id)
    */
   public function id(string $column = 'id'): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'BIGINT',
         'unsigned' => true,
         'autoIncrement' => true,
         'nullable' => false,
      ];
      $this->primaryKey = $column;
      return $this;
   }

   /**
    * Add a VARCHAR column
    */
   public function string(string $column, int $length = 255): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'VARCHAR',
         'length' => $length,
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a TEXT column
    */
   public function text(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'TEXT',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a LONGTEXT column
    */
   public function longText(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'LONGTEXT',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add an INTEGER column
    */
   public function integer(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'INT',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a BIGINT column
    */
   public function bigInteger(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'BIGINT',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a TINYINT column
    */
   public function tinyInteger(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'TINYINT',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a BOOLEAN column (TINYINT(1))
    */
   public function boolean(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'TINYINT',
         'length' => 1,
         'nullable' => false,
         'default' => 0,
      ];
      return $this;
   }

   /**
    * Add a DECIMAL column
    */
   public function decimal(string $column, int $precision = 8, int $scale = 2): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'DECIMAL',
         'precision' => $precision,
         'scale' => $scale,
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a FLOAT column
    */
   public function float(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'FLOAT',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a DOUBLE column
    */
   public function double(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'DOUBLE',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a DATE column
    */
   public function date(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'DATE',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a DATETIME column
    */
   public function dateTime(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'DATETIME',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a TIMESTAMP column
    */
   public function timestamp(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'TIMESTAMP',
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add created_at and updated_at timestamp columns
    */
   public function timestamps(): self
   {
      $this->timestamp('created_at')->nullable()->useCurrent();
      $this->timestamp('updated_at')->nullable()->useCurrent()->useCurrentOnUpdate();
      return $this;
   }

   /**
    * Add a soft delete timestamp column
    */
   public function softDeletes(string $column = 'deleted_at'): self
   {
      $this->timestamp($column)->nullable();
      return $this;
   }

   /**
    * Add an ENUM column
    */
   public function enum(string $column, array $values): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'ENUM',
         'values' => $values,
         'nullable' => false,
      ];
      return $this;
   }

   /**
    * Add a JSON column
    */
   public function json(string $column): self
   {
      $this->columns[] = [
         'name' => $column,
         'type' => 'JSON',
         'nullable' => false,
      ];
      return $this;
   }

    // ==================== COLUMN MODIFIERS ====================

   /**
    * Make the last column nullable
    */
   public function nullable(): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['nullable'] = true;
      }
      return $this;
   }

   /**
    * Set default value for the last column
    */
   public function default(mixed $value): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['default'] = $value;
      }
      return $this;
   }

   /**
    * Make the last column unsigned
    */
   public function unsigned(): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['unsigned'] = true;
      }
      return $this;
   }

   /**
    * Set CURRENT_TIMESTAMP as default for the last column
    */
   public function useCurrent(): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['useCurrent'] = true;
      }
      return $this;
   }

   /**
    * Set ON UPDATE CURRENT_TIMESTAMP for the last column
    */
   public function useCurrentOnUpdate(): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['useCurrentOnUpdate'] = true;
      }
      return $this;
   }

   /**
    * Add a comment to the last column
    */
   public function comment(string $comment): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['comment'] = $comment;
      }
      return $this;
   }

   /**
    * Add AFTER clause to the last column
    */
   public function after(string $column): self
   {
      if (!empty($this->columns)) {
         $lastIndex = count($this->columns) - 1;
         $this->columns[$lastIndex]['after'] = $column;
      }
      return $this;
   }

    // ==================== INDEXES ====================

   /**
    * Add a primary key
    */
   public function primary(string|array $columns): self
   {
      $this->indexes[] = [
         'type' => 'PRIMARY',
         'columns' => is_array($columns) ? $columns : [$columns],
      ];
      return $this;
   }

   /**
    * Add a unique index
    */
   public function unique(string|array|null $columns = null, ?string $name = null): self
   {
      $cols = is_array($columns) ? $columns : [$columns];
      $this->indexes[] = [
         'type' => 'UNIQUE',
         'columns' => $cols,
         'name' => $name ?? 'unique_' . implode('_', $cols),
      ];
      return $this;
   }

   /**
    * Add a regular index
    */
   public function index(string|array $columns, ?string $name = null): self
   {
      $cols = is_array($columns) ? $columns : [$columns];
      $this->indexes[] = [
         'type' => 'INDEX',
         'columns' => $cols,
         'name' => $name ?? 'idx_' . implode('_', $cols),
      ];
      return $this;
   }

    // ==================== FOREIGN KEYS ====================

   /**
    * Add a foreign key constraint
    */
   public function foreign(string|array $columns, ?string $name = null): ForeignKeyDefinition
   {
      $cols = is_array($columns) ? $columns : [$columns];
      $fk = new ForeignKeyDefinition($cols, $name);
      $this->foreignKeys[] = $fk;
      return $fk;
   }

    // ==================== TABLE OPTIONS ====================

   /**
    * Set table engine
    */
   public function engine(string $engine): self
   {
      $this->engine = $engine;
      return $this;
   }

   /**
    * Set table charset
    */
   public function charset(string $charset): self
   {
      $this->charset = $charset;
      return $this;
   }

   /**
    * Set table collation
    */
   public function collation(string $collation): self
   {
      $this->collation = $collation;
      return $this;
   }

    // ==================== SQL GENERATION ====================

   /**
    * Generate CREATE TABLE SQL
    */
   public function toSql(): string
   {
      $sql = "CREATE TABLE `{$this->table}` (\n";

      // Add columns
      $columnDefinitions = [];
      foreach ($this->columns as $column) {
         $columnDefinitions[] = $this->buildColumnSql($column);
      }

      $sql .= "  " . implode(",\n  ", $columnDefinitions);

      // Add primary key if set
      if ($this->primaryKey) {
         $sql .= ",\n  PRIMARY KEY (`{$this->primaryKey}`)";
      }

      // Add indexes
      foreach ($this->indexes as $index) {
         $sql .= ",\n  " . $this->buildIndexSql($index);
      }

      // Add foreign keys
      foreach ($this->foreignKeys as $fk) {
         $sql .= ",\n  " . $fk->toSql();
      }

      $sql .= "\n) ENGINE={$this->engine} DEFAULT CHARSET={$this->charset} COLLATE={$this->collation};";

      return $sql;
   }

   /**
    * Build column SQL definition
    */
   private function buildColumnSql(array $column): string
   {
      $sql = "`{$column['name']}` {$column['type']}";

      // Add length/precision
      if (isset($column['length'])) {
         $sql .= "({$column['length']})";
      } elseif (isset($column['precision']) && isset($column['scale'])) {
         $sql .= "({$column['precision']}, {$column['scale']})";
      }

      // Add ENUM values
      if (isset($column['values'])) {
         $values = array_map(fn($v) => "'{$v}'", $column['values']);
         $sql .= "(" . implode(', ', $values) . ")";
      }

      // Add unsigned
      if (isset($column['unsigned']) && $column['unsigned']) {
         $sql .= " UNSIGNED";
      }

      // Add nullable
      if (isset($column['nullable']) && $column['nullable']) {
         $sql .= " NULL";
      } else {
         $sql .= " NOT NULL";
      }

      // Add auto increment
      if (isset($column['autoIncrement']) && $column['autoIncrement']) {
         $sql .= " AUTO_INCREMENT";
      }

      // Add default value
      if (isset($column['default'])) {
         if ($column['default'] === null) {
            $sql .= " DEFAULT NULL";
         } elseif (is_string($column['default'])) {
            $sql .= " DEFAULT '{$column['default']}'";
         } else {
            $sql .= " DEFAULT {$column['default']}";
         }
      }

      // Add CURRENT_TIMESTAMP
      if (isset($column['useCurrent']) && $column['useCurrent']) {
         $sql .= " DEFAULT CURRENT_TIMESTAMP";
      }

      // Add ON UPDATE CURRENT_TIMESTAMP
      if (isset($column['useCurrentOnUpdate']) && $column['useCurrentOnUpdate']) {
         $sql .= " ON UPDATE CURRENT_TIMESTAMP";
      }

      // Add comment
      if (isset($column['comment'])) {
         $sql .= " COMMENT '{$column['comment']}'";
      }

      return $sql;
   }

   /**
    * Build index SQL definition
    */
   private function buildIndexSql(array $index): string
   {
      $columns = implode('`, `', $index['columns']);

      if ($index['type'] === 'PRIMARY') {
         return "PRIMARY KEY (`{$columns}`)";
      } elseif ($index['type'] === 'UNIQUE') {
         return "UNIQUE KEY `{$index['name']}` (`{$columns}`)";
      } else {
         return "KEY `{$index['name']}` (`{$columns}`)";
      }
   }

   /**
    * Get table name
    */
   public function getTable(): string
   {
      return $this->table;
   }

   /**
    * Get all columns
    */
   public function getColumns(): array
   {
      return $this->columns;
   }
}

/**
 * Foreign Key Definition Class
 */
class ForeignKeyDefinition
{
   private array $columns;
   private ?string $name;
   private ?string $referencedTable = null;
   private array $referencedColumns = [];
   private string $onDelete = 'RESTRICT';
   private string $onUpdate = 'RESTRICT';

   public function __construct(array $columns, ?string $name = null)
   {
      $this->columns = $columns;
      $this->name = $name ?? 'fk_' . implode('_', $columns);
   }

   /**
    * Set the referenced table and columns
    */
   public function references(string|array $columns): self
   {
      $this->referencedColumns = is_array($columns) ? $columns : [$columns];
      return $this;
   }

   /**
    * Set the referenced table
    */
   public function on(string $table): self
   {
      $this->referencedTable = $table;
      return $this;
   }

   /**
    * Set ON DELETE action
    */
   public function onDelete(string $action): self
   {
      $this->onDelete = strtoupper($action);
      return $this;
   }

   /**
    * Set ON UPDATE action
    */
   public function onUpdate(string $action): self
   {
      $this->onUpdate = strtoupper($action);
      return $this;
   }

   /**
    * Set CASCADE on delete
    */
   public function cascadeOnDelete(): self
   {
      return $this->onDelete('CASCADE');
   }

   /**
    * Set CASCADE on update
    */
   public function cascadeOnUpdate(): self
   {
      return $this->onUpdate('CASCADE');
   }

   /**
    * Generate foreign key SQL
    */
   public function toSql(): string
   {
      $columns = implode('`, `', $this->columns);
      $refColumns = implode('`, `', $this->referencedColumns);

      return "CONSTRAINT `{$this->name}` FOREIGN KEY (`{$columns}`) " .
         "REFERENCES `{$this->referencedTable}` (`{$refColumns}`) " .
         "ON DELETE {$this->onDelete} ON UPDATE {$this->onUpdate}";
   }
}
