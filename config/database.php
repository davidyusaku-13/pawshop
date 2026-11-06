<?php

// Database configuration (supports environment variables or defaults to localhost)
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ?: "pawshop";
$port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('BASE_URL')) {
    define('BASE_URL', '/');
}

date_default_timezone_set("Asia/Jakarta");

// Autoloader for App namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = ROOT_PATH . '/src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load helper functions
require_once ROOT_PATH . '/src/helpers.php';

// Create MySQLi connection (backward compatibility for legacy views)
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
$conn->autocommit(TRUE);

// Initialize Session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SECURE SESSION & AUTH RESOLUTION (Eliminates cookie privilege spoofing)
$userid = '';
$privilege = '';

if (!empty($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $privilege = $_SESSION['privilege'] ?? 'user';
}
