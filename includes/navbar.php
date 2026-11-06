  <!-- Start Navbar -->
  <header>
    <nav class="navbar navbar-expand-sm navbar-light bg-light sticky-top shadow-sm">
      <div class="container-fluid d-flex">
        <a class="navbar-brand" href="<?= BASE_URL ?>index.php">
          <img src="<?= BASE_URL ?>assets/images/brand/logo.png" height="30" alt="Pawshop">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mynavbar">
          <ul class="navbar-nav me-auto">
            <li class="nav-item dropdown" id="profileDropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-user-tie"></i>
              </a>
              <ul class="dropdown-menu">
                <?php if (\App\AuthService::isLoggedIn()): ?>
                  <?php if (\App\AuthService::isAdmin()): ?>
                    <li><a class="dropdown-item fw-semibold text-danger" href="<?= BASE_URL ?>admin/index.php"><i class="fa-solid fa-shield-halved me-1"></i> Dasbor Admin</a></li>
                    <li><hr class="dropdown-divider"></li>
                  <?php endif; ?>
                  <li><a class="dropdown-item" href="<?= BASE_URL ?>customer/profile.php"><i class="fa-solid fa-user me-1"></i> Profil</a></li>
                  <li><a class="dropdown-item" href="<?= BASE_URL ?>customer/transactions.php"><i class="fa-solid fa-clock-rotate-left me-1"></i> Transaksi</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout.php"><i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Keluar</a>
                  </li>
                <?php else: ?>
                  <li>
                    <a class="dropdown-item" href="<?= BASE_URL ?>auth/login.php"><i class="fa-solid fa-right-to-bracket me-1"></i> Masuk</a>
                  </li>
                  <li>
                    <a class="dropdown-item" href="<?= BASE_URL ?>auth/register.php"><i class="fa-solid fa-user-plus me-1"></i> Daftar</a>
                  </li>
                <?php endif; ?>
              </ul>
            </li>
            <li class="nav-item dropdown" id="kategoriDropdown">
              <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Kategori
              </button>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item <?= empty($kategoriId) ? 'active' : '' ?>" href="<?= BASE_URL ?>index.php">Semua Produk</a></li>
                <li><hr class="dropdown-divider"></li>
                <?php
                $navCategories = \App\ProductService::getAllCategories();
                foreach ($navCategories as $catRow) {
                    $catActive = (isset($kategoriId) && (int)$kategoriId === (int)$catRow['id']) ? 'active' : '';
                    echo '<li><a class="dropdown-item ' . $catActive . '" href="' . BASE_URL . 'index.php?k=' . $catRow['id'] . '">' . htmlspecialchars($catRow['name']) . '</a></li>';
                }
                ?>
              </ul>
            </li>
            <li class="nav-item">
              <a class="nav-link position-relative" data-bs-toggle="offcanvas" href="#cart" role="button" aria-controls="cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <?php if (!empty($_SESSION['cart'])): ?>
                  <span class="position-absolute top-1 start-100 translate-middle badge rounded-pill bg-danger" id="navCartBadge" style="font-size: 0.65rem;">
                    <?= count($_SESSION['cart']) ?>
                  </span>
                <?php endif; ?>
              </a>
            </li>
          </ul>
          <form class="d-flex" action="<?= BASE_URL ?>index.php" method="GET">
            <input class="form-control me-2" type="search" placeholder="Cari Produk" name="s" value="<?= isset($search) ? htmlspecialchars($search) : ''; ?>">
            <button class="btn btn-warning text-dark fw-semibold" type="submit">Cari</button>
          </form>
        </div>
      </div>
    </nav>
  </header>
  <!-- End Navbar -->
