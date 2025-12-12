<?php
/**
 * CSRF Protection
 * Provides token generation and validation
 */

/**
 * Generate CSRF token and store in session
 */
function csrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get HTML hidden input for CSRF token
 */
function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">';
}

/**
 * Validate CSRF token from request
 */
function csrfValidate(?string $token = null): bool {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    }

    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate CSRF token and die on failure
 */
function csrfVerify(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrfValidate()) {
        http_response_code(403);
        die('Invalid CSRF token. Please refresh the page and try again.');
    }
}

/**
 * Regenerate CSRF token (call after sensitive actions)
 */
function csrfRegenerate(): string {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
