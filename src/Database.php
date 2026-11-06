<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $name = getenv('DB_NAME') ?: 'pawshop';
        $port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function getPdo(): PDO
    {
        return self::getInstance()->pdo;
    }

    public static function query(string $sql, array $params = []): array
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($params);
    }

    public static function lastInsertId(): string
    {
        return self::getPdo()->lastInsertId();
    }

    public static function beginTransaction(): bool
    {
        return self::getPdo()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getPdo()->commit();
    }

    public static function rollBack(): bool
    {
        if (self::getPdo()->inTransaction()) {
            return self::getPdo()->rollBack();
        }
        return false;
    }
}
