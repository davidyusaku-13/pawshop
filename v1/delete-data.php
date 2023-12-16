<?php
include "config.php";
session_start();
if (!isset($_SESSION['idsession']) && !isset($_SESSION['usersession'])) {
    header('Location: login.php');
} else {
    $idsession = $_SESSION['idsession'];
}
$id = $_GET['id'];
$sql = "DELETE FROM product WHERE id='$id'";

if (mysqli_query($conn, $sql)) {
    echo "Record deleted successfully";
    header('Location: index.php');
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
