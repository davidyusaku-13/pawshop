<?php
/**
 * Database connection and helper functions
 * Uses prepared statements for all queries
 */

// Environment configuration - use environment variables in production
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'pawshop');

/**
 * Get database connection (singleton pattern)
 */
function getDB(): mysqli {
    static $conn = null;

    if ($conn === null) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $conn->set_charset('utf8mb4');
            $conn->autocommit(true);
        } catch (mysqli_sql_exception $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Koneksi database gagal. Silakan hubungi administrator.");
        }
    }

    return $conn;
}

/**
 * Execute a prepared statement with parameters
 * @param string $sql SQL query with ? placeholders
 * @param string $types Parameter types (i=int, s=string, d=double, b=blob)
 * @param array $params Array of parameters
 * @return mysqli_result|bool
 */
function dbQuery(string $sql, string $types = '', array $params = []): mysqli_result|bool {
    $conn = getDB();

    if (empty($types) && empty($params)) {
        return $conn->query($sql);
    }

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }

    $result = $stmt->get_result();
    return $result !== false ? $result : true;
}

/**
 * Fetch single row from prepared statement
 */
function dbFetchOne(string $sql, string $types = '', array $params = []): ?array {
    $result = dbQuery($sql, $types, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_assoc();
    }
    return null;
}

/**
 * Fetch all rows from prepared statement
 */
function dbFetchAll(string $sql, string $types = '', array $params = []): array {
    $result = dbQuery($sql, $types, $params);
    if ($result instanceof mysqli_result) {
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    return [];
}

/**
 * Get last insert ID
 */
function dbLastInsertId(): int {
    return getDB()->insert_id;
}

/**
 * Get affected rows from last query
 */
function dbAffectedRows(): int {
    return getDB()->affected_rows;
}

/**
 * Begin transaction
 */
function dbBeginTransaction(): bool {
    return getDB()->begin_transaction();
}

/**
 * Commit transaction
 */
function dbCommit(): bool {
    return getDB()->commit();
}

/**
 * Rollback transaction
 */
function dbRollback(): bool {
    return getDB()->rollback();
}

/**
 * Execute multiple queries in a transaction
 * @param callable $callback Function to execute within transaction
 * @return bool Success status
 */
function dbTransaction(callable $callback): bool {
    $conn = getDB();

    try {
        $conn->begin_transaction();
        $result = $callback($conn);

        if ($result === false) {
            $conn->rollback();
            return false;
        }

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Transaction failed: " . $e->getMessage());
        return false;
    }
}
