<?php

// Database configuration (supports environment variables or defaults to localhost)
$servername = getenv('DB_HOST') ?: "localhost";
$username = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : "";
$dbname = getenv('DB_NAME') ?: "pawshop";
$port = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3306;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->autocommit(TRUE);

date_default_timezone_set("Asia/Jakarta");

$privilege = '';
$userid = '';
session_start();
if (
    isset($_COOKIE['remember-me']) && $_COOKIE['remember-me'] != '' &&
    isset($_COOKIE['userid']) && $_COOKIE['remember-me'] != '' &&
    isset($_COOKIE['privilege']) && $_COOKIE['remember-me'] != ''
) {
    $userid = $_COOKIE['userid'];
    $privilege = $_COOKIE['privilege'];
} else if (isset($_SESSION['userid']) && $_SESSION['userid'] != '' && isset($_SESSION['privilege']) && $_SESSION['privilege'] != '') {
    $userid = $_SESSION['userid'];
    $privilege = $_SESSION['privilege'];
}
