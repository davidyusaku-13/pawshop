<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/admin-guard.php';

use App\ProductService;
use App\AuthService;

$pageTitle = "Laporan & Ekspor Data";
$prodCount = ProductService::count();
$userCount = count(AuthService::getAllUsers());

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h4 class="fw-bold mb-1">Pusat Laporan & Rekap</h4>
    <p class="text-muted small mb-0">Cetak rekap data transaksi, katalog produk, dan daftar pengguna</p>
  </div>
  <a href="<?= BASE_URL ?>admin/index.php" class="btn btn-outline-secondary btn-sm">
    <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dasbor
  </a>
</div>

<?= render_flash() ?>

<div class="row g-4">
  <!-- LAPORAN TRANSAKSI -->
  <div class="col-md-6 col-lg-4">
    <div class="card h-100 shadow-sm border-0">
      <div class="card-header bg-primary text-white fw-bold py-3">
        <i class="fa-solid fa-receipt me-2"></i> Laporan Transaksi
      </div>
      <div class="card-body p-4">
        <p class="text-muted small">Tentukan rentang tanggal untuk melihat tabel transaksi atau mengekspor berkas CSV.</p>
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
    <div class="card h-100 shadow-sm border-0">
      <div class="card-header bg-success text-white fw-bold py-3">
        <i class="fa-solid fa-box-open me-2"></i> Laporan Data Produk
      </div>
      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <div>
          <p class="text-muted small">Cetak atau ekspor katalog produk lengkap mencakup ID, nama produk, kategori, harga, dan sisa stok.</p>
          <div class="alert alert-light border my-3 text-center py-3">
            <span class="fs-3 fw-bold text-success d-block"><?= $prodCount ?></span>
            <span class="small text-muted">Total Produk Terdaftar</span>
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
    <div class="card h-100 shadow-sm border-0">
      <div class="card-header bg-info text-white fw-bold py-3">
        <i class="fa-solid fa-users me-2"></i> Laporan Pengguna
      </div>
      <div class="card-body p-4 d-flex flex-column justify-content-between">
        <div>
          <p class="text-muted small">Rekap akun pengguna, nomor kontak, serta hak akses peran terdaftar di sistem Pawshop.</p>
          <div class="alert alert-light border my-3 text-center py-3">
            <span class="fs-3 fw-bold text-info d-block"><?= $userCount ?></span>
            <span class="small text-muted">Total Pengguna Terdaftar</span>
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

<?php include __DIR__ . '/../includes/admin-footer.php'; ?>
