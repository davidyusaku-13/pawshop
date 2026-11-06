<?php
require_once __DIR__ . '/../config/database.php';

\App\AuthService::logout();

$cookie_names = ['userid', 'privilege', 'remember-me'];
foreach ($cookie_names as $cookie_name) {
    setcookie($cookie_name, '', time() - 3600, '/');
    unset($_COOKIE[$cookie_name]);
}

flash('info', 'Anda telah berhasil keluar.');
redirect('auth/login.php');
