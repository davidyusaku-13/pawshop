<?php
/**
 * Main configuration file
 * This is the new secure config that replaces the old config.php
 */

// Set timezone
date_default_timezone_set("Asia/Jakarta");

// Include security infrastructure
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/csrf.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/validation.php';

// Initialize session securely
initSession();

// Set security headers
setSecurityHeaders();

// Get database connection (for backward compatibility with existing code)
$conn = getDB();

// Get current user info (for backward compatibility)
$userid = getCurrentUserId();
$privilege = getCurrentUserPrivilege();

// Constants
define('MIN_STOCK_ALERT', 20);
define('UPLOAD_DIR', __DIR__ . '/img/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

/**
 * Admin secret key for registration
 *
 * SECURITY: This MUST be set via environment variable, not hardcoded.
 * Generate a secure random key (min 32 chars) using:
 *   php -r "echo bin2hex(random_bytes(32));"
 *
 * Set it in your environment:
 *   - Apache: SetEnv PAWSHOP_ADMIN_SECRET "your-secret-key"
 *   - Nginx/PHP-FPM: env[PAWSHOP_ADMIN_SECRET] = "your-secret-key"
 *   - .env file (excluded from VCS): PAWSHOP_ADMIN_SECRET=your-secret-key
 *   - System environment: export PAWSHOP_ADMIN_SECRET="your-secret-key"
 *
 * NEVER commit the actual secret to version control.
 */
$adminSecretKey = getenv('PAWSHOP_ADMIN_SECRET');
if ($adminSecretKey === false || $adminSecretKey === '') {
    $adminSecretKey = $_ENV['PAWSHOP_ADMIN_SECRET'] ?? '';
}

if (empty($adminSecretKey)) {
    error_log('CRITICAL: PAWSHOP_ADMIN_SECRET environment variable is not set');
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        die('Server configuration error. Please contact administrator.');
    }
}

// Validate minimum entropy (at least 32 characters for sufficient security)
if (strlen($adminSecretKey) < 32) {
    error_log('CRITICAL: PAWSHOP_ADMIN_SECRET is too short (min 32 chars required)');
    if (php_sapi_name() !== 'cli') {
        http_response_code(500);
        die('Server configuration error. Please contact administrator.');
    }
}

define('ADMIN_SECRET_KEY', $adminSecretKey);
unset($adminSecretKey); // Clear from memory
