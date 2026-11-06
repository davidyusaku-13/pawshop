<?php
$cur = str_replace('.php', '', basename($_SERVER['PHP_SELF']));
$adminUser = \App\AuthService::getCurrentUser();
?>
<!-- ADMIN SIDEBAR -->
<aside class="admin-sidebar shadow" id="adminSidebar">
  <div class="sidebar-brand d-flex align-items-center justify-content-between">
    <a href="<?= BASE_URL ?>admin/" class="d-flex align-items-center gap-2 text-decoration-none text-nowrap">
      <img src="<?= BASE_URL ?>assets/images/brand/logo.png" style="height: 25px; max-width: 125px; object-fit: contain;" alt="Pawshop">
      <span class="badge bg-danger text-uppercase px-2 py-1 flex-shrink-0" style="font-size: 0.62rem; letter-spacing: 0.5px; font-weight: 700;">ADMIN</span>
    </a>
    <button class="btn btn-sm btn-outline-light d-lg-none" type="button" id="sidebarCloseBtn" aria-label="Tutup Menu">
      <i class="fa-solid fa-xmark"></i>
    </button>
  </div>

  <div class="px-3 py-3 border-bottom border-secondary border-opacity-25 d-flex align-items-center gap-2">
    <div class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0" style="width: 38px; height: 38px;">
      <i class="fa-solid fa-user-shield"></i>
    </div>
    <div class="overflow-hidden">
      <div class="text-white fw-bold text-truncate small"><?= htmlspecialchars($adminUser['username'] ?? 'Administrator') ?></div>
      <div class="text-white-50" style="font-size: 0.75rem;">Pengelola Toko</div>
    </div>
  </div>

  <nav class="mt-3 flex-grow-1">
    <ul class="nav flex-column">
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'index') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/">
          <i class="fa-solid fa-chart-pie"></i>
          <span>Dasbor</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'transactions') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/transactions">
          <i class="fa-solid fa-receipt"></i>
          <span>Transaksi</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'products') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/products">
          <i class="fa-solid fa-box-open"></i>
          <span>Produk</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'categories') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/categories">
          <i class="fa-solid fa-tags"></i>
          <span>Kategori</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'statuses') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/statuses">
          <i class="fa-solid fa-list-check"></i>
          <span>Status</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'users') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/users">
          <i class="fa-solid fa-users"></i>
          <span>Pengguna</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($cur == 'reports') ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/reports">
          <i class="fa-solid fa-file-lines"></i>
          <span>Laporan</span>
        </a>
      </li>
    </ul>
  </nav>

  <div class="p-3 border-top border-secondary border-opacity-25 mt-auto">
    <a href="<?= BASE_URL ?>" target="_blank" class="nav-link mb-2 text-warning d-flex align-items-center gap-2">
      <i class="fa-solid fa-store"></i>
      <span>Buka Toko</span>
    </a>
    <a href="<?= BASE_URL ?>auth/logout" class="nav-link text-danger d-flex align-items-center gap-2">
      <i class="fa-solid fa-arrow-right-from-bracket"></i>
      <span>Keluar</span>
    </a>
  </div>
</aside>
