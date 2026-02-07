<?php

declare(strict_types=1);

namespace App\App\Database;

use PDO;
use Exception;

/**
 * Migration Manager
 * 
 * Handles the execution, rollback, and tracking of database migrations.
 */
class MigrationManager
{
   private PDO $connection;
   private string $migrationsPath;
   private string $migrationsTable = 'migrations';

   public function __construct(?PDO $connection = null, ?string $migrationsPath = null)
   {
      $this->connection = $connection ?? Database::initialize();
      $this->migrationsPath = $migrationsPath ?? $this->getDefaultMigrationsPath();
      $this->ensureMigrationsTableExists();
   }

   /**
    * Get default migrations path
    */
   private function getDefaultMigrationsPath(): string
   {
      return dirname(__DIR__, 4) . '/src/database/migrations';
   }

   /**
    * Ensure the migrations table exists
    */
   private function ensureMigrationsTableExists(): void
   {
      $sql = "CREATE TABLE IF NOT EXISTS `{$this->migrationsTable}` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL,
            `batch` INT NOT NULL,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `unique_migration` (`migration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

      $this->connection->exec($sql);
   }

   /**
    * Run all pending migrations
    */
   public function run(?int $step = null): array
   {
      $executed = [];
      $pending = $this->getPendingMigrations();

      if (empty($pending)) {
         return $executed;
      }

      // Limit by step if specified
      if ($step !== null) {
         $pending = array_slice($pending, 0, $step);
      }

      $batch = $this->getNextBatchNumber();

      foreach ($pending as $migrationFile) {
         try {
            $this->runMigration($migrationFile, $batch);
            $executed[] = $migrationFile;
         } catch (Exception $e) {
            throw new Exception("Migration failed: {$migrationFile}\n{$e->getMessage()}");
         }
      }

      return $executed;
   }

   /**
    * Rollback the last batch of migrations
    */
   public function rollback(?int $step = null): array
   {
      $rolledBack = [];
      $migrations = $this->getLastBatchMigrations($step);

      if (empty($migrations)) {
         return $rolledBack;
      }

      foreach (array_reverse($migrations) as $migration) {
         try {
            $this->rollbackMigration($migration['migration']);
            $rolledBack[] = $migration['migration'];
         } catch (Exception $e) {
            throw new Exception("Rollback failed: {$migration['migration']}\n{$e->getMessage()}");
         }
      }

      return $rolledBack;
   }

   /**
    * Reset all migrations (rollback everything)
    */
   public function reset(): array
   {
      $rolledBack = [];
      $allMigrations = $this->getExecutedMigrations();

      foreach (array_reverse($allMigrations) as $migration) {
         try {
            $this->rollbackMigration($migration['migration']);
            $rolledBack[] = $migration['migration'];
         } catch (Exception $e) {
            throw new Exception("Reset failed: {$migration['migration']}\n{$e->getMessage()}");
         }
      }

      return $rolledBack;
   }

   /**
    * Get migration status
    */
   public function status(): array
   {
      $allFiles = $this->getAllMigrationFiles();
      $executed = $this->getExecutedMigrations();
      $executedMap = [];

      foreach ($executed as $migration) {
         $executedMap[$migration['migration']] = [
            'batch' => $migration['batch'],
            'executed_at' => $migration['executed_at']
         ];
      }

      $status = [];
      foreach ($allFiles as $file) {
         $status[] = [
            'migration' => $file,
            'ran' => isset($executedMap[$file]),
            'batch' => $executedMap[$file]['batch'] ?? null,
            'executed_at' => $executedMap[$file]['executed_at'] ?? null
         ];
      }

      return $status;
   }

   /**
    * Run a single migration
    */
   private function runMigration(string $migrationFile, int $batch): void
   {
      $migration = $this->loadMigration($migrationFile);

      // Begin transaction
      $this->connection->beginTransaction();

      try {
         // Run the migration
         $migration->up();

         // Record in migrations table
         $stmt = $this->connection->prepare(
            "INSERT INTO `{$this->migrationsTable}` (`migration`, `batch`) VALUES (?, ?)"
         );
         $stmt->execute([$migrationFile, $batch]);

         // Commit transaction
         $this->connection->commit();
      } catch (Exception $e) {
         $this->connection->rollBack();
         throw $e;
      }
   }

   /**
    * Rollback a single migration
    */
   private function rollbackMigration(string $migrationFile): void
   {
      $migration = $this->loadMigration($migrationFile);

      // Begin transaction
      $this->connection->beginTransaction();

      try {
         // Rollback the migration
         $migration->down();

         // Remove from migrations table
         $stmt = $this->connection->prepare(
            "DELETE FROM `{$this->migrationsTable}` WHERE `migration` = ?"
         );
         $stmt->execute([$migrationFile]);

         // Commit transaction
         $this->connection->commit();
      } catch (Exception $e) {
         $this->connection->rollBack();
         throw $e;
      }
   }

   /**
    * Load a migration instance
    */
   private function loadMigration(string $migrationFile): Migration
   {
      $path = $this->migrationsPath . '/' . $migrationFile;

      if (!file_exists($path)) {
         throw new Exception("Migration file not found: {$path}");
      }

      require_once $path;

      // Extract class name from filename
      // Format: YYYYMMDDHHMMSS_description.php
      $className = $this->getClassNameFromFile($migrationFile);

      if (!class_exists($className)) {
         throw new Exception("Migration class not found: {$className}");
      }

      return new $className();
   }

   /**
    * Get class name from migration filename
    */
   private function getClassNameFromFile(string $filename): string
   {
      // Remove .php extension
      $name = str_replace('.php', '', $filename);

      // Format: Migration_YYYYMMDDHHMMSS_description
      return 'Migration_' . $name;
   }

   /**
    * Get all migration files
    */
   private function getAllMigrationFiles(): array
   {
      if (!is_dir($this->migrationsPath)) {
         return [];
      }

      $files = scandir($this->migrationsPath);
      $migrations = [];

      foreach ($files as $file) {
         if (preg_match('/^\d{14}_.*\.php$/', $file)) {
            $migrations[] = $file;
         }
      }

      sort($migrations);
      return $migrations;
   }

   /**
    * Get pending migrations
    */
   private function getPendingMigrations(): array
   {
      $all = $this->getAllMigrationFiles();
      $executed = $this->getExecutedMigrations();
      $executedNames = array_column($executed, 'migration');

      return array_values(array_diff($all, $executedNames));
   }

   /**
    * Get executed migrations
    */
   private function getExecutedMigrations(): array
   {
      $stmt = $this->connection->query(
         "SELECT * FROM `{$this->migrationsTable}` ORDER BY `batch` ASC, `id` ASC"
      );
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   /**
    * Get migrations from the last batch(es)
    */
   private function getLastBatchMigrations(?int $step = null): array
   {
      if ($step === null) {
         $step = 1;
      }

      // Get the last N batch numbers
      $stmt = $this->connection->query(
         "SELECT DISTINCT `batch` FROM `{$this->migrationsTable}` ORDER BY `batch` DESC LIMIT {$step}"
      );
      $batches = $stmt->fetchAll(PDO::FETCH_COLUMN);

      if (empty($batches)) {
         return [];
      }

      $placeholders = implode(',', array_fill(0, count($batches), '?'));
      $stmt = $this->connection->prepare(
         "SELECT * FROM `{$this->migrationsTable}` WHERE `batch` IN ({$placeholders}) ORDER BY `id` ASC"
      );
      $stmt->execute($batches);

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   /**
    * Get next batch number
    */
   private function getNextBatchNumber(): int
   {
      $stmt = $this->connection->query(
         "SELECT MAX(`batch`) as max_batch FROM `{$this->migrationsTable}`"
      );
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      return ((int)($result['max_batch'] ?? 0)) + 1;
   }

   /**
    * Get migrations path
    */
   public function getMigrationsPath(): string
   {
      return $this->migrationsPath;
   }

   /**
    * Mark a migration as executed (for sync purposes)
    */
   public function markAsExecuted(string $migrationFile, ?int $batch = null): void
   {
      if ($batch === null) {
         $batch = $this->getNextBatchNumber();
      }

      $stmt = $this->connection->prepare(
         "INSERT IGNORE INTO `{$this->migrationsTable}` (`migration`, `batch`) VALUES (?, ?)"
      );
      $stmt->execute([$migrationFile, $batch]);
   }
}
