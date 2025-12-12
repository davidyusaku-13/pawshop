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

// Admin secret key for registration (hashed with password_hash in production)
define('ADMIN_SECRET_KEY', '2023');
