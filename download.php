<?php
include 'config.php';

// Require admin access
requireAdmin();

// Validate CSRF token from URL
$token = get('token');
if (!csrfValidate($token)) {
    header('Location: admin-report.php');
    exit;
}

$type = get('type');

if ($type === 'transaksi') {
    $tanggalAwalDb = get('tanggal-awal');
    $tanggalAkhirDb = get('tanggal-akhir');

    // Validate date format
    $tglawl = DateTime::createFromFormat('Y-m-d', $tanggalAwalDb);
    $tglakhr = DateTime::createFromFormat('Y-m-d', $tanggalAkhirDb);

    if (!$tglawl || !$tglakhr) {
        header('Location: admin-report.php');
        exit;
    }

    $tanggalAwal = $tglawl->format("d-m-Y");
    $tanggalAkhir = $tglakhr->format("d-m-Y");

    // Sanitize filename
    $filename = "transaksi_data_" . preg_replace('/[^a-zA-Z0-9\-]/', '', $tanggalAwal) . "-" . preg_replace('/[^a-zA-Z0-9\-]/', '', $tanggalAkhir) . ".csv";

    // Fetch data using prepared statement
    $transactions = dbFetchAll(
        "SELECT t.id, t.timestamp, u.username, t.total_amount, t.payment_method, s.name
         FROM transaksi t
         JOIN users u ON t.user_id = u.id
         JOIN status s ON t.status_id = s.id
         WHERE DATE(t.timestamp) >= ? AND DATE(t.timestamp) <= ?
         ORDER BY t.timestamp DESC",
        'ss',
        [$tanggalAwalDb, $tanggalAkhirDb]
    );

    // Output CSV directly (no temp file)
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ["ID", "Timestamp", "Username", "Total Amount", "Payment Method", "Status"]);

    foreach ($transactions as $row) {
        fputcsv($output, [$row['id'], $row['timestamp'], $row['username'], $row['total_amount'], $row['payment_method'], $row['name']]);
    }

    fclose($output);
    exit;

} elseif ($type === 'produk') {
    $filename = "produk_data.csv";

    $products = dbFetchAll(
        "SELECT p.id, p.gambar, p.nama_produk, k.name, p.stok, p.harga, p.detail
         FROM produk p
         JOIN kategori k ON p.category_id = k.id"
    );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ["ID", "Gambar", "Nama Produk", "Kategori", "Stok", "Harga", "Detail"]);

    foreach ($products as $row) {
        fputcsv($output, [$row['id'], $row['gambar'], $row['nama_produk'], $row['name'], $row['stok'], $row['harga'], $row['detail']]);
    }

    fclose($output);
    exit;

} elseif ($type === 'user') {
    $filename = "user_data.csv";

    // Never export passwords - security fix
    $users = dbFetchAll("SELECT id, username, phone_number, privilege FROM users");

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ["ID", "Username", "Nomer HP", "Privilege"]);

    foreach ($users as $row) {
        fputcsv($output, [$row['id'], $row['username'], $row['phone_number'], $row['privilege']]);
    }

    fclose($output);
    exit;

} else {
    header('Location: admin-report.php');
    exit;
}
