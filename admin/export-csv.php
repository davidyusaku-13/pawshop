<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\Database;
use App\ProductService;
use App\AuthService;

if (isset($_GET['tanggal-awal'], $_GET['tanggal-akhir'])) {
    $tanggalAwal = trim($_GET['tanggal-awal']);
    $tanggalAkhir = trim($_GET['tanggal-akhir']);

    $filename = "transaksi_{$tanggalAwal}_{$tanggalAkhir}.csv";
    $sql = "SELECT t.id, t.timestamp, u.username, t.total_amount, t.payment_method, s.name 
            FROM transaksi t 
            JOIN users u ON t.user_id = u.id 
            JOIN status s ON t.status_id = s.id 
            WHERE timestamp >= ? AND timestamp <= ? 
            ORDER BY t.id DESC";
    $results = Database::query($sql, [$tanggalAwal, $tanggalAkhir]);

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ["ID Transaksi", "Timestamp", "Username", "Total Amount", "Payment Method", "Status"]);

    foreach ($results as $row) {
        fputcsv($output, [$row['id'], $row['timestamp'], $row['username'], $row['total_amount'], $row['payment_method'], $row['name']]);
    }
    fclose($output);
    exit();

} elseif (isset($_GET['download-produk'])) {
    $filename = "produk_data_" . date('Ymd') . ".csv";
    $products = ProductService::getAll();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ["ID", "Nama Produk", "Kategori", "Stok", "Harga", "Detail"]);

    foreach ($products as $row) {
        fputcsv($output, [$row['id'], $row['nama_produk'], $row['kategori_name'] ?? '-', $row['stok'], $row['harga'], $row['detail']]);
    }
    fclose($output);
    exit();

} elseif (isset($_GET['download-user'])) {
    $filename = "user_data_" . date('Ymd') . ".csv";
    $users = AuthService::getAllUsers();

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ["ID", "Username", "Nomor Telepon", "Privilege"]);

    foreach ($users as $row) {
        fputcsv($output, [$row['id'], $row['username'], $row['phone_number'] ?? '-', $row['privilege']]);
    }
    fclose($output);
    exit();
}

redirect('admin/reports.php');
