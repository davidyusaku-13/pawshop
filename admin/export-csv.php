<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

if (isset($_GET['tanggal-awal']) && isset($_GET['tanggal-akhir'])) {
  $tanggalAwal = mysqli_real_escape_string($conn, $_GET['tanggal-awal']);
  $tanggalAkhir = mysqli_real_escape_string($conn, $_GET['tanggal-akhir']);

  $filename = "transaksi_{$tanggalAwal}_{$tanggalAkhir}.csv";
  $sql = "SELECT t.id, t.timestamp, u.username, t.total_amount, t.payment_method, s.name 
          FROM transaksi t 
          JOIN users u ON t.user_id=u.id 
          JOIN status s ON t.status_id=s.id 
          WHERE timestamp >= '$tanggalAwal' AND timestamp <= '$tanggalAkhir'";
  $result = $conn->query($sql);

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="' . $filename . '"');

  $output = fopen('php://output', 'w');
  fputcsv($output, ["ID Transaksi", "Timestamp", "Username", "Total Amount", "Payment Method", "Status"]);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      fputcsv($output, [$row['id'], $row['timestamp'], $row['username'], $row['total_amount'], $row['payment_method'], $row['name']]);
    }
  }
  fclose($output);
  exit();

} elseif (isset($_GET['download-produk'])) {
  $filename = "produk_data_" . date('Ymd') . ".csv";
  $sql = "SELECT p.id, p.nama_produk, k.name, p.stok, p.harga, p.detail 
          FROM produk p 
          LEFT JOIN kategori k ON p.category_id=k.id";
  $result = $conn->query($sql);

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="' . $filename . '"');

  $output = fopen('php://output', 'w');
  fputcsv($output, ["ID", "Nama Produk", "Kategori", "Stok", "Harga", "Detail"]);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      fputcsv($output, [$row['id'], $row['nama_produk'], $row['name'], $row['stok'], $row['harga'], $row['detail']]);
    }
  }
  fclose($output);
  exit();

} elseif (isset($_GET['download-user'])) {
  $filename = "user_data_" . date('Ymd') . ".csv";
  $sql = "SELECT id, username, phone_number, privilege FROM users";
  $result = $conn->query($sql);

  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename="' . $filename . '"');

  $output = fopen('php://output', 'w');
  fputcsv($output, ["ID", "Username", "Nomor Telepon", "Privilege"]);

  if ($result) {
    while ($row = $result->fetch_assoc()) {
      fputcsv($output, [$row['id'], $row['username'], $row['phone_number'], $row['privilege']]);
    }
  }
  fclose($output);
  exit();
}

header('Location: ' . BASE_URL . 'admin/reports.php');
exit();
