<?php
/**
 * Input validation functions
 */

require_once __DIR__ . '/security.php';

/**
 * Validate required POST fields
 */
function validateRequired(array $fields, array $source = null): array {
    if ($source === null) {
        $source = $_POST;
    }

    $errors = [];
    foreach ($fields as $field => $label) {
        if (!isset($source[$field]) || trim($source[$field]) === '') {
            $errors[$field] = "$label harus diisi";
        }
    }
    return $errors;
}

/**
 * Get POST value with default
 */
function post(string $key, $default = '') {
    return isset($_POST[$key]) ? sanitizeString($_POST[$key]) : $default;
}

/**
 * Get GET value with default
 */
function get(string $key, $default = '') {
    return isset($_GET[$key]) ? sanitizeString($_GET[$key]) : $default;
}

/**
 * Get integer POST value
 */
function postInt(string $key, int $default = 0): int {
    return isset($_POST[$key]) ? sanitizeInt($_POST[$key], $default) : $default;
}

/**
 * Get integer GET value
 */
function getInt(string $key, int $default = 0): int {
    return isset($_GET[$key]) ? sanitizeInt($_GET[$key], $default) : $default;
}

/**
 * Get positive integer POST value
 */
function postPositiveInt(string $key, int $default = 0): int {
    return isset($_POST[$key]) ? sanitizePositiveInt($_POST[$key], $default) : $default;
}

/**
 * Get positive integer GET value
 */
function getPositiveInt(string $key, int $default = 0): int {
    return isset($_GET[$key]) ? sanitizePositiveInt($_GET[$key], $default) : $default;
}

/**
 * Validate transaction ID format (TRSddmmyyHHiiss)
 */
function validateTransactionId(string $id): bool {
    return preg_match('/^TRS\d{12}$/', $id) === 1;
}

/**
 * Validate payment method
 */
function validatePaymentMethod(string $method): bool {
    return in_array($method, ['Transfer', 'Tunai'], true);
}

/**
 * Validate that quantity is positive and within stock
 */
function validateQuantity(int $quantity, int $stock): bool {
    return $quantity > 0 && $quantity <= $stock;
}
