<?php
include 'config.php';

if (!isset($userid)) {
  header('Location: login.php');
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
      if ($_POST['tanggal-awal'] == '' || $_POST['tanggal-akhir'] == '') {
        header('Location: admin-report.php');
      } else {
        $tanggalAwal = $_POST['tanggal-awal'];
        $tanggalAkhir = $_POST['tanggal-akhir'];
        $tglawl = new DateTime($tanggalAwal);
        $tglakhr = new DateTime($tanggalAkhir);
        $tanggalAwal = $tglawl->format("d-m-Y");
        $tanggalAkhir = $tglakhr->format("d-m-Y");
      }
    ?>
      <h1 class="text-center">Tabel Transaksi</h1>
      <p class="text-center"><?= $tanggalAwal ?> s/d <?= $tanggalAkhir ?></p>
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
            $sql = "SELECT t.id, t.timestamp, u.username, t.total_amount, t.payment_method, s.name FROM transaksi t JOIN users u ON t.user_id=u.id JOIN status s ON t.status_id=s.id WHERE timestamp>'$tanggalAwal' AND timestamp<'$tanggalAkhir'";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= $row['timestamp'] ?></td>
                  <td><?= $row['username'] ?></td>
                  <td><?= $row['total_amount'] ?></td>
                  <td><?= $row['payment_method'] ?></td>
                  <td><?= $row['name'] ?></td>
                </tr>
            <?php
              }
            }
            ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="download.php?tanggal-awal=<?= $tanggalAwal ?>&tanggal-akhir=<?= $tanggalAkhir ?>" class="btn btn-primary">Download</a>
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
            $sql = "SELECT p.id, p.gambar, p.nama_produk, k.name, p.stok, p.harga, p.detail FROM produk p JOIN kategori k ON p.category_id=k.id";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= $row['gambar'] ?></td>
                  <td><?= $row['nama_produk'] ?></td>
                  <td><?= $row['name'] ?></td>
                  <td><?= $row['stok'] ?></td>
                  <td><?= $row['harga'] ?></td>
                  <td><?= $row['detail'] ?></td>
                </tr>
            <?php
              }
            }
            ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="download.php?download-produk" class="btn btn-primary">Download</a>
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
              <th>Password</th>
              <th>Nomer HP</th>
              <th>Privilege</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "SELECT * FROM users";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                  <td><?= $row['id'] ?></td>
                  <td><?= $row['username'] ?></td>
                  <td><?= $row['password'] ?></td>
                  <td><?= $row['phone_number'] ?></td>
                  <td><?= $row['privilege'] ?></td>
                </tr>
            <?php
              }
            }
            ?>
          </tbody>
        </table>
        <div class="text-center">
          <a href="download.php?download-user" class="btn btn-primary">Download</a>
        </div>
      </div>
    <?php
    } else {
      header('Location: admin-report.php');
    }
    ?>
  </div>
</body>

</html>