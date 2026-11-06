<?php
use App\AuthService;
use App\ProductService;

$currentUser = AuthService::getCurrentUser();
$isLoggedIn = AuthService::isLoggedIn();
$isAdmin = AuthService::isAdmin();
$navCategories = ProductService::getAllCategories();
$cartItemCount = !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!-- Start Navbar -->
<header>
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top shadow-sm py-2">
    <div class="container">
      <!-- Brand Logo -->
      <a class="navbar-brand d-flex align-items-center me-3" href="<?= BASE_URL ?>">
        <img src="<?= BASE_URL ?>assets/images/brand/logo.png" height="34" alt="Pawshop">
      </a>

      <!-- Mobile Toggle -->
      <div class="d-flex align-items-center gap-2 d-lg-none">
        <button type="button" class="btn btn-light position-relative rounded-pill px-3 py-1 border" data-bs-toggle="offcanvas" data-bs-target="#cart" aria-label="Keranjang">
          <i class="fa-solid fa-cart-shopping text-warning"></i>
          <?php if ($cartItemCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="navCartBadgeMobile" style="font-size: 0.65rem;">
              <?= $cartItemCount ?>
            </span>
          <?php endif; ?>
        </button>
        <button class="navbar-toggler border-0 shadow-none p-1" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar" aria-label="Menu Navigasi">
          <span class="navbar-toggler-icon"></span>
        </button>
      </div>

      <!-- Collapsible Nav & Search -->
      <div class="collapse navbar-collapse" id="mynavbar">
        <!-- Search Bar (Centered & Spacious) -->
        <form class="d-flex flex-grow-1 mx-lg-4 my-2 my-lg-0" action="<?= BASE_URL ?>" method="GET">
          <div class="input-group w-100">
            <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3 text-muted">
              <i class="fa-solid fa-magnifying-glass"></i>
            </span>
            <input class="form-control bg-light border-start-0 border-end-0 py-2" type="search" placeholder="Cari makanan kucing, vitamin, obat, aksesoris..." name="s" value="<?= isset($search) ? htmlspecialchars($search) : ''; ?>" aria-label="Cari Produk">
            <button class="btn btn-pawshop rounded-end-pill px-3 fw-semibold" type="submit">
              Cari
            </button>
          </div>
        </form>

        <!-- Right Side: Navigation & User Hub -->
        <ul class="navbar-nav align-items-lg-center gap-2">
          <!-- Categories Dropdown (Quick Browse) -->
          <li class="nav-item dropdown">
            <button class="nav-link dropdown-toggle btn btn-link text-decoration-none text-dark fw-medium px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-shapes text-warning me-1"></i> Kategori
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2">
              <li>
                <a class="dropdown-item py-2 <?= empty($kategoriId) ? 'active fw-bold' : '' ?>" href="<?= BASE_URL ?>">
                  <i class="fa-solid fa-border-all me-2 text-muted"></i> Semua Produk
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <?php foreach ($navCategories as $catRow): ?>
                <?php $catActive = (isset($kategoriId) && (int)$kategoriId === (int)$catRow['id']) ? 'active fw-bold' : ''; ?>
                <li>
                  <a class="dropdown-item py-2 <?= $catActive ?>" href="<?= BASE_URL ?>?k=<?= $catRow['id'] ?>">
                    <i class="fa-solid fa-tag me-2 text-muted"></i> <?= htmlspecialchars($catRow['name']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>

          <!-- Desktop Cart Button -->
          <li class="nav-item d-none d-lg-block">
            <button type="button" class="btn btn-light border position-relative rounded-pill px-3 py-2 d-flex align-items-center gap-2" data-bs-toggle="offcanvas" data-bs-target="#cart">
              <i class="fa-solid fa-cart-shopping text-warning"></i>
              <span class="small fw-bold">Keranjang</span>
              <span class="badge rounded-pill bg-danger" id="navCartBadge" style="font-size: 0.7rem;">
                <?= $cartItemCount ?>
              </span>
            </button>
          </li>

          <!-- User Authentication Cluster -->
          <?php if ($isLoggedIn): ?>
            <li class="nav-item dropdown">
              <button class="btn btn-light border rounded-pill px-3 py-2 d-flex align-items-center gap-2 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 24px; height: 24px; font-size: 0.75rem;">
                  <?= strtoupper(substr($currentUser['username'] ?? 'U', 0, 1)) ?>
                </div>
                <span class="small fw-semibold text-truncate" style="max-width: 130px;">
                  Halo, <?= htmlspecialchars($currentUser['username'] ?? 'User') ?>
                </span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 mt-2 p-2">
                <li class="px-3 py-2 border-bottom mb-2 bg-light rounded-2">
                  <div class="small fw-bold text-dark text-truncate"><?= htmlspecialchars($currentUser['username'] ?? '') ?></div>
                  <span class="badge <?= $isAdmin ? 'bg-danger' : 'bg-primary' ?> text-uppercase" style="font-size: 0.65rem;">
                    <?= $isAdmin ? 'Administrator' : 'Pelanggan' ?>
                  </span>
                </li>
                <?php if ($isAdmin): ?>
                  <li>
                    <a class="dropdown-item py-2 text-danger fw-semibold" href="<?= BASE_URL ?>admin/">
                      <i class="fa-solid fa-shield-halved me-2"></i> Dasbor Admin
                    </a>
                  </li>
                  <li><hr class="dropdown-divider"></li>
                <?php endif; ?>
                <li>
                  <a class="dropdown-item py-2" href="<?= BASE_URL ?>customer/profile">
                    <i class="fa-solid fa-user me-2 text-muted"></i> Profil Saya
                  </a>
                </li>
                <li>
                  <a class="dropdown-item py-2" href="<?= BASE_URL ?>customer/transactions">
                    <i class="fa-solid fa-clock-rotate-left me-2 text-muted"></i> Riwayat Belanja
                  </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                  <a class="dropdown-item py-2 text-danger" href="<?= BASE_URL ?>auth/logout">
                    <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Keluar
                  </a>
                </li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item d-flex align-items-center gap-2 mt-2 mt-lg-0">
              <a href="<?= BASE_URL ?>auth/login" class="btn btn-outline-dark btn-sm rounded-pill px-3 py-1 fw-semibold">
                Masuk
              </a>
              <a href="<?= BASE_URL ?>auth/register" class="btn btn-pawshop btn-sm rounded-pill px-3 py-1 fw-semibold">
                Daftar
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
</header>
<!-- End Navbar -->
