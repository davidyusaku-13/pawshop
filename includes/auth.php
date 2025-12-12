<?php
/**
 * Authentication functions
 * Handles user sessions, login, logout, and remember me functionality
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/security.php';

// Session configuration constants
define('SESSION_LIFETIME', 3600); // 1 hour
define('REMEMBER_ME_DAYS', 30);
define('REMEMBER_ME_COOKIE', 'remember_token');

/**
 * Initialize secure session
 */
function initSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        // Secure session settings
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.gc_maxlifetime', (string) SESSION_LIFETIME);

        // Use secure cookies in production (HTTPS)
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            ini_set('session.cookie_secure', '1');
        }

        session_start();

        // Regenerate session ID periodically
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }
}

/**
 * Get current user ID from session or remember token
 */
function getCurrentUserId(): ?int {
    initSession();

    // Check session first
    if (isset($_SESSION['userid']) && !empty($_SESSION['userid'])) {
        return (int) $_SESSION['userid'];
    }

    // Check remember me cookie
    if (isset($_COOKIE[REMEMBER_ME_COOKIE]) && !empty($_COOKIE[REMEMBER_ME_COOKIE])) {
        $token = $_COOKIE[REMEMBER_ME_COOKIE];
        $user = validateRememberToken($token);
        if ($user) {
            // Restore session from remember token
            $_SESSION['userid'] = $user['id'];
            $_SESSION['privilege'] = $user['privilege'];
            $_SESSION['username'] = $user['username'];
            return (int) $user['id'];
        } else {
            // Invalid token, clear cookie
            clearRememberToken();
        }
    }

    return null;
}

/**
 * Get current user privilege
 */
function getCurrentUserPrivilege(): string {
    initSession();

    if (isset($_SESSION['privilege']) && !empty($_SESSION['privilege'])) {
        return $_SESSION['privilege'];
    }

    // Try to restore from remember token
    $userId = getCurrentUserId();
    if ($userId && isset($_SESSION['privilege'])) {
        return $_SESSION['privilege'];
    }

    return '';
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return getCurrentUserId() !== null;
}

/**
 * Check if current user is admin
 */
function isAdmin(): bool {
    return getCurrentUserPrivilege() === 'admin';
}

/**
 * Require user to be logged in
 */
function requireLogin(string $redirect = 'login.php'): void {
    if (!isLoggedIn()) {
        header("Location: $redirect");
        exit;
    }
}

/**
 * Require user to be admin
 */
function requireAdmin(string $redirect = 'index.php'): void {
    if (!isAdmin()) {
        header("Location: $redirect");
        exit;
    }
}

/**
 * Authenticate user with username and password
 * Returns user data on success, null on failure
 */
function authenticateUser(string $username, string $password): ?array {
    $sql = "SELECT id, username, password, privilege FROM users WHERE username = ?";
    $user = dbFetchOne($sql, 's', [$username]);

    if (!$user) {
        return null;
    }

    $verification = verifyPassword($password, $user['password']);

    if ($verification === 'invalid') {
        return null;
    }

    // Upgrade password if needed
    if ($verification === 'needs_upgrade') {
        $newHash = hashPassword($password);
        dbQuery(
            "UPDATE users SET password = ? WHERE id = ?",
            'si',
            [$newHash, $user['id']]
        );
    }

    return $user;
}

/**
 * Log in user and create session
 */
function loginUser(array $user, bool $remember = false): void {
    initSession();

    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['userid'] = $user['id'];
    $_SESSION['privilege'] = $user['privilege'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['_created'] = time();

    if ($remember) {
        createRememberToken($user['id']);
    }
}

/**
 * Log out user
 */
function logoutUser(): void {
    initSession();

    // Clear remember token from database if exists
    if (isset($_COOKIE[REMEMBER_ME_COOKIE])) {
        $token = $_COOKIE[REMEMBER_ME_COOKIE];
        dbQuery(
            "UPDATE users SET remember_token = '' WHERE remember_token = ?",
            's',
            [$token]
        );
        clearRememberToken();
    }

    // Clear session
    $_SESSION = [];

    // Delete session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

/**
 * Create remember me token
 */
function createRememberToken(int $userId): void {
    $token = bin2hex(random_bytes(32));
    $hashedToken = hash('sha256', $token);

    dbQuery(
        "UPDATE users SET remember_token = ? WHERE id = ?",
        'si',
        [$hashedToken, $userId]
    );

    $expires = time() + (86400 * REMEMBER_ME_DAYS);
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

    setcookie(REMEMBER_ME_COOKIE, $token, [
        'expires' => $expires,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Validate remember me token
 */
function validateRememberToken(string $token): ?array {
    $hashedToken = hash('sha256', $token);

    return dbFetchOne(
        "SELECT id, username, privilege FROM users WHERE remember_token = ? AND remember_token != ''",
        's',
        [$hashedToken]
    );
}

/**
 * Clear remember me token cookie
 */
function clearRememberToken(): void {
    setcookie(REMEMBER_ME_COOKIE, '', [
        'expires' => time() - 3600,
        'path' => '/',
        'domain' => '',
        'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

/**
 * Register new user
 */
function registerUser(string $username, string $password, string $phoneNumber, string $privilege = 'user'): array {
    // Validate input
    if (!validateUsername($username)) {
        return ['success' => false, 'error' => 'Username tidak valid (3-40 karakter alfanumerik)'];
    }

    $passwordErrors = validatePassword($password);
    if (!empty($passwordErrors)) {
        return ['success' => false, 'error' => implode(', ', $passwordErrors)];
    }

    if (!validatePhoneNumber($phoneNumber)) {
        return ['success' => false, 'error' => 'Nomor telepon tidak valid'];
    }

    // Check if username exists
    $existing = dbFetchOne("SELECT id FROM users WHERE username = ?", 's', [$username]);
    if ($existing) {
        return ['success' => false, 'error' => 'Username sudah digunakan'];
    }

    // Create user
    $hashedPassword = hashPassword($password);

    $result = dbQuery(
        "INSERT INTO users (username, password, phone_number, remember_token, privilege) VALUES (?, ?, ?, '', ?)",
        'ssss',
        [$username, $hashedPassword, $phoneNumber, $privilege]
    );

    if ($result) {
        return ['success' => true, 'user_id' => dbLastInsertId()];
    }

    return ['success' => false, 'error' => 'Gagal membuat akun'];
}
