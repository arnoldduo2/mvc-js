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
            $dsn = DB_ENG . ':host=' . DB_HOST . ';dbname=' . DB_NAME;
            $dbuser = DB_USER;
            $dbpass = DB_PASS;
            try {
                self::$conn = new PDO($dsn, $dbuser, $dbpass);
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                // Return the connection
                return self::$conn;
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage());
            }
        }
        return null;
    }
}