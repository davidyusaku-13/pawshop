<?php

// LOCAL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pawshop";

// PUBLISH
// $servername = "localhost";
// $username = "dave3253_david";
// $password = "D-KILL3DX+";
// $dbname = "dave3253_kb2";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
