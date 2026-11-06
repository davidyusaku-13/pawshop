<?php

namespace App;

class AuthService
{
    public static function login(string $username, string $password, bool $rememberMe = false): bool
    {
        $user = Database::fetchOne(
            "SELECT id, username, password, privilege FROM users WHERE username = ?",
            [$username]
        );

        if (!$user) {
            return false;
        }

        $isValid = false;

        // Modern password_verify
        if (password_verify($password, $user['password'])) {
            $isValid = true;
        } elseif (strlen($user['password']) === 32 && md5($password) === $user['password']) {
            // Transparent upgrade from legacy MD5 to modern bcrypt/argon2
            $isValid = true;
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            Database::execute("UPDATE users SET password = ? WHERE id = ?", [$newHash, $user['id']]);
        }

        if (!$isValid) {
            return false;
        }

        // Regenerate session ID to prevent session fixation attacks
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            session_regenerate_id(true);
        }

        $_SESSION['userid'] = (int)$user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['privilege'] = $user['privilege'];

        return true;
    }

    public static function register(string $username, string $password, string $phone, string $privilege = 'user'): bool
    {
        $existing = Database::fetchOne("SELECT id FROM users WHERE username = ?", [$username]);
        if ($existing) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        return Database::execute(
            "INSERT INTO users (username, password, phone_number, privilege) VALUES (?, ?, ?, ?)",
            [$username, $hash, $phone, $privilege]
        );
    }

    public static function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    public static function changePassword(int $userId, string $oldPassword, string $newPassword): bool
    {
        $user = Database::fetchOne("SELECT password FROM users WHERE id = ?", [$userId]);
        if (!$user) {
            return false;
        }

        $isValid = false;
        if (password_verify($oldPassword, $user['password'])) {
            $isValid = true;
        } elseif (strlen($user['password']) === 32 && md5($oldPassword) === $user['password']) {
            $isValid = true;
        }

        if (!$isValid) {
            return false;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return Database::execute("UPDATE users SET password = ? WHERE id = ?", [$newHash, $userId]);
    }

    public static function adminResetPassword(int $userId, string $newPassword): bool
    {
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        return Database::execute("UPDATE users SET password = ? WHERE id = ?", [$newHash, $userId]);
    }

    public static function updatePrivilege(int $userId, string $privilege): bool
    {
        if (!in_array($privilege, ['admin', 'user'], true)) {
            return false;
        }
        return Database::execute("UPDATE users SET privilege = ? WHERE id = ?", [$privilege, $userId]);
    }

    public static function deleteUser(int $userId, int $currentUserId): bool
    {
        if ($userId === $currentUserId) {
            return false; // Prevent deleting active admin
        }
        return Database::execute("DELETE FROM users WHERE id = ?", [$userId]);
    }

    public static function getUserById(int $userId): ?array
    {
        return Database::fetchOne("SELECT id, username, phone_number, privilege FROM users WHERE id = ?", [$userId]);
    }

    public static function getAllUsers(): array
    {
        return Database::query("SELECT id, username, phone_number, privilege FROM users ORDER BY id ASC");
    }

    public static function getCurrentUserId(): ?int
    {
        return isset($_SESSION['userid']) ? (int)$_SESSION['userid'] : null;
    }

    public static function getCurrentUser(): ?array
    {
        $id = self::getCurrentUserId();
        return $id ? self::getUserById($id) : null;
    }

    public static function isLoggedIn(): bool
    {
        return !empty($_SESSION['userid']);
    }

    public static function isAdmin(): bool
    {
        return self::isLoggedIn() && ($_SESSION['privilege'] ?? '') === 'admin';
    }

    public static function requireAuth(): void
    {
        if (!self::isLoggedIn()) {
            redirect('auth/login.php');
        }
    }

    public static function requireAdmin(): void
    {
        if (!self::isAdmin()) {
            redirect('auth/login.php');
        }
    }
}
