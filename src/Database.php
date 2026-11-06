<?php

namespace App;

use PDO;
use PDOException;
use mysqli;
use Exception;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $pdo = null;
    private ?mysqli $mysqli = null;
    private bool $usePdo = false;

    public function __construct(bool $connect = true)
    {
        if (!$connect) {
            return;
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $name = getenv('DB_NAME') ?: 'pawshop';
        $port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

        // Try PDO if pdo_mysql is loaded
        if (extension_loaded('pdo_mysql')) {
            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                $this->pdo = new PDO($dsn, $user, $pass, $options);
                $this->usePdo = true;
                return;
            } catch (PDOException $e) {
                // If PDO connection fails, fallback to mysqli if available
            }
        }

        // Fallback to MySQLi driver
        if (extension_loaded('mysqli')) {
            $this->mysqli = new mysqli($host, $user, $pass, $name, $port);
            if ($this->mysqli->connect_error) {
                throw new \RuntimeException("Database connection failed: " . $this->mysqli->connect_error);
            }
            $this->mysqli->set_charset("utf8mb4");
            $this->usePdo = false;
            return;
        }

        throw new \RuntimeException("Database connection failed: Neither PDO (pdo_mysql) nor MySQLi extension is installed.");
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function createWithPdo(PDO $pdo): Database
    {
        $db = new self(false);
        $db->pdo = $pdo;
        $db->usePdo = true;
        self::$instance = $db;
        return $db;
    }

    public static function setInstance(?Database $instance): void
    {
        self::$instance = $instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    private function prepareSql(string $sql): string
    {
        if ($this->usePdo && $this->pdo && $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
            return preg_replace('/\s+FOR\s+UPDATE\s*$/i', '', $sql);
        }
        return $sql;
    }

    public static function query(string $sql, array $params = []): array
    {
        $db = self::getInstance();
        $sql = $db->prepareSql($sql);
        if ($db->usePdo && $db->pdo) {
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        }

        if ($db->mysqli) {
            $stmt = $db->mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Query error: " . $db->mysqli->error);
            }
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            $result = $stmt->get_result();
            return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
        }

        return [];
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $db = self::getInstance();
        $sql = $db->prepareSql($sql);
        if ($db->usePdo && $db->pdo) {
            $stmt = $db->pdo->prepare($sql);
            $stmt->execute($params);
            $res = $stmt->fetch();
            return $res ?: null;
        }

        if ($db->mysqli) {
            $stmt = $db->mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Query error: " . $db->mysqli->error);
            }
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            $result = $stmt->get_result();
            if ($result) {
                $row = $result->fetch_assoc();
                return $row ?: null;
            }
        }

        return null;
    }

    public static function execute(string $sql, array $params = []): bool
    {
        $db = self::getInstance();
        $sql = $db->prepareSql($sql);
        if ($db->usePdo && $db->pdo) {
            $stmt = $db->pdo->prepare($sql);
            return $stmt->execute($params);
        }

        if ($db->mysqli) {
            $stmt = $db->mysqli->prepare($sql);
            if (!$stmt) {
                throw new Exception("Query error: " . $db->mysqli->error);
            }
            if (!empty($params)) {
                return $stmt->execute($params);
            }
            return $stmt->execute();
        }

        return false;
    }

    public static function lastInsertId(): string
    {
        $db = self::getInstance();
        if ($db->usePdo && $db->pdo) {
            return $db->pdo->lastInsertId();
        }
        if ($db->mysqli) {
            return (string)$db->mysqli->insert_id;
        }
        return '0';
    }

    public static function beginTransaction(): bool
    {
        $db = self::getInstance();
        if ($db->usePdo && $db->pdo) {
            return $db->pdo->beginTransaction();
        }
        if ($db->mysqli) {
            return $db->mysqli->begin_transaction();
        }
        return false;
    }

    public static function commit(): bool
    {
        $db = self::getInstance();
        if ($db->usePdo && $db->pdo) {
            return $db->pdo->commit();
        }
        if ($db->mysqli) {
            return $db->mysqli->commit();
        }
        return false;
    }

    public static function rollBack(): bool
    {
        $db = self::getInstance();
        if ($db->usePdo && $db->pdo) {
            if ($db->pdo->inTransaction()) {
                return $db->pdo->rollBack();
            }
            return false;
        }
        if ($db->mysqli) {
            return $db->mysqli->rollback();
        }
        return false;
    }
}
