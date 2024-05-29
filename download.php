<?php
include 'config.php';

if (!isset($userid)) {
  header('Location: login.php');
}

if (isset($_GET['tanggal-awal']) && isset($_GET['tanggal-akhir'])) {
  $tanggalAwal = $_GET['tanggal-awal'];
  $tanggalAkhir = $_GET['tanggal-akhir'];

  // Generate your download file (e.g., CSV) based on the date range
  $filename = "transaksi_data_$tanggalAwal-$tanggalAkhir.csv";

  // Your query to fetch data for the CSV file
  $sql = "SELECT t.id, t.timestamp, u.username, t.total_amount, t.payment_method, s.name FROM transaksi t JOIN users u ON t.user_id=u.id JOIN status s ON t.status_id=s.id WHERE timestamp>'$tanggalAwal' AND timestamp<'$tanggalAkhir'";
  $result = $conn->query($sql);

  // Create and write data to CSV file
  $fp = fopen($filename, 'w');
  fputcsv($fp, ["ID", "Timestamp", "Username", "Total Amount", "Payment Method", "Status"]);

  while ($row = $result->fetch_assoc()) {
    fputcsv($fp, [$row['id'], $row['timestamp'], $row['username'], $row['total_amount'], $row['payment_method'], $row['name']]);
  }

  fclose($fp);

  // Set headers for file download
  header('Content-Type: application/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  readfile($filename);

  // Delete the temporary file after download
  unlink($filename);
  exit;
} elseif (isset($_GET['download-produk'])) {
  // Your existing code to fetch and format date range goes here

  // Generate your download file (e.g., CSV) based on the product data
  $filename = "produk_data.csv";

  // Your query to fetch data for the CSV file
  $sql = "SELECT p.id, p.gambar, p.nama_produk, k.name, p.stok, p.harga, p.detail FROM produk p JOIN kategori k ON p.category_id=k.id";
  $result = $conn->query($sql);

  // Create and write data to CSV file
  $fp = fopen($filename, 'w');
  fputcsv($fp, ["ID", "Gambar", "Nama Produk", "Kategori", "Stok", "Harga", "Detail"]);

  while ($row = $result->fetch_assoc()) {
    fputcsv($fp, [$row['id'], $row['gambar'], $row['nama_produk'], $row['name'], $row['stok'], $row['harga'], $row['detail']]);
  }

  fclose($fp);

  // Set headers for file download
  header('Content-Type: application/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  readfile($filename);

  // Delete the temporary file after download
  unlink($filename);
  exit;
} elseif (isset($_GET['download-user'])) {
  // Generate your download file (e.g., CSV) based on user data
  $filename = "user_data.csv";

  // Your query to fetch data for the CSV file
  $sql = "SELECT * FROM users";
  $result = $conn->query($sql);

  // Create and write data to CSV file
  $fp = fopen($filename, 'w');
  fputcsv($fp, ["ID", "Username", "Password", "Nomer HP", "Privilege"]);

  while ($row = $result->fetch_assoc()) {
    fputcsv($fp, [$row['id'], $row['username'], $row['password'], $row['phone_number'], $row['privilege']]);
  }

  fclose($fp);

  // Set headers for file download
  header('Content-Type: application/csv');
  header('Content-Disposition: attachment; filename="' . $filename . '"');
  readfile($filename);

  // Delete the temporary file after download
  unlink($filename);
  exit;
} else {
  // Redirect if date range parameters are not provided
  header('Location: report.php');
  exit;
}
