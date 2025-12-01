<?php

// LOCAL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pawshop";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
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
