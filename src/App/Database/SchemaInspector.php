<?php

declare(strict_types=1);

namespace App\App\Database;

use PDO;

/**
 * Schema Inspector
 * 
 * Introspects existing database tables to extract their structure.
 * Used for generating migrations from existing databases.
 */
class SchemaInspector
{
   private PDO $connection;

   public function __construct(?PDO $connection = null)
   {
      $this->connection = $connection ?? Database::initialize();
   }

   /**
    * Get all tables in the database
    */
   public function getTables(): array
   {
      $stmt = $this->connection->query("SHOW TABLES");
      return $stmt->fetchAll(PDO::FETCH_COLUMN);
   }

   /**
    * Get complete table structure
    */
   public function getTableStructure(string $table): array
   {
      return [
         'name' => $table,
         'columns' => $this->getColumns($table),
         'indexes' => $this->getIndexes($table),
         'foreignKeys' => $this->getForeignKeys($table),
         'engine' => $this->getTableEngine($table),
         'charset' => $this->getTableCharset($table),
         'collation' => $this->getTableCollation($table),
      ];
   }

   /**
    * Get all columns for a table
    */
   public function getColumns(string $table): array
   {
      $stmt = $this->connection->query("SHOW FULL COLUMNS FROM `{$table}`");
      $columns = [];

      foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
         $columns[] = $this->parseColumn($column);
      }

      return $columns;
   }

   /**
    * Parse column information
    */
   private function parseColumn(array $column): array
   {
      $parsed = [
         'name' => $column['Field'],
         'type' => $this->parseColumnType($column['Type']),
         'nullable' => $column['Null'] === 'YES',
         'default' => $column['Default'],
         'extra' => $column['Extra'],
         'comment' => $column['Comment'] ?? '',
      ];

      // Parse type details
      if (preg_match('/^(\w+)\((.+)\)/', $column['Type'], $matches)) {
         $parsed['baseType'] = strtoupper($matches[1]);
         $parsed['typeParams'] = $matches[2];

         // Handle specific types
         if (in_array($parsed['baseType'], ['VARCHAR', 'CHAR'])) {
            $parsed['length'] = (int)$matches[2];
         } elseif ($parsed['baseType'] === 'DECIMAL') {
            $parts = explode(',', $matches[2]);
            $parsed['precision'] = (int)$parts[0];
            $parsed['scale'] = isset($parts[1]) ? (int)$parts[1] : 0;
         } elseif ($parsed['baseType'] === 'ENUM') {
            $parsed['values'] = $this->parseEnumValues($matches[2]);
         }
      } else {
         $parsed['baseType'] = strtoupper($column['Type']);
      }

      // Check for unsigned
      if (strpos($column['Type'], 'unsigned') !== false) {
         $parsed['unsigned'] = true;
      }

      // Check for auto increment
      if (strpos($column['Extra'], 'auto_increment') !== false) {
         $parsed['autoIncrement'] = true;
      }

      // Check for on update current timestamp
      if (strpos($column['Extra'], 'on update') !== false) {
         $parsed['onUpdate'] = true;
      }

      return $parsed;
   }

   /**
    * Parse column type
    */
   private function parseColumnType(string $type): string
   {
      // Remove unsigned, zerofill, etc.
      $type = preg_replace('/\s+(unsigned|zerofill)/i', '', $type);
      return $type;
   }

   /**
    * Parse ENUM values
    */
   private function parseEnumValues(string $values): array
   {
      // Remove quotes and split
      $values = str_replace("'", '', $values);
      return explode(',', $values);
   }

   /**
    * Get all indexes for a table
    */
   public function getIndexes(string $table): array
   {
      $stmt = $this->connection->query("SHOW INDEXES FROM `{$table}`");
      $rawIndexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $indexes = [];
      foreach ($rawIndexes as $index) {
         $keyName = $index['Key_name'];

         if (!isset($indexes[$keyName])) {
            $indexes[$keyName] = [
               'name' => $keyName,
               'columns' => [],
               'unique' => $index['Non_unique'] == 0,
               'primary' => $keyName === 'PRIMARY',
            ];
         }

         $indexes[$keyName]['columns'][] = $index['Column_name'];
      }

      return array_values($indexes);
   }

   /**
    * Get all foreign keys for a table
    */
   public function getForeignKeys(string $table): array
   {
      $database = $this->getDatabaseName();

      $sql = "SELECT 
                    CONSTRAINT_NAME as name,
                    COLUMN_NAME as column_name,
                    REFERENCED_TABLE_NAME as referenced_table,
                    REFERENCED_COLUMN_NAME as referenced_column,
                    UPDATE_RULE as on_update,
                    DELETE_RULE as on_delete
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = :database
                    AND TABLE_NAME = :table
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ORDER BY CONSTRAINT_NAME, ORDINAL_POSITION";

      $stmt = $this->connection->prepare($sql);
      $stmt->execute(['database' => $database, 'table' => $table]);
      $rawForeignKeys = $stmt->fetchAll(PDO::FETCH_ASSOC);

      $foreignKeys = [];
      foreach ($rawForeignKeys as $fk) {
         $name = $fk['name'];

         if (!isset($foreignKeys[$name])) {
            $foreignKeys[$name] = [
               'name' => $name,
               'columns' => [],
               'referencedTable' => $fk['referenced_table'],
               'referencedColumns' => [],
               'onUpdate' => $fk['on_update'],
               'onDelete' => $fk['on_delete'],
            ];
         }

         $foreignKeys[$name]['columns'][] = $fk['column_name'];
         $foreignKeys[$name]['referencedColumns'][] = $fk['referenced_column'];
      }

      return array_values($foreignKeys);
   }

   /**
    * Get table engine
    */
   public function getTableEngine(string $table): string
   {
      $info = $this->getTableInfo($table);
      return $info['ENGINE'] ?? 'InnoDB';
   }

   /**
    * Get table charset
    */
   public function getTableCharset(string $table): string
   {
      $info = $this->getTableInfo($table);

      if (isset($info['TABLE_COLLATION'])) {
         // Extract charset from collation (e.g., utf8mb4_unicode_ci -> utf8mb4)
         $parts = explode('_', $info['TABLE_COLLATION']);
         return $parts[0];
      }

      return 'utf8mb4';
   }

   /**
    * Get table collation
    */
   public function getTableCollation(string $table): string
   {
      $info = $this->getTableInfo($table);
      return $info['TABLE_COLLATION'] ?? 'utf8mb4_unicode_ci';
   }

   /**
    * Get table information
    */
   private function getTableInfo(string $table): array
   {
      $database = $this->getDatabaseName();

      $sql = "SELECT * FROM information_schema.TABLES 
                WHERE TABLE_SCHEMA = :database AND TABLE_NAME = :table";

      $stmt = $this->connection->prepare($sql);
      $stmt->execute(['database' => $database, 'table' => $table]);

      return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
   }

   /**
    * Get current database name
    */
   private function getDatabaseName(): string
   {
      $stmt = $this->connection->query("SELECT DATABASE()");
      return $stmt->fetchColumn();
   }

   /**
    * Check if table exists
    */
   public function tableExists(string $table): bool
   {
      $stmt = $this->connection->prepare("SHOW TABLES LIKE ?");
      $stmt->execute([$table]);
      return $stmt->fetch() !== false;
   }
}
