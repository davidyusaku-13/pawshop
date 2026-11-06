<?php
$cur = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-sm navbar-dark bg-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>admin/index.php">
      <img src="<?= BASE_URL ?>assets/images/brand/logo.png" height="30" alt="Pawshop">
      <span class="badge bg-danger">ADMIN</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'index.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/index.php">Dasbor</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'transactions.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/transactions.php">Transaksi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'products.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/products.php">Produk</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'categories.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/categories.php">Kategori</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'statuses.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/statuses.php">Status</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'users.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/users.php">Pengguna</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($cur == 'reports.php') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/reports.php">Laporan</a>
        </li>
      </ul>
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-warning" href="<?= BASE_URL ?>index.php" target="_blank">
            <i class="fa-solid fa-store me-1"></i> Toko
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="<?= BASE_URL ?>auth/logout.php">
            <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
