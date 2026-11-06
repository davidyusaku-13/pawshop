<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/admin-guard.php';

header('Content-Type: application/json');

if (isset($_GET['id'])) {
  $id = (int)$_GET['id'];
  $stmt = mysqli_prepare($conn, "SELECT * FROM produk WHERE id=?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode($row);
  } else {
    http_response_code(404);
    echo json_encode(['error' => 'Produk tidak ditemukan']);
  }
} else {
  http_response_code(400);
  echo json_encode(['error' => 'Permintaan tidak valid']);
}
