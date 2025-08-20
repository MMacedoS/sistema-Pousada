<?php

namespace App\Config;

use PDO;

class Database extends SingletonInstance
{
    private ?PDO $pdo;

    protected function __construct()
    {
        try {
            if (!isset($_ENV['DB_HOST'])) {
                $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
                $dotenv->load();
            }

            $dbHost = $_ENV['DB_HOST'];
            $dbName = $_ENV['DB_NAME'];
            $dbUser = $_ENV['DB_USER'];
            $dbPass = $_ENV['DB_PASS'];

            if (!$dbHost || !$dbName || !$dbUser) {
                throw new \Exception("Variáveis de ambiente do banco de dados não carregadas. Host: $dbHost, Name: $dbName, User: $dbUser");
            }

            $this->pdo = new PDO(
                "mysql:host=$dbHost;dbname=$dbName",
                $dbUser,
                $dbPass
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Erro na conexão PDO: " . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }
}
