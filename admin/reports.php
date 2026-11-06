<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\ProductService;
use App\AuthService;
use App\Database;

$pageTitle = "Pawshop | Laporan Penjualan & Data";
$prodCount = ProductService::count();
$userCount = count(AuthService::getAllUsers());
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <link rel="icon" type="image/x-icon" href="<?= BASE_URL ?>assets/images/brand/logo-title.png">
  <link href="<?= BASE_URL ?>assets/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/ec712a3d01.js" crossorigin="anonymous"></script>
</head>

<body>
  <?php include __DIR__ . '/../includes/admin-navbar.php'; ?>

  <div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3 class="fw-bold">Laporan & Ekspor Data</h3>
      <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-outline-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dasbor
      </a>
    </div>

    <div class="row g-4">
      <!-- LAPORAN TRANSAKSI -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-primary">
          <div class="card-header bg-primary text-white fw-bold">
            <i class="fa-solid fa-receipt me-2"></i> Laporan Transaksi
          </div>
          <div class="card-body">
            <p class="text-muted small">Pilih rentang tanggal untuk melihat atau mengekspor data transaksi.</p>
            <form action="<?= BASE_URL ?>admin/generate-report.php" method="POST">
              <div class="mb-3">
                <label class="form-label small fw-semibold">Tanggal Mulai</label>
                <input class="form-control" type="date" name="tanggal-awal" required>
              </div>
              <div class="mb-3">
                <label class="form-label small fw-semibold">Tanggal Selesai</label>
                <input class="form-control" type="date" name="tanggal-akhir" required>
              </div>
              <button class="btn btn-primary w-100" type="submit" name="generate-transaksi">
                <i class="fa-solid fa-file-lines me-1"></i> Buat Laporan Transaksi
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- LAPORAN PRODUK -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-success">
          <div class="card-header bg-success text-white fw-bold">
            <i class="fa-solid fa-box-open me-2"></i> Laporan Data Produk
          </div>
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <p class="text-muted small">Laporan katalog produk lengkap mencakup ID, nama produk, kategori, harga, dan sisa stok.</p>
              <div class="alert alert-light border my-3 text-center">
                <span class="fs-4 fw-bold text-success"><?= $prodCount ?></span>
                <span class="d-block small text-muted">Total Produk Terdaftar</span>
              </div>
            </div>
            <form action="<?= BASE_URL ?>admin/generate-report.php" method="POST">
              <button class="btn btn-success w-100" type="submit" name="generate-produk">
                <i class="fa-solid fa-file-lines me-1"></i> Buat Laporan Produk
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- LAPORAN PENGGUNA -->
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm border-info">
          <div class="card-header bg-info text-white fw-bold">
            <i class="fa-solid fa-users me-2"></i> Laporan Pengguna
          </div>
          <div class="card-body d-flex flex-column justify-content-between">
            <div>
              <p class="text-muted small">Laporan akun pengguna dan hak akses terdaftar di sistem Pawshop.</p>
              <div class="alert alert-light border my-3 text-center">
                <span class="fs-4 fw-bold text-info"><?= $userCount ?></span>
                <span class="d-block small text-muted">Total Pengguna Terdaftar</span>
              </div>
            </div>
            <form action="<?= BASE_URL ?>admin/generate-report.php" method="POST">
              <button class="btn btn-info text-white w-100" type="submit" name="generate-user">
                <i class="fa-solid fa-file-lines me-1"></i> Buat Laporan Pengguna
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="<?= BASE_URL ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
