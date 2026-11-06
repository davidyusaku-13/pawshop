<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth-guard.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
  $transID = mysqli_real_escape_string($conn, $_GET['id']);
} else {
  header('Location: ' . BASE_URL . 'customer/transactions.php');
  exit();
}

$pageTitle = "Pawshop | Invoice " . $transID;

// Fetch transaction info
$uRes = mysqli_query($conn, "SELECT username FROM users WHERE id=$userid");
$username = ($uRes && $uRow = mysqli_fetch_assoc($uRes)) ? $uRow['username'] : '';

$prefix = substr($transID, 0, 3);
$datePart = substr($transID, 3, 6);
$timePart = substr($transID, 9);
$year = "20" . substr($datePart, 0, 2);
$month = substr($datePart, 2, 2);
$day = substr($datePart, 4, 2);
$hour = substr($timePart, 0, 2);
$minute = substr($timePart, 2, 2);
$second = substr($timePart, 4, 2);
$outputDate = "$day-$month-$year $hour:$minute:$second";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
    }
    .invoice-card {
      max-width: 650px;
      margin: 30px auto;
      border: 1px dashed #ccc;
      padding: 30px;
      background: #fff;
    }
  </style>
</head>
<body class="bg-light">

<div class="invoice-card shadow-sm">
  <div class="no-print d-flex justify-content-between mb-4">
    <a href="<?= BASE_URL ?>customer/transactions.php" class="btn btn-sm btn-outline-secondary">
      &larr; Kembali
    </a>
    <button onclick="window.print()" class="btn btn-sm btn-primary">
      Cetak / Simpan PDF
    </button>
  </div>

  <div class="text-center mb-4">
    <img src="<?= BASE_URL ?>assets/images/brand/logo-title.png" alt="Pawshop" width="70" class="mb-2">
    <h3 class="fw-bold mb-0">PAWSHOP</h3>
    <p class="text-muted small mb-1">Kebutuhan Kucing Terlengkap</p>
    <div class="border-top border-bottom py-2 my-2">
      <strong>INVOICE: <?= htmlspecialchars($transID) ?></strong>
    </div>
  </div>

  <div class="d-flex justify-content-between small text-muted mb-3">
    <span>Tanggal: <?= $outputDate ?></span>
    <span>Pembeli: <strong><?= htmlspecialchars($username) ?></strong></span>
  </div>

  <table class="table table-sm align-middle">
    <thead>
      <tr class="border-bottom">
        <th>No</th>
        <th>Produk</th>
        <th class="text-center">Qty</th>
        <th class="text-end">Harga</th>
        <th class="text-end">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $sql = "SELECT trd.*, p.nama_produk, tr.payment_method 
              FROM transaksi_detail trd 
              JOIN produk p ON trd.product_id=p.id 
              JOIN transaksi tr ON trd.transactions_id=tr.id 
              WHERE transactions_id='$transID'";
      $result = mysqli_query($conn, $sql);
      $total = 0;
      $payment_method = '';
      $c = 1;
      if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $total += $row['subtotal'];
          $payment_method = $row['payment_method'];
      ?>
          <tr>
            <td><?= $c++ ?></td>
            <td><?= htmlspecialchars($row['nama_produk']) ?></td>
            <td class="text-center"><?= $row['quantity'] ?></td>
            <td class="text-end">Rp <?= number_format($row['unit_price'], 0, ',', '.') ?></td>
            <td class="text-end">Rp <?= number_format($row['subtotal'], 0, ',', '.') ?></td>
          </tr>
      <?php
        }
      }
      ?>
    </tbody>
    <tfoot>
      <tr class="border-top">
        <th colspan="4" class="text-end">Total Pembayaran:</th>
        <th class="text-end text-primary">Rp <?= number_format($total, 0, ',', '.') ?></th>
      </tr>
      <tr>
        <td colspan="5" class="text-end small text-muted">Metode Pembayaran: <strong><?= htmlspecialchars($payment_method) ?></strong></td>
      </tr>
    </tfoot>
  </table>

  <div class="text-center mt-5 pt-3 border-top small text-muted">
    <p class="mb-0">Terima kasih atas pesanan Anda!</p>
    <p>Salam hangat, <strong>Pawshop Team</strong></p>
  </div>
</div>

</body>
</html>
