<?php
session_start();
include 'config.php';

if ($_SESSION['privilege'] != 'admin') {
  header('Location: index.php');
}

if (isset($_GET['id'])) {
  $id = $_GET['id'];

  $sql = "SELECT * FROM transaksi WHERE id=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "s", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
  } else {
    echo json_encode(['error' => 'Transaction not found']);
  }
} else {
  echo json_encode(['error' => 'Invalid request']);
}
