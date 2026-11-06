<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\Database;
use App\ProductService;
use App\AuthService;

$pageTitle = "Pawshop | Hasil Laporan";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png" type="image/x-icon">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/bootstrap.min.css">
  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>

<body class="bg-light">
  <?php include __DIR__ . '/../includes/admin-navbar.php'; ?>

  <div class="container my-4">
    <div class="mb-3">
      <a href="<?= BASE_URL ?>admin/reports.php" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Menu Laporan
      </a>
    </div>

    <?php if (isset($_POST['generate-transaksi'])): ?>
      <?php
      if (empty($_POST['tanggal-awal']) || empty($_POST['tanggal-akhir'])) {
          redirect('admin/reports.php');
      }
      $tglAwalInput = $_POST['tanggal-awal'];
      $tglAkhirInput = $_POST['tanggal-akhir'];
      $tglawl = new DateTime($tglAwalInput);
      $tglakhr = new DateTime($tglAkhirInput);
      $tanggalAwal = $tglawl->format("d-m-Y");
      $tanggalAkhir = $tglakhr->format("d-m-Y");

      $sql = "SELECT t.id, t.timestamp, u.username, t.total_amount, t.payment_method, s.name 
              FROM transaksi t 
              JOIN users u ON t.user_id=u.id 
              JOIN status s ON t.status_id=s.id 
              WHERE timestamp >= ? AND timestamp <= ? 
              ORDER BY t.id DESC";
      $orders = Database::query($sql, [$tanggalAwal, $tanggalAkhir]);
      ?>
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <div>
            <h4 class="mb-0 fw-bold">Laporan Transaksi</h4>
            <span class="text-muted small">Periode: <?= $tanggalAwal ?> s/d <?= $tanggalAkhir ?></span>
          </div>
          <a href="<?= BASE_URL ?>admin/export-csv.php?tanggal-awal=<?= urlencode($tanggalAwal) ?>&tanggal-akhir=<?= urlencode($tanggalAkhir) ?>" class="btn btn-success btn-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Unduh File CSV
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="table-dark">
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
                <?php if (!empty($orders)): foreach ($orders as $row): ?>
                  <tr>
                    <td class="fw-bold font-monospace"><?= $row['id'] ?></td>
                    <td><?= $row['timestamp'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td class="fw-semibold text-primary"><?= format_rupiah($row['total_amount']) ?></td>
                    <td><?= htmlspecialchars($row['payment_method']) ?></td>
                    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['name']) ?></span></td>
                  </tr>
                <?php endforeach; else: ?>
                  <tr><td colspan="6" class="text-center py-4 text-muted">Tidak ada transaksi ditemukan pada rentang tanggal ini.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    <?php elseif (isset($_POST['generate-produk'])): ?>
      <?php $products = ProductService::getAll(); ?>
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <h4 class="mb-0 fw-bold">Laporan Data Produk</h4>
          <a href="<?= BASE_URL ?>admin/export-csv.php?download-produk=1" class="btn btn-success btn-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Unduh File CSV
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Nama Produk</th>
                  <th>Kategori</th>
                  <th>Stok</th>
                  <th>Harga</th>
                  <th>Detail</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $row): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['nama_produk']) ?></td>
                    <td><?= htmlspecialchars($row['kategori_name'] ?? '-') ?></td>
                    <td><?= $row['stok'] ?></td>
                    <td><?= format_rupiah($row['harga']) ?></td>
                    <td class="small text-muted text-truncate" style="max-width: 250px;"><?= htmlspecialchars($row['detail'] ?? '-') ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    <?php elseif (isset($_POST['generate-user'])): ?>
      <?php $users = AuthService::getAllUsers(); ?>
      <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
          <h4 class="mb-0 fw-bold">Laporan Data Pengguna</h4>
          <a href="<?= BASE_URL ?>admin/export-csv.php?download-user=1" class="btn btn-success btn-sm">
            <i class="fa-solid fa-file-csv me-1"></i> Unduh File CSV
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th>ID</th>
                  <th>Username</th>
                  <th>Nomor HP</th>
                  <th>Hak Akses</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $row): ?>
                  <tr>
                    <td><?= $row['id'] ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['phone_number'] ?? '-') ?></td>
                    <td><span class="badge <?= ($row['privilege'] === 'admin') ? 'bg-danger' : 'bg-primary' ?>"><?= strtoupper($row['privilege']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    <?php else: ?>
      <p class="text-muted text-center py-5">Silakan pilih laporan yang ingin dibuat di <a href="<?= BASE_URL ?>admin/reports.php">Menu Laporan</a>.</p>
    <?php endif; ?>
  </div>
</body>
</html>
