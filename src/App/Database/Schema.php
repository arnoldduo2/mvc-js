<?php

declare(strict_types=1);

namespace App\App\Database;

use PDO;
use Closure;

/**
 * Schema Class
 * 
 * Provides static methods for creating, modifying, and dropping database tables.
 * Uses Blueprint for defining table structure.
 */
class Schema
{
   private static ?PDO $connection = null;

   /**
    * Set the database connection
    */
   public static function setConnection(PDO $connection): void
   {
      self::$connection = $connection;
   }

   /**
    * Get the database connection
    */
   private static function getConnection(): PDO
   {
      if (self::$connection === null) {
         self::$connection = Database::initialize();
      }
      return self::$connection;
   }

   /**
    * Create a new table
    */
   public static function create(string $table, Closure $callback): void
   {
      $blueprint = new Blueprint($table);
      $callback($blueprint);

      $sql = $blueprint->toSql();
      self::getConnection()->exec($sql);
   }

   /**
    * Modify an existing table
    */
   public static function table(string $table, Closure $callback): void
   {
      $blueprint = new Blueprint($table);
      $callback($blueprint);

      // Generate ALTER TABLE statements
      $statements = self::buildAlterStatements($blueprint);

      foreach ($statements as $sql) {
         self::getConnection()->exec($sql);
      }
   }

   /**
    * Drop a table
    */
   public static function drop(string $table): void
   {
      $sql = "DROP TABLE `{$table}`";
      self::getConnection()->exec($sql);
   }

   /**
    * Drop a table if it exists
    */
   public static function dropIfExists(string $table): void
   {
      $sql = "DROP TABLE IF EXISTS `{$table}`";
      self::getConnection()->exec($sql);
   }

   /**
    * Check if a table exists
    */
   public static function hasTable(string $table): bool
   {
      $sql = "SHOW TABLES LIKE :table";
      $stmt = self::getConnection()->prepare($sql);
      $stmt->execute(['table' => $table]);
      return $stmt->fetch() !== false;
   }

   /**
    * Check if a column exists in a table
    */
   public static function hasColumn(string $table, string $column): bool
   {
      $sql = "SHOW COLUMNS FROM `{$table}` LIKE :column";
      $stmt = self::getConnection()->prepare($sql);
      $stmt->execute(['column' => $column]);
      return $stmt->fetch() !== false;
   }

   /**
    * Rename a table
    */
   public static function rename(string $from, string $to): void
   {
      $sql = "RENAME TABLE `{$from}` TO `{$to}`";
      self::getConnection()->exec($sql);
   }

   /**
    * Drop a column from a table
    */
   public static function dropColumn(string $table, string|array $columns): void
   {
      $columns = is_array($columns) ? $columns : [$columns];

      foreach ($columns as $column) {
         $sql = "ALTER TABLE `{$table}` DROP COLUMN `{$column}`";
         self::getConnection()->exec($sql);
      }
   }

   /**
    * Rename a column
    */
   public static function renameColumn(string $table, string $from, string $to): void
   {
      // Get column definition
      $sql = "SHOW COLUMNS FROM `{$table}` WHERE Field = :column";
      $stmt = self::getConnection()->prepare($sql);
      $stmt->execute(['column' => $from]);
      $columnInfo = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$columnInfo) {
         throw new \Exception("Column {$from} does not exist in table {$table}");
      }

      // Build CHANGE COLUMN statement
      $sql = "ALTER TABLE `{$table}` CHANGE `{$from}` `{$to}` {$columnInfo['Type']}";

      if ($columnInfo['Null'] === 'NO') {
         $sql .= " NOT NULL";
      }

      if ($columnInfo['Default'] !== null) {
         $sql .= " DEFAULT '{$columnInfo['Default']}'";
      }

      self::getConnection()->exec($sql);
   }

   /**
    * Get all tables in the database
    */
   public static function getTables(): array
   {
      $sql = "SHOW TABLES";
      $stmt = self::getConnection()->query($sql);
      return $stmt->fetchAll(PDO::FETCH_COLUMN);
   }

   /**
    * Get all columns for a table
    */
   public static function getColumns(string $table): array
   {
      $sql = "SHOW COLUMNS FROM `{$table}`";
      $stmt = self::getConnection()->query($sql);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   /**
    * Disable foreign key checks
    */
   public static function disableForeignKeyConstraints(): void
   {
      self::getConnection()->exec("SET FOREIGN_KEY_CHECKS=0");
   }

   /**
    * Enable foreign key checks
    */
   public static function enableForeignKeyConstraints(): void
   {
      self::getConnection()->exec("SET FOREIGN_KEY_CHECKS=1");
   }

   /**
    * Build ALTER TABLE statements from blueprint
    */
   private static function buildAlterStatements(Blueprint $blueprint): array
   {
      $statements = [];
      $table = $blueprint->getTable();

      // Add new columns
      foreach ($blueprint->getColumns() as $column) {
         $sql = "ALTER TABLE `{$table}` ADD COLUMN ";
         $sql .= self::buildColumnDefinition($column);

         if (isset($column['after'])) {
            $sql .= " AFTER `{$column['after']}`";
         }

         $statements[] = $sql;
      }

      return $statements;
   }

   /**
    * Build column definition for ALTER statements
    */
   private static function buildColumnDefinition(array $column): string
   {
      $sql = "`{$column['name']}` {$column['type']}";

      if (isset($column['length'])) {
         $sql .= "({$column['length']})";
      } elseif (isset($column['precision']) && isset($column['scale'])) {
         $sql .= "({$column['precision']}, {$column['scale']})";
      }

      if (isset($column['unsigned']) && $column['unsigned']) {
         $sql .= " UNSIGNED";
      }

      if (isset($column['nullable']) && $column['nullable']) {
         $sql .= " NULL";
      } else {
         $sql .= " NOT NULL";
      }

      if (isset($column['default'])) {
         if ($column['default'] === null) {
            $sql .= " DEFAULT NULL";
         } elseif (is_string($column['default'])) {
            $sql .= " DEFAULT '{$column['default']}'";
         } else {
            $sql .= " DEFAULT {$column['default']}";
         }
      }

      return $sql;
   }

   /**
    * Execute raw SQL
    */
   public static function raw(string $sql): void
   {
      self::getConnection()->exec($sql);
   }
}
