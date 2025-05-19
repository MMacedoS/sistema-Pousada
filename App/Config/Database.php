<?php

namespace App\Config;

use PDO;

class Database {
    private static $instance = null;
    private ?PDO $pdo; 

    private function __construct() {
        $this->pdo = new PDO(
            'mysql:host=' . $_ENV['DB_HOST'] . ';dbname=' . $_ENV['DB_NAME'], 
            $_ENV['DB_USER'], 
            $_ENV['DB_PASS']
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    public static function getInstance(): self {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): ?PDO {
        return $this->pdo;
    }

    public function closeConnection(): void {
        $this->pdo = null; 
    }
}
