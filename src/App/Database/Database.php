<?php

declare(strict_types=1);

namespace App\App\Database;

use PDO;
use PDOException;

class Database
{
    private static $conn = null;

    // Static method to initialize the database connection
    public static function initialize(): ?PDO
    {
        if (self::$conn === null) {
            try {
                $engine = env('DB_ENGINE', 'mysql');
                $host = env('DB_HOST', '127.0.0.1');
                $name = env('DB_NAME', 'mvc_js');
                $user = env('DB_USERNAME', 'root');
                $pass = env('DB_PASSWORD', '');

                if ($engine === 'sqlite') {
                    $databasePath = defined('APP_STORAGE') ? APP_STORAGE . '/database/database.sqlite' : dirname(__DIR__, 2) . '/storage/database/database.sqlite';

                    if (!file_exists($databasePath)) {
                        throw new PDOException("SQLite database file not found: $databasePath");
                    }

                    $dsn = "sqlite:$databasePath";
                    self::$conn = new PDO($dsn);
                } else {
                    $dsn = "{$engine}:host={$host};dbname={$name}";
                    self::$conn = new PDO($dsn, $user, $pass);
                }

                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                return self::$conn;
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }
        return self::$conn;
    }
}
