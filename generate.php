<?php
include 'config.php';

// Require admin access
requireAdmin();

// Validate CSRF for all POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrfValidate()) {
    header('Location: admin-report.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="./logo-title.png" type="image/x-icon">
  <link rel="stylesheet" href="./css/bootstrap.min.css">
  <script src="./js/bootstrap.bundle.js"></script>
  <title>Pawshop</title>
</head>

<body>
  <div class="container mt-3">
    <?php
    if (isset($_POST['generate-transaksi'])) {
      $tanggalAwalRaw = post('tanggal-awal');
      $tanggalAkhirRaw = post('tanggal-akhir');

      if (empty($tanggalAwalRaw) || empty($tanggalAkhirRaw)) {
        header('Location: admin-report.php');
        exit;
      }

      // Validate date format
      $tglawl = DateTime::createFromFormat('Y-m-d', $tanggalAwalRaw);
      $tglakhr = DateTime::createFromFormat('Y-m-d', $tanggalAkhirRaw);

      if (!$tglawl || !$tglakhr) {
        header('Location: admin-report.php');
        exit;
      }

      $tanggalAwal = $tglawl->format("d-m-Y");
      $tanggalAkhir = $tglakhr->format("d-m-Y");
      $tanggalAwalDb = $tglawl->format("Y-m-d");
      $tanggalAkhirDb = $tglakhr->format("Y-m-d");
    ?>
      <h1 class="text-center">Tabel Transaksi</h1>
      <p class="text-center"><?= e($tanggalAwal) ?> s/d <?= e($tanggalAkhir) ?></p>
      <div class="table-responsive text-center">
        <table class="table table-borderless">
          <thead>
            <tr>
              <th>ID</th>
              <th>Tanggal</th>
              <th>Pengguna</th>
              <th>Total</th>
              <th>Metode Pembayaran</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
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
            foreach ($transactions as $row):
            ?>
                <tr>
                  <td><?= e($row['id']) ?></td>
                  <td><?= e($row['timestamp']) ?></td>
                  <td><?= e($row['username']) ?></td>
                  <td>Rp <?= e(number_format($row['total_amount'])) ?></td>
                  <td><?= e($row['payment_method']) ?></td>
                  <td><?= e($row['name']) ?></td>
                </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="download.php?type=transaksi&tanggal-awal=<?= e(urlencode($tanggalAwalDb)) ?>&tanggal-akhir=<?= e(urlencode($tanggalAkhirDb)) ?>&token=<?= e(csrfToken()) ?>" class="btn btn-primary">Download</a>
        </div>
      </div>
    <?php
    } elseif (isset($_POST['generate-produk'])) {
    ?>
      <h1 class="text-center">Tabel Produk</h1>
      <div class="table-responsive text-center">
        <table class="table table-borderless">
          <thead>
            <tr>
              <th>ID</th>
              <th>Gambar</th>
              <th>Nama Produk</th>
              <th>Kategori</th>
              <th>Stok</th>
              <th>Harga</th>
              <th>Detail</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $products = dbFetchAll(
              "SELECT p.id, p.gambar, p.nama_produk, k.name, p.stok, p.harga, p.detail
               FROM produk p
               JOIN kategori k ON p.category_id = k.id"
            );
            foreach ($products as $row):
            ?>
                <tr>
                  <td><?= e($row['id']) ?></td>
                  <td><?= e($row['gambar']) ?></td>
                  <td><?= e($row['nama_produk']) ?></td>
                  <td><?= e($row['name']) ?></td>
                  <td><?= e($row['stok']) ?></td>
                  <td>Rp <?= e(number_format($row['harga'])) ?></td>
                  <td><?= e($row['detail']) ?></td>
                </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="download.php?type=produk&token=<?= e(csrfToken()) ?>" class="btn btn-primary">Download</a>
        </div>
      </div>
    <?php
    } elseif (isset($_POST['generate-user'])) {
    ?>
      <h1 class="text-center">Tabel User</h1>
      <div class="table-responsive text-center">
        <table class="table table-borderless">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Nomer HP</th>
              <th>Privilege</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Never expose passwords - removed password column
            $users = dbFetchAll("SELECT id, username, phone_number, privilege FROM users");
            foreach ($users as $row):
            ?>
                <tr>
                  <td><?= e($row['id']) ?></td>
                  <td><?= e($row['username']) ?></td>
                  <td><?= e($row['phone_number']) ?></td>
                  <td><?= e($row['privilege']) ?></td>
                </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="download.php?type=user&token=<?= e(csrfToken()) ?>" class="btn btn-primary">Download</a>
        </div>
      </div>
    <?php
    } else {
      header('Location: admin-report.php');
      exit;
    }
    ?>
  </div>
</body>

</html>
