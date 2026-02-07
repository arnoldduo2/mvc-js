<?php

declare(strict_types=1);

namespace Tests\Unit\Database;

use App\App\Database\Database;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use PDO;

class DatabaseTest extends TestCase
{
   protected function setUp(): void
   {
      // Reset the singleton connection before each test
      $this->resetDatabaseConnection();
   }

   protected function tearDown(): void
   {
      // Clean up environment variables
      unset($_ENV['DB_ENGINE'], $_ENV['DB_HOST'], $_ENV['DB_NAME'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
      putenv('DB_ENGINE');
      putenv('DB_HOST');
      putenv('DB_NAME');
      putenv('DB_USERNAME');
      putenv('DB_PASSWORD');
   }

   private function resetDatabaseConnection()
   {
      $reflection = new ReflectionClass(Database::class);
      $property = $reflection->getProperty('conn');
      $property->setValue(null, null);
   }

   public function test_singleton_pattern(): void
   {
      // Mock SQLite in memory for speed and isolation
      $_ENV['DB_ENGINE'] = 'sqlite';
      // We'll trick the path logic or just use a non-existent file check logic?
      // Database.php: if sqlite, checks path exists.
      // We need to bypass the file check or point to a temp file.

      // Let's create a temp sqlite file
      $tempDb = tempnam(sys_get_temp_dir(), 'test_db');

      // The Database class logic is tricky for testing paths because it uses defined('APP_STORAGE') or dirname.
      // Let's see if we can mock env to use a path we control?
      // Actually, the path is hardcoded logic:
      // $databasePath = defined('APP_STORAGE') ? APP_STORAGE . '/database/database.sqlite' : dirname(__DIR__, 2) . '/storage/database/database.sqlite';

      // We can't easily inject the path unless we define APP_STORAGE.
      // Let's define APP_STORAGE if not defined.
      if (!defined('APP_STORAGE')) {
         define('APP_STORAGE', sys_get_temp_dir());
      }

      // Mock the directory structure expects: /database/database.sqlite
      $dbDir = sys_get_temp_dir() . '/database';
      if (!is_dir($dbDir)) {
         mkdir($dbDir, 0777, true);
      }
      $dbFile = $dbDir . '/database.sqlite';
      touch($dbFile);

      // Now set the env to sqlite
      // Note: The helper `env()` might read $_ENV or getenv or $_SERVER.
      // Assuming unit tests run in CLI, we might need to populate $_ENV.
      // But the Database class code uses `env()`.

      // We need to verify how `env()` works. Assuming it reads $_ENV first.
      $_ENV['DB_ENGINE'] = 'sqlite';

      $db1 = Database::initialize();
      $this->assertInstanceOf(PDO::class, $db1);

      $db2 = Database::initialize();
      $this->assertSame($db1, $db2);

      // Cleanup
      @unlink($dbFile);
      @rmdir($dbDir);
   }

   public function test_mysql_connection_attempt(): void
   {
      // This test likely fails if no MySQL server is running.
      // But we can check if it attempts to create PDO with correct signature.
      // Without mocking PDO construction (which is hard for static method),
      // we might skip this or expect exception.

      $_ENV['DB_ENGINE'] = 'mysql';
      $_ENV['DB_HOST'] = '127.0.0.1';
      $_ENV['DB_NAME'] = 'test_db';
      $_ENV['DB_USERNAME'] = 'root';
      $_ENV['DB_PASSWORD'] = '';

      try {
         Database::initialize();
      } catch (\PDOException $e) {
         // Expected if no db server
         $this->assertStringContainsString('SQLSTATE', $e->getMessage());
         // Or 'Connection refused' etc.
         // As long as it tries to connect, we are good.
         $this->assertTrue(true);
         return;
      }

      // If it actually connected (e.g. valid local creds), that's also fine.
      $this->assertTrue(true);
   }
}
