<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Database;
use App\AuthService;
use PDO;

class AuthServiceTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        parent::setUp();
        $_SESSION = [];

        $this->pdo = new PDO('sqlite::memory:', null, null, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->pdo->exec("
            CREATE TABLE users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE,
                password TEXT,
                phone_number TEXT,
                privilege TEXT
            );
        ");

        Database::createWithPdo($this->pdo);
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
        Database::reset();
        parent::tearDown();
    }

    public function testRegisterCreatesNewUserWithSecurePasswordHash(): void
    {
        $registered = AuthService::register('alice', 'secret123', '08123456789', 'user');
        $this->assertTrue($registered);

        $user = Database::fetchOne("SELECT * FROM users WHERE username = 'alice'");
        $this->assertNotNull($user);
        $this->assertSame('alice', $user['username']);
        $this->assertSame('08123456789', $user['phone_number']);
        $this->assertSame('user', $user['privilege']);

        // Verify password is NOT stored in plain text
        $this->assertNotSame('secret123', $user['password']);
        $this->assertTrue(password_verify('secret123', $user['password']));
    }

    public function testRegisterRejectsDuplicateUsername(): void
    {
        AuthService::register('bob', 'secret123', '08123456789', 'user');

        // Second registration with same username must fail
        $duplicate = AuthService::register('bob', 'anotherpassword', '08987654321', 'user');
        $this->assertFalse($duplicate);
    }

    public function testLoginSucceedsWithValidCredentialsAndSetsSession(): void
    {
        AuthService::register('charlie', 'mypassword', '08111222333', 'admin');

        $loggedIn = AuthService::login('charlie', 'mypassword');
        $this->assertTrue($loggedIn);

        // Check session population
        $this->assertSame('charlie', $_SESSION['username']);
        $this->assertSame('admin', $_SESSION['privilege']);
        $this->assertTrue(AuthService::isLoggedIn());
        $this->assertTrue(AuthService::isAdmin());
    }

    public function testLoginRejectsInvalidPassword(): void
    {
        AuthService::register('david', 'correctpass', '08222333444', 'user');

        $loggedIn = AuthService::login('david', 'wrongpass');
        $this->assertFalse($loggedIn);
        $this->assertFalse(AuthService::isLoggedIn());
    }

    public function testChangePasswordVerifiesOldPassword(): void
    {
        AuthService::register('eva', 'oldpass123', '08333444555', 'user');
        $user = Database::fetchOne("SELECT id FROM users WHERE username = 'eva'");
        $userId = (int)$user['id'];

        // Fails if old password does not match
        $failed = AuthService::changePassword($userId, 'wrongoldpass', 'newpass123');
        $this->assertFalse($failed);

        // Succeeds with correct old password
        $succeeded = AuthService::changePassword($userId, 'oldpass123', 'newpass123');
        $this->assertTrue($succeeded);

        // Verify can now login with new password
        $loginWithNew = AuthService::login('eva', 'newpass123');
        $this->assertTrue($loginWithNew);
    }

    public function testDeleteUserPreventsDeletingCurrentlyActiveAdmin(): void
    {
        AuthService::register('superadmin', 'adminpass', '08999999999', 'admin');
        $admin = Database::fetchOne("SELECT id FROM users WHERE username = 'superadmin'");
        $adminId = (int)$admin['id'];

        $cannotDeleteSelf = AuthService::deleteUser($adminId, $adminId);
        $this->assertFalse($cannotDeleteSelf);

        // User must still exist
        $stillExists = Database::fetchOne("SELECT id FROM users WHERE id = ?", [$adminId]);
        $this->assertNotNull($stillExists);
    }
}
